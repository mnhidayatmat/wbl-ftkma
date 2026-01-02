@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-umpsa-soft-gray dark:bg-gray-900 -mx-10 -my-6 px-10 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('student.' . strtolower($module) . '.overview') }}"
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to {{ $module }} Overview
            </a>
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $title }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $moduleFullName }} - Submit your assessments here</p>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @php
                $totalAssessments = $submissionAssessments->count();
                $submitted = $submissionAssessments->filter(fn($item) => $item['latest_submission'] !== null)->count();
                $pending = $submissionAssessments->filter(fn($item) => $item['latest_submission'] === null && $item['can_submit'])->count();
                $evaluated = $submissionAssessments->filter(fn($item) => $item['latest_submission'] && $item['latest_submission']->status === 'evaluated')->count();
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-[#003A6C]">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Assessments</p>
                <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $totalAssessments }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pending }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500 dark:text-gray-400">Submitted</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $submitted }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-sm text-gray-500 dark:text-gray-400">Evaluated</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $evaluated }}</p>
            </div>
        </div>

        <!-- Assessment Submissions List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Assessment Submissions</h2>

            @if($submissionAssessments->isEmpty())
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">No assessments requiring submission for this module yet.</p>
                    <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Check back later or contact your coordinator.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($submissionAssessments as $item)
                        @php
                            $assessment = $item['assessment'];
                            $statusColor = match($item['status']['color']) {
                                'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            };
                        @endphp

                        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:border-[#0084C5] dark:hover:border-[#0084C5] transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Assessment Info -->
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $assessment->assessment_name }}
                                        </h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                                            {{ $item['status']['label'] }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                        <!-- Assessment Type -->
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            {{ $assessment->assessment_type }}
                                        </span>

                                        <!-- Deadline -->
                                        @if($assessment->submission_deadline)
                                            <span class="flex items-center gap-1 {{ $item['is_late'] ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Due: {{ $assessment->submission_deadline->format('d M Y, g:i A') }}
                                            </span>
                                        @endif

                                        <!-- Attempts -->
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            {{ $item['attempt_count'] }}/{{ $assessment->max_attempts }} attempts
                                        </span>

                                        <!-- Max File Size -->
                                        @if($assessment->max_file_size_mb)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                Max {{ $assessment->max_file_size_mb }}MB
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Allowed File Types -->
                                    @if($assessment->allowed_file_types)
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach($assessment->allowed_file_types as $type)
                                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded uppercase">
                                                    {{ $type }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Instructions -->
                                    @if($assessment->submission_instructions)
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                            {{ $assessment->submission_instructions }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($item['latest_submission'])
                                        <!-- View Submission -->
                                        <a href="{{ route('student.submissions.download', $item['latest_submission']->id) }}"
                                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            Download
                                        </a>

                                        <!-- Submission Info -->
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Submitted: {{ $item['latest_submission']->submitted_at->format('d M Y, g:i A') }}
                                        </span>
                                    @endif

                                    @if($item['can_submit'])
                                        <a href="{{ route('student.submissions.create', $assessment->id) }}"
                                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#0084C5] rounded-lg hover:bg-[#003A6C] transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            {{ $item['attempt_count'] > 0 ? 'Resubmit' : 'Submit' }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Submission History (if multiple submissions) -->
                            @if($item['submissions']->count() > 1)
                                <div x-data="{ showHistory: false }" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <button @click="showHistory = !showHistory"
                                            class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showHistory }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        View Submission History ({{ $item['submissions']->count() }} submissions)
                                    </button>

                                    <div x-show="showHistory" x-collapse class="mt-3 space-y-2">
                                        @foreach($item['submissions'] as $submission)
                                            <div class="flex items-center justify-between text-sm bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2">
                                                <div>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Attempt {{ $submission->attempt_number }}</span>
                                                    <span class="text-gray-500 dark:text-gray-400 ml-2">{{ $submission->submitted_at->format('d M Y, g:i A') }}</span>
                                                    @if($submission->is_late)
                                                        <span class="ml-2 text-orange-600 dark:text-orange-400">(Late)</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('student.submissions.download', $submission->id) }}"
                                                   class="text-[#0084C5] hover:text-[#003A6C]">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
