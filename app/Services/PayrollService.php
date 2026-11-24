<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate monthly payroll draft for all active employees inside DB::transaction.
     */
    public function generateMonthlyPayroll(int $month, int $year, User $processor): Payroll
    {
        $existing = Payroll::where('month', $month)->where('year', $year)->first();

        if ($existing && $existing->status === 'Paid') {
            throw new Exception("Payroll untuk periode " . Carbon::create($year, $month, 1)->translatedFormat('F Y') . " sudah berstatus 'Paid' dan tidak dapat dibuat ulang.");
        }

        return DB::transaction(function () use ($month, $year, $processor, $existing) {
            $payroll = $existing ?: Payroll::create([
                'month' => $month,
                'year' => $year,
                'status' => 'Draft',
                'processed_by' => $processor->id,
                'processed_at' => Carbon::now(),
            ]);

            // If updating an existing Draft, clear old items first
            if ($existing) {
                $payroll->items()->delete();
                $payroll->update([
                    'processed_by' => $processor->id,
                    'processed_at' => Carbon::now(),
                ]);
            }

            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

            $employees = Employee::with(['user', 'department', 'position'])->get();

            foreach ($employees as $employee) {
                $calculation = $this->calculateEmployeePayroll($employee, $startDate, $endDate);

                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $employee->id,
                    'base_salary' => $calculation['base_salary'],
                    'allowances' => $calculation['allowances'],
                    'late_deduction' => $calculation['late_deduction'],
                    'absence_deduction' => $calculation['absence_deduction'],
                    'net_salary' => $calculation['net_salary'],
                ]);
            }

            return $payroll;
        });
    }

    /**
     * Calculate individual employee payroll breakdown for a given date range.
     */
    public function calculateEmployeePayroll(Employee $employee, string $startDate, string $endDate): array
    {
        $baseSalary = (float) $employee->base_salary;
        $allowances = (float) $employee->allowance_fixed;

        // Formula: daily_rate = base_salary / 22; minute_rate = daily_rate / 480
        $dailyRate = $baseSalary / 22;
        $minuteRate = $dailyRate / 480;

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalLateMinutes = (int) $attendances->sum('late_minutes');
        $lateDeduction = round($totalLateMinutes * $minuteRate, 2);

        $totalAbsentDays = (int) $attendances->where('status', 'Absent')->count();
        $absenceDeduction = round($totalAbsentDays * $dailyRate, 2);

        $netSalary = ($baseSalary + $allowances) - ($lateDeduction + $absenceDeduction);
        if ($netSalary < 0) {
            $netSalary = 0;
        }

        return [
            'base_salary' => $baseSalary,
            'allowances' => $allowances,
            'daily_rate' => $dailyRate,
            'minute_rate' => $minuteRate,
            'total_late_minutes' => $totalLateMinutes,
            'late_deduction' => $lateDeduction,
            'total_absent_days' => $totalAbsentDays,
            'absence_deduction' => $absenceDeduction,
            'net_salary' => round($netSalary, 2),
        ];
    }

    /**
     * Finalize payroll status from Draft to Paid.
     */
    public function finalizePayroll(Payroll $payroll): Payroll
    {
        if ($payroll->status === 'Paid') {
            throw new Exception("Payroll periode ini sudah berstatus 'Paid'.");
        }

        $payroll->update([
            'status' => 'Paid',
        ]);

        return $payroll;
    }
}
