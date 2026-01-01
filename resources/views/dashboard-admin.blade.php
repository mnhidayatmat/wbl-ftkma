@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    /* Elegant gradient background */
    .admin-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 25%, #3b82a0 50%, #4a9eb8 75%, #1e3a5f 100%);
        background-size: 400% 400%;
        animation: elegantGradient 20s ease infinite;
    }

    @keyframes elegantGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Glass morphism effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .glass-card {
        background: rgba(31, 41, 55, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Elegant card hover */
    .elegant-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .elegant-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.6s;
    }

    .elegant-card:hover::before {
        left: 100%;
    }

    .elegant-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    /* KPI card gradients */
    .kpi-gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-gradient-2 { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .kpi-gradient-3 { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }
    .kpi-gradient-4 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .kpi-gradient-5 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .kpi-gradient-6 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

    /* Pulse animation for alerts */
    .pulse-alert {
        animation: pulseAlert 2s ease-in-out infinite;
    }

    @keyframes pulseAlert {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
    }

    /* Smooth progress bar animation */
    .progress-animate {
        animation: progressFill 1.5s ease-out forwards;
    }

    @keyframes progressFill {
        from { width: 0%; }
    }

    /* Icon float */
    .icon-float {
        animation: iconFloat 3s ease-in-out infinite;
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-5px) rotate(2deg); }
    }

    /* Sparkle effect */
    .sparkle {
        position: relative;
    }

    .sparkle::after {
        content: '✨';
        position: absolute;
        top: -5px;
        right: -10px;
        font-size: 12px;
        animation: sparkleAnim 1.5s ease-in-out infinite;
    }

    @keyframes sparkleAnim {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    /* Table row hover */
    .table-row-elegant {
        transition: all 0.3s ease;
    }

    .table-row-elegant:hover {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
    }

    /* Badge shine */
    .badge-shine {
        position: relative;
        overflow: hidden;
    }

    .badge-shine::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: badgeShine 3s infinite;
    }

    @keyframes badgeShine {
        0% { left: -100%; }
        50%, 100% { left: 150%; }
    }

    /* Section divider */
    .section-divider {
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #4facfe);
        border-radius: 2px;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30 dark:from-gray-900 dark:via-indigo-950/20 dark:to-purple-950/20" x-data="{ showFilters: false }">

    <!-- Elegant Admin Header -->
    <div class="admin-hero rounded-2xl p-8 mb-8 text-white relative overflow-hidden shadow-2xl">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute top-1/2 right-1/4 w-32 h-32 bg-white/5 rounded-full"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center icon-float">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
                            <p class="text-white/80">Welcome back! Here's your system overview.</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Pills -->
                <div class="hidden lg:flex items-center gap-3">
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                        <p class="text-xs text-white/70">Today</p>
                        <p class="text-lg font-bold">{{ now()->format('d M Y') }}</p>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                        <p class="text-xs text-white/70">Active Groups</p>
                        <p class="text-lg font-bold">{{ $kpiCards['groups']['active'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter Toggle -->
            <div class="mt-6 flex items-center gap-4">
                <button @click="showFilters = !showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl text-sm font-medium hover:bg-white/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                    <svg class="w-4 h-4 transition-transform" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Inline filter pills when collapsed -->
                <div x-show="!showFilters" class="flex items-center gap-2">
                    <a href="{{ route('dashboard', ['group_filter' => 'all']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $groupFilter === 'all' ? 'bg-white text-indigo-700 font-bold' : 'bg-white/20 hover:bg-white/30' }}">
                        All
                    </a>
                    <a href="{{ route('dashboard', ['group_filter' => 'active']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $groupFilter === 'active' ? 'bg-emerald-400 text-emerald-900 font-bold' : 'bg-white/20 hover:bg-white/30' }}">
                        Active
                    </a>
                    <a href="{{ route('dashboard', ['group_filter' => 'completed']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $groupFilter === 'completed' ? 'bg-gray-300 text-gray-800 font-bold' : 'bg-white/20 hover:bg-white/30' }}">
                        Completed
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Expanded Filters Panel -->
    <div x-show="showFilters" x-collapse class="mb-6">
        <div class="glass-card rounded-2xl p-6 shadow-lg">
            <div class="flex flex-wrap items-center gap-4">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Group Status:</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard', ['group_filter' => 'all']) }}"
                       class="px-4 py-2 text-sm rounded-xl font-medium transition-all {{ $groupFilter === 'all' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        All Groups
                    </a>
                    <a href="{{ route('dashboard', ['group_filter' => 'active']) }}"
                       class="px-4 py-2 text-sm rounded-xl font-medium transition-all {{ $groupFilter === 'active' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Active Only
                    </a>
                    <a href="{{ route('dashboard', ['group_filter' => 'completed']) }}"
                       class="px-4 py-2 text-sm rounded-xl font-medium transition-all {{ $groupFilter === 'completed' ? 'bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Completed
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($systemAlerts) > 0)
    <div class="mb-8 space-y-3">
        @foreach($systemAlerts as $alert)
        <div class="elegant-card rounded-2xl border-l-4 overflow-hidden
            {{ $alert['type'] === 'danger' ? 'bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 border-red-500' : '' }}
            {{ $alert['type'] === 'warning' ? 'bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/30 border-amber-500' : '' }}
            {{ $alert['type'] === 'info' ? 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border-blue-500' : '' }}
            {{ $alert['type'] === 'danger' ? 'pulse-alert' : '' }}">
            <div class="flex items-center justify-between p-5">
                <div class="flex items-center gap-4">
                    @if($alert['type'] === 'danger')
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    @elseif($alert['type'] === 'warning')
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @else
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @endif
                    <span class="text-base font-semibold {{ $alert['type'] === 'danger' ? 'text-red-800 dark:text-red-200' : ($alert['type'] === 'warning' ? 'text-amber-800 dark:text-amber-200' : 'text-blue-800 dark:text-blue-200') }}">
                        {{ $alert['message'] }}
                    </span>
                </div>
                <a href="{{ $alert['link'] }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all
                    {{ $alert['type'] === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' : ($alert['type'] === 'warning' ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white') }}">
                    View
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- =====================================================
         1. TOP KPI SUMMARY CARDS
    ====================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-5 mb-8">
        <!-- Total Students -->
        <div class="elegant-card glass-card rounded-2xl p-6 group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl kpi-gradient-1 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Students</p>
                    </div>
                    <p class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ number_format($kpiCards['students']['total']) }}</p>
                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                        <span class="badge-shine inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                            {{ $kpiCards['students']['active'] }} Active
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {{ $kpiCards['students']['completed'] }} Done
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 group-hover:gap-2 transition-all">
                    View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- Active Groups -->
        <div class="elegant-card glass-card rounded-2xl p-6 group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl kpi-gradient-2 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Groups</p>
                    </div>
                    <p class="text-4xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">{{ number_format($kpiCards['groups']['total']) }}</p>
                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                        <span class="badge-shine inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                            {{ $kpiCards['groups']['active'] }} Active
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {{ $kpiCards['groups']['completed'] }} Done
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 group-hover:gap-2 transition-all">
                    Manage <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- Active Companies -->
        <div class="elegant-card glass-card rounded-2xl p-6 group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl kpi-gradient-3 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Companies</p>
                    </div>
                    <p class="text-4xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">{{ number_format($kpiCards['companies']['total']) }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-800 dark:text-purple-200">
                            {{ $kpiCards['companies']['with_students'] }} with students
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.companies.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 group-hover:gap-2 transition-all">
                    View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- Active Agreements -->
        <div class="elegant-card glass-card rounded-2xl p-6 group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl kpi-gradient-4 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agreements</p>
                    </div>
                    <p class="text-4xl font-black bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">{{ number_format($kpiCards['agreements']['total']) }}</p>
                    <div class="flex items-center gap-1 mt-3 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200">
                            {{ $kpiCards['agreements']['mou'] }} MoU
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-violet-100 text-violet-700 dark:bg-violet-800 dark:text-violet-200">
                            {{ $kpiCards['agreements']['moa'] }} MoA
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-800 dark:text-amber-200">
                            {{ $kpiCards['agreements']['loi'] }} LOI
                        </span>
                    </div>
                </div>
            </div>
            @if($kpiCards['agreements']['expiring_soon'] > 0)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <span class="inline-flex items-center gap-1 text-sm font-bold text-red-600 dark:text-red-400">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    {{ $kpiCards['agreements']['expiring_soon'] }} expiring soon
                </span>
            </div>
            @else
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.agreements.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-pink-600 hover:text-pink-800 dark:text-pink-400 group-hover:gap-2 transition-all">
                    View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            @endif
        </div>

        <!-- Placement Completion -->
        <div class="elegant-card glass-card rounded-2xl p-6 group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl kpi-gradient-5 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Placement</p>
                    </div>
                    <p class="text-4xl font-black bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">{{ $kpiCards['placement']['completion_rate'] }}%</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2.5 rounded-full progress-animate" style="width: {{ $kpiCards['placement']['completion_rate'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $kpiCards['placement']['confirmed'] }} confirmed</span>
                        <span>{{ $kpiCards['placement']['pending'] }} pending</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('placement.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 group-hover:gap-2 transition-all">
                    View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- Workplace Issues -->
        <div class="elegant-card glass-card rounded-2xl p-6 group {{ $workplaceIssueStats['critical_high'] > 0 ? 'ring-2 ring-red-400 ring-offset-2' : '' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl {{ $workplaceIssueStats['critical_high'] > 0 ? 'bg-gradient-to-br from-red-500 to-rose-600' : 'kpi-gradient-6' }} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Issues</p>
                    </div>
                    <p class="text-4xl font-black {{ $workplaceIssueStats['critical_high'] > 0 ? 'text-red-600' : 'bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent' }}">{{ number_format($workplaceIssueStats['open']) }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        @if($workplaceIssueStats['critical_high'] > 0)
                            <span class="badge-shine inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-500 to-rose-500 text-white">
                                {{ $workplaceIssueStats['critical_high'] }} urgent
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                                All clear
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('workplace-issues.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-800 dark:text-orange-400 group-hover:gap-2 transition-all">
                    Manage <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Section Divider -->
    <div class="section-divider mb-8"></div>

    <!-- =====================================================
         2. STUDENT DISTRIBUTION
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Students by Group (Stacked Bar) -->
        <div class="lg:col-span-2 elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Students by Group</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Placement status per cohort</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"></span>
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Placed</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gradient-to-r from-rose-400 to-red-500"></span>
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Not Placed</span>
                    </div>
                </div>
            </div>
            <div class="h-[300px]">
                <canvas id="studentsByGroupChart"></canvas>
            </div>
        </div>

        <!-- Students by Programme (Donut) -->
        <div class="elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">By Programme</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Distribution</p>
                </div>
            </div>
            <div class="h-[200px]">
                <canvas id="studentsByProgrammeChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($studentsByProgramme['labels'] as $index => $label)
                <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background: linear-gradient(135deg, {{ ['#667eea', '#11998e', '#6a11cb', '#f093fb', '#fa709a'][$index % 5] }}, {{ ['#764ba2', '#38ef7d', '#2575fc', '#f5576c', '#fee140'][$index % 5] }})"></span>
                        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $label }}</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $studentsByProgramme['data'][$index] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- =====================================================
         3. PLACEMENT HEALTH
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Placement Funnel -->
        <div class="lg:col-span-2 elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Placement Pipeline</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Student journey from resume to confirmation</p>
                </div>
            </div>
            <div class="space-y-4">
                @php
                    $maxCount = max(array_column($placementFunnel, 'count')) ?: 1;
                @endphp
                @foreach($placementFunnel as $stage)
                <div class="relative group">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $stage['stage'] }}</span>
                        <span class="text-sm font-black text-gray-900 dark:text-white">{{ $stage['count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-xl h-10 overflow-hidden">
                        <div class="h-full rounded-xl flex items-center justify-end pr-4 transition-all duration-700 progress-animate group-hover:opacity-90"
                             style="width: {{ ($stage['count'] / $maxCount) * 100 }}%; background: linear-gradient(135deg, {{ $stage['color'] }}, {{ $stage['color'] }}dd)">
                            <span class="text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity">{{ round(($stage['count'] / $maxCount) * 100) }}%</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">At-Risk</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Needs attention</p>
                    </div>
                </div>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white text-lg font-black">
                    {{ count($atRiskStudents) }}
                </span>
            </div>
            @if(count($atRiskStudents) > 0)
            <div class="space-y-3 max-h-[350px] overflow-y-auto">
                @foreach($atRiskStudents as $student)
                <div class="p-4 rounded-xl transition-all hover:scale-[1.02] {{ $student['risk_level'] === 'high' ? 'bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 border border-red-200 dark:border-red-800' : 'bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/30 border border-amber-200 dark:border-amber-800' }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $student['student_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student['matric_no'] }} • {{ $student['programme'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $student['risk_level'] === 'high' ? 'bg-red-500 text-white' : 'bg-amber-500 text-white' }}">
                            {{ $student['days_stuck'] }}d
                        </span>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $student['group'] }}</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span>{{ $student['status'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-10">
                <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">All students are on track!</p>
            </div>
            @endif
        </div>
    </div>

    <!-- =====================================================
         4. COMPANY & AGREEMENT INTELLIGENCE
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Companies by Agreement Type -->
        <div class="elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Agreements by Type</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active industry partnerships</p>
                </div>
            </div>
            <div class="h-[250px]">
                <canvas id="agreementsByTypeChart"></canvas>
            </div>
        </div>

        <!-- Agreement Expiry Watchlist -->
        <div class="elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Expiry Watchlist</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Within 6 months</p>
                    </div>
                </div>
                <a href="{{ route('admin.agreements.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-amber-600 hover:text-amber-800">
                    View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            @if(count($expiryWatchlist) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left py-3 font-semibold">Company</th>
                            <th class="text-left py-3 font-semibold">Type</th>
                            <th class="text-left py-3 font-semibold">Expiry</th>
                            <th class="text-right py-3 font-semibold">Days</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($expiryWatchlist as $agreement)
                        <tr class="table-row-elegant border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                            <td class="py-3 text-gray-900 dark:text-white font-semibold">{{ Str::limit($agreement['company'], 20) }}</td>
                            <td class="py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                    {{ $agreement['type'] === 'MoU' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : '' }}
                                    {{ $agreement['type'] === 'MoA' ? 'bg-gradient-to-r from-violet-500 to-purple-500 text-white' : '' }}
                                    {{ $agreement['type'] === 'LOI' ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white' : '' }}">
                                    {{ $agreement['type'] }}
                                </span>
                            </td>
                            <td class="py-3 text-gray-600 dark:text-gray-400">{{ $agreement['expiry_date'] }}</td>
                            <td class="py-3 text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                    {{ $agreement['urgency'] === 'critical' ? 'bg-red-500 text-white' : '' }}
                                    {{ $agreement['urgency'] === 'warning' ? 'bg-amber-500 text-white' : '' }}
                                    {{ $agreement['urgency'] === 'normal' ? 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                    {{ $agreement['days_remaining'] }}d
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-10">
                <p class="text-sm text-gray-500 dark:text-gray-400">No agreements expiring soon</p>
            </div>
            @endif
        </div>
    </div>

    <!-- =====================================================
         5. WORKPLACE SAFETY & STUDENT WELLBEING
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Workplace Issues by Status -->
        <div class="elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Issues by Status</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Safety & wellbeing reports</p>
                </div>
            </div>
            <div class="h-[200px]">
                <canvas id="workplaceIssuesByStatusChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach(['new', 'under_review', 'in_progress', 'resolved', 'closed'] as $index => $status)
                <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $workplaceIssuesByStatus['colors'][$index] }}"></span>
                        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $workplaceIssuesByStatus['labels'][$index] }}</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $workplaceIssuesByStatus['data'][$index] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Workplace Issues by Severity -->
        <div class="elegant-card glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">By Severity</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Priority distribution</p>
                </div>
            </div>
            <div class="h-[200px]">
                <canvas id="workplaceIssuesBySeverityChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach(['critical', 'high', 'medium', 'low'] as $index => $severity)
                <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $workplaceIssuesBySeverity['colors'][$index] }}"></span>
                        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $workplaceIssuesBySeverity['labels'][$index] }}</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $workplaceIssuesBySeverity['data'][$index] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- =====================================================
         6. CRITICAL WORKPLACE ISSUES ALERT PANEL
    ====================================================== -->
    @if(count($criticalWorkplaceIssues) > 0)
    <div class="elegant-card glass-card rounded-2xl border-2 border-red-300 dark:border-red-700 p-6 mb-8 relative overflow-hidden">
        <!-- Animated border glow -->
        <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 via-rose-500/5 to-red-500/5"></div>

        <div class="relative">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg icon-float">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">Critical Workplace Issues</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Requires immediate attention</p>
                    </div>
                </div>
                <span class="badge-shine inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white text-lg font-black shadow-lg">
                    {{ count($criticalWorkplaceIssues) }} urgent
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-xs text-gray-500 dark:text-gray-400 border-b-2 border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 font-bold">Student</th>
                            <th class="text-left py-3 font-bold">Company</th>
                            <th class="text-left py-3 font-bold">Category</th>
                            <th class="text-left py-3 font-bold">Severity</th>
                            <th class="text-left py-3 font-bold">Status</th>
                            <th class="text-left py-3 font-bold">Assigned</th>
                            <th class="text-right py-3 font-bold">Days</th>
                            <th class="text-right py-3 font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($criticalWorkplaceIssues as $issue)
                        <tr class="table-row-elegant border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                            <td class="py-4">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $issue['student_name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $issue['matric_no'] }} • {{ $issue['group'] }}</p>
                            </td>
                            <td class="py-4">
                                <p class="font-medium text-gray-900 dark:text-white">{{ Str::limit($issue['company'], 25) }}</p>
                            </td>
                            <td class="py-4 text-gray-600 dark:text-gray-400">{{ Str::limit($issue['category'], 20) }}</td>
                            <td class="py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                                    {{ $issue['severity'] === 'critical' ? 'bg-gradient-to-r from-red-500 to-rose-500 text-white' : 'bg-gradient-to-r from-orange-500 to-amber-500 text-white' }}">
                                    {{ $issue['severity_display'] }}
                                </span>
                            </td>
                            <td class="py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                                    {{ $issue['status'] === 'new' ? 'bg-purple-500 text-white' : '' }}
                                    {{ $issue['status'] === 'under_review' ? 'bg-blue-500 text-white' : '' }}
                                    {{ $issue['status'] === 'in_progress' ? 'bg-amber-500 text-white' : '' }}">
                                    {{ $issue['status_display'] }}
                                </span>
                            </td>
                            <td class="py-4 text-gray-600 dark:text-gray-400 font-medium">{{ $issue['assigned_to'] }}</td>
                            <td class="py-4 text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black
                                    {{ $issue['days_open'] > 7 ? 'bg-red-500 text-white' : 'bg-amber-500 text-white' }}">
                                    {{ $issue['days_open'] }}d
                                </span>
                            </td>
                            <td class="py-4 text-right">
                                <a href="{{ route('workplace-issues.show', $issue['id']) }}"
                                   class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-xl transition-all shadow-md hover:shadow-lg">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- =====================================================
         7. WORKPLACE ISSUE RESPONSE METRICS
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Average Response Time -->
        <div class="elegant-card rounded-2xl p-6 bg-gradient-to-br from-blue-500 to-indigo-600 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-white/80 mb-1">Avg Response Time</p>
                <p class="text-5xl font-black">{{ $workplaceIssueMetrics['avg_response_hours'] }}</p>
                <p class="text-sm text-white/70 mt-2">hours to first review</p>
            </div>
        </div>

        <!-- Average Resolution Time -->
        <div class="elegant-card rounded-2xl p-6 bg-gradient-to-br from-emerald-500 to-teal-600 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-white/80 mb-1">Avg Resolution Time</p>
                <p class="text-5xl font-black">{{ $workplaceIssueMetrics['avg_resolution_days'] }}</p>
                <p class="text-sm text-white/70 mt-2">days to resolve</p>
            </div>
        </div>

        <!-- Student Feedback Rate -->
        <div class="elegant-card rounded-2xl p-6 bg-gradient-to-br from-violet-500 to-purple-600 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-white/80 mb-1">Student Feedback</p>
                <p class="text-5xl font-black">{{ $workplaceIssueMetrics['feedback_rate'] }}%</p>
                <p class="text-sm text-white/70 mt-2">{{ $workplaceIssueMetrics['with_feedback'] }}/{{ $workplaceIssueMetrics['total_resolved'] }} provided feedback</p>
            </div>
        </div>
    </div>

    <!-- =====================================================
         8. COMPANIES WITH MOST WORKPLACE ISSUES
    ====================================================== -->
    @if(count($companiesWithIssues) > 0)
    <div class="elegant-card glass-card rounded-2xl p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Companies with Most Issues</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor for student safety</p>
                </div>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Top 10</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-gray-500 dark:text-gray-400 border-b-2 border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 font-bold">Rank</th>
                        <th class="text-left py-3 font-bold">Company</th>
                        <th class="text-center py-3 font-bold">Total</th>
                        <th class="text-center py-3 font-bold">Critical</th>
                        <th class="text-center py-3 font-bold">High</th>
                        <th class="text-center py-3 font-bold">Medium</th>
                        <th class="text-center py-3 font-bold">Low</th>
                        <th class="text-center py-3 font-bold">Open</th>
                        <th class="text-center py-3 font-bold">Risk</th>
                        <th class="text-right py-3 font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($companiesWithIssues as $index => $company)
                    <tr class="table-row-elegant border-b border-gray-100 dark:border-gray-700/50 last:border-0
                        {{ $company['risk_level'] === 'high' ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                        <td class="py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-black
                                {{ $index === 0 ? 'bg-gradient-to-r from-red-500 to-rose-500 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="py-4">
                            <p class="font-bold text-gray-900 dark:text-white">{{ $company['company_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $company['company_id'] }}</p>
                        </td>
                        <td class="py-4 text-center">
                            <span class="font-black text-lg text-gray-900 dark:text-white">{{ $company['total_issues'] }}</span>
                        </td>
                        <td class="py-4 text-center">
                            @if($company['critical_count'] > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-red-500 text-white">
                                    {{ $company['critical_count'] }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 text-center">
                            @if($company['high_count'] > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-orange-500 text-white">
                                    {{ $company['high_count'] }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 text-center text-gray-700 dark:text-gray-300">{{ $company['medium_count'] ?: '-' }}</td>
                        <td class="py-4 text-center text-gray-600 dark:text-gray-400">{{ $company['low_count'] ?: '-' }}</td>
                        <td class="py-4 text-center">
                            @if($company['open_issues'] > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500 text-white">
                                    {{ $company['open_issues'] }}
                                </span>
                            @else
                                <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold">Resolved</span>
                            @endif
                        </td>
                        <td class="py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black
                                {{ $company['risk_level'] === 'high' ? 'bg-gradient-to-r from-red-500 to-rose-500 text-white' : '' }}
                                {{ $company['risk_level'] === 'medium' ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white' : '' }}
                                {{ $company['risk_level'] === 'low' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white' : '' }}">
                                {{ ucfirst($company['risk_level']) }}
                            </span>
                        </td>
                        <td class="py-4 text-right">
                            <a href="{{ route('admin.companies.show', $company['company_id']) }}"
                               class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-xl transition-all">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-xs text-amber-800 dark:text-amber-200">
                    <p class="font-bold mb-2">Risk Level Guide:</p>
                    <ul class="space-y-1">
                        <li><span class="font-bold text-red-600">High Risk:</span> 3+ critical OR 5+ high severity - Consider restricting placements</li>
                        <li><span class="font-bold text-orange-600">Medium Risk:</span> 1-2 critical OR 2-4 high severity - Monitor closely</li>
                        <li><span class="font-bold text-emerald-600">Low Risk:</span> Only low/medium severity - Standard monitoring</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- =====================================================
         9. ASSESSMENT COMPLETION
    ====================================================== -->
    <div class="elegant-card glass-card rounded-2xl p-6 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Assessment Completion</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Marks submission progress by course</p>
            </div>
        </div>
        <div class="space-y-5">
            @foreach($assessmentCompletion as $course => $data)
            <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <span class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br
                    {{ $data['percentage'] >= 80 ? 'from-emerald-500 to-teal-600' : ($data['percentage'] >= 50 ? 'from-amber-500 to-orange-600' : 'from-gray-400 to-gray-500') }}
                    text-white text-sm font-black flex-shrink-0 shadow-lg">
                    {{ $course }}
                </span>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $data['assessments'] }} assessments</span>
                        <span class="text-lg font-black {{ $data['percentage'] >= 80 ? 'text-emerald-600' : ($data['percentage'] >= 50 ? 'text-amber-600' : 'text-gray-600') }}">
                            {{ $data['percentage'] }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-700 progress-animate
                            {{ $data['percentage'] >= 80 ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : ($data['percentage'] >= 50 ? 'bg-gradient-to-r from-amber-500 to-orange-500' : 'bg-gradient-to-r from-gray-400 to-gray-500') }}"
                             style="width: {{ $data['percentage'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        {{ number_format($data['submitted']) }} / {{ number_format($data['total_possible']) }} marks submitted
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elegant color palette
    const colors = {
        primary: '#667eea',
        secondary: '#764ba2',
        success: '#11998e',
        successLight: '#38ef7d',
        info: '#4facfe',
        infoLight: '#00f2fe',
        warning: '#f093fb',
        warningLight: '#f5576c',
        danger: '#fa709a',
        dangerLight: '#fee140',
        purple: '#6a11cb',
        purpleLight: '#2575fc',
    };

    // Chart.js default styling
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.font.weight = '500';

    // Students by Group (Stacked Bar)
    const groupCtx = document.getElementById('studentsByGroupChart');
    if (groupCtx) {
        new Chart(groupCtx, {
            type: 'bar',
            data: {
                labels: @json($studentsByGroup['labels']),
                datasets: [
                    {
                        label: 'Placed',
                        data: @json($studentsByGroup['placed']),
                        backgroundColor: 'rgba(17, 153, 142, 0.8)',
                        borderColor: '#11998e',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        label: 'Not Placed',
                        data: @json($studentsByGroup['not_placed']),
                        backgroundColor: 'rgba(248, 113, 113, 0.8)',
                        borderColor: '#f87171',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { size: 14, weight: 'bold' },
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    }

    // Students by Programme (Donut)
    const programmeCtx = document.getElementById('studentsByProgrammeChart');
    if (programmeCtx) {
        new Chart(programmeCtx, {
            type: 'doughnut',
            data: {
                labels: @json($studentsByProgramme['labels']),
                datasets: [{
                    data: @json($studentsByProgramme['data']),
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.9)',
                        'rgba(17, 153, 142, 0.9)',
                        'rgba(106, 17, 203, 0.9)',
                        'rgba(240, 147, 251, 0.9)',
                        'rgba(250, 112, 154, 0.9)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                    }
                }
            }
        });
    }

    // Agreements by Type (Donut)
    const agreementCtx = document.getElementById('agreementsByTypeChart');
    if (agreementCtx) {
        new Chart(agreementCtx, {
            type: 'doughnut',
            data: {
                labels: @json($companiesByAgreement['labels']),
                datasets: [{
                    data: @json($companiesByAgreement['data']),
                    backgroundColor: [
                        'rgba(79, 172, 254, 0.9)',
                        'rgba(106, 17, 203, 0.9)',
                        'rgba(249, 115, 22, 0.9)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                    }
                }
            }
        });
    }

    // Workplace Issues by Status (Donut)
    const issuesStatusCtx = document.getElementById('workplaceIssuesByStatusChart');
    if (issuesStatusCtx) {
        new Chart(issuesStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($workplaceIssuesByStatus['labels']),
                datasets: [{
                    data: @json($workplaceIssuesByStatus['data']),
                    backgroundColor: @json($workplaceIssuesByStatus['colors']),
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                    }
                }
            }
        });
    }

    // Workplace Issues by Severity (Horizontal Bar)
    const issuesSeverityCtx = document.getElementById('workplaceIssuesBySeverityChart');
    if (issuesSeverityCtx) {
        new Chart(issuesSeverityCtx, {
            type: 'bar',
            data: {
                labels: @json($workplaceIssuesBySeverity['labels']),
                datasets: [{
                    label: 'Issues',
                    data: @json($workplaceIssuesBySeverity['data']),
                    backgroundColor: @json($workplaceIssuesBySeverity['colors']),
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        cornerRadius: 12,
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
