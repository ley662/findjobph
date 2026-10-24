<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobPost;
use App\Models\RequirementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedController extends Controller
{
    /**
     * Display Facebook-style job feed with search and filters.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Eager load relations for the user if applicant
        if ($user && $user->isApplicant()) {
            $user->load('documents.requirementType', 'applications');
        }

        $query = JobPost::query()
            ->where('status', 'active')
            ->with(['category', 'employer', 'requirements', 'applications'])
            ->latest();

        // Search Keyword (Title, Company, Location, Description)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter Category
        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter Job Type
        if ($jobType = $request->input('job_type')) {
            $query->where('job_type', $jobType);
        }

        // Filter Workplace Type
        if ($workplace = $request->input('workplace_type')) {
            $query->where('workplace_type', $workplace);
        }

        // Filter Min Salary
        if ($minSalary = $request->input('min_salary')) {
            $query->where(function ($q) use ($minSalary) {
                $q->where('salary_max', '>=', (int) $minSalary)
                    ->orWhere('salary_min', '>=', (int) $minSalary);
            });
        }

        $jobs = $query->paginate(10)->withQueryString();

        $categories = Category::withCount(['jobPosts' => function ($q) {
            $q->where('status', 'active');
        }])->get();

        $allRequirementTypes = RequirementType::all();

        // Calculate readiness stats for applicant widget
        $readinessPercent = 0;
        $missingDefaults = collect();
        if ($user && $user->isApplicant()) {
            $defaultReqs = $allRequirementTypes->where('is_default', true);
            if ($defaultReqs->isNotEmpty()) {
                $userDocTypeIds = $user->documents->pluck('requirement_type_id')->all();
                $uploadedDefaultCount = $defaultReqs->filter(fn ($r) => in_array($r->id, $userDocTypeIds, true))->count();
                $readinessPercent = (int) round(($uploadedDefaultCount / $defaultReqs->count()) * 100);
                $missingDefaults = $defaultReqs->reject(fn ($r) => in_array($r->id, $userDocTypeIds, true));
            }
        }

        // Urgent/Featured jobs for sidebar
        $urgentJobs = JobPost::where('status', 'active')
            ->where(function ($q) {
                $q->where('tags', 'like', '%urgent%')
                    ->orWhere('job_type', 'Full-time');
            })
            ->latest()
            ->take(4)
            ->get();

        return view('jobs.index', compact(
            'jobs',
            'categories',
            'allRequirementTypes',
            'readinessPercent',
            'missingDefaults',
            'urgentJobs'
        ));
    }
}
