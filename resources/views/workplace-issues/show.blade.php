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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Issue Details Card -->
            <div class="card-umpsa p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold heading-umpsa mb-2">{{ $workplaceIssue->title }}</h1>
                        <div class="flex flex-wrap gap-2 mb-4">
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

                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</h3>
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $workplaceIssue->description }}</p>
                    </div>

                    @if($workplaceIssue->location)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Location</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $workplaceIssue->location }}</p>
                        </div>
                    @endif

                    @if($workplaceIssue->incident_date || $workplaceIssue->incident_time)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Incident Date & Time</h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                @if($workplaceIssue->incident_date)
                                    {{ $workplaceIssue->incident_date->format('d M Y') }}
                                @endif
                                @if($workplaceIssue->incident_time)
                                    at {{ $workplaceIssue->incident_time }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Coordinator Response -->
            @if($workplaceIssue->coordinator_comment || $workplaceIssue->resolution_notes)
                <div class="card-umpsa p-6">
                    <h2 class="text-lg font-semibold heading-umpsa mb-4">Coordinator Response</h2>

                    @if($workplaceIssue->coordinator_comment)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Comment</h3>
                            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $workplaceIssue->coordinator_comment }}</p>
                        </div>
                    @endif

                    @if($workplaceIssue->resolution_notes)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Resolution Notes</h3>
                            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $workplaceIssue->resolution_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Attachments -->
            @if($workplaceIssue->attachments->count() > 0)
                <div class="card-umpsa p-6">
                    <h2 class="text-lg font-semibold heading-umpsa mb-4">Attachments ({{ $workplaceIssue->attachments->count() }})</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($workplaceIssue->attachments as $attachment)
                            <div class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex-shrink-0">
                                    @if(in_array($attachment->file_type, ['jpg', 'jpeg', 'png']))
                                        <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($attachment->file_type === 'pdf')
                                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $attachment->file_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attachment->formatted_file_size }}</p>
                                </div>
                                <a href="{{ route('workplace-issues.attachments.download', $attachment) }}" class="flex-shrink-0 p-2 text-umpsa-primary hover:text-umpsa-secondary transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Activity History -->
            @if($workplaceIssue->history->count() > 0)
                <div class="card-umpsa p-6">
                    <h2 class="text-lg font-semibold heading-umpsa mb-4">Activity History</h2>

                    <div class="space-y-4">
                        @foreach($workplaceIssue->history->sortByDesc('created_at') as $history)
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 {{ $history->action_icon_color }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $history->action_label }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                by {{ $history->user->name }}
                                                <span class="text-gray-400 dark:text-gray-500">• {{ $history->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    @if($history->comment)
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $history->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Student Information (Hidden for Students viewing their own report) -->
            @if(!Auth::user()->isStudent())
                <div class="card-umpsa p-6">
                    <h2 class="text-lg font-semibold heading-umpsa mb-4">Student Information</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->student->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Matric Number</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->student->matric_no }}</p>
                        </div>
                        @if($workplaceIssue->student->company)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Company</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->student->company->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Student Help Panel (Only for Students) -->
            @if(Auth::user()->isStudent())
                <div class="card-umpsa p-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500">
                    <div class="flex gap-3 mb-3">
                        <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Report Submitted</h3>
                            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                                <p>Your workplace issue report has been successfully submitted and is being reviewed by the WBL coordinator.</p>
                                <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-800">
                                    <p class="font-medium mb-2">What happens next?</p>
                                    <ul class="list-disc list-inside space-y-1 text-sm">
                                        <li>Coordinator will review your report</li>
                                        <li>Status will be updated as actions are taken</li>
                                        <li>You'll be notified of any updates</li>
                                        <li>Reports cannot be edited after submission</li>
                                    </ul>
                                </div>
                                @if($workplaceIssue->isNew())
                                    <div class="mt-3 p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded border border-yellow-300 dark:border-yellow-700">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            <strong>Status: New</strong> - Your report is awaiting initial review.
                                        </p>
                                    </div>
                                @elseif($workplaceIssue->isUnderReview() || $workplaceIssue->isInProgress())
                                    <div class="mt-3 p-2 bg-blue-100 dark:bg-blue-900/30 rounded border border-blue-300 dark:border-blue-700">
                                        <p class="text-sm text-blue-800 dark:text-blue-200">
                                            <strong>Status: {{ $workplaceIssue->status_display }}</strong> - The coordinator is actively working on your report.
                                        </p>
                                    </div>
                                @elseif($workplaceIssue->isResolved())
                                    <div class="mt-3 p-2 bg-green-100 dark:bg-green-900/30 rounded border border-green-300 dark:border-green-700">
                                        <p class="text-sm text-green-800 dark:text-green-200">
                                            <strong>Status: Resolved</strong> - Your issue has been resolved. Please review the coordinator's response.
                                        </p>
                                    </div>
                                @elseif($workplaceIssue->isClosed())
                                    <div class="mt-3 p-2 bg-gray-100 dark:bg-gray-900/30 rounded border border-gray-300 dark:border-gray-700">
                                        <p class="text-sm text-gray-800 dark:text-gray-200">
                                            <strong>Status: Closed</strong> - This case has been closed.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Report Details -->
            <div class="card-umpsa p-6">
                <h2 class="text-lg font-semibold heading-umpsa mb-4">Report Details</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Submitted</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->submitted_at->format('d M Y, H:i') }}</p>
                    </div>
                    @if($workplaceIssue->reviewed_at)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reviewed</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->reviewed_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    @if($workplaceIssue->in_progress_at)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">In Progress Since</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->in_progress_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    @if($workplaceIssue->resolved_at)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Resolved</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->resolved_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    @if($workplaceIssue->closed_at)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Closed</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->closed_at->format('d M Y, H:i') }}</p>
                        </div>
                    @endif
                    @if($workplaceIssue->assignedTo)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $workplaceIssue->assignedTo->name }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Update Status (Admin/Coordinator Only) -->
            @if(Auth::user()->isAdmin() || Auth::user()->hasRole('coordinator'))
                <div class="card-umpsa p-6">
                    <h2 class="text-lg font-semibold heading-umpsa mb-4">Update Report</h2>

                    <form action="{{ route('workplace-issues.update', $workplaceIssue) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select
                                id="status"
                                name="status"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary"
                            >
                                <option value="new" {{ $workplaceIssue->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="under_review" {{ $workplaceIssue->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                                <option value="in_progress" {{ $workplaceIssue->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $workplaceIssue->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $workplaceIssue->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div>
                            <label for="coordinator_comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment</label>
                            <textarea
                                id="coordinator_comment"
                                name="coordinator_comment"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary"
                                placeholder="Add your comment or response..."
                            >{{ old('coordinator_comment', $workplaceIssue->coordinator_comment) }}</textarea>
                        </div>

                        <div>
                            <label for="resolution_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Resolution Notes</label>
                            <textarea
                                id="resolution_notes"
                                name="resolution_notes"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary"
                                placeholder="Document the resolution (if applicable)..."
                            >{{ old('resolution_notes', $workplaceIssue->resolution_notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full btn-umpsa-primary">
                            Update Report
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
