<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreLeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function __construct(
        protected LeaveService $leaveService
    ) {}

    public function index(Request $request): View
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403, 'Profil karyawan tidak ditemukan.');

        $currentYear = (int) Carbon::now()->year;
        $leaveBalance = $employee->currentLeaveBalance ?: LeaveBalance::firstOrCreate(
            ['employee_id' => $employee->id, 'year' => $currentYear],
            ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12]
        );

        $leaves = LeaveRequest::with(['managerApprover', 'hrApprover'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('employee.leaves.index', compact('employee', 'leaveBalance', 'leaves'));
    }

    public function create(): View
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403, 'Profil karyawan tidak ditemukan.');

        Gate::authorize('create', LeaveRequest::class);

        $currentYear = (int) Carbon::now()->year;
        $leaveBalance = $employee->currentLeaveBalance ?: LeaveBalance::firstOrCreate(
            ['employee_id' => $employee->id, 'year' => $currentYear],
            ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12]
        );

        return view('employee.leaves.create', compact('employee', 'leaveBalance'));
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403, 'Profil karyawan tidak ditemukan.');

        Gate::authorize('create', LeaveRequest::class);

        try {
            $leave = $this->leaveService->submitLeaveRequest(
                $employee,
                $request->validated(),
                $request->file('attachment')
            );

            return redirect()->route('employee.leaves.index')
                ->with('success', "Permohonan cuti {$leave->leave_type} selama {$leave->total_days} hari berhasil diajukan dan sedang menunggu review Manager.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
