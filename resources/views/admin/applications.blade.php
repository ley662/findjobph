@extends('layouts.app', ['title' => 'System Applications Log - Admin - FindJobPh'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-purple-600 hover:underline">
                &larr; Admin Dashboard
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Platform Applications Log</h1>
            <p class="text-xs text-slate-500 mt-0.5">Global monitor for all applications submitted across employers</p>
        </div>

        <span class="px-3.5 py-1.5 rounded-xl bg-purple-50 text-purple-800 text-xs font-bold border border-purple-200">
            Total Applications: {{ $applications->total() }}
        </span>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <form action="{{ route('admin.applications') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search candidate, job, or employer..."
                class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired 🎉</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Not Selected</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.applications') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 font-semibold">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Applicant</th>
                        <th class="px-6 py-3">Applied Job & Employer</th>
                        <th class="px-6 py-3">Submitted Credentials</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Applied Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $app->applicant->name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $app->applicant->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('jobs.show', $app->jobPost) }}" target="_blank" class="font-bold text-slate-900 hover:text-blue-600">
                                    {{ $app->jobPost->title }}
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $app->jobPost->company_name }} &bull; {{ $app->jobPost->category->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800">
                                    {{ $app->jobPost->requirements->count() }} required docs
                                </span>
                                <div class="text-[10px] text-slate-500 truncate max-w-xs mt-0.5">
                                    {{ $app->jobPost->requirements->pluck('name')->join(', ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $app->statusBadgeClasses() }}">
                                    @if($app->status === 'hired') <span>🎉</span> @endif
                                    {{ $app->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500">
                                <div>{{ $app->created_at->format('M d, Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $app->created_at->diffForHumans() }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                No application records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $applications->links() }}
    </div>

</div>
@endsection
