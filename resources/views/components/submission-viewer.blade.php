@props([
    'submission' => null,
    'assessment' => null,
    'student' => null,
    'showHeader' => true,
    'compact' => false,
])

@php
    // Try to get submission if not passed
    if (!$submission && $assessment && $student) {
        $submission = \App\Models\StudentSubmission::getLatestForStudent($student->id, $assessment->id);
    }
@endphp

<div class="submission-viewer {{ $compact ? 'p-3' : 'p-4 sm:p-5' }} bg-gradient-to-r from-slate-50 to-blue-50 dark:from-gray-800 dark:to-slate-800 rounded-xl border border-slate-200 dark:border-slate-700"
     x-data="{ showPreview: false, previewLoading: true }">

    @if($showHeader)
    <div class="flex items-center gap-2 mb-3">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Student Submission</h4>
            <p class="text-xs text-gray-500 dark:text-gray-400">Review before evaluating</p>
        </div>
    </div>
    @endif

    @if($submission)
        <div class="space-y-3">
            <!-- File Info Card -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 bg-white dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <!-- File Icon -->
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0
                        @if(str_contains($submission->mime_type ?? '', 'pdf'))
                            bg-red-100 dark:bg-red-900/30
                        @elseif(str_contains($submission->mime_type ?? '', 'word'))
                            bg-blue-100 dark:bg-blue-900/30
                        @elseif(str_contains($submission->mime_type ?? '', 'image'))
                            bg-green-100 dark:bg-green-900/30
                        @elseif(str_contains($submission->mime_type ?? '', 'zip'))
                            bg-yellow-100 dark:bg-yellow-900/30
                        @else
                            bg-gray-100 dark:bg-gray-600
                        @endif">
                        @if(str_contains($submission->mime_type ?? '', 'pdf'))
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 15.5a.5.5 0 01.5-.5h1.5v-1H9a.5.5 0 010-1h1.5V12H9a.5.5 0 010-1h2a.5.5 0 01.5.5v4a.5.5 0 01-.5.5H9a.5.5 0 01-.5-.5zM13 12h1.5a.5.5 0 01.5.5v3a.5.5 0 01-.5.5H13a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm.5 1v2h.5v-2h-.5z"/>
                            </svg>
                        @elseif(str_contains($submission->mime_type ?? '', 'word'))
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM9.998 12.5l.9 3.6.9-3.6h1.4l.9 3.6.9-3.6H16l-1.4 4h-1.4l-.8-3.2-.8 3.2H10.2L8.8 12.5h1.198z"/>
                            </svg>
                        @elseif(str_contains($submission->mime_type ?? '', 'image'))
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                    </div>

                    <!-- File Details -->
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white text-sm truncate" title="{{ $submission->original_name }}">
                            {{ $submission->original_name }}
                        </p>
                        <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $submission->file_size_human }}</span>
                            <span class="hidden sm:inline">•</span>
                            <span class="hidden sm:inline">{{ $submission->submitted_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <!-- Mobile: Show date on separate line -->
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 sm:hidden">
                            {{ $submission->submitted_at?->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="flex items-center gap-2 sm:flex-shrink-0">
                    @php $badge = $submission->status_badge; @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badge['bg'] }} {{ $badge['text'] }}">
                        {{ $badge['label'] }}
                    </span>
                    @if($submission->is_late)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                            Late
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons - Mobile Optimized -->
            <div class="flex flex-col sm:flex-row gap-2">
                <!-- Preview Button -->
                @if(str_contains($submission->mime_type ?? '', 'pdf') || str_contains($submission->mime_type ?? '', 'image'))
                <button @click="showPreview = true; previewLoading = true"
                        type="button"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 sm:py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Preview</span>
                </button>
                @endif

                <!-- Download Button -->
                <a href="{{ route('evaluator.submissions.download', $submission) }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 sm:py-2.5 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg border border-gray-300 dark:border-gray-600 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download</span>
                </a>
            </div>

            <!-- Student Remarks (if any) -->
            @if($submission->student_remarks)
            <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                <p class="text-xs font-medium text-amber-800 dark:text-amber-200 mb-1">Student's Note:</p>
                <p class="text-sm text-amber-700 dark:text-amber-300">{{ $submission->student_remarks }}</p>
            </div>
            @endif
        </div>

        <!-- Preview Modal (Fullscreen for Mobile) -->
        <div x-show="showPreview"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-2 sm:p-4"
             @click.self="showPreview = false"
             @keydown.escape.window="showPreview = false"
             style="display: none;">

            <div class="relative w-full h-full sm:w-[95%] sm:h-[95%] md:w-[90%] md:h-[90%] max-w-6xl bg-white dark:bg-gray-900 rounded-lg sm:rounded-2xl shadow-2xl overflow-hidden flex flex-col"
                 @click.stop>

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex-shrink-0">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base truncate">
                                {{ $submission->original_name }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $submission->file_size_human }} • {{ $submission->submitted_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <!-- Download in Modal -->
                        <a href="{{ route('evaluator.submissions.download', $submission) }}"
                           class="hidden sm:inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </a>

                        <!-- Close Button -->
                        <button @click="showPreview = false"
                                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Preview Content -->
                <div class="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900 relative">
                    <!-- Loading Spinner -->
                    <div x-show="previewLoading" class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-900 z-10">
                        <div class="text-center">
                            <svg class="animate-spin h-10 w-10 text-blue-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Loading preview...</p>
                        </div>
                    </div>

                    @if(str_contains($submission->mime_type ?? '', 'pdf'))
                        <iframe src="{{ route('evaluator.submissions.preview', $submission) }}#toolbar=0&navpanes=0"
                                class="w-full h-full"
                                @load="previewLoading = false"
                                title="Document Preview"></iframe>
                    @elseif(str_contains($submission->mime_type ?? '', 'image'))
                        <div class="flex items-center justify-center p-4 h-full">
                            <img src="{{ route('evaluator.submissions.preview', $submission) }}"
                                 alt="Submission Preview"
                                 class="max-w-full max-h-full object-contain rounded-lg shadow-lg"
                                 @load="previewLoading = false">
                        </div>
                    @endif
                </div>

                <!-- Mobile Bottom Actions -->
                <div class="sm:hidden flex items-center gap-2 p-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <a href="{{ route('evaluator.submissions.download', $submission) }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download File
                    </a>
                </div>
            </div>
        </div>

    @else
        <!-- No Submission State -->
        <div class="text-center py-6 bg-white dark:bg-gray-700/30 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-600 dark:text-gray-400 font-medium">No Submission Yet</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Student has not submitted any files for this assessment.</p>
        </div>
    @endif
</div>
