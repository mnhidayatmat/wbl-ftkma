@extends('layouts.app')

@section('title', 'OSH Coordinator Dashboard')

@push('styles')
<style>
    .coordinator-hero {
        background: linear-gradient(135deg, #c2410c 0%, #ea580c 25%, #f97316 50%, #fb923c 75%, #c2410c 100%);
        background-size: 400% 400%;
        animation: elegantGradient 20s ease infinite;
    }

    @keyframes elegantGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .icon-float {
        animation: iconFloat 3s ease-in-out infinite;
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .glass-card {
        background: rgba(31, 41, 55, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
@php
    // Calculate summary statistics
    $overallProgress = $stats['total_students'] > 0
        ? round((($assessmentBreakdown['at_completed'] ?? 0) + ($assessmentBreakdown['ic_completed'] ?? 0)) / ($stats['total_students'] * 2) * 100)
        : 0;
    $supervisionRate = $stats['total_students'] > 0
        ? round(($supervisorStatus['with_both'] ?? 0) / $stats['total_students'] * 100)
        : 0;
    $qualityScore = min(100, max(0, 100 - ((count($atRiskStudents) ?? 0) * 5)));
@endphp

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-orange-50/30 to-amber-50/30 dark:from-gray-900 dark:via-orange-950/20 dark:to-amber-950/20 space-y-6">

    <!-- Elegant Coordinator Header -->
    <div class="coordinator-hero rounded-2xl p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center icon-float">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">OSH Coordinator Dashboard</h1>
                        <p class="text-white/80">Occupational Safety & Health Management</p>
                    </div>
                </div>

                <div class="hidden lg:flex items-center gap-3">
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                        <p class="text-xs text-white/70">Total Students</p>
                        <p class="text-xl font-bold">{{ $stats['total_students'] }}</p>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                        <p class="text-xs text-white/70">Active Groups</p>
                        <p class="text-xl font-bold">{{ $stats['total_groups'] }}</p>
                    </div>
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                        <p class="text-xs text-white/70">Progress</p>
                        <p class="text-xl font-bold">{{ $overallProgress }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Project Milestone Timeline -->
    <x-coordinator.milestone-timeline
        module="osh"
        :atWindow="$atWindow ?? null"
        :icWindow="$icWindow ?? null"
        :groups="$groups ?? null"
    />

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total Students -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Students</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_students'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Enrolled in OSH module</p>
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
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Safety training cohorts</p>
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
                    <p class="text-3xl font-bold {{ count($atRiskStudents) > 0 ? 'text-amber-600' : 'text-green-600' }} mt-2">{{ count($atRiskStudents) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ count($atRiskStudents) > 0 ? 'Needs attention' : 'All on track' }}</p>
                </div>
                <div class="p-4 {{ count($atRiskStudents) > 0 ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-green-100 dark:bg-green-900/30' }} rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 {{ count($atRiskStudents) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <!-- Lecturer OSH Evaluation -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lecturer OSH Evaluation</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $assessmentBreakdown['at_completed'] ?? 0 }} / {{ $stats['total_students'] }}
                        </span>
                    </div>
                    @php
                        $lecturerPercent = $stats['total_students'] > 0
                            ? (($assessmentBreakdown['at_completed'] ?? 0) / $stats['total_students']) * 100
                            : 0;
                    @endphp
                    <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-500" style="width: {{ $lecturerPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ round($lecturerPercent) }}% complete</p>
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
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Lecturer Only</span>
                    </div>
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $supervisorStatus['at_only'] ?? 0 }}</span>
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
    @if(count($atRiskStudents) > 0)
    <div id="at-risk-section" class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-2xl border-2 border-red-200 dark:border-red-800 mb-6 relative overflow-hidden" x-data="{ isMinimized: true }">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-100/50 dark:bg-red-900/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-orange-100/50 dark:bg-orange-900/10 rounded-full -ml-24 -mb-24"></div>

        <div class="relative z-10">
            <!-- Header (Always visible) -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 md:p-8" :class="{ 'pb-4': !isMinimized }">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg" :class="{ 'animate-pulse': !isMinimized }">
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
                <button @click="isMinimized = !isMinimized" class="p-2 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors self-start sm:self-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 transition-transform duration-300" :class="{ 'rotate-180': isMinimized }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <!-- Collapsible Content -->
            <div x-show="!isMinimized" x-collapse class="px-6 md:px-8 pb-6 md:pb-8">
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
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('academic.osh.schedule.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300">
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

        <a href="{{ route('academic.osh.performance.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-green-300 dark:hover:border-green-600 transition-all duration-300">
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

        <a href="{{ route('academic.osh.finalisation.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300">
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

        <a href="{{ route('academic.osh.moderation.index') }}" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-300">
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
