<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    protected User $hrUser;
    protected User $itManagerUser;
    protected User $finManagerUser;
    protected User $budiUser; // Employee in IT
    protected Employee $budiEmp;
    protected User $nurulUser; // Employee in Finance
    protected Employee $nurulEmp;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->hrUser = User::where('email', 'hr@nashris.test')->first();
        $this->itManagerUser = User::where('email', 'manager.it@nashris.test')->first();
        $this->finManagerUser = User::where('email', 'manager.fin@nashris.test')->first();

        $this->budiUser = User::where('email', 'budi.santoso@nashris.test')->first();
        $this->budiEmp = $this->budiUser->employee;

        $this->nurulUser = User::where('email', 'nurul.hidayah@nashris.test')->first();
        $this->nurulEmp = $this->nurulUser->employee;

        Payroll::where('year', 2099)->delete();
        LeaveRequest::where('reason', 'like', '%test%')->orWhere('reason', 'like', '%Demam%')->orWhere('reason', 'like', '%keluarga%')->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Anti-IDOR: Payroll & Payslip Access Control
    |--------------------------------------------------------------------------
    */

    public function test_employee_cannot_view_another_employees_payslip(): void
    {
        // Create Paid Payroll Batch
        $payroll = Payroll::create([
            'month' => 1,
            'year' => 2099,
            'status' => 'Paid',
            'processed_by' => $this->hrUser->id,
            'finalized_at' => now(),
        ]);

        // Payslip for Nurul (Finance)
        $nurulItem = PayrollItem::create([
            'payroll_id' => $payroll->id,
            'employee_id' => $this->nurulEmp->id,
            'base_salary' => 11000000,
            'allowances' => 1500000,
            'late_deduction' => 0,
            'absence_deduction' => 0,
            'net_salary' => 12500000,
        ]);

        // Budi attempts to view Nurul's payslip
        $response = $this->actingAs($this->budiUser)->get(route('employee.payslips.show', $nurulItem));
        $response->assertStatus(403);
    }

    public function test_employee_cannot_download_another_employees_payslip_pdf(): void
    {
        $payroll = Payroll::create([
            'month' => 2,
            'year' => 2099,
            'status' => 'Paid',
            'processed_by' => $this->hrUser->id,
            'finalized_at' => now(),
        ]);

        $nurulItem = PayrollItem::create([
            'payroll_id' => $payroll->id,
            'employee_id' => $this->nurulEmp->id,
            'base_salary' => 11000000,
            'allowances' => 1500000,
            'late_deduction' => 0,
            'absence_deduction' => 0,
            'net_salary' => 12500000,
        ]);

        // Budi attempts to download Nurul's payslip PDF
        $response = $this->actingAs($this->budiUser)->get(route('employee.payslips.download', $nurulItem));
        $response->assertStatus(403);
    }

    public function test_employee_cannot_view_or_download_own_payslip_when_payroll_is_draft(): void
    {
        $draftPayroll = Payroll::create([
            'month' => 3,
            'year' => 2099,
            'status' => 'Draft',
            'processed_by' => $this->hrUser->id,
        ]);

        $budiItem = PayrollItem::create([
            'payroll_id' => $draftPayroll->id,
            'employee_id' => $this->budiEmp->id,
            'base_salary' => 11500000,
            'allowances' => 1500000,
            'late_deduction' => 0,
            'absence_deduction' => 0,
            'net_salary' => 13000000,
        ]);

        // Budi attempts to view and download own draft payslip
        $responseView = $this->actingAs($this->budiUser)->get(route('employee.payslips.show', $budiItem));
        $responseView->assertStatus(403);

        $responseDownload = $this->actingAs($this->budiUser)->get(route('employee.payslips.download', $budiItem));
        $responseDownload->assertStatus(403);
    }

    public function test_owner_employee_can_view_and_download_own_paid_payslip(): void
    {
        $paidPayroll = Payroll::create([
            'month' => 4,
            'year' => 2099,
            'status' => 'Paid',
            'processed_by' => $this->hrUser->id,
            'finalized_at' => now(),
        ]);

        $budiItem = PayrollItem::create([
            'payroll_id' => $paidPayroll->id,
            'employee_id' => $this->budiEmp->id,
            'base_salary' => 11500000,
            'allowances' => 1500000,
            'late_deduction' => 0,
            'absence_deduction' => 0,
            'net_salary' => 13000000,
        ]);

        $responseView = $this->actingAs($this->budiUser)->get(route('employee.payslips.show', $budiItem));
        $responseView->assertStatus(200);

        $responseDownload = $this->actingAs($this->budiUser)->get(route('employee.payslips.download', $budiItem));
        $responseDownload->assertStatus(200);
        $this->assertEquals('application/pdf', $responseDownload->headers->get('content-type'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Anti-IDOR & Private File Storage: Employee Documents (KTP/NPWP/Contract)
    |--------------------------------------------------------------------------
    */

    public function test_employee_cannot_view_or_download_another_employees_private_document(): void
    {
        // Seed private document for Nurul
        $nurulDoc = EmployeeDocument::create([
            'employee_id' => $this->nurulEmp->id,
            'document_type' => 'KTP',
            'file_path' => "documents/emp_{$this->nurulEmp->id}/ktp_test.pdf",
        ]);
        Storage::disk('local')->put($nurulDoc->file_path, '%PDF-1.4 dummy ktp');

        // Budi attempts to view Nurul's KTP via URL ID manipulation
        $responseView = $this->actingAs($this->budiUser)->get(route('hr.employees.documents.view', [$this->nurulEmp, $nurulDoc]));
        $responseView->assertStatus(403);

        // Budi attempts to download Nurul's KTP
        $responseDownload = $this->actingAs($this->budiUser)->get(route('hr.employees.documents.download', [$this->nurulEmp, $nurulDoc]));
        $responseDownload->assertStatus(403);
    }

    public function test_manager_cannot_view_private_documents_of_employees(): void
    {
        $nurulDoc = EmployeeDocument::create([
            'employee_id' => $this->nurulEmp->id,
            'document_type' => 'NPWP',
            'file_path' => "documents/emp_{$this->nurulEmp->id}/npwp_test.pdf",
        ]);
        Storage::disk('local')->put($nurulDoc->file_path, '%PDF-1.4 dummy npwp');

        // Manager IT attempts to view Nurul's document
        $response = $this->actingAs($this->itManagerUser)->get(route('hr.employees.documents.view', [$this->nurulEmp, $nurulDoc]));
        $response->assertStatus(403);
    }

    public function test_owner_employee_and_hr_can_view_and_download_own_document(): void
    {
        $budiDoc = EmployeeDocument::create([
            'employee_id' => $this->budiEmp->id,
            'document_type' => 'CONTRACT',
            'file_path' => "documents/emp_{$this->budiEmp->id}/contract_test.pdf",
        ]);
        Storage::disk('local')->put($budiDoc->file_path, '%PDF-1.4 dummy contract');

        // Owner (Budi) can view & download
        $responseView = $this->actingAs($this->budiUser)->get(route('hr.employees.documents.view', [$this->budiEmp, $budiDoc]));
        $responseView->assertStatus(200);

        $responseDownload = $this->actingAs($this->budiUser)->get(route('hr.employees.documents.download', [$this->budiEmp, $budiDoc]));
        $responseDownload->assertStatus(200);

        // HR can also view & download
        $responseHr = $this->actingAs($this->hrUser)->get(route('hr.employees.documents.download', [$this->budiEmp, $budiDoc]));
        $responseHr->assertStatus(200);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Anti-IDOR: Leave Attachments Access Control
    |--------------------------------------------------------------------------
    */

    public function test_employee_cannot_view_another_employees_leave_attachment(): void
    {
        $attachmentPath = 'attachments/leaves/nurul_medical.pdf';
        Storage::disk('local')->put($attachmentPath, '%PDF-1.4 medical certificate');

        $nurulLeave = LeaveRequest::create([
            'employee_id' => $this->nurulEmp->id,
            'leave_type' => 'Sick',
            'start_date' => Carbon::now()->addDays(1)->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'total_days' => 2,
            'reason' => 'Demam dan flu berat',
            'status' => 'Pending Manager',
            'attachment_path' => $attachmentPath,
        ]);

        // Budi attempts to view Nurul's medical attachment
        $response = $this->actingAs($this->budiUser)->get(route('leaves.attachment', $nurulLeave));
        $response->assertStatus(403);
    }

    public function test_owner_employee_their_department_manager_and_hr_can_view_leave_attachment(): void
    {
        $attachmentPath = 'attachments/leaves/budi_medical.pdf';
        Storage::disk('local')->put($attachmentPath, '%PDF-1.4 medical certificate');

        $budiLeave = LeaveRequest::create([
            'employee_id' => $this->budiEmp->id,
            'leave_type' => 'Sick',
            'start_date' => Carbon::now()->addDays(5)->toDateString(),
            'end_date' => Carbon::now()->addDays(6)->toDateString(),
            'total_days' => 2,
            'reason' => 'Sakit tipes',
            'status' => 'Pending Manager',
            'attachment_path' => $attachmentPath,
        ]);

        // 1. Budi (Owner) CAN view
        $responseOwner = $this->actingAs($this->budiUser)->get(route('leaves.attachment', $budiLeave));
        $responseOwner->assertStatus(200);

        // 2. IT Manager (Budi's department manager) CAN view
        $responseManager = $this->actingAs($this->itManagerUser)->get(route('leaves.attachment', $budiLeave));
        $responseManager->assertStatus(200);

        // 3. HR CAN view
        $responseHr = $this->actingAs($this->hrUser)->get(route('leaves.attachment', $budiLeave));
        $responseHr->assertStatus(200);

        // 4. Finance Manager (Different department) CANNOT view -> 403
        $responseFinManager = $this->actingAs($this->finManagerUser)->get(route('leaves.attachment', $budiLeave));
        $responseFinManager->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Manager Department Isolation: Cross-Department Prevention
    |--------------------------------------------------------------------------
    */

    public function test_manager_cannot_approve_leave_of_employee_from_different_department(): void
    {
        // Leave request from Nurul (Finance)
        $nurulLeave = LeaveRequest::create([
            'employee_id' => $this->nurulEmp->id,
            'leave_type' => 'Annual',
            'start_date' => Carbon::now()->addDays(10)->toDateString(),
            'end_date' => Carbon::now()->addDays(12)->toDateString(),
            'total_days' => 3,
            'reason' => 'Liburan keluarga',
            'status' => 'Pending Manager',
        ]);

        // IT Manager attempts to approve Nurul's Finance leave
        $response = $this->actingAs($this->itManagerUser)->post(route('manager.leaves.approve', $nurulLeave), [
            'action' => 'approve',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Pending Manager', $nurulLeave->fresh()->status);
    }

    public function test_manager_cannot_reject_leave_of_employee_from_different_department(): void
    {
        $nurulLeave = LeaveRequest::create([
            'employee_id' => $this->nurulEmp->id,
            'leave_type' => 'Annual',
            'start_date' => Carbon::now()->addDays(15)->toDateString(),
            'end_date' => Carbon::now()->addDays(16)->toDateString(),
            'total_days' => 2,
            'reason' => 'Urusan keluarga',
            'status' => 'Pending Manager',
        ]);

        // IT Manager attempts to reject Nurul's Finance leave
        $response = $this->actingAs($this->itManagerUser)->post(route('manager.leaves.reject', $nurulLeave), [
            'action' => 'reject',
            'rejection_note' => 'Penolakan tidak sah antar departemen',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Pending Manager', $nurulLeave->fresh()->status);
    }

    public function test_manager_cannot_filter_attendance_of_employee_outside_department(): void
    {
        // IT Manager attempts to filter attendance by Nurul (Finance employee ID)
        $response = $this->actingAs($this->itManagerUser)->get(route('manager.attendance.index', [
            'employee_id' => $this->nurulEmp->id,
        ]));

        $response->assertStatus(403);
    }
}
