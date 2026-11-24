<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Department;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index(Request $request): View
    {
        $departments = Cache::remember('departments_all', 600, fn() => Department::orderBy('name')->get());

        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = Attendance::with(['employee.user', 'employee.department', 'employee.position', 'shift'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $request->input('department_id')));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        return view('hr.attendance.index', compact('departments', 'attendances', 'startDate', 'endDate'));
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        Gate::authorize('update', $attendance);

        try {
            $this->attendanceService->correctAttendance($attendance, $request->validated());

            Cache::forget('hr_dashboard_analytics');

            return back()->with('success', "Koreksi data presensi tanggal {$attendance->date->format('d/m/Y')} ({$attendance->employee->user->name}) berhasil disimpan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
