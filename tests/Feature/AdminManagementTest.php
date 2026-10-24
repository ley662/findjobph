<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $employer;

    private User $applicant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employer = User::factory()->create(['role' => 'employer']);
        $this->applicant = User::factory()->create(['role' => 'applicant']);
    }

    public function test_admin_can_access_admin_dashboard_and_users(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertStatus(200)->assertSee('Platform Admin Overview');
        $this->actingAs($this->admin)->get(route('admin.users'))->assertStatus(200)->assertSee('Platform Users Management');
    }

    public function test_non_admin_cannot_access_admin_portal(): void
    {
        $this->actingAs($this->employer)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($this->applicant)->get(route('admin.users'))->assertStatus(403);
    }

    public function test_admin_can_deactivate_and_activate_user(): void
    {
        $this->assertTrue($this->applicant->is_active);

        // Deactivate
        $this->actingAs($this->admin)->patch(route('admin.users.toggle', $this->applicant));
        $this->assertFalse($this->applicant->fresh()->is_active);

        // Reactivate
        $this->actingAs($this->admin)->patch(route('admin.users.toggle', $this->applicant));
        $this->assertTrue($this->applicant->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $this->actingAs($this->admin)->patch(route('admin.users.toggle', $this->admin));
        $this->assertTrue($this->admin->fresh()->is_active);
    }
}
