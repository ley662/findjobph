<?php

namespace Tests\Feature;

use App\Models\ApplicantDocument;
use App\Models\Category;
use App\Models\JobPost;
use App\Models\RequirementType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationGatingTest extends TestCase
{
    use RefreshDatabase;

    private User $employer;

    private User $applicant;

    private Category $category;

    private RequirementType $reqId;

    private RequirementType $reqLicense;

    private RequirementType $reqNbi;

    private JobPost $job;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->employer = User::factory()->create([
            'role' => 'employer',
            'company_name' => 'Fast Logistics PH',
        ]);

        $this->applicant = User::factory()->create([
            'role' => 'applicant',
        ]);

        $this->category = Category::create([
            'name' => 'Delivery',
            'slug' => 'delivery',
        ]);

        $this->reqId = RequirementType::create([
            'name' => 'Valid ID',
            'slug' => 'valid-id',
            'is_default' => true,
        ]);

        $this->reqLicense = RequirementType::create([
            'name' => "Driver's License",
            'slug' => 'drivers-license',
            'is_default' => false,
        ]);

        $this->reqNbi = RequirementType::create([
            'name' => 'NBI Clearance',
            'slug' => 'nbi-clearance',
            'is_default' => true,
        ]);

        // Job post requires Valid ID, Driver's License, and NBI Clearance
        $this->job = JobPost::create([
            'user_id' => $this->employer->id,
            'category_id' => $this->category->id,
            'title' => 'Courier Rider',
            'company_name' => 'Fast Logistics PH',
            'location' => 'Quezon City',
            'job_type' => 'Full-time',
            'workplace_type' => 'On-site',
            'salary_min' => 20000,
            'salary_max' => 30000,
            'salary_period' => 'month',
            'description' => 'Deliver parcels across Metro Manila.',
            'status' => 'active',
        ]);

        $this->job->requirements()->sync([
            $this->reqId->id => ['is_mandatory' => true],
            $this->reqLicense->id => ['is_mandatory' => true],
            $this->reqNbi->id => ['is_mandatory' => true],
        ]);
    }

    public function test_guest_is_redirected_when_attempting_to_apply(): void
    {
        $response = $this->post(route('jobs.apply', $this->job));
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_applicant_with_missing_mandatory_requirement_is_blocked_from_applying(): void
    {
        // Applicant only has Valid ID and Driver's License, missing NBI Clearance
        ApplicantDocument::create([
            'user_id' => $this->applicant->id,
            'requirement_type_id' => $this->reqId->id,
            'file_path' => 'documents/1/id.pdf',
            'file_name' => 'id.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);

        ApplicantDocument::create([
            'user_id' => $this->applicant->id,
            'requirement_type_id' => $this->reqLicense->id,
            'file_path' => 'documents/1/license.pdf',
            'file_name' => 'license.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);

        // Attempting to apply should be blocked by gating
        $response = $this->actingAs($this->applicant)
            ->from(route('jobs.show', $this->job))
            ->post(route('jobs.apply', $this->job));

        $response->assertRedirect(route('jobs.show', $this->job));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('job_applications', 0);
    }

    public function test_applicant_can_upload_missing_requirement_and_then_apply_successfully(): void
    {
        // Seed first 2 documents
        ApplicantDocument::create([
            'user_id' => $this->applicant->id,
            'requirement_type_id' => $this->reqId->id,
            'file_path' => 'documents/1/id.pdf',
            'file_name' => 'id.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);

        ApplicantDocument::create([
            'user_id' => $this->applicant->id,
            'requirement_type_id' => $this->reqLicense->id,
            'file_path' => 'documents/1/license.pdf',
            'file_name' => 'license.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);

        // 1. Upload missing NBI Clearance directly on job page
        $file = UploadedFile::fake()->create('nbi_clearance.pdf', 500, 'application/pdf');
        $uploadResponse = $this->actingAs($this->applicant)
            ->post(route('jobs.upload-requirement', $this->job), [
                'requirement_type_id' => $this->reqNbi->id,
                'document_file' => $file,
            ]);

        $uploadResponse->assertSessionHas('success');
        $this->assertDatabaseHas('applicant_documents', [
            'user_id' => $this->applicant->id,
            'requirement_type_id' => $this->reqNbi->id,
        ]);

        // 2. Now submit application
        $applyResponse = $this->actingAs($this->applicant)
            ->post(route('jobs.apply', $this->job), [
                'cover_letter' => 'Ready to ride immediately!',
            ]);

        $applyResponse->assertSessionHas('success');
        $this->assertDatabaseHas('job_applications', [
            'job_post_id' => $this->job->id,
            'applicant_id' => $this->applicant->id,
            'status' => 'pending',
            'cover_letter' => 'Ready to ride immediately!',
        ]);
    }

    public function test_cannot_apply_twice_to_same_job(): void
    {
        // Give applicant all requirements
        foreach ([$this->reqId, $this->reqLicense, $this->reqNbi] as $req) {
            ApplicantDocument::create([
                'user_id' => $this->applicant->id,
                'requirement_type_id' => $req->id,
                'file_path' => "documents/1/{$req->slug}.pdf",
                'file_name' => "{$req->slug}.pdf",
                'file_size' => 1024,
                'mime_type' => 'application/pdf',
            ]);
        }

        // First application
        $this->actingAs($this->applicant)->post(route('jobs.apply', $this->job))->assertSessionHas('success');
        $this->assertDatabaseCount('job_applications', 1);

        // Duplicate application
        $response = $this->actingAs($this->applicant)->post(route('jobs.apply', $this->job));
        $response->assertSessionHas('warning');
        $this->assertDatabaseCount('job_applications', 1);
    }
}
