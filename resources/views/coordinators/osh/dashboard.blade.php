@extends('layouts.app')

@section('title', 'OSH Coordinator Dashboard')

@section('content')
<div class="min-h-screen">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Dashboard</span>
                    <span class="mx-2">›</span>
                    <span class="text-gray-700 dark:text-gray-200">OSH Coordinator</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">OSH Coordinator Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor and manage Occupational Safety & Health module</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('academic.osh.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003A6C] text-white rounded-lg text-sm font-medium hover:bg-[#002D54] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Banner with Key Insights -->
    <div class="mb-6 relative overflow-hidden">
        <div class="bg-white/40 dark:bg-gray-800/40 backdrop-blur-xl border border-gray-200/50 dark:border-gray-700/50 rounded-2xl p-6 md:p-8 shadow-xl">
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#0084C5]/10 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-[#00AEEF]/10 rounded-full -ml-24 -mb-24"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#003A6C] to-[#0084C5] flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-gray-900 dark:text-white text-2xl font-bold">Module Overview</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Real-time insights and performance metrics</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <!-- Overall Progress -->
                    <div class="bg-white/60 dark:bg-gray-700/60 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50 dark:border-gray-600/50 hover:bg-white/80 dark:hover:bg-gray-700/80 transition-all duration-300 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider font-medium">Overall Progress</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1">{{ $stats['assessment_completion_rate'] }}</p>
                        <div class="flex items-center gap-1 text-xs">
                            <span class="text-green-600 dark:text-green-400 font-medium">↑ {{ $stats['assessment_completion_rate'] }}%</span>
                            <span class="text-gray-600 dark:text-gray-400">complete</span>
                        </div>
                    </div>

                    <!-- Active Engagement -->
                    <div class="bg-white/60 dark:bg-gray-700/60 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50 dark:border-gray-600/50 hover:bg-white/80 dark:hover:bg-gray-700/80 transition-all duration-300 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider font-medium">Active Students</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1">{{ $stats['total_students'] - $stats['at_risk_count'] }}</p>
                        <div class="flex items-center gap-1 text-xs">
                            <span class="text-gray-600 dark:text-gray-400">of</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $stats['total_students'] }} total</span>
                        </div>
                    </div>

                    <!-- Supervision Coverage -->
                    <div class="bg-white/60 dark:bg-gray-700/60 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50 dark:border-gray-600/50 hover:bg-white/80 dark:hover:bg-gray-700/80 transition-all duration-300 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider font-medium">Supervision</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1">{{ $stats['supervisor_assignment_rate'] }}</p>
                        <div class="flex items-center gap-1 text-xs">
                            <span class="text-gray-900 dark:text-white font-medium">{{ $stats['supervisor_assignment_rate'] }}%</span>
                            <span class="text-gray-600 dark:text-gray-400">assigned</span>
                        </div>
                    </div>

                    <!-- Quality Index -->
                    <div class="bg-white/60 dark:bg-gray-700/60 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50 dark:border-gray-600/50 hover:bg-white/80 dark:hover:bg-gray-700/80 transition-all duration-300 shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider font-medium">Quality Score</span>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        @php
                            $qualityScore = round(($stats['assessment_completion_rate'] + $logbookStatus['compliance_rate']) / 2);
                        @endphp
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1">{{ $qualityScore }}</p>
                        <div class="flex items-center gap-1 text-xs">
                            @if($qualityScore >= 80)
                                <span class="text-green-600 dark:text-green-400 font-medium">Excellent</span>
                            @elseif($qualityScore >= 60)
                                <span class="text-yellow-600 dark:text-yellow-400 font-medium">Good</span>
                            @else
                                <span class="text-orange-600 dark:text-orange-400 font-medium">Needs Attention</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if(count($atRiskStudents) > 0)
                <div class="mt-5 bg-red-50/80 dark:bg-red-900/30 backdrop-blur-sm border border-red-200/50 dark:border-red-800/50 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-gray-900 dark:text-white text-sm">
                            <span class="font-bold">{{ count($atRiskStudents) }} student(s)</span> require immediate attention
                            <a href="#at-risk-section" class="ml-2 underline hover:no-underline text-[#0084C5] dark:text-[#00AEEF]">View details →</a>
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <!-- Total Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 dark:bg-blue-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Students</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['total_students']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">100%</span>
                            <span class="text-xs text-gray-500">enrolled</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Groups -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-100 dark:bg-emerald-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Groups</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['total_groups']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center gap-0.5">
                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Active</span>
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-{{ count($atRiskStudents) > 0 ? 'red' : 'green' }}-100 dark:bg-{{ count($atRiskStudents) > 0 ? 'red' : 'green' }}-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">At-Risk Students</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['at_risk_count']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            @if(count($atRiskStudents) > 0)
                                <span class="text-xs text-red-600 dark:text-red-400 font-medium flex items-center gap-0.5">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Action Required
                                </span>
                            @else
                                <span class="text-xs text-green-600 dark:text-green-400 font-medium">All On Track</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ count($atRiskStudents) > 0 ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600' }} flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                        @if(count($atRiskStudents) > 0)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisor Assignment Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-purple-100 dark:bg-purple-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supervisor Rate</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['supervisor_assignment_rate'] }}%</p>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2.5 rounded-full transition-all duration-500 relative" style="width: {{ $stats['supervisor_assignment_rate'] }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Completion -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-orange-100 dark:bg-orange-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assessment Rate</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['assessment_completion_rate'] }}%</p>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full transition-all duration-500 relative" style="width: {{ $stats['assessment_completion_rate'] }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logbook Compliance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-teal-100 dark:bg-teal-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Logbook Status</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $logbookStatus['compliance_rate'] }}%</p>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-500 to-teal-600 h-2.5 rounded-full transition-all duration-500 relative" style="width: {{ $logbookStatus['compliance_rate'] }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Completion Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assessment Completion by Type</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lecturer and IC evaluation progress</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-purple-600 dark:text-purple-400 mb-3">Lecturer Evaluations</h4>
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Evaluation</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $assessmentBreakdown['lecturer']['submitted'] }} / {{ $assessmentBreakdown['lecturer']['total'] }}
                            </span>
                        </div>
                        @php
                            $lecturerPercentage = $assessmentBreakdown['lecturer']['total'] > 0
                                ? ($assessmentBreakdown['lecturer']['submitted'] / $assessmentBreakdown['lecturer']['total']) * 100
                                : 0;
                        @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $lecturerPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-orange-600 dark:text-orange-400 mb-3">Industry Coach (IC) Evaluations</h4>
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Evaluation</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $assessmentBreakdown['ic']['submitted'] }} / {{ $assessmentBreakdown['ic']['total'] }}
                            </span>
                        </div>
                        @php
                            $icPercentage = $assessmentBreakdown['ic']['total'] > 0
                                ? ($assessmentBreakdown['ic']['submitted'] / $assessmentBreakdown['ic']['total']) * 100
                                : 0;
                        @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" style="width: {{ $icPercentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supervisor Assignment Status -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Supervisor Assignment Status</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lecturer and Industry Coach assignments</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Lecturer Assignment -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lecturers</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $supervisorStatus['lecturer_assigned'] }} / {{ $supervisorStatus['lecturer_total'] }}
                    </span>
                </div>
                @php
                    $lecturerPercentage = $supervisorStatus['lecturer_total'] > 0
                        ? ($supervisorStatus['lecturer_assigned'] / $supervisorStatus['lecturer_total']) * 100
                        : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                    <div class="bg-purple-600 h-4 rounded-full transition-all duration-300" style="width: {{ $lecturerPercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ round($lecturerPercentage) }}% assigned</p>
            </div>

            <!-- IC Assignment -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Industry Coaches (IC)</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $supervisorStatus['ic_assigned'] }} / {{ $supervisorStatus['ic_total'] }}
                    </span>
                </div>
                @php
                    $icPercentage = $supervisorStatus['ic_total'] > 0
                        ? ($supervisorStatus['ic_assigned'] / $supervisorStatus['ic_total']) * 100
                        : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                    <div class="bg-orange-600 h-4 rounded-full transition-all duration-300" style="width: {{ $icPercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ round($icPercentage) }}% assigned</p>
            </div>
        </div>

        @if(count($supervisorStatus['unassigned_students']) > 0)
        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-sm text-amber-800 dark:text-amber-200">
                    <p class="font-semibold">{{ count($supervisorStatus['unassigned_students']) }} students without complete supervisor assignments</p>
                    <p class="mt-1">Some students are missing either Lecturer or IC assignment. Please assign supervisors promptly.</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- At-Risk Students Section -->
    @if(count($atRiskStudents) > 0)
    <div id="at-risk-section" class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-2xl border-2 border-red-200 dark:border-red-800 p-6 md:p-8 mb-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-100/50 dark:bg-red-900/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-orange-100/50 dark:bg-orange-900/10 rounded-full -ml-24 -mb-24"></div>

        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg animate-pulse">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            Attention Required
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-red-600 text-white text-sm font-bold shadow-lg">
                                {{ count($atRiskStudents) }}
                            </span>
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Students requiring immediate intervention and support</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @foreach($atRiskStudents as $student)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ substr($student['student_name'], 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $student['student_name'] }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student['matric_no'] }} • {{ $student['group'] }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student['risk_level'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                    {{ ucfirst($student['risk_level']) }} Risk
                                </span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($student['issues'] as $issue)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 text-xs">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $issue }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="{{ route('academic.osh.schedule.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Assessment Schedule</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage assessment windows</p>
                </div>
            </div>
        </a>

        <a href="{{ route('academic.osh.performance.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Student Performance</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View marks and progress</p>
                </div>
            </div>
        </a>

        <a href="{{ route('academic.osh.finalisation.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Finalisation</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Review and finalize marks</p>
                </div>
            </div>
        </a>

        <a href="{{ route('academic.osh.moderation.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:scale-105 transition-all duration-300 group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Moderation</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Moderate assessments</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
