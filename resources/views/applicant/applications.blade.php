@extends('layouts.app', ['title' => 'My Applications - FindJobPh'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">My Job Applications</h1>
            <p class="text-xs text-slate-500 mt-0.5">Track the status of all positions you have applied for</p>
        </div>
        <a href="{{ route('jobs.feed') }}" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-xs">
            + Explore More Jobs
        </a>
    </div>

    <!-- Applications List -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        @forelse($applications as $app)
            <div class="p-5 sm:p-6 border-b border-slate-100 last:border-b-0 hover:bg-slate-50/50 transition">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0 border border-blue-200">
                            {{ strtoupper(substr($app->jobPost->company_name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-base text-slate-900 hover:text-blue-600">
                                    <a href="{{ route('jobs.show', $app->jobPost) }}">{{ $app->jobPost->title }}</a>
                                </h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                    {{ $app->jobPost->job_type }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                <strong class="text-slate-700">{{ $app->jobPost->company_name }}</strong>
                                <span>•</span>
                                <span>{{ $app->jobPost->location }}</span>
                                <span>•</span>
                                <span class="text-emerald-600 font-bold">{{ $app->jobPost->formattedSalary() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Pill -->
                    <div class="sm:text-right shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $app->statusBadgeClasses() }}">
                            @if($app->status === 'hired')
                                <span>🎉</span>
                            @endif
                            {{ $app->statusLabel() }}
                        </span>
                        <div class="text-[11px] text-slate-400 mt-1">
                            Applied {{ $app->created_at->format('M d, Y') }} ({{ $app->created_at->diffForHumans() }})
                        </div>
                    </div>
                </div>

                @if($app->cover_letter)
                    <div class="mt-3.5 pt-3 border-t border-slate-100 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl">
                        <span class="font-bold text-slate-700 block mb-0.5">Your Cover Note:</span>
                        {{ $app->cover_letter }}
                    </div>
                @endif

                @if($app->employer_notes)
                    <div class="mt-2 text-xs p-3 rounded-xl border {{ $app->status === 'hired' ? 'bg-emerald-50 text-emerald-900 border-emerald-200' : 'bg-blue-50 text-blue-900 border-blue-200' }}">
                        <span class="font-bold block mb-0.5">Note from Employer:</span>
                        {{ $app->employer_notes }}
                    </div>
                @endif
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-base">No Applications Yet</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    You haven't submitted any job applications yet. Complete your Requirements Vault and start applying!
                </p>
                <div class="mt-4">
                    <a href="{{ route('jobs.feed') }}" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">
                        Browse Jobs Feed
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div>
        {{ $applications->links() }}
    </div>

</div>
@endsection
