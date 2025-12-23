@extends('layouts.app')

@section('title', 'IP AT Evaluation - ' . $student->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('academic.ip.at.index', request()->query()) }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Student List
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

        <!-- Student Info Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $student->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">{{ $student->matric_no }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2">
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Group</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->group->name ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2">
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Programme</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->programme ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Summary -->
        @php
            $totalAssessments = $atAssessments->count();
            $completedCount = 0;
            foreach($atAssessments as $assessment) {
                // Check if marked
                $mark = $marks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                }
            }
            $progressPercent = $totalAssessments > 0 ? ($completedCount / $totalAssessments) * 100 : 0;
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                     <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Tutor Evaluation Progress</h2>
                     <p class="text-sm text-gray-500 dark:text-gray-400">{{ $completedCount }} of {{ $totalAssessments }} assessments completed</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-[#0084C5]">{{ number_format($totalContribution, 2) }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Contribution</div>
                </div>
            </div>
             <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-[#0084C5] to-[#00A86B] h-3 rounded-full transition-all duration-500" 
                         style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </div>

        <!-- Assessments by CLO -->
        @forelse($assessmentsByClo as $cloCode => $cloAssessments)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-2xl">📋</span>
                    <div>
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">{{ $cloCode }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $cloAssessments->sum('weight_percentage') }}% Total Weight
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cloAssessments as $assessment)
                        @php
                            $mark = $marks->get($assessment->id);
                            $currentMark = $mark?->mark; // 0-5 scale
                            $isCompleted = $currentMark !== null;
                            
                            // Calculate specific contribution for this assessment
                            // Mark is 0-5. Contribution = (Mark/5) * Weight
                            $myContribution = 0;
                            if ($currentMark !== null) {
                                $myContribution = ($currentMark / 5) * $assessment->weight_percentage;
                            }
                        @endphp
                        
                        <!-- Card -->
                        <div class="group relative bg-white dark:bg-gray-800 rounded-xl border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm hover:shadow-lg hover:border-[#0084C5] transition-all duration-300 overflow-hidden"
                             x-data="{ showModal: false }">
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @if($isCompleted)
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs font-medium rounded-full flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Done
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-full">
                                        Pending
                                    </span>
                                @endif
                            </div>

                            <div class="p-5">
                                <div class="mb-4">
                                    <h4 class="font-semibold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight min-h-[3rem]">
                                        {{ $assessment->assessment_name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">
                                            {{ number_format($assessment->weight_percentage, 2) }}% Weight
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block">Score</span>
                                        @if($isCompleted)
                                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($currentMark, 2) }}</span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">/ 5.00</span>
                                        @else
                                            <span class="text-xl font-bold text-gray-400">—</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block">Contribution</span>
                                        <span class="text-lg font-bold text-[#0084C5]">{{ number_format($myContribution, 2) }}%</span>
                                    </div>
                                </div>

                                @can('edit-at-marks', $student)
                                    <button @click="showModal = true" 
                                            class="w-full py-2.5 px-4 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        {{ $isCompleted ? 'Edit Score' : 'Enter Score' }}
                                    </button>
                                @else
                                     <div class="w-full py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium rounded-lg text-center text-sm">
                                        View Only
                                    </div>
                                @endcan
                            </div>

                            <!-- MODAL -->
                            <div x-show="showModal" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                                 @click.self="showModal = false"
                                 style="display: none;">
                                
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
                                    <!-- Modal Header -->
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-[#003A6C] to-[#0084C5]">
                                        <div class="text-white">
                                            <h3 class="text-xl font-bold mb-1">{{ $assessment->assessment_name }}</h3>
                                            <p class="text-sm text-white/80">Rate the student's performance from 1 (Poor) to 5 (Excellent).</p>
                                        </div>
                                        <button @click="showModal = false" class="p-2 hover:bg-white/20 rounded-full text-white transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body (Form) -->
                                    <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900/50">
                                        <form action="{{ route('academic.ip.at.store', $student) }}" method="POST" id="form-{{ $assessment->id }}">
                                            @csrf
                                            <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">

                                            <div class="p-6">
                                                @if($assessment->components->isNotEmpty())
                                                    <!-- Components List -->
                                                    <div class="space-y-4">
                                                        @foreach($assessment->components->sortBy('order') as $component)
                                                            @php
                                                                $cMark = $componentMarks->firstWhere('component_id', $component->id);
                                                                $cScore = $cMark?->rubric_score;
                                                            @endphp
                                                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                                                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                                                    <div class="flex-1">
                                                                        <div class="flex items-center gap-2 mb-1">
                                                                            <h5 class="font-bold text-gray-900 dark:text-white">{{ $component->component_name }}</h5>
                                                                            <span class="text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-500">{{ number_format($component->weight_percentage, 2) }}%</span>
                                                                        </div>
                                                                        @if($component->criteria_keywords)
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">{{ $component->criteria_keywords }}</p>
                                                                        @endif
                                                                    </div>
                                                                    
                                                                    <!-- 1-5 Radios -->
                                                                    <div class="flex items-center gap-1 md:gap-3 bg-gray-50 dark:bg-gray-700/30 p-2 rounded-lg">
                                                                        @for($i=1; $i<=5; $i++)
                                                                            <label class="cursor-pointer">
                                                                                <input type="radio" 
                                                                                       name="component_marks[{{ $component->id }}]" 
                                                                                       value="{{ $i }}" 
                                                                                       {{ $cScore == $i ? 'checked' : '' }} 
                                                                                       required
                                                                                       class="peer sr-only">
                                                                                <div class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-transparent font-bold text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 peer-checked:bg-[#0084C5] peer-checked:text-white peer-checked:shadow-md transition-all">
                                                                                    {{ $i }}
                                                                                </div>
                                                                            </label>
                                                                        @endfor
                                                                    </div>
                                                                </div>
                                                                <!-- Remarks -->
                                                                <div class="mt-3">
                                                                    <input type="text" 
                                                                           name="component_remarks[{{ $component->id }}]" 
                                                                           value="{{ $cMark?->remarks }}"
                                                                           placeholder="Optional remarks..." 
                                                                           class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white placeholder-gray-400">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                     <div class="text-center py-10 text-gray-500 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300">
                                                        <p>No components defined for this assessment.</p>
                                                        <p class="text-xs mt-2">Please ask an administrator to add components to this assessment to enable rubric grading.</p>
                                                     </div>
                                                @endif
                                                
                                                <!-- Overall Remarks -->
                                                <div class="mt-6">
                                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Overall Remarks</label>
                                                    <textarea name="remarks" rows="3" class="w-full border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white p-3">{{ $mark?->remarks }}</textarea>
                                                </div>
                                            </div>

                                            <!-- Modal Footer -->
                                            <div class="flex gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 sticky bottom-0 z-10">
                                                <button type="button" @click="showModal = false" class="flex-1 py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="flex-1 py-2.5 px-4 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors shadow-lg shadow-blue-500/30">
                                                    Save Evaluation
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- END MODAL -->
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
             <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-10 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-xl font-medium text-gray-900 dark:text-white">No Assessments Found</h3>
                <p class="text-gray-500 mt-2">There are no assessments assigned for AT evaluation.</p>
            </div>
        @endforelse
        
         <!-- Read-Only IC Marks Section -->
        @if($icAssessments->isNotEmpty())
            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Industry Coach Evaluation (View Only)
                </h3>
                
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Assessment</th>
                                    <th class="px-6 py-4 font-semibold text-center">Weight</th>
                                    <th class="px-6 py-4 font-semibold text-center">Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($icAssessments as $icAssessment)
                                    @php
                                        $icMark = $icMarks->get($icAssessment->id);
                                        $scoreVal = ($icMark && $icMark->mark !== null && $icMark->max_mark <= 5)
                                            ? number_format($icMark->mark, 2) . ' / 5.00'
                                            : ($icMark && $icMark->mark !== null ? number_format($icMark->mark, 2) . ' / ' . number_format($icMark->max_mark, 2) : '-');
                                    @endphp
                                    <tr class="bg-white dark:bg-gray-800">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $icAssessment->assessment_name }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            {{ $icAssessment->weight_percentage }}%
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-[#003A6C] dark:text-[#0084C5]">
                                            {{ $scoreVal }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold text-gray-900 dark:text-white">
                                <tr>
                                    <td class="px-6 py-4" colspan="2">Total Contribution</td>
                                    <td class="px-6 py-4 text-center text-[#0084C5]">
                                        {{ number_format($icTotalContribution, 2) }}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        
    </div>
</div>
@endsection
