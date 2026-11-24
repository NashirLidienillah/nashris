<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    /**
     * Determine whether the user can view any leave requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['hr', 'manager', 'employee']);
    }

    /**
     * Determine whether the user can view the specific leave request.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->hasRole('hr')) {
            return true;
        }

        if ($user->hasRole('manager')) {
            return $user->employee && (
                $user->employee->department_id === $leaveRequest->employee->department_id ||
                $leaveRequest->employee->manager_id === $user->employee->id
            );
        }

        return $user->id === $leaveRequest->employee->user_id;
    }

    /**
     * Determine whether the user can create leave requests.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('employee') && $user->employee !== null;
    }

    /**
     * Determine whether the user (as manager) can review this request.
     */
    public function managerReview(User $user, LeaveRequest $leaveRequest): bool
    {
        if (! $user->hasRole('manager')) {
            return false;
        }

        if ($leaveRequest->status !== 'Pending Manager') {
            return false;
        }

        return $user->employee && (
            $user->employee->department_id === $leaveRequest->employee->department_id ||
            $leaveRequest->employee->manager_id === $user->employee->id
        );
    }

    /**
     * Determine whether the user (as HR) can review this request.
     */
    public function hrReview(User $user, LeaveRequest $leaveRequest): bool
    {
        if (! $user->hasRole('hr')) {
            return false;
        }

        return $leaveRequest->status === 'Pending HR';
    }
}
