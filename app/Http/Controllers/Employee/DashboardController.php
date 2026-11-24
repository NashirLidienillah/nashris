<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $employee = $user->employee?->load(['department', 'position', 'shift', 'manager.user']);

        $today = Carbon::today()->toDateString();
        $todayAttendance = $employee ? Attendance::where('employee_id', $employee->id)->where('date', $today)->first() : null;
        $leaveBalance = $employee ? $employee->currentLeaveBalance : null;

        $recentAttendances = $employee ? Attendance::where('employee_id', $employee->id)
            ->latest('date')
            ->limit(5)
            ->get() : collect();

        return view('employee.dashboard', compact('employee', 'todayAttendance', 'leaveBalance', 'recentAttendances'));
    }
}
