@extends('layouts.app')

@section('title', $assessment->assessment_name . ' - ' . $student->name)

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('academic.fyp.ic.show', $student) }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Evaluation Overview
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl">🎤</span>
                        <h1 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                            {{ $assessment->assessment_name }}
                        </h1>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded font-medium">
                            {{ $assessment->rubrics->count() }} Rubrics
                        </span>
                        <span>•</span>
                        <span>{{ $assessment->clo_code }}</span>
                        <span>•</span>
                        <span>Weight: {{ number_format($assessment->weight_percentage, 2) }}%</span>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3 text-center">
                    <div class="text-2xl font-bold text-[#0084C5]">{{ number_format($totalContribution, 2) }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Current Contribution</div>
                </div>
            </div>

            <!-- Student Info -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $student->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->matric_no }} • {{ $student->group->name ?? 'No Group' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rubric Form -->
        <form action="{{ route('academic.fyp.ic.rubric.store', ['student' => $student->id, 'assessment' => $assessment->id]) }}" method="POST" id="rubricForm">
            @csrf

            <div class="space-y-4">
                @foreach($assessment->rubrics->sortBy('sort_order') as $index => $rubric)
                    @php
                        $rubricMark = $rubricMarks->get($rubric->id);
                        $currentScore = $rubricMark?->rubric_score ?? null;
                        $contribution = $rubricMark ? $rubricMark->weighted_contribution : 0;
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden" x-data="{ expanded: false }">
                        <!-- Rubric Header -->
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-8 h-8 bg-[#0084C5] text-white rounded-full flex items-center justify-center text-sm font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                                            {{ $rubric->question_code }}
                                        </span>
                                        <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">
                                            {{ number_format($rubric->weight_percentage, 2) }}%
                                        </span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white leading-relaxed">
                                        {{ $rubric->question_title }}
                                    </h3>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-lg font-bold text-[#0084C5]" id="contribution-{{ $rubric->id }}">
                                        {{ number_format($contribution, 2) }}%
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Contribution</div>
                                </div>
                            </div>

                            <!-- Details Toggle -->
                            @if($rubric->question_description || $rubric->example_answer)
                            <button type="button" 
                                    @click="expanded = !expanded"
                                    class="mt-3 text-sm text-[#0084C5] hover:text-[#003A6C] font-medium flex items-center gap-1">
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span x-text="expanded ? 'Hide Details' : 'Show Details'"></span>
                            </button>
                            <div x-show="expanded" x-transition class="mt-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm text-gray-600 dark:text-gray-400 space-y-2">
                                @if($rubric->question_description)
                                    <p>{{ $rubric->question_description }}</p>
                                @endif
                                @if($rubric->example_answer)
                                    <p class="italic text-gray-500">
                                        <span class="font-medium">Example:</span> {{ $rubric->example_answer }}
                                    </p>
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Score Selection -->
                        <div class="p-5 bg-gray-50 dark:bg-gray-700/30">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Select Score ({{ $rubric->rubric_min }} - {{ $rubric->rubric_max }})
                            </label>
                            
                            @if($canEdit)
                            <div class="flex flex-wrap gap-2">
                                @for($i = $rubric->rubric_min; $i <= $rubric->rubric_max; $i++)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" 
                                               name="rubric_scores[{{ $rubric->id }}]" 
                                               value="{{ $i }}"
                                               {{ $currentScore == $i ? 'checked' : '' }}
                                               class="sr-only peer"
                                               onchange="updateContribution({{ $rubric->id }}, {{ $i }}, {{ $rubric->weight_percentage }}, {{ $rubric->rubric_min }}, {{ $rubric->rubric_max }})">
                                        <div class="w-14 h-14 flex items-center justify-center rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-lg transition-all
                                                    peer-checked:border-[#0084C5] peer-checked:bg-[#0084C5] peer-checked:text-white
                                                    hover:border-[#0084C5] hover:bg-[#0084C5]/10">
                                            {{ $i }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                            
                            <!-- Score Labels -->
                            <div class="flex justify-between mt-3 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $rubric->rubric_min }} = Poor</span>
                                <span>{{ $rubric->rubric_max }} = Excellent</span>
                            </div>
                            @else
                            <div class="flex items-center gap-4">
                                <span class="text-2xl font-bold text-[#0084C5]">{{ $currentScore ?? 'Not Evaluated' }}</span>
                                @if($currentScore)
                                <span class="text-gray-500 dark:text-gray-400">/ {{ $rubric->rubric_max }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sticky Save Button -->
            @if($canEdit)
            <div class="sticky bottom-0 mt-6 -mx-4 sm:mx-0">
                <div class="bg-white dark:bg-gray-800 rounded-t-xl sm:rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-center sm:text-left">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Contribution:</span>
                            <span class="ml-2 text-xl font-bold text-[#0084C5]" id="totalContribution">{{ number_format($totalContribution, 2) }}%</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">/ {{ number_format($assessment->weight_percentage, 2) }}%</span>
                        </div>
                        <button type="submit" 
                                class="w-full sm:w-auto px-8 py-3 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors shadow-md flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save All Scores
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>

<script>
function updateContribution(rubricId, score, weight, min, max) {
    const range = max - min;
    const normalizedScore = (score - min) / range;
    const contribution = normalizedScore * weight;
    
    const contributionSpan = document.getElementById(`contribution-${rubricId}`);
    if (contributionSpan) {
        contributionSpan.textContent = contribution.toFixed(2) + '%';
    }
    
    updateTotalContribution();
}

function updateTotalContribution() {
    let total = 0;
    document.querySelectorAll('[id^="contribution-"]').forEach(span => {
        const value = parseFloat(span.textContent) || 0;
        total += value;
    });
    
    const totalSpan = document.getElementById('totalContribution');
    if (totalSpan) {
        totalSpan.textContent = total.toFixed(2) + '%';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateTotalContribution);
</script>
@endsection



