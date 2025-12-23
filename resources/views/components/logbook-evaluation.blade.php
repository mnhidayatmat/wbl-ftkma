{{-- 
    Reusable Logbook Evaluation Component
    Parameters:
    - $student: Student model
    - $periods: Collection of period data (from assessment components) with period_index, label, score, remarks, evaluated_at
      OR $months: Collection of month data (for FYP backward compatibility)
    - $totalScore: Total score across all periods
    - $completedPeriods: Number of completed periods (or $completedMonths for FYP)
    - $totalPeriods: Total number of periods (or defaults to 6 for FYP)
    - $assessmentWeight: Assessment weight percentage
    - $canEdit: Boolean indicating if user can edit
    - $moduleType: 'FYP' or 'LI'
    - $backRoute: Route name for back button
    - $storeRoute: Route name for form submission
--}}
@php
    // Support both $periods (new) and $months (FYP backward compatibility)
    $periodsData = $periods ?? $months ?? collect();
    $completedCount = $completedPeriods ?? $completedMonths ?? 0;
    $totalPeriodsCount = $totalPeriods ?? ($periodsData->count() ?: 6);
    $maxPossibleScore = $maxPossibleScore ?? ($totalPeriodsCount * 10);
@endphp
<div class="py-6" x-data="logbookEvaluation()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route($backRoute, $student) }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to IC Evaluation
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

        <!-- Student Info Header - Improved Design -->
        <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] rounded-2xl shadow-lg p-6 mb-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">{{ $student->name }}</h1>
                    <p class="text-white/80">{{ $student->matric_no }}</p>
                    <div class="flex flex-wrap gap-3 mt-3 text-sm text-white/70">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $student->group->name ?? 'No Group' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $student->company->company_name ?? 'No Company' }}
                        </span>
                    </div>
                </div>
                <!-- Real-time Score & Progress -->
                <div class="flex items-center gap-6">
                    <div class="text-center bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-3xl font-bold">
                            <span x-text="evaluatedCount">{{ $completedCount }}</span><span class="text-lg text-white/60">/{{ $totalPeriodsCount }}</span>
                        </div>
                        <div class="text-sm text-white/80">Periods Evaluated</div>
                    </div>
                    <div class="text-center bg-white/10 rounded-xl p-4 backdrop-blur-sm">
                        <div class="text-3xl font-bold">
                            <span x-text="totalScore">{{ $totalScore }}</span><span class="text-lg text-white/60">/{{ $maxPossibleScore }}</span>
                        </div>
                        <div class="text-sm text-white/80">Total Score</div>
                    </div>
                    @if($assessmentWeight > 0)
                    <div class="text-center bg-[#00A86B]/30 rounded-xl p-4 backdrop-blur-sm border border-[#00A86B]/50">
                        <div class="text-3xl font-bold text-[#00FF9C]">
                            <span x-text="weightedScore.toFixed(2)">{{ number_format(($totalScore / $maxPossibleScore) * $assessmentWeight, 2) }}</span><span class="text-lg text-white/60">%</span>
                        </div>
                        <div class="text-sm text-white/80">Weighted ({{ number_format($assessmentWeight, 2) }}%)</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Logbook Evaluation Form -->
        <form action="{{ route($storeRoute, $student) }}" method="POST" id="logbookForm">
            @csrf

            <!-- Period Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($periodsData as $periodData)
                    @php
                        // Support both new format (period_index) and old format (month) for backward compatibility
                        $periodIndex = $periodData['period_index'] ?? $periodData['month'] ?? $loop->index;
                        $periodLabel = $periodData['label'] ?? ($periodData['component_name'] ?? "Period " . ($loop->index + 1));
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-2 transition-all duration-200"
                         :class="getMonthBorderClass({{ $periodIndex }})"
                         x-data="{ isExpanded: false }">
                        <!-- Period Header -->
                        <div class="p-4 cursor-pointer"
                             @click="isExpanded = !isExpanded"
                             :class="scores[{{ $periodIndex }}] ? 'bg-gradient-to-r from-[#0084C5]/10 to-[#00A86B]/10' : 'bg-gray-50 dark:bg-gray-700/50'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg"
                                         :class="scores[{{ $periodIndex }}] ? getScoreColorClass(scores[{{ $periodIndex }}]) : 'bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400'">
                                        <span x-text="scores[{{ $periodIndex }}] || '—'">{{ $periodData['score'] ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-[#003A6C] dark:text-[#0084C5]">
                                            {{ $periodLabel }}
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            @if($periodData['evaluated_at'])
                                                Updated {{ $periodData['evaluated_at']->diffForHumans() }}
                                            @else
                                                Not evaluated
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="isExpanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Expandable Content -->
                        <div x-show="isExpanded" x-collapse class="p-4 border-t border-gray-200 dark:border-gray-700">
                            <!-- Score Selection (1-10 scale) -->
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2 uppercase tracking-wide">
                                    Select Score
                                </label>
                                <div class="grid grid-cols-5 gap-2">
                                    @for($score = 1; $score <= 10; $score++)
                                        <label class="relative">
                                            @if($canEdit)
                                            <input 
                                                type="radio" 
                                                name="scores[{{ $periodIndex }}]" 
                                                value="{{ $score }}"
                                                {{ ($periodData['score'] ?? null) == $score ? 'checked' : '' }}
                                                @change="updateScore({{ $periodIndex }}, {{ $score }})"
                                                class="sr-only peer"
                                            >
                                            <div class="w-full aspect-square rounded-lg flex items-center justify-center cursor-pointer transition-all text-sm font-bold
                                                peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-[#0084C5]
                                                {{ $score <= 2 ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 peer-checked:bg-red-500 peer-checked:text-white' : '' }}
                                                {{ $score >= 3 && $score <= 4 ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 hover:bg-orange-200 peer-checked:bg-orange-500 peer-checked:text-white' : '' }}
                                                {{ $score >= 5 && $score <= 6 ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-200 peer-checked:bg-yellow-500 peer-checked:text-white' : '' }}
                                                {{ $score >= 7 && $score <= 8 ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 peer-checked:bg-blue-500 peer-checked:text-white' : '' }}
                                                {{ $score >= 9 ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-200 peer-checked:bg-green-500 peer-checked:text-white' : '' }}
                                            ">
                                                {{ $score }}
                                            </div>
                                            @else
                                            <div class="w-full aspect-square rounded-lg flex items-center justify-center text-sm font-bold
                                                {{ ($periodData['score'] ?? null) == $score ? 'ring-2 ring-offset-2 ring-[#0084C5]' : '' }}
                                                {{ $score <= 2 ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : '' }}
                                                {{ $score >= 3 && $score <= 4 ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' : '' }}
                                                {{ $score >= 5 && $score <= 6 ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400' : '' }}
                                                {{ $score >= 7 && $score <= 8 ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                                                {{ $score >= 9 ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : '' }}
                                                {{ ($periodData['score'] ?? null) == $score ? ($score <= 2 ? 'bg-red-500 text-white' : ($score <= 4 ? 'bg-orange-500 text-white' : ($score <= 6 ? 'bg-yellow-500 text-white' : ($score <= 8 ? 'bg-blue-500 text-white' : 'bg-green-500 text-white')))) : '' }}
                                            ">
                                                {{ $score }}
                                            </div>
                                            @endif
                                        </label>
                                    @endfor
                                </div>
                                <div class="flex justify-between text-xs mt-2 px-1">
                                    <span class="text-red-500 font-medium">Poor</span>
                                    <span class="text-green-500 font-medium">Excellent</span>
                                </div>
                            </div>

                            <!-- Remarks -->
                            @if($canEdit)
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">
                                    Remarks (Optional)
                                </label>
                                <textarea 
                                    name="remarks[{{ $periodIndex }}]"
                                    rows="2"
                                    placeholder="Add feedback for this period..."
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm resize-none"
                                >{{ $periodData['remarks'] ?? '' }}</textarea>
                            </div>
                            @elseif(!empty($periodData['remarks']))
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1 uppercase tracking-wide">
                                    Remarks
                                </label>
                                <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    {{ $periodData['remarks'] }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($canEdit)
            <!-- Sticky Save Button -->
            <div class="sticky bottom-4 z-10">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full animate-pulse"
                                     :class="evaluatedCount === {{ $totalPeriodsCount }} ? 'bg-green-500' : (evaluatedCount > 0 ? 'bg-yellow-500' : 'bg-gray-400')"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-bold text-[#0084C5]" x-text="evaluatedCount">{{ $completedCount }}</span>/{{ $totalPeriodsCount }} periods evaluated
                                </span>
                            </div>
                            <div class="hidden md:block text-sm text-gray-500 dark:text-gray-400">
                                Total: <span class="font-bold text-gray-900 dark:text-white" x-text="totalScore">{{ $totalScore }}</span>/{{ $maxPossibleScore }}
                                @if($assessmentWeight > 0)
                                    • Weighted: <span class="font-bold text-[#00A86B]" x-text="weightedScore.toFixed(2) + '%'">{{ number_format(($totalScore / $maxPossibleScore) * $assessmentWeight, 2) }}%</span>
                                @endif
                            </div>
                        </div>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-gradient-to-r from-[#0084C5] to-[#003A6C] hover:from-[#003A6C] hover:to-[#0084C5] text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Evaluation
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </form>

        <!-- Score Legend -->
        <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Score Guide
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                    <span class="w-8 h-8 rounded-lg bg-red-500 text-white flex items-center justify-center font-bold text-sm">1-2</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Poor</span>
                </div>
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                    <span class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center font-bold text-sm">3-4</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Below Avg</span>
                </div>
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                    <span class="w-8 h-8 rounded-lg bg-yellow-500 text-white flex items-center justify-center font-bold text-sm">5-6</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Average</span>
                </div>
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                    <span class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center font-bold text-sm">7-8</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Good</span>
                </div>
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                    <span class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center font-bold text-sm">9-10</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Excellent</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function logbookEvaluation() {
    return {
        scores: {
            @foreach($periodsData as $periodData)
            @php
                $periodIndex = $periodData['period_index'] ?? $periodData['month'] ?? $loop->index;
            @endphp
            {{ $periodIndex }}: {{ $periodData['score'] ?? 'null' }},
            @endforeach
        },
        assessmentWeight: {{ $assessmentWeight ?? 0 }},
        maxPossibleScore: {{ $maxPossibleScore }},
        
        get evaluatedCount() {
            return Object.values(this.scores).filter(s => s !== null && s > 0).length;
        },
        
        get totalScore() {
            return Object.values(this.scores).reduce((sum, s) => sum + (s || 0), 0);
        },
        
        get weightedScore() {
            if (this.assessmentWeight <= 0 || this.maxPossibleScore <= 0) return 0;
            return (this.totalScore / this.maxPossibleScore) * this.assessmentWeight;
        },
        
        updateScore(periodIndex, score) {
            this.scores[periodIndex] = score;
        },
        
        getMonthBorderClass(periodIndex) {
            const score = this.scores[periodIndex];
            if (!score) return 'border-gray-200 dark:border-gray-700';
            if (score <= 2) return 'border-red-300 dark:border-red-700';
            if (score <= 4) return 'border-orange-300 dark:border-orange-700';
            if (score <= 6) return 'border-yellow-300 dark:border-yellow-700';
            if (score <= 8) return 'border-blue-300 dark:border-blue-700';
            return 'border-green-300 dark:border-green-700';
        },
        
        getScoreColorClass(score) {
            if (!score) return 'bg-gray-200 dark:bg-gray-600 text-gray-500';
            if (score <= 2) return 'bg-red-500 text-white';
            if (score <= 4) return 'bg-orange-500 text-white';
            if (score <= 6) return 'bg-yellow-500 text-white';
            if (score <= 8) return 'bg-blue-500 text-white';
            return 'bg-green-500 text-white';
        }
    }
}
</script>
