<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\User;

class PayrollPolicy
{
    /**
     * Determine whether user can view payroll list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['hr', 'employee']);
    }

    /**
     * Determine whether user can view the payroll batch (HR only).
     */
    public function viewBatch(User $user, Payroll $payroll): bool
    {
        return $user->hasRole('hr');
    }

    /**
     * Anti-IDOR: Determine whether user can view an individual payroll item.
     * Employees can only view their own item if status is Paid.
     */
    public function view(User $user, PayrollItem $item): bool
    {
        if ($user->hasRole('hr')) {
            return true;
        }

        if ($user->hasRole('employee')) {
            return $user->id === $item->employee->user_id && $item->payroll->status === 'Paid';
        }

        return false;
    }

    /**
     * Anti-IDOR: Determine whether user can download the PDF payslip.
     * Employees can only download their own payslip if status is Paid.
     */
    public function downloadPdf(User $user, PayrollItem $item): bool
    {
        if ($user->hasRole('hr')) {
            return true;
        }

        if ($user->hasRole('employee')) {
            return $user->id === $item->employee->user_id && $item->payroll->status === 'Paid';
        }

        return false;
    }

    /**
     * Only HR can generate payroll.
     */
    public function generate(User $user): bool
    {
        return $user->hasRole('hr');
    }

    /**
     * Only HR can finalize payroll.
     */
    public function finalize(User $user): bool
    {
        return $user->hasRole('hr');
    }
}
