@extends('layouts.app')

@section('title', 'My Dashboard')

@push('styles')
<style>
    /* Animated gradient background */
    .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Card hover effects */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
    }

    /* Floating animation for icons */
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }

    /* Pulse animation */
    .pulse-soft {
        animation: pulseSoft 2s ease-in-out infinite;
    }

    @keyframes pulseSoft {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Shimmer effect for score cards */
    .shimmer {
        position: relative;
        overflow: hidden;
    }
    .shimmer::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    /* Progress ring animation */
    .progress-ring {
        transition: stroke-dashoffset 0.5s ease;
    }

    /* Emoji bounce */
    .emoji-bounce {
        display: inline-block;
        animation: emojiBounce 2s ease infinite;
    }

    @keyframes emojiBounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-purple-900/20 dark:to-gray-900 -mx-10 -my-6 px-10 py-6">
    @if(isset($needsProfile) && $needsProfile)
        <!-- Profile Required Message - Vibrant Version -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-6 border border-purple-100 dark:border-purple-800">
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center mx-auto mb-6 float-animation">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-3">Let's Get Started! <span class="emoji-bounce">🚀</span></h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">Create your student profile to unlock your personalized dashboard</p>
                <a href="{{ route('students.profile.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create My Profile
                </a>
            </div>
        </div>
    @else
    <!-- Vibrant Welcome Header -->
    <div class="hero-gradient rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
        <!-- Decorative circles -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-2">
                <span class="text-4xl emoji-bounce">👋</span>
                <h1 class="text-3xl md:text-4xl font-bold">Hey, {{ $student->name ?? 'Student' }}!</h1>
            </div>
            <p class="text-white/90 text-lg ml-14">Ready to crush your WBL journey today? Let's make it happen! ✨</p>

            <!-- Quick Stats -->
            <div class="flex flex-wrap gap-4 mt-6 ml-14">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 flex items-center gap-2">
                    <span class="text-2xl">📚</span>
                    <span class="font-semibold">5 Modules</span>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 flex items-center gap-2">
                    <span class="text-2xl">🎯</span>
                    <span class="font-semibold">Track Progress</span>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 flex items-center gap-2">
                    <span class="text-2xl">💼</span>
                    <span class="font-semibold">Get Placed</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Follow-up Reminders Notification --}}
    @if(isset($followUpReminders) && $followUpReminders->count() > 0)
        @php
            $overdueCount = $followUpReminders->where('is_overdue', true)->count();
            $todayCount = $followUpReminders->where('is_today', true)->count();
            $upcomingCount = $followUpReminders->where('is_upcoming', true)->count();
        @endphp
        @if($overdueCount > 0 || $todayCount > 0 || $upcomingCount > 0)
        <div class="mb-8">
            <div class="bg-gradient-to-r {{ $overdueCount > 0 ? 'from-red-500 to-orange-500' : ($todayCount > 0 ? 'from-amber-500 to-yellow-500' : 'from-blue-500 to-cyan-500') }} rounded-2xl shadow-lg p-5 text-white">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center float-animation">
                            <span class="text-2xl">{{ $overdueCount > 0 ? '⚠️' : ($todayCount > 0 ? '📅' : '🔔') }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold mb-2">
                            @if($overdueCount > 0)
                                Heads up! Company Follow-up Overdue 🏃
                            @elseif($todayCount > 0)
                                Today's the Day! Follow-up Due 📞
                            @else
                                Upcoming Follow-ups 📌
                            @endif
                        </h3>
                        <div class="space-y-2">
                            @foreach($followUpReminders->take(3) as $reminder)
                                <div class="flex items-center justify-between bg-white/10 rounded-lg px-3 py-2">
                                    <span class="font-medium">
                                        {{ $reminder->company_name }}
                                        @if($reminder->follow_up_notes)
                                            <span class="opacity-75 text-sm">- {{ Str::limit($reminder->follow_up_notes, 30) }}</span>
                                        @endif
                                    </span>
                                    <span class="text-sm font-semibold bg-white/20 px-2 py-1 rounded-full">
                                        @if($reminder->is_overdue)
                                            {{ $reminder->follow_up_date->diffForHumans() }}
                                        @elseif($reminder->is_today)
                                            Today
                                        @else
                                            {{ $reminder->follow_up_date->format('d M') }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                            @if($followUpReminders->count() > 3)
                                <p class="text-sm opacity-75 mt-2">+{{ $followUpReminders->count() - 3 }} more follow-ups</p>
                            @endif
                        </div>
                        <a href="{{ route('student.placement.index') }}" class="inline-flex items-center gap-2 mt-3 bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg font-semibold transition-colors">
                            View Placement Tracking
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    @if($student)
    <!-- Resume & Poster Submission Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-purple-500/10 to-indigo-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">Resume & Poster Submission</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Step 1: Get your documents approved before placement</p>
                    </div>
                </div>
                <a href="{{ route('student.resume.index') }}" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    {{ $resumeInspection && $resumeInspection->resume_file_path ? 'View Submission' : 'Submit Now' }}
                </a>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">
                        @if($resumeInspection && $resumeInspection->resume_file_path)
                            1
                        @else
                            0
                        @endif
                    </div>
                    <div class="text-xs text-purple-700 dark:text-purple-400">Documents</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        @if($resumeInspection && $resumeInspection->isPending())
                            1
                        @else
                            0
                        @endif
                    </div>
                    <div class="text-xs text-blue-700 dark:text-blue-400">Under Review</div>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600">
                        @if($resumeInspection && $resumeInspection->isRevisionRequired())
                            1
                        @else
                            0
                        @endif
                    </div>
                    <div class="text-xs text-orange-700 dark:text-orange-400">Needs Revision</div>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-600">
                        @if($resumeInspection && $resumeInspection->isApproved())
                            1
                        @else
                            0
                        @endif
                    </div>
                    <div class="text-xs text-emerald-700 dark:text-emerald-400">Approved</div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-gradient-to-r {{ $resumeInspection && $resumeInspection->isApproved() ? 'from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border-emerald-200 dark:border-emerald-800' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border-orange-200 dark:border-orange-800' : ($resumeInspection && $resumeInspection->resume_file_path ? 'from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-blue-200 dark:border-blue-800' : 'from-gray-50 to-slate-50 dark:from-gray-700/50 dark:to-slate-700/50 border-gray-200 dark:border-gray-700')) }} rounded-xl p-4 border">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl {{ $resumeInspection && $resumeInspection->isApproved() ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'bg-gradient-to-br from-orange-400 to-amber-500' : ($resumeInspection && $resumeInspection->resume_file_path ? 'bg-gradient-to-br from-blue-400 to-indigo-500' : 'bg-gradient-to-br from-gray-400 to-slate-500')) }} flex items-center justify-center shadow-md">
                        @if($resumeInspection && $resumeInspection->isApproved())
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @elseif($resumeInspection && $resumeInspection->isRevisionRequired())
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        @elseif($resumeInspection && $resumeInspection->resume_file_path)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 dark:text-white">
                            @if($resumeInspection && $resumeInspection->isApproved())
                                Approved - Ready for Placement!
                            @elseif($resumeInspection && $resumeInspection->isRevisionRequired())
                                Revision Required
                            @elseif($resumeInspection && $resumeInspection->resume_file_path)
                                Under Review
                            @else
                                Not Submitted Yet
                            @endif
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            @if($resumeInspection && $resumeInspection->isApproved())
                                Your resume & poster have been approved. You can now proceed with placement applications.
                            @elseif($resumeInspection && $resumeInspection->isRevisionRequired())
                                Please check coordinator feedback and resubmit your revised documents.
                            @elseif($resumeInspection && $resumeInspection->resume_file_path)
                                Your submission is being reviewed. This typically takes 3-5 business days.
                            @else
                                Upload your merged Resume + Posters (PD3, PD4, PD5) as ONE PDF file.
                            @endif
                        </p>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full {{ $resumeInspection && $resumeInspection->isApproved() ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : ($resumeInspection && $resumeInspection->resume_file_path ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')) }}">
                        {{ $resumeInspection ? $resumeInspection->status_display ?? 'Pending' : 'Not Started' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Workplace Issues Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-red-500/10 to-orange-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">Workplace Issues</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Report safety, health, or wellbeing concerns</p>
                    </div>
                </div>
                <a href="{{ route('workplace-issues.create') }}" class="px-4 py-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white font-semibold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Report Issue
                </a>
            </div>

            <!-- Summary Cards -->
            @php
                $workplaceStats = $workplaceIssueStats ?? ['total' => 0, 'new' => 0, 'in_progress' => 0, 'resolved' => 0];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $workplaceStats['total'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Reports</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $workplaceStats['new'] }}</div>
                    <div class="text-xs text-purple-700 dark:text-purple-400">New</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $workplaceStats['in_progress'] }}</div>
                    <div class="text-xs text-yellow-700 dark:text-yellow-400">In Progress</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $workplaceStats['resolved'] }}</div>
                    <div class="text-xs text-green-700 dark:text-green-400">Resolved</div>
                </div>
            </div>

            <!-- Info Card -->
            @if($workplaceStats['total'] == 0)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 dark:text-white">No Issues Reported</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                If you encounter any workplace safety, health, or wellbeing concerns during your placement, you can report them here confidentially.
                            </p>
                        </div>
                        <a href="{{ route('workplace-issues.index') }}" class="px-4 py-2 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold text-sm">
                            View All Reports →
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center pt-2">
                    <a href="{{ route('workplace-issues.index') }}" class="text-[#0084C5] hover:underline text-sm font-medium">
                        View all workplace issue reports →
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- My Courses Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-cyan-500/10 to-blue-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative">
            <!-- Header -->
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">My Courses</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Track your progress across all WBL modules</p>
                </div>
            </div>

            <!-- Course Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- PPE Card -->
                <a href="{{ route('student.ppe.overview') }}" class="card-hover bg-gradient-to-br from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-xl p-5 border border-violet-200 dark:border-violet-800 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-md">
                                <span class="text-lg">📊</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">PPE</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Professional Practice</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-violet-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Current Score</span>
                            <span class="font-bold text-violet-600">{{ number_format($courseScores['PPE']['score'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-violet-100 dark:bg-violet-900/30 rounded-full h-2">
                            <div class="bg-gradient-to-r from-violet-500 to-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($courseScores['PPE']['score'], 100) }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>AT: {{ number_format($courseScores['PPE']['at_score'], 1) }}%</span>
                            <span>IC: {{ number_format($courseScores['PPE']['ic_score'], 1) }}%</span>
                        </div>
                    </div>
                </a>

                <!-- FYP Card -->
                <a href="{{ route('student.fyp.overview') }}" class="card-hover bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                                <span class="text-lg">🎓</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">FYP</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Final Year Project</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Current Score</span>
                            <span class="font-bold text-blue-600">{{ number_format($courseScores['FYP']['score'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-blue-100 dark:bg-blue-900/30 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($courseScores['FYP']['score'], 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <span>Max: {{ $courseScores['FYP']['max'] }}%</span>
                        </div>
                    </div>
                </a>

                <!-- IP Card -->
                <a href="#" class="card-hover bg-gradient-to-br from-cyan-50 to-teal-50 dark:from-cyan-900/20 dark:to-teal-900/20 rounded-xl p-5 border border-cyan-200 dark:border-cyan-800 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 flex items-center justify-center shadow-md">
                                <span class="text-lg">🏭</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">IP</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Industrial Project</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-cyan-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Current Score</span>
                            <span class="font-bold text-cyan-600">{{ number_format($courseScores['IP']['score'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-cyan-100 dark:bg-cyan-900/30 rounded-full h-2">
                            <div class="bg-gradient-to-r from-cyan-500 to-teal-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($courseScores['IP']['score'], 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <span>Max: {{ $courseScores['IP']['max'] }}%</span>
                        </div>
                    </div>
                </a>

                <!-- OSH Card -->
                <a href="#" class="card-hover bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center shadow-md">
                                <span class="text-lg">🦺</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">OSH</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Safety & Health</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Current Score</span>
                            <span class="font-bold text-orange-600">{{ number_format($courseScores['OSH']['score'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-orange-100 dark:bg-orange-900/30 rounded-full h-2">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($courseScores['OSH']['score'], 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <span>Max: {{ $courseScores['OSH']['max'] }}%</span>
                        </div>
                    </div>
                </a>

                <!-- LI Card -->
                <a href="{{ route('student.li.overview') }}" class="card-hover bg-gradient-to-br from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 rounded-xl p-5 border border-rose-200 dark:border-rose-800 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-md">
                                <span class="text-lg">🔗</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">LI</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Learning Integration</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-rose-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Current Score</span>
                            <span class="font-bold text-rose-600">{{ number_format($courseScores['LI']['score'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-rose-100 dark:bg-rose-900/30 rounded-full h-2">
                            <div class="bg-gradient-to-r from-rose-500 to-pink-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($courseScores['LI']['score'], 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <span>Max: {{ $courseScores['LI']['max'] }}%</span>
                        </div>
                    </div>
                </a>

                <!-- Placement Card -->
                <a href="{{ route('student.placement.index') }}" class="card-hover bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-5 border border-emerald-200 dark:border-emerald-800 hover:shadow-lg transition-all duration-300 group {{ !$resumeInspection || !$resumeInspection->isApproved() ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-md">
                                <span class="text-lg">💼</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Placement</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Track Applications</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        @php
                            $placementStatusLabels = [
                                'NOT_APPLIED' => ['label' => 'Not Started', 'color' => 'text-gray-600'],
                                'SAL_RELEASED' => ['label' => 'SAL Released', 'color' => 'text-blue-600'],
                                'APPLIED' => ['label' => 'Applied', 'color' => 'text-blue-600'],
                                'INTERVIEWED' => ['label' => 'Interviewed', 'color' => 'text-indigo-600'],
                                'OFFER_RECEIVED' => ['label' => 'Offer Received', 'color' => 'text-pink-600'],
                                'ACCEPTED' => ['label' => 'Accepted', 'color' => 'text-emerald-600'],
                                'SCL_RELEASED' => ['label' => 'Hired', 'color' => 'text-green-600'],
                            ];
                            $pStatus = $placementTracking->status ?? 'NOT_APPLIED';
                            $pStatusInfo = $placementStatusLabels[$pStatus] ?? ['label' => 'Unknown', 'color' => 'text-gray-600'];
                        @endphp
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Status</span>
                            <span class="font-bold {{ $pStatusInfo['color'] }}">{{ $pStatusInfo['label'] }}</span>
                        </div>
                        @if(!$resumeInspection || !$resumeInspection->isApproved())
                            <p class="text-xs text-amber-600 dark:text-amber-400">Resume approval required</p>
                        @else
                            <p class="text-xs text-emerald-600 dark:text-emerald-400">Ready to track applications</p>
                        @endif
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Assessment Timeline Section -->
    @if($assessmentWindows->count() > 0)
    <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8 relative overflow-hidden">
        <!-- Decorative background -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <span class="text-2xl">📅</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Assessment Timeline</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Stay on top of your evaluations! 🎯</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($assessmentWindows->take(6) as $window)
                    @php
                        $statusStyles = [
                            'open' => ['gradient' => 'from-emerald-500 to-teal-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'emoji' => '🟢'],
                            'upcoming' => ['gradient' => 'from-blue-500 to-indigo-500', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'emoji' => '🔵'],
                            'closed' => ['gradient' => 'from-gray-400 to-gray-500', 'bg' => 'bg-gray-50 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-400', 'emoji' => '⚪'],
                            'disabled' => ['gradient' => 'from-gray-300 to-gray-400', 'bg' => 'bg-gray-50 dark:bg-gray-700', 'text' => 'text-gray-500 dark:text-gray-500', 'emoji' => '⚫'],
                        ];
                        $style = $statusStyles[$window->status] ?? $statusStyles['closed'];
                    @endphp
                    <div class="card-hover {{ $style['bg'] }} rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider {{ $style['text'] }} mb-1">
                                    {{ $style['emoji'] }} {{ $window->module }}
                                </span>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $window->evaluator_role)) }} Evaluation</h4>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r {{ $style['gradient'] }} text-white shadow-sm">
                                @if($window->status === 'open')
                                    <span class="w-1.5 h-1.5 rounded-full bg-white mr-1.5 animate-pulse"></span>
                                @endif
                                {{ $window->status_label }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                            @if($window->start_at)
                                <p class="flex items-center gap-1"><span>🗓️</span> Start: {{ $window->start_at->format('d M Y, H:i') }}</p>
                            @endif
                            @if($window->end_at)
                                <p class="flex items-center gap-1"><span>🏁</span> End: {{ $window->end_at->format('d M Y, H:i') }}</p>
                            @endif
                            @if($window->status === 'open' && $window->end_at)
                                @php
                                    $daysLeft = now()->diffInDays($window->end_at, false);
                                @endphp
                                @if($daysLeft > 0)
                                    <p class="font-bold text-amber-600 dark:text-amber-400 mt-2">⏰ {{ $daysLeft }} days left!</p>
                                @elseif($daysLeft === 0)
                                    <p class="font-bold text-rose-600 dark:text-rose-400 mt-2">🔥 Ends today!</p>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($assessmentWindows->count() > 6)
                <div class="mt-6 text-center">
                    <span class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-full">
                        <span>📋</span> And {{ $assessmentWindows->count() - 6 }} more assessment windows...
                    </span>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Course Progress Summary - Vibrant Score Cards -->
    <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8 relative overflow-hidden">
        <!-- Decorative background -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-pink-500/10 to-orange-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-500 to-orange-500 flex items-center justify-center shadow-lg">
                    <span class="text-2xl">📈</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-orange-600 bg-clip-text text-transparent">Your Scores</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">You're doing great! Keep it up! 🌟</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- PPE Score Card -->
                <div class="shimmer bg-gradient-to-br from-violet-500 via-purple-500 to-fuchsia-500 rounded-2xl p-5 text-white shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">📊</span>
                        <h3 class="text-sm font-bold opacity-90">PPE</h3>
                    </div>
                    <p class="text-4xl font-black mb-1">{{ number_format($courseScores['PPE']['score'], 1) }}<span class="text-lg">%</span></p>
                    <p class="text-xs opacity-80">AT: {{ number_format($courseScores['PPE']['at_score'], 1) }} | IC: {{ number_format($courseScores['PPE']['ic_score'], 1) }}</p>
                </div>

                <!-- FYP Score Card -->
                <div class="shimmer bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-500 rounded-2xl p-5 text-white shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">🎓</span>
                        <h3 class="text-sm font-bold opacity-90">FYP</h3>
                    </div>
                    <p class="text-4xl font-black mb-1">{{ number_format($courseScores['FYP']['score'], 1) }}<span class="text-lg">%</span></p>
                    <p class="text-xs opacity-80">Max: {{ $courseScores['FYP']['max'] }}%</p>
                </div>

                <!-- IP Score Card -->
                <div class="shimmer bg-gradient-to-br from-cyan-500 via-teal-500 to-emerald-500 rounded-2xl p-5 text-white shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">🏭</span>
                        <h3 class="text-sm font-bold opacity-90">IP</h3>
                    </div>
                    <p class="text-4xl font-black mb-1">{{ number_format($courseScores['IP']['score'], 1) }}<span class="text-lg">%</span></p>
                    <p class="text-xs opacity-80">Max: {{ $courseScores['IP']['max'] }}%</p>
                </div>

                <!-- OSH Score Card -->
                <div class="shimmer bg-gradient-to-br from-orange-500 via-amber-500 to-yellow-500 rounded-2xl p-5 text-white shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">🦺</span>
                        <h3 class="text-sm font-bold opacity-90">OSH</h3>
                    </div>
                    <p class="text-4xl font-black mb-1">{{ number_format($courseScores['OSH']['score'], 1) }}<span class="text-lg">%</span></p>
                    <p class="text-xs opacity-80">Max: {{ $courseScores['OSH']['max'] }}%</p>
                </div>

                <!-- LI Score Card -->
                <div class="shimmer bg-gradient-to-br from-rose-500 via-pink-500 to-fuchsia-500 rounded-2xl p-5 text-white shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">🔗</span>
                        <h3 class="text-sm font-bold opacity-90">LI</h3>
                    </div>
                    <p class="text-4xl font-black mb-1">{{ number_format($courseScores['LI']['score'], 1) }}<span class="text-lg">%</span></p>
                    <p class="text-xs opacity-80">Max: {{ $courseScores['LI']['max'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Supervisors Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Bar Chart - Course Scores Comparison -->
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-2xl">📊</span>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Score Overview</h3>
                </div>
                <div style="height: 280px;">
                    <canvas id="courseScoresChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Assigned Supervisors Card -->
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-2xl">👥</span>
                    <h3 class="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Your Mentors</h3>
                </div>

                <div class="space-y-3">
                    <!-- Academic Tutor -->
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border border-purple-100 dark:border-purple-800">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center shadow-md">
                                <span class="text-lg">🎓</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-purple-600 dark:text-purple-400">Academic Tutor (FYP)</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $assignedAt->name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>
                        @if($assignedAt)
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-sm">✓ Assigned</span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                        @endif
                    </div>

                    <!-- Industry Coach -->
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-md">
                                <span class="text-lg">💼</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400">Industry Coach (IC)</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $assignedIc->name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>
                        @if($assignedIc)
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-sm">✓ Assigned</span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                        @endif
                    </div>

                    <!-- Supervisor LI -->
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-teal-50 to-emerald-50 dark:from-teal-900/20 dark:to-emerald-900/20 border border-teal-100 dark:border-teal-800">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center shadow-md">
                                <span class="text-lg">🔗</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-teal-600 dark:text-teal-400">Supervisor LI</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $assignedSupervisorLi->name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>
                        @if($assignedSupervisorLi)
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-sm">✓ Assigned</span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                        @endif
                    </div>

                    <!-- PPE Lecturer -->
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 border border-orange-100 dark:border-orange-800">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center shadow-md">
                                <span class="text-lg">📚</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-orange-600 dark:text-orange-400">PPE Lecturer</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $assignedPpeLecturer->name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>
                        @if($assignedPpeLecturer)
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-sm">✓ Assigned</span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @push('scripts')
    <script>
        // Vibrant Color Palette for Students
        const VIBRANT_COLORS = {
            purple: '#8B5CF6',
            indigo: '#6366F1',
            blue: '#3B82F6',
            cyan: '#06B6D4',
            teal: '#14B8A6',
            emerald: '#10B981',
            pink: '#EC4899',
            rose: '#F43F5E',
            orange: '#F97316',
            amber: '#F59E0B',
        };

        // Bar Chart - Course Scores with vibrant colors
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
                            'rgba(139, 92, 246, 0.8)',  // purple
                            'rgba(59, 130, 246, 0.8)',  // blue
                            'rgba(20, 184, 166, 0.8)',  // teal
                            'rgba(249, 115, 22, 0.8)',  // orange
                            'rgba(236, 72, 153, 0.8)',  // pink
                        ],
                        borderColor: [
                            'rgb(139, 92, 246)',
                            'rgb(59, 130, 246)',
                            'rgb(20, 184, 166)',
                            'rgb(249, 115, 22)',
                            'rgb(236, 72, 153)',
                        ],
                        borderWidth: 2,
                        borderRadius: 12,
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
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 14,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            cornerRadius: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return '🎯 Score: ' + context.parsed.y.toFixed(1) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.2)',
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#6B7280',
                                font: {
                                    size: 12,
                                    weight: '500'
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
                                    size: 12,
                                    weight: '600'
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
