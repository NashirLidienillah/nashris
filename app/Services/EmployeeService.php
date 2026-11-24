<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\LeaveBalance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class EmployeeService
{
    /**
     * Generate sequential employee code formatted as EMP-YYYY-XXX
     */
    public function generateEmployeeCode(): string
    {
        $year = Carbon::now()->year;
        $prefix = "EMP-{$year}-";

        $latestEmployee = Employee::where('employee_code', 'like', "{$prefix}%")
            ->orderBy('employee_code', 'desc')
            ->first();

        if (! $latestEmployee) {
            return "{$prefix}001";
        }

        $lastSeq = (int) substr($latestEmployee->employee_code, -3);
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextSeq}";
    }

    /**
     * Create a new employee along with User account and initial LeaveBalance inside a transaction.
     */
    public function createEmployee(array $userData, array $employeeData, array $documents = []): Employee
    {
        return DB::transaction(function () use ($userData, $employeeData, $documents) {
            // 1. Create User account
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'is_active' => true,
                'must_change_password' => $userData['must_change_password'] ?? true,
            ]);

            $roleName = $userData['role'] ?? 'employee';
            $userRole = Role::firstOrCreate(['name' => $roleName]);
            $user->syncRoles([$userRole]);

            // 2. Generate unique employee code & create Employee
            $employeeData['user_id'] = $user->id;
            $employeeData['employee_code'] = $this->generateEmployeeCode();

            $employee = Employee::create($employeeData);

            // 3. Initialize Leave Balance for current year
            LeaveBalance::create([
                'employee_id' => $employee->id,
                'year' => Carbon::now()->year,
                'total_quota' => 12,
                'used_quota' => 0,
                'remaining_quota' => 12,
            ]);

            // 4. Handle initial document uploads if present
            foreach (['KTP', 'NPWP', 'CV', 'CONTRACT'] as $type) {
                $key = 'document_' . strtolower($type);
                if (isset($documents[$key]) && $documents[$key] instanceof UploadedFile) {
                    $this->uploadDocument($employee, $type, $documents[$key]);
                }
            }

            return $employee;
        });
    }

    /**
     * Update employee & associated user details inside a transaction.
     */
    public function updateEmployee(Employee $employee, array $userData, array $employeeData): Employee
    {
        return DB::transaction(function () use ($employee, $userData, $employeeData) {
            $employee->user->update([
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);

            if (! empty($userData['role'])) {
                $role = Role::firstOrCreate(['name' => $userData['role']]);
                $employee->user->syncRoles([$role]);
            }

            $employee->update($employeeData);

            return $employee->fresh();
        });
    }

    /**
     * Upload an employee document securely to local disk.
     */
    public function uploadDocument(Employee $employee, string $type, UploadedFile $file): EmployeeDocument
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = strtolower($type) . '_' . time() . '.' . $extension;
        $dir = "documents/emp_{$employee->id}";

        $path = $file->storeAs($dir, $fileName, 'local');

        // Update or create document record
        return EmployeeDocument::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'document_type' => $type,
            ],
            [
                'file_path' => $path,
            ]
        );
    }

    /**
     * Delete an employee document from storage and database.
     */
    public function deleteDocument(EmployeeDocument $document): bool
    {
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        return $document->delete();
    }
}
