<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\ClockInRequest;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    public function index(Request $request): View
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403, 'Profil karyawan tidak ditemukan.');

        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endOfMonth = (clone $startOfMonth)->endOfMonth();

        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::where('employee_id', $employee->id)->where('date', $today)->first();

        $attendances = Attendance::with('shift')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'present' => $attendances->where('status', 'Present')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'late_minutes' => $attendances->sum('late_minutes'),
            'on_leave' => $attendances->where('status', 'On Leave')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
        ];

        return view('employee.attendance.index', compact('employee', 'todayAttendance', 'attendances', 'selectedMonth', 'summary'));
    }

    public function clockIn(ClockInRequest $request): RedirectResponse
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403, 'Profil karyawan tidak ditemukan.');

        Gate::authorize('clock', $employee);

        try {
            $attendance = $this->attendanceService->clockIn($employee, $request->ip(), $request->input('notes'));
            $lateInfo = $attendance->late_minutes > 0 ? " (Terlambat {$attendance->late_minutes} menit)" : "";

            return back()->with('success', "Clock-In berhasil dicatat pada pukul {$attendance->clock_in}{$lateInfo}. Selamat bekerja!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403, 'Profil karyawan tidak ditemukan.');

        Gate::authorize('clock', $employee);

        try {
            $attendance = $this->attendanceService->clockOut($employee);

            return back()->with('success', "Clock-Out berhasil dicatat pada pukul {$attendance->clock_out}. Terima kasih atas dedikasi Anda hari ini!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
