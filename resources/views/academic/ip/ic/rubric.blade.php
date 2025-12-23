@extends('layouts.app')

@section('title', $assessment->assessment_name . ' - ' . $student->name)

@section('content')
<div class="py-6" x-data="rubricEvaluation()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('academic.ip.ic.show', $student) }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Evaluation Overview
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-2xl flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-2xl shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-8 mb-8 border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-950/30 rounded-2xl flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-[#003A6C] dark:text-[#0084C5] tracking-tight leading-none">
                                {{ $assessment->assessment_name }}
                            </h1>
                            <p class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mt-2">Internship Preparation Rubric</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full font-black text-[10px] uppercase tracking-widest border border-gray-100 dark:border-gray-600">
                            {{ $assessment->rubrics->count() }} Criteria
                        </span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full font-black text-[10px] uppercase tracking-widest border border-blue-100 dark:border-blue-800">
                            {{ $assessment->clo_code }}
                        </span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Weight: {{ number_format($assessment->weight_percentage, 2) }}%</span>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-3xl p-6 text-center border border-gray-100 dark:border-gray-700 min-w-[160px]">
                    <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Accumulated</div>
                    <div class="text-4xl font-black text-[#0084C5] tracking-tighter" id="headerContribution">{{ number_format($totalContribution, 2) }}%</div>
                    <div class="text-[10px] font-bold text-gray-400 mt-1 italic">of potential full score</div>
                </div>
            </div>

            <!-- Student Profile Summary -->
            <div class="mt-8 pt-8 border-t border-gray-50 dark:border-gray-700/50">
                <div class="flex items-center gap-5">
                    <div class="relative">
                        <div class="w-14 h-14 bg-gradient-to-tr from-[#003A6C] to-[#0084C5] rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-4 border-white dark:border-gray-800 rounded-full"></div>
                    </div>
                    <div>
                        <p class="text-lg font-black text-gray-900 dark:text-white leading-tight">{{ $student->name }}</p>
                        <div class="flex items-center gap-3 mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            <span>ID: {{ $student->matric_no }}</span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span>{{ $student->group->name ?? 'Unassigned Group' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rubric Form -->
        <form action="{{ route('academic.ip.ic.rubric.store', ['student' => $student->id, 'assessment' => $assessment->id]) }}" method="POST" id="rubricForm">
            @csrf

            <div class="space-y-6">
                @foreach($assessment->rubrics->sortBy('sort_order') as $index => $rubric)
                    @php
                        $rubricMark = $rubricMarks->get($rubric->id);
                        $currentScore = $rubricMark?->rubric_score ?? null;
                        $contribution = $rubricMark ? $rubricMark->weighted_contribution : 0;
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg overflow-hidden border border-gray-50 dark:border-gray-700 transition-all duration-300 hover:shadow-xl group/card" 
                         x-data="{ expanded: false }">
                        <!-- Rubric Header -->
                        <div class="p-8">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="w-10 h-10 bg-[#003A6C] text-white rounded-2xl flex items-center justify-center text-lg font-black shadow-md shadow-[#003A6C]/20">
                                            {{ $index + 1 }}
                                        </span>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">
                                                Code: {{ $rubric->question_code }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 text-[9px] font-black bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-md border border-blue-100 dark:border-blue-800 uppercase tracking-tighter">
                                                    Weight: {{ number_format($rubric->weight_percentage, 2) }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-snug group-hover/card:text-[#003A6C] dark:group-hover/card:text-[#0084C5] transition-colors">
                                        {{ $rubric->question_title }}
                                    </h3>
                                </div>
                                <div class="shrink-0 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 min-w-[120px] text-center">
                                    <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Mark Impact</div>
                                    <div class="text-2xl font-black text-[#0084C5] tracking-tight" id="contribution-{{ $rubric->id }}">
                                        {{ number_format($contribution, 2) }}%
                                    </div>
                                </div>
                            </div>

                            <!-- Details Toggle -->
                            @if($rubric->question_description || $rubric->example_answer)
                            <div class="mt-6 border-t border-gray-50 dark:border-gray-700/50 pt-4">
                                <button type="button" 
                                        @click="expanded = !expanded"
                                        class="text-xs font-black text-[#0084C5] hover:text-[#003A6C] uppercase tracking-widest flex items-center gap-2 transition-colors">
                                    <div class="w-6 h-6 bg-[#0084C5]/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-3 h-3 transition-transform duration-300" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                    <span x-text="expanded ? 'Condense Context' : 'Expand Context'"></span>
                                </button>
                                <div x-show="expanded" 
                                     x-collapse
                                     class="mt-4 p-6 bg-gray-50/50 dark:bg-gray-900/30 rounded-3xl border border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400 leading-relaxed shadow-inner">
                                    @if($rubric->question_description)
                                        <div class="mb-4">
                                            <p class="font-bold text-gray-800 dark:text-gray-300 mb-1">Criterion Description:</p>
                                            <p>{{ $rubric->question_description }}</p>
                                        </div>
                                    @endif
                                    @if($rubric->example_answer)
                                        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-dashed border-gray-200 dark:border-gray-600">
                                            <p class="font-extrabold text-blue-600 dark:text-blue-400 mb-1 text-[11px] uppercase tracking-widest">Expected Performance Indicator:</p>
                                            <p class="italic italic text-gray-500">{{ $rubric->example_answer }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Score Selection Area -->
                        <div class="px-8 pb-8 pt-2 bg-gray-50/20 dark:bg-gray-900/10">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-1 h-3 bg-[#0084C5] rounded-full"></div>
                                <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                                    Rating Calibration ({{ $rubric->rubric_min }} Lowest - {{ $rubric->rubric_max }} Highest)
                                </label>
                            </div>
                            
                            @if($canEdit)
                            <div class="flex flex-wrap gap-3">
                                @for($i = $rubric->rubric_min; $i <= $rubric->rubric_max; $i++)
                                    <label class="relative cursor-pointer group/score">
                                        <input type="radio" 
                                               name="rubric_scores[{{ $rubric->id }}]" 
                                               value="{{ $i }}"
                                               {{ $currentScore == $i ? 'checked' : '' }}
                                               class="sr-only peer"
                                               required
                                               onchange="updateContribution({{ $rubric->id }}, {{ $i }}, {{ $rubric->weight_percentage }}, {{ $rubric->rubric_min }}, {{ $rubric->rubric_max }})">
                                        <div class="w-14 h-14 md:w-16 md:h-16 flex flex-col items-center justify-center rounded-2xl border-2 border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 font-black text-xl transition-all duration-300
                                                    peer-checked:border-[#0084C5] peer-checked:bg-[#003A6C] peer-checked:text-white peer-checked:scale-110 peer-checked:shadow-xl peer-checked:shadow-[#003A6C]/30
                                                    hover:border-[#0084C5] hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#0084C5]
                                                    active:scale-90">
                                            {{ $i }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                            @else
                            <div class="flex items-center gap-6 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 w-fit">
                                <div class="text-center">
                                    <span class="text-4xl font-black text-[#003A6C] dark:text-[#0084C5] tracking-tighter">{{ $currentScore ?? '?' }}</span>
                                    <span class="text-xl font-bold text-gray-300">/ {{ $rubric->rubric_max }}</span>
                                </div>
                                <div class="h-10 w-px bg-gray-100 dark:bg-gray-700"></div>
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                    Status: <span class="{{ $currentScore !== null ? 'text-green-600' : 'text-amber-500' }}">{{ $currentScore !== null ? 'Evaluated' : 'Not Graded' }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Dashboard Summary Bar (Sticky) -->
            @if($canEdit)
            <div class="sticky bottom-8 mt-12 z-40">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white dark:border-gray-700 p-6 max-w-4xl mx-auto ring-1 ring-black/5">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-6">
                            <div class="text-center md:text-left">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest block mb-1">Total Weighted Score</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-black text-[#003A6C] dark:text-[#0084C5] tracking-tighter" id="totalContribution">{{ number_format($totalContribution, 2) }}%</span>
                                    <span class="text-lg font-bold text-gray-300 select-none">/ {{ number_format($assessment->weight_percentage, 2) }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <button type="button" 
                                    onclick="window.location.href='{{ route('academic.ip.ic.show', $student) }}'"
                                    class="flex-1 md:flex-none px-6 py-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 font-bold rounded-2xl transition-all active:scale-95">
                                Discard
                            </button>
                            <button type="submit" 
                                    form="rubricForm"
                                    class="flex-1 md:flex-none px-12 py-4 bg-gradient-to-r from-[#003A6C] to-[#0084C5] hover:from-[#002A50] hover:to-[#0074B5] text-white font-black rounded-2xl shadow-xl shadow-[#0084C5]/20 transition-all active:scale-95 flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Finalize Rubric
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>

@push('scripts')
<script>
function rubricEvaluation() {
    return {
        // Alpine.js data
    };
}

function updateContribution(rubricId, score, weight, min, max) {
    const range = max - min;
    const normalizedScore = (score - min) / range;
    const contribution = normalizedScore * weight;
    
    const contributionSpan = document.getElementById(`contribution-${rubricId}`);
    if (contributionSpan) {
        contributionSpan.textContent = contribution.toFixed(2) + '%';
        contributionSpan.classList.add('scale-125', 'text-green-600');
        setTimeout(() => {
            contributionSpan.classList.remove('scale-125', 'text-green-600');
        }, 500);
    }
    
    updateTotalContribution();
}

function updateTotalContribution() {
    let total = 0;
    document.querySelectorAll('[id^="contribution-"]').forEach(span => {
        const value = parseFloat(span.textContent) || 0;
        total += value;
    });
    
    const formattedTotal = total.toFixed(2) + '%';
    
    const totalSpan = document.getElementById('totalContribution');
    const headerSpan = document.getElementById('headerContribution');
    
    if (totalSpan) {
        totalSpan.textContent = formattedTotal;
    }
    if (headerSpan) {
        headerSpan.textContent = formattedTotal;
    }
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
    updateTotalContribution();
});
</script>
<style>
/* Custom radio bounce animation */
.peer:checked + div {
    animation: bounce 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes bounce {
    0%, 100% { transform: scale(1.1); }
    50% { transform: scale(0.9); }
}
</style>
@endpush
@endsection
