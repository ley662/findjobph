@extends('layouts.app', ['title' => 'Edit ' . $job->title . ' - FindJobPh'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Job Post: {{ $job->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update job details and adjust mandatory requirements</p>
        </div>
        <a href="{{ route('employer.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
            &larr; Back to Dashboard
        </a>
    </div>

    <form action="{{ route('employer.jobs.update', $job) }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <!-- Section 1: Overview -->
        <div>
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 pb-2 border-b border-slate-100 mb-4">
                1. Job Overview & Status
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Job Title</label>
                    <input type="text" name="title" required value="{{ old('title', $job->title) }}" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Company / Hiring Entity</label>
                    <input type="text" name="company_name" required value="{{ old('company_name', $job->company_name) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Job Category</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 outline-hidden">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $job->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" required value="{{ old('location', $job->location) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Job Type</label>
                        <select name="job_type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm">
                            @foreach(['Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship'] as $type)
                                <option value="{{ $type }}" {{ old('job_type', $job->job_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-bold {{ $job->status === 'active' ? 'text-emerald-700' : 'text-slate-600' }}">
                            <option value="active" {{ old('status', $job->status) === 'active' ? 'selected' : '' }}>Active (Accepting)</option>
                            <option value="closed" {{ old('status', $job->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Compensation -->
        <div>
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 pb-2 border-b border-slate-100 mb-4">
                2. Salary & Compensation
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Minimum (₱)</label>
                    <input type="number" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-hidden focus:bg-white focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Maximum (₱)</label>
                    <input type="number" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-hidden focus:bg-white focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Period</label>
                    <select name="salary_period" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                        <option value="month" {{ old('salary_period', $job->salary_period) === 'month' ? 'selected' : '' }}>Per Month</option>
                        <option value="day" {{ old('salary_period', $job->salary_period) === 'day' ? 'selected' : '' }}>Per Day</option>
                        <option value="hour" {{ old('salary_period', $job->salary_period) === 'hour' ? 'selected' : '' }}>Per Hour</option>
                        <option value="project" {{ old('salary_period', $job->salary_period) === 'project' ? 'selected' : '' }}>Per Project</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 3: REQUIREMENTS CHECKLIST -->
        <div class="bg-indigo-50/50 p-5 sm:p-6 rounded-2xl border border-indigo-200">
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-indigo-950 mb-2">
                3. Mandatory Verification Documents (Gated Application)
            </h2>
            <p class="text-xs text-indigo-800 mb-4">
                Checked documents must be in applicant's vault before the Apply button activates.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($requirementTypes as $req)
                    <label class="flex items-start gap-3 p-3 bg-white rounded-xl border border-indigo-100 hover:border-indigo-300 transition cursor-pointer">
                        <input type="checkbox" name="requirements[]" value="{{ $req->id }}"
                            class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            {{ in_array($req->id, old('requirements', $selectedRequirements)) ? 'checked' : '' }}>
                        <div>
                            <span class="font-bold text-xs text-slate-900 block">{{ $req->name }}</span>
                            <span class="text-[11px] text-slate-500 block">{{ $req->description }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Section 4: Descriptions -->
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Job Description</label>
                <textarea name="description" rows="4" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm outline-hidden focus:bg-white focus:border-indigo-600">{{ old('description', $job->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Responsibilities</label>
                    <textarea name="responsibilities" rows="4" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">{{ old('responsibilities', $job->responsibilities) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Qualifications</label>
                    <textarea name="qualifications" rows="4" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">{{ old('qualifications', $job->qualifications) }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Benefits</label>
                <textarea name="benefits" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">{{ old('benefits', $job->benefits) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tags (Comma separated)</label>
                <input type="text" name="tags" value="{{ old('tags', !empty($job->tags) ? implode(', ', $job->tags) : '') }}"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('employer.dashboard') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                Cancel
            </a>
            <button type="submit" class="px-7 py-3 text-sm font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/25 rounded-2xl transition">
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection
