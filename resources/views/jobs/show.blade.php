@extends('layouts.app', ['title' => $job->title . ' - FindJobPh'])

@section('content')
@php
    $user = auth()->user();
    $isApplicant = $user && $user->isApplicant();
@endphp

<div class="max-w-5xl mx-auto space-y-6" x-data="{ uploadModalOpen: false, selectedReqId: '', selectedReqName: '', applyModalOpen: false }">
    
    <!-- Top Back Breadcrumb -->
    <div class="flex items-center justify-between text-xs font-medium text-slate-500">
        <a href="{{ route('jobs.feed') }}" class="inline-flex items-center gap-1 hover:text-blue-600 font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Jobs Feed
        </a>
        <div class="flex items-center gap-2">
            <span>Posted {{ $job->created_at->diffForHumans() }}</span>
            <span>•</span>
            <span>{{ $job->views_count }} views</span>
        </div>
    </div>

    <!-- Main Job Detail Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
        
        <!-- Header: Company & Job Title -->
        <div class="flex flex-col sm:flex-row items-start justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                    {{ strtoupper(substr($job->company_name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-bold text-slate-700">{{ $job->company_name }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $job->category->name }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $job->title }}</h1>
                    
                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-2 flex-wrap">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $job->location }}
                        </span>
                        <span>•</span>
                        <span class="font-medium text-slate-700">{{ $job->job_type }}</span>
                        <span>•</span>
                        <span class="font-medium text-blue-600">{{ $job->workplace_type }}</span>
                    </div>
                </div>
            </div>

            <!-- Salary Badge -->
            <div class="sm:text-right shrink-0 bg-slate-50 sm:bg-transparent p-4 sm:p-0 rounded-2xl w-full sm:w-auto">
                <div class="text-xs text-slate-500 font-medium">Offered Compensation</div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-0.5">
                    {{ $job->formattedSalary() }}
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- CORE REQUIREMENT-GATED APPLICATION SECTION -->
        <!-- ========================================== -->
        <div id="apply-section" class="my-8 rounded-3xl p-6 sm:p-7 border transition-all duration-300 {{ $hasApplied ? 'bg-slate-50 border-slate-200' : ($isRequirementsComplete ? 'bg-emerald-50/70 border-emerald-300 ring-2 ring-emerald-100' : 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-100') }}">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-2">
                        @if($hasApplied)
                            <span class="p-1 rounded-full bg-slate-200 text-slate-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </span>
                            <h2 class="text-lg font-bold text-slate-900">Application Submitted</h2>
                        @elseif($isRequirementsComplete)
                            <span class="p-1 rounded-full bg-emerald-600 text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                            <h2 class="text-lg font-bold text-emerald-950">All Requirements Ready to Apply!</h2>
                        @else
                            <span class="p-1 rounded-full bg-amber-500 text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </span>
                            <h2 class="text-lg font-bold text-amber-950">Requirement-Gated Application Flow</h2>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 mt-1">
                        @if($hasApplied)
                            You applied on {{ $existingApplication->created_at->format('M d, Y') }}. Current status: <strong class="text-slate-800 uppercase">{{ $existingApplication->statusLabel() }}</strong>.
                        @elseif($isRequirementsComplete)
                            Your credentials in your Requirements Vault satisfy all {{ $job->requirements->count() }} employer criteria for this position.
                        @elseif($isApplicant)
                            You are missing <strong class="text-amber-800">{{ $missingRequirements->count() }} required document(s)</strong> for this job post. The Apply button will activate as soon as you upload them.
                        @else
                            This job requires document verification before applying. Please sign in as an applicant to check your credentials.
                        @endif
                    </p>
                </div>

                <!-- Primary Action / Apply Button -->
                <div class="shrink-0 w-full md:w-auto">
                    @if($hasApplied)
                        <div class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-xs font-bold bg-white text-slate-800 border border-slate-300 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Status: {{ $existingApplication->statusLabel() }}
                        </div>
                    @elseif($isApplicant)
                        @if($isRequirementsComplete)
                            <button type="button" @click="applyModalOpen = true"
                                class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl text-sm font-extrabold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-lg shadow-emerald-600/30 hover:scale-[1.02] active:scale-[0.98] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Submit Application Now
                            </button>
                        @else
                            <!-- DISABLED BUTTON with explanatory tooltip -->
                            <div class="relative group">
                                <button type="button" disabled
                                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-400 bg-slate-200 cursor-not-allowed border border-slate-300">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Apply Disabled (Missing Docs)
                                </button>
                                <div class="hidden group-hover:block absolute right-0 bottom-full mb-2 w-64 p-2 bg-slate-900 text-white text-[11px] rounded-lg shadow-lg z-20">
                                    Upload all missing documents in the checklist below to enable the Apply button.
                                </div>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition">
                            Sign In to Check Requirements & Apply
                        </a>
                    @endif
                </div>
            </div>

            <!-- Mandatory Requirements Breakdown Table / Checklist -->
            <div class="mt-4 pt-4 border-t border-slate-200/80">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                    Employer Document Requirements Checklist:
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @forelse($job->requirements as $req)
                        @php
                            $applicantHasDoc = $isApplicant && $user->hasDocument($req->id);
                            $doc = $isApplicant ? $user->getDocument($req->id) : null;
                        @endphp
                        
                        <div class="p-3.5 rounded-2xl bg-white border flex flex-col justify-between transition {{ $isApplicant ? ($applicantHasDoc ? 'border-emerald-300 bg-emerald-50/30' : 'border-rose-300 bg-rose-50/20') : 'border-slate-200' }}">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <div class="font-bold text-xs text-slate-900 leading-snug">
                                        {{ $req->name }}
                                    </div>
                                    @if($isApplicant)
                                        @if($applicantHasDoc)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full shrink-0">
                                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                Ready
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full shrink-0">
                                                Missing
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">
                                            Mandatory
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $req->description }}</p>
                            </div>

                            <!-- Document State / Upload Trigger Button -->
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                @if($isApplicant)
                                    @if($applicantHasDoc)
                                        <span class="text-slate-500 truncate max-w-[120px]" title="{{ $doc->file_name }}">
                                            📄 {{ $doc->file_name }}
                                        </span>
                                        <button type="button" @click="uploadModalOpen = true; selectedReqId = '{{ $req->id }}'; selectedReqName = '{{ addslashes($req->name) }}'"
                                            class="text-blue-600 hover:text-blue-800 font-semibold underline">
                                            Replace
                                        </button>
                                    @else
                                        <span class="text-rose-600 font-medium">Not in your vault</span>
                                        <button type="button" @click="uploadModalOpen = true; selectedReqId = '{{ $req->id }}'; selectedReqName = '{{ addslashes($req->name) }}'"
                                            class="inline-flex items-center gap-1 font-bold text-white bg-blue-600 hover:bg-blue-700 px-2.5 py-1 rounded-lg shadow-xs transition">
                                            + Upload Now
                                        </button>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Sign in to check</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-xs text-slate-500 italic p-3 bg-white rounded-xl border border-slate-200">
                            No document prerequisites were specified for this opening.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Job Overview & Description -->
        <div class="space-y-6 text-slate-700 text-sm leading-relaxed">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-2">About This Role</h3>
                <p class="whitespace-pre-line text-slate-600">{{ $job->description }}</p>
            </div>

            @if($job->responsibilities)
                <div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Key Responsibilities</h3>
                    <div class="whitespace-pre-line text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                        {{ $job->responsibilities }}
                    </div>
                </div>
            @endif

            @if($job->qualifications)
                <div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Candidate Qualifications</h3>
                    <div class="whitespace-pre-line text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                        {{ $job->qualifications }}
                    </div>
                </div>
            @endif

            @if($job->benefits)
                <div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Perks & Compensation Benefits</h3>
                    <div class="whitespace-pre-line text-slate-600 bg-emerald-50/50 p-4 rounded-2xl border border-emerald-200/70">
                        {{ $job->benefits }}
                    </div>
                </div>
            @endif

            @if(!empty($job->tags))
                <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-2">
                    @foreach($job->tags as $tag)
                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-lg">
                            #{{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Related Jobs in same category -->
    @if($relatedJobs->isNotEmpty())
        <div class="mt-10">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Other Openings in {{ $job->category->name }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($relatedJobs as $rel)
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 hover:shadow-md transition">
                        <div class="font-bold text-sm text-slate-900 hover:text-blue-600 line-clamp-1">
                            <a href="{{ route('jobs.show', $rel) }}">{{ $rel->title }}</a>
                        </div>
                        <div class="text-xs text-slate-500 mt-1 truncate">{{ $rel->company_name }} • {{ $rel->location }}</div>
                        <div class="text-xs font-bold text-emerald-600 mt-2">{{ $rel->formattedSalary() }}</div>
                        <a href="{{ route('jobs.show', $rel) }}" class="mt-3 block text-center py-1.5 text-xs font-bold text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100">
                            View Job
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ============================================== -->
    <!-- MODAL 1: Inline Requirement File Upload Modal -->
    <!-- ============================================== -->
    <!-- ============================================== -->
    <!-- MODAL 1: Inline Requirement File Upload Modal -->
    <!-- ============================================== -->
    <div x-show="uploadModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop: dark overlay without backdrop-filter blur bleed -->
        <div x-show="uploadModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/70 transition-opacity" 
             @click="uploadModalOpen = false"
             aria-hidden="true"></div>

        <!-- Centering Wrapper -->
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4 text-center">
            <!-- Modal Card (Crystal-clear, crisp, solid white, elevated, relative z-20) -->
            <div x-show="uploadModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative z-20 w-full max-w-lg p-6 overflow-hidden text-left bg-white shadow-2xl rounded-3xl border border-slate-100 transition-all">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-xl bg-blue-100 text-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Upload Requirement Document</h3>
                            <p class="text-xs text-slate-500" x-text="selectedReqName"></p>
                        </div>
                    </div>
                    <button type="button" @click="uploadModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>

                <form action="{{ route('jobs.upload-requirement', $job) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="requirement_type_id" :value="selectedReqId">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Choose File (PDF, JPG, PNG)</label>
                        <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png,.webp"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl p-2 cursor-pointer bg-slate-50">
                        <p class="text-[11px] text-slate-500 mt-1">Maximum file size: 10MB. Files are securely saved to your Requirements Vault.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Document Notes / References (Optional)</label>
                        <input type="text" name="notes" placeholder="e.g. PhilSys ID Number / Expiry Year"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-blue-600">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition">
                            Upload & Satisfy Requirement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- MODAL 2: Application Submission Confirmation  -->
    <!-- ============================================== -->
    <div x-show="applyModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop: dark overlay without backdrop-filter blur bleed -->
        <div x-show="applyModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/70 transition-opacity" 
             @click="applyModalOpen = false"
             aria-hidden="true"></div>

        <!-- Centering Wrapper -->
        <div class="fixed inset-0 z-10 overflow-y-auto flex min-h-full items-center justify-center p-4 text-center">
            <!-- Modal Card (Crystal-clear, crisp, solid white, elevated, relative z-20) -->
            <div x-show="applyModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative z-20 w-full max-w-lg p-6 overflow-hidden text-left bg-white shadow-2xl rounded-3xl border border-slate-100 transition-all">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Confirm Job Application</h3>
                            <p class="text-xs text-slate-500">{{ $job->title }} &bull; {{ $job->company_name }}</p>
                        </div>
                    </div>
                    <button type="button" @click="applyModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>

                <form action="{{ route('jobs.apply', $job) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    
                    <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900">
                        <span class="font-bold block mb-1">✓ Verified Credentials Ready:</span>
                        All required documents ({{ $job->requirements->pluck('name')->join(', ') }}) from your Requirements Vault will be shared with the employer for verification.
                    </div>

                    <div>
                        <label for="cover_letter" class="block text-xs font-bold text-slate-700 mb-1">
                            Cover Message / Introduction for the Employer (Optional)
                        </label>
                        <textarea name="cover_letter" id="cover_letter" rows="4" 
                            placeholder="Briefly state your availability, experience, or excitement for this position..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-blue-600"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="applyModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Back
                        </button>
                        <button type="submit" class="px-6 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/25 rounded-xl transition">
                            Confirm & Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
