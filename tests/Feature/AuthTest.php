<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_root_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('NasHRIS');
        $response->assertSee('Login as HR Admin');
        $response->assertSee('Login as Manager');
        $response->assertSee('Login as Employee');
    }

    public function test_user_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => 'hr@nashris.test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $this->post('/login', [
            'email' => 'hr@nashris.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_one_click_demo_login_as_hr_works(): void
    {
        $response = $this->post('/demo-login', ['role' => 'hr']);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('hr'));
        $response->assertRedirect('/dashboard');
    }

    public function test_one_click_demo_login_as_manager_works(): void
    {
        $response = $this->post('/demo-login', ['role' => 'manager']);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('manager'));
        $response->assertRedirect('/dashboard');
    }

    public function test_one_click_demo_login_as_employee_works(): void
    {
        $response = $this->post('/demo-login', ['role' => 'employee']);

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->hasRole('employee'));
        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'hr@nashris.test')->first();

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
