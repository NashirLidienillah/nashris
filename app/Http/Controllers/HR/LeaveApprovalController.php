<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\HR\ReviewLeaveRequest;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LeaveApprovalController extends Controller
{
    public function __construct(
        protected LeaveService $leaveService
    ) {}

    public function index(Request $request): View
    {
        $departments = Department::orderBy('name')->get();

        $query = LeaveRequest::with(['employee.user', 'employee.department', 'employee.position', 'employee.currentLeaveBalance', 'managerApprover', 'hrApprover']);

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $request->input('department_id')));
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->input('leave_type'));
        }

        $leaves = $query->latest()->paginate(15)->withQueryString();

        return view('hr.leaves.index', compact('departments', 'leaves'));
    }

    public function approve(ReviewLeaveRequest $request, LeaveRequest $leave): RedirectResponse
    {
        Gate::authorize('hrReview', $leave);

        try {
            $this->leaveService->hrReview($leave, auth()->user(), 'approve');

            return back()->with('success', "Pengajuan cuti {$leave->employee->user->name} disetujui (Approved). Kuota cuti terpotong dan presensi otomatis sinkron.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(ReviewLeaveRequest $request, LeaveRequest $leave): RedirectResponse
    {
        Gate::authorize('hrReview', $leave);

        try {
            $this->leaveService->hrReview($leave, auth()->user(), 'reject', $request->input('rejection_note'));

            return back()->with('success', "Pengajuan cuti {$leave->employee->user->name} telah ditolak oleh HR.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
