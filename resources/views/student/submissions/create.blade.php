@extends('layouts.app')

@section('title', 'Submit Assignment')

@section('content')
<div class="min-h-screen bg-umpsa-soft-gray dark:bg-gray-900 -mx-10 -my-6 px-10 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">Submit Assignment</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $assessment->assessment_name }}</p>
        </div>

        <!-- Assessment Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Assessment Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Type:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $assessment->assessment_type }}</span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Course:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $assessment->course_code }}</span>
                </div>
                @if($assessment->submission_deadline)
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Deadline:</span>
                    <span class="ml-2 font-medium {{ $isLate ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ $assessment->submission_deadline->format('d M Y, g:i A') }}
                        @if($isLate)
                            <span class="text-red-600">(Overdue)</span>
                        @endif
                    </span>
                </div>
                @endif
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Attempts:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $attemptCount }}/{{ $assessment->max_attempts }}</span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Allowed Files:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-gray-100 uppercase">
                        {{ implode(', ', $assessment->getAllowedExtensions()) }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Max File Size:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $assessment->max_file_size_mb }} MB</span>
                </div>
            </div>

            @if($assessment->submission_instructions)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instructions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $assessment->submission_instructions }}</p>
            </div>
            @endif
        </div>

        <!-- Late Submission Warning -->
        @if($isLate && $assessment->allow_late_submission)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Late Submission Warning</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        This submission is past the deadline. A penalty of <strong>{{ number_format($latePenalty, 1) }}%</strong> will be applied to your marks.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Previous Submissions -->
        @if($submissions->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Previous Submissions</h2>
            <div class="space-y-3">
                @foreach($submissions as $submission)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gray-200 dark:bg-gray-600 rounded">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $submission->original_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Attempt {{ $submission->attempt_number }} - {{ $submission->submitted_at->format('d M Y, g:i A') }}
                                @if($submission->is_late)
                                    <span class="text-yellow-600">(Late)</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('student.submissions.download', $submission->id) }}"
                       class="text-[#0084C5] hover:text-[#003A6C] text-sm font-medium">
                        Download
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Submission Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $attemptCount > 0 ? 'Resubmit Assignment' : 'Upload Your Submission' }}
            </h2>

            <form action="{{ route('student.submissions.store', $assessment->id) }}" method="POST" enctype="multipart/form-data" x-data="{ fileName: '', dragging: false }">
                @csrf

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload File <span class="text-red-500">*</span>
                    </label>
                    <div class="relative"
                         x-on:dragover.prevent="dragging = true"
                         x-on:dragleave.prevent="dragging = false"
                         x-on:drop.prevent="dragging = false; fileName = $event.dataTransfer.files[0]?.name || ''; $refs.fileInput.files = $event.dataTransfer.files">
                        <input type="file"
                               name="file"
                               x-ref="fileInput"
                               x-on:change="fileName = $event.target.files[0]?.name || ''"
                               accept=".{{ implode(',.', $assessment->getAllowedExtensions()) }}"
                               class="hidden">
                        <div x-on:click="$refs.fileInput.click()"
                             :class="{ 'border-[#0084C5] bg-blue-50 dark:bg-blue-900/20': dragging }"
                             class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center cursor-pointer hover:border-[#0084C5] transition-colors">
                            <div x-show="!fileName">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400">
                                    <span class="text-[#0084C5] font-medium">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                    {{ strtoupper(implode(', ', $assessment->getAllowedExtensions())) }} (max {{ $assessment->max_file_size_mb }}MB)
                                </p>
                            </div>
                            <div x-show="fileName" class="flex items-center justify-center gap-3">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-gray-900 dark:text-gray-100 font-medium" x-text="fileName"></span>
                                <button type="button" x-on:click.stop="fileName = ''; $refs.fileInput.value = ''" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Student Remarks -->
                <div class="mb-6">
                    <label for="student_remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Remarks (Optional)
                    </label>
                    <textarea name="student_remarks"
                              id="student_remarks"
                              rows="3"
                              maxlength="1000"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-[#0084C5] focus:ring-[#0084C5]"
                              placeholder="Add any notes or comments about your submission...">{{ old('student_remarks') }}</textarea>
                    @error('student_remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Integrity Declaration -->
                @if($assessment->require_declaration)
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="declaration"
                                   id="declaration"
                                   value="1"
                                   class="w-4 h-4 rounded border-gray-300 text-[#0084C5] focus:ring-[#0084C5]"
                                   {{ old('declaration') ? 'checked' : '' }}>
                        </div>
                        <label for="declaration" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-medium">Academic Integrity Declaration</span>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                I declare that this submission is my own original work. I have not plagiarized or copied from other sources without proper citation. I understand that academic dishonesty may result in disciplinary action.
                            </p>
                        </label>
                    </div>
                    @error('declaration')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ url()->previous() }}"
                       class="px-6 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-white bg-[#0084C5] rounded-lg hover:bg-[#003A6C] transition-colors font-medium">
                        <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        {{ $attemptCount > 0 ? 'Resubmit' : 'Submit' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
