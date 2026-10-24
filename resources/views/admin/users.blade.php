@extends('layouts.app', ['title' => 'Manage Users - Admin - FindJobPh'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-purple-600 hover:underline">
                &larr; Admin Dashboard
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">Platform Users Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Activate, suspend, or delete employer and applicant accounts</p>
        </div>

        <span class="px-3.5 py-1.5 rounded-xl bg-purple-50 text-purple-800 text-xs font-bold border border-purple-200">
            Total Users: {{ $users->total() }}
        </span>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <form action="{{ route('admin.users') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
                class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden focus:bg-white focus:border-purple-600">

            <select name="role" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="employer" {{ request('role') === 'employer' ? 'selected' : '' }}>Employer</option>
                <option value="applicant" {{ request('role') === 'applicant' ? 'selected' : '' }}>Applicant</option>
            </select>

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 outline-hidden">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 font-semibold">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">User & Contact</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Activity</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Moderation Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/70 transition {{ !$u->is_active ? 'bg-rose-50/30' : '' }}">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $u->email }}</div>
                                @if($u->company_name)
                                    <div class="text-[10px] text-indigo-600 font-semibold mt-0.5">🏢 {{ $u->company_name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($u->role === 'employer' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                @if($u->role === 'employer')
                                    <span>{{ $u->job_posts_count }} jobs posted</span>
                                @elseif($u->role === 'applicant')
                                    <span>{{ $u->applications_count }} applications</span>
                                @else
                                    <span>System Administrator</span>
                                @endif
                                <div class="text-[10px] text-slate-400">Joined {{ $u->created_at->format('M Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="font-bold text-xs {{ $u->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                            {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.users.delete', $u) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete user {{ $u->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-xs text-rose-600 hover:text-rose-800">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">(You)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                No users matched your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $users->links() }}
    </div>

</div>
@endsection
