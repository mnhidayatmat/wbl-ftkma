@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Elegant gradient background */
    .dashboard-hero {
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

    /* Icon float */
    .icon-float {
        animation: iconFloat 3s ease-in-out infinite;
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-5px) rotate(2deg); }
    }

    /* Section divider */
    .section-divider {
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #4facfe);
        border-radius: 2px;
    }

    /* Chart card styling */
    .chart-card {
        position: relative;
        overflow: hidden;
    }

    .chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    /* Filter pills */
    .filter-pill {
        transition: all 0.3s ease;
    }

    .filter-pill:hover {
        transform: translateY(-2px);
    }

    .filter-pill.active {
        box-shadow: 0 4px 15px rgba(0, 132, 197, 0.3);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30 dark:from-gray-900 dark:via-indigo-950/20 dark:to-purple-950/20">

    <!-- Elegant Dashboard Header -->
    <div class="dashboard-hero rounded-2xl p-8 mb-8 text-white relative overflow-hidden shadow-2xl">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Dashboard</h1>
                            <p class="text-white/80">Welcome back, {{ auth()->user()->name }}!</p>
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
                        <p class="text-xs text-white/70">Total Students</p>
                        <p class="text-lg font-bold">{{ number_format($stats['students']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Filter (Admin & Coordinator only) -->
    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
    <div class="mb-6 glass-card rounded-2xl shadow-xl p-5">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter Groups:
                </span>
                <a href="{{ route('dashboard', ['group_filter' => 'all']) }}"
                   class="filter-pill px-4 py-2 text-sm rounded-xl font-semibold transition-all {{ (!isset($groupFilter) || $groupFilter === 'all') ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    All Groups
                </a>
                <a href="{{ route('dashboard', ['group_filter' => 'active']) }}"
                   class="filter-pill px-4 py-2 text-sm rounded-xl font-semibold transition-all {{ (isset($groupFilter) && $groupFilter === 'active') ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Active Only
                </a>
                <a href="{{ route('dashboard', ['group_filter' => 'completed']) }}"
                   class="filter-pill px-4 py-2 text-sm rounded-xl font-semibold transition-all {{ (isset($groupFilter) && $groupFilter === 'completed') ? 'bg-gradient-to-r from-gray-500 to-slate-600 text-white active' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Completed Only
                </a>
            </div>
            @if(isset($groupFilter) && $groupFilter !== 'all')
                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                    Showing {{ $groupFilter === 'active' ? 'Active' : 'Completed' }} groups only
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- KPI Stats Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Students -->
        <div class="elegant-card glass-card rounded-2xl shadow-xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Students</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['students']) }}</p>
                    <div class="mt-3 flex items-center gap-2">
                        @if($changes['students']['type'] === 'increase')
                            <span class="inline-flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                {{ $changes['students']['value'] }}
                            </span>
                        @elseif($changes['students']['type'] === 'decrease')
                            <span class="inline-flex items-center gap-1 text-sm text-red-600 dark:text-red-400 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                                {{ $changes['students']['value'] }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl kpi-gradient-1 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Groups -->
        <div class="elegant-card glass-card rounded-2xl shadow-xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Groups</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['groups']) }}</p>
                    @if(isset($stats['active_groups']))
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $stats['active_groups'] }} Active</span> •
                        <span class="text-gray-500">{{ $stats['completed_groups'] }} Completed</span>
                    </p>
                    @endif
                </div>
                <div class="w-14 h-14 rounded-2xl kpi-gradient-2 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Companies -->
        <div class="elegant-card glass-card rounded-2xl shadow-xl p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Companies</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['companies']) }}</p>
                    <div class="mt-3 flex items-center gap-2">
                        @if($changes['companies']['type'] === 'increase')
                            <span class="inline-flex items-center gap-1 text-sm text-emerald-600 dark:text-emerald-400 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                {{ $changes['companies']['value'] }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl kpi-gradient-3 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Students by Program Chart Card -->
        <div class="lg:col-span-2 chart-card glass-card rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Students by Program</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Distribution across programs</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="studentsProgramChart"></canvas>
            </div>
        </div>

        <!-- Donut Chart Card -->
        <div class="chart-card glass-card rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Students by Company</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Top placements</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="studentsDonutChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Program and Group Chart Card -->
        <div class="chart-card glass-card rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Students by Program and Group</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Grouped distribution</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="studentsProgramGroupChart"></canvas>
            </div>
        </div>

        <!-- Bar Chart Card -->
        <div class="chart-card glass-card rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Students by Group</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Group-wise breakdown</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="studentsBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Elegant Color Palette
    const CHART_COLORS = {
        primary: '#667eea',
        secondary: '#764ba2',
        tertiary: '#11998e',
        quaternary: '#f093fb',
        quinary: '#4facfe',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444'
    };

    // Detect dark mode
    const isDarkMode = document.documentElement.classList.contains('dark');

    // Chart colors based on theme
    const chartColors = {
        text: isDarkMode ? '#9CA3AF' : '#6B7280',
        grid: isDarkMode ? '#374151' : '#E5E7EB',
        background: isDarkMode ? '#1F2937' : '#FFFFFF'
    };

    // Bar Chart - Students by Program
    const programCtx = document.getElementById('studentsProgramChart');
    if (programCtx) {
        new Chart(programCtx, {
            type: 'bar',
            data: {
                labels: @json($programChartData['labels']),
                datasets: [{
                    label: 'Students',
                    data: @json($programChartData['data']),
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(17, 153, 142, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(250, 112, 154, 0.8)'
                    ],
                    borderRadius: 12,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : '#667eea',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        borderColor: isDarkMode ? '#374151' : '#764ba2',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartColors.grid },
                        ticks: {
                            color: chartColors.text,
                            font: { size: 12 },
                            stepSize: 1,
                            precision: 0
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: chartColors.text,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }

    // Grouped Bar Chart - Students by Program and Group
    const programGroupCtx = document.getElementById('studentsProgramGroupChart');
    if (programGroupCtx) {
        new Chart(programGroupCtx, {
            type: 'bar',
            data: {
                labels: @json($programGroupChartData['labels']),
                datasets: @json($programGroupChartData['datasets'])
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            color: chartColors.text,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : '#667eea',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartColors.grid },
                        ticks: {
                            color: chartColors.text,
                            font: { size: 12 },
                            stepSize: 1,
                            precision: 0
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: chartColors.text,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }

    // Bar Chart - Students by Group
    const barCtx = document.getElementById('studentsBarChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($barChartData['labels']),
                datasets: [{
                    label: 'Students',
                    data: @json($barChartData['data']),
                    backgroundColor: [
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(17, 153, 142, 0.8)',
                        'rgba(250, 112, 154, 0.8)'
                    ],
                    borderRadius: 12,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : '#667eea',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartColors.grid },
                        ticks: { color: chartColors.text, font: { size: 12 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: chartColors.text, font: { size: 12 } }
                    }
                }
            }
        });
    }

    // Donut Chart - Students by Company
    const donutCtx = document.getElementById('studentsDonutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: @json($donutChartData['labels']),
                datasets: [{
                    data: @json($donutChartData['data']),
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.9)',
                        'rgba(118, 75, 162, 0.9)',
                        'rgba(17, 153, 142, 0.9)',
                        'rgba(240, 147, 251, 0.9)',
                        'rgba(79, 172, 254, 0.9)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            color: chartColors.text,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : '#667eea',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                cutout: '65%'
            }
        });
    }
</script>
@endsection
