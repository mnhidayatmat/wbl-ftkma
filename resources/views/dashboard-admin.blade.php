@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen" x-data="{ showFilters: false }">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Dashboard</span>
                    <span class="mx-2">›</span>
                    <span class="text-gray-700 dark:text-gray-200">Admin Overview</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Work-Based Learning Programme Overview</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Filter Toggle -->
                <button @click="showFilters = !showFilters" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filters
                </button>
                <!-- Export Button -->
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-[#003A6C] text-white rounded-lg text-sm font-medium hover:bg-[#002D54] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
        
        <!-- Filters Panel -->
        <div x-show="showFilters" x-collapse class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Group Status:</span>
                    <a href="{{ route('dashboard', ['group_filter' => 'all']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $groupFilter === 'all' ? 'bg-[#003A6C] text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        All
                    </a>
                    <a href="{{ route('dashboard', ['group_filter' => 'active']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $groupFilter === 'active' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Active
                    </a>
                    <a href="{{ route('dashboard', ['group_filter' => 'completed']) }}" 
                       class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $groupFilter === 'completed' ? 'bg-gray-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        Completed
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if(count($systemAlerts) > 0)
    <div class="mb-6 space-y-2">
        @foreach($systemAlerts as $alert)
        <div class="flex items-center justify-between p-4 rounded-xl border
            {{ $alert['type'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : '' }}
            {{ $alert['type'] === 'warning' ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' : '' }}
            {{ $alert['type'] === 'info' ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}">
            <div class="flex items-center gap-3">
                @if($alert['type'] === 'danger')
                <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                @elseif($alert['type'] === 'warning')
                <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                @else
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                @endif
                <span class="text-sm font-medium {{ $alert['type'] === 'danger' ? 'text-red-800 dark:text-red-200' : ($alert['type'] === 'warning' ? 'text-amber-800 dark:text-amber-200' : 'text-blue-800 dark:text-blue-200') }}">
                    {{ $alert['message'] }}
                </span>
            </div>
            <a href="{{ $alert['link'] }}" class="text-sm font-medium {{ $alert['type'] === 'danger' ? 'text-red-600 dark:text-red-400 hover:text-red-700' : ($alert['type'] === 'warning' ? 'text-amber-600 dark:text-amber-400 hover:text-amber-700' : 'text-blue-600 dark:text-blue-400 hover:text-blue-700') }}">
                View →
            </a>
        </div>
        @endforeach
    </div>
    @endif

    <!-- =====================================================
         1. TOP KPI SUMMARY CARDS
    ====================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <!-- Total Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Students</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($kpiCards['students']['total']) }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                            {{ $kpiCards['students']['active'] }} Active
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {{ $kpiCards['students']['completed'] }} Completed
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.students.index') }}" class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium">View all students →</a>
            </div>
        </div>

        <!-- Active Groups -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Groups</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($kpiCards['groups']['total']) }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-100">
                            {{ $kpiCards['groups']['active'] }} Active
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {{ $kpiCards['groups']['completed'] }} Done
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.groups.index') }}" class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium">Manage groups →</a>
            </div>
        </div>

        <!-- Active Companies -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Companies</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($kpiCards['companies']['total']) }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                            {{ $kpiCards['companies']['with_students'] }} with students
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('companies.index') }}" class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium">View companies →</a>
            </div>
        </div>

        <!-- Active Agreements -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agreements</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($kpiCards['agreements']['total']) }}</p>
                    <div class="flex items-center gap-1 mt-2 flex-wrap">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200">
                            {{ $kpiCards['agreements']['mou'] }} MoU
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-800 dark:text-violet-200">
                            {{ $kpiCards['agreements']['moa'] }} MoA
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-800 dark:text-amber-200">
                            {{ $kpiCards['agreements']['loi'] }} LOI
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            @if($kpiCards['agreements']['expiring_soon'] > 0)
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <span class="text-xs text-red-600 dark:text-red-400 font-medium">⚠ {{ $kpiCards['agreements']['expiring_soon'] }} expiring soon</span>
            </div>
            @else
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.agreements.index') }}" class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium">View agreements →</a>
            </div>
            @endif
        </div>

        <!-- Placement Completion -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Placement</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $kpiCards['placement']['completion_rate'] }}%</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-3">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $kpiCards['placement']['completion_rate'] }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $kpiCards['placement']['confirmed'] }} confirmed</span>
                        <span>{{ $kpiCards['placement']['pending'] }} pending</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('placement.index') }}" class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium">View placements →</a>
            </div>
        </div>

        <!-- Workplace Issues -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group {{ $workplaceIssueStats['critical_high'] > 0 ? 'ring-2 ring-red-400' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Workplace Issues</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($workplaceIssueStats['open']) }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        @if($workplaceIssueStats['critical_high'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                {{ $workplaceIssueStats['critical_high'] }} urgent
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                All clear
                            </span>
                        @endif
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $workplaceIssueStats['critical_high'] > 0 ? 'from-red-500 to-red-600' : 'from-orange-500 to-orange-600' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('workplace-issues.index') }}" class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium">Manage issues →</a>
            </div>
        </div>
    </div>

    <!-- =====================================================
         2. STUDENT DISTRIBUTION
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Students by Group (Stacked Bar) -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Students by Group</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Placement status per cohort group</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-emerald-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Placed</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-red-400"></span>
                        <span class="text-gray-600 dark:text-gray-400">Not Placed</span>
                    </div>
                </div>
            </div>
            <div class="h-[300px]">
                <canvas id="studentsByGroupChart"></canvas>
            </div>
        </div>

        <!-- Students by Programme (Donut) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Students by Programme</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Distribution across programmes</p>
            </div>
            <div class="h-[250px]">
                <canvas id="studentsByProgrammeChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($studentsByProgramme['labels'] as $index => $label)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ ['#003A6C', '#0084C5', '#00AEEF', '#7C3AED', '#F59E0B'][$index % 5] }}"></span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $studentsByProgramme['data'][$index] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- =====================================================
         3. PLACEMENT HEALTH
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Placement Funnel -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Placement Pipeline</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Student journey from resume to confirmation</p>
            </div>
            <div class="space-y-3">
                @php
                    $maxCount = max(array_column($placementFunnel, 'count')) ?: 1;
                @endphp
                @foreach($placementFunnel as $stage)
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $stage['stage'] }}</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $stage['count'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-8 overflow-hidden">
                        <div class="h-full rounded-full flex items-center justify-end pr-3 transition-all duration-500"
                             style="width: {{ ($stage['count'] / $maxCount) * 100 }}%; background-color: {{ $stage['color'] }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">At-Risk Students</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Requires immediate attention</p>
                </div>
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 text-sm font-bold">
                    {{ count($atRiskStudents) }}
                </span>
            </div>
            @if(count($atRiskStudents) > 0)
            <div class="space-y-3 max-h-[350px] overflow-y-auto">
                @foreach($atRiskStudents as $student)
                <div class="p-3 rounded-lg {{ $student['risk_level'] === 'high' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student['student_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student['matric_no'] }} • {{ $student['programme'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $student['risk_level'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 'bg-amber-100 text-amber-800 dark:bg-amber-800 dark:text-amber-100' }}">
                            {{ $student['days_stuck'] }} days
                        </span>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        <span class="text-gray-500 dark:text-gray-400">{{ $student['group'] }}</span>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ $student['status'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">All students are on track!</p>
            </div>
            @endif
        </div>
    </div>

    <!-- =====================================================
         4. COMPANY & AGREEMENT INTELLIGENCE
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Companies by Agreement Type -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Agreements by Type</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active formal agreements with industry partners</p>
            </div>
            <div class="h-[250px]">
                <canvas id="agreementsByTypeChart"></canvas>
            </div>
        </div>

        <!-- Agreement Expiry Watchlist -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Expiry Watchlist</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Agreements expiring within 6 months</p>
                </div>
                <a href="{{ route('admin.agreements.index') }}" class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium">View all →</a>
            </div>
            @if(count($expiryWatchlist) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left py-2 font-medium">Company</th>
                            <th class="text-left py-2 font-medium">Type</th>
                            <th class="text-left py-2 font-medium">Expiry</th>
                            <th class="text-right py-2 font-medium">Days</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($expiryWatchlist as $agreement)
                        <tr class="border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                            <td class="py-2.5 text-gray-900 dark:text-white font-medium">{{ Str::limit($agreement['company'], 20) }}</td>
                            <td class="py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $agreement['type'] === 'MoU' ? 'bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200' : '' }}
                                    {{ $agreement['type'] === 'MoA' ? 'bg-violet-100 text-violet-700 dark:bg-violet-800 dark:text-violet-200' : '' }}
                                    {{ $agreement['type'] === 'LOI' ? 'bg-amber-100 text-amber-700 dark:bg-amber-800 dark:text-amber-200' : '' }}">
                                    {{ $agreement['type'] }}
                                </span>
                            </td>
                            <td class="py-2.5 text-gray-600 dark:text-gray-400">{{ $agreement['expiry_date'] }}</td>
                            <td class="py-2.5 text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                    {{ $agreement['urgency'] === 'critical' ? 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200' : '' }}
                                    {{ $agreement['urgency'] === 'warning' ? 'bg-amber-100 text-amber-700 dark:bg-amber-800 dark:text-amber-200' : '' }}
                                    {{ $agreement['urgency'] === 'normal' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                    {{ $agreement['days_remaining'] }}d
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No agreements expiring soon</p>
            </div>
            @endif
        </div>
    </div>

    <!-- =====================================================
         5. WORKPLACE SAFETY & STUDENT WELLBEING
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Workplace Issues by Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Workplace Issues by Status</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Student safety and wellbeing reports</p>
            </div>
            <div class="h-[250px]">
                <canvas id="workplaceIssuesByStatusChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach(['new', 'under_review', 'in_progress', 'resolved', 'closed'] as $index => $status)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $workplaceIssuesByStatus['colors'][$index] }}"></span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $workplaceIssuesByStatus['labels'][$index] }}</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $workplaceIssuesByStatus['data'][$index] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Workplace Issues by Severity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Issues by Severity</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Priority level distribution</p>
            </div>
            <div class="h-[250px]">
                <canvas id="workplaceIssuesBySeverityChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach(['critical', 'high', 'medium', 'low'] as $index => $severity)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $workplaceIssuesBySeverity['colors'][$index] }}"></span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $workplaceIssuesBySeverity['labels'][$index] }}</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $workplaceIssuesBySeverity['data'][$index] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- =====================================================
         6. CRITICAL WORKPLACE ISSUES ALERT PANEL
    ====================================================== -->
    @if(count($criticalWorkplaceIssues) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-red-200 dark:border-red-800 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Critical Workplace Issues</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Requires immediate attention</p>
                </div>
            </div>
            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 text-sm font-bold">
                {{ count($criticalWorkplaceIssues) }} urgent
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <th class="text-left py-2 font-medium">Student</th>
                        <th class="text-left py-2 font-medium">Company</th>
                        <th class="text-left py-2 font-medium">Category</th>
                        <th class="text-left py-2 font-medium">Severity</th>
                        <th class="text-left py-2 font-medium">Status</th>
                        <th class="text-left py-2 font-medium">Assigned To</th>
                        <th class="text-right py-2 font-medium">Days Open</th>
                        <th class="text-right py-2 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($criticalWorkplaceIssues as $issue)
                    <tr class="border-b border-gray-50 dark:border-gray-700/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="py-3">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $issue['student_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $issue['matric_no'] }} • {{ $issue['group'] }}</p>
                        </td>
                        <td class="py-3">
                            <p class="font-medium text-gray-900 dark:text-white">{{ Str::limit($issue['company'], 30) }}</p>
                        </td>
                        <td class="py-3 text-gray-600 dark:text-gray-400">{{ Str::limit($issue['category'], 25) }}</td>
                        <td class="py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $issue['severity'] === 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100' }}">
                                {{ $issue['severity_display'] }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $issue['status'] === 'new' ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
                                {{ $issue['status'] === 'under_review' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
                                {{ $issue['status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}">
                                {{ $issue['status_display'] }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-600 dark:text-gray-400">{{ $issue['assigned_to'] }}</td>
                        <td class="py-3 text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                {{ $issue['days_open'] > 7 ? 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200' }}">
                                {{ $issue['days_open'] }}d
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('workplace-issues.show', $issue['id']) }}"
                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-[#0084C5] hover:bg-[#003A6C] rounded-lg transition-colors">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- =====================================================
         7. WORKPLACE ISSUE RESPONSE METRICS
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Average Response Time -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Avg Response Time</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $workplaceIssueMetrics['avg_response_hours'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">hours to first review</p>
        </div>

        <!-- Average Resolution Time -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Avg Resolution Time</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $workplaceIssueMetrics['avg_resolution_days'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">days to resolve</p>
        </div>

        <!-- Student Feedback Rate -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-800 p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Student Feedback</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $workplaceIssueMetrics['feedback_rate'] }}%</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $workplaceIssueMetrics['with_feedback'] }}/{{ $workplaceIssueMetrics['total_resolved'] }} provided feedback</p>
        </div>
    </div>

    <!-- =====================================================
         8. COMPANIES WITH MOST WORKPLACE ISSUES
    ====================================================== -->
    @if(count($companiesWithIssues) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Companies with Most Workplace Issues</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Identify problematic companies for student safety monitoring</p>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Top 10 ranked by total issues</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <th class="text-left py-2 font-medium">Rank</th>
                        <th class="text-left py-2 font-medium">Company</th>
                        <th class="text-center py-2 font-medium">Total</th>
                        <th class="text-center py-2 font-medium">Critical</th>
                        <th class="text-center py-2 font-medium">High</th>
                        <th class="text-center py-2 font-medium">Medium</th>
                        <th class="text-center py-2 font-medium">Low</th>
                        <th class="text-center py-2 font-medium">Open</th>
                        <th class="text-center py-2 font-medium">Risk Level</th>
                        <th class="text-right py-2 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($companiesWithIssues as $index => $company)
                    <tr class="border-b border-gray-50 dark:border-gray-700/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700/30
                        {{ $company['risk_level'] === 'high' ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                        <td class="py-3">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                {{ $index === 0 ? 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200 font-bold' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }} text-xs">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td class="py-3">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $company['company_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $company['company_id'] }}</p>
                        </td>
                        <td class="py-3 text-center">
                            <span class="font-bold text-gray-900 dark:text-white">{{ $company['total_issues'] }}</span>
                        </td>
                        <td class="py-3 text-center">
                            @if($company['critical_count'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                    {{ $company['critical_count'] }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($company['high_count'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100">
                                    {{ $company['high_count'] }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($company['medium_count'] > 0)
                                <span class="text-gray-700 dark:text-gray-300">{{ $company['medium_count'] }}</span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($company['low_count'] > 0)
                                <span class="text-gray-600 dark:text-gray-400">{{ $company['low_count'] }}</span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($company['open_issues'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                    {{ $company['open_issues'] }} open
                                </span>
                            @else
                                <span class="text-green-600 dark:text-green-400 text-xs">All resolved</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $company['risk_level'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}
                                {{ $company['risk_level'] === 'medium' ? 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100' : '' }}
                                {{ $company['risk_level'] === 'low' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}">
                                {{ ucfirst($company['risk_level']) }} Risk
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('companies.show', $company['company_id']) }}"
                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-[#0084C5] hover:bg-[#003A6C] rounded-lg transition-colors">
                                View Company
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-xs text-yellow-800 dark:text-yellow-200">
                    <p class="font-semibold mb-1">Risk Level Guide:</p>
                    <ul class="space-y-1">
                        <li><strong>High Risk:</strong> 3+ critical issues OR 5+ high severity issues - Consider restricting student placements</li>
                        <li><strong>Medium Risk:</strong> 1-2 critical OR 2-4 high severity issues - Monitor closely and conduct follow-up</li>
                        <li><strong>Low Risk:</strong> Only low/medium severity issues - Standard monitoring sufficient</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- =====================================================
         9. ASSESSMENT COMPLETION
    ====================================================== -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assessment Completion</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Marks submission progress by course</p>
        </div>
        <div class="space-y-4">
            @foreach($assessmentCompletion as $course => $data)
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-bold flex-shrink-0">
                    {{ $course }}
                </span>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $data['assessments'] }} assessments</span>
                        <span class="text-sm font-bold {{ $data['percentage'] >= 80 ? 'text-green-600' : ($data['percentage'] >= 50 ? 'text-amber-600' : 'text-gray-600') }}">
                            {{ $data['percentage'] }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full transition-all duration-500
                            {{ $data['percentage'] >= 80 ? 'bg-green-500' : ($data['percentage'] >= 50 ? 'bg-amber-500' : 'bg-gray-400') }}"
                             style="width: {{ $data['percentage'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
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
    // Color palette
    const colors = {
        primary: '#003A6C',
        secondary: '#0084C5',
        accent: '#00AEEF',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        purple: '#7C3AED',
    };

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
                        backgroundColor: colors.success,
                        borderRadius: 4,
                    },
                    {
                        label: 'Not Placed',
                        data: @json($studentsByGroup['not_placed']),
                        backgroundColor: '#F87171',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
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
                    backgroundColor: [colors.primary, colors.secondary, colors.accent, colors.purple, colors.warning],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false
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
                    backgroundColor: @json($companiesByAgreement['colors']),
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
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
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false
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
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

