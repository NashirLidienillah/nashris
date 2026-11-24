<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;

class AttendancePolicy
{
    /**
     * Determine whether the user can view attendances list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['hr', 'manager', 'employee']);
    }

    /**
     * Determine whether the user can view specific attendance record.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole('hr')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $user->employee && $attendance->employee && (
                $user->employee->department_id === $attendance->employee->department_id ||
                $attendance->employee->manager_id === $user->employee->id
            );
        }

        return $attendance->employee && $user->id === $attendance->employee->user_id;
    }

    /**
     * Determine whether the user can clock in/out for the given employee.
     */
    public function clock(User $user, Employee $employee): bool
    {
        return $user->id === $employee->user_id;
    }

    /**
     * Determine whether the user can update/correct the attendance.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        return $user->hasRole('hr');
    }
}
