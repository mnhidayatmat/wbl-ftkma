@extends('layouts.app')

@section('title', 'FYP Coordinator Dashboard')

@section('content')
<div class="min-h-screen">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Dashboard</span>
                    <span class="mx-2">›</span>
                    <span class="text-gray-700 dark:text-gray-200">FYP Coordinator</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">FYP Coordinator Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor and manage Final Year Project module</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('academic.fyp.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003A6C] text-white rounded-lg text-sm font-medium hover:bg-[#002D54] transition-colors">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
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
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1" data-count="{{ $stats['assessment_completion_rate'] }}">0</p>
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
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1" data-count="{{ $stats['total_students'] - $stats['at_risk_count'] }}">0</p>
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
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1" data-count="{{ $stats['supervisor_assignment_rate'] }}">0</p>
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
                        <p class="text-gray-900 dark:text-white text-3xl font-bold mb-1" data-count="{{ $qualityScore }}">0</p>
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

    <!-- Enhanced KPI Cards with Animation -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <!-- Total Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 dark:bg-blue-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Students</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" data-count="{{ $stats['total_students'] }}">{{ number_format($stats['total_students']) }}</p>
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

        <!-- Active Projects -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-2xl hover:scale-105 transition-all duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-100 dark:bg-emerald-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Projects</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" data-count="{{ $stats['active_projects'] }}">{{ number_format($stats['active_projects']) }}</p>
                        <div class="mt-2 flex items-center gap-1">
                            <span class="inline-flex items-center gap-0.5">
                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">In Progress</span>
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center group-hover:rotate-12 transition-all duration-300 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" data-count="{{ $stats['at_risk_count'] }}">{{ number_format($stats['at_risk_count']) }}</p>
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
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" data-count="{{ $stats['supervisor_assignment_rate'] }}">{{ $stats['supervisor_assignment_rate'] }}%</p>
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
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" data-count="{{ $stats['assessment_completion_rate'] }}">{{ $stats['assessment_completion_rate'] }}%</p>
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
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" data-count="{{ $logbookStatus['compliance_rate'] }}">{{ $logbookStatus['compliance_rate'] }}%</p>
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

    <!-- Project Status & Assessment Completion -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Project Status Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Project Status</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Distribution across project stages</p>
            </div>
            <div class="h-[250px]">
                <canvas id="projectStatusChart"></canvas>
            </div>
        </div>

        <!-- Assessment Completion by Type -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assessment Completion by Type</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">AT and IC evaluation progress</p>
            </div>
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-semibold text-purple-600 dark:text-purple-400 mb-3">Academic Tutor (AT) Evaluations</h4>
                    <div class="space-y-3">
                        @foreach(['logbook' => 'Logbook', 'progress' => 'Progress Eval', 'final_report' => 'Final Report', 'presentation' => 'Presentation'] as $key => $label)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $assessmentBreakdown['at'][$key]['submitted'] }} / {{ $assessmentBreakdown['at'][$key]['total'] }}
                                </span>
                            </div>
                            @php
                                $percentage = $assessmentBreakdown['at'][$key]['total'] > 0
                                    ? ($assessmentBreakdown['at'][$key]['submitted'] / $assessmentBreakdown['at'][$key]['total']) * 100
                                    : 0;
                            @endphp
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-orange-600 dark:text-orange-400 mb-3">Industry Coach (IC) Evaluations</h4>
                    <div class="space-y-3">
                        @foreach(['logbook' => 'Logbook', 'progress' => 'Progress Eval', 'final_report' => 'Final Report', 'presentation' => 'Presentation'] as $key => $label)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $assessmentBreakdown['ic'][$key]['submitted'] }} / {{ $assessmentBreakdown['ic'][$key]['total'] }}
                                </span>
                            </div>
                            @php
                                $percentage = $assessmentBreakdown['ic'][$key]['total'] > 0
                                    ? ($assessmentBreakdown['ic'][$key]['submitted'] / $assessmentBreakdown['ic'][$key]['total']) * 100
                                    : 0;
                            @endphp
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Grade Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Grade Distribution</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Student performance overview</p>
            </div>
            <div class="h-[250px]">
                <canvas id="gradeDistributionChart"></canvas>
            </div>
        </div>

        <!-- AT vs IC Marks Comparison -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">AT vs IC Marks Comparison</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Average marks by assessment type</p>
            </div>
            <div class="h-[250px]">
                <canvas id="marksComparisonChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Supervisor Assignment Status -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Supervisor Assignment Status</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Academic Tutor and Industry Coach assignments</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- AT Assignment -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Academic Tutors (AT)</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $supervisorStatus['at_assigned'] }} / {{ $supervisorStatus['at_total'] }}
                    </span>
                </div>
                @php
                    $atPercentage = $supervisorStatus['at_total'] > 0
                        ? ($supervisorStatus['at_assigned'] / $supervisorStatus['at_total']) * 100
                        : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                    <div class="bg-purple-600 h-4 rounded-full transition-all duration-300" style="width: {{ $atPercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ round($atPercentage) }}% assigned</p>
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
                    <p class="font-semibold">⚠️ {{ count($supervisorStatus['unassigned_students']) }} students without complete supervisor assignments</p>
                    <p class="mt-1">Some students are missing either AT or IC assignment. Please assign supervisors promptly.</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Enhanced At-Risk Students Section -->
    @if(count($atRiskStudents) > 0)
    <div id="at-risk-section" class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-2xl border-2 border-red-200 dark:border-red-800 p-6 md:p-8 mb-6 relative overflow-hidden">
        <!-- Decorative Background -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-100/50 dark:bg-red-900/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-orange-100/50 dark:bg-orange-900/10 rounded-full -ml-24 -mb-24"></div>

        <div class="relative z-10">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex items-center gap-4 cursor-pointer" onclick="toggleAtRiskSection()">
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
                <div class="flex items-center gap-2">
                    <button onclick="toggleAtRiskSection()" id="at-risk-toggle-btn" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:shadow-md transition-all border border-gray-200 dark:border-gray-700">
                        <svg id="at-risk-chevron-down" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <svg id="at-risk-chevron-up" class="w-4 h-4 hidden transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                        <span id="at-risk-toggle-text">Minimize</span>
                    </button>
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:shadow-md transition-all border border-gray-200 dark:border-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print List
                    </button>
                </div>
            </div>

            <!-- Student Cards (Collapsible) -->
            <div id="at-risk-content" class="grid grid-cols-1 gap-4 transition-all duration-300 ease-in-out overflow-hidden">
                @foreach($atRiskStudents as $index => $student)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-xl transition-all duration-300 hover:scale-[1.01] group">
                    <div class="flex items-start gap-4">
                        <!-- Risk Indicator -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full {{ $student['risk_level'] === 'high' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-orange-100 dark:bg-orange-900/30' }} flex items-center justify-center border-2 {{ $student['risk_level'] === 'high' ? 'border-red-300 dark:border-red-700' : 'border-orange-300 dark:border-orange-700' }}">
                                <span class="text-xl font-bold {{ $student['risk_level'] === 'high' ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400' }}">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                        </div>

                        <!-- Student Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ $student['student_name'] }}</h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $student['matric_no'] }}</span>
                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $student['group'] }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold {{ $student['risk_level'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 border border-red-200 dark:border-red-700' : 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100 border border-orange-200 dark:border-orange-700' }}">
                                    @if($student['risk_level'] === 'high')
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    {{ ucfirst($student['risk_level']) }} Risk
                                </span>
                            </div>

                            <!-- Issues -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Issues Identified</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($student['issues'] as $issue)
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $issue }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2 mt-4">
                                <a href="{{ route('admin.students.show', $student['student_id'] ?? '#') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#003A6C] hover:bg-[#002D54] text-white rounded-lg text-xs font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </a>
                                <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium transition-colors border border-gray-200 dark:border-gray-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Contact
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Project Milestones Timeline -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Project Milestones Timeline
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track submission progress across project phases</p>
        </div>

        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-200 via-purple-200 to-green-200 dark:from-blue-900/50 dark:via-purple-900/50 dark:to-green-900/50"></div>

            <!-- Milestones -->
            <div class="space-y-8">
                @php
                    $milestones = [
                        'proposal' => ['label' => 'Project Proposal', 'color' => 'blue', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        'progress_report_1' => ['label' => 'Progress Report 1', 'color' => 'purple', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        'progress_report_2' => ['label' => 'Progress Report 2', 'color' => 'indigo', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        'final_report' => ['label' => 'Final Report', 'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'presentation' => ['label' => 'Presentation Scheduled', 'color' => 'orange', 'icon' => 'M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z'],
                    ];
                @endphp

                @foreach($milestones as $key => $milestone)
                    @php
                        $submitted = $projectMilestones[$key]['submitted'] ?? $projectMilestones[$key]['scheduled'] ?? 0;
                        $total = $projectMilestones[$key]['total'];
                        $percentage = $total > 0 ? ($submitted / $total) * 100 : 0;
                        $isComplete = $percentage >= 80;
                        $isInProgress = $percentage >= 30 && $percentage < 80;
                    @endphp

                    <div class="relative pl-16 group">
                        <!-- Timeline Dot -->
                        <div class="absolute left-3 w-6 h-6 rounded-full border-4 border-white dark:border-gray-800 {{ $isComplete ? 'bg-green-500' : ($isInProgress ? 'bg-'.$milestone['color'].'-500' : 'bg-gray-300 dark:bg-gray-600') }} group-hover:scale-125 transition-transform duration-300 shadow-lg"></div>

                        <!-- Milestone Card -->
                        <div class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/50 dark:to-gray-800/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-{{ $milestone['color'] }}-100 dark:bg-{{ $milestone['color'] }}-900/30 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-{{ $milestone['color'] }}-600 dark:text-{{ $milestone['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $milestone['icon'] }}"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $milestone['label'] }}</h4>
                                            @if($isComplete)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Complete
                                                </span>
                                            @elseif($isInProgress)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $milestone['color'] }}-100 text-{{ $milestone['color'] }}-800 dark:bg-{{ $milestone['color'] }}-800 dark:text-{{ $milestone['color'] }}-100">
                                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    In Progress
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Progress:</span>
                                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                                <div class="bg-gradient-to-r from-{{ $milestone['color'] }}-500 to-{{ $milestone['color'] }}-600 h-2 rounded-full transition-all duration-500 relative" style="width: {{ $percentage }}%">
                                                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                                </div>
                                            </div>
                                            <span class="text-xs font-bold text-gray-900 dark:text-white min-w-[3rem] text-right">{{ round($percentage) }}%</span>
                                        </div>

                                        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span><strong>{{ $submitted }}</strong> of <strong>{{ $total }}</strong> students submitted</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-2xl font-bold text-{{ $milestone['color'] }}-600 dark:text-{{ $milestone['color'] }}-400">
                                        {{ $submitted }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">/ {{ $total }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CLO/PLO Mapping & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- CLO/PLO Mapping Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">CLO/PLO Mapping Status</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Learning outcome mapping compliance</p>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">CLO Completed</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $cloploStatus['clo_completed'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">PLO Completed</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $cloploStatus['plo_completed'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Pending Review</p>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $cloploStatus['pending_review'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Actions -->
        <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-700 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quick Actions
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Navigate to key FYP management functions</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('admin.students.index') }}" class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100 dark:bg-blue-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:rotate-6 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Students</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View all students</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.lecturer.index') }}" class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-purple-100 dark:bg-purple-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:rotate-6 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">AT Evaluations</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage tutor marks</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.ic.index') }}" class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-orange-100 dark:bg-orange-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:rotate-6 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">IC Evaluations</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage coach marks</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.logbook.index') }}" class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-teal-100 dark:bg-teal-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:rotate-6 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Logbooks</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Review submissions</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.clo-plo.index') }}" class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-indigo-100 dark:bg-indigo-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:rotate-6 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">CLO/PLO</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage mapping</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.performance.index') }}" class="group relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-green-100 dark:bg-green-900/20 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:rotate-6 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Performance</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View analytics</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Toggle At-Risk Section
function toggleAtRiskSection() {
    const content = document.getElementById('at-risk-content');
    const toggleText = document.getElementById('at-risk-toggle-text');
    const chevronDown = document.getElementById('at-risk-chevron-down');
    const chevronUp = document.getElementById('at-risk-chevron-up');

    if (content.style.maxHeight && content.style.maxHeight !== '0px') {
        // Collapse
        content.style.maxHeight = '0px';
        content.style.opacity = '0';
        content.style.marginTop = '0';
        toggleText.textContent = 'Expand';
        chevronDown.classList.add('hidden');
        chevronUp.classList.remove('hidden');
    } else {
        // Expand
        content.style.maxHeight = content.scrollHeight + 'px';
        content.style.opacity = '1';
        content.style.marginTop = '';
        toggleText.textContent = 'Minimize';
        chevronDown.classList.remove('hidden');
        chevronUp.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize At-Risk Section as expanded
    const atRiskContent = document.getElementById('at-risk-content');
    if (atRiskContent) {
        atRiskContent.style.maxHeight = atRiskContent.scrollHeight + 'px';
        atRiskContent.style.opacity = '1';
    }

    // Count-up Animation Function
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Animate all elements with data-count attribute
    document.querySelectorAll('[data-count]').forEach(element => {
        animateCounter(element);
    });

    // Color palette
    const colors = {
        primary: '#003A6C',
        secondary: '#0084C5',
        accent: '#00AEEF',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        purple: '#7C3AED',
        orange: '#F97316',
    };

    // Project Status Chart (Donut)
    const projectStatusCtx = document.getElementById('projectStatusChart');
    if (projectStatusCtx) {
        new Chart(projectStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($projectStatus['labels']),
                datasets: [{
                    data: @json($projectStatus['data']),
                    backgroundColor: @json($projectStatus['colors']),
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 8,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 11, weight: '500' },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        return {
                                            text: `${label}: ${value}`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Grade Distribution Chart
    const gradeCtx = document.getElementById('gradeDistributionChart');
    if (gradeCtx) {
        new Chart(gradeCtx, {
            type: 'bar',
            data: {
                labels: @json($gradeDistribution['labels']),
                datasets: [{
                    label: 'Students',
                    data: @json($gradeDistribution['data']),
                    backgroundColor: @json($gradeDistribution['colors']),
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart',
                    delay: (context) => context.dataIndex * 100
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return `Students: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 11 },
                            color: '#6B7280'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#374151'
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Marks Comparison Chart (Grouped Bar)
    const marksCtx = document.getElementById('marksComparisonChart');
    if (marksCtx) {
        new Chart(marksCtx, {
            type: 'bar',
            data: {
                labels: @json($marksComparison['labels']),
                datasets: [
                    {
                        label: 'AT Marks',
                        data: @json($marksComparison['at_marks']),
                        backgroundColor: 'rgba(124, 58, 237, 0.8)',
                        hoverBackgroundColor: 'rgba(124, 58, 237, 1)',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'IC Marks',
                        data: @json($marksComparison['ic_marks']),
                        backgroundColor: 'rgba(249, 115, 22, 0.8)',
                        hoverBackgroundColor: 'rgba(249, 115, 22, 1)',
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 11, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}%`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            font: { size: 11 },
                            color: '#6B7280'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#374151'
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Smooth scroll for at-risk section link
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
@endpush
