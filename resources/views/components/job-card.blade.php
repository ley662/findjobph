@props(['job'])

@php
    $user = auth()->user();
    $isApplicant = $user && $user->isApplicant();
    $hasApplied = $isApplicant && $job->hasApplicantApplied($user);
    $missingReqs = $isApplicant ? $job->missingRequirementsFor($user) : collect();
    $isReady = $isApplicant && $missingReqs->isEmpty();
@endphp

<article class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col">
    <!-- Card Header (Facebook-style post author) -->
    <div class="p-5 pb-3">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <!-- Company Avatar -->
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-base flex items-center justify-center shrink-0 shadow-xs">
                    {{ strtoupper(substr($job->company_name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <h4 class="font-bold text-slate-900 text-sm hover:text-blue-600 transition">{{ $job->company_name }}</h4>
                        <span class="inline-flex text-blue-500" title="Verified Employer">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $job->location }}
                        </span>
                        <span>•</span>
                        <span>{{ $job->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Job Type & Workplace Pills -->
            <div class="flex items-center gap-1.5 shrink-0">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                    {{ $job->job_type }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200/60">
                    {{ $job->workplace_type }}
                </span>
            </div>
        </div>

        <!-- Job Title -->
        <h3 class="mt-3 text-lg font-bold text-slate-900 group">
            <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600 transition flex items-center justify-between">
                {{ $job->title }}
            </a>
        </h3>

        <!-- Salary Badge -->
        <div class="mt-2.5 flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                <span class="text-emerald-600 font-extrabold text-sm">₱</span>
                {{ $job->formattedSalary() }}
            </span>
            <span class="text-xs font-medium text-slate-500">
                Category: <strong class="text-slate-700">{{ $job->category->name }}</strong>
            </span>
        </div>

        <!-- Description Excerpt -->
        <p class="mt-3 text-sm text-slate-600 line-clamp-2 leading-relaxed">
            {{ $job->description }}
        </p>

        <!-- Tags -->
        @if(!empty($job->tags))
            <div class="mt-3 flex flex-wrap gap-1.5">
                @foreach($job->tags as $tag)
                    <span class="text-xs text-blue-600 hover:text-blue-700 font-medium bg-blue-50/70 px-2 py-0.5 rounded-md">
                        #{{ $tag }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Requirements Preview & Gating Status Bar -->
    <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-100 mt-auto">
        <div class="text-xs font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Required Documents ({{ $job->requirements->count() }}):
            </span>

            @if($isApplicant)
                @if($hasApplied)
                    <span class="text-emerald-700 font-bold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Applied
                    </span>
                @elseif($isReady)
                    <span class="text-emerald-700 font-bold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Requirements Met
                    </span>
                @else
                    <span class="text-amber-700 font-semibold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ $missingReqs->count() }} missing
                    </span>
                @endif
            @endif
        </div>

        <div class="flex flex-wrap gap-1">
            @forelse($job->requirements as $req)
                @php
                    $applicantHasThis = $isApplicant && $user->hasDocument($req->id);
                @endphp
                <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-md font-medium {{ $isApplicant ? ($applicantHasThis ? 'bg-emerald-100/80 text-emerald-800 border border-emerald-300' : 'bg-rose-100/80 text-rose-800 border border-rose-300') : 'bg-slate-200/80 text-slate-700' }}">
                    @if($isApplicant)
                        @if($applicantHasThis)
                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        @else
                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        @endif
                    @endif
                    {{ $req->name }}
                </span>
            @empty
                <span class="text-xs text-slate-500 italic">No special documents specified.</span>
            @endforelse
        </div>
    </div>

    <!-- Action Button Bar -->
    <div class="px-5 py-3 border-t border-slate-200 bg-white flex items-center justify-between gap-3">
        <a href="{{ route('jobs.show', $job) }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 flex items-center gap-1">
            Read Full Job Post
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>

        @if($hasApplied)
            <span class="inline-flex items-center gap-1 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                Application Submitted
            </span>
        @elseif($isApplicant && $isReady)
            <a href="{{ route('jobs.show', $job) }}#apply-section" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-600/20 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Apply Now
            </a>
        @elseif($isApplicant && !$isReady)
            <a href="{{ route('jobs.show', $job) }}#apply-section" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 transition">
                Complete {{ $missingReqs->count() }} Requirements
            </a>
        @else
            <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm shadow-blue-600/20 transition">
                View & Apply
            </a>
        @endif
    </div>
</article>
