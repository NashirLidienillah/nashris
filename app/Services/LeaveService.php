<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    /**
     * Submit a new leave request (ESS).
     */
    public function submitLeaveRequest(Employee $employee, array $data, ?UploadedFile $attachment = null): LeaveRequest
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if ($endDate->lessThan($startDate)) {
            throw new Exception("Tanggal selesai cuti tidak boleh lebih awal dari tanggal mulai.");
        }

        $totalDays = (int) $startDate->diffInDays($endDate) + 1;

        // Quota check for Annual leave
        if ($data['leave_type'] === 'Annual') {
            $year = $startDate->year;
            $leaveBalance = LeaveBalance::where('employee_id', $employee->id)->where('year', $year)->first();
            $remaining = $leaveBalance ? $leaveBalance->remaining_quota : 0;

            if ($remaining < $totalDays) {
                throw new Exception("Sisa kuota cuti tahunan tidak mencukupi (Sisa: {$remaining} hari, Pengajuan: {$totalDays} hari).");
            }
        }

        $attachmentPath = null;
        if ($attachment) {
            $attachmentPath = $attachment->store('attachments/leaves', 'local');
        }

        return LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_days' => $totalDays,
            'reason' => $data['reason'],
            'attachment_path' => $attachmentPath,
            'status' => 'Pending Manager',
        ]);
    }

    /**
     * Manager Step 1 Review: Approve or Reject.
     */
    public function managerReview(LeaveRequest $leaveRequest, User $managerUser, string $action, ?string $rejectionNote = null): LeaveRequest
    {
        if ($leaveRequest->status !== 'Pending Manager') {
            throw new Exception("Pengajuan cuti ini tidak dapat direview karena berstatus '{$leaveRequest->status}'.");
        }

        if ($action === 'approve') {
            $leaveRequest->update([
                'status' => 'Pending HR',
                'manager_approved_by' => $managerUser->id,
            ]);
        } elseif ($action === 'reject') {
            $leaveRequest->update([
                'status' => 'Rejected',
                'manager_approved_by' => $managerUser->id,
                'rejection_note' => $rejectionNote ?: 'Ditolak oleh Manager.',
            ]);
        } else {
            throw new Exception("Aksi review tidak valid: {$action}.");
        }

        return $leaveRequest;
    }

    /**
     * HR Step 2 Final Review: Approve (with Quota Deduction & Attendance Sync) or Reject.
     */
    public function hrReview(LeaveRequest $leaveRequest, User $hrUser, string $action, ?string $rejectionNote = null): LeaveRequest
    {
        if ($leaveRequest->status !== 'Pending HR') {
            throw new Exception("Pengajuan cuti ini tidak dapat diverifikasi HR karena berstatus '{$leaveRequest->status}'.");
        }

        return DB::transaction(function () use ($leaveRequest, $hrUser, $action, $rejectionNote) {
            if ($action === 'approve') {
                $leaveRequest->update([
                    'status' => 'Approved',
                    'hr_approved_by' => $hrUser->id,
                ]);

                // Deduct quota for Annual Leave
                if ($leaveRequest->leave_type === 'Annual') {
                    $year = Carbon::parse($leaveRequest->start_date)->year;
                    $leaveBalance = LeaveBalance::firstOrCreate(
                        ['employee_id' => $leaveRequest->employee_id, 'year' => $year],
                        ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12]
                    );

                    $leaveBalance->increment('used_quota', $leaveRequest->total_days);
                    $leaveBalance->decrement('remaining_quota', $leaveRequest->total_days);
                }

                // Sync to Attendances table (marking dates as 'On Leave')
                $period = CarbonPeriod::create($leaveRequest->start_date, $leaveRequest->end_date);
                $shiftId = $leaveRequest->employee->shift_id ?: Shift::first()?->id;

                foreach ($period as $date) {
                    Attendance::updateOrCreate(
                        [
                            'employee_id' => $leaveRequest->employee_id,
                            'date' => $date->toDateString(),
                        ],
                        [
                            'shift_id' => $shiftId,
                            'clock_in' => null,
                            'clock_out' => null,
                            'late_minutes' => 0,
                            'status' => 'On Leave',
                            'notes' => "Cuti {$leaveRequest->leave_type}: {$leaveRequest->reason}",
                        ]
                    );
                }
            } elseif ($action === 'reject') {
                $leaveRequest->update([
                    'status' => 'Rejected',
                    'hr_approved_by' => $hrUser->id,
                    'rejection_note' => $rejectionNote ?: 'Ditolak oleh HR.',
                ]);
            } else {
                throw new Exception("Aksi review tidak valid: {$action}.");
            }

            return $leaveRequest;
        });
    }
}
