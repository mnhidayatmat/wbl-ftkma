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

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <!-- Total Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Students</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['total_students']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Projects</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['active_projects']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group {{ count($atRiskStudents) > 0 ? 'ring-2 ring-red-400' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">At-Risk Students</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($stats['at_risk_count']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ count($atRiskStudents) > 0 ? 'from-red-500 to-red-600' : 'from-green-500 to-green-600' }} flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Supervisor Assignment Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supervisor Rate</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['supervisor_assignment_rate'] }}%</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-3">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['supervisor_assignment_rate'] }}%"></div>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Assessment Completion -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assessment Rate</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['assessment_completion_rate'] }}%</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-3">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-500" style="width: {{ $stats['assessment_completion_rate'] }}%"></div>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Logbook Compliance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-shadow group">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Logbook Status</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $logbookStatus['compliance_rate'] }}%</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-3">
                        <div class="bg-gradient-to-r from-teal-500 to-teal-600 h-2 rounded-full transition-all duration-500" style="width: {{ $logbookStatus['compliance_rate'] }}%"></div>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
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

    <!-- At-Risk Students Alert -->
    @if(count($atRiskStudents) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-red-200 dark:border-red-800 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">At-Risk Students</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Students requiring immediate attention</p>
                </div>
            </div>
            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 text-sm font-bold">
                {{ count($atRiskStudents) }} students
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <th class="text-left py-2 font-medium">Student</th>
                        <th class="text-left py-2 font-medium">Group</th>
                        <th class="text-left py-2 font-medium">Issues</th>
                        <th class="text-center py-2 font-medium">Risk Level</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($atRiskStudents as $student)
                    <tr class="border-b border-gray-50 dark:border-gray-700/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="py-3">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $student['student_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student['matric_no'] }}</p>
                        </td>
                        <td class="py-3 text-gray-600 dark:text-gray-400">{{ $student['group'] }}</td>
                        <td class="py-3">
                            <div class="space-y-1">
                                @foreach($student['issues'] as $issue)
                                <p class="text-xs text-gray-600 dark:text-gray-400">• {{ $issue }}</p>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $student['risk_level'] === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100' }}">
                                {{ ucfirst($student['risk_level']) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Project Milestones Tracker -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Project Milestones Tracker</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Track submission progress across project phases</p>
        </div>
        <div class="space-y-4">
            @foreach([
                'proposal' => ['label' => 'Project Proposal', 'color' => 'blue'],
                'progress_report_1' => ['label' => 'Progress Report 1', 'color' => 'purple'],
                'progress_report_2' => ['label' => 'Progress Report 2', 'color' => 'indigo'],
                'final_report' => ['label' => 'Final Report', 'color' => 'green'],
                'presentation' => ['label' => 'Presentation Scheduled', 'color' => 'orange'],
            ] as $key => $milestone)
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $milestone['label'] }}</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $projectMilestones[$key]['submitted'] ?? $projectMilestones[$key]['scheduled'] ?? 0 }} / {{ $projectMilestones[$key]['total'] }}
                    </span>
                </div>
                @php
                    $percentage = $projectMilestones[$key]['total'] > 0
                        ? (($projectMilestones[$key]['submitted'] ?? $projectMilestones[$key]['scheduled'] ?? 0) / $projectMilestones[$key]['total']) * 100
                        : 0;
                @endphp
                <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
                    <div class="bg-{{ $milestone['color'] }}-600 h-3 rounded-full transition-all duration-300 flex items-center justify-end pr-2" style="width: {{ $percentage }}%">
                        @if($percentage > 20)
                        <span class="text-xs font-bold text-white">{{ round($percentage) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
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

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Navigate to key FYP management functions</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Students</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View all</p>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.lecturer.index') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">AT Evaluations</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Manage</p>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.ic.index') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">IC Evaluations</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Manage</p>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.logbook.index') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 dark:bg-teal-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Logbooks</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Review</p>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.clo-plo.index') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">CLO/PLO</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Manage</p>
                    </div>
                </a>

                <a href="{{ route('academic.fyp.performance.index') }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Performance</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View</p>
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
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 11 }
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
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
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
                        backgroundColor: colors.purple,
                        borderRadius: 4,
                    },
                    {
                        label: 'IC Marks',
                        data: @json($marksComparison['ic_marks']),
                        backgroundColor: colors.orange,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
