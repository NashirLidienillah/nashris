<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\ReviewLeaveRequest;
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
        $managerEmp = auth()->user()->employee;
        abort_if(! $managerEmp, 403, 'Profil manajerial tidak ditemukan.');

        $department = $managerEmp->department;

        $query = LeaveRequest::with(['employee.user', 'employee.position', 'managerApprover', 'hrApprover'])
            ->whereHas('employee', function ($q) use ($managerEmp) {
                $q->where('department_id', $managerEmp->department_id);
            });

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $leaves = $query->latest()->paginate(15)->withQueryString();

        return view('manager.leaves.index', compact('managerEmp', 'department', 'leaves'));
    }

    public function approve(ReviewLeaveRequest $request, LeaveRequest $leave): RedirectResponse
    {
        Gate::authorize('managerReview', $leave);

        try {
            $this->leaveService->managerReview($leave, auth()->user(), 'approve');

            return back()->with('success', "Pengajuan cuti atas nama {$leave->employee->user->name} telah disetujui (Tahap 1) dan diteruskan ke HR.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(ReviewLeaveRequest $request, LeaveRequest $leave): RedirectResponse
    {
        Gate::authorize('managerReview', $leave);

        try {
            $this->leaveService->managerReview($leave, auth()->user(), 'reject', $request->input('rejection_note'));

            return back()->with('success', "Pengajuan cuti atas nama {$leave->employee->user->name} telah ditolak.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
