<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FindJobPh - Philippine Job Posting & Application Platform' }}</title>
    
    <!-- Google Fonts & Vite assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-100/70" x-data="{ mobileMenuOpen: false }">

    <!-- Top Sticky Navigation (Facebook-style header) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('jobs.feed') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xl font-extrabold tracking-tight text-slate-900">FindJob<span class="text-blue-600">Ph</span></span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">PH</span>
                            </div>
                            <span class="hidden sm:block text-[11px] font-medium text-slate-500 -mt-1">Verified Jobs & Requirements</span>
                        </div>
                    </a>
                </div>

                <!-- Global Quick Search Bar (Feed) -->
                <div class="hidden md:flex flex-1 max-w-md mx-4">
                    <form action="{{ route('jobs.feed') }}" method="GET" class="w-full relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Search job title, company, or city (e.g. Rider, QC, Manila)..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-100 hover:bg-slate-50 focus:bg-white text-sm rounded-full border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all outline-hidden">
                    </form>
                </div>

                <!-- Center Nav Links -->
                <nav class="hidden lg:flex items-center gap-1 font-medium text-sm text-slate-600">
                    <a href="{{ route('jobs.feed') }}" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100 transition {{ request()->routeIs('jobs.*') && !request()->routeIs('employer.*') ? 'text-blue-600 font-semibold bg-blue-50/60' : '' }}">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                            Feed
                        </span>
                    </a>

                    @auth
                        @if(auth()->user()->isApplicant())
                            <a href="{{ route('applicant.documents') }}" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100 transition {{ request()->routeIs('applicant.documents') ? 'text-blue-600 font-semibold bg-blue-50/60' : '' }}">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Requirements Vault
                                </span>
                            </a>
                            <a href="{{ route('applicant.applications') }}" class="px-3.5 py-2 rounded-lg hover:text-blue-600 hover:bg-slate-100 transition {{ request()->routeIs('applicant.applications') ? 'text-blue-600 font-semibold bg-blue-50/60' : '' }}">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    My Applications
                                </span>
                            </a>
                        @elseif(auth()->user()->isEmployer())
                            <a href="{{ route('employer.dashboard') }}" class="px-3.5 py-2 rounded-lg hover:text-indigo-600 hover:bg-slate-100 transition {{ request()->routeIs('employer.dashboard') ? 'text-indigo-600 font-semibold bg-indigo-50/60' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('employer.applicants') }}" class="px-3.5 py-2 rounded-lg hover:text-indigo-600 hover:bg-slate-100 transition {{ request()->routeIs('employer.applicants') ? 'text-indigo-600 font-semibold bg-indigo-50/60' : '' }}">
                                Applicants
                            </a>
                            <a href="{{ route('employer.jobs.create') }}" class="ml-2 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Post a Job
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-lg hover:text-purple-600 hover:bg-slate-100 transition {{ request()->routeIs('admin.dashboard') ? 'text-purple-600 font-semibold bg-purple-50/60' : '' }}">
                                Admin Overview
                            </a>
                            <a href="{{ route('admin.users') }}" class="px-3.5 py-2 rounded-lg hover:text-purple-600 hover:bg-slate-100 transition {{ request()->routeIs('admin.users') ? 'text-purple-600 font-semibold bg-purple-50/60' : '' }}">
                                Users
                            </a>
                            <a href="{{ route('admin.jobs') }}" class="px-3.5 py-2 rounded-lg hover:text-purple-600 hover:bg-slate-100 transition {{ request()->routeIs('admin.jobs') ? 'text-purple-600 font-semibold bg-purple-50/60' : '' }}">
                                Jobs
                            </a>
                            <a href="{{ route('admin.applications') }}" class="px-3.5 py-2 rounded-lg hover:text-purple-600 hover:bg-slate-100 transition {{ request()->routeIs('admin.applications') ? 'text-purple-600 font-semibold bg-purple-50/60' : '' }}">
                                Applications
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- Right Action Buttons / User Menu -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-slate-100 transition">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg shadow-sm shadow-blue-500/20 transition">
                            Sign Up
                        </a>
                    @else
                        <!-- Role Badge Indicator -->
                        @if(auth()->user()->isAdmin())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Admin
                            </span>
                        @elseif(auth()->user()->isEmployer())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Employer
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Applicant
                            </span>
                        @endif

                        <!-- User Dropdown Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false" type="button" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-hidden">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs border border-blue-200">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <span class="hidden sm:block text-sm font-medium text-slate-700 max-w-[120px] truncate">
                                    {{ auth()->user()->name }}
                                </span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 rounded-xl bg-white shadow-xl ring-1 ring-black/5 divide-y divide-slate-100 z-50 py-1" style="display: none;">
                                
                                <div class="px-4 py-3">
                                    <p class="text-xs text-slate-500 font-medium">Signed in as</p>
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                </div>

                                <div class="py-1 text-sm text-slate-700 font-medium">
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Admin Dashboard</a>
                                        <a href="{{ route('admin.users') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Manage Users</a>
                                        <a href="{{ route('admin.jobs') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Manage All Jobs</a>
                                    @elseif(auth()->user()->isEmployer())
                                        <a href="{{ route('employer.dashboard') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Employer Dashboard</a>
                                        <a href="{{ route('employer.jobs.create') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Post New Job</a>
                                        <a href="{{ route('employer.applicants') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Review Applicants</a>
                                    @else
                                        <a href="{{ route('applicant.documents') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Requirements Vault</a>
                                        <a href="{{ route('applicant.applications') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">My Applications</a>
                                    @endif
                                    <a href="{{ route('jobs.feed') }}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600">Browse Jobs Feed</a>
                                </div>

                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 font-medium transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endguest

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" x-transition class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2">
            <form action="{{ route('jobs.feed') }}" method="GET" class="w-full mb-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search jobs..."
                    class="w-full px-3 py-2 bg-slate-100 text-sm rounded-lg border border-slate-200 focus:outline-hidden">
            </form>

            <a href="{{ route('jobs.feed') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                Jobs Feed
            </a>

            @auth
                @if(auth()->user()->isApplicant())
                    <a href="{{ route('applicant.documents') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        Requirements Vault
                    </a>
                    <a href="{{ route('applicant.applications') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        My Applications
                    </a>
                @elseif(auth()->user()->isEmployer())
                    <a href="{{ route('employer.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        Employer Dashboard
                    </a>
                    <a href="{{ route('employer.jobs.create') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-indigo-600 font-semibold hover:bg-indigo-50">
                        + Post a New Job
                    </a>
                    <a href="{{ route('employer.applicants') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        Review Applicants
                    </a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        Admin Overview
                    </a>
                    <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        Manage Users
                    </a>
                    <a href="{{ route('admin.jobs') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        Manage Jobs
                    </a>
                    <a href="{{ route('admin.applications') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-800 hover:bg-slate-100">
                        All Applications
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-slate-100">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-rose-600 font-semibold hover:bg-rose-50 rounded-lg">
                        Sign Out
                    </button>
                </form>
            @else
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="w-full text-center py-2 text-sm font-semibold text-slate-700 bg-slate-100 rounded-lg">Log In</a>
                    <a href="{{ route('register') }}" class="w-full text-center py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg">Create Account</a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Global Flash Notification Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm flex items-start justify-between gap-3 text-emerald-800">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm flex items-start justify-between gap-3 text-rose-800">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 rounded-lg bg-rose-100 text-rose-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700 p-1">&times;</button>
            </div>
        @endif

        @if(session('warning'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-amber-50 border border-amber-200 p-4 shadow-sm flex items-start justify-between gap-3 text-amber-900">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 rounded-lg bg-amber-100 text-amber-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('warning') }}</p>
                </div>
                <button @click="show = false" class="text-amber-500 hover:text-amber-700 p-1">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-blue-50 border border-blue-200 p-4 shadow-sm flex items-start justify-between gap-3 text-blue-900">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 rounded-lg bg-blue-100 text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('info') }}</p>
                </div>
                <button @click="show = false" class="text-blue-500 hover:text-blue-700 p-1">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm text-rose-800">
                <div class="flex items-start justify-between gap-2">
                    <div class="font-semibold text-sm">Please resolve the following errors:</div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700">&times;</button>
                </div>
                <ul class="mt-2 list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-8 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800">FindJobPh</span>
                <span>&copy; {{ date('Y') }} — Connecting Filipino Jobseekers & Employers</span>
            </div>
            <div class="flex items-center gap-4 text-slate-600">
                <span class="inline-flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Verified DOLE Compliant Flow
                </span>
                <span>•</span>
                <a href="{{ route('jobs.feed') }}" class="hover:underline">Browse All Jobs</a>
                <span>•</span>
                <span>Philippines</span>
            </div>
        </div>
    </footer>

</body>
</html>
