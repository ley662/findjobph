@extends('layouts.app', ['title' => 'Employer Dashboard - FindJobPh'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <!-- Employer Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">
                    Employer Portal
                </span>
                <span class="text-xs text-slate-500">{{ auth()->user()->company_name ?? auth()->user()->name }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Employer Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage your job postings, requirements, and candidate applications</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('employer.applicants') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                View All Applicants ({{ $totalApplicants }})
            </a>
            <a href="{{ route('employer.jobs.create') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm shadow-indigo-600/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Post New Job
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Total Job Posts</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalJobs }}</div>
            <div class="text-[11px] text-emerald-600 mt-1 font-medium">{{ $activeJobs }} active postings</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Total Applicants</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $totalApplicants }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Across all your jobs</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Pending Review</div>
            <div class="text-2xl font-black text-amber-500 mt-1">{{ $pendingCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">Awaiting evaluation</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Hired Candidates</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $hiredCount }}</div>
            <div class="text-[11px] text-emerald-700 mt-1 font-medium">Successfully matched!</div>
        </div>
    </div>

    <!-- Job Posts Management Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Your Posted Jobs</h2>
                <p class="text-xs text-slate-500">Manage attached requirements and view candidates</p>
            </div>
            <a href="{{ route('employer.jobs.create') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                + Create Another Job Post
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Job Title & Category</th>
                        <th class="px-6 py-3">Attached Requirements</th>
                        <th class="px-6 py-3">Candidates</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($jobPosts as $post)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('jobs.show', $post) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block">
                                    {{ $post->title }}
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $post->category->name }} &bull; {{ $post->location }} &bull; {{ $post->job_type }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach($post->requirements as $req)
                                        <span class="inline-flex text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-medium">
                                            {{ $req->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('employer.applicants', ['job_id' => $post->id]) }}" class="inline-flex items-center gap-1 font-bold text-blue-600 hover:underline">
                                    {{ $post->applications_count }} applicants
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('employer.jobs.toggle', $post) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold cursor-pointer transition {{ $post->status === 'active' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}" title="Click to toggle active/closed">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $post->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ ucfirst($post->status) }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('employer.jobs.edit', $post) }}" class="text-blue-600 hover:text-blue-800 font-bold">Edit</a>
                                <form action="{{ route('employer.jobs.delete', $post) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this job post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                You haven't posted any jobs yet. Click <a href="{{ route('employer.jobs.create') }}" class="text-indigo-600 font-bold hover:underline">Post a New Job</a> to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Applicants Table -->
    @if($recentApplications->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recent Candidate Applications</h2>
                    <p class="text-xs text-slate-500">Latest applicants who have satisfied your requirement checklist</p>
                </div>
                <a href="{{ route('employer.applicants') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                    View All Candidates &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentApplications as $recent)
                    <div class="py-3.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($recent->applicant->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">{{ $recent->applicant->name }}</div>
                                <div class="text-[11px] text-slate-500">Applied for <strong class="text-slate-700">{{ $recent->jobPost->title }}</strong> &bull; {{ $recent->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $recent->statusBadgeClasses() }}">
                                {{ $recent->statusLabel() }}
                            </span>
                            <a href="{{ route('employer.applicants', ['job_id' => $recent->job_post_id]) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-bold hover:bg-indigo-100 transition">
                                Review Credentials
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
