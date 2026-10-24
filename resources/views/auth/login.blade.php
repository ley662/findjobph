@extends('layouts.app', ['title' => 'Sign In - FindJobPh'])

@section('content')
<div class="max-w-md mx-auto my-6 sm:my-10">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 p-6 sm:p-8">
        
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-500 mx-auto flex items-center justify-center text-white shadow-md shadow-blue-500/20 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Sign in to FindJob<span class="text-blue-600">Ph</span></h1>
            <p class="text-xs text-slate-500 mt-1">One portal for Jobseekers, Employers, and Administrators</p>
        </div>

        <!-- Quick 1-Click Demo Accounts -->
        <div class="mb-6 bg-slate-50 rounded-2xl p-3.5 border border-slate-200" x-data>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Quick 1-Click Demo Login Fill:
            </p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" @click="document.getElementById('email').value='juan.rider@example.com'; document.getElementById('password').value='password'" 
                    class="p-2 text-left bg-white hover:bg-emerald-50 rounded-xl border border-slate-200 hover:border-emerald-300 transition group">
                    <div class="font-bold text-slate-800 group-hover:text-emerald-700 flex items-center gap-1">
                        🛵 Rider Juan
                    </div>
                    <div class="text-[10px] text-slate-500">Applicant (Missing NBI)</div>
                </button>

                <button type="button" @click="document.getElementById('email').value='maria.creative@example.com'; document.getElementById('password').value='password'" 
                    class="p-2 text-left bg-white hover:bg-emerald-50 rounded-xl border border-slate-200 hover:border-emerald-300 transition group">
                    <div class="font-bold text-slate-800 group-hover:text-emerald-700 flex items-center gap-1">
                        ✨ Maria Santos
                    </div>
                    <div class="text-[10px] text-slate-500">Applicant (100% Ready)</div>
                </button>

                <button type="button" @click="document.getElementById('email').value='express@angkaslike.ph'; document.getElementById('password').value='password'" 
                    class="p-2 text-left bg-white hover:bg-indigo-50 rounded-xl border border-slate-200 hover:border-indigo-300 transition group">
                    <div class="font-bold text-slate-800 group-hover:text-indigo-700 flex items-center gap-1">
                        🏢 Express Logistics
                    </div>
                    <div class="text-[10px] text-slate-500">Employer Account</div>
                </button>

                <button type="button" @click="document.getElementById('email').value='admin@findjobph.com'; document.getElementById('password').value='password'" 
                    class="p-2 text-left bg-white hover:bg-purple-50 rounded-xl border border-slate-200 hover:border-purple-300 transition group">
                    <div class="font-bold text-slate-800 group-hover:text-purple-700 flex items-center gap-1">
                        🛡️ Platform Admin
                    </div>
                    <div class="text-[10px] text-slate-500">Admin Oversight</div>
                </button>
            </div>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

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
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition outline-hidden">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span>Remember me on this device</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 hover:shadow-lg transition">
                Sign In to Account
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            Don't have an account yet? 
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">
                Create Free Account
            </a>
        </div>
    </div>
</div>
@endsection
