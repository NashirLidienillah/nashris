<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class Phase6AnalyticsAndAuditTest extends TestCase
{
    protected User $hrUser;
    protected User $managerUser;
    protected User $employeeUser;

    protected function setUp(): void
    {
        parent::setUp();

        Department::where('code', 'like', 'RND%')->delete();

        $this->hrUser = User::where('email', 'hr@nashris.test')->first();
        $this->managerUser = User::where('email', 'manager.it@nashris.test')->first();
        $this->employeeUser = User::where('email', 'budi.santoso@nashris.test')->first();
    }

    public function test_hr_can_view_analytics_dashboard_with_kpi_and_charts(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('hr.dashboard'));
        $response->assertStatus(200);

        // Assert Core KPI Titles and Widgets
        $response->assertSee('Total Karyawan Aktif');
        $response->assertSee('Tingkat Presensi Hari Ini');
        $response->assertSee('Review Cuti Menunggu HR');
        $response->assertSee('Estimasi Payroll Bulanan');
        $response->assertSee('Tren Kedisiplinan & Presensi (6 Bulan Terakhir)', false);
        $response->assertSee('Distribusi Divisi');
        $response->assertSee('Peringatan Berakhir Kontrak (30 Hari ke Depan)');
        $response->assertSee('Aktivitas Sistem Terkini');
    }

    public function test_hr_can_view_audit_logs_index(): void
    {
        // Generate an activity log entry first
        activity('test_log')
            ->causedBy($this->hrUser)
            ->log('Testing audit log generation for Phase 6');

        $response = $this->actingAs($this->hrUser)->get(route('hr.audit-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Audit Trail Riwayat Aktivitas Pengguna');
        $response->assertSee('Testing audit log generation for Phase 6');
    }

    public function test_non_hr_cannot_access_analytics_and_audit_logs(): void
    {
        // Manager cannot access HR dashboard or audit logs
        $responseMgrDash = $this->actingAs($this->managerUser)->get(route('hr.dashboard'));
        $responseMgrDash->assertStatus(403);

        $responseMgrAudit = $this->actingAs($this->managerUser)->get(route('hr.audit-logs.index'));
        $responseMgrAudit->assertStatus(403);

        // Employee cannot access
        $responseEmpAudit = $this->actingAs($this->employeeUser)->get(route('hr.audit-logs.index'));
        $responseEmpAudit->assertStatus(403);
    }

    public function test_model_mutations_automatically_trigger_activity_logging(): void
    {
        $initialCount = Activity::count();

        // Mutate a department
        $dept = Department::create([
            'name' => 'Research & Innovation ' . rand(100, 999),
            'code' => 'RND' . rand(10, 99),
            'description' => 'Departemen R&D baru untuk audit log testing.',
        ]);

        $this->assertGreaterThan($initialCount, Activity::count());

        $latestActivity = Activity::where('subject_type', Department::class)->where('subject_id', $dept->id)->first();
        $this->assertNotNull($latestActivity);
        $this->assertEquals('department', $latestActivity->log_name);
        $this->assertEquals('created', $latestActivity->event);
    }
}
