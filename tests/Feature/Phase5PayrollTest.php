<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Shift;
use App\Models\User;
use App\Services\PayrollService;
use Carbon\Carbon;
use Tests\TestCase;

class Phase5PayrollTest extends TestCase
{
    protected User $hrUser;
    protected User $managerUser;
    protected User $budiUser;
    protected Employee $budiEmp;
    protected User $otherUser;
    protected Employee $otherEmp;
    protected Shift $regularShift;

    protected function setUp(): void
    {
        parent::setUp();

        if (User::where('email', 'hr@nashris.test')->doesntExist()) {
            $this->seed(\Database\Seeders\DummyDataSeeder::class);
        }

        $this->hrUser = User::where('email', 'hr@nashris.test')->first();
        $this->managerUser = User::where('email', 'manager.it@nashris.test')->first();
        $this->budiUser = User::where('email', 'budi.santoso@nashris.test')->first();
        $this->budiEmp = $this->budiUser->employee;
        $this->otherUser = User::where('email', 'ahmad.fauzi@nashris.test')->first();
        $this->otherEmp = $this->otherUser->employee;
        $this->regularShift = Shift::where('name', 'Regular Office')->first();

        // Clean slate for test payroll period: Month 10 Year 2026
        Payroll::where('month', 10)->where('year', 2026)->delete();
        Payroll::where('month', 11)->where('year', 2026)->delete();
    }

    public function test_hr_can_view_payrolls_index(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('hr.payrolls.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Batch Payroll Karyawan');
        $response->assertSee('Generate Payroll Bulanan');
    }

    public function test_hr_can_generate_monthly_payroll_draft_for_all_employees(): void
    {
        $response = $this->actingAs($this->hrUser)->post(route('hr.payrolls.store'), [
            'month' => 10,
            'year' => 2026,
        ]);

        $payroll = Payroll::where('month', 10)->where('year', 2026)->first();
        $this->assertNotNull($payroll);
        $this->assertEquals('Draft', $payroll->status);
        $this->assertEquals($this->hrUser->id, $payroll->processed_by);

        // All active employees have payroll items
        $this->assertGreaterThan(0, $payroll->items()->count());

        $response->assertRedirect(route('hr.payrolls.show', $payroll));
        $response->assertSessionHas('success');
    }

    public function test_payroll_calculation_accurately_computes_late_and_absence_deductions(): void
    {
        // Setup Budi's attendances in October 2026:
        // Clear October attendances for Budi
        Attendance::where('employee_id', $this->budiEmp->id)
            ->whereBetween('date', ['2026-10-01', '2026-10-31'])
            ->delete();

        // 1 Late day: 60 minutes late
        Attendance::create([
            'employee_id' => $this->budiEmp->id,
            'shift_id' => $this->regularShift->id,
            'date' => '2026-10-05',
            'clock_in' => '09:00:00',
            'clock_out' => '17:00:00',
            'late_minutes' => 60,
            'status' => 'Late',
        ]);

        // 1 Absent day
        Attendance::create([
            'employee_id' => $this->budiEmp->id,
            'shift_id' => $this->regularShift->id,
            'date' => '2026-10-06',
            'clock_in' => null,
            'clock_out' => null,
            'late_minutes' => 0,
            'status' => 'Absent',
        ]);

        // Generate Payroll
        $payrollService = app(PayrollService::class);
        $payroll = $payrollService->generateMonthlyPayroll(10, 2026, $this->hrUser);

        $budiItem = PayrollItem::where('payroll_id', $payroll->id)
            ->where('employee_id', $this->budiEmp->id)
            ->first();

        $this->assertNotNull($budiItem);

        // Expected calculations
        $base = (float) $this->budiEmp->base_salary;
        $allowances = (float) $this->budiEmp->allowance_fixed;
        $dailyRate = $base / 22;
        $minuteRate = $dailyRate / 480;

        $expectedLateDeduction = round(60 * $minuteRate, 2);
        $expectedAbsenceDeduction = round(1 * $dailyRate, 2);
        $expectedNet = round(($base + $allowances) - ($expectedLateDeduction + $expectedAbsenceDeduction), 2);

        $this->assertEqualsWithDelta($expectedLateDeduction, (float) $budiItem->late_deduction, 1.0);
        $this->assertEqualsWithDelta($expectedAbsenceDeduction, (float) $budiItem->absence_deduction, 1.0);
        $this->assertEqualsWithDelta($expectedNet, (float) $budiItem->net_salary, 1.0);
    }

    public function test_hr_can_finalize_payroll_from_draft_to_paid(): void
    {
        $payroll = Payroll::create([
            'month' => 10,
            'year' => 2026,
            'status' => 'Draft',
            'processed_by' => $this->hrUser->id,
            'processed_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->hrUser)->post(route('hr.payrolls.finalize', $payroll));
        $response->assertSessionHas('success');

        $payroll->refresh();
        $this->assertEquals('Paid', $payroll->status);
    }

    public function test_employee_cannot_view_or_download_draft_payslip(): void
    {
        $payroll = Payroll::create([
            'month' => 10,
            'year' => 2026,
            'status' => 'Draft',
            'processed_by' => $this->hrUser->id,
            'processed_at' => Carbon::now(),
        ]);

        $item = PayrollItem::create([
            'payroll_id' => $payroll->id,
            'employee_id' => $this->budiEmp->id,
            'base_salary' => $this->budiEmp->base_salary,
            'allowances' => $this->budiEmp->allowance_fixed,
            'late_deduction' => 0,
            'absence_deduction' => 0,
            'net_salary' => $this->budiEmp->base_salary + $this->budiEmp->allowance_fixed,
        ]);

        // Attempt view draft -> 403 Forbidden
        $responseView = $this->actingAs($this->budiUser)->get(route('employee.payslips.show', $item));
        $responseView->assertStatus(403);

        // Attempt download draft -> 403 Forbidden
        $responseDownload = $this->actingAs($this->budiUser)->get(route('employee.payslips.download', $item));
        $responseDownload->assertStatus(403);
    }

    public function test_employee_can_view_and_download_their_own_paid_payslip_pdf(): void
    {
        $payroll = Payroll::create([
            'month' => 10,
            'year' => 2026,
            'status' => 'Paid',
            'processed_by' => $this->hrUser->id,
            'processed_at' => Carbon::now(),
        ]);

        $item = PayrollItem::create([
            'payroll_id' => $payroll->id,
            'employee_id' => $this->budiEmp->id,
            'base_salary' => $this->budiEmp->base_salary,
            'allowances' => $this->budiEmp->allowance_fixed,
            'late_deduction' => 0,
            'absence_deduction' => 0,
            'net_salary' => $this->budiEmp->base_salary + $this->budiEmp->allowance_fixed,
        ]);

        // Index lists it
        $indexResponse = $this->actingAs($this->budiUser)->get(route('employee.payslips.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Oktober 2026');

        // Show view
        $showResponse = $this->actingAs($this->budiUser)->get(route('employee.payslips.show', $item));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Slip Gaji Elektronik Resmi');

        // Download PDF
        $downloadResponse = $this->actingAs($this->budiUser)->get(route('employee.payslips.download', $item));
        $downloadResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $downloadResponse->headers->get('Content-Type'));
    }

    public function test_employee_cannot_download_another_employees_payslip_anti_idor(): void
    {
        $payroll = Payroll::create([
            'month' => 10,
            'year' => 2026,
            'status' => 'Paid',
            'processed_by' => $this->hrUser->id,
            'processed_at' => Carbon::now(),
        ]);

        // Ahmad Fauzi's payslip
        $ahmadItem = PayrollItem::create([
            'payroll_id' => $payroll->id,
            'employee_id' => $this->otherEmp->id,
            'base_salary' => $this->otherEmp->base_salary,
            'allowances' => $this->otherEmp->allowance_fixed,
            'late_deduction' => 0,
            'absence_deduction' => 0,
            'net_salary' => $this->otherEmp->base_salary + $this->otherEmp->allowance_fixed,
        ]);

        // Budi attempts to access Ahmad's payslip -> 403 Forbidden
        $response = $this->actingAs($this->budiUser)->get(route('employee.payslips.download', $ahmadItem));
        $response->assertStatus(403);
    }

    public function test_non_hr_cannot_generate_or_finalize_payroll(): void
    {
        $payroll = Payroll::create([
            'month' => 10,
            'year' => 2026,
            'status' => 'Draft',
            'processed_by' => $this->hrUser->id,
            'processed_at' => Carbon::now(),
        ]);

        // Regular employee attempts generate -> 403
        $resp1 = $this->actingAs($this->budiUser)->post(route('hr.payrolls.store'), ['month' => 11, 'year' => 2026]);
        $resp1->assertStatus(403);

        // Manager attempts finalize -> 403
        $resp2 = $this->actingAs($this->managerUser)->post(route('hr.payrolls.finalize', $payroll));
        $resp2->assertStatus(403);
    }
}
