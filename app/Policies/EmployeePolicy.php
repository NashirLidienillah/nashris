<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('hr') || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can view the specific employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        if ($user->hasRole('hr')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $user->employee && $user->employee->department_id === $employee->department_id;
        }

        return $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can create employees.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('hr');
    }

    /**
     * Determine whether the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->hasRole('hr');
    }

    /**
     * Determine whether the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->hasRole('hr');
    }

    /**
     * Determine whether the user can clock in/out for the employee.
     */
    public function clock(User $user, Employee $employee): bool
    {
        return $user->id === $employee->user_id;
    }
}
