@extends('layouts.app')

@section('title', 'Student Placement Tracking')

@section('content')
<div x-data="{
    showFilters: true,
    liveSearch: '',
    selectedStatuses: [],
    showExportMenu: false
}">
    <!-- Page Header with Actions -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Student Placement Tracking</h1>
            <p class="text-gray-600 dark:text-gray-400">
                Manage and track student placement before and after WBL hiring.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3">
            <button @click="showFilters = !showFilters"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
            </button>

            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
            <!-- Export Menu -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50"
                     style="display: none;">
                    <a href="{{ route('placement.index', array_merge(request()->all(), ['export' => 'excel'])) }}"
                       class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors rounded-t-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                                <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                            </svg>
                            Export to Excel
                        </div>
                    </a>
                    <a href="{{ route('placement.index', array_merge(request()->all(), ['export' => 'csv'])) }}"
                       class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            Export to CSV
                        </div>
                    </a>
                    <a href="{{ route('placement.index', array_merge(request()->all(), ['export' => 'pdf'])) }}"
                       class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors rounded-b-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            Analytics Report (PDF)
                        </div>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Enhanced Statistics Cards with Gradients and Icons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-4 mb-6">
        <!-- Total Students Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-sm font-medium text-blue-100 mb-1">Total Students</div>
                    <div class="text-3xl font-bold">{{ $stats['total'] }}</div>
                    <div class="text-xs text-blue-100 mt-2">All students tracked</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Resume Recommended Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-sm font-medium text-green-100 mb-1">Resume Recommended</div>
                    <div class="text-3xl font-bold">{{ $stats['resume_recommended'] }}</div>
                    <div class="text-xs text-green-100 mt-2">{{ $stats['total'] > 0 ? round(($stats['resume_recommended'] / $stats['total']) * 100, 1) : 0 }}% completion</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- SAL Released Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-sm font-medium text-purple-100 mb-1">SAL Released</div>
                    <div class="text-3xl font-bold">{{ $stats['sal_released'] }}</div>
                    <div class="text-xs text-purple-100 mt-2">
                        @if($stats['pending_sal'] > 0)
                            <span class="bg-white bg-opacity-20 px-2 py-0.5 rounded-full">{{ $stats['pending_sal'] }} pending</span>
                        @else
                            All processed
                        @endif
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Placement Success Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-5 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-sm font-medium text-orange-100 mb-1">Placement Success</div>
                    <div class="text-3xl font-bold">{{ $stats['accepted'] }}</div>
                    <div class="text-xs text-orange-100 mt-2">{{ $stats['total'] > 0 ? round(($stats['accepted'] / $stats['total']) * 100, 1) : 0 }}% success rate</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Placement Funnel Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
                        </svg>
                        Placement Funnel
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Student progression through placement stages</p>
                </div>
            </div>
            <div class="relative" style="height: 350px;">
                <canvas id="funnelChart"></canvas>
            </div>
            <!-- Funnel Stats Summary -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-3">
                <div class="text-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Conversion Rate</div>
                    <div class="text-lg font-bold text-green-600 dark:text-green-400">
                        {{ $stats['total'] > 0 ? round(($stats['accepted'] / $stats['total']) * 100, 1) : 0 }}%
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Drop-off Rate</div>
                    <div class="text-lg font-bold text-red-600 dark:text-red-400">
                        {{ $stats['total'] > 0 ? round((($stats['total'] - $stats['accepted']) / $stats['total']) * 100, 1) : 0 }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Trend Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        SAL Release Timeline
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SAL releases over the last 30 days</p>
                </div>
            </div>
            <div class="relative" style="height: 350px;">
                <canvas id="timelineChart"></canvas>
            </div>
            <!-- Timeline Stats Summary -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-3 gap-3">
                <div class="text-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Released</div>
                    <div class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $stats['sal_released'] }}</div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Pending</div>
                    <div class="text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_sal'] }}</div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Last 30 Days</div>
                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $timelineData->sum('count') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Comparison Chart (Full Width) - ENHANCED -->
    @if($groupStats->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 mb-6" x-data="{ viewMode: 'chart' }">
        <!-- Header with View Toggle -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                        Group Performance Comparison
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Analyze placement progress and success rates across all groups</p>
                </div>

                <!-- View Mode Toggle -->
                <div class="flex gap-2 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
                    <button @click="viewMode = 'chart'"
                            :class="viewMode === 'chart' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all flex items-center gap-2"
                            :class="viewMode === 'chart' ? 'text-[#0084C5]' : 'text-gray-600 dark:text-gray-300'">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                        Chart View
                    </button>
                    <button @click="viewMode = 'table'"
                            :class="viewMode === 'table' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all flex items-center gap-2"
                            :class="viewMode === 'table' ? 'text-[#0084C5]' : 'text-gray-600 dark:text-gray-300'">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z" clip-rule="evenodd"/>
                        </svg>
                        Table View
                    </button>
                </div>
            </div>
        </div>

        <!-- Chart View -->
        <div x-show="viewMode === 'chart'" class="p-6">
            <div class="relative" style="height: 400px;">
                <canvas id="groupChart"></canvas>
            </div>

            <!-- Chart Legend with Statistics -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-1">Total Students</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $groupStats->sum('total') }}</div>
                </div>
                <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="text-xs font-medium text-purple-600 dark:text-purple-400 mb-1">SAL Released</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $groupStats->sum('sal_released') }}</div>
                </div>
                <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="text-xs font-medium text-yellow-600 dark:text-yellow-400 mb-1">Applied</div>
                    <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $groupStats->sum('applied') }}</div>
                </div>
                <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">Accepted</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $groupStats->sum('accepted') }}</div>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div x-show="viewMode === 'table'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gradient-to-r from-[#003A6C] to-[#0084C5]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Group</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Total Students</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Resume OK</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">SAL Released</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Applied</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Accepted</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Success Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($groupStats as $group)
                        @php
                            $successRate = $group['total'] > 0 ? round(($group['accepted'] / $group['total']) * 100, 1) : 0;
                            $salRate = $group['total'] > 0 ? round(($group['sal_released'] / $group['total']) * 100, 1) : 0;
                            $resumeRate = $group['total'] > 0 ? round(($group['resume_ok'] / $group['total']) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $group['name'] }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $group['total'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $group['resume_ok'] }}</span>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 max-w-[80px]">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $resumeRate }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $resumeRate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $group['sal_released'] }}</span>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 max-w-[80px]">
                                        <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ $salRate }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $salRate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $group['applied'] }}</span>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 max-w-[80px]">
                                        <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" style="width: {{ $group['total'] > 0 ? round(($group['applied'] / $group['total']) * 100, 1) : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ $group['total'] > 0 ? round(($group['applied'] / $group['total']) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $group['accepted'] }}</span>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 max-w-[80px]">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $successRate }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $successRate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex flex-col items-center gap-1">
                                    <span class="text-2xl font-black {{ $successRate >= 75 ? 'text-green-600' : ($successRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $successRate }}%
                                    </span>
                                    @if($successRate >= 75)
                                        <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs font-semibold rounded-full">Excellent</span>
                                    @elseif($successRate >= 50)
                                        <span class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 text-xs font-semibold rounded-full">Good</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 text-xs font-semibold rounded-full">Needs Attention</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <!-- Totals Row -->
                    <tr class="bg-gray-50 dark:bg-gray-900 font-bold border-t-2 border-gray-300 dark:border-gray-600">
                        <td class="px-6 py-4 text-gray-900 dark:text-white">TOTAL</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ $groupStats->sum('total') }}</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ $groupStats->sum('resume_ok') }}</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ $groupStats->sum('sal_released') }}</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ $groupStats->sum('applied') }}</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{ $groupStats->sum('accepted') }}</td>
                        <td class="px-6 py-4 text-center text-gray-900 dark:text-white">
                            {{ $groupStats->sum('total') > 0 ? round(($groupStats->sum('accepted') / $groupStats->sum('total')) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Enhanced Filters Section -->
    <div x-show="showFilters"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Filters</h3>
            <a href="{{ route('placement.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-[#0084C5] dark:hover:text-[#00AEEF] transition-colors">
                Clear All Filters
            </a>
        </div>

        <form method="GET" action="{{ route('placement.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Group Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                        Group
                    </div>
                </label>
                <select name="group" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white transition-all">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }} {{ $group->isCompleted() ? '(Completed)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Group Status Filter (Admin & Coordinator only) -->
            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Group Status
                    </div>
                </label>
                <select name="group_status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white transition-all">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('group_status') == 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="completed" {{ request('group_status') == 'completed' ? 'selected' : '' }}>Completed Only</option>
                </select>
            </div>
            @endif

            <!-- Resume Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                        Resume Status
                    </div>
                </label>
                <select name="resume_status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white transition-all">
                    <option value="">All Statuses</option>
                    <option value="NOT_STARTED" {{ request('resume_status') == 'NOT_STARTED' ? 'selected' : '' }}>Not Started</option>
                    <option value="PENDING" {{ request('resume_status') == 'PENDING' ? 'selected' : '' }}>Pending Review</option>
                    <option value="RECOMMENDED" {{ request('resume_status') == 'RECOMMENDED' ? 'selected' : '' }}>Resume Recommended</option>
                    <option value="REVISION_REQUIRED" {{ request('resume_status') == 'REVISION_REQUIRED' ? 'selected' : '' }}>Revision Required</option>
                </select>
            </div>

            <!-- Placement Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Placement Status
                    </div>
                </label>
                <select name="placement_status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white transition-all">
                    <option value="">All Statuses</option>
                    <option value="NOT_APPLIED" {{ request('placement_status') == 'NOT_APPLIED' ? 'selected' : '' }}>Not Applied</option>
                    <option value="SAL_RELEASED" {{ request('placement_status') == 'SAL_RELEASED' ? 'selected' : '' }}>SAL Released</option>
                    <option value="APPLIED" {{ request('placement_status') == 'APPLIED' ? 'selected' : '' }}>Applied</option>
                    <option value="INTERVIEWED" {{ request('placement_status') == 'INTERVIEWED' ? 'selected' : '' }}>Interviewed</option>
                    <option value="OFFER_RECEIVED" {{ request('placement_status') == 'OFFER_RECEIVED' ? 'selected' : '' }}>Offer Received</option>
                    <option value="ACCEPTED" {{ request('placement_status') == 'ACCEPTED' ? 'selected' : '' }}>Accepted</option>
                    <option value="CONFIRMED" {{ request('placement_status') == 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                    <option value="SCL_RELEASED" {{ request('placement_status') == 'SCL_RELEASED' ? 'selected' : '' }}>SCL Released</option>
                </select>
            </div>

            <!-- Search with Live Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        Search
                    </div>
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       x-model="liveSearch"
                       placeholder="Name or Matric No"
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white transition-all">
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-2 lg:col-span-4 flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-all transform hover:scale-105 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ route('placement.index') }}" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition-all transform hover:scale-105">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">
                Students ({{ $students->count() }})
            </h3>
            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Bulk Actions:</span>
                <button onclick="selectAll()" class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium">
                    Select All
                </button>
                <button onclick="deselectAll()" class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium">
                    Deselect All
                </button>
            </div>
            @endif
        </div>

        <!-- Desktop/Tablet Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gradient-to-r from-[#003A6C] to-[#0084C5]">
                    <tr>
                        @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleAll(this)" class="rounded border-gray-300">
                        </th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Resume Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Interview</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Company</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">SAL</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $student)
                        @php
                            $resumeInspection = $student->resumeInspection;
                            $tracking = $student->placementTracking;
                            $isInCompletedGroup = $student->group && $student->group->isCompleted();

                            // Resume status
                            $resumeStatus = 'Not Started';
                            $resumeStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            if ($resumeInspection) {
                                if (empty($resumeInspection->resume_file_path)) {
                                    $resumeStatus = 'Not Started';
                                    $resumeStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                } elseif ($resumeInspection->status === 'PENDING') {
                                    $resumeStatus = 'Submitted';
                                    $resumeStatusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                } elseif ($resumeInspection->status === 'PASSED') {
                                    $resumeStatus = 'Recommended';
                                    $resumeStatusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                } elseif ($resumeInspection->status === 'REVISION_REQUIRED') {
                                    $resumeStatus = 'Revision';
                                    $resumeStatusColor = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
                                } elseif ($resumeInspection->status === 'FAILED') {
                                    $resumeStatus = 'Rejected';
                                    $resumeStatusColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                }
                            }

                            // Interview status
                            $interviewedCompanies = $tracking ? $tracking->companyApplications->filter(fn($app) => $app->interviewed === true) : collect();
                            $hasInterviewed = $interviewedCompanies->count() > 0;

                            // SAL Status
                            $salStatus = 'Not Released';
                            $salStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            if ($tracking && $tracking->sal_file_path) {
                                $salStatus = 'Released';
                                $salStatusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                            } elseif ($resumeInspection && $resumeInspection->status === 'PASSED' && (!$tracking || $tracking->status === 'NOT_APPLIED')) {
                                $salStatus = 'Pending';
                                $salStatusColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                            }

                            $canReleaseSal = $resumeInspection && $resumeInspection->status === 'PASSED' && $tracking && $tracking->status === 'NOT_APPLIED';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $isInCompletedGroup ? 'opacity-60' : '' }}">
                            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                            <td class="px-6 py-4">
                                <input type="checkbox" class="student-checkbox rounded border-gray-300" value="{{ $student->id }}">
                            </td>
                            @endif

                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->matric_no }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($student->group)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $student->group->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $resumeStatusColor }}">
                                    {{ $resumeStatus }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($hasInterviewed)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $interviewedCompanies->count() }} Interview{{ $interviewedCompanies->count() > 1 ? 's' : '' }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        None
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($student->company)
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->company->company_name }}</span>
                                @else
                                    <span class="text-gray-400">–</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tracking)
                                    @php
                                        $statusMap = [
                                            'NOT_APPLIED' => ['label' => 'Not Applied', 'color' => 'bg-gray-100 text-gray-800'],
                                            'SAL_RELEASED' => ['label' => 'SAL Released', 'color' => 'bg-blue-100 text-blue-800'],
                                            'APPLIED' => ['label' => 'Applied', 'color' => 'bg-indigo-100 text-indigo-800'],
                                            'INTERVIEWED' => ['label' => 'Interviewed', 'color' => 'bg-purple-100 text-purple-800'],
                                            'OFFER_RECEIVED' => ['label' => 'Offer Received', 'color' => 'bg-yellow-100 text-yellow-800'],
                                            'ACCEPTED' => ['label' => 'Accepted', 'color' => 'bg-green-100 text-green-800'],
                                            'CONFIRMED' => ['label' => 'Confirmed', 'color' => 'bg-green-100 text-green-800'],
                                            'SCL_RELEASED' => ['label' => 'SCL Released', 'color' => 'bg-green-100 text-green-800'],
                                        ];
                                        $statusInfo = $statusMap[$tracking->status] ?? ['label' => $tracking->status, 'color' => 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusInfo['color'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $salStatusColor }}">
                                        {{ $salStatus }}
                                    </span>
                                    @if($tracking && $tracking->sal_released_at)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $tracking->sal_released_at->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                                        <a href="{{ route('placement.student.view', $student) }}"
                                           class="px-3 py-1.5 bg-[#0084C5] hover:bg-[#003A6C] text-white text-xs font-semibold rounded-lg transition-colors">
                                            View
                                        </a>
                                    @endif

                                    @if(!$isInCompletedGroup && (auth()->user()->isAdmin() || auth()->user()->isCoordinator()))
                                        @if($canReleaseSal)
                                            <form action="{{ route('placement.student.sal.release', $student) }}" method="POST" class="inline" onsubmit="return confirm('Release SAL for {{ $student->name }}?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                    Release SAL
                                                </button>
                                            </form>
                                        @endif

                                        @if($tracking && $tracking->sal_file_path)
                                            <a href="{{ route('placement.student.sal.download', $student) }}"
                                               class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-lg font-medium">No students found matching your filters.</p>
                                <p class="text-sm text-gray-400 mt-2">Try adjusting your filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (continues from existing mobile view) -->
        <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($students as $student)
                @php
                    $resumeInspection = $student->resumeInspection;
                    $tracking = $student->placementTracking;
                    $isInCompletedGroup = $student->group && $student->group->isCompleted();

                    $resumeStatus = 'Not Started';
                    $resumeStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                    if ($resumeInspection) {
                        if (empty($resumeInspection->resume_file_path)) {
                            $resumeStatus = 'Not Started';
                        } elseif ($resumeInspection->status === 'PENDING') {
                            $resumeStatus = 'Submitted';
                            $resumeStatusColor = 'bg-blue-100 text-blue-800';
                        } elseif ($resumeInspection->status === 'PASSED') {
                            $resumeStatus = 'Recommended';
                            $resumeStatusColor = 'bg-green-100 text-green-800';
                        }
                    }

                    $canReleaseSal = $resumeInspection && $resumeInspection->status === 'PASSED' && $tracking && $tracking->status === 'NOT_APPLIED';
                @endphp
                <div class="p-4 {{ $isInCompletedGroup ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $student->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->matric_no }}</div>
                        </div>
                        @if($student->group)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $student->group->name }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Resume:</span>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $resumeStatusColor }}">{{ $resumeStatus }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Company:</span>
                            <span class="text-sm font-medium">{{ $student->company ? $student->company->company_name : '–' }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                            <a href="{{ route('placement.student.view', $student) }}"
                               class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors text-center">
                                View Details
                            </a>
                        @endif

                        @if(!$isInCompletedGroup && $canReleaseSal && (auth()->user()->isAdmin() || auth()->user()->isCoordinator()))
                            <form action="{{ route('placement.student.sal.release', $student) }}" method="POST" onsubmit="return confirm('Release SAL for {{ $student->name }}?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    Release SAL
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    No students found matching your filters.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Bulk Actions Panel (Admin & Coordinator only) -->
    @if((auth()->user()->isAdmin() || auth()->user()->isCoordinator()) && $stats['pending_sal'] > 0)
    <div class="mt-6 bg-gradient-to-r from-green-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-green-500 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bulk SAL Release</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Release SAL for all students with "Resume Recommended" status.
                    </p>
                    <p class="text-xs font-semibold text-green-600 dark:text-green-400 mt-1">
                        {{ $stats['pending_sal'] }} student(s) eligible
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('placement.bulk.sal.release') }}" method="POST" onsubmit="return confirm('Release SAL for all eligible students? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Release All SAL
                    </button>
                </form>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.documents.sal') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-all flex items-center gap-2" title="Configure SAL Template">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    SAL Template
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js Library -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
    // Dark mode detection for charts
    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#E5E7EB' : '#374151';
    const gridColor = isDarkMode ? '#374151' : '#E5E7EB';
    const tooltipBg = isDarkMode ? '#1F2937' : '#FFFFFF';

    // Placement Funnel Chart - Enhanced Horizontal Bar with Funnel Effect
    const funnelCtx = document.getElementById('funnelChart').getContext('2d');
    const funnelData = [
        {{ $funnelData['resume_recommended'] }},
        {{ $funnelData['sal_released'] }},
        {{ $funnelData['applied'] }},
        {{ $funnelData['interviewed'] }},
        {{ $funnelData['offer_received'] }},
        {{ $funnelData['accepted'] }},
        {{ $funnelData['scl_released'] }}
    ];
    const totalStudents = {{ $stats['total'] }};

    // Register datalabels plugin only for this chart
    Chart.register(ChartDataLabels);

    const funnelChart = new Chart(funnelCtx, {
        type: 'bar',
        data: {
            labels: ['Resume OK', 'SAL Released', 'Applied', 'Interviewed', 'Offer', 'Accepted', 'SCL'],
            datasets: [{
                label: 'Students',
                data: funnelData,
                backgroundColor: function(context) {
                    const gradient = context.chart.ctx.createLinearGradient(0, 0, 400, 0);
                    const colors = [
                        ['rgba(34, 197, 94, 0.9)', 'rgba(34, 197, 94, 0.6)'],
                        ['rgba(168, 85, 247, 0.9)', 'rgba(168, 85, 247, 0.6)'],
                        ['rgba(59, 130, 246, 0.9)', 'rgba(59, 130, 246, 0.6)'],
                        ['rgba(99, 102, 241, 0.9)', 'rgba(99, 102, 241, 0.6)'],
                        ['rgba(234, 179, 8, 0.9)', 'rgba(234, 179, 8, 0.6)'],
                        ['rgba(249, 115, 22, 0.9)', 'rgba(249, 115, 22, 0.6)'],
                        ['rgba(16, 185, 129, 0.9)', 'rgba(16, 185, 129, 0.6)']
                    ];
                    const index = context.dataIndex;
                    if (index !== undefined) {
                        gradient.addColorStop(0, colors[index][0]);
                        gradient.addColorStop(1, colors[index][1]);
                    }
                    return gradient;
                },
                borderColor: [
                    'rgb(34, 197, 94)',
                    'rgb(168, 85, 247)',
                    'rgb(59, 130, 246)',
                    'rgb(99, 102, 241)',
                    'rgb(234, 179, 8)',
                    'rgb(249, 115, 22)',
                    'rgb(16, 185, 129)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 'flex',
                maxBarThickness: 40
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: textColor,
                    bodyColor: textColor,
                    borderColor: gridColor,
                    borderWidth: 1,
                    padding: 15,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const value = context.parsed.x;
                            const percentage = totalStudents > 0 ? ((value / totalStudents) * 100).toFixed(1) : 0;
                            return `${value} students (${percentage}% of total)`;
                        },
                        afterLabel: function(context) {
                            const index = context.dataIndex;
                            if (index > 0) {
                                const previousValue = funnelData[index - 1];
                                const currentValue = funnelData[index];
                                const dropOff = previousValue - currentValue;
                                const dropOffPercentage = previousValue > 0 ? ((dropOff / previousValue) * 100).toFixed(1) : 0;
                                if (dropOff > 0) {
                                    return `Drop-off: ${dropOff} students (${dropOffPercentage}%)`;
                                }
                            }
                            return '';
                        }
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    color: textColor,
                    font: {
                        weight: 'bold',
                        size: 11
                    },
                    formatter: function(value, context) {
                        const percentage = totalStudents > 0 ? ((value / totalStudents) * 100).toFixed(0) : 0;
                        return `${value} (${percentage}%)`;
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        color: textColor,
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: gridColor,
                        borderDash: [5, 5]
                    }
                },
                y: {
                    ticks: {
                        color: textColor,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Timeline Chart - Enhanced with Dual Dataset (Daily & Cumulative)
    const timelineCtx = document.getElementById('timelineChart').getContext('2d');
    const timelineLabels = {!! json_encode($timelineData->pluck('date')) !!};
    const timelineCounts = {!! json_encode($timelineData->pluck('count')) !!};

    // Calculate cumulative totals
    let cumulativeData = [];
    let cumulative = 0;
    timelineCounts.forEach(count => {
        cumulative += count;
        cumulativeData.push(cumulative);
    });

    // Create gradient for area fill
    const timelineGradient = timelineCtx.createLinearGradient(0, 0, 0, 350);
    timelineGradient.addColorStop(0, 'rgba(168, 85, 247, 0.4)');
    timelineGradient.addColorStop(0.5, 'rgba(168, 85, 247, 0.2)');
    timelineGradient.addColorStop(1, 'rgba(168, 85, 247, 0.0)');

    const cumulativeGradient = timelineCtx.createLinearGradient(0, 0, 0, 350);
    cumulativeGradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
    cumulativeGradient.addColorStop(0.5, 'rgba(59, 130, 246, 0.15)');
    cumulativeGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: timelineLabels,
            datasets: [
                {
                    label: 'Daily Releases',
                    data: timelineCounts,
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: timelineGradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(168, 85, 247)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'rgb(168, 85, 247)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                    yAxisID: 'y'
                },
                {
                    label: 'Cumulative Total',
                    data: cumulativeData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: cumulativeGradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: 'rgb(59, 130, 246)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                datalabels: {
                    display: false
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: textColor,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: textColor,
                    bodyColor: textColor,
                    borderColor: gridColor,
                    borderWidth: 1,
                    padding: 15,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            if (context.datasetIndex === 0) {
                                return `${label}: ${value} ${value === 1 ? 'release' : 'releases'}`;
                            } else {
                                return `${label}: ${value} total`;
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Daily Releases',
                        color: textColor,
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    },
                    ticks: {
                        color: textColor,
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: gridColor,
                        borderDash: [3, 3]
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cumulative Total',
                        color: textColor,
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    },
                    ticks: {
                        color: textColor,
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    ticks: {
                        color: textColor,
                        maxRotation: 45,
                        minRotation: 45,
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Group Comparison Chart - ENHANCED with Better Visualization
    @if($groupStats->count() > 0)
    const groupCtx = document.getElementById('groupChart').getContext('2d');

    // Prepare data with percentages
    const groupData = {!! json_encode($groupStats) !!};
    const groupLabels = groupData.map(g => g.name);
    const totalData = groupData.map(g => g.total);
    const resumeOkData = groupData.map(g => g.resume_ok);
    const salReleasedData = groupData.map(g => g.sal_released);
    const appliedData = groupData.map(g => g.applied);
    const acceptedData = groupData.map(g => g.accepted);

    // Calculate success rates for each group
    const successRates = groupData.map(g => g.total > 0 ? ((g.accepted / g.total) * 100).toFixed(1) : 0);

    new Chart(groupCtx, {
        type: 'bar',
        data: {
            labels: groupLabels,
            datasets: [
                {
                    label: 'Total Students',
                    data: totalData,
                    backgroundColor: function(context) {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.9)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.6)');
                        return gradient;
                    },
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    borderRadius: 8,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                },
                {
                    label: 'Resume OK',
                    data: resumeOkData,
                    backgroundColor: function(context) {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.6)');
                        return gradient;
                    },
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 2,
                    borderRadius: 8,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                },
                {
                    label: 'SAL Released',
                    data: salReleasedData,
                    backgroundColor: function(context) {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(168, 85, 247, 0.9)');
                        gradient.addColorStop(1, 'rgba(168, 85, 247, 0.6)');
                        return gradient;
                    },
                    borderColor: 'rgb(168, 85, 247)',
                    borderWidth: 2,
                    borderRadius: 8,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                },
                {
                    label: 'Applied',
                    data: appliedData,
                    backgroundColor: function(context) {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(234, 179, 8, 0.9)');
                        gradient.addColorStop(1, 'rgba(234, 179, 8, 0.6)');
                        return gradient;
                    },
                    borderColor: 'rgb(234, 179, 8)',
                    borderWidth: 2,
                    borderRadius: 8,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                },
                {
                    label: 'Accepted ✓',
                    data: acceptedData,
                    backgroundColor: function(context) {
                        const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(34, 197, 94, 0.95)');
                        gradient.addColorStop(1, 'rgba(34, 197, 94, 0.7)');
                        return gradient;
                    },
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 3,
                    borderRadius: 8,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                datalabels: {
                    display: false
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: textColor,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        boxWidth: 12,
                        boxHeight: 12
                    }
                },
                tooltip: {
                    backgroundColor: tooltipBg,
                    titleColor: textColor,
                    bodyColor: textColor,
                    borderColor: gridColor,
                    borderWidth: 1,
                    padding: 15,
                    displayColors: true,
                    boxPadding: 6,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            const groupIndex = context.dataIndex;
                            const total = totalData[groupIndex];

                            let percentage = 0;
                            if (total > 0 && context.datasetIndex > 0) {
                                percentage = ((value / total) * 100).toFixed(1);
                            }

                            if (context.datasetIndex === 0) {
                                return `${label}: ${value} students`;
                            } else {
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        },
                        afterBody: function(context) {
                            const groupIndex = context[0].dataIndex;
                            const successRate = successRates[groupIndex];
                            return `\nSuccess Rate: ${successRate}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: textColor,
                        stepSize: 1,
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        callback: function(value) {
                            return value % 1 === 0 ? value : '';
                        }
                    },
                    grid: {
                        color: gridColor,
                        borderDash: [3, 3],
                        drawBorder: false
                    },
                    title: {
                        display: true,
                        text: 'Number of Students',
                        color: textColor,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        padding: {
                            top: 10
                        }
                    }
                },
                x: {
                    ticks: {
                        color: textColor,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        maxRotation: 0,
                        minRotation: 0
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
    @endif

    // Bulk selection functions
    function toggleAll(checkbox) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
    }

    function selectAll() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = true);
        document.getElementById('selectAllCheckbox').checked = true;
    }

    function deselectAll() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('selectAllCheckbox').checked = false;
    }
</script>
@endpush
@endsection
