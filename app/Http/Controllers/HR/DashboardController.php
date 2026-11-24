<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today()->toDateString();

        $analytics = Cache::remember('hr_dashboard_analytics', 300, function () use ($today) {
            $totalEmployees = Employee::count();
            $presentToday = Attendance::where('date', $today)->whereIn('status', ['Present', 'Late'])->count();
            $attendanceRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;

            $stats = [
                'total_employees' => $totalEmployees,
                'attendance_today' => $presentToday,
                'attendance_rate' => $attendanceRate,
                'pending_leaves' => LeaveRequest::where('status', 'Pending HR')->count(),
                'monthly_payroll_estimate' => (float) Employee::sum('base_salary') + (float) Employee::sum('allowance_fixed'),
                'expiring_contracts' => Employee::whereNotNull('contract_end_date')
                    ->whereBetween('contract_end_date', [Carbon::today(), Carbon::today()->addDays(30)])
                    ->count(),
            ];

            // 1. Department Distribution Chart Data
            $departments = Department::withCount('employees')->orderBy('name')->get();
            $deptLabels = $departments->pluck('name')->toArray();
            $deptCounts = $departments->pluck('employees_count')->toArray();

            // 2. 6-Month Attendance & Late Trend Data
            $trendLabels = [];
            $trendPresent = [];
            $trendLate = [];
            $trendAbsent = [];

            for ($i = 5; $i >= 0; $i--) {
                $m = Carbon::today()->subMonths($i);
                $start = (clone $m)->startOfMonth()->toDateString();
                $end = (clone $m)->endOfMonth()->toDateString();

                $trendLabels[] = $m->translatedFormat('M Y');
                $trendPresent[] = Attendance::whereBetween('date', [$start, $end])->where('status', 'Present')->count();
                $trendLate[] = Attendance::whereBetween('date', [$start, $end])->where('status', 'Late')->count();
                $trendAbsent[] = Attendance::whereBetween('date', [$start, $end])->where('status', 'Absent')->count();
            }

            return compact('stats', 'deptLabels', 'deptCounts', 'trendLabels', 'trendPresent', 'trendLate', 'trendAbsent');
        });

        // 3. 30-Day Contract Expiry Reminder List
        $expiringEmployees = Employee::with(['department', 'position', 'user'])
            ->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [Carbon::today(), Carbon::today()->addDays(30)])
            ->orderBy('contract_end_date')
            ->limit(5)
            ->get();

        // 4. Recent Activity Logs
        $recentActivities = Activity::with('causer')->latest()->limit(5)->get();

        return view('hr.dashboard', array_merge($analytics, compact('expiringEmployees', 'recentActivities')));
    }
}
