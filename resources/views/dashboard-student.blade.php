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
    <!-- Priority Actions Section - Vibrant Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Resume Submission Card -->
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border-l-4 {{ $resumeInspection && $resumeInspection->isApproved() ? 'border-emerald-500' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'border-rose-500' : 'border-amber-500') }} relative overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute top-0 right-0 w-24 h-24 {{ $resumeInspection && $resumeInspection->isApproved() ? 'bg-emerald-500/10' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'bg-rose-500/10' : 'bg-amber-500/10') }} rounded-full -translate-y-1/2 translate-x-1/2"></div>

            <div class="relative">
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl {{ $resumeInspection && $resumeInspection->isApproved() ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : ($resumeInspection && $resumeInspection->isRevisionRequired() ? 'bg-gradient-to-br from-rose-400 to-pink-500' : 'bg-gradient-to-br from-amber-400 to-orange-500') }} flex items-center justify-center shadow-lg float-animation">
                            <span class="text-2xl">📄</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Resume</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Step 1: Get it approved!</p>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    @if($resumeInspection && $resumeInspection->isApproved())
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-md">
                            <span>✅</span> Approved
                        </span>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">Awesome! Your resume is approved. Start applying now! 🎉</p>
                    @elseif($resumeInspection && $resumeInspection->isRevisionRequired())
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-rose-500 to-pink-500 text-white shadow-md">
                            <span>🔄</span> Needs Revision
                        </span>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">Check the feedback and make it even better! 💪</p>
                    @elseif($resumeInspection && $resumeInspection->resume_file_path)
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-md pulse-soft">
                            <span>⏳</span> Under Review
                        </span>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">Hang tight! Your resume is being reviewed.</p>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md">
                            <span>📝</span> Not Submitted
                        </span>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">Upload your resume to kick off your journey!</p>
                    @endif
                </div>

                <a href="{{ route('student.resume.index') }}" class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">
                    <span class="mr-2">{{ $resumeInspection && $resumeInspection->resume_file_path ? '👀 View Status' : '📤 Submit Resume' }}</span>
                </a>
            </div>
        </div>

        <!-- Placement Tracking Card -->
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border-l-4 {{ $placementTracking && $placementTracking->status === 'SCL_RELEASED' ? 'border-emerald-500' : ($placementTracking && in_array($placementTracking->status, ['ACCEPTED', 'OFFER_RECEIVED', 'INTERVIEWED']) ? 'border-blue-500' : 'border-violet-500') }} relative overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute top-0 right-0 w-24 h-24 {{ $placementTracking && $placementTracking->status === 'SCL_RELEASED' ? 'bg-emerald-500/10' : ($placementTracking && in_array($placementTracking->status, ['ACCEPTED', 'OFFER_RECEIVED', 'INTERVIEWED']) ? 'bg-blue-500/10' : 'bg-violet-500/10') }} rounded-full -translate-y-1/2 translate-x-1/2"></div>

            <div class="relative">
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl {{ $placementTracking && $placementTracking->status === 'SCL_RELEASED' ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : ($placementTracking && in_array($placementTracking->status, ['ACCEPTED', 'OFFER_RECEIVED', 'INTERVIEWED']) ? 'bg-gradient-to-br from-blue-400 to-indigo-500' : 'bg-gradient-to-br from-violet-400 to-purple-500') }} flex items-center justify-center shadow-lg float-animation">
                            <span class="text-2xl">💼</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Placement</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Step 2: Land that role!</p>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    @php
                        $statusLabels = [
                            'NOT_APPLIED' => ['label' => 'Not Started', 'emoji' => '🎯', 'gradient' => 'from-violet-500 to-purple-500'],
                            'SAL_RELEASED' => ['label' => 'SAL Released', 'emoji' => '📋', 'gradient' => 'from-blue-500 to-indigo-500'],
                            'APPLIED' => ['label' => 'Applied', 'emoji' => '📨', 'gradient' => 'from-blue-500 to-cyan-500'],
                            'INTERVIEWED' => ['label' => 'Interviewed', 'emoji' => '🎤', 'gradient' => 'from-indigo-500 to-purple-500'],
                            'OFFER_RECEIVED' => ['label' => 'Offer Received', 'emoji' => '🎁', 'gradient' => 'from-pink-500 to-rose-500'],
                            'ACCEPTED' => ['label' => 'Accepted', 'emoji' => '🤝', 'gradient' => 'from-emerald-500 to-teal-500'],
                            'SCL_RELEASED' => ['label' => 'Completed', 'emoji' => '🏆', 'gradient' => 'from-emerald-500 to-green-500'],
                        ];
                        $currentStatus = $placementTracking->status ?? 'NOT_APPLIED';
                        $statusInfo = $statusLabels[$currentStatus] ?? ['label' => 'Unknown', 'emoji' => '❓', 'gradient' => 'from-gray-500 to-gray-600'];
                    @endphp
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r {{ $statusInfo['gradient'] }} text-white shadow-md">
                        <span>{{ $statusInfo['emoji'] }}</span> {{ $statusInfo['label'] }}
                    </span>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                        @if(!$resumeInspection || !$resumeInspection->isApproved())
                            Get your resume approved first! 📄
                        @elseif($currentStatus === 'NOT_APPLIED')
                            Time to explore and apply to companies! 🚀
                        @elseif($currentStatus === 'SCL_RELEASED')
                            You did it! Placement complete! 🎊
                        @else
                            Keep pushing! You're making progress! 💪
                        @endif
                    </p>
                </div>

                <a href="{{ route('student.placement.index') }}" class="inline-flex items-center justify-center w-full px-5 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg {{ !$resumeInspection || !$resumeInspection->isApproved() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !$resumeInspection || !$resumeInspection->isApproved() ? 'disabled' : '' }}>
                    <span class="mr-2">📊 Track Progress</span>
                </a>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card-hover bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border-l-4 border-cyan-500 relative overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>

            <div class="relative">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center shadow-lg float-animation">
                        <span class="text-2xl">⚡</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Quick Access</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jump to your modules</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('student.fyp.overview') }}" class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-slate-50 to-purple-50 dark:from-gray-700 dark:to-purple-900/20 hover:from-purple-100 hover:to-pink-100 dark:hover:from-gray-600 dark:hover:to-purple-800/30 transition-all duration-300 group">
                        <span class="flex items-center gap-3">
                            <span class="text-xl">🎓</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">FYP Overview</span>
                        </span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('student.ppe.overview') }}" class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-slate-50 to-blue-50 dark:from-gray-700 dark:to-blue-900/20 hover:from-blue-100 hover:to-cyan-100 dark:hover:from-gray-600 dark:hover:to-blue-800/30 transition-all duration-300 group">
                        <span class="flex items-center gap-3">
                            <span class="text-xl">📊</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">PPE Overview</span>
                        </span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('student.li.overview') }}" class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-slate-50 to-teal-50 dark:from-gray-700 dark:to-teal-900/20 hover:from-teal-100 hover:to-emerald-100 dark:hover:from-gray-600 dark:hover:to-teal-800/30 transition-all duration-300 group">
                        <span class="flex items-center gap-3">
                            <span class="text-xl">🔗</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">LI Overview</span>
                        </span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-teal-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('students.profile.edit', $student) }}" class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-slate-50 to-pink-50 dark:from-gray-700 dark:to-pink-900/20 hover:from-pink-100 hover:to-rose-100 dark:hover:from-gray-600 dark:hover:to-pink-800/30 transition-all duration-300 group">
                        <span class="flex items-center gap-3">
                            <span class="text-xl">👤</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">My Profile</span>
                        </span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
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
