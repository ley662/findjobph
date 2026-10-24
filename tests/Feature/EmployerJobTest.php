<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\RequirementType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerJobTest extends TestCase
{
    use RefreshDatabase;

    private User $employer;

    private Category $category;

    private RequirementType $req;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Metro Express',
        ]);

        $this->category = Category::create([
            'name' => 'Logistics',
            'slug' => 'logistics',
        ]);

        $this->req = RequirementType::create([
            'name' => 'Valid ID',
            'slug' => 'valid-id',
        ]);
    }

    public function test_employer_can_create_job_post_with_requirements(): void
    {
        $response = $this->actingAs($this->employer)->post(route('employer.jobs.store'), [
            'title' => 'Warehouse Supervisor',
            'category_id' => $this->category->id,
            'company_name' => 'Metro Express',
            'location' => 'Pasig City',
            'job_type' => 'Full-time',
            'workplace_type' => 'On-site',
            'salary_min' => 28000,
            'salary_max' => 38000,
            'salary_period' => 'month',
            'description' => 'Oversee inventory logistics and personnel.',
            'requirements' => [$this->req->id],
        ]);

        $response->assertRedirect(route('employer.dashboard'));
        $this->assertDatabaseHas('job_posts', [
            'title' => 'Warehouse Supervisor',
            'user_id' => $this->employer->id,
        ]);

        $job = JobPost::first();
        $this->assertTrue($job->requirements->contains('id', $this->req->id));
    }

    public function test_unauthorized_user_cannot_edit_other_employer_post(): void
    {
        $job = JobPost::create([
            'user_id' => $this->employer->id,
            'category_id' => $this->category->id,
            'title' => 'Courier',
            'company_name' => 'Metro Express',
            'location' => 'Manila',
            'job_type' => 'Full-time',
            'description' => 'Desc',
        ]);

        $otherEmployer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Other Corp',
        ]);

        $response = $this->actingAs($otherEmployer)->get(route('employer.jobs.edit', $job));
        $response->assertStatus(403);
    }

    public function test_employer_can_mark_applicant_as_hired(): void
    {
        $job = JobPost::create([
            'user_id' => $this->employer->id,
            'category_id' => $this->category->id,
            'title' => 'Courier',
            'company_name' => 'Metro Express',
            'location' => 'Manila',
            'job_type' => 'Full-time',
            'description' => 'Desc',
        ]);

        $applicant = User::factory()->create(['role' => 'applicant']);

        $application = JobApplication::create([
            'job_post_id' => $job->id,
            'applicant_id' => $applicant->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->employer)
            ->patch(route('employer.applications.status', $application), [
                'status' => 'hired',
                'employer_notes' => 'Welcome to the team!',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'status' => 'hired',
            'employer_notes' => 'Welcome to the team!',
        ]);
    }
}
