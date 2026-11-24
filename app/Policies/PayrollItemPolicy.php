<?php

namespace App\Policies;

use App\Models\PayrollItem;
use App\Models\User;

class PayrollItemPolicy
{
    /**
     * Determine whether user can view an individual payslip item.
     * HR can view all; employees can only view their own Paid payslips.
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
     * Anti-IDOR: Determine whether user can download PDF payslip.
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
}
