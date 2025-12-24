@extends('layouts.app')

@section('title', 'IP Overview')

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
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">IP Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Internship Preparation - Your Performance Summary</p>
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
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Logbook Evaluations</h2>

            @if($logbooks->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        No logbook evaluations yet. Your evaluator will assess your monthly preparedness for internship.
                    </p>
                </div>
            @else
                <x-student.logbook-progress-grid :logbooks="$logbooks" />
            @endif
        </div>

        <!-- Monthly Performance Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Monthly Performance</h3>
            @if($logbooks->isEmpty())
                <div class="flex items-center justify-center" style="height: 300px;">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">No logbook data yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Chart will appear after evaluations</p>
                    </div>
                </div>
            @else
                <div style="height: 300px;">
                    <canvas id="monthlyPerformanceChart"></canvas>
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

    const barCtx = document.getElementById('monthlyPerformanceChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($barChartData['labels']),
                datasets: [{
                    label: 'Score',
                    data: @json($barChartData['scores']),
                    backgroundColor: UMPSA_COLORS.secondary,
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
