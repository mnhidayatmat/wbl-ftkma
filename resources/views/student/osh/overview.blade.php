@extends('layouts.app')

@section('title', 'OSH Overview')

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
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">OSH Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Occupational Safety & Health - Your Compliance Summary</p>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <x-student.module-score-card
                title="Total Score"
                :score="$totalScore"
                :maxScore="60"
                color="primary"
            />

            <x-student.module-score-card
                title="Average Score"
                :score="$averageScore ?? 0"
                :maxScore="10"
                color="secondary"
            />

            <x-student.module-score-card
                title="Logbook Completion"
                :score="$logbooks->count()"
                :maxScore="6"
                color="accent"
            />

            <x-student.module-score-card
                title="Progress"
                :score="$completionPercentage"
                :maxScore="100"
                color="mixed"
            />
        </div>

        <!-- Logbook Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Safety Logbook Evaluations</h2>

            @if($logbooks->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        No safety logbook evaluations yet. Your evaluator will assess your monthly OSH compliance and safety awareness.
                    </p>
                </div>
            @else
                <x-student.logbook-progress-grid :logbooks="$logbooks" />
            @endif
        </div>

        <!-- Monthly Performance Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Safety Compliance Trend</h3>
            @if($logbooks->isEmpty())
                <div class="flex items-center justify-center" style="height: 300px;">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">No safety logbook data yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Chart will appear after evaluations</p>
                    </div>
                </div>
            @else
                <div style="height: 300px;">
                    <canvas id="safetyComplianceChart"></canvas>
                </div>
            @endif
        </div>

        <!-- Assessment Windows -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Assessment Windows</h2>

            @if($assessmentWindows->isEmpty())
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-gray-600 dark:text-gray-400">No assessment windows configured yet.</p>
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
    const UMPSA_COLORS = { primary: '#003A6C', secondary: '#0084C5', accent: '#00AEEF', light: '#66C3FF' };

    const barCtx = document.getElementById('safetyComplianceChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($barChartData['labels']),
                datasets: [{
                    label: 'Safety Score',
                    data: @json($barChartData['scores']),
                    backgroundColor: UMPSA_COLORS.accent,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 10, grid: { color: '#E5E7EB' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>
@endpush
@endsection
