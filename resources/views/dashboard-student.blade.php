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
        <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Welcome, {{ $student->name ?? 'Student' }}!</h1>
        <p class="text-gray-600 dark:text-gray-400">Track your WBL journey, submit documents, and monitor your progress</p>
    </div>

    @if($student)
    <!-- Priority Actions Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Resume Submission Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 {{ $resumeInspection && $resumeInspection->isApproved() ? 'border-green-500' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'border-red-500' : 'border-amber-500') }}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl {{ $resumeInspection && $resumeInspection->isApproved() ? 'bg-green-100 dark:bg-green-900/30' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'bg-red-100 dark:bg-red-900/30' : 'bg-amber-100 dark:bg-amber-900/30') }} flex items-center justify-center">
                        <svg class="w-6 h-6 {{ $resumeInspection && $resumeInspection->isApproved() ? 'text-green-600 dark:text-green-400' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Resume Submission</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Step 1: Submit for inspection</p>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                @if($resumeInspection && $resumeInspection->isApproved())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approved
                    </span>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Your resume has been approved. You can proceed with placement applications.</p>
                @elseif($resumeInspection && $resumeInspection->isRevisionRequired())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Revision Required
                    </span>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Please review coordinator comments and resubmit.</p>
                @elseif($resumeInspection && $resumeInspection->resume_file_path)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending Review
                    </span>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Your resume is being reviewed by the coordinator.</p>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Not Submitted
                    </span>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Submit your resume to begin the placement process.</p>
                @endif
            </div>
            <a href="{{ route('student.resume.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-[#003A6C] hover:bg-[#0084C5] text-white font-semibold rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ $resumeInspection && $resumeInspection->resume_file_path ? 'View Resume Status' : 'Submit Resume' }}
            </a>
        </div>

        <!-- Placement Tracking Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 {{ $placementTracking && $placementTracking->status === 'SCL_RELEASED' ? 'border-green-500' : ($placementTracking && in_array($placementTracking->status, ['ACCEPTED', 'OFFER_RECEIVED', 'INTERVIEWED']) ? 'border-blue-500' : 'border-amber-500') }}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl {{ $placementTracking && $placementTracking->status === 'SCL_RELEASED' ? 'bg-green-100 dark:bg-green-900/30' : ($placementTracking && in_array($placementTracking->status, ['ACCEPTED', 'OFFER_RECEIVED', 'INTERVIEWED']) ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-amber-100 dark:bg-amber-900/30') }} flex items-center justify-center">
                        <svg class="w-6 h-6 {{ $placementTracking && $placementTracking->status === 'SCL_RELEASED' ? 'text-green-600 dark:text-green-400' : ($placementTracking && in_array($placementTracking->status, ['ACCEPTED', 'OFFER_RECEIVED', 'INTERVIEWED']) ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Placement Tracking</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Step 2: Track your placement</p>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                @php
                    $statusLabels = [
                        'NOT_APPLIED' => ['label' => 'Not Started', 'color' => 'amber'],
                        'SAL_RELEASED' => ['label' => 'SAL Released', 'color' => 'blue'],
                        'APPLIED' => ['label' => 'Applied', 'color' => 'blue'],
                        'INTERVIEWED' => ['label' => 'Interviewed', 'color' => 'blue'],
                        'OFFER_RECEIVED' => ['label' => 'Offer Received', 'color' => 'blue'],
                        'ACCEPTED' => ['label' => 'Accepted', 'color' => 'green'],
                        'SCL_RELEASED' => ['label' => 'Completed', 'color' => 'green'],
                    ];
                    $currentStatus = $placementTracking->status ?? 'NOT_APPLIED';
                    $statusInfo = $statusLabels[$currentStatus] ?? ['label' => 'Unknown', 'color' => 'gray'];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $statusInfo['color'] }}-100 text-{{ $statusInfo['color'] }}-800 dark:bg-{{ $statusInfo['color'] }}-900/30 dark:text-{{ $statusInfo['color'] }}-400">
                    @if($statusInfo['color'] === 'green')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                    {{ $statusInfo['label'] }}
                </span>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    @if(!$resumeInspection || !$resumeInspection->isApproved())
                        Resume approval required before applying.
                    @elseif($currentStatus === 'NOT_APPLIED')
                        Start applying to companies for your placement.
                    @elseif($currentStatus === 'SCL_RELEASED')
                        Congratulations! Your placement is complete.
                    @else
                        Continue tracking your placement progress.
                    @endif
                </p>
            </div>
            <a href="{{ route('placement.student.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-[#003A6C] hover:bg-[#0084C5] text-white font-semibold rounded-lg transition-colors {{ !$resumeInspection || !$resumeInspection->isApproved() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$resumeInspection || !$resumeInspection->isApproved() ? 'disabled' : '' }}>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Track Placement
            </a>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-[#003A6C]">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Access important features</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('student.fyp.overview') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">FYP Overview</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('student.ppe.overview') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">PPE Overview</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('student.li.overview') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">LI Overview</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('students.profile.edit', $student) }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">My Profile</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Assessment Timeline Section -->
    @if($assessmentWindows->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#003A6C] to-[#0084C5] flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">Assessment Timeline</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Track evaluation periods for all modules</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($assessmentWindows->take(6) as $window)
                @php
                    $statusColors = [
                        'open' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-800 dark:text-green-400', 'border' => 'border-green-500', 'icon' => 'text-green-500'],
                        'upcoming' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-800 dark:text-blue-400', 'border' => 'border-blue-500', 'icon' => 'text-blue-500'],
                        'closed' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400', 'border' => 'border-gray-400', 'icon' => 'text-gray-400'],
                        'disabled' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-500 dark:text-gray-500', 'border' => 'border-gray-300', 'icon' => 'text-gray-400'],
                    ];
                    $colors = $statusColors[$window->status] ?? $statusColors['closed'];
                @endphp
                <div class="border-l-4 {{ $colors['border'] }} bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider {{ $colors['text'] }}">{{ $window->module }}</span>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ ucfirst(str_replace('_', ' ', $window->evaluator_role)) }} Evaluation</h4>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
                            @if($window->status === 'open')
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                            @endif
                            {{ $window->status_label }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                        @if($window->start_at)
                            <p><span class="font-medium">Start:</span> {{ $window->start_at->format('d M Y, H:i') }}</p>
                        @endif
                        @if($window->end_at)
                            <p><span class="font-medium">End:</span> {{ $window->end_at->format('d M Y, H:i') }}</p>
                        @endif
                        @if($window->status === 'open' && $window->end_at)
                            @php
                                $daysLeft = now()->diffInDays($window->end_at, false);
                            @endphp
                            @if($daysLeft > 0)
                                <p class="text-amber-600 dark:text-amber-400 font-medium">{{ $daysLeft }} days remaining</p>
                            @elseif($daysLeft === 0)
                                <p class="text-red-600 dark:text-red-400 font-medium">Ends today!</p>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($assessmentWindows->count() > 6)
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">And {{ $assessmentWindows->count() - 6 }} more assessment windows...</p>
            </div>
        @endif
    </div>
    @endif

    <!-- Course Progress Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#0084C5] to-[#00AEEF] flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">Course Progress Summary</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your scores across all WBL modules</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- PPE Score Card -->
            <div class="bg-gradient-to-br from-[#003A6C] to-[#0084C5] rounded-xl p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">PPE</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['PPE']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">AT: {{ number_format($courseScores['PPE']['at_score'], 1) }} | IC: {{ number_format($courseScores['PPE']['ic_score'], 1) }}</p>
            </div>

            <!-- FYP Score Card -->
            <div class="bg-gradient-to-br from-[#0084C5] to-[#00AEEF] rounded-xl p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">FYP</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['FYP']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['FYP']['max'] }}%</p>
            </div>

            <!-- IP Score Card -->
            <div class="bg-gradient-to-br from-[#00AEEF] to-[#66C3FF] rounded-xl p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">IP</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['IP']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['IP']['max'] }}%</p>
            </div>

            <!-- OSH Score Card -->
            <div class="bg-gradient-to-br from-[#003A6C] to-[#00AEEF] rounded-xl p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">OSH</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['OSH']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['OSH']['max'] }}%</p>
            </div>

            <!-- LI Score Card -->
            <div class="bg-gradient-to-br from-[#0084C5] to-[#66C3FF] rounded-xl p-4 text-white">
                <h3 class="text-sm font-semibold mb-2 opacity-90">LI</h3>
                <p class="text-3xl font-bold mb-1">{{ number_format($courseScores['LI']['score'], 1) }}%</p>
                <p class="text-xs opacity-75">Max: {{ $courseScores['LI']['max'] }}%</p>
            </div>
        </div>
    </div>

    <!-- Charts and Supervisors Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Bar Chart - Course Scores Comparison -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Course Scores Overview</h3>
            <div style="height: 280px;">
                <canvas id="courseScoresChart"></canvas>
            </div>
        </div>

        <!-- Assigned Supervisors Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Your Supervisors & Lecturers</h3>
            <div class="space-y-3">
                <!-- Academic Tutor -->
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Academic Tutor (FYP)</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignedAt->name ?? 'Not Assigned' }}</p>
                        </div>
                    </div>
                    @if($assignedAt)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Assigned</span>
                    @else
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                    @endif
                </div>

                <!-- Industry Coach -->
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#0084C5]/10 dark:bg-[#0084C5]/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Industry Coach (IC)</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignedIc->name ?? 'Not Assigned' }}</p>
                        </div>
                    </div>
                    @if($assignedIc)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Assigned</span>
                    @else
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                    @endif
                </div>

                <!-- Supervisor LI -->
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#00AEEF]/10 dark:bg-[#00AEEF]/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#00AEEF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Supervisor LI</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignedSupervisorLi->name ?? 'Not Assigned' }}</p>
                        </div>
                    </div>
                    @if($assignedSupervisorLi)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Assigned</span>
                    @else
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                    @endif
                </div>

                <!-- PPE Lecturer -->
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PPE Lecturer</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignedPpeLecturer->name ?? 'Not Assigned' }}</p>
                        </div>
                    </div>
                    @if($assignedPpeLecturer)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Assigned</span>
                    @else
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                    @endif
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
    </script>
    @endpush
    @endif
    @endif
</div>
@endsection
