<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $managerEmp = $user->employee;
        $deptId = $managerEmp?->department_id;

        // Strictly isolated to department team members (excluding manager)
        $teamEmployees = $managerEmp
            ? \App\Models\Employee::where('department_id', $deptId)->where('id', '!=', $managerEmp->id)->pluck('id')
            : collect();

        $today = Carbon::today()->toDateString();

        $stats = [
            'team_members' => $teamEmployees->count(),
            'team_present_today' => Attendance::whereIn('employee_id', $teamEmployees)->where('date', $today)->where('status', 'Present')->count(),
            'pending_approval' => LeaveRequest::whereIn('employee_id', $teamEmployees)->where('status', 'Pending Manager')->count(),
        ];

        $pendingLeaves = LeaveRequest::with(['employee.user', 'employee.position'])
            ->whereIn('employee_id', $teamEmployees)
            ->where('status', 'Pending Manager')
            ->latest()
            ->limit(5)
            ->get();

        return view('manager.dashboard', compact('stats', 'pendingLeaves', 'managerEmp'));
    }
}
