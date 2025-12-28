@extends('layouts.app')

@section('title', 'Companies & Agreements')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Compact Header -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Companies & Agreements</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unified management of industry partners and agreements</p>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="flex gap-2">
                <a href="{{ route('admin.companies.create') }}"
                   class="px-3 py-2 text-sm bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Company & Agreement
                </a>
                <a href="{{ route('admin.companies.import.form') }}"
                   class="px-3 py-2 text-sm bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Import Excel
                </a>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-3 py-2 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-3 py-2 rounded-lg text-sm">
            {{ session('error') }}
        </div>
        @endif

        <!-- Enhanced Statistics Cards with Gradients and Icons -->
        @if(isset($stats))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Companies Card -->
            <div class="bg-gradient-to-br from-[#003A6C] to-[#0084C5] rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-blue-100 mb-1">Total Companies</div>
                        <div class="text-3xl font-bold">{{ $stats['total_companies'] ?? 0 }}</div>
                        <div class="text-xs text-blue-100 mt-2">{{ $stats['with_active_agreements'] ?? 0 }} with active agreements</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Agreements Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-green-100 mb-1">Active Agreements</div>
                        <div class="text-3xl font-bold">{{ $stats['active_agreements'] ?? 0 }}</div>
                        <div class="text-xs text-green-100 mt-2">
                            <span class="bg-white bg-opacity-20 px-2 py-0.5 rounded-full">MoU: {{ $stats['mou_count'] ?? 0 }}</span>
                            <span class="bg-white bg-opacity-20 px-2 py-0.5 rounded-full ml-1">MoA: {{ $stats['moa_count'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Expiring Soon Card -->
            <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-amber-100 mb-1">Expiring Soon</div>
                        <div class="text-3xl font-bold">{{ $stats['expiring_3_months'] ?? 0 }}</div>
                        <div class="text-xs text-amber-100 mt-2">Within 3 months | {{ $stats['expiring_6_months'] ?? 0 }} in 6mo</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Placed Students Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-purple-100 mb-1">Placed Students</div>
                        <div class="text-3xl font-bold">{{ $stats['total_students'] ?? 0 }}</div>
                        <div class="text-xs text-purple-100 mt-2">Assigned to companies</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                            </svg>
                            Agreement Distribution
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">By type and status</p>
                    </div>
                </div>
                <div class="relative" style="height: 280px;">
                    <canvas id="agreementTypeChart"></canvas>
                </div>
                <!-- Agreement Summary -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-3 gap-3">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400">MoU</div>
                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $stats['mou_count'] ?? 0 }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400">MoA</div>
                        <div class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $stats['moa_count'] ?? 0 }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400">LoI</div>
                        <div class="text-lg font-bold text-orange-600 dark:text-orange-400">{{ $stats['loi_count'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Category Distribution Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            Industry Categories
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Company distribution by industry</p>
                    </div>
                </div>
                <div class="relative" style="height: 280px;">
                    <canvas id="categoryChart"></canvas>
                </div>
                <!-- Category Stats Summary -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-3">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Total Categories</div>
                        <div class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5]">{{ count($stats['category_distribution'] ?? []) }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400">No Agreement</div>
                        <div class="text-lg font-bold text-gray-600 dark:text-gray-400">{{ $stats['companies_without_agreements'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attention Required Section -->
        @if(($stats['expiring_soon'] ?? collect())->count() > 0 || ($stats['pending_agreements'] ?? 0) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 mb-6" x-data="{ isMinimized: false }">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-2">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attention Required</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Agreements expiring soon or pending action</p>
                    </div>
                </div>
                <button @click="isMinimized = !isMinimized" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': isMinimized }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
            <div x-show="!isMinimized" x-collapse class="p-4">
                @if(($stats['expiring_soon'] ?? collect())->count() > 0)
                {{-- Layout with expiring agreements: 2 columns --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Expiring Soon List -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Expiring Within 3 Months
                        </h4>
                        @foreach($stats['expiring_soon'] as $agreement)
                        <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $agreement->company->company_name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium
                                        @if($agreement->agreement_type === 'MoU') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($agreement->agreement_type === 'MoA') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                        @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @endif">
                                        {{ $agreement->agreement_type }}
                                    </span>
                                    <span class="ml-2">Expires: {{ $agreement->end_date?->format('d M Y') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.companies.show', $agreement->company_id) }}" class="text-[#0084C5] hover:text-[#003A6C] text-sm font-medium">View</a>
                        </div>
                        @endforeach
                    </div>

                    <!-- Quick Stats Summary (2x2 grid when expiring list exists) -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Status Summary</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 text-center">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Pending</div>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 text-center">
                                <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['draft_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Draft</div>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 text-center">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['expired_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Expired</div>
                            </div>
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['companies_without_agreements'] ?? 0 }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">No Agreement</div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                {{-- Layout without expiring agreements: Full width 4 columns --}}
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Status Summary</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800 text-center transform hover:scale-105 transition-transform duration-200">
                            <div class="flex items-center justify-center mb-2">
                                <div class="bg-yellow-100 dark:bg-yellow-800/50 rounded-full p-2">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_agreements'] ?? 0 }}</div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Pending</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 text-center transform hover:scale-105 transition-transform duration-200">
                            <div class="flex items-center justify-center mb-2">
                                <div class="bg-gray-200 dark:bg-gray-600 rounded-full p-2">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['draft_agreements'] ?? 0 }}</div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Draft</div>
                        </div>
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800 text-center transform hover:scale-105 transition-transform duration-200">
                            <div class="flex items-center justify-center mb-2">
                                <div class="bg-red-100 dark:bg-red-800/50 rounded-full p-2">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['expired_agreements'] ?? 0 }}</div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Expired</div>
                        </div>
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 text-center transform hover:scale-105 transition-transform duration-200">
                            <div class="flex items-center justify-center mb-2">
                                <div class="bg-blue-100 dark:bg-blue-800/50 rounded-full p-2">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1h-2a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['companies_without_agreements'] ?? 0 }}</div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">No Agreement</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endif

        <!-- Compact Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 mb-4 border border-gray-200 dark:border-gray-700">
            <form action="{{ route('admin.companies.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                <select name="agreement_type" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Agreement Types</option>
                    <option value="MoU" {{ request('agreement_type') == 'MoU' ? 'selected' : '' }}>With MoU</option>
                    <option value="MoA" {{ request('agreement_type') == 'MoA' ? 'selected' : '' }}>With MoA</option>
                    <option value="LOI" {{ request('agreement_type') == 'LOI' ? 'selected' : '' }}>With LOI</option>
                    <option value="none" {{ request('agreement_type') == 'none' ? 'selected' : '' }}>No Agreements</option>
                </select>
                <select name="agreement_status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('agreement_status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Pending" {{ request('agreement_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Draft" {{ request('agreement_status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Expired" {{ request('agreement_status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Terminated" {{ request('agreement_status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
                <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    <option value="Oil and Gas" {{ request('category') == 'Oil and Gas' ? 'selected' : '' }}>Oil and Gas</option>
                    <option value="Design" {{ request('category') == 'Design' ? 'selected' : '' }}>Design</option>
                    <option value="Automotive" {{ request('category') == 'Automotive' ? 'selected' : '' }}>Automotive</option>
                    <option value="IT" {{ request('category') == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="Manufacturing" {{ request('category') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                    <option value="Construction" {{ request('category') == 'Construction' ? 'selected' : '' }}>Construction</option>
                    <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <select name="expiring" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">Expiring Filter</option>
                    <option value="3" {{ request('expiring') == '3' ? 'selected' : '' }}>3 months</option>
                    <option value="6" {{ request('expiring') == '6' ? 'selected' : '' }}>6 months</option>
                    <option value="12" {{ request('expiring') == '12' ? 'selected' : '' }}>12 months</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-sm bg-[#0084C5] text-white rounded-lg hover:bg-[#003A6C] transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.companies.index') }}" class="px-3 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Unified Companies & Agreements Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Company</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Contact Info</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Students</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Agreements</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Documents</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($companies as $company)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-3 py-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $company->company_name }}</div>
                                @if($company->category)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->category }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $company->pic_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->email }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->phone }}</div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full bg-[#00AEEF]/10 text-[#00AEEF]">
                                    {{ $company->students_count }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                @php
                                    $mou = $company->agreements->where('agreement_type', 'MoU')->where('status', 'Active')->first();
                                    $moa = $company->agreements->where('agreement_type', 'MoA')->where('status', 'Active')->first();
                                    $loi = $company->agreements->where('agreement_type', 'LOI')->where('status', 'Active')->first();
                                    $hasAgreements = $mou || $moa || $loi;
                                @endphp

                                @if($hasAgreements)
                                    <div class="flex flex-wrap gap-1">
                                        @if($mou)
                                            <div class="group relative">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    MoU
                                                    @if($mou->isExpiringSoon())
                                                    <span class="text-yellow-600">⚠</span>
                                                    @endif
                                                </span>
                                                @if($mou->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-2 py-1 text-xs bg-gray-900 text-white rounded shadow-lg -top-8 left-0 whitespace-nowrap">
                                                    Expires: {{ $mou->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($moa)
                                            <div class="group relative">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    MoA
                                                    @if($moa->isExpiringSoon())
                                                    <span class="text-yellow-600">⚠</span>
                                                    @endif
                                                </span>
                                                @if($moa->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-2 py-1 text-xs bg-gray-900 text-white rounded shadow-lg -top-8 left-0 whitespace-nowrap">
                                                    Expires: {{ $moa->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($loi)
                                            <div class="group relative">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                    LOI
                                                    @if($loi->isExpiringSoon())
                                                    <span class="text-yellow-600">⚠</span>
                                                    @endif
                                                </span>
                                                @if($loi->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-2 py-1 text-xs bg-gray-900 text-white rounded shadow-lg -top-8 left-0 whitespace-nowrap">
                                                    Expires: {{ $loi->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        No Active Agreements
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @php
                                    // Count agreements by status (priority order: Active > Pending > Draft > Expired > Terminated)
                                    $hasActive = $company->agreements->where('status', 'Active')->count() > 0;
                                    $hasPending = $company->agreements->where('status', 'Pending')->count() > 0;
                                    $hasDraft = $company->agreements->where('status', 'Draft')->count() > 0;
                                    $hasExpired = $company->agreements->where('status', 'Expired')->count() > 0;
                                    $hasTerminated = $company->agreements->where('status', 'Terminated')->count() > 0;
                                @endphp
                                @if($hasActive)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                @elseif($hasPending)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Pending
                                    </span>
                                @elseif($hasDraft)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Draft
                                    </span>
                                @elseif($hasExpired)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Expired
                                    </span>
                                @elseif($hasTerminated)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-700 text-white dark:bg-gray-600 dark:text-gray-100">
                                        Terminated
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        None
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @php
                                    $agreementsWithDocs = $company->agreements->filter(fn($a) => $a->document_path);
                                @endphp
                                @if($agreementsWithDocs->count() > 0)
                                    <div class="flex items-center justify-center gap-1">
                                        @foreach($agreementsWithDocs->take(3) as $agreement)
                                        <a href="{{ Storage::url($agreement->document_path) }}"
                                           target="_blank"
                                           title="{{ $agreement->agreement_type }} - {{ $agreement->agreement_title }}"
                                           class="p-1.5 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/30 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                        @endforeach
                                        @if($agreementsWithDocs->count() > 3)
                                        <span class="text-xs text-gray-500">+{{ $agreementsWithDocs->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.companies.show', $company) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#0084C5] hover:text-white hover:bg-[#0084C5] dark:text-[#00AEEF] dark:hover:bg-[#0084C5] dark:hover:text-white rounded-lg transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 dark:text-red-400 dark:hover:bg-red-600 dark:hover:text-white rounded-lg transition-all duration-200"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <td colspan="7" class="px-3 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No companies found</p>
                                    @if(request()->hasAny(['search', 'agreement_type', 'agreement_status', 'category', 'expiring']))
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filters</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($companies->hasPages())
            <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 text-sm">
                {{ $companies->withQueryString()->links() }}
            </div>
            @endif
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
                        'rgba(59, 130, 246, 0.8)',  // Blue for MoU
                        'rgba(147, 51, 234, 0.8)',  // Purple for MoA
                        'rgba(249, 115, 22, 0.8)'   // Orange for LoI
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(147, 51, 234, 1)',
                        'rgba(249, 115, 22, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 10
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
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 58, 108, 0.9)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 8,
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

    // Category Distribution Chart (Horizontal Bar)
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const categoryData = @json($stats['category_distribution'] ?? []);
        const labels = categoryData.map(item => item.name || 'Uncategorized');
        const data = categoryData.map(item => item.count);

        // Generate colors for categories
        const colors = [
            'rgba(0, 58, 108, 0.8)',   // UMPSA Primary
            'rgba(0, 132, 197, 0.8)',  // UMPSA Secondary
            'rgba(0, 174, 239, 0.8)',  // UMPSA Accent
            'rgba(34, 197, 94, 0.8)',  // Green
            'rgba(249, 115, 22, 0.8)', // Orange
            'rgba(147, 51, 234, 0.8)', // Purple
            'rgba(236, 72, 153, 0.8)', // Pink
            'rgba(107, 114, 128, 0.8)' // Gray
        ];

        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Companies',
                    data: data,
                    backgroundColor: labels.map((_, i) => colors[i % colors.length]),
                    borderColor: labels.map((_, i) => colors[i % colors.length].replace('0.8', '1')),
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 24
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 58, 108, 0.9)',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            precision: 0,
                            font: { size: 11 }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 },
                            callback: function(value, index) {
                                const label = this.getLabelForValue(value);
                                return label.length > 15 ? label.substring(0, 15) + '...' : label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
