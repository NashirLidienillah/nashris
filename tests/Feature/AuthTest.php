<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_root_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_can_be_rendered_without_demo_buttons(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('NasHRIS');
        $response->assertDontSee('Demo Login Sekali Klik');
        $response->assertDontSee('Login as HR Admin');
        $response->assertDontSee('Login as Manager');
        $response->assertDontSee('Login as Employee');
    }

    public function test_demo_login_route_is_removed(): void
    {
        $response = $this->post('/demo-login', ['role' => 'hr']);
        $response->assertStatus(404);
    }

    public function test_super_admin_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@nashris.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('hr'));
        $response->assertRedirect('/dashboard');
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $this->post('/login', [
            'email' => 'admin@nashris.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'ratelimit.test@nashris.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'ratelimit.test@nashris.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_user_with_must_change_password_is_redirected_and_can_update_password(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'firstlogin.test@nashris.com'],
            [
                'name' => 'First Login User',
                'password' => Hash::make('TempPass123'),
                'is_active' => true,
                'must_change_password' => true,
            ]
        );
        $user->assignRole('hr');

        // Accessing dashboard redirects to mandatory password change
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('password.change'));

        // Submitting new password clears must_change_password
        $updateResponse = $this->actingAs($user)->post(route('password.change.update'), [
            'password' => 'NewSecurePass123',
            'password_confirmation' => 'NewSecurePass123',
        ]);

        $updateResponse->assertRedirect(route('dashboard'));
        $this->assertFalse($user->fresh()->must_change_password);

        // Cleanup
        $user->delete();
    }

    public function test_authenticated_user_can_view_and_update_profile_and_password(): void
    {
        $admin = User::where('email', 'admin@nashris.com')->first();

        $response = $this->actingAs($admin)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profil Saya');

        // Create temporary employee profile to test phone & address update
        User::where('email', 'profile.tester@nashris.com')->delete();
        Employee::where('employee_code', 'EMP-TEST-999')->delete();

        $dept = Department::firstOrCreate(['code' => 'PROF'], ['name' => 'Profile Test Dept']);
        $pos = Position::firstOrCreate(['department_id' => $dept->id, 'title' => 'Profile Tester'], ['base_salary_default' => 5000000]);
        $shift = Shift::first();

        $tempUser = User::create([
            'name' => 'Profile Tester User',
            'email' => 'profile.tester@nashris.com',
            'password' => Hash::make('OldPass1234'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $tempUser->assignRole('employee');

        $emp = Employee::create([
            'user_id' => $tempUser->id,
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'shift_id' => $shift->id,
            'employee_code' => 'EMP-TEST-999',
            'phone' => '0811111111',
            'address' => 'Alamat Lama',
            'join_date' => '2026-01-01',
            'contract_type' => 'PKWTT',
            'base_salary' => 5000000,
            'allowance_fixed' => 0,
        ]);

        // Update phone and address
        $updateRes = $this->actingAs($tempUser)->put(route('profile.update'), [
            'phone' => '0899999999',
            'address' => 'Alamat Baru Jalan Merdeka No 1',
        ]);
        $updateRes->assertRedirect();
        $this->assertEquals('0899999999', $emp->fresh()->phone);
        $this->assertEquals('Alamat Baru Jalan Merdeka No 1', $emp->fresh()->address);

        // Update password
        $passRes = $this->actingAs($tempUser)->put(route('profile.password'), [
            'current_password' => 'OldPass1234',
            'password' => 'NewPass5678',
            'password_confirmation' => 'NewPass5678',
        ]);
        $passRes->assertRedirect();
        $this->assertTrue(Hash::check('NewPass5678', $tempUser->fresh()->password));

        // Cleanup
        $emp->delete();
        $tempUser->delete();
        $pos->delete();
        $dept->delete();
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'admin@nashris.com')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
