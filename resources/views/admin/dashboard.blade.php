@extends('layouts.app', ['title' => 'Admin Dashboard - FindJobPh'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <!-- Admin Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                    🛡️ Administrator Oversight
                </span>
                <span class="text-xs text-slate-500">{{ auth()->user()->email }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Platform Admin Overview</h1>
            <p class="text-xs text-slate-500 mt-0.5">Global metrics, user moderation, and application monitoring</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users') }}" class="px-4 py-2 text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition shadow-xs">
                Manage All Users
            </a>
            <a href="{{ route('admin.jobs') }}" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                Manage Jobs
            </a>
        </div>
    </div>

    <!-- Platform Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Total Platform Users</div>
            <div class="text-3xl font-black text-slate-900 mt-1">{{ $totalUsers }}</div>
            <div class="text-[11px] text-slate-500 mt-1">
                {{ $employersCount }} Employers &bull; {{ $applicantsCount }} Applicants
            </div>
            @if($suspendedUsersCount > 0)
                <div class="text-[10px] font-bold text-rose-600 mt-0.5">{{ $suspendedUsersCount }} suspended</div>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Total Job Posts</div>
            <div class="text-3xl font-black text-blue-600 mt-1">{{ $totalJobs }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">
                {{ $activeJobsCount }} active &bull; {{ $closedJobsCount }} closed
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Submitted Applications</div>
            <div class="text-3xl font-black text-indigo-600 mt-1">{{ $totalApplications }}</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $pendingCount }} under initial review</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-500 font-semibold">Matched & Hired</div>
            <div class="text-3xl font-black text-emerald-600 mt-1">{{ $hiredCount }}</div>
            <div class="text-[11px] text-emerald-700 mt-1 font-medium">Successful recruitments!</div>
        </div>
    </div>

    <!-- Recent Users & Recent Jobs 2-Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Users -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900">Recent Platform Users</h2>
                <a href="{{ route('admin.users') }}" class="text-xs font-bold text-purple-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentUsers as $ru)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl font-bold flex items-center justify-center shrink-0 {{ $ru->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($ru->role === 'employer' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ strtoupper(substr($ru->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 truncate">{{ $ru->name }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ $ru->email }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ru->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($ru->role === 'employer' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ ucfirst($ru->role) }}
                            </span>
                            @if(!$ru->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Suspended</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Job Posts -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-900">Recent Job Posts</h2>
                <a href="{{ route('admin.jobs') }}" class="text-xs font-bold text-purple-600 hover:underline">
                    View All &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentJobs as $rj)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('jobs.show', $rj) }}" class="font-bold text-slate-900 hover:text-blue-600 truncate block">
                                {{ $rj->title }}
                            </a>
                            <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                {{ $rj->company_name }} &bull; {{ $rj->category->name }}
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $rj->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $rj->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ ucfirst($rj->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
