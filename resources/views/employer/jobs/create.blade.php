@extends('layouts.app', ['title' => 'Post a New Job - FindJobPh'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Post a New Job Opening</h1>
            <p class="text-xs text-slate-500 mt-0.5">Specify job requirements, candidate qualifications, and required verification documents</p>
        </div>
        <a href="{{ route('employer.dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
            &larr; Back to Dashboard
        </a>
    </div>

    <form action="{{ route('employer.jobs.store') }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Section 1: Basic Information -->
        <div>
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 pb-2 border-b border-slate-100 mb-4">
                1. Job Overview
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Job Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" 
                        placeholder="e.g. Motorcycle Delivery Rider, Senior Videographer, Laravel Developer..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Company / Hiring Entity <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required value="{{ old('company_name', auth()->user()->company_name) }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Job Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition outline-hidden">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Work Location (City, Province, or Hub) <span class="text-rose-500">*</span></label>
                    <input type="text" name="location" required value="{{ old('location') }}"
                        placeholder="e.g. Quezon City, Makati, Cebu City, Remote..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Job Type</label>
                        <select name="job_type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm">
                            <option value="Full-time" {{ old('job_type') === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Part-time" {{ old('job_type') === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Contract" {{ old('job_type') === 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Freelance" {{ old('job_type') === 'Freelance' ? 'selected' : '' }}>Freelance</option>
                            <option value="Internship" {{ old('job_type') === 'Internship' ? 'selected' : '' }}>Internship</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Workplace</label>
                        <select name="workplace_type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm">
                            <option value="On-site" {{ old('workplace_type') === 'On-site' ? 'selected' : '' }}>On-site</option>
                            <option value="Remote" {{ old('workplace_type') === 'Remote' ? 'selected' : '' }}>Remote</option>
                            <option value="Hybrid" {{ old('workplace_type') === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
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
                    <input type="number" name="salary_min" value="{{ old('salary_min') }}" placeholder="e.g. 25000"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-hidden focus:bg-white focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Maximum (₱)</label>
                    <input type="number" name="salary_max" value="{{ old('salary_max') }}" placeholder="e.g. 35000"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-hidden focus:bg-white focus:border-indigo-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Period</label>
                    <select name="salary_period" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                        <option value="month" {{ old('salary_period') === 'month' ? 'selected' : '' }}>Per Month</option>
                        <option value="day" {{ old('salary_period') === 'day' ? 'selected' : '' }}>Per Day</option>
                        <option value="hour" {{ old('salary_period') === 'hour' ? 'selected' : '' }}>Per Hour</option>
                        <option value="project" {{ old('salary_period') === 'project' ? 'selected' : '' }}>Per Project</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 3: ATTACHED REQUIREMENT DOCUMENTS (Gating Engine) -->
        <div class="bg-indigo-50/50 p-5 sm:p-6 rounded-2xl border border-indigo-200">
            <div class="flex items-center gap-2 mb-2">
                <span class="p-1 rounded-lg bg-indigo-600 text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </span>
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-indigo-950">
                    3. Mandatory Verification Documents (Application Gating)
                </h2>
            </div>
            <p class="text-xs text-indigo-800 mb-4">
                Select the documents applicants <strong>must upload</strong> before the system allows them to click "Apply". If they lack any checked document, their Apply button will be disabled.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($requirementTypes as $req)
                    <label class="flex items-start gap-3 p-3 bg-white rounded-xl border border-indigo-100 hover:border-indigo-300 transition cursor-pointer">
                        <input type="checkbox" name="requirements[]" value="{{ $req->id }}"
                            class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            {{ in_array($req->id, old('requirements', [1, 2])) ? 'checked' : '' }}>
                        <div>
                            <span class="font-bold text-xs text-slate-900 block">{{ $req->name }}</span>
                            <span class="text-[11px] text-slate-500 block leading-tight">{{ $req->description }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Section 4: Details & Responsibilities -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 pb-2 border-b border-slate-100">
                4. Job Description & Responsibilities
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Job Description <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="4" required placeholder="Provide a summary of the role, team, and company culture..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm outline-hidden focus:bg-white focus:border-indigo-600">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Key Responsibilities (Optional)</label>
                    <textarea name="responsibilities" rows="4" placeholder="• Item 1&#10;• Item 2..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">{{ old('responsibilities') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Qualifications & Requirements (Optional)</label>
                    <textarea name="qualifications" rows="4" placeholder="• Experience level&#10;• Tools or vehicles needed..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">{{ old('qualifications') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Benefits & Perks (Optional)</label>
                <textarea name="benefits" rows="2" placeholder="HMO, accident insurance, fuel subsidies, 14th month pay..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">{{ old('benefits') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Search Tags (Comma separated)</label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="e.g. Rider, Motorcycle, Urgent, Delivery, FullTime"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-hidden focus:bg-white focus:border-indigo-600">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('employer.dashboard') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                Cancel
            </a>
            <button type="submit" class="px-7 py-3 text-sm font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/25 rounded-2xl transition">
                Publish Job Opening
            </button>
        </div>
    </form>

</div>
@endsection
