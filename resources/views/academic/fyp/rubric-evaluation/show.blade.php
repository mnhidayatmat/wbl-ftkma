@extends('layouts.app')

@section('title', 'Rubric Evaluation - ' . $student->name . ' (' . ($evaluatorRole === 'at' ? 'AT' : 'IC') . ')')

@section('content')
<div class="py-6" x-data="rubricEvaluation()">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button & Student Info -->
        <div class="mb-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <a href="{{ route('academic.fyp.rubric-evaluation.index', ['template' => $template->id, 'role' => $evaluatorRole]) }}" 
                   class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Student List
                </a>
                <h1 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                    {{ $student->name }}
                    <span class="ml-2 px-2 py-1 text-sm font-medium rounded {{ $evaluatorRole === 'at' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $evaluatorRole === 'at' ? 'AT Evaluation' : 'IC Evaluation' }}
                    </span>
                </h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $student->matric_no }} | {{ $student->group->name ?? 'No Group' }}</p>
            </div>
            
            <!-- Template Selector -->
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">Rubric:</label>
                <select onchange="window.location.href='{{ route('academic.fyp.rubric-evaluation.show', $student) }}?template=' + this.value + '&role={{ $evaluatorRole }}'"
                        class="px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                    @foreach($allTemplates as $t)
                        <option value="{{ $t->id }}" {{ $t->id == $template->id ? 'selected' : '' }}>
                            {{ $t->phase }} - {{ number_format($t->component_marks, 0) }}%
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- Assessment Info Banner -->
        <div class="mb-4 p-3 bg-gradient-to-r from-[#003A6C] to-[#0084C5] rounded-lg text-white">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <span class="text-sm opacity-80">{{ $template->phase }} {{ $template->assessment_type }} Report</span>
                    <span class="mx-2">•</span>
                    <span class="font-semibold">{{ $evaluatorRole === 'at' ? 'Academic Tutor (AT)' : 'Industry Coach (IC)' }}</span>
                </div>
                <div class="text-right">
                    <span class="text-sm opacity-80">Component Weight:</span>
                    <span class="ml-1 px-2 py-1 bg-yellow-400 text-yellow-900 font-bold rounded">{{ number_format($componentMarks, 0) }}%</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('academic.fyp.rubric-evaluation.store', $student) }}" method="POST" id="evaluationForm">
            @csrf
            <input type="hidden" name="template_id" value="{{ $template->id }}">

            <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-6">
                <!-- Main Evaluation Grid -->
                <div class="space-y-6">
                    <!-- Performance Levels Legend (Sticky) -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sticky top-0 z-20">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Performance Levels:</span>
                            @foreach(\App\Models\FYP\FypRubricTemplate::PERFORMANCE_LEVELS as $level => $label)
                                <span class="px-3 py-1 text-xs font-medium rounded
                                    @if($level == 1) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @elseif($level == 2) bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300
                                    @elseif($level == 3) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($level == 4) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @endif">
                                    {{ $level }} - {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Rubric Grid by CLO -->
                    @foreach($elementsByClo as $cloCode => $elements)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                            <div class="p-4 bg-[#003A6C] text-white">
                                <h2 class="font-semibold">{{ $cloCode }} - {{ $elements->sum('weight_percentage') }}% Total Weight</h2>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-[60px] z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-56 min-w-[200px]">
                                                Element
                                            </th>
                                            @foreach(\App\Models\FYP\FypRubricTemplate::PERFORMANCE_LEVELS as $level => $label)
                                                <th class="px-2 py-3 text-center text-xs font-medium uppercase min-w-[120px]
                                                    @if($level == 1) text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400
                                                    @elseif($level == 2) text-orange-600 bg-orange-50 dark:bg-orange-900/20 dark:text-orange-400
                                                    @elseif($level == 3) text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400
                                                    @elseif($level == 4) text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400
                                                    @else text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400
                                                    @endif">
                                                    {{ $level }}<br><span class="text-[10px] normal-case font-normal">{{ $label }}</span>
                                                </th>
                                            @endforeach
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-24">
                                                Score
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($elements as $element)
                                            @php
                                                $evaluation = $evaluations->get($element->id);
                                                $selectedLevel = $evaluation?->selected_level;
                                                $weightedScore = $evaluation?->weighted_score ?? 0;
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" 
                                                x-data="{ showRemarks: false }">
                                                <!-- Element Info -->
                                                <td class="px-4 py-4 align-top">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-mono rounded">
                                                            {{ $element->element_code }}
                                                        </span>
                                                        <span class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 text-xs font-medium rounded">
                                                            {{ number_format($element->weight_percentage, 1) }}%
                                                        </span>
                                                    </div>
                                                    <div class="font-medium text-gray-900 dark:text-gray-200 text-sm">{{ $element->name }}</div>
                                                    @if($element->description)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $element->description }}</div>
                                                    @endif
                                                    @if($canEdit)
                                                        <button type="button" @click="showRemarks = !showRemarks" 
                                                                class="text-xs text-[#0084C5] hover:underline mt-2">
                                                            <span x-text="showRemarks ? '▲ Hide remarks' : '▼ Add remarks'"></span>
                                                        </button>
                                                        <div x-show="showRemarks" x-collapse class="mt-2">
                                                            <textarea name="evaluations[{{ $element->id }}][remarks]" 
                                                                      rows="2" 
                                                                      placeholder="Optional remarks..."
                                                                      class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded focus:outline-none focus:ring-1 focus:ring-[#0084C5]">{{ $evaluation?->remarks }}</textarea>
                                                        </div>
                                                    @elseif($evaluation?->remarks)
                                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic">
                                                            {{ $evaluation->remarks }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <input type="hidden" name="evaluations[{{ $element->id }}][element_id]" value="{{ $element->id }}">

                                                <!-- Level Selection Cells -->
                                                @foreach(\App\Models\FYP\FypRubricTemplate::PERFORMANCE_LEVELS as $level => $label)
                                                    @php
                                                        $descriptor = $element->levelDescriptors->firstWhere('level', $level);
                                                    @endphp
                                                    <td class="px-2 py-4 align-top text-center
                                                        @if($level == 1) bg-red-50/50 dark:bg-red-900/10
                                                        @elseif($level == 2) bg-orange-50/50 dark:bg-orange-900/10
                                                        @elseif($level == 3) bg-yellow-50/50 dark:bg-yellow-900/10
                                                        @elseif($level == 4) bg-blue-50/50 dark:bg-blue-900/10
                                                        @else bg-green-50/50 dark:bg-green-900/10
                                                        @endif
                                                        {{ $selectedLevel == $level ? 'ring-2 ring-inset ring-[#0084C5]' : '' }}">
                                                        @if($canEdit)
                                                            <label class="cursor-pointer block p-2 rounded hover:bg-white/50 dark:hover:bg-gray-700/50 transition-colors">
                                                                <input type="radio" 
                                                                       name="evaluations[{{ $element->id }}][level]" 
                                                                       value="{{ $level }}"
                                                                       {{ $selectedLevel == $level ? 'checked' : '' }}
                                                                       class="w-5 h-5 text-[#0084C5] border-gray-300 focus:ring-[#0084C5] rubric-level-radio"
                                                                       data-element-id="{{ $element->id }}"
                                                                       data-weight="{{ $element->weight_percentage }}"
                                                                       @change="updateScore({{ $element->id }}, {{ $level }}, {{ $element->weight_percentage }})">
                                                                @if($descriptor)
                                                                    <div class="mt-2 text-[10px] text-gray-600 dark:text-gray-400 leading-tight max-h-20 overflow-y-auto">
                                                                        {{ Str::limit($descriptor->descriptor, 80) }}
                                                                    </div>
                                                                @endif
                                                            </label>
                                                        @else
                                                            <div class="p-2 {{ $selectedLevel == $level ? 'bg-[#0084C5]/10 rounded' : '' }}">
                                                                @if($selectedLevel == $level)
                                                                    <svg class="w-6 h-6 text-[#0084C5] mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                    </svg>
                                                                @endif
                                                                @if($descriptor)
                                                                    <div class="mt-2 text-[10px] text-gray-600 dark:text-gray-400 leading-tight">
                                                                        {{ Str::limit($descriptor->descriptor, 80) }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach

                                                <!-- Score -->
                                                <td class="px-4 py-4 text-center align-middle">
                                                    <span class="text-lg font-bold text-[#0084C5]" id="score-{{ $element->id }}">
                                                        {{ number_format($weightedScore, 2) }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <!-- Overall Feedback -->
                    @if($canEdit)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Overall Feedback</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">General Comments</label>
                                    <textarea name="overall_feedback" rows="3" 
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]"
                                              placeholder="Overall feedback for the student...">{{ $overallFeedback->overall_feedback ?? '' }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Strengths</label>
                                        <textarea name="strengths" rows="2" 
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]"
                                                  placeholder="What the student did well...">{{ $overallFeedback->strengths ?? '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Areas for Improvement</label>
                                        <textarea name="areas_for_improvement" rows="2" 
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]"
                                                  placeholder="Areas that need improvement...">{{ $overallFeedback->areas_for_improvement ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($overallFeedback->overall_feedback || $overallFeedback->strengths || $overallFeedback->areas_for_improvement)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Overall Feedback</h3>
                            @if($overallFeedback->overall_feedback)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">General Comments</label>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $overallFeedback->overall_feedback }}</p>
                                </div>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($overallFeedback->strengths)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Strengths</label>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $overallFeedback->strengths }}</p>
                                    </div>
                                @endif
                                @if($overallFeedback->areas_for_improvement)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Areas for Improvement</label>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $overallFeedback->areas_for_improvement }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sticky Summary Panel -->
                <div class="xl:block">
                    <div class="sticky top-6 space-y-4">
                        <!-- Score Summary -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Rubric Score</h3>
                            <div class="text-4xl font-bold text-[#0084C5] mb-1" id="totalScore">
                                {{ number_format($totalScore, 2) }}%
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                out of {{ number_format($totalWeight, 2) }}%
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-4">
                                <div id="progressBar" class="bg-[#0084C5] h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ min($percentageScore, 100) }}%"></div>
                            </div>

                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span id="percentageScore">{{ number_format($percentageScore, 1) }}</span>% of maximum score
                            </div>
                            
                            <!-- Contribution to Grade -->
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Contribution to FYP Grade</h4>
                                <div class="text-2xl font-bold text-green-600" id="contributionScore">
                                    {{ number_format($contributionToGrade, 2) }}%
                                </div>
                                <div class="text-xs text-gray-500">
                                    (Score × {{ number_format($componentMarks, 0) }}% component weight)
                                </div>
                            </div>
                        </div>

                        @if($canEdit)
                            <!-- Save Button -->
                            <button type="submit" 
                                    class="w-full px-6 py-3 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors shadow-md flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Evaluation
                            </button>

                            @if($overallFeedback->id && $overallFeedback->status !== 'submitted')
                                <form action="{{ route('academic.fyp.rubric-evaluation.submit', [$student, $template]) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-md flex items-center justify-center gap-2"
                                            onclick="return confirm('Are you sure you want to submit this evaluation? Make sure all elements are assessed.')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Submit Evaluation
                                    </button>
                                </form>
                            @endif
                        @endif

                        <!-- Status Badge -->
                        @if($overallFeedback->id)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                @if($overallFeedback->status === 'released')
                                    <span class="ml-2 px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-sm font-medium rounded-full">
                                        Released
                                    </span>
                                @elseif($overallFeedback->status === 'submitted')
                                    <span class="ml-2 px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-sm font-medium rounded-full">
                                        Submitted
                                    </span>
                                @else
                                    <span class="ml-2 px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 text-sm font-medium rounded-full">
                                        Draft
                                    </span>
                                @endif
                            </div>
                        @endif

                        @if(auth()->user()->isAdmin() && $overallFeedback->status === 'submitted')
                            <form action="{{ route('academic.fyp.rubric-evaluation.release', [$student, $template]) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors shadow-md flex items-center justify-center gap-2"
                                        onclick="return confirm('Release this evaluation to the student?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Release to Student
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function rubricEvaluation() {
    return {
        totalWeight: {{ $totalWeight }},
        componentMarks: {{ $componentMarks }},
        
        updateScore(elementId, level, weight) {
            // Calculate weighted score for this element
            const maxLevel = 5;
            const weightedScore = (level / maxLevel) * weight;
            
            // Update individual score display
            const scoreEl = document.getElementById(`score-${elementId}`);
            if (scoreEl) {
                scoreEl.textContent = weightedScore.toFixed(2) + '%';
            }
            
            // Update total score
            this.updateTotalScore();
        },
        
        updateTotalScore() {
            const radios = document.querySelectorAll('.rubric-level-radio:checked');
            let total = 0;
            
            radios.forEach(radio => {
                const level = parseInt(radio.value);
                const weight = parseFloat(radio.dataset.weight);
                const maxLevel = 5;
                total += (level / maxLevel) * weight;
            });
            
            // Update display
            document.getElementById('totalScore').textContent = total.toFixed(2) + '%';
            
            const percentage = this.totalWeight > 0 ? (total / this.totalWeight) * 100 : 0;
            document.getElementById('percentageScore').textContent = percentage.toFixed(1);
            document.getElementById('progressBar').style.width = Math.min(percentage, 100) + '%';
            
            // Update contribution to grade
            const contribution = total * (this.componentMarks / 100);
            const contributionEl = document.getElementById('contributionScore');
            if (contributionEl) {
                contributionEl.textContent = contribution.toFixed(2) + '%';
            }
        },
        
        init() {
            // Initialize on page load
            this.updateTotalScore();
        }
    }
}
</script>
@endsection
