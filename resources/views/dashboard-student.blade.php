@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="min-h-screen bg-umpsa-soft-gray dark:bg-gray-900 -mx-10 -my-6 px-10 py-6">
    @if(isset($needsProfile) && $needsProfile)
        <!-- Profile Required Message -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 mb-6">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Profile Required</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">You need to create your student profile to access the dashboard.</p>
                <a href="{{ route('students.profile.create') }}" class="inline-flex items-center px-6 py-3 bg-[#003A6C] hover:bg-[#0084C5] text-white font-semibold rounded-lg shadow-md transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create My Profile
                </a>
            </div>
        </div>
    @else
    <!-- Welcome Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Welcome, {{ $student->name ?? 'Student' }}!</h1>
                <p class="text-gray-600 dark:text-gray-400">View your WBL progress, assignments, and evaluation status</p>
            </div>
            @if($student && $resumeInspection)
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Resume Inspection Status</p>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $resumeInspection->status_badge_color }}">
                        {{ $resumeInspection->status_display }}
                    </span>
                </div>
            @endif
        </div>
        
        @if($student && $resumeInspection && $resumeInspection->isApproved())
            <div class="mt-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            ✅ Resume Approved
                        </p>
                        <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                            Your resume has been approved. You may now begin applying for WBL placements.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($student && (!$resumeInspection || !$resumeInspection->isApproved()))
            <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            ❌ Resume Inspection Required
                        </p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            @if(!$resumeInspection || !$resumeInspection->resume_file_path)
                                You must submit your resume for inspection before you can apply for placements.
                            @elseif($resumeInspection->isRevisionRequired())
                                Your resume requires revision. Please review the coordinator's comments and resubmit.
                            @else
                                Your resume is pending review. Job application features are disabled until approval.
                            @endif
                            <a href="{{ route('student.resume.index') }}" class="underline font-semibold ml-1">Go to Resume Inspection</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($student)
    <!-- Assigned Supervisory Roles Row 1 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Academic Tutor (AT) Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Academic Tutor (AT)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">FYP - Semester 7</p>
                    </div>
                </div>
            </div>
            @if($assignedAt)
                <div class="space-y-2">
                    <p class="text-base font-medium text-gray-900 dark:text-gray-200">{{ $assignedAt->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignedAt->email }}</p>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">Not Assigned</p>
            @endif
        </div>

        <!-- Industry Coach (IC) Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-[#0084C5]/10 dark:bg-[#0084C5]/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Industry Coach (IC)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Entire WBL Duration</p>
                    </div>
                </div>
            </div>
            @if($assignedIc)
                <div class="space-y-2">
                    <p class="text-base font-medium text-gray-900 dark:text-gray-200">{{ $assignedIc->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignedIc->email }}</p>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">Not Assigned</p>
            @endif
        </div>

        <!-- Supervisor LI Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-[#00AEEF]/10 dark:bg-[#00AEEF]/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00AEEF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Supervisor LI</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Latihan Industri - Semester 8</p>
                    </div>
                </div>
            </div>
            @if($assignedSupervisorLi)
                <div class="space-y-2">
                    <p class="text-base font-medium text-gray-900 dark:text-gray-200">{{ $assignedSupervisorLi->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignedSupervisorLi->email }}</p>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">Not Assigned</p>
            @endif
        </div>
    </div>

    <!-- Assigned Lecturers Row 2 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- PPE Lecturer Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Lecturer (PPE)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Professional Practice & Ethics</p>
                    </div>
                </div>
            </div>
            @if($assignedPpeLecturer)
                <div class="space-y-2">
                    <p class="text-base font-medium text-gray-900 dark:text-gray-200">{{ $assignedPpeLecturer->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignedPpeLecturer->email }}</p>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">Not Assigned</p>
            @endif
        </div>

        <!-- OSH Lecturer Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-[#0084C5]/10 dark:bg-[#0084C5]/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Lecturer (OSH)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Occupational Safety & Health</p>
                    </div>
                </div>
            </div>
            @if($assignedOshLecturer)
                <div class="space-y-2">
                    <p class="text-base font-medium text-gray-900 dark:text-gray-200">{{ $assignedOshLecturer->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignedOshLecturer->email }}</p>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">Not Assigned</p>
            @endif
        </div>

        <!-- IP Lecturer Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-[#00AEEF]/10 dark:bg-[#00AEEF]/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#00AEEF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Lecturer (IP)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Internship Preparation</p>
                    </div>
                </div>
            </div>
            @if($assignedIpLecturer)
                <div class="space-y-2">
                    <p class="text-base font-medium text-gray-900 dark:text-gray-200">{{ $assignedIpLecturer->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assignedIpLecturer->email }}</p>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 italic">Not Assigned</p>
            @endif
        </div>
    </div>

    <!-- Course Progress Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-6">Course Progress Summary</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- PPE Score Card -->
            <div class="bg-gradient-to-br from-[#003A6C] to-[#0084C5] rounded-lg p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">PPE Score</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['PPE']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">AT: {{ number_format($courseScores['PPE']['at_score'], 1) }}/{{ $courseScores['PPE']['at_max'] }}, IC: {{ number_format($courseScores['PPE']['ic_score'], 1) }}/{{ $courseScores['PPE']['ic_max'] }}</p>
            </div>

            <!-- FYP Score Card -->
            <div class="bg-gradient-to-br from-[#0084C5] to-[#00AEEF] rounded-lg p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">FYP Score</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['FYP']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['FYP']['max'] }}%</p>
            </div>

            <!-- IP Score Card -->
            <div class="bg-gradient-to-br from-[#00AEEF] to-[#66C3FF] rounded-lg p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">IP Score</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['IP']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['IP']['max'] }}%</p>
            </div>

            <!-- OSH Score Card -->
            <div class="bg-gradient-to-br from-[#003A6C] to-[#00AEEF] rounded-lg p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">OSH Score</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['OSH']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['OSH']['max'] }}%</p>
            </div>

            <!-- LI Score Card -->
            <div class="bg-gradient-to-br from-[#0084C5] to-[#66C3FF] rounded-lg p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">LI Score</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['LI']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['LI']['max'] }}%</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Bar Chart - Course Scores Comparison -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Course Scores Overview</h3>
            <div style="height: 300px;">
                <canvas id="courseScoresChart"></canvas>
            </div>
        </div>

        <!-- Donut Chart - PPE AT vs IC Contribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">PPE Contribution Breakdown</h3>
            <div style="height: 300px;">
                <canvas id="ppeContributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Course Breakdown Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- PPE Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h4 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-3">PPE</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">AT Contribution:</span>
                    <span class="font-medium">{{ number_format($courseScores['PPE']['at_score'], 1) }}/{{ $courseScores['PPE']['at_max'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">IC Contribution:</span>
                    <span class="font-medium">{{ number_format($courseScores['PPE']['ic_score'], 1) }}/{{ $courseScores['PPE']['ic_max'] }}</span>
                </div>
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between">
                        <span class="font-semibold">Total:</span>
                        <span class="font-bold text-[#0084C5]">{{ number_format($courseScores['PPE']['score'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FYP Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h4 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-3">FYP</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Score:</span>
                    <span class="font-medium">{{ number_format($courseScores['FYP']['score'], 1) }}/{{ $courseScores['FYP']['max'] }}</span>
                </div>
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between">
                        <span class="font-semibold">Total:</span>
                        <span class="font-bold text-[#0084C5]">{{ number_format($courseScores['FYP']['score'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- IP Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h4 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-3">IP</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Score:</span>
                    <span class="font-medium">{{ number_format($courseScores['IP']['score'], 1) }}/{{ $courseScores['IP']['max'] }}</span>
                </div>
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between">
                        <span class="font-semibold">Total:</span>
                        <span class="font-bold text-[#0084C5]">{{ number_format($courseScores['IP']['score'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- OSH Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h4 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-3">OSH</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Score:</span>
                    <span class="font-medium">{{ number_format($courseScores['OSH']['score'], 1) }}/{{ $courseScores['OSH']['max'] }}</span>
                </div>
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between">
                        <span class="font-semibold">Total:</span>
                        <span class="font-bold text-[#0084C5]">{{ number_format($courseScores['OSH']['score'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- LI Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h4 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-3">LI</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Score:</span>
                    <span class="font-medium">{{ number_format($courseScores['LI']['score'], 1) }}/{{ $courseScores['LI']['max'] }}</span>
                </div>
                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between">
                        <span class="font-semibold">Total:</span>
                        <span class="font-bold text-[#0084C5]">{{ number_format($courseScores['LI']['score'], 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
    // UMPSA Color Palette
    const UMPSA_COLORS = {
        primary: '#003A6C',
        secondary: '#0084C5',
        accent: '#00AEEF',
        light: '#66C3FF',
        darkNavy: '#002244',
        softGray: '#F4F7FC',
        neutralGray: '#E6ECF2',
    };

    // Bar Chart - Course Scores
    const courseScoresCtx = document.getElementById('courseScoresChart');
    if (courseScoresCtx) {
        new Chart(courseScoresCtx, {
            type: 'bar',
            data: {
                labels: @json($barChartData['labels']),
                datasets: [{
                    label: 'Score (%)',
                    data: @json($barChartData['scores']),
                    backgroundColor: [
                        UMPSA_COLORS.primary,
                        UMPSA_COLORS.secondary,
                        UMPSA_COLORS.accent,
                        UMPSA_COLORS.primary + 'CC',
                        UMPSA_COLORS.secondary + 'CC',
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
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
                        backgroundColor: UMPSA_COLORS.primary,
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: UMPSA_COLORS.neutralGray
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Score (%)',
                            color: '#6B7280',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }

    // Donut Chart - PPE AT vs IC Contribution
    const ppeContributionCtx = document.getElementById('ppeContributionChart');
    if (ppeContributionCtx) {
        new Chart(ppeContributionCtx, {
            type: 'doughnut',
            data: {
                labels: @json($ppeDonutData['labels']),
                datasets: [{
                    data: @json($ppeDonutData['data']),
                    backgroundColor: [
                        UMPSA_COLORS.primary,
                        UMPSA_COLORS.secondary,
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
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
                            color: '#6B7280',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: UMPSA_COLORS.primary,
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value.toFixed(1) + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
</script>
@endpush
    @endif
    @endif
</div>
@endsection

