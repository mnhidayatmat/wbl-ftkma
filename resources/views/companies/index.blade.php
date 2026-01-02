@extends('layouts.app')

@section('title', 'Companies & Agreements')

@push('styles')
<style>
    /* Elegant Gradient Header */
    .companies-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 25%, #3b82a0 50%, #4a9eb8 75%, #1e3a5f 100%);
        background-size: 400% 400%;
        animation: elegantGradient 20s ease infinite;
    }

    @keyframes elegantGradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Wave Pattern Overlay */
    .wave-pattern {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
        background-size: cover;
        background-position: bottom;
    }

    /* Glass Card Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .glass-card {
        background: rgba(31, 41, 55, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* KPI Card Gradients */
    .kpi-gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-gradient-2 { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .kpi-gradient-3 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .kpi-gradient-4 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .kpi-gradient-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-gradient-6 { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); }

    /* Icon Float Animation */
    @keyframes iconFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }

    .icon-float {
        animation: iconFloat 3s ease-in-out infinite;
    }

    /* Card Hover Effect */
    .kpi-card {
        transition: all 0.3s ease;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* Table Row Gradient Hover */
    .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }

    .dark .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    }

    /* Badge Shine Animation */
    @keyframes badgeShine {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }

    .badge-shine {
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
        background-size: 200% 100%;
        animation: badgeShine 3s ease-in-out infinite;
    }

    /* Chart Container Styling */
    .chart-container {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
    }

    .chart-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    }

    /* Filter Section Styling */
    .filter-input {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .filter-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Button Gradient */
    .btn-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    /* Alert Panel Glass Effect */
    .alert-glass {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, rgba(245, 158, 11, 0.1) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    /* Pulse Animation for Alerts */
    @keyframes alertPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .alert-icon-pulse {
        animation: alertPulse 2s ease-in-out infinite;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Elegant Header Section -->
    <div class="companies-hero relative overflow-hidden">
        <div class="wave-pattern absolute inset-0"></div>
        <div class="relative max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-white">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm icon-float">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Companies & Agreements</h1>
                            <p class="text-blue-100 mt-1">Unified management of industry partners and agreements</p>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->isAdmin() || auth()->user()->isWblCoordinator())
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.companies.create') }}"
                       class="px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl transition-all duration-300 backdrop-blur-sm inline-flex items-center gap-2 border border-white/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Company
                    </a>
                    <a href="{{ route('admin.companies.import.form') }}"
                       class="px-4 py-2.5 bg-purple-500/80 hover:bg-purple-600/80 text-white font-semibold rounded-xl transition-all duration-300 backdrop-blur-sm inline-flex items-center gap-2 border border-purple-400/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import
                    </a>
                    <!-- Export Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                                class="px-4 py-2.5 bg-emerald-500/80 hover:bg-emerald-600/80 text-white font-semibold rounded-xl transition-all duration-300 backdrop-blur-sm inline-flex items-center gap-2 border border-emerald-400/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-52 glass-card rounded-xl shadow-2xl z-50 overflow-hidden">
                            <a href="{{ route('admin.companies.export.excel', request()->query()) }}"
                               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 dark:hover:from-green-900/20 dark:hover:to-emerald-900/20 transition-colors">
                                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9.5 11.5l2 2.5-2 2.5 1 1 3-3.5-3-3.5-1 1zm5 0l-2 2.5 2 2.5-1 1-3-3.5 3-3.5 1 1z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">Export Excel</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">.xlsx format</div>
                                </div>
                            </a>
                            <a href="{{ route('admin.companies.export.pdf', request()->query()) }}"
                               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 dark:hover:from-red-900/20 dark:hover:to-rose-900/20 transition-colors border-t border-gray-100 dark:border-gray-700">
                                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1v4h-1v-4zm2.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1v-4zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h2v1h-1v.5h1v1h-1v1.5h-1v-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">Export PDF</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">.pdf format</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 mt-6 pb-8">
        @if(session('success'))
        <div class="mb-4 glass-card bg-green-50/90 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-800/50 rounded-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 glass-card bg-red-50/90 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <div class="p-2 bg-red-100 dark:bg-red-800/50 rounded-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            {{ session('error') }}
        </div>
        @endif

        <!-- KPI Statistics Cards -->
        @if(isset($stats))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Companies Card -->
            <div class="kpi-card kpi-gradient-1 rounded-2xl shadow-xl p-5 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-white/80 mb-1">Total Companies</div>
                        <div class="text-4xl font-bold">{{ $stats['total_companies'] ?? 0 }}</div>
                        <div class="text-xs text-white/70 mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-white/20 text-white">
                                {{ $stats['with_active_agreements'] ?? 0 }} active
                            </span>
                        </div>
                    </div>
                    <div class="bg-white/20 rounded-2xl p-4 icon-float">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Agreements Card -->
            <div class="kpi-card kpi-gradient-2 rounded-2xl shadow-xl p-5 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-white/80 mb-1">Active Agreements</div>
                        <div class="text-4xl font-bold">{{ $stats['active_agreements'] ?? 0 }}</div>
                        <div class="text-xs text-white/70 mt-2 flex flex-wrap gap-1">
                            <span class="px-2 py-0.5 rounded-full bg-white/20">MoU: {{ $stats['mou_count'] ?? 0 }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-white/20">MoA: {{ $stats['moa_count'] ?? 0 }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-white/20">LOI: {{ $stats['loi_count'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="bg-white/20 rounded-2xl p-4 icon-float">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Expiring Soon Card -->
            <div class="kpi-card kpi-gradient-5 rounded-2xl shadow-xl p-5 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-white/80 mb-1">Expiring Soon</div>
                        <div class="text-4xl font-bold">{{ $stats['expiring_3_months'] ?? 0 }}</div>
                        <div class="text-xs text-white/70 mt-2">Within 3 months | {{ $stats['expiring_6_months'] ?? 0 }} in 6mo</div>
                    </div>
                    <div class="bg-white/20 rounded-2xl p-4 icon-float alert-icon-pulse">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Placed Students Card -->
            <div class="kpi-card kpi-gradient-6 rounded-2xl shadow-xl p-5 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-white/80 mb-1">Placed Students</div>
                        <div class="text-4xl font-bold">{{ $stats['total_students'] ?? 0 }}</div>
                        <div class="text-xs text-white/70 mt-2">Assigned to companies</div>
                    </div>
                    <div class="bg-white/20 rounded-2xl p-4 icon-float">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Agreement Type Distribution Chart -->
            <div class="glass-card chart-container rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <div class="p-2 kpi-gradient-1 rounded-lg text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                                </svg>
                            </div>
                            Agreement Distribution
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-11">By type and status</p>
                    </div>
                </div>
                <div class="relative" style="height: 280px;">
                    <canvas id="agreementTypeChart"></canvas>
                </div>
                <!-- Agreement Summary -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400">MoU</div>
                        <div class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['mou_count'] ?? 0 }}</div>
                    </div>
                    <div class="text-center p-3 bg-gradient-to-br from-purple-50 to-fuchsia-50 dark:from-purple-900/20 dark:to-fuchsia-900/20 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400">MoA</div>
                        <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['moa_count'] ?? 0 }}</div>
                    </div>
                    <div class="text-center p-3 bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400">LoI</div>
                        <div class="text-xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['loi_count'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Category Distribution Chart -->
            <div class="glass-card chart-container rounded-2xl shadow-xl p-6 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <div class="p-2 kpi-gradient-2 rounded-lg text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                            Industry Categories
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-11">Company distribution by industry</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ $stats['total_companies'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                    <!-- Doughnut Chart -->
                    <div class="relative flex items-center justify-center" style="height: 280px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <!-- Category Legend -->
                    <div class="flex flex-col justify-center">
                        <div id="categoryLegend" class="space-y-2 max-h-[280px] overflow-y-auto pr-2">
                            <!-- Legend items will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
                <!-- Category Stats Summary -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-gradient-to-br from-slate-50 to-gray-50 dark:from-slate-900/20 dark:to-gray-900/20 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Categories</div>
                        <div class="text-xl font-bold text-gray-700 dark:text-gray-300">{{ count($stats['category_distribution'] ?? []) }}</div>
                    </div>
                    <div class="text-center p-3 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400">With Agreement</div>
                        <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ $stats['with_active_agreements'] ?? 0 }}</div>
                    </div>
                    <div class="text-center p-3 bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-900/20 dark:to-slate-900/20 rounded-xl">
                        <div class="text-xs text-gray-500 dark:text-gray-400">No Agreement</div>
                        <div class="text-xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['companies_without_agreements'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attention Required Section -->
        @if(($stats['expiring_soon'] ?? collect())->count() > 0 || ($stats['pending_agreements'] ?? 0) > 0)
        <div class="glass-card rounded-2xl shadow-xl mb-6 overflow-hidden" x-data="{ isMinimized: false }">
            <div class="p-4 bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-b border-amber-200/50 dark:border-amber-800/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl text-white alert-icon-pulse">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Attention Required</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Agreements expiring soon or pending action</p>
                    </div>
                </div>
                <button @click="isMinimized = !isMinimized" class="p-2 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-amber-600 transition-transform" :class="{ 'rotate-180': isMinimized }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            <div x-show="!isMinimized" x-collapse class="p-5">
                @if(($stats['expiring_soon'] ?? collect())->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Expiring Soon List -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-bold text-amber-600 dark:text-amber-400 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Expiring Within 3 Months
                        </h4>
                        @foreach($stats['expiring_soon'] as $agreement)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl border border-amber-200/50 dark:border-amber-800/50 hover:shadow-md transition-shadow">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $agreement->company->company_name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($agreement->agreement_type === 'MoU') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200
                                        @elseif($agreement->agreement_type === 'MoA') bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200
                                        @else bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-200 @endif">
                                        {{ $agreement->agreement_type }}
                                    </span>
                                    <span>Expires: {{ $agreement->end_date?->format('d M Y') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.companies.show', $agreement->company_id) }}" class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-medium rounded-lg transition-all">View</a>
                        </div>
                        @endforeach
                    </div>

                    <!-- Quick Stats Summary -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-bold text-gray-600 dark:text-gray-400">Status Summary</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-xl border border-yellow-200/50 dark:border-yellow-800/50 text-center hover:shadow-md transition-shadow">
                                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Pending</div>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-800/50 dark:to-slate-800/50 rounded-xl border border-gray-200/50 dark:border-gray-700/50 text-center hover:shadow-md transition-shadow">
                                <div class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['draft_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Draft</div>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-xl border border-red-200/50 dark:border-red-800/50 text-center hover:shadow-md transition-shadow">
                                <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['expired_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Expired</div>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-slate-50 to-gray-50 dark:from-slate-800/50 dark:to-gray-800/50 rounded-xl border border-slate-200/50 dark:border-slate-700/50 text-center hover:shadow-md transition-shadow">
                                <div class="text-3xl font-bold text-slate-600 dark:text-slate-400">{{ $stats['not_started_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Not Started</div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="space-y-2">
                    <h4 class="text-sm font-bold text-gray-600 dark:text-gray-400">Status Summary</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="px-4 py-3 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-xl border border-yellow-200/50 dark:border-yellow-800/50 flex items-center gap-3 hover:shadow-md transition-shadow">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-800/50 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_agreements'] ?? 0 }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">Pending</span>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-800/50 dark:to-slate-800/50 rounded-xl border border-gray-200/50 dark:border-gray-700/50 flex items-center gap-3 hover:shadow-md transition-shadow">
                            <div class="p-2 bg-gray-200 dark:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['draft_agreements'] ?? 0 }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">Draft</span>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-xl border border-red-200/50 dark:border-red-800/50 flex items-center gap-3 hover:shadow-md transition-shadow">
                            <div class="p-2 bg-red-100 dark:bg-red-800/50 rounded-lg">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['expired_agreements'] ?? 0 }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">Expired</span>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gradient-to-br from-slate-50 to-gray-50 dark:from-slate-800/50 dark:to-gray-800/50 rounded-xl border border-slate-200/50 dark:border-slate-700/50 flex items-center gap-3 hover:shadow-md transition-shadow">
                            <div class="p-2 bg-slate-200 dark:bg-slate-700 rounded-lg">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-2xl font-bold text-slate-600 dark:text-slate-400">{{ $stats['not_started_agreements'] ?? 0 }}</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">Not Started</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endif

        <!-- Filters Section -->
        <div class="glass-card rounded-2xl shadow-xl p-5 mb-6">
            <form action="{{ route('admin.companies.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies..."
                           class="filter-input w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                </div>
                <select name="agreement_type" class="filter-input w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                    <option value="">All Agreement Types</option>
                    <option value="MoU" {{ request('agreement_type') == 'MoU' ? 'selected' : '' }}>With MoU</option>
                    <option value="MoA" {{ request('agreement_type') == 'MoA' ? 'selected' : '' }}>With MoA</option>
                    <option value="LOI" {{ request('agreement_type') == 'LOI' ? 'selected' : '' }}>With LOI</option>
                    <option value="none" {{ request('agreement_type') == 'none' ? 'selected' : '' }}>No Agreements</option>
                </select>
                <select name="agreement_status" class="filter-input w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="Not Started" {{ request('agreement_status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                    <option value="Draft" {{ request('agreement_status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Pending" {{ request('agreement_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Active" {{ request('agreement_status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Expired" {{ request('agreement_status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Terminated" {{ request('agreement_status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
                <select name="category" class="filter-input w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    <option value="Oil and Gas" {{ request('category') == 'Oil and Gas' ? 'selected' : '' }}>Oil and Gas</option>
                    <option value="Design" {{ request('category') == 'Design' ? 'selected' : '' }}>Design</option>
                    <option value="Automotive" {{ request('category') == 'Automotive' ? 'selected' : '' }}>Automotive</option>
                    <option value="IT" {{ request('category') == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="Manufacturing" {{ request('category') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                    <option value="Construction" {{ request('category') == 'Construction' ? 'selected' : '' }}>Construction</option>
                    <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <select name="expiring" class="filter-input w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                    <option value="">Expiring Filter</option>
                    <option value="3" {{ request('expiring') == '3' ? 'selected' : '' }}>3 months</option>
                    <option value="6" {{ request('expiring') == '6' ? 'selected' : '' }}>6 months</option>
                    <option value="12" {{ request('expiring') == '12' ? 'selected' : '' }}>12 months</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 btn-gradient text-white font-semibold rounded-xl transition-all">
                        Filter
                    </button>
                    <a href="{{ route('admin.companies.index') }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Companies Table -->
        <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Company</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Industry Type</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Contact Info</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Students</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Agreements</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Status</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Documents</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($companies as $company)
                        <tr class="table-row-hover transition-all duration-200">
                            <td class="px-4 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $company->company_name }}</div>
                            </td>
                            <td class="px-4 py-4">
                                @if($company->category)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gradient-to-r from-slate-100 to-gray-100 dark:from-slate-800 dark:to-gray-800 text-gray-700 dark:text-gray-300">
                                    {{ $company->category }}
                                </span>
                                @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->pic_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->email }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->phone }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-bold rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-sm">
                                    {{ $company->students_count }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $mou = $company->agreements->where('agreement_type', 'MoU')->where('status', 'Active')->first();
                                    $moa = $company->agreements->where('agreement_type', 'MoA')->where('status', 'Active')->first();
                                    $loi = $company->agreements->where('agreement_type', 'LOI')->where('status', 'Active')->first();
                                    $hasAgreements = $mou || $moa || $loi;
                                @endphp

                                @if($hasAgreements)
                                    <div class="flex flex-wrap gap-1.5">
                                        @if($mou)
                                            <div class="group relative">
                                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-sm inline-flex items-center gap-1">
                                                    MoU
                                                    @if($mou->isExpiringSoon())
                                                    <span class="w-2 h-2 bg-yellow-300 rounded-full animate-pulse"></span>
                                                    @endif
                                                </span>
                                                @if($mou->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-3 py-2 text-xs bg-gray-900 text-white rounded-lg shadow-xl -top-10 left-0 whitespace-nowrap">
                                                    Expires: {{ $mou->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($moa)
                                            <div class="group relative">
                                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-purple-500 to-fuchsia-500 text-white shadow-sm inline-flex items-center gap-1">
                                                    MoA
                                                    @if($moa->isExpiringSoon())
                                                    <span class="w-2 h-2 bg-yellow-300 rounded-full animate-pulse"></span>
                                                    @endif
                                                </span>
                                                @if($moa->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-3 py-2 text-xs bg-gray-900 text-white rounded-lg shadow-xl -top-10 left-0 whitespace-nowrap">
                                                    Expires: {{ $moa->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($loi)
                                            <div class="group relative">
                                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-sm inline-flex items-center gap-1">
                                                    LOI
                                                    @if($loi->isExpiringSoon())
                                                    <span class="w-2 h-2 bg-yellow-300 rounded-full animate-pulse"></span>
                                                    @endif
                                                </span>
                                                @if($loi->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-3 py-2 text-xs bg-gray-900 text-white rounded-lg shadow-xl -top-10 left-0 whitespace-nowrap">
                                                    Expires: {{ $loi->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        No Active Agreements
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @php
                                    $hasActive = $company->agreements->where('status', 'Active')->count() > 0;
                                    $hasPending = $company->agreements->where('status', 'Pending')->count() > 0;
                                    $hasDraft = $company->agreements->where('status', 'Draft')->count() > 0;
                                    $hasNotStarted = $company->agreements->where('status', 'Not Started')->count() > 0;
                                    $hasExpired = $company->agreements->where('status', 'Expired')->count() > 0;
                                    $hasTerminated = $company->agreements->where('status', 'Terminated')->count() > 0;
                                @endphp
                                @if($hasActive)
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-sm">
                                        Active
                                    </span>
                                @elseif($hasPending)
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-yellow-500 to-amber-500 text-white shadow-sm">
                                        Pending
                                    </span>
                                @elseif($hasDraft)
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-blue-400 to-cyan-400 text-white shadow-sm">
                                        Draft
                                    </span>
                                @elseif($hasNotStarted)
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-slate-400 to-gray-400 text-white shadow-sm">
                                        Not Started
                                    </span>
                                @elseif($hasExpired)
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-sm">
                                        Expired
                                    </span>
                                @elseif($hasTerminated)
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 text-white shadow-sm">
                                        Terminated
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 text-xs font-medium rounded-xl bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        None
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @php
                                    $agreementsWithDocs = $company->agreements->filter(fn($a) => $a->document_path);
                                @endphp
                                @if($agreementsWithDocs->count() > 0)
                                    <div class="flex items-center justify-center gap-1">
                                        @foreach($agreementsWithDocs->take(3) as $agreement)
                                        <a href="{{ Storage::url($agreement->document_path) }}"
                                           target="_blank"
                                           title="{{ $agreement->agreement_type }} - {{ $agreement->agreement_title }}"
                                           class="p-2 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                        @endforeach
                                        @if($agreementsWithDocs->count() > 3)
                                        <span class="text-xs font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-lg">+{{ $agreementsWithDocs->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.companies.show', $company) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-indigo-600 hover:text-white hover:bg-gradient-to-r hover:from-indigo-500 hover:to-purple-500 dark:text-indigo-400 rounded-xl transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this company? This will also delete all related agreements, contacts, notes, and documents.')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-600 hover:text-white hover:bg-gradient-to-r hover:from-red-500 hover:to-rose-500 dark:text-red-400 rounded-xl transition-all duration-200"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-2xl mb-4">
                                        <svg class="w-16 h-16 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-bold text-gray-700 dark:text-gray-300">No companies found</p>
                                    @if(request()->hasAny(['search', 'agreement_type', 'agreement_status', 'category', 'expiring']))
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Try adjusting your filters</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-4 border-t border-gray-100 dark:border-gray-700 bg-gradient-to-r from-slate-50 to-gray-50 dark:from-gray-800 dark:to-gray-800">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    {{-- Per Page Selector --}}
                    <div class="flex items-center gap-3">
                        <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">Show:</span>
                        @php
                            $currentPerPage = request('per_page', 15);
                            $perPageOptions = [15, 25, 50, 100, 'all'];
                        @endphp
                        <div class="flex items-center gap-1">
                            @foreach($perPageOptions as $option)
                                @php
                                    $isActive = ($currentPerPage == $option) || ($option === 15 && !request('per_page'));
                                    $queryParams = array_merge(request()->except(['per_page', 'page']), ['per_page' => $option]);
                                @endphp
                                <a href="{{ route('admin.companies.index', $queryParams) }}"
                                   class="px-3 py-1.5 text-sm font-semibold rounded-lg transition-all
                                       {{ $isActive
                                           ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-md'
                                           : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600' }}">
                                    {{ $option === 'all' ? 'All' : $option }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Company Count --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                        Showing <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $companies->count() }}</span> of <span class="font-bold">{{ $companies->total() }}</span> {{ Str::plural('company', $companies->total()) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agreement Type Distribution Chart (Doughnut)
    const agreementTypeCtx = document.getElementById('agreementTypeChart');
    if (agreementTypeCtx) {
        new Chart(agreementTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['MoU', 'MoA', 'LoI'],
                datasets: [{
                    data: [
                        {{ $stats['mou_count'] ?? 0 }},
                        {{ $stats['moa_count'] ?? 0 }},
                        {{ $stats['loi_count'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.9)',   // Indigo for MoU
                        'rgba(168, 85, 247, 0.9)',  // Purple for MoA
                        'rgba(249, 115, 22, 0.9)'   // Orange for LoI
                    ],
                    borderColor: [
                        'rgba(99, 102, 241, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(249, 115, 22, 1)'
                    ],
                    borderWidth: 3,
                    hoverOffset: 15
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
                            padding: 25,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 13, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 58, 95, 0.95)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 14,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Category Distribution Chart (Doughnut with Custom Legend)
    const categoryCtx = document.getElementById('categoryChart');
    const categoryLegend = document.getElementById('categoryLegend');
    if (categoryCtx && categoryLegend) {
        const categoryData = @json($stats['category_distribution'] ?? []);

        // Sort by count descending and take top 5, group rest as "Others"
        const sortedData = [...categoryData].sort((a, b) => b.count - a.count);
        const topCategories = sortedData.slice(0, 5);
        const otherCategories = sortedData.slice(5);
        const othersCount = otherCategories.reduce((sum, item) => sum + item.count, 0);

        // Prepare final data
        const finalData = [...topCategories];
        if (othersCount > 0) {
            finalData.push({ name: 'Others', count: othersCount });
        }

        const labels = finalData.map(item => item.name || 'Uncategorized');
        const data = finalData.map(item => item.count);
        const total = data.reduce((a, b) => a + b, 0);

        // Vibrant color palette
        const colors = [
            'rgba(99, 102, 241, 0.9)',   // Indigo
            'rgba(16, 185, 129, 0.9)',   // Emerald
            'rgba(168, 85, 247, 0.9)',   // Purple
            'rgba(249, 115, 22, 0.9)',   // Orange
            'rgba(236, 72, 153, 0.9)',   // Pink
            'rgba(107, 114, 128, 0.9)'   // Gray (for Others)
        ];

        // Create doughnut chart
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30, 58, 95, 0.95)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return ` ${context.raw} companies (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Generate custom legend
        let legendHTML = '';
        finalData.forEach((item, index) => {
            const percentage = total > 0 ? ((item.count / total) * 100).toFixed(1) : 0;
            const colorClass = colors[index] || colors[colors.length - 1];
            legendHTML += `
                <div class="flex items-center justify-between py-2 px-3 rounded-xl hover:bg-gradient-to-r hover:from-slate-50 hover:to-gray-50 dark:hover:from-gray-700 dark:hover:to-gray-700 transition-all">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-lg flex-shrink-0 shadow-sm" style="background-color: ${colorClass}"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate max-w-[120px]" title="${item.name || 'Uncategorized'}">${item.name || 'Uncategorized'}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">${item.count}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">${percentage}%</span>
                    </div>
                </div>
            `;
        });
        categoryLegend.innerHTML = legendHTML;
    }
});
</script>
@endpush
