{{--
    Evaluator Submissions Partial
    Shows student submissions for assessments that require file uploads

    Required variables:
    - $student: The student being evaluated
    - $submissions: Collection of StudentSubmission records (optional, will be fetched if not provided)
    - $courseCode: The course code (FYP, PPE, etc.) to filter assessments
--}}

@php
    use App\Models\Assessment;
    use App\Models\StudentSubmission;

    // Fetch submissions if not provided
    if (!isset($submissions)) {
        $assessmentIds = Assessment::where('course_code', $courseCode ?? 'FYP')
            ->where('requires_submission', true)
            ->where('is_active', true)
            ->pluck('id');

        $submissions = StudentSubmission::where('student_id', $student->id)
            ->whereIn('assessment_id', $assessmentIds)
            ->with('assessment')
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->groupBy('assessment_id');
    }

    // Get assessments that require submission
    $submissionAssessments = Assessment::where('course_code', $courseCode ?? 'FYP')
        ->where('requires_submission', true)
        ->where('is_active', true)
        ->orderBy('assessment_name')
        ->get();
@endphp

@if($submissionAssessments->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Student Submissions</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Files uploaded by student for evaluation</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($submissionAssessments as $assessment)
            @php
                $assessmentSubmissions = $submissions[$assessment->id] ?? collect();
                $latestSubmission = $assessmentSubmissions->first();
                $hasSubmission = $latestSubmission !== null;
            @endphp

            <div class="border rounded-lg {{ $hasSubmission ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50' }}">
                <div class="p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $assessment->assessment_name }}</h4>
                                @if($hasSubmission)
                                    <span class="px-2 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 rounded-full">
                                        Submitted
                                    </span>
                                    @if($latestSubmission->is_late)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-yellow-100 dark:bg-yellow-800 text-yellow-700 dark:text-yellow-300 rounded-full">
                                            Late
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-full">
                                        Not Submitted
                                    </span>
                                @endif
                            </div>

                            @if($hasSubmission)
                                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <p class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Submitted: {{ $latestSubmission->submitted_at->format('d M Y, g:i A') }}
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $latestSubmission->original_name }}
                                        <span class="text-gray-400">({{ number_format($latestSubmission->file_size / 1024 / 1024, 2) }} MB)</span>
                                    </p>
                                    @if($assessmentSubmissions->count() > 1)
                                        <p class="text-xs text-gray-500">
                                            Attempt {{ $latestSubmission->attempt_number }} of {{ $assessment->max_attempts }}
                                        </p>
                                    @endif
                                    @if($latestSubmission->is_late && $latestSubmission->late_penalty_applied)
                                        <p class="text-yellow-600 dark:text-yellow-400 text-xs">
                                            Late penalty: {{ number_format($latestSubmission->late_penalty_applied, 1) }}%
                                        </p>
                                    @endif
                                    @if($latestSubmission->student_remarks)
                                        <p class="mt-2 text-sm italic text-gray-500 dark:text-gray-400">
                                            "{{ $latestSubmission->student_remarks }}"
                                        </p>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($assessment->submission_deadline)
                                        Deadline: {{ $assessment->submission_deadline->format('d M Y, g:i A') }}
                                        @if($assessment->submission_deadline->isPast())
                                            <span class="text-red-500">(Overdue)</span>
                                        @endif
                                    @else
                                        No deadline set
                                    @endif
                                </p>
                            @endif
                        </div>

                        @if($hasSubmission)
                            <div class="flex items-center gap-2">
                                {{-- View/Download Button --}}
                                <a href="{{ route('evaluator.submissions.download', $latestSubmission->id) }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#0084C5] rounded-lg hover:bg-[#003A6C] transition-colors"
                                   target="_blank">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>

                                {{-- Previous Submissions Dropdown --}}
                                @if($assessmentSubmissions->count() > 1)
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" type="button"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                             x-transition
                                             class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                                            <div class="p-2">
                                                <p class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Previous Submissions</p>
                                                @foreach($assessmentSubmissions->skip(1) as $prevSubmission)
                                                    <a href="{{ route('evaluator.submissions.download', $prevSubmission->id) }}"
                                                       class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                                        <span>Attempt {{ $prevSubmission->attempt_number }}</span>
                                                        <span class="text-xs text-gray-500">{{ $prevSubmission->submitted_at->format('d M Y') }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
