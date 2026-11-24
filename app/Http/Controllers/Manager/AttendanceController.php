<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $managerEmp = auth()->user()->employee;
        abort_if(! $managerEmp, 403, 'Profil manajerial tidak ditemukan.');

        $department = $managerEmp->department;

        // Team members within manager's department
        $teamMembers = Employee::with('user')
            ->where('department_id', $managerEmp->department_id)
            ->where('id', '!=', $managerEmp->id)
            ->orderBy('employee_code')
            ->get();

        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = Attendance::with(['employee.user', 'employee.position', 'shift'])
            ->whereHas('employee', function ($q) use ($managerEmp) {
                $q->where('department_id', $managerEmp->department_id);
            })
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('employee_id')) {
            $reqEmpId = (int) $request->input('employee_id');
            // Anti-IDOR: verify requested employee belongs to manager's department
            if (! $teamMembers->contains('id', $reqEmpId)) {
                abort(403, 'Akses ditolak: Karyawan tidak berada di bawah divisi Anda.');
            }
            $query->where('employee_id', $reqEmpId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        return view('manager.attendance.index', compact('managerEmp', 'department', 'teamMembers', 'attendances', 'startDate', 'endDate'));
    }
}
