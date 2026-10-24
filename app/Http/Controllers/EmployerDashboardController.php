<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\RequirementType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployerDashboardController extends Controller
{
    /**
     * Employer overview dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        $jobPosts = JobPost::where('user_id', $user->id)
            ->with(['category', 'requirements'])
            ->withCount('applications')
            ->latest()
            ->get();

        $jobPostIds = $jobPosts->pluck('id');

        $totalJobs = $jobPosts->count();
        $activeJobs = $jobPosts->where('status', 'active')->count();

        $totalApplicants = JobApplication::whereIn('job_post_id', $jobPostIds)->count();
        $hiredCount = JobApplication::whereIn('job_post_id', $jobPostIds)->where('status', 'hired')->count();
        $pendingCount = JobApplication::whereIn('job_post_id', $jobPostIds)->where('status', 'pending')->count();

        $recentApplications = JobApplication::whereIn('job_post_id', $jobPostIds)
            ->with(['applicant.documents.requirementType', 'jobPost'])
            ->latest()
            ->take(5)
            ->get();

        return view('employer.dashboard', compact(
            'jobPosts',
            'totalJobs',
            'activeJobs',
            'totalApplicants',
            'hiredCount',
            'pendingCount',
            'recentApplications'
        ));
    }

    /**
     * Show form to create a new job post.
     */
    public function createJob(): View
    {
        $categories = Category::all();
        $requirementTypes = RequirementType::all();

        return view('employer.jobs.create', compact('categories', 'requirementTypes'));
    }

    /**
     * Store a new job post.
     */
    public function storeJob(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'job_type' => ['required', 'string', 'in:Full-time,Part-time,Contract,Freelance,Internship'],
            'workplace_type' => ['required', 'string', 'in:On-site,Remote,Hybrid'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'salary_period' => ['required', 'string', 'in:month,day,hour,project'],
            'description' => ['required', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['exists:requirement_types,id'],
        ]);

        $tagsArray = null;
        if (! empty($validated['tags'])) {
            $tagsArray = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
        }

        $job = JobPost::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'company_name' => $validated['company_name'],
            'location' => $validated['location'],
            'job_type' => $validated['job_type'],
            'workplace_type' => $validated['workplace_type'],
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'salary_period' => $validated['salary_period'],
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'] ?? null,
            'qualifications' => $validated['qualifications'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'tags' => $tagsArray,
            'status' => 'active',
        ]);

        // Attach requirements
        if (! empty($validated['requirements'])) {
            $syncData = [];
            foreach ($validated['requirements'] as $reqId) {
                $syncData[$reqId] = ['is_mandatory' => true];
            }
            $job->requirements()->sync($syncData);
        }

        return redirect()->route('employer.dashboard')
            ->with('success', "Job posting '{$job->title}' created successfully with required documents attached!");
    }

    /**
     * Show form to edit job post.
     */
    public function editJob(JobPost $job): View
    {
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        $requirementTypes = RequirementType::all();
        $selectedRequirements = $job->requirements->pluck('id')->all();

        return view('employer.jobs.edit', compact('job', 'categories', 'requirementTypes', 'selectedRequirements'));
    }

    /**
     * Update existing job post.
     */
    public function updateJob(Request $request, JobPost $job): RedirectResponse
    {
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'job_type' => ['required', 'string', 'in:Full-time,Part-time,Contract,Freelance,Internship'],
            'workplace_type' => ['required', 'string', 'in:On-site,Remote,Hybrid'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'salary_period' => ['required', 'string', 'in:month,day,hour,project'],
            'description' => ['required', 'string'],
            'responsibilities' => ['nullable', 'string'],
            'qualifications' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'status' => ['required', 'in:active,closed'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['exists:requirement_types,id'],
        ]);

        $tagsArray = null;
        if (! empty($validated['tags'])) {
            $tagsArray = array_values(array_filter(array_map('trim', explode(',', $validated['tags']))));
        }

        $job->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'company_name' => $validated['company_name'],
            'location' => $validated['location'],
            'job_type' => $validated['job_type'],
            'workplace_type' => $validated['workplace_type'],
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'salary_period' => $validated['salary_period'],
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'] ?? null,
            'qualifications' => $validated['qualifications'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'tags' => $tagsArray,
            'status' => $validated['status'],
        ]);

        $syncData = [];
        if (! empty($validated['requirements'])) {
            foreach ($validated['requirements'] as $reqId) {
                $syncData[$reqId] = ['is_mandatory' => true];
            }
        }
        $job->requirements()->sync($syncData);

        return redirect()->route('employer.dashboard')
            ->with('success', "Job posting '{$job->title}' updated successfully!");
    }

    /**
     * Toggle job post status between active and closed.
     */
    public function toggleStatus(JobPost $job): RedirectResponse
    {
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $job->status = $job->status === 'active' ? 'closed' : 'active';
        $job->save();

        $statusLabel = $job->status === 'active' ? 'activated' : 'closed';

        return back()->with('info', "Job post has been {$statusLabel}.");
    }

    /**
     * Delete a job post.
     */
    public function deleteJob(JobPost $job): RedirectResponse
    {
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $title = $job->title;
        $job->delete();

        return redirect()->route('employer.dashboard')
            ->with('success', "Job '{$title}' has been deleted.");
    }

    /**
     * View and filter applicants across employer's jobs.
     */
    public function applicants(Request $request): View
    {
        $user = Auth::user();

        $employerJobs = JobPost::where('user_id', $user->id)->get();
        $jobPostIds = $employerJobs->pluck('id');

        $query = JobApplication::whereIn('job_post_id', $jobPostIds)
            ->with([
                'applicant.documents.requirementType',
                'jobPost.requirements',
            ])
            ->latest();

        if ($jobId = $request->input('job_id')) {
            $query->where('job_post_id', $jobId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $applications = $query->paginate(12)->withQueryString();

        return view('employer.applicants.index', compact('applications', 'employerJobs'));
    }

    /**
     * Update application status (Under Review, Hired, Rejected).
     */
    public function updateApplicationStatus(Request $request, JobApplication $application): RedirectResponse
    {
        if ($application->jobPost->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,under_review,hired,rejected'],
            'employer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => $validated['status'],
            'employer_notes' => $validated['employer_notes'] ?? $application->employer_notes,
            'reviewed_at' => now(),
        ]);

        $statusMsg = match ($validated['status']) {
            'hired' => "🎉 {$application->applicant->name} has been marked as HIRED!",
            'rejected' => "{$application->applicant->name} marked as Not Selected.",
            'under_review' => "{$application->applicant->name} is now Under Review.",
            default => "Status updated to {$validated['status']}.",
        };

        return back()->with('success', $statusMsg);
    }
}
