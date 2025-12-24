@extends('layouts.app')

@section('title', 'LI Overview')

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
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">LI Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Learning Integration / Industrial Training - Your Performance</p>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <x-student.module-score-card
                title="Total Score"
                :score="$totalScore"
                :maxScore="60"
                color="primary"
                subtitle="6 Months Maximum"
            />

            <x-student.module-score-card
                title="Average Score"
                :score="$averageScore ?? 0"
                :maxScore="10"
                color="secondary"
                subtitle="Per Month Average"
            />

            <x-student.module-score-card
                title="Logbook Completion"
                :score="$logbooks->count()"
                :maxScore="6"
                color="accent"
                subtitle="Months Evaluated"
            />

            <x-student.module-score-card
                title="Progress"
                :score="$completionPercentage"
                :maxScore="100"
                color="mixed"
                :subtitle="number_format($completionPercentage, 0) . '%'"
            />
        </div>

        <!-- Supervisor Info -->
        @if($supervisor)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">LI Supervisor</h2>
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-[#0084C5]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $supervisor->name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $supervisor->email }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Logbook Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Logbook Evaluations (6 Months)</h2>

            @if($logbooks->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        No logbook evaluations yet. Your supervisor will evaluate your monthly progress throughout the industrial training period.
                    </p>
                </div>
            @else
                <x-student.logbook-progress-grid :logbooks="$logbooks" />
            @endif
        </div>

        <!-- Logbook Trend Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Monthly Performance Trend</h3>
            @if($logbooks->isEmpty())
                <div class="flex items-center justify-center" style="height: 300px;">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">No logbook data yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Trend chart will appear after evaluations</p>
                    </div>
                </div>
            @else
                <div style="height: 300px;">
                    <canvas id="logbookTrendChart"></canvas>
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
    const UMPSA_COLORS = {
        primary: '#003A6C',
        secondary: '#0084C5',
        accent: '#00AEEF',
        light: '#66C3FF',
    };

    // Line Chart - Logbook Trend
    const trendCtx = document.getElementById('logbookTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($lineChartData['labels']),
                datasets: [{
                    label: 'Monthly Score',
                    data: @json($lineChartData['scores']),
                    borderColor: UMPSA_COLORS.secondary,
                    backgroundColor: 'rgba(0, 132, 197, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: UMPSA_COLORS.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
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
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        grid: { color: '#E5E7EB' },
                        ticks: { stepSize: 2 },
                        title: {
                            display: true,
                            text: 'Score (out of 10)',
                            color: '#6B7280',
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
