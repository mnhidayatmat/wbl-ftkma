@extends('layouts.app')

@section('title', 'FYP Overview')

@section('content')
<div class="min-h-screen bg-umpsa-soft-gray dark:bg-gray-900 -mx-10 -my-6 px-10 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Final Year Project - Your Progress Summary</p>
        </div>

        <!-- Project Proposal Status -->
        @if($proposal)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6 border-l-4
                    {{ $proposal->status === 'approved' ? 'border-green-500' :
                       ($proposal->status === 'rejected' ? 'border-red-500' :
                       ($proposal->status === 'submitted' ? 'border-blue-500' : 'border-gray-300')) }}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Project Proposal</h2>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ $proposal->project_title }}</h3>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-bold
                            {{ $proposal->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                               ($proposal->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                               ($proposal->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                               'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) }}">
                    {{ ucfirst($proposal->status) }}
                </span>
            </div>

            @if($proposal->remarks)
                <div class="mt-4">
                    <x-student.feedback-card
                        title="AT Remarks"
                        :content="$proposal->remarks"
                        :author="$proposal->approver->name ?? null"
                        :type="$proposal->status === 'approved' ? 'success' : 'warning'"
                    />
                </div>
            @endif
        </div>
        @else
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
            <p class="text-yellow-800 dark:text-yellow-200">
                No project proposal yet. Please create and submit your FYP project proposal.
            </p>
        </div>
        @endif

        <!-- Assignment Submissions Section -->
        @if($submissionAssessments->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Assignment Submissions</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $submissionAssessments->count() }} assignment(s)</span>
            </div>

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
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-[#0084C5] transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $assessment->assessment_name }}
                                    </h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                                        {{ $item['status']['label'] }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $assessment->assessment_type }}
                                    </span>
                                    @if($assessment->submission_deadline)
                                    <span class="flex items-center gap-1 {{ $item['is_late'] ? 'text-red-600 dark:text-red-400' : '' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Due: {{ $assessment->submission_deadline->format('d M Y, g:i A') }}
                                    </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        {{ $item['attempt_count'] }}/{{ $assessment->max_attempts }} attempts
                                    </span>
                                </div>
                                @if($assessment->submission_instructions)
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ $assessment->submission_instructions }}
                                </p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if($item['latest_submission'])
                                    <a href="{{ route('student.submissions.download', $item['latest_submission']->id) }}"
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        View
                                    </a>
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
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Performance Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <x-student.module-score-card
                title="Total FYP Score"
                :score="$totalScore"
                :maxScore="100"
                color="primary"
            />

            <x-student.module-score-card
                title="Evaluations Completed"
                :score="$evaluationSummary->sum('evaluated_elements')"
                :maxScore="max($evaluationSummary->sum('total_elements'), 1)"
                color="secondary"
            />

            <x-student.module-score-card
                title="Logbook Progress"
                :score="$logbooks->count()"
                :maxScore="6"
                color="accent"
            />

            <x-student.module-score-card
                title="Overall Completion"
                :score="$completionPercentage"
                :maxScore="100"
                color="mixed"
            />
        </div>

        <!-- Rubric Evaluations -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Rubric Evaluations</h2>

            @if($evaluationSummary->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        No rubric evaluations available yet.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($evaluationSummary as $summary)
                        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $summary['template']->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $summary['evaluated_elements'] }}/{{ $summary['total_elements'] }} Elements Evaluated
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-[#0084C5]">
                                        {{ number_format($summary['total_score'], 1) }}
                                    </p>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                                {{ $summary['status'] === 'released' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($summary['status']) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Performance Overview</h3>
                @if($evaluations->isEmpty())
                    <div class="flex items-center justify-center" style="height: 300px;">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400">No evaluation data yet</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Chart will appear once you have rubric evaluations</p>
                        </div>
                    </div>
                @else
                    <div style="height: 300px;">
                        <canvas id="performanceRadarChart"></canvas>
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Progress Breakdown</h3>
                <div class="space-y-6 mt-8">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium">Project Proposal</span>
                            <span class="text-sm font-bold text-[#0084C5]">{{ $proposal && $proposal->status === 'approved' ? '100' : '0' }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] h-3 rounded-full"
                                 style="width: {{ $proposal && $proposal->status === 'approved' ? '100' : '0' }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium">Evaluations</span>
                            <span class="text-sm font-bold text-[#0084C5]">
                                {{ $evaluationSummary->sum('total_elements') > 0 ?
                                   number_format(($evaluationSummary->sum('evaluated_elements') / $evaluationSummary->sum('total_elements')) * 100, 0) : 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-[#0084C5] to-[#00AEEF] h-3 rounded-full"
                                 style="width: {{ $evaluationSummary->sum('total_elements') > 0 ?
                                   ($evaluationSummary->sum('evaluated_elements') / $evaluationSummary->sum('total_elements')) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium">Logbook</span>
                            <span class="text-sm font-bold text-[#0084C5]">{{ number_format(($logbooks->count() / 6) * 100, 0) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-[#00AEEF] to-[#66C3FF] h-3 rounded-full"
                                 style="width: {{ ($logbooks->count() / 6) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logbook Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Logbook Evaluations</h2>

            @if($logbooks->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">No logbook evaluations yet.</p>
                </div>
            @else
                <x-student.logbook-progress-grid :logbooks="$logbooks" />
            @endif
        </div>

        <!-- Assessment Windows -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Assessment Windows</h2>

            @if($assessmentWindows->isEmpty())
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-600 dark:text-gray-400">No assessment windows configured.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($assessmentWindows as $window)
                        <x-student.assessment-window-badge :window="$window" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
    const UMPSA_COLORS = {
        primary: '#003A6C',
        secondary: '#0084C5',
        accent: '#00AEEF',
        light: '#66C3FF',
    };

    // Radar Chart
    const radarCtx = document.getElementById('performanceRadarChart');
    if (radarCtx) {
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: @json($radarChartData['labels']),
                datasets: [{
                    label: 'Performance',
                    data: @json($radarChartData['data']),
                    backgroundColor: 'rgba(0, 132, 197, 0.2)',
                    borderColor: UMPSA_COLORS.secondary,
                    borderWidth: 2,
                    pointBackgroundColor: UMPSA_COLORS.primary,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }
</script>
@endpush
@endsection
