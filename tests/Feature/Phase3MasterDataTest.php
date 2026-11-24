<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase3MasterDataTest extends TestCase
{
    protected User $hrUser;
    protected User $managerUser;
    protected User $employeeUser;

    protected function setUp(): void
    {
        parent::setUp();

        Department::where('code', 'like', 'OPS%')->orWhere('code', 'like', 'RND%')->delete();

        $this->hrUser = User::where('email', 'hr@nashris.test')->first();
        $this->managerUser = User::where('email', 'manager.it@nashris.test')->first();
        $this->employeeUser = User::where('email', 'budi.santoso@nashris.test')->first();
    }

    public function test_hr_can_view_departments_index(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('hr.departments.index'));
        $response->assertStatus(200);
        $response->assertSee('IT Engineering');
        $response->assertSee('ENG');
    }

    public function test_hr_can_create_department(): void
    {
        $code = 'OPS' . rand(10, 99);
        $response = $this->actingAs($this->hrUser)->post(route('hr.departments.store'), [
            'name' => 'Operations & Logistics',
            'code' => $code,
            'description' => 'Operasional dan rantai pasok.',
        ]);

        $response->assertRedirect(route('hr.departments.index'));
        $this->assertDatabaseHas('departments', ['code' => $code]);
    }

    public function test_hr_cannot_delete_department_with_employees(): void
    {
        $dept = Department::where('code', 'ENG')->first();
        $response = $this->actingAs($this->hrUser)->delete(route('hr.departments.destroy', $dept));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('departments', ['code' => 'ENG']);
    }

    public function test_hr_can_view_and_create_position(): void
    {
        $dept = Department::first();

        $response = $this->actingAs($this->hrUser)->get(route('hr.positions.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->hrUser)->post(route('hr.positions.store'), [
            'department_id' => $dept->id,
            'title' => 'DevOps Specialist ' . rand(100, 999),
            'base_salary_default' => 14000000,
        ]);

        $response->assertRedirect(route('hr.positions.index'));
    }

    public function test_hr_can_view_employee_directory(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('hr.employees.index'));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('EMP-2026-006');
    }

    public function test_hr_can_create_employee_with_auto_generated_nik_and_leave_balance(): void
    {
        $dept = Department::first();
        $pos = Position::first();
        $shift = Shift::first();

        $randomEmail = 'test.employee.' . rand(1000, 9999) . '@nashris.test';

        $response = $this->actingAs($this->hrUser)->post(route('hr.employees.store'), [
            'name' => 'Testing New Employee',
            'email' => $randomEmail,
            'phone' => '081299998888',
            'address' => 'Jl. Kebon Jeruk No. 88, Jakarta Barat',
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'shift_id' => $shift->id,
            'join_date' => '2026-11-20',
            'contract_type' => 'PKWTT',
            'base_salary' => 10000000,
            'allowance_fixed' => 1000000,
        ]);

        $newEmp = Employee::whereHas('user', fn($q) => $q->where('email', $randomEmail))->first();
        $this->assertNotNull($newEmp);
        $this->assertStringStartsWith('EMP-' . date('Y'), $newEmp->employee_code);

        // Verify initial LeaveBalance is created
        $this->assertDatabaseHas('leave_balances', [
            'employee_id' => $newEmp->id,
            'year' => date('Y'),
            'total_quota' => 12,
            'remaining_quota' => 12,
        ]);

        $response->assertRedirect(route('hr.employees.show', $newEmp));
    }

    public function test_hr_can_upload_and_download_document_and_anti_idor_protects_other_employees(): void
    {
        Storage::fake('local');

        $emp = Employee::where('employee_code', 'EMP-2026-006')->first(); // Budi Santoso

        $file = UploadedFile::fake()->create('ktp_budi.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->hrUser)->post(route('hr.employees.documents.upload', $emp), [
            'document_type' => 'KTP',
            'document_file' => $file,
        ]);

        $response->assertSessionHas('success');

        $doc = EmployeeDocument::where('employee_id', $emp->id)->where('document_type', 'KTP')->first();
        $this->assertNotNull($doc);

        // HR can download
        $hrDownload = $this->actingAs($this->hrUser)->get(route('hr.employees.documents.download', [$emp, $doc]));
        $hrDownload->assertStatus(200);

        // Budi himself can view/download
        $budiUser = $emp->user;
        $budiDownload = $this->actingAs($budiUser)->get(route('hr.employees.documents.download', [$emp, $doc]));
        $budiDownload->assertStatus(200);

        // Another employee (EMP-2026-007) CANNOT download Budi's KTP (Anti-IDOR)
        $otherUser = User::where('email', 'ahmad.fauzi@nashris.test')->first();
        $otherDownload = $this->actingAs($otherUser)->get(route('hr.employees.documents.download', [$emp, $doc]));
        $otherDownload->assertStatus(403);
    }

    public function test_non_hr_cannot_access_hr_master_data_routes(): void
    {
        // Manager receives 403 Forbidden on HR routes
        $response = $this->actingAs($this->managerUser)->get(route('hr.departments.index'));
        $response->assertStatus(403);

        // Regular employee receives 403 Forbidden
        $response = $this->actingAs($this->employeeUser)->get(route('hr.employees.create'));
        $response->assertStatus(403);
    }
}
