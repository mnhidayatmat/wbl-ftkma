@extends('layouts.app')

@section('title', 'PPE Overview')

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
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">PPE Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Professional Practice & Ethics - Your Performance Summary</p>
        </div>

        <!-- Overall Score Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <x-student.module-score-card
                title="Total PPE Score"
                :score="$totalScore"
                :maxScore="100"
                color="primary"
                :subtitle="number_format(($totalScore / 100) * 100, 1) . '% Complete'"
            />

            <x-student.module-score-card
                title="AT Contribution"
                :score="$atScore"
                :maxScore="$atMaxScore"
                color="secondary"
                subtitle="40% of Total Grade"
            />

            <x-student.module-score-card
                title="IC Contribution"
                :score="$icScore"
                :maxScore="$icMaxScore"
                color="accent"
                subtitle="60% of Total Grade"
            />
        </div>

        <!-- AT Assignments Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Academic Tutor (AT) Assignments</h2>

            @if($atMarks->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        No AT evaluations available yet. Your Academic Tutor will submit marks here when assignments are graded.
                    </p>
                </div>
            @else
                <x-student.grade-breakdown-table
                    :marks="$atMarks->map(function($mark) {
                        return (object)[
                            'name' => $mark->assessment->assessment_name ?? 'Assignment',
                            'score' => $mark->mark ?? 0,
                            'max' => $mark->assessment->max_mark ?? 100,
                            'weight' => $mark->assessment->weight ?? 0,
                            'status' => $mark->mark !== null ? 'graded' : 'pending'
                        ];
                    })"
                    :columns="[
                        ['key' => 'name', 'label' => 'Assignment'],
                        ['key' => 'score', 'label' => 'Your Mark'],
                        ['key' => 'max', 'label' => 'Max Mark'],
                        ['key' => 'weight', 'label' => 'Weight'],
                        ['key' => 'percentage', 'label' => 'Performance'],
                        ['key' => 'status', 'label' => 'Status']
                    ]"
                />
            @endif
        </div>

        <!-- IC CLO Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Industry Coach (IC) Evaluation - CLO Breakdown</h2>

            @if($icMarks->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        No IC evaluations available yet. Your Industry Coach will evaluate your performance based on Course Learning Outcomes (CLOs).
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    @foreach(['CLO1', 'CLO2', 'CLO3', 'CLO4'] as $clo)
                        @php
                            $cloMarks = $icMarks->where('clo', strtolower($clo));
                            $cloTotal = 0;
                            foreach($cloMarks as $mark) {
                                if($mark->rubric_value !== null) {
                                    $cloTotal += ($mark->rubric_value / 5) * 15;
                                }
                            }
                        @endphp
                        <div class="bg-gradient-to-br from-[#003A6C] to-[#0084C5] rounded-lg p-4 text-white">
                            <h3 class="text-sm font-semibold mb-2 opacity-90">{{ $clo }}</h3>
                            <p class="text-3xl font-bold mb-1">{{ number_format($cloTotal, 1) }}</p>
                            <p class="text-xs opacity-75">out of 15.0</p>
                            <div class="mt-3 bg-white/20 rounded-full h-2">
                                <div class="bg-white rounded-full h-2" style="width: {{ min(($cloTotal / 15) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Detailed IC Marks Table -->
                <x-student.grade-breakdown-table
                    :marks="$icMarks->map(function($mark) {
                        return (object)[
                            'name' => 'Question ' . $mark->question_no,
                            'clo' => strtoupper($mark->clo),
                            'rubric_value' => $mark->rubric_value ?? '-',
                            'score' => $mark->mark ?? 0,
                            'status' => $mark->mark !== null ? 'graded' : 'pending'
                        ];
                    })"
                    :columns="[
                        ['key' => 'name', 'label' => 'Item'],
                        ['key' => 'clo', 'label' => 'CLO'],
                        ['key' => 'rubric_value', 'label' => 'Rubric (1-5)'],
                        ['key' => 'score', 'label' => 'Marks'],
                        ['key' => 'status', 'label' => 'Status']
                    ]"
                />
            @endif
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pie Chart - AT vs IC Contribution -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">AT vs IC Contribution</h3>
                <div style="height: 300px;">
                    <canvas id="ppeContributionChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart - CLO Performance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">CLO Performance Breakdown</h3>
                <div style="height: 300px;">
                    <canvas id="cloPerformanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Logbook Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Logbook Evaluations (6 Months)</h2>

            @if($logbooks->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        No logbook evaluations yet. Your Industry Coach will evaluate your monthly logbook entries throughout your WBL period.
                    </p>
                </div>
            @else
                <x-student.logbook-progress-grid :logbooks="$logbooks" :totalMonths="6" />
            @endif
        </div>

        <!-- Assessment Windows -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Assessment Windows & Deadlines</h2>

            @if($assessmentWindows->isEmpty())
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-600 dark:text-gray-400">
                        No assessment windows configured yet.
                    </p>
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
    // UMPSA Color Palette
    const UMPSA_COLORS = {
        primary: '#003A6C',
        secondary: '#0084C5',
        accent: '#00AEEF',
        light: '#66C3FF',
    };

    // Pie Chart - AT vs IC Contribution
    const ppeContributionCtx = document.getElementById('ppeContributionChart');
    if (ppeContributionCtx) {
        new Chart(ppeContributionCtx, {
            type: 'doughnut',
            data: {
                labels: @json($pieChartData['labels']),
                datasets: [{
                    data: @json($pieChartData['data']),
                    backgroundColor: [UMPSA_COLORS.primary, UMPSA_COLORS.secondary],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            color: '#6B7280',
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: UMPSA_COLORS.primary,
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + value.toFixed(1) + ' marks (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // Bar Chart - CLO Performance
    const cloPerformanceCtx = document.getElementById('cloPerformanceChart');
    if (cloPerformanceCtx) {
        new Chart(cloPerformanceCtx, {
            type: 'bar',
            data: {
                labels: @json($cloChartData['labels']),
                datasets: [{
                    label: 'Marks out of 15',
                    data: @json($cloChartData['scores']),
                    backgroundColor: [
                        UMPSA_COLORS.primary,
                        UMPSA_COLORS.secondary,
                        UMPSA_COLORS.accent,
                        UMPSA_COLORS.light
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: UMPSA_COLORS.primary,
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Marks: ' + context.parsed.y.toFixed(1) + ' / 15.0';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 15,
                        grid: { color: '#E5E7EB' },
                        ticks: {
                            color: '#6B7280',
                            font: { size: 12 },
                            callback: function(value) {
                                return value.toFixed(0);
                            }
                        },
                        title: {
                            display: true,
                            text: 'Marks (out of 15)',
                            color: '#6B7280',
                            font: { size: 12, weight: '500' }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#6B7280',
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
