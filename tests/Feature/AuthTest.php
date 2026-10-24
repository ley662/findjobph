<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_and_register_pages(): void
    {
        $this->get(route('login'))->assertStatus(200)->assertSee('Sign in to FindJob');
        $this->get(route('register'))->assertStatus(200)->assertSee('Job Applicant');
    }

    public function test_user_can_register_as_applicant(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Carlos Delgado',
            'email' => 'carlos@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'applicant',
        ]);

        $response->assertRedirect(route('jobs.feed'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'carlos@example.com',
            'role' => 'applicant',
        ]);
    }

    public function test_user_can_register_as_employer_with_company_name(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'HR Manager',
            'email' => 'hr@manilatrucks.ph',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'employer',
            'company_name' => 'Manila Heavy Trucks Inc.',
        ]);

        $response->assertRedirect(route('employer.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'hr@manilatrucks.ph',
            'role' => 'employer',
            'company_name' => 'Manila Heavy Trucks Inc.',
        ]);
    }

    public function test_cannot_self_register_as_admin(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Malicious User',
            'email' => 'hacker@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }

    public function test_admin_logs_in_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_employer_logs_in_and_redirects_to_employer_dashboard(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Fast Express',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $employer->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('employer.dashboard'));
    }

    public function test_suspended_user_cannot_login(): void
    {
        $suspended = User::factory()->create([
            'role' => 'applicant',
            'is_active' => false,
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $suspended->email,
            'password' => 'secret123',
        ]);

        $this->assertGuest();
        $response->assertSessionHas('error');
    }
}
