@extends('layouts.app', ['title' => 'Review Candidates & Requirements - FindJobPh'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ statusModalOpen: false, selectedAppId: '', selectedAppName: '', currentStatus: '', modalAction: '' }">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('employer.dashboard') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                    &larr; Employer Dashboard
                </a>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Review Applicants & Requirements</h1>
            <p class="text-xs text-slate-500 mt-0.5">Inspect verified credentials and mark candidates as Hired or Not Selected</p>
        </div>

        <span class="px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-800 text-xs font-bold border border-indigo-200">
            Total Candidates: {{ $applications->total() }}
        </span>
    </div>

    <!-- Filters Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <form action="{{ route('employer.applicants') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <select name="job_id" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">
                <option value="">All Job Posts</option>
                @foreach($employerJobs as $job)
                    <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                        {{ $job->title }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">
                <option value="">All Application Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired 🎉</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Not Selected</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition">
                Filter
            </button>

            @if(request()->hasAny(['job_id', 'status']))
                <a href="{{ route('employer.applicants') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 font-semibold">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Applicants Cards / Stream -->
    <div class="space-y-4">
        @forelse($applications as $app)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 font-black text-base flex items-center justify-center shrink-0 border border-indigo-200">
                            {{ strtoupper(substr($app->applicant->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-base text-slate-900">{{ $app->applicant->name }}</h3>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $app->statusBadgeClasses() }}">
                                    @if($app->status === 'hired') <span>🎉</span> @endif
                                    {{ $app->statusLabel() }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-3 flex-wrap">
                                <span>Applied for: <strong class="text-slate-800">{{ $app->jobPost->title }}</strong></span>
                                <span>•</span>
                                <span>{{ $app->applicant->email }}</span>
                                @if($app->applicant->phone)
                                    <span>•</span>
                                    <span>📞 {{ $app->applicant->phone }}</span>
                                @endif
                                <span>•</span>
                                <span>{{ $app->created_at->format('M d, Y (h:i A)') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 shrink-0 w-full md:w-auto">
                        @if($app->status !== 'hired')
                            <form action="{{ route('employer.applications.status', $app) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="hired">
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition cursor-pointer">
                                    🎉 Mark as Hired
                                </button>
                            </form>
                        @endif

                        @if($app->status !== 'under_review')
                            <form action="{{ route('employer.applications.status', $app) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="under_review">
                                <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                    Under Review
                                </button>
                            </form>
                        @endif

                        @if($app->status !== 'rejected')
                            <form action="{{ route('employer.applications.status', $app) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                    Not Selected
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($app->cover_letter)
                    <div class="mt-3.5 p-3.5 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-700">
                        <strong class="text-slate-900 block mb-1">Applicant Introduction / Note:</strong>
                        {{ $app->cover_letter }}
                    </div>
                @endif

                <!-- SUBMITTED REQUIREMENT CREDENTIALS -->
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Submitted Requirement Credentials:
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                        @foreach($app->jobPost->requirements as $req)
                            @php
                                $applicantDoc = $app->applicant->documents->firstWhere('requirement_type_id', $req->id);
                            @endphp
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 truncate">{{ $req->name }}</div>
                                    @if($applicantDoc)
                                        <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                            📄 {{ $applicantDoc->file_name }} ({{ $applicantDoc->formattedSize() }})
                                        </div>
                                    @else
                                        <div class="text-[11px] text-rose-500">Document unavailable</div>
                                    @endif
                                </div>

                                @if($applicantDoc)
                                    <a href="{{ $applicantDoc->getUrl() }}" target="_blank" 
                                        class="shrink-0 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-blue-600 hover:text-blue-800 font-bold text-[11px] hover:bg-blue-50 transition">
                                        Inspect File
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($app->employer_notes)
                    <div class="mt-3 text-xs text-slate-500 italic">
                        Internal Note: "{{ $app->employer_notes }}"
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-500">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-900 text-base">No applicants matched</h3>
                <p class="text-xs text-slate-500 mt-1">Candidates who fulfill your job requirements will appear here.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $applications->links() }}
    </div>

</div>
@endsection
