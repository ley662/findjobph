<?php

namespace App\Http\Controllers;

use App\Models\ApplicantDocument;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\RequirementType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JobDetailController extends Controller
{
    /**
     * Display job post details with requirements checklist.
     */
    public function show(JobPost $job): View
    {
        $job->increment('views_count');
        $job->load(['category', 'employer', 'requirements', 'applications']);

        $user = Auth::user();
        $hasApplied = false;
        $missingRequirements = collect();
        $isRequirementsComplete = false;
        $existingApplication = null;

        if ($user && $user->isApplicant()) {
            $user->load('documents.requirementType');
            $hasApplied = $job->hasApplicantApplied($user);
            $missingRequirements = $job->missingRequirementsFor($user);
            $isRequirementsComplete = $job->hasApplicantSatisfiedRequirements($user);

            if ($hasApplied) {
                $existingApplication = $job->applications->firstWhere('applicant_id', $user->id);
            }
        }

        // Related jobs in same category
        $relatedJobs = JobPost::where('status', 'active')
            ->where('id', '!=', $job->id)
            ->where('category_id', $job->category_id)
            ->latest()
            ->take(3)
            ->get();

        return view('jobs.show', compact(
            'job',
            'hasApplied',
            'missingRequirements',
            'isRequirementsComplete',
            'existingApplication',
            'relatedJobs'
        ));
    }

    /**
     * Submit job application (strictly gated by mandatory requirements).
     */
    public function apply(Request $request, JobPost $job): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ! $user->isApplicant()) {
            return redirect()->route('login')->with('warning', 'Please sign in as an applicant to apply.');
        }

        // Check if already applied
        if ($job->hasApplicantApplied($user)) {
            return back()->with('warning', 'You have already submitted an application for this position.');
        }

        // STRICT GATING: Check if all requirements are met
        $missing = $job->missingRequirementsFor($user);
        if ($missing->isNotEmpty()) {
            $missingNames = $missing->pluck('name')->join(', ');

            return back()->with('error', "Application blocked: You must upload the following required documents before applying: {$missingNames}.");
        }

        $validated = $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:2000'],
        ]);

        JobApplication::create([
            'job_post_id' => $job->id,
            'applicant_id' => $user->id,
            'status' => 'pending',
            'cover_letter' => $validated['cover_letter'] ?? null,
        ]);

        return back()->with('success', "Your application to {$job->company_name} for {$job->title} has been submitted successfully!");
    }

    /**
     * Upload a requirement document directly from the job detail view.
     */
    public function uploadRequirement(Request $request, JobPost $job): RedirectResponse
    {
        $user = Auth::user();

        if (! $user || ! $user->isApplicant()) {
            return redirect()->route('login')->with('warning', 'Please sign in to upload documents.');
        }

        $validated = $request->validate([
            'requirement_type_id' => ['required', 'exists:requirement_types,id'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $request->file('document_file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $fileSize = $file->getSize();

        $path = $file->store("documents/{$user->id}", 'public');

        ApplicantDocument::updateOrCreate(
            [
                'user_id' => $user->id,
                'requirement_type_id' => $validated['requirement_type_id'],
            ],
            [
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $reqType = RequirementType::find($validated['requirement_type_id']);

        return back()->with('success', "Successfully uploaded {$reqType?->name}! You can now proceed with your application if all requirements are met.");
    }
}
