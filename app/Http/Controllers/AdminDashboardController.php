<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Admin platform analytics overview.
     */
    public function dashboard(): View
    {
        $totalUsers = User::count();
        $employersCount = User::where('role', 'employer')->count();
        $applicantsCount = User::where('role', 'applicant')->count();
        $suspendedUsersCount = User::where('is_active', false)->count();

        $totalJobs = JobPost::count();
        $activeJobsCount = JobPost::where('status', 'active')->count();
        $closedJobsCount = JobPost::where('status', 'closed')->count();

        $totalApplications = JobApplication::count();
        $hiredCount = JobApplication::where('status', 'hired')->count();
        $pendingCount = JobApplication::where('status', 'pending')->count();

        $recentUsers = User::latest()->take(6)->get();
        $recentJobs = JobPost::with(['employer', 'category'])->latest()->take(5)->get();
        $recentApplications = JobApplication::with(['applicant', 'jobPost.employer'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'employersCount',
            'applicantsCount',
            'suspendedUsersCount',
            'totalJobs',
            'activeJobsCount',
            'closedJobsCount',
            'totalApplications',
            'hiredCount',
            'pendingCount',
            'recentUsers',
            'recentJobs',
            'recentApplications'
        ));
    }

    /**
     * Manage all users.
     */
    public function users(Request $request): View
    {
        $query = User::withCount(['jobPosts', 'applications'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'suspended') {
                $query->where('is_active', false);
            }
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Activate or deactivate a user account.
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own administrator account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $action = $user->is_active ? 'activated' : 'suspended';

        return back()->with('info', "User {$user->name} has been {$action}.");
    }

    /**
     * Delete user account.
     */
    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own administrator account.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "User account for {$name} has been permanently deleted.");
    }

    /**
     * Manage all job posts.
     */
    public function jobs(Request $request): View
    {
        $query = JobPost::with(['employer', 'category', 'requirements'])->withCount('applications')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $jobs = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.jobs', compact('jobs', 'categories'));
    }

    /**
     * Toggle job post status as admin.
     */
    public function toggleJobStatus(JobPost $job): RedirectResponse
    {
        $job->status = $job->status === 'active' ? 'closed' : 'active';
        $job->save();

        $statusLabel = $job->status === 'active' ? 'activated' : 'closed';

        return back()->with('info', "Job post '{$job->title}' has been {$statusLabel}.");
    }

    /**
     * Delete job post as admin.
     */
    public function deleteJob(JobPost $job): RedirectResponse
    {
        $title = $job->title;
        $job->delete();

        return back()->with('success', "Job posting '{$title}' has been deleted by administrator.");
    }

    /**
     * System-wide applications monitor.
     */
    public function applications(Request $request): View
    {
        $query = JobApplication::with(['applicant.documents.requirementType', 'jobPost.employer', 'jobPost.category'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('jobPost', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('admin.applications', compact('applications'));
    }
}
