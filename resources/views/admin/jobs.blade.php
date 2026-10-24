@extends('layouts.app', ['title' => 'Manage Jobs - Admin - FindJobPh'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-purple-600 hover:underline">
                &larr; Admin Dashboard
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Platform Job Posts Moderation</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage, review, or remove job listings from all employers</p>
        </div>

        <span class="px-3.5 py-1.5 rounded-xl bg-purple-50 text-purple-800 text-xs font-bold border border-purple-200">
            Total Jobs: {{ $jobs->total() }}
        </span>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <form action="{{ route('admin.jobs') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or company..."
                class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="category_id" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'category_id']))
                <a href="{{ route('admin.jobs') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 font-semibold">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Jobs Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Job Title & Employer</th>
                        <th class="px-6 py-3">Category & Location</th>
                        <th class="px-6 py-3">Requirements Attached</th>
                        <th class="px-6 py-3">Applicants</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Moderation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($jobs as $j)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('jobs.show', $j) }}" class="font-bold text-slate-900 hover:text-blue-600 text-sm block">
                                    {{ $j->title }}
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5">By {{ $j->company_name }} ({{ $j->employer->email }})</div>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                <div>{{ $j->category->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $j->location }} &bull; {{ $j->job_type }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800">{{ $j->requirements->count() }} required docs</span>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs">
                                    {{ $j->requirements->pluck('name')->join(', ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-blue-600">
                                {{ $j->applications_count }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.jobs.toggle', $j) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold cursor-pointer transition {{ $j->status === 'active' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $j->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ ucfirst($j->status) }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('jobs.show', $j) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold">
                                    View
                                </a>
                                <form action="{{ route('admin.jobs.delete', $j) }}" method="POST" class="inline" onsubmit="return confirm('Delete this job post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                No job postings match your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $jobs->links() }}
    </div>

</div>
@endsection
