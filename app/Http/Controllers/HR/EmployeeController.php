<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\StoreEmployeeRequest;
use App\Http\Requests\HR\UpdateEmployeeRequest;
use App\Http\Requests\HR\UploadDocumentRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Position;
use App\Models\Shift;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    public function index(Request $request): View
    {
        $departments = Department::orderBy('name')->get();
        $contractTypes = ['PKWTT', 'PKWT', 'Probation', 'Internship'];

        $query = Employee::with(['user', 'department', 'position', 'shift'])->orderBy('employee_code', 'asc');

        // Department Filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // Contract Filter
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->input('contract_type'));
        }

        // Keyword Search (Name, Code, Email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $employees = $query->paginate(12)->withQueryString();

        return view('hr.employees.index', compact('employees', 'departments', 'contractTypes'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();
        $shifts = Shift::orderBy('name')->get();
        $managers = Employee::with(['user', 'department'])->get();

        return view('hr.employees.create', compact('departments', 'positions', 'shifts', 'managers'));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ?? 'password',
        ];

        $employeeData = [
            'department_id' => $validated['department_id'],
            'position_id' => $validated['position_id'],
            'shift_id' => $validated['shift_id'],
            'manager_id' => $validated['manager_id'] ?? null,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'join_date' => $validated['join_date'],
            'contract_type' => $validated['contract_type'],
            'contract_end_date' => $validated['contract_type'] === 'PKWTT' ? null : ($validated['contract_end_date'] ?? null),
            'base_salary' => $validated['base_salary'],
            'allowance_fixed' => $validated['allowance_fixed'],
        ];

        $documents = [
            'document_ktp' => $request->file('document_ktp'),
            'document_npwp' => $request->file('document_npwp'),
            'document_cv' => $request->file('document_cv'),
            'document_contract' => $request->file('document_contract'),
        ];

        $employee = $this->employeeService->createEmployee($userData, $employeeData, $documents);

        return redirect()->route('hr.employees.show', $employee)
            ->with('success', "Karyawan {$employee->user->name} ({$employee->employee_code}) berhasil didaftarkan ke sistem.");
    }

    public function show(Employee $employee): View
    {
        Gate::authorize('view', $employee);

        $employee->load([
            'user',
            'department',
            'position',
            'manager.user',
            'subordinates.user',
            'shift',
            'documents',
            'leaveBalances',
        ]);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        Gate::authorize('update', $employee);

        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();
        $shifts = Shift::orderBy('name')->get();
        $managers = Employee::with('user')->where('id', '!=', $employee->id)->get();

        return view('hr.employees.edit', compact('employee', 'departments', 'positions', 'shifts', 'managers'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        Gate::authorize('update', $employee);

        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        $employeeData = [
            'department_id' => $validated['department_id'],
            'position_id' => $validated['position_id'],
            'shift_id' => $validated['shift_id'],
            'manager_id' => $validated['manager_id'] ?? null,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'join_date' => $validated['join_date'],
            'contract_type' => $validated['contract_type'],
            'contract_end_date' => $validated['contract_type'] === 'PKWTT' ? null : ($validated['contract_end_date'] ?? null),
            'base_salary' => $validated['base_salary'],
            'allowance_fixed' => $validated['allowance_fixed'],
        ];

        $this->employeeService->updateEmployee($employee, $userData, $employeeData);

        return redirect()->route('hr.employees.show', $employee)
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        Gate::authorize('delete', $employee);

        $name = $employee->user->name;

        // Cleanly delete employee & user
        $employee->user()->delete();

        return redirect()->route('hr.employees.index')
            ->with('success', "Karyawan {$name} telah berhasil dihapus dari sistem.");
    }

    public function uploadDocument(UploadDocumentRequest $request, Employee $employee): RedirectResponse
    {
        Gate::authorize('update', $employee);

        $type = $request->validated('document_type');
        $file = $request->file('document_file');

        $this->employeeService->uploadDocument($employee, $type, $file);

        return back()->with('success', "Dokumen {$type} berhasil diunggah.");
    }

    public function viewDocument(Employee $employee, EmployeeDocument $document): BinaryFileResponse
    {
        if ($document->employee_id !== $employee->id) {
            abort(404);
        }

        Gate::authorize('view', $document);

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Berkas dokumen fisik tidak ditemukan pada server.');
        }

        $fullPath = Storage::disk('local')->path($document->file_path);
        return response()->file($fullPath);
    }

    public function downloadDocument(Employee $employee, EmployeeDocument $document): BinaryFileResponse
    {
        if ($document->employee_id !== $employee->id) {
            abort(404);
        }

        Gate::authorize('download', $document);

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Berkas dokumen fisik tidak ditemukan pada server.');
        }

        $fullPath = Storage::disk('local')->path($document->file_path);
        $downloadName = "{$employee->employee_code}_{$document->document_type}." . pathinfo($fullPath, PATHINFO_EXTENSION);

        return response()->download($fullPath, $downloadName);
    }

    public function destroyDocument(Employee $employee, EmployeeDocument $document): RedirectResponse
    {
        if ($document->employee_id !== $employee->id) {
            abort(404);
        }

        Gate::authorize('delete', $document);

        $type = $document->document_type;
        $this->employeeService->deleteDocument($document);

        return back()->with('success', "Dokumen {$type} berhasil dihapus.");
    }
}
