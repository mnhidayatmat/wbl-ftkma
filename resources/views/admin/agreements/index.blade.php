@extends('layouts.app')

@section('title', 'Company Agreements')

@push('styles')
<style>
    /* Elegant Gradient Header */
    .agreements-hero {
        background: linear-gradient(135deg, #312e81 0%, #4338ca 25%, #6366f1 50%, #818cf8 75%, #312e81 100%);
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

    /* Stat Card Gradients */
    .stat-gradient-mou { background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); }
    .stat-gradient-moa { background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%); }
    .stat-gradient-loi { background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); }
    .stat-gradient-active { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
    .stat-gradient-expired { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }
    .stat-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
    .stat-gradient-alert { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }

    /* Icon Float Animation */
    @keyframes iconFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }

    .icon-float {
        animation: iconFloat 3s ease-in-out infinite;
    }

    /* Card Hover Effect */
    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    /* Table Row Gradient Hover */
    .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
    }

    .dark .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.15) 100%);
    }

    /* Filter Section Styling */
    .filter-input {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .filter-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Button Gradient */
    .btn-gradient {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
    }

    /* Pulse Animation for Alerts */
    @keyframes alertPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .alert-icon-pulse {
        animation: alertPulse 2s ease-in-out infinite;
    }

    /* Shimmer effect for expiring badge */
    @keyframes shimmer {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }

    .shimmer-badge {
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
        background-size: 200% 100%;
        animation: shimmer 2s ease-in-out infinite;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-slate-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Elegant Header Section -->
    <div class="agreements-hero relative overflow-hidden">
        <div class="wave-pattern absolute inset-0"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-white">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm icon-float">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Company Agreements</h1>
                            <p class="text-indigo-100 mt-1">Manage MoU, MoA, and LOI records</p>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->isAdmin())
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.agreements.import') }}"
                       class="px-4 py-2.5 bg-emerald-500/80 hover:bg-emerald-600/80 text-white font-semibold rounded-xl transition-all duration-300 backdrop-blur-sm inline-flex items-center gap-2 border border-emerald-400/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import Excel
                    </a>
                    <a href="{{ route('admin.agreements.create') }}"
                       class="px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl transition-all duration-300 backdrop-blur-sm inline-flex items-center gap-2 border border-white/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Agreement
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 pb-8">
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

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
            <div class="stat-card stat-gradient-mou rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold">{{ $stats['total_mou_active'] }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Active MoU</div>
                </div>
            </div>
            <div class="stat-card stat-gradient-moa rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold">{{ $stats['total_moa_active'] }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Active MoA</div>
                </div>
            </div>
            <div class="stat-card stat-gradient-loi rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold">{{ $stats['total_loi_active'] }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Active LOI</div>
                </div>
            </div>
            <div class="stat-card stat-gradient-active rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold">{{ $stats['total_active'] }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Total Active</div>
                </div>
            </div>
            <div class="stat-card stat-gradient-warning rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold alert-icon-pulse">{{ $stats['total_near_expiry'] ?? 0 }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Near Expiry</div>
                </div>
            </div>
            <div class="stat-card stat-gradient-expired rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold">{{ $stats['total_expired'] }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Expired</div>
                </div>
            </div>
            <div class="stat-card stat-gradient-alert rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold">{{ $stats['expiring_3_months'] }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Expiring 3mo</div>
                </div>
            </div>
            <div class="stat-card bg-gradient-to-r from-slate-500 to-slate-600 rounded-xl shadow-lg p-4 text-white text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative">
                    <div class="text-3xl font-bold">{{ $stats['expiring_6_months'] }}</div>
                    <div class="text-xs text-white/80 mt-1 font-medium">Expiring 6mo</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card rounded-2xl shadow-xl p-5 mb-6">
            <form action="{{ route('admin.agreements.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search title, ref no, company..."
                           class="filter-input w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                </div>
                <select name="type" class="filter-input w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    <option value="MoU" {{ request('type') == 'MoU' ? 'selected' : '' }}>MoU</option>
                    <option value="MoA" {{ request('type') == 'MoA' ? 'selected' : '' }}>MoA</option>
                    <option value="LOI" {{ request('type') == 'LOI' ? 'selected' : '' }}>LOI</option>
                </select>
                <select name="status" class="filter-input w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="Not Started" {{ request('status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Near Expiry" {{ request('status') == 'Near Expiry' ? 'selected' : '' }}>Near Expiry</option>
                    <option value="Expired" {{ request('status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Terminated" {{ request('status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
                <select name="expiring" class="filter-input w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white">
                    <option value="">Expiring Filter</option>
                    <option value="3" {{ request('expiring') == '3' ? 'selected' : '' }}>Expiring in 3 months</option>
                    <option value="6" {{ request('expiring') == '6' ? 'selected' : '' }}>Expiring in 6 months</option>
                    <option value="12" {{ request('expiring') == '12' ? 'selected' : '' }}>Expiring in 12 months</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 btn-gradient text-white font-semibold rounded-xl transition-all">
                        Filter
                    </button>
                    <a href="{{ route('admin.agreements.index') }}" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Agreements Table -->
        <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Company</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Title / Ref No</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Period</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($agreements as $agreement)
                        <tr class="table-row-hover transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1.5 text-xs font-bold rounded-xl shadow-sm
                                    {{ $agreement->agreement_type == 'MoU' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : '' }}
                                    {{ $agreement->agreement_type == 'MoA' ? 'bg-gradient-to-r from-purple-500 to-fuchsia-500 text-white' : '' }}
                                    {{ $agreement->agreement_type == 'LOI' ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white' : '' }}">
                                    {{ $agreement->agreement_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $agreement->company->company_name ?? 'N/A' }}
                                </div>
                                @if($agreement->faculty || $agreement->programme)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $agreement->faculty }} {{ $agreement->programme ? '/ ' . $agreement->programme : '' }}
                                </div>
                                @endif
                                @if($agreement->staff_pic_name)
                                <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $agreement->staff_pic_name }}</span>
                                    @if($agreement->staff_pic_phone)
                                    <span class="ml-1">({{ $agreement->staff_pic_phone }})</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $agreement->agreement_title ?: 'No title' }}
                                </div>
                                @if($agreement->reference_no)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 inline-flex items-center gap-1">
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-md">Ref: {{ $agreement->reference_no }}</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $agreement->start_date ? $agreement->start_date->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    to {{ $agreement->end_date ? $agreement->end_date->format('d/m/Y') : '-' }}
                                </div>
                                @if($agreement->isExpiringSoon())
                                <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 rounded-lg">
                                    <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400">{{ $agreement->days_until_expiry }} days left</span>
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1.5 text-xs font-bold rounded-xl shadow-sm
                                    {{ $agreement->status == 'Active' ? 'bg-gradient-to-r from-green-500 to-emerald-500 text-white' : '' }}
                                    {{ $agreement->status == 'Near Expiry' ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white animate-pulse' : '' }}
                                    {{ $agreement->status == 'Expired' ? 'bg-gradient-to-r from-red-500 to-rose-500 text-white' : '' }}
                                    {{ $agreement->status == 'Terminated' ? 'bg-gradient-to-r from-gray-600 to-gray-700 text-white' : '' }}
                                    {{ $agreement->status == 'Pending' ? 'bg-gradient-to-r from-yellow-500 to-amber-500 text-white' : '' }}
                                    {{ $agreement->status == 'Draft' ? 'bg-gradient-to-r from-blue-400 to-cyan-400 text-white' : '' }}
                                    {{ $agreement->status == 'Not Started' ? 'bg-gradient-to-r from-slate-400 to-gray-400 text-white' : '' }}">
                                    {{ $agreement->status }}
                                </span>
                                @if($agreement->status == 'Near Expiry' && $agreement->days_until_expiry !== null)
                                <div class="text-xs text-orange-600 dark:text-orange-400 mt-1 font-medium">
                                    {{ $agreement->days_until_expiry }} days left
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($agreement->document_path)
                                    <a href="{{ Storage::url($agreement->document_path) }}"
                                       target="_blank"
                                       class="p-2.5 text-green-600 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 dark:hover:from-green-900/30 dark:hover:to-emerald-900/30 rounded-xl transition-all"
                                       title="View PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.agreements.edit', $agreement) }}"
                                       class="p-2.5 text-indigo-600 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/30 dark:hover:to-purple-900/30 rounded-xl transition-all"
                                       title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.agreements.destroy', $agreement) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this agreement?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2.5 text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 dark:hover:from-red-900/30 dark:hover:to-rose-900/30 rounded-xl transition-all"
                                                title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-2xl mb-4">
                                        <svg class="w-16 h-16 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-bold text-gray-700 dark:text-gray-300">No agreements found</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                        @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.agreements.create') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-semibold hover:underline">Add your first agreement</a>
                                        or
                                        <a href="{{ route('admin.agreements.import') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-semibold hover:underline">import from Excel</a>
                                        @else
                                        No agreements have been recorded yet.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($agreements->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gradient-to-r from-slate-50 to-gray-50 dark:from-gray-800 dark:to-gray-800">
                {{ $agreements->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
