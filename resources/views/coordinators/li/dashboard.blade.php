@extends('layouts.app')

@section('title', 'Industrial Training Coordinator Dashboard')

@section('content')
@php
    // Calculate summary statistics
    $overallProgress = $stats['total_students'] > 0
        ? round((($assessmentBreakdown['supervisor_completed'] ?? 0) + ($assessmentBreakdown['ic_completed'] ?? 0)) / ($stats['total_students'] * 2) * 100)
        : 0;
    $supervisionRate = $stats['total_students'] > 0
        ? round(($supervisorStatus['with_both'] ?? 0) / $stats['total_students'] * 100)
        : 0;
    $qualityScore = min(100, max(0, 100 - (($atRiskStudents->count() ?? 0) * 5)));
@endphp

<div class="space-y-6">
    <!-- Summary Banner with Glassmorphism -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 p-6 text-white shadow-xl">
        <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>

        <div class="relative">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold">Industrial Training Coordinator Dashboard</h1>
                    </div>
                    <p class="text-white/80">Monitor and manage Learning Integration (LI) module assessments</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('academic.li.reports.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm font-semibold rounded-lg transition-all inline-flex items-center gap-2 border border-white/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Reports
                    </a>
                </div>
            </div>

            <!-- Summary Stats Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="text-white/70 text-sm font-medium">Overall Progress</div>
                    <div class="text-3xl font-bold mt-1">{{ $overallProgress }}%</div>
                    <div class="text-white/60 text-xs mt-1">All assessments</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="text-white/70 text-sm font-medium">Active Students</div>
                    <div class="text-3xl font-bold mt-1">{{ $stats['total_students'] }}</div>
                    <div class="text-white/60 text-xs mt-1">In {{ $stats['total_groups'] }} groups</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="text-white/70 text-sm font-medium">Supervision</div>
                    <div class="text-3xl font-bold mt-1">{{ $supervisionRate }}%</div>
                    <div class="text-white/60 text-xs mt-1">Fully assigned</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="text-white/70 text-sm font-medium">Quality Score</div>
                    <div class="text-3xl font-bold mt-1">{{ $qualityScore }}</div>
                    <div class="text-white/60 text-xs mt-1">Based on at-risk</div>
                </div>
            </div>
        </div>
    </div>

    <!-- At-Risk Alert Banner -->
    @if($atRiskStudents->count() > 0)
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 p-2 bg-amber-100 dark:bg-amber-800/50 rounded-lg">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-amber-800 dark:text-amber-200">Attention Required</h3>
                <p class="text-sm text-amber-700 dark:text-amber-300">{{ $atRiskStudents->count() }} student(s) need immediate attention due to missing evaluations or low logbook compliance.</p>
            </div>
            <a href="#at-risk-section" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors text-sm">
                View Details
            </a>
        </div>
    </div>
    @endif

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total Students -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Students</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_students'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Enrolled in LI module</p>
                </div>
                <div class="p-4 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Groups -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Groups</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_groups'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Training cohorts</p>
                </div>
                <div class="p-4 bg-purple-100 dark:bg-purple-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">At-Risk Students</p>
                    <p class="text-3xl font-bold {{ $atRiskStudents->count() > 0 ? 'text-amber-600' : 'text-green-600' }} mt-2">{{ $atRiskStudents->count() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $atRiskStudents->count() > 0 ? 'Needs attention' : 'All on track' }}</p>
                </div>
                <div class="p-4 {{ $atRiskStudents->count() > 0 ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-green-100 dark:bg-green-900/30' }} rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 {{ $atRiskStudents->count() > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Supervisor Assignment Rate -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Supervisor Rate</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $supervisionRate }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $supervisorStatus['with_both'] ?? 0 }} fully assigned</p>
                </div>
                <div class="p-4 bg-blue-100 dark:bg-blue-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Assessment Rate -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-green-300 dark:hover:border-green-600 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assessment Rate</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $overallProgress }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Evaluations completed</p>
                </div>
                <div class="p-4 bg-green-100 dark:bg-green-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Logbook Status -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-teal-300 dark:hover:border-teal-600 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Logbook Status</p>
                    <p class="text-3xl font-bold text-teal-600 mt-2">{{ $logbookStatus['compliant'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Students compliant</p>
                </div>
                <div class="p-4 bg-teal-100 dark:bg-teal-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout for Detailed Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assessment Completion by Type -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Assessment Completion by Type
            </h2>
            <div class="space-y-4">
                <!-- Supervisor LI Evaluation -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Supervisor LI Evaluation</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $assessmentBreakdown['supervisor_completed'] ?? 0 }} / {{ $stats['total_students'] }}
                        </span>
                    </div>
                    @php
                        $supervisorPercent = $stats['total_students'] > 0
                            ? (($assessmentBreakdown['supervisor_completed'] ?? 0) / $stats['total_students']) * 100
                            : 0;
                    @endphp
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-500" style="width: {{ $supervisorPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ round($supervisorPercent) }}% complete</p>
                </div>

                <!-- IC Evaluation -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Industry Coach Evaluation</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $assessmentBreakdown['ic_completed'] ?? 0 }} / {{ $stats['total_students'] }}
                        </span>
                    </div>
                    @php
                        $icPercent = $stats['total_students'] > 0
                            ? (($assessmentBreakdown['ic_completed'] ?? 0) / $stats['total_students']) * 100
                            : 0;
                    @endphp
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ $icPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ round($icPercent) }}% complete</p>
                </div>

                <!-- Overall -->
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Overall Completion</span>
                        <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $overallProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-4 rounded-full transition-all duration-500 flex items-center justify-center" style="width: {{ max($overallProgress, 5) }}%">
                            @if($overallProgress > 15)
                            <span class="text-xs font-medium text-white">{{ $overallProgress }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisor Assignment Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Supervisor Assignment Status
            </h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-green-800 dark:text-green-200">Both Assigned</span>
                    </div>
                    <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ $supervisorStatus['with_both'] ?? 0 }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Supervisor LI Only</span>
                    </div>
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $supervisorStatus['supervisor_only'] ?? 0 }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-orange-100 dark:bg-orange-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-orange-800 dark:text-orange-200">IC Only</span>
                    </div>
                    <span class="text-lg font-bold text-orange-600 dark:text-orange-400">{{ $supervisorStatus['ic_only'] ?? 0 }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 dark:bg-red-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-red-800 dark:text-red-200">No Assignment</span>
                    </div>
                    <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ $supervisorStatus['no_assignment'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- At-Risk Students Section -->
    @if($atRiskStudents->count() > 0)
    <div id="at-risk-section" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                At-Risk Students ({{ $atRiskStudents->count() }})
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Students requiring immediate attention based on evaluation status and logbook compliance</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Matric No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supervisor LI</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">IC</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Logbook</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Risk Factors</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($atRiskStudents as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium">
                                        {{ substr($student->user->name ?? 'N', 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-mono">
                            {{ $student->matric_no ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($student->supervisor)
                                <span class="text-sm text-gray-900 dark:text-white">{{ $student->supervisor->name ?? 'Assigned' }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                    Not Assigned
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($student->ic)
                                <span class="text-sm text-gray-900 dark:text-white">{{ $student->ic->name ?? 'Assigned' }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                    Not Assigned
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $logbookPercent = $student->logbook_compliance ?? 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                    <div class="h-2 rounded-full {{ $logbookPercent >= 80 ? 'bg-green-500' : ($logbookPercent >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $logbookPercent }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $logbookPercent }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @if(!$student->supervisor)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                        No Supervisor LI
                                    </span>
                                @endif
                                @if(!$student->ic)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                        No IC
                                    </span>
                                @endif
                                @if(($student->logbook_compliance ?? 0) < 50)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        Low Logbook
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('academic.li.schedule.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Assessment Schedule</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage assessment windows</p>
                </div>
            </div>
        </a>

        <a href="{{ route('academic.li.performance.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-green-300 dark:hover:border-green-600 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Student Performance</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View marks and progress</p>
                </div>
            </div>
        </a>

        <a href="{{ route('academic.li.finalisation.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Finalisation</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Review and finalize marks</p>
                </div>
            </div>
        </a>

        <a href="{{ route('academic.li.moderation.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Moderation</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Review marks consistency</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
