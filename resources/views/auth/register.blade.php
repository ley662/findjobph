@extends('layouts.app', ['title' => 'Register - FindJobPh'])

@section('content')
<div class="max-w-xl mx-auto my-6 sm:my-10" x-data="{ role: '{{ old('role', $selectedRole ?? 'applicant') }}' }">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 p-6 sm:p-8">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create your FindJob<span class="text-blue-600">Ph</span> Account</h1>
            <p class="text-xs text-slate-500 mt-1">Join thousands of Filipino workers and verified employers</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">I am registering as an:</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex flex-col p-4 cursor-pointer rounded-2xl border-2 transition"
                        :class="role === 'applicant' ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="role" value="applicant" x-model="role" class="sr-only">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="p-1.5 rounded-lg bg-blue-100 text-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <span class="font-bold text-sm text-slate-900">Job Applicant</span>
                        </div>
                        <span class="text-[11px] text-slate-500">I am looking for courier, creative, tech, or service jobs</span>
                    </label>

                    <label class="relative flex flex-col p-4 cursor-pointer rounded-2xl border-2 transition"
                        :class="role === 'employer' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-100' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="role" value="employer" x-model="role" class="sr-only">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </span>
                            <span class="font-bold text-sm text-slate-900">Employer</span>
                        </div>
                        <span class="text-[11px] text-slate-500">I want to post jobs and verify applicants' requirements</span>
                    </label>
                </div>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                    placeholder="e.g. Juan dela Cruz"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-hidden">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Company Name (Conditional for Employer) -->
            <div x-show="role === 'employer'" x-transition>
                <label for="company_name" class="block text-xs font-bold text-indigo-900 mb-1">
                    Company / Organization Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                    placeholder="e.g. Express Courier & Logistics Corp."
                    class="w-full px-3.5 py-2.5 bg-indigo-50/50 border border-indigo-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition outline-hidden">
                @error('company_name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & Phone in 2 cols -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                        placeholder="e.g. juan@example.com"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-hidden">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Mobile Number (Optional)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        placeholder="e.g. 0917 123 4567"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-hidden">
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password & Confirmation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                        placeholder="At least 8 characters"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-hidden">
                    @error('password')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        placeholder="Re-type password"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-hidden">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 hover:shadow-lg transition">
                Create My Account
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">
                Sign In
            </a>
        </div>
    </div>
</div>
@endsection
