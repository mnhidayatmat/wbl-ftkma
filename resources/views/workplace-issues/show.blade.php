@extends('layouts.app')

@section('title', 'Workplace Issue Report - ' . $workplaceIssue->title)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('workplace-issues.index') }}" class="text-umpsa-primary hover:text-umpsa-secondary flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Reports
        </a>
    </div>

    @if(Auth::user()->isStudent())
        {{-- STUDENT VIEW - Compact Two-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Issue Header Card -->
                <div class="card-umpsa p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                        <div class="flex-1">
                            <h1 class="text-xl font-bold heading-umpsa mb-3">{{ $workplaceIssue->title }}</h1>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $workplaceIssue->status_badge_color }}">
                                    {{ $workplaceIssue->status_display }}
                                </span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $workplaceIssue->severity_badge_color }}">
                                    {{ $workplaceIssue->severity_display }}
                                </span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $workplaceIssue->category_display }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $workplaceIssue->description }}</p>
                    </div>

                    @if($workplaceIssue->location || $workplaceIssue->incident_date)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                            @if($workplaceIssue->location)
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Location</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->location }}</p>
                                </div>
                            @endif
                            @if($workplaceIssue->incident_date)
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Incident Date</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $workplaceIssue->incident_date->format('d M Y') }}
                                        @if($workplaceIssue->incident_time) at {{ $workplaceIssue->incident_time }} @endif
                                    </p>
                                </div>
                            @endif
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Submitted</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->submitted_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Coordinator Response -->
                @if($workplaceIssue->coordinator_comment || $workplaceIssue->resolution_notes)
                    <div class="card-umpsa overflow-hidden border-l-4 border-green-500">
                        <div class="bg-green-50 dark:bg-green-900/20 px-6 py-4 flex items-center justify-between">
                            <h2 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Coordinator Response
                            </h2>
                            @if($workplaceIssue->reviewed_at)
                                <span class="text-xs text-green-700 dark:text-green-300">{{ $workplaceIssue->reviewed_at->format('d M Y, H:i') }}</span>
                            @endif
                        </div>
                        <div class="p-6 space-y-4">
                            @if($workplaceIssue->coordinator_comment)
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Comment</p>
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $workplaceIssue->coordinator_comment }}</p>
                                </div>
                            @endif
                            @if($workplaceIssue->resolution_notes)
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Resolution Notes</p>
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $workplaceIssue->resolution_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Student Feedback Section -->
                @if(($workplaceIssue->coordinator_comment || $workplaceIssue->resolution_notes) && !$workplaceIssue->isClosed())
                    <div class="card-umpsa p-6">
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            Your Feedback
                        </h2>

                        @if($workplaceIssue->student_feedback)
                            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm">
                                <p class="text-xs text-blue-600 dark:text-blue-400 mb-1">Submitted {{ $workplaceIssue->student_feedback_at->format('d M Y, H:i') }}</p>
                                <p class="text-gray-700 dark:text-gray-300">{{ $workplaceIssue->student_feedback }}</p>
                            </div>
                        @endif

                        <form action="{{ route('workplace-issues.feedback.store', $workplaceIssue) }}" method="POST">
                            @csrf
                            <textarea
                                name="student_feedback"
                                rows="3"
                                required
                                maxlength="2000"
                                class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('student_feedback') border-red-500 @enderror"
                                placeholder="Share your thoughts on the coordinator's response..."
                            >{{ old('student_feedback', $workplaceIssue->student_feedback) }}</textarea>
                            @error('student_feedback')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="mt-3 btn-umpsa-primary text-sm px-4 py-2">
                                {{ $workplaceIssue->student_feedback ? 'Update Feedback' : 'Submit Feedback' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="card-umpsa overflow-hidden">
                    @php
                        $statusConfig = match($workplaceIssue->status) {
                            'new' => ['bg' => 'bg-purple-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Awaiting Review'],
                            'under_review' => ['bg' => 'bg-blue-500', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'text' => 'Being Reviewed'],
                            'in_progress' => ['bg' => 'bg-yellow-500', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'text' => 'Action In Progress'],
                            'resolved' => ['bg' => 'bg-green-500', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Issue Resolved'],
                            'closed' => ['bg' => 'bg-gray-500', 'icon' => 'M5 13l4 4L19 7', 'text' => 'Case Closed'],
                            default => ['bg' => 'bg-gray-500', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Unknown']
                        };
                    @endphp
                    <div class="{{ $statusConfig['bg'] }} p-4 text-white text-center">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusConfig['icon'] }}"></path>
                        </svg>
                        <p class="font-bold">{{ $workplaceIssue->status_display }}</p>
                        <p class="text-sm opacity-90">{{ $statusConfig['text'] }}</p>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card-umpsa p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 text-sm">Timeline</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Submitted</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->submitted_at->format('d M Y') }}</span>
                        </div>
                        @if($workplaceIssue->reviewed_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Reviewed</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->reviewed_at->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($workplaceIssue->resolved_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Resolved</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->resolved_at->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($workplaceIssue->closed_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Closed</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->closed_at->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attachments -->
                @if($workplaceIssue->attachments->count() > 0)
                    <div class="card-umpsa p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 text-sm">Attachments ({{ $workplaceIssue->attachments->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($workplaceIssue->attachments as $attachment)
                                <a href="{{ route('workplace-issues.attachments.download', $attachment) }}" class="flex items-center gap-2 p-2 text-sm border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span class="flex-1 truncate text-gray-700 dark:text-gray-300">{{ $attachment->file_name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Help Info -->
                <div class="card-umpsa p-4 bg-blue-50 dark:bg-blue-900/20">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2 text-sm">Need Help?</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Your report is being handled by the WBL coordinator. You'll be notified of any updates.
                    </p>
                </div>

                <!-- Activity History (Collapsible) -->
                @if($workplaceIssue->history->count() > 0)
                    <div class="card-umpsa p-4">
                        <details>
                            <summary class="font-semibold text-gray-900 dark:text-gray-100 text-sm cursor-pointer">
                                Activity History ({{ $workplaceIssue->history->count() }})
                            </summary>
                            <div class="mt-3 space-y-2 text-sm">
                                @foreach($workplaceIssue->history->sortByDesc('created_at')->take(5) as $history)
                                    <div class="flex gap-2 pb-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <div class="flex-1">
                                            <p class="text-gray-900 dark:text-gray-100">{{ $history->action_label }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $history->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    </div>
                @endif
            </div>
        </div>

    @else
        {{-- COORDINATOR/ADMIN VIEW - Compact Two-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Issue Header Card -->
                <div class="card-umpsa p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                        <div class="flex-1">
                            <h1 class="text-xl font-bold heading-umpsa mb-3">{{ $workplaceIssue->title }}</h1>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $workplaceIssue->status_badge_color }}">
                                    {{ $workplaceIssue->status_display }}
                                </span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $workplaceIssue->severity_badge_color }}">
                                    {{ $workplaceIssue->severity_display }}
                                </span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $workplaceIssue->category_display }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $workplaceIssue->description }}</p>
                    </div>

                    @if($workplaceIssue->location || $workplaceIssue->incident_date)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                            @if($workplaceIssue->location)
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Location</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->location }}</p>
                                </div>
                            @endif
                            @if($workplaceIssue->incident_date)
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400">Incident Date</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $workplaceIssue->incident_date->format('d M Y') }}
                                        @if($workplaceIssue->incident_time) at {{ $workplaceIssue->incident_time }} @endif
                                    </p>
                                </div>
                            @endif
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Submitted</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->submitted_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Student Feedback (Prominent Display for Coordinators) -->
                @if($workplaceIssue->student_feedback)
                    <div class="card-umpsa overflow-hidden border-l-4 border-blue-500">
                        <div class="bg-blue-50 dark:bg-blue-900/20 px-6 py-4 flex items-center justify-between">
                            <h2 class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Student Feedback
                            </h2>
                            <span class="text-xs text-blue-700 dark:text-blue-300">{{ $workplaceIssue->student_feedback_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $workplaceIssue->student_feedback }}</p>
                        </div>
                    </div>
                @endif

                <!-- Activity History (Collapsible) -->
                @if($workplaceIssue->history->count() > 0)
                    <div class="card-umpsa p-4">
                        <details open>
                            <summary class="font-semibold text-gray-900 dark:text-gray-100 text-sm cursor-pointer mb-3">
                                Activity History ({{ $workplaceIssue->history->count() }})
                            </summary>
                            <div class="space-y-2 text-sm">
                                @foreach($workplaceIssue->history->sortByDesc('created_at') as $history)
                                    <div class="flex gap-3 pb-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <div class="flex-shrink-0 {{ $history->action_icon_color }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-gray-900 dark:text-gray-100">{{ $history->action_label }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                by {{ $history->user->name }} • {{ $history->created_at->format('d M Y, H:i') }}
                                            </p>
                                            @if($history->comment)
                                                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $history->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="card-umpsa overflow-hidden">
                    @php
                        $statusConfig = match($workplaceIssue->status) {
                            'new' => ['bg' => 'bg-purple-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Awaiting Review'],
                            'under_review' => ['bg' => 'bg-blue-500', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'text' => 'Being Reviewed'],
                            'in_progress' => ['bg' => 'bg-yellow-500', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'text' => 'Action In Progress'],
                            'resolved' => ['bg' => 'bg-green-500', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Issue Resolved'],
                            'closed' => ['bg' => 'bg-gray-500', 'icon' => 'M5 13l4 4L19 7', 'text' => 'Case Closed'],
                            default => ['bg' => 'bg-gray-500', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Unknown']
                        };
                    @endphp
                    <div class="{{ $statusConfig['bg'] }} p-4 text-white text-center">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusConfig['icon'] }}"></path>
                        </svg>
                        <p class="font-bold">{{ $workplaceIssue->status_display }}</p>
                        <p class="text-sm opacity-90">{{ $statusConfig['text'] }}</p>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="card-umpsa p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 text-sm">Student Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Name</span>
                            <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $workplaceIssue->student->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Matric No</span>
                            <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $workplaceIssue->student->matric_no }}</span>
                        </div>
                        @if($workplaceIssue->student->company)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Company</span>
                                <span class="text-gray-900 dark:text-gray-100 font-medium text-right">{{ $workplaceIssue->student->company->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card-umpsa p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 text-sm">Timeline</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Submitted</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->submitted_at->format('d M Y') }}</span>
                        </div>
                        @if($workplaceIssue->reviewed_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Reviewed</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->reviewed_at->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($workplaceIssue->in_progress_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">In Progress</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->in_progress_at->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($workplaceIssue->resolved_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Resolved</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->resolved_at->format('d M Y') }}</span>
                            </div>
                        @endif
                        @if($workplaceIssue->closed_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Closed</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $workplaceIssue->closed_at->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attachments -->
                @if($workplaceIssue->attachments->count() > 0)
                    <div class="card-umpsa p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 text-sm">Attachments ({{ $workplaceIssue->attachments->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($workplaceIssue->attachments as $attachment)
                                <a href="{{ route('workplace-issues.attachments.download', $attachment) }}" class="flex items-center gap-2 p-2 text-sm border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span class="flex-1 truncate text-gray-700 dark:text-gray-300">{{ $attachment->file_name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Update Report Form (Admin/Coordinator/WBL Coordinator Only) -->
                @if(Auth::user()->isAdmin() || Auth::user()->hasRole('coordinator') || Auth::user()->isWblCoordinator())
                    <div class="card-umpsa p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 text-sm">Update Report</h3>

                        <form action="{{ route('workplace-issues.update', $workplaceIssue) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select
                                    id="status"
                                    name="status"
                                    required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary"
                                >
                                    <option value="new" {{ $workplaceIssue->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="under_review" {{ $workplaceIssue->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="in_progress" {{ $workplaceIssue->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $workplaceIssue->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $workplaceIssue->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div>
                                <label for="coordinator_comment" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Comment</label>
                                <textarea
                                    id="coordinator_comment"
                                    name="coordinator_comment"
                                    rows="3"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary"
                                    placeholder="Add your comment..."
                                >{{ old('coordinator_comment', $workplaceIssue->coordinator_comment) }}</textarea>
                            </div>

                            <div>
                                <label for="resolution_notes" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Resolution Notes</label>
                                <textarea
                                    id="resolution_notes"
                                    name="resolution_notes"
                                    rows="2"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary"
                                    placeholder="Document the resolution..."
                                >{{ old('resolution_notes', $workplaceIssue->resolution_notes) }}</textarea>
                            </div>

                            <button type="submit" class="w-full btn-umpsa-primary text-sm py-2">
                                Update Report
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
