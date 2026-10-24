@extends('layouts.app', ['title' => 'Job Feed - FindJobPh'])

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- LEFT SIDEBAR: Filters & Applicant Profile Widget -->
    <aside class="lg:col-span-3 space-y-5">
        
        <!-- Applicant Profile & Readiness Widget (if logged in) -->
        @auth
            @if(auth()->user()->isApplicant())
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold flex items-center justify-center text-base shadow-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 text-sm truncate">{{ auth()->user()->name }}</h3>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Job Applicant
                            </span>
                        </div>
                    </div>

                    <!-- Requirements Vault Meter -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-slate-700">Requirements Vault</span>
                            <span class="font-extrabold text-blue-600">{{ $readinessPercent }}% Complete</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $readinessPercent }}%"></div>
                        </div>

                        @if($missingDefaults->isNotEmpty())
                            <p class="text-[11px] text-amber-700 mt-2 font-medium flex items-start gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Missing: {{ $missingDefaults->pluck('name')->join(', ') }}
                            </p>
                        @else
                            <p class="text-[11px] text-emerald-700 mt-2 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                Standard requirements complete!
                            </p>
                        @endif

                        <a href="{{ route('applicant.documents') }}" class="mt-3 block w-full text-center py-2 px-3 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50/70 hover:bg-blue-100 rounded-xl transition">
                            Manage Vault Documents &rarr;
                        </a>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Category Filters -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-slate-900 text-sm">Categories</h3>
                @if(request('category'))
                    <a href="{{ route('jobs.feed', request()->except('category')) }}" class="text-[11px] font-semibold text-blue-600 hover:underline">Clear</a>
                @endif
            </div>

            <ul class="space-y-1 text-xs">
                <li>
                    <a href="{{ route('jobs.feed', request()->except('category')) }}" 
                        class="flex items-center justify-between p-2 rounded-xl transition font-medium {{ !request('category') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>All Categories</span>
                        <span class="text-slate-400 font-semibold">{{ $categories->sum('job_posts_count') }}</span>
                    </a>
                </li>
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('jobs.feed', array_merge(request()->query(), ['category' => $category->slug])) }}" 
                            class="flex items-center justify-between p-2 rounded-xl transition font-medium {{ request('category') === $category->slug ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span class="truncate pr-2">{{ $category->name }}</span>
                            <span class="text-slate-400 font-semibold shrink-0">{{ $category->job_posts_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Workplace & Job Type Filter -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 space-y-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm mb-2.5">Workplace</h3>
                <div class="flex flex-wrap gap-1.5 text-xs">
                    @foreach(['On-site', 'Remote', 'Hybrid'] as $wp)
                        <a href="{{ route('jobs.feed', array_merge(request()->query(), ['workplace_type' => request('workplace_type') === $wp ? null : $wp])) }}"
                            class="px-2.5 py-1 rounded-lg border font-medium transition {{ request('workplace_type') === $wp ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                            {{ $wp }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm mb-2.5">Job Type</h3>
                <div class="flex flex-wrap gap-1.5 text-xs">
                    @foreach(['Full-time', 'Part-time', 'Contract', 'Freelance'] as $jt)
                        <a href="{{ route('jobs.feed', array_merge(request()->query(), ['job_type' => request('job_type') === $jt ? null : $jt])) }}"
                            class="px-2.5 py-1 rounded-lg border font-medium transition {{ request('job_type') === $jt ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                            {{ $jt }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if(request()->hasAny(['search', 'category', 'workplace_type', 'job_type', 'min_salary']))
                <div class="pt-3 border-t border-slate-100">
                    <a href="{{ route('jobs.feed') }}" class="block w-full text-center py-2 text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition">
                        Reset All Filters
                    </a>
                </div>
            @endif
        </div>
    </aside>

    <!-- CENTER FEED: Search Bar & Job Cards Feed -->
    <main class="lg:col-span-6 space-y-4">
        
        <!-- Post/Announcement Box (Facebook-style prompt) -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-4 sm:p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                    🇵🇭
                </div>
                <div class="flex-1">
                    <h2 class="text-sm font-bold text-slate-900">Find Your Next Job Opportunity</h2>
                    <p class="text-xs text-slate-500">Fast application process with requirement verification</p>
                </div>
                @auth
                    @if(auth()->user()->isEmployer())
                        <a href="{{ route('employer.jobs.create') }}" class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white transition">
                            + Post Job
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition">
                        Sign Up
                    </a>
                @endauth
            </div>

            <!-- Search Bar inside Feed -->
            <form action="{{ route('jobs.feed') }}" method="GET" class="mt-4 flex gap-2">
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('job_type')) <input type="hidden" name="job_type" value="{{ request('job_type') }}"> @endif
                @if(request('workplace_type')) <input type="hidden" name="workplace_type" value="{{ request('workplace_type') }}"> @endif

                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Keyword: Rider, Videographer, Developer, Manila..."
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-100 hover:bg-slate-50 focus:bg-white text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition outline-hidden">
                </div>
                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-black text-white font-bold text-xs sm:text-sm rounded-xl transition">
                    Search
                </button>
            </form>

            <!-- Active filter chips -->
            @if(request()->hasAny(['search', 'category', 'workplace_type', 'job_type']))
                <div class="mt-3 flex items-center gap-1.5 flex-wrap text-xs">
                    <span class="text-slate-500 font-semibold text-[11px]">Filtered by:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 text-[11px] font-medium border border-slate-200">
                            "{{ request('search') }}"
                            <a href="{{ route('jobs.feed', request()->except('search')) }}" class="hover:text-rose-600">&times;</a>
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[11px] font-medium border border-blue-200">
                            Category: {{ request('category') }}
                            <a href="{{ route('jobs.feed', request()->except('category')) }}" class="hover:text-rose-600">&times;</a>
                        </span>
                    @endif
                    @if(request('workplace_type'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[11px] font-medium border border-indigo-200">
                            {{ request('workplace_type') }}
                            <a href="{{ route('jobs.feed', request()->except('workplace_type')) }}" class="hover:text-rose-600">&times;</a>
                        </span>
                    @endif
                    @if(request('job_type'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-medium border border-emerald-200">
                            {{ request('job_type') }}
                            <a href="{{ route('jobs.feed', request()->except('job_type')) }}" class="hover:text-rose-600">&times;</a>
                        </span>
                    @endif
                    <a href="{{ route('jobs.feed') }}" class="text-[11px] font-bold text-rose-600 hover:underline ml-1">Clear all</a>
                </div>
            @endif
        </div>

        <!-- Job Cards Feed List -->
        @forelse($jobs as $job)
            <x-job-card :job="$job" />
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">No job openings found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Try adjusting your search terms, changing the category, or clearing the active filters.
                </p>
                <div class="mt-4">
                    <a href="{{ route('jobs.feed') }}" class="inline-flex items-center px-4 py-2 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition">
                        View All Openings
                    </a>
                </div>
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="pt-2">
            {{ $jobs->links() }}
        </div>
    </main>

    <!-- RIGHT SIDEBAR: Requirement Gating Info & Urgent Openings -->
    <aside class="hidden lg:block lg:col-span-3 space-y-5">
        
        <!-- Requirement Gating Explainer Widget -->
        <div class="bg-gradient-to-br from-blue-900 to-indigo-900 rounded-2xl p-5 text-white shadow-md">
            <div class="flex items-center gap-2 mb-2">
                <span class="p-1.5 rounded-lg bg-blue-500/30 text-amber-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </span>
                <h3 class="font-bold text-sm">How Gating Works</h3>
            </div>
            <p class="text-xs text-blue-100/90 leading-relaxed">
                FindJobPh verifies that applicants possess required documents (Valid ID, Driver's License, NBI Clearance, etc.) <strong>before</strong> applying.
            </p>
            <div class="mt-3.5 space-y-2 text-[11px] text-blue-200">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-emerald-500/30 text-emerald-300 font-bold flex items-center justify-center text-[10px]">1</span>
                    <span>Upload credentials to your Vault</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-emerald-500/30 text-emerald-300 font-bold flex items-center justify-center text-[10px]">2</span>
                    <span>Job checks requirements automatically</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-emerald-500/30 text-emerald-300 font-bold flex items-center justify-center text-[10px]">3</span>
                    <span>Apply button unlocks instantly!</span>
                </div>
            </div>

            @auth
                @if(auth()->user()->isApplicant())
                    <a href="{{ route('applicant.documents') }}" class="mt-4 block text-center py-2 px-3 text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-300 rounded-xl transition shadow-xs">
                        Upload to My Vault
                    </a>
                @endif
            @endauth
        </div>

        <!-- Urgent Hiring Openings -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5">
            <h3 class="font-bold text-slate-900 text-sm mb-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                Urgent Hiring
            </h3>
            <div class="space-y-3">
                @foreach($urgentJobs as $uj)
                    <a href="{{ route('jobs.show', $uj) }}" class="block p-2.5 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 group">
                        <div class="font-bold text-xs text-slate-900 group-hover:text-blue-600 line-clamp-1">
                            {{ $uj->title }}
                        </div>
                        <div class="text-[11px] text-slate-500 truncate mt-0.5">
                            {{ $uj->company_name }} • {{ $uj->location }}
                        </div>
                        <div class="text-[11px] font-bold text-emerald-600 mt-1">
                            {{ $uj->formattedSalary() }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Verified Philippine Employers -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 text-xs text-slate-600">
            <h3 class="font-bold text-slate-900 text-sm mb-2.5">Featured Employers</h3>
            <ul class="space-y-2">
                <li class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0">EX</span>
                    <span class="font-semibold text-slate-800">Express Logistics PH</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-purple-100 text-purple-700 font-bold text-[10px] flex items-center justify-center shrink-0">LM</span>
                    <span class="font-semibold text-slate-800">Lights & Motion Studios</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-indigo-100 text-indigo-700 font-bold text-[10px] flex items-center justify-center shrink-0">PC</span>
                    <span class="font-semibold text-slate-800">PinoyCloud Solutions</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-amber-100 text-amber-700 font-bold text-[10px] flex items-center justify-center shrink-0">MB</span>
                    <span class="font-semibold text-slate-800">Manila Bites Food Group</span>
                </li>
            </ul>
        </div>
    </aside>

</div>
@endsection
