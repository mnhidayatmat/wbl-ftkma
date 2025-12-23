@extends('layouts.app')

@section('title', 'Review Resume - ' . $student->name)

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Review Resume Inspection</h1>
                <p class="text-gray-600 dark:text-gray-400">Review and approve student resume and portfolio</p>
            </div>
            <a href="{{ route('coordinator.resume.index') }}" 
               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                ← Back to List
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Student Info & Document Viewer -->
            <div class="lg:col-span-2 space-y-6">
                <!-- A. STUDENT INFO (READ-ONLY) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Student Information</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Name</label>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Matric No</label>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->matric_no }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Programme</label>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->programme ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Group</label>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->group ? $student->group->name : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- B. DOCUMENT VIEWER -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Document Viewer</h2>
                        @if($inspection->resume_file_path)
                            <a href="{{ route('coordinator.resume.download-document', $inspection) }}" 
                               class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>
                        @endif
                    </div>
                    
                    @if($inspection->resume_file_path)
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden" style="height: 800px;">
                            <iframe src="{{ route('coordinator.resume.view-document', $inspection) }}#toolbar=0" 
                                    class="w-full h-full" 
                                    frameborder="0">
                                <p class="p-4 text-gray-600 dark:text-gray-400">
                                    Your browser does not support PDFs. 
                                    <a href="{{ route('coordinator.resume.download-document', $inspection) }}" class="text-blue-600 hover:underline">Download the PDF</a> instead.
                                </p>
                            </iframe>
                        </div>
                    @else
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No document uploaded</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Inspection Action Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Inspection Action Panel</h2>
                    
                    <!-- Current Status -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Current Status</label>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $inspection->status_badge_color }}">
                            {{ $inspection->status_display }}
                        </span>
                    </div>

                    <!-- Compliance Checklist Status -->
                    @if($inspection->checklist_confirmed_at)
                        <div class="mb-6 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Compliance Declaration</label>
                            <div class="space-y-1 text-sm text-green-800 dark:text-green-200">
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Student confirmed all submission declarations
                                </p>
                                <p class="text-xs text-green-600 dark:text-green-400 ml-6">
                                    Confirmed on {{ $inspection->checklist_confirmed_at->format('d M Y, h:i A') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="mb-6 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Compliance Declaration</label>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                Student has not completed the compliance checklist.
                            </p>
                        </div>
                    @endif

                    <!-- Student Reply (if exists) -->
                    @if($inspection->student_reply)
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border-l-4 border-green-500">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Student Reply</p>
                            @if($inspection->student_replied_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    {{ $inspection->student_replied_at->format('d M Y, h:i A') }}
                                </p>
                            @endif
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $inspection->student_reply }}</p>
                        </div>
                    @endif

                    <!-- Review Form -->
                    <form method="POST" action="{{ route('coordinator.resume.review.submit', $inspection) }}" class="space-y-4" onsubmit="return confirm('Are you sure you want to submit this review?');">
                        @csrf
                        
                        <!-- Comment Box -->
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Review Comments
                            </label>
                            <textarea 
                                id="comment" 
                                name="comment" 
                                rows="6" 
                                placeholder="Enter your review comments for the student..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"
                            >{{ old('comment', $inspection->coordinator_comment) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comments will be visible to the student</p>
                        </div>

                        <!-- Previous Comment (if exists) -->
                        @if($inspection->coordinator_comment && $inspection->reviewed_at)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Previous Comment</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    {{ $inspection->reviewed_at->format('d M Y, h:i A') }} by {{ $inspection->reviewer->name ?? 'Unknown' }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $inspection->coordinator_comment }}</p>
                            </div>
                        @endif

                        <!-- Inspection Decision Buttons -->
                        <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button 
                                type="submit" 
                                name="status" 
                                value="PASSED"
                                class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve Resume
                            </button>
                            
                            <button 
                                type="submit" 
                                name="status" 
                                value="REVISION_REQUIRED"
                                class="w-full px-4 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors font-semibold flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Request Revision
                            </button>
                        </div>

                        <!-- Info Note -->
                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-500">
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                <strong>Note:</strong> Approving the resume will unlock job application features for the student. Requesting revision will require the student to resubmit.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Review History Section -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Review History
                </h2>
                @if($history->count() > 0)
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $history->count() }} {{ Str::plural('entry', $history->count()) }}
                    </span>
                @endif
            </div>

            @if($history->count() > 0)
                <div class="space-y-4">
                    @foreach($history as $entry)
                        <div class="border-l-4 {{ $entry->action === 'APPROVED' ? 'border-green-500' : ($entry->action === 'REVISION_REQUESTED' ? 'border-orange-500' : 'border-blue-500') }} bg-gray-50 dark:bg-gray-700/50 rounded-r-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if($entry->action === 'APPROVED')
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @elseif($entry->action === 'REVISION_REQUESTED')
                                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $entry->action_label }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            by {{ $entry->reviewer->name ?? 'Unknown' }} • {{ $entry->created_at->format('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                @if($entry->status)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $entry->status_badge_color }}">
                                        {{ match($entry->status) {
                                            'PENDING' => 'Pending',
                                            'PASSED' => 'Approved',
                                            'FAILED' => 'Rejected',
                                            'REVISION_REQUIRED' => 'Revision Required',
                                            default => $entry->status,
                                        } }}
                                    </span>
                                @endif
                            </div>

                            @if($entry->comment)
                                <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Comment:</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $entry->comment }}</p>
                                </div>
                            @endif

                            @if($entry->previous_comment && $entry->action === 'COMMENT_UPDATED')
                                <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-900 rounded text-xs">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Previous Comment:</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 line-through">{{ Str::limit($entry->previous_comment, 150) }}</p>
                                </div>
                            @endif

                            @if($entry->metadata && isset($entry->metadata['status_changed']) && $entry->metadata['status_changed'])
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">Status changed:</span> 
                                    {{ match($entry->metadata['previous_status'] ?? null) {
                                        'PENDING' => 'Pending',
                                        'PASSED' => 'Approved',
                                        'FAILED' => 'Rejected',
                                        'REVISION_REQUIRED' => 'Revision Required',
                                        default => $entry->metadata['previous_status'] ?? 'Unknown',
                                    } }} 
                                    → 
                                    {{ match($entry->status) {
                                        'PENDING' => 'Pending',
                                        'PASSED' => 'Approved',
                                        'FAILED' => 'Rejected',
                                        'REVISION_REQUIRED' => 'Revision Required',
                                        default => $entry->status ?? 'Unknown',
                                    } }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No review history available yet.</p>
                    <p class="text-xs mt-2">History will appear here after you submit your first review.</p>
                </div>
            @endif
        </div>
</div>
@endsection
