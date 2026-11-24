<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Shift;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Tests\TestCase;

class Phase4AttendanceAndLeaveTest extends TestCase
{
    protected User $hrUser;
    protected User $itManagerUser;
    protected User $financeManagerUser;
    protected User $budiUser;
    protected Employee $budiEmp;
    protected Shift $regularShift;

    protected function setUp(): void
    {
        parent::setUp();

        if (User::where('email', 'hr@nashris.test')->doesntExist()) {
            $this->seed(\Database\Seeders\DummyDataSeeder::class);
        }

        $this->hrUser = User::where('email', 'hr@nashris.test')->first();
        $this->itManagerUser = User::where('email', 'manager.it@nashris.test')->first();
        $this->financeManagerUser = User::where('email', 'manager.fin@nashris.test')->first();
        $this->budiUser = User::where('email', 'budi.santoso@nashris.test')->first();
        $this->budiEmp = $this->budiUser->employee;
        $this->regularShift = Shift::where('name', 'Regular Office')->first();

        // Ensure clean attendance & leave request slate for testing dates
        Attendance::where('employee_id', $this->budiEmp->id)
            ->whereIn('date', ['2026-11-23', '2026-12-01', '2026-12-02', '2026-12-03'])
            ->delete();

        LeaveRequest::where('employee_id', $this->budiEmp->id)->delete();

        LeaveBalance::updateOrCreate(
            ['employee_id' => $this->budiEmp->id, 'year' => 2026],
            ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12]
        );
    }

    public function test_employee_can_view_attendance_page(): void
    {
        $response = $this->actingAs($this->budiUser)->get(route('employee.attendance.index'));
        $response->assertStatus(200);
        $response->assertSee('Presensi Hari Ini');
        $response->assertSee('Clock In');
    }

    public function test_employee_can_clock_in_on_time_as_present(): void
    {
        // 08:05 is within 15-minute grace period of 08:00
        $onTime = Carbon::parse('2026-11-23 08:05:00');
        Carbon::setTestNow($onTime);

        $response = $this->actingAs($this->budiUser)->post(route('employee.attendance.clock-in'), [
            'notes' => 'Tiba tepat waktu di kantor',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->budiEmp->id,
            'date' => '2026-11-23',
            'status' => 'Present',
            'late_minutes' => 0,
        ]);

        Carbon::setTestNow(); // Reset mock
    }

    public function test_employee_clocking_in_after_grace_period_is_marked_late_with_correct_minutes(): void
    {
        // 08:25 is past 08:15 grace period, so 25 minutes late from 08:00
        $lateTime = Carbon::parse('2026-11-23 08:25:00');
        Carbon::setTestNow($lateTime);

        $response = $this->actingAs($this->budiUser)->post(route('employee.attendance.clock-in'), [
            'notes' => 'Macet di jalan tol',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->budiEmp->id,
            'date' => '2026-11-23',
            'status' => 'Late',
            'late_minutes' => 25,
        ]);

        Carbon::setTestNow();
    }

    public function test_employee_cannot_clock_in_twice_on_same_day(): void
    {
        $time = Carbon::parse('2026-11-23 08:00:00');
        Carbon::setTestNow($time);

        // First Clock In
        $this->actingAs($this->budiUser)->post(route('employee.attendance.clock-in'));

        // Second Clock In Attempt
        $response = $this->actingAs($this->budiUser)->post(route('employee.attendance.clock-in'));
        $response->assertSessionHas('error');

        Carbon::setTestNow();
    }

    public function test_employee_can_clock_out(): void
    {
        // Clock In first
        $inTime = Carbon::parse('2026-11-23 08:00:00');
        Carbon::setTestNow($inTime);
        $this->actingAs($this->budiUser)->post(route('employee.attendance.clock-in'));

        // Clock Out at 17:05:00
        $outTime = Carbon::parse('2026-11-23 17:05:00');
        Carbon::setTestNow($outTime);

        $response = $this->actingAs($this->budiUser)->post(route('employee.attendance.clock-out'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->budiEmp->id,
            'date' => '2026-11-23',
            'clock_out' => '17:05:00',
        ]);

        Carbon::setTestNow();
    }

    public function test_manager_can_view_only_team_members_in_their_department(): void
    {
        $response = $this->actingAs($this->itManagerUser)->get(route('manager.attendance.index'));
        $response->assertStatus(200);

        // Budi Santoso belongs to IT Engineering -> Visible
        $response->assertSee('Budi Santoso');

        // Ahmad Fauzi or Siti belongs to Finance / HR -> Not in IT Manager's team
        $response->assertDontSee('Finance & Accounting');
    }

    public function test_hr_can_view_all_attendances_and_perform_manual_correction(): void
    {
        // Create an attendance record to correct
        $att = Attendance::create([
            'employee_id' => $this->budiEmp->id,
            'shift_id' => $this->regularShift->id,
            'date' => '2026-11-23',
            'clock_in' => '08:45:00',
            'clock_out' => '17:00:00',
            'late_minutes' => 45,
            'status' => 'Late',
            'notes' => 'Sebelum koreksi',
        ]);

        // HR updates to Present (e.g. approved morning doctor visit)
        $response = $this->actingAs($this->hrUser)->put(route('hr.attendance.update', $att), [
            'clock_in' => '08:00:00',
            'clock_out' => '17:00:00',
            'late_minutes' => 0,
            'status' => 'Present',
            'notes' => 'Koreksi HR: Izin tugas luar pagi',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'id' => $att->id,
            'status' => 'Present',
            'late_minutes' => 0,
            'notes' => 'Koreksi HR: Izin tugas luar pagi',
        ]);
    }

    public function test_employee_can_submit_leave_request_with_status_pending_manager(): void
    {
        $response = $this->actingAs($this->budiUser)->post(route('employee.leaves.store'), [
            'leave_type' => 'Annual',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'reason' => 'Liburan keluarga akhir tahun',
        ]);

        $response->assertRedirect(route('employee.leaves.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->budiEmp->id,
            'leave_type' => 'Annual',
            'total_days' => 2,
            'status' => 'Pending Manager',
        ]);
    }

    public function test_employee_cannot_submit_annual_leave_exceeding_quota(): void
    {
        // Request 25 days when quota is 12
        $response = $this->actingAs($this->budiUser)->post(route('employee.leaves.store'), [
            'leave_type' => 'Annual',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-25',
            'reason' => 'Cuti terlalu panjang melebihi kuota',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('leave_requests', [
            'reason' => 'Cuti terlalu panjang melebihi kuota',
        ]);
    }

    public function test_manager_step_1_approval_transitions_to_pending_hr(): void
    {
        $leave = LeaveRequest::create([
            'employee_id' => $this->budiEmp->id,
            'leave_type' => 'Annual',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'total_days' => 2,
            'reason' => 'Cuti tahunan testing',
            'status' => 'Pending Manager',
        ]);

        // IT Manager approves (Step 1)
        $response = $this->actingAs($this->itManagerUser)->post(route('manager.leaves.approve', $leave), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('success');

        $leave->refresh();
        $this->assertEquals('Pending HR', $leave->status);
        $this->assertEquals($this->itManagerUser->id, $leave->manager_approved_by);
    }

    public function test_manager_from_different_department_cannot_review_leave_request(): void
    {
        $leave = LeaveRequest::create([
            'employee_id' => $this->budiEmp->id,
            'leave_type' => 'Annual',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'total_days' => 2,
            'reason' => 'Cuti Budi IT',
            'status' => 'Pending Manager',
        ]);

        // Finance Manager attempts to approve IT employee's leave request -> 403 Forbidden (Anti-IDOR)
        $response = $this->actingAs($this->financeManagerUser)->post(route('manager.leaves.approve', $leave), [
            'action' => 'approve',
        ]);

        $response->assertStatus(403);
    }

    public function test_hr_step_2_final_approval_deducts_quota_and_syncs_attendance(): void
    {
        // Ensure starting balance is clean
        $balance = LeaveBalance::firstOrCreate(
            ['employee_id' => $this->budiEmp->id, 'year' => 2026],
            ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12]
        );
        $initialRemaining = $balance->remaining_quota;
        $initialUsed = $balance->used_quota;

        $leave = LeaveRequest::create([
            'employee_id' => $this->budiEmp->id,
            'leave_type' => 'Annual',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-02',
            'total_days' => 2,
            'reason' => 'Cuti tahunan 2 hari',
            'status' => 'Pending HR',
            'manager_approved_by' => $this->itManagerUser->id,
        ]);

        // HR Final Approval
        $response = $this->actingAs($this->hrUser)->post(route('hr.leaves.approve', $leave), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('success');

        $leave->refresh();
        $this->assertEquals('Approved', $leave->status);
        $this->assertEquals($this->hrUser->id, $leave->hr_approved_by);

        // Verify Quota Deduction
        $balance->refresh();
        $this->assertEquals($initialRemaining - 2, $balance->remaining_quota);
        $this->assertEquals($initialUsed + 2, $balance->used_quota);

        // Verify Attendance Synchronization (On Leave records created for 2026-12-01 and 2026-12-02)
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->budiEmp->id,
            'date' => '2026-12-01',
            'status' => 'On Leave',
        ]);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->budiEmp->id,
            'date' => '2026-12-02',
            'status' => 'On Leave',
        ]);
    }

    public function test_leave_rejection_workflow_does_not_deduct_quota(): void
    {
        $balance = LeaveBalance::where('employee_id', $this->budiEmp->id)->where('year', 2026)->first();
        $initialRemaining = $balance->remaining_quota;

        $leave = LeaveRequest::create([
            'employee_id' => $this->budiEmp->id,
            'leave_type' => 'Annual',
            'start_date' => '2026-12-10',
            'end_date' => '2026-12-12',
            'total_days' => 3,
            'reason' => 'Cuti ditolak',
            'status' => 'Pending Manager',
        ]);

        // IT Manager rejects with note
        $response = $this->actingAs($this->itManagerUser)->post(route('manager.leaves.reject', $leave), [
            'action' => 'reject',
            'rejection_note' => 'Ada rilis produksi besar minggu itu, mohon reschedule.',
        ]);

        $response->assertSessionHas('success');

        $leave->refresh();
        $this->assertEquals('Rejected', $leave->status);
        $this->assertEquals('Ada rilis produksi besar minggu itu, mohon reschedule.', $leave->rejection_note);

        // Verify quota was NOT deducted
        $balance->refresh();
        $this->assertEquals($initialRemaining, $balance->remaining_quota);
    }
}
