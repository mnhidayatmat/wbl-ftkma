@extends('layouts.app')

@section('title', 'Industrial Training – Industry Coach Evaluation - ' . $student->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('academic.li.ic.index', request()->query()) }}" 
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
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 text-sm w-full md:w-auto">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2">
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Group</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->group->name ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2">
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Programme</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->programme ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2 hidden lg:block">
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Company</span>
                        <span class="font-medium text-gray-900 dark:text-white truncate max-w-[150px] inline-block">{{ $student->company->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Summary -->
        @php
            $totalAssessmentsCount = $icAssessments->count();
            $completedCount = 0;
            foreach($icAssessments as $assessment) {
                $mark = $marks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                }
            }
            $progressPercent = $totalAssessmentsCount > 0 ? ($completedCount / $totalAssessmentsCount) * 100 : 0;
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                     <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Industry Coach Evaluation Progress</h2>
                     <p class="text-sm text-gray-500 dark:text-gray-400">{{ $completedCount }} of {{ $totalAssessmentsCount }} assessments completed</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-[#0084C5]">{{ number_format($totalContribution, 2) }}%</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Contribution</div>
                </div>
            </div>
             <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-[#0084C5] to-[#10b981] h-3 rounded-full transition-all duration-500 shadow-sm" 
                         style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </div>

        <!-- Assessments by CLO -->
        @forelse($assessmentsByClo as $cloCode => $cloAssessments)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] uppercase tracking-wide">{{ $cloCode }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                             Weight: {{ number_format($cloAssessments->sum('weight_percentage'), 2) }}%
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cloAssessments as $assessment)
                        @php
                            $mark = $marks->get($assessment->id);
                            $currentMark = $mark?->mark; 
                            $maxMarkVal = $mark?->max_mark ?? 100;
                            $isCompleted = $currentMark !== null;
                            
                            // Check if this is a logbook assessment
                            $isLogbook = str_contains(strtolower($assessment->assessment_name), 'logbook') || $assessment->assessment_type === 'Logbook';
                            
                            // Calculate specific contribution
                            $myAssessmentWeight = $assessment->weight_percentage;
                            $myContribution = 0;
                            if ($isCompleted && $maxMarkVal > 0) {
                                $myContribution = ($currentMark / $maxMarkVal) * $myAssessmentWeight;
                            }
                            
                            // For logbook, check logbook evaluations
                            if ($isLogbook) {
                                $logbookEvaluations = \App\Models\LiLogbookEvaluation::forStudent($student->id)->get();
                                $completedMonths = $logbookEvaluations->whereNotNull('score')->count();
                                $totalScore = $logbookEvaluations->sum('score');
                                $isCompleted = $completedMonths === 6;
                            }
                        @endphp
                        
                        @if($isLogbook)
                            {{-- Logbook Card - Links to Logbook Evaluation --}}
                            <a href="{{ route('academic.li.logbook.show', ['student' => $student->id]) }}"
                               class="group block bg-white dark:bg-gray-800 rounded-2xl border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm hover:shadow-lg hover:border-[#0084C5] transition-all duration-300 overflow-hidden flex flex-col h-full">
                                
                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3 z-10">
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

                                <div class="p-5 relative flex-1 flex flex-col">
                                    <div class="mb-4">
                                        <h4 class="font-semibold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight group-hover:text-[#0084C5]">
                                            {{ $assessment->assessment_name }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded">
                                                Logbook
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-auto">
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Progress</span>
                                                <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $completedMonths ?? 0 }}/6</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Total Score</span>
                                                <span class="text-lg font-bold text-[#0084C5]">{{ $totalScore ?? 0 }}/60</span>
                                            </div>
                                        </div>

                                        <div class="w-full py-2.5 px-4 bg-[#0084C5] group-hover:bg-[#003A6C] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            {{ $isCompleted ? 'View Logbook' : 'Evaluate Logbook' }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @else
                        <!-- Assessment Card -->
                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-800/50 shadow-green-50/50' : 'border-gray-100 dark:border-gray-700' }} shadow-sm hover:shadow-xl hover:border-[#0084C5]/30 transition-all duration-300 flex flex-col h-full"
                             x-data="{ showModal: false }">
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                @if($isCompleted)
                                    <span class="px-2.5 py-1 bg-green-50 dark:bg-green-900/40 text-green-600 dark:text-green-300 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-green-100 dark:border-green-800 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                        Done
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-amber-100 dark:border-amber-800">
                                        Pending
                                    </span>
                                @endif
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <div class="mb-4">
                                    <h4 class="font-bold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight min-h-[40px] text-base group-hover:text-[#0084C5] transition-colors">
                                        {{ $assessment->assessment_name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded uppercase tracking-tighter border border-blue-100 dark:border-blue-800">
                                            {{ number_format($assessment->weight_percentage, 2) }}% Weight
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <div class="flex items-center justify-between mb-4 bg-gray-50 dark:bg-gray-700/30 p-3 rounded-xl">
                                        <div>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold uppercase tracking-widest">Score</span>
                                            @if($isCompleted)
                                                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($currentMark, 2) }}</span>
                                                <span class="text-xs text-gray-400">/ {{ number_format($maxMarkVal, 2) }}</span>
                                            @else
                                                <span class="text-2xl font-black text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-bold uppercase tracking-widest">Mark</span>
                                            <span class="text-xl font-black text-[#0084C5]">{{ number_format($myContribution, 2) }}%</span>
                                        </div>
                                    </div>

                                    @can('edit-li-ic-marks', $student)
                                        <button @click="showModal = true" 
                                                class="w-full py-3 px-4 bg-[#0084C5] hover:bg-[#003A6C] text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 active:scale-95">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            {{ $isCompleted ? 'Edit Evaluation' : 'Evaluate Now' }}
                                        </button>
                                    @else
                                         <div class="w-full py-3 px-4 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold rounded-xl text-center text-xs uppercase tracking-widest">
                                            Read Only Access
                                        </div>
                                    @endcan
                                </div>
                            </div>

                            <!-- MODAL FOR EVALUATION -->
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
                                
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col"
                                     @click.stop
                                     style="width: 95%; max-width: 1100px; height: auto; max-height: 80vh; margin: auto;">
                                    <!-- Modal Header -->
                                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex-shrink-0">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                                                {{ $assessment->assessment_name }} – Rubric Evaluation
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Select ONE performance level per criterion.
                                                <span class="text-red-500 ml-1">*</span>
                                            </p>
                                        </div>
                                        <button @click="showModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="flex-1 overflow-y-auto" style="max-height: calc(80vh - 180px);">
                                        <form action="{{ route('academic.li.ic.store', $student) }}" method="POST" id="form-{{ $assessment->id }}">
                                            @csrf
                                            <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                                            
                                            @php
                                                // Get components from the assessment
                                                $components = $assessment->components->sortBy('order');
                                                $hasComponents = $components->count() > 0;
                                                
                                                // Get existing component marks
                                                $componentMarks = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
                                                    ->where('assessment_id', $assessment->id)
                                                    ->get()
                                                    ->keyBy('component_id');
                                                
                                                $ratingLabels = ['Poor', 'Limited', 'Fair', 'Good', 'Excellent'];
                                            @endphp
                                            
                                            @if($hasComponents)
                                                <!-- Standard Academic Rubric Table -->
                                                <div class="overflow-x-auto p-6">
                                                    <table class="w-full border-collapse rubric-table">
                                                        <thead class="sticky top-0 z-20 bg-white dark:bg-gray-800 shadow-sm">
                                                            <tr>
                                                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-700 w-1/3">
                                                                    Component
                                                                </th>
                                                                @foreach($ratingLabels as $index => $label)
                                                                <th class="text-center py-4 px-4 text-xs font-semibold text-gray-600 dark:text-gray-400 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                                                    {{ $label }}
                                                                </th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white dark:bg-gray-800">
                                                            @foreach($components as $index => $component)
                                                                @php
                                                                    $componentMark = $hasComponents && $component->id ? $componentMarks->get($component->id) : null;
                                                                    $currentScore = $componentMark?->rubric_score;
                                                                @endphp
                                                                <tr class="rubric-row border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors" 
                                                                    x-data="{ showRemarks: false }"
                                                                    data-component-id="{{ $hasComponents && $component->id ? $component->id : 'temp_' . $index }}">
                                                                    <!-- Left Column: Component Info -->
                                                                    <td class="py-4 px-6 align-top">
                                                                        <div class="space-y-2">
                                                                            <div class="font-semibold text-gray-900 dark:text-white text-sm">
                                                                                {{ $component->component_name }}
                                                                            </div>
                                                                            @if($hasComponents && $component->clo_code)
                                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                                <span class="px-2 py-0.5 bg-[#003A6C]/10 dark:bg-[#0084C5]/20 text-[#003A6C] dark:text-[#0084C5] text-xs font-medium rounded">
                                                                                    {{ $component->clo_code }}
                                                                                </span>
                                                                                @if($component->weight_percentage)
                                                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                                                                    {{ number_format($component->weight_percentage, 2) }}%
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                            @endif
                                                                            @if($component->criteria_keywords)
                                                                            <div class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed italic">
                                                                                {{ $component->criteria_keywords }}
                                                                            </div>
                                                                            @endif
                                                                            @can('edit-ic-marks', $student)
                                                                            <button type="button" 
                                                                                    @click="showRemarks = !showRemarks" 
                                                                                    class="text-xs text-[#0084C5] hover:text-[#003A6C] dark:text-[#0084C5] dark:hover:text-[#00A86B] mt-1 flex items-center gap-1 transition-colors font-medium">
                                                                                <span x-text="showRemarks ? '▲ Hide remarks' : '▼ Add remarks'"></span>
                                                                            </button>
                                                                            <div x-show="showRemarks" 
                                                                                 x-collapse 
                                                                                 class="mt-2 space-y-1">
                                                                                <textarea name="component_remarks[{{ $hasComponents && $component->id ? $component->id : 'temp_' . $index }}]" 
                                                                                          rows="2" 
                                                                                          placeholder="Optional remarks for this component..."
                                                                                          class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] resize-none">{{ $componentMark?->remarks }}</textarea>
                                                                                <p class="text-xs text-gray-400 dark:text-gray-500 italic">
                                                                                    Remarks are recommended for Fair or below.
                                                                                </p>
                                                                            </div>
                                                                            @elseif($componentMark?->remarks)
                                                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic bg-gray-50 dark:bg-gray-700/50 p-2 rounded">
                                                                                {{ $componentMark->remarks }}
                                                                            </div>
                                                                            @endcan
                                                                        </div>
                                                                    </td>
                                                                    <!-- Rating Columns: Radio Buttons -->
                                                                    @for($score = 1; $score <= 5; $score++)
                                                                    <td class="py-4 px-3 text-center align-middle rating-cell-wrapper transition-all duration-200">
                                                                        <label class="rubric-radio-label inline-flex items-center justify-center cursor-pointer w-full py-3 rounded-md transition-all duration-200
                                                                            @if($currentScore == $score)
                                                                                
                                                                            @else
                                                                                hover:bg-gray-50 dark:hover:bg-gray-700/30
                                                                            @endif">
                                                                            <input type="radio" 
                                                                                   name="component_marks[{{ $hasComponents && $component->id ? $component->id : 'temp_' . $index }}]" 
                                                                                   value="{{ $score }}"
                                                                                   {{ $currentScore == $score ? 'checked' : '' }}
                                                                                   class="rubric-radio w-5 h-5 text-[#0084C5] border-2 focus:ring-2 focus:ring-[#0084C5] focus:ring-offset-1 cursor-pointer transition-all
                                                                                   @if($currentScore == $score)
                                                                                       border-[#0084C5] dark:border-[#0084C5] bg-[#0084C5] dark:bg-[#0084C5]
                                                                                   @else
                                                                                       border-gray-300 dark:border-gray-600
                                                                                   @endif"
                                                                                   onchange="updateRubricSelection(this)"
                                                                                   data-component-row="{{ $hasComponents && $component->id ? $component->id : 'temp_' . $index }}">
                                                                            <span class="sr-only">{{ $ratingLabels[$score - 1] }}</span>
                                                                        </label>
                                                                    </td>
                                                                    @endfor
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <!-- Overall Remarks Section -->
                                                <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Summative Feedback / Coach Remarks (Optional)
                                                        </span>
                                                    </label>
                                                    <textarea name="remarks"
                                                              rows="4"
                                                              class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] transition-all resize-none"
                                                              placeholder="Enter comments on student's performance...">{{ $mark?->remarks ?? '' }}</textarea>
                                                </div>
                                            @elseif($assessment->rubrics->isNotEmpty())
                                                <!-- Legacy Rubrics (keep existing structure for backward compatibility) -->
                                                <div class="p-6 space-y-6">
                                                    @foreach($assessment->rubrics as $rubric)
                                                         @php $rMark = $rubricMarks->get($rubric->id); @endphp
                                                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                                                            <h5 class="font-bold text-gray-900 dark:text-white mb-4">{{ $rubric->question_title }}</h5>
                                                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                                                @for($i=$rubric->rubric_min; $i<=$rubric->rubric_max; $i++)
                                                                    <label class="cursor-pointer group flex flex-col gap-2">
                                                                        <input type="radio" name="rubric_scores[{{ $rubric->id }}]" value="{{ $i }}" {{ $rMark?->rubric_score == $i ? 'checked' : '' }} class="peer sr-only">
                                                                        <div class="py-4 text-center border-2 border-gray-100 dark:border-gray-700 rounded-2xl font-bold text-gray-400 peer-checked:border-[#00A86B] peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 peer-checked:text-[#00A86B] transition-all">
                                                                            <span class="text-xl block">{{ $i }}</span>
                                                                        </div>
                                                                    </label>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    <!-- Overall Remarks -->
                                                    <div class="mt-6">
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Summative Feedback / Coach Remarks</label>
                                                        <textarea name="remarks" rows="4" class="w-full border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white p-3">{{ $mark?->remarks }}</textarea>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Simple Mark Input (keep existing structure) -->
                                                <div class="p-6">
                                                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm max-w-lg mx-auto">
                                                        <div class="grid grid-cols-2 gap-6">
                                                            <div>
                                                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Obtained Mark</label>
                                                                <input type="number" step="0.01" name="mark" value="{{ $mark?->mark }}" class="w-full text-2xl font-black border-gray-200 dark:border-gray-700 rounded-2xl focus:ring-[#00A86B] focus:border-[#00A86B] dark:bg-gray-700 dark:text-white p-4" placeholder="0.00">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Max Mark</label>
                                                                <input type="number" step="0.01" name="max_mark" value="{{ $mark?->max_mark ?? 100 }}" class="w-full text-2xl font-black border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 text-gray-400 cursor-not-allowed" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Overall Remarks -->
                                                    <div class="mt-6">
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Summative Feedback / Coach Remarks</label>
                                                        <textarea name="remarks" rows="4" class="w-full border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white p-3">{{ $mark?->remarks }}</textarea>
                                                    </div>
                                                </div>
                                            @endif
                                        </form>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex-shrink-0">
                                        <button type="button" 
                                                @click="showModal = false"
                                                class="px-6 py-2.5 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors border border-gray-300 dark:border-gray-600">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                                form="form-{{ $assessment->id }}"
                                                id="saveBtn-{{ $assessment->id }}"
                                                class="px-6 py-2.5 bg-[#0084C5] hover:bg-[#0073a3] text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed">
                                            Save Evaluation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @empty
             <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-md p-16 text-center border-2 border-dashed border-gray-100 dark:border-gray-800">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No IC Assessments Found</h3>
                <p class="text-gray-500 text-sm mt-2 max-w-sm mx-auto">There are currently no active assessments for the Industry Coach role for this student.</p>
            </div>
        @endforelse

        <!-- Read-Only Section for LI Supervisor -->
        @if($supervisorAssessments->isNotEmpty())
            <div class="mt-12 pt-12 border-t-2 border-dashed border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <svg class="w-6 h-6 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Supervisor Evaluation</h3>
                        <p class="text-[10px] text-blue-600 dark:text-blue-400 font-black uppercase tracking-[0.2em]">Marks recorded by Faculty Supervisor</p>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50/50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500">
                                <tr>
                                    <th class="px-8 py-5 text-[10px] uppercase font-black tracking-widest">Assessment Name</th>
                                    <th class="px-8 py-5 text-[10px] uppercase font-black tracking-widest text-center">Score / Max</th>
                                    <th class="px-8 py-5 text-[10px] uppercase font-black tracking-widest text-right">Contribution</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                @foreach($supervisorAssessments as $sAssessment)
                                    @php
                                        $sMark = $supervisorMarks->get($sAssessment->id);
                                        $mVal = $sMark?->mark;
                                        $maxV = $sMark?->max_mark ?? 100;
                                        $cVal = ($mVal !== null && $maxV > 0) ? ($mVal / $maxV) * $sAssessment->weight_percentage : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                        <td class="px-8 py-5 font-bold text-gray-900 dark:text-white">
                                            {{ $sAssessment->assessment_name }}
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @if($mVal !== null)
                                                <span class="text-base font-black text-gray-900 dark:text-white">{{ number_format($mVal, 2) }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold">/{{ number_format($maxV, 2) }}</span>
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600 italic">—</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-5 text-right font-black text-[#0084C5]">
                                            {{ number_format($cVal, 2) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50/50 dark:bg-gray-700/50">
                                <tr>
                                    <td class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-500">Total Supervisor Contribution</td>
                                    <td colspan="2" class="px-8 py-6 text-right text-2xl font-black text-[#0084C5]">
                                        {{ number_format($supervisorTotalContribution, 2) }}%
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

<script>
// Function to update rubric selection visual state
function updateRubricSelection(radioInput) {
    const row = radioInput.closest('tr.rubric-row');
    if (!row) return;
    
    // Get all rating cells in this row
    const allLabels = row.querySelectorAll('.rubric-radio-label');
    const allRadios = row.querySelectorAll('.rubric-radio');
    
    // Remove selected state from all labels in the row
    allLabels.forEach(label => {
        label.classList.remove('bg-gray-100/60', 'dark:bg-gray-700/40', 'ring-1', 'ring-gray-300', 'dark:ring-gray-600');
        // Keep hover state for unselected
        if (!label.querySelector('.rubric-radio:checked')) {
            label.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-700/30');
        }
    });
    
    // Reset all radio buttons
    allRadios.forEach(radio => {
        radio.classList.remove('border-[#0084C5]', 'dark:border-[#0084C5]', 'bg-[#0084C5]', 'dark:bg-[#0084C5]');
        radio.classList.add('border-gray-300', 'dark:border-gray-600');
    });
    
    // Apply selected state to the clicked cell
    if (radioInput.checked) {
        const selectedLabel = radioInput.closest('.rubric-radio-label');
        
        // Remove any background - keep transparent
        selectedLabel.classList.remove('hover:bg-gray-50', 'dark:hover:bg-gray-700/30', 'bg-gray-100/60', 'dark:bg-gray-700/40', 'ring-1', 'ring-gray-300', 'dark:ring-gray-600');
        
        // Update radio button style - filled with primary color only
        radioInput.classList.remove('border-gray-300', 'dark:border-gray-600');
        radioInput.classList.add('border-[#0084C5]', 'dark:border-[#0084C5]', 'bg-[#0084C5]', 'dark:bg-[#0084C5]');
    }
    
    // Validate form and update save button state
    validateRubricForm(radioInput);
}

// Validate rubric form - ensure all rows have a selection
function validateRubricForm(changedInput) {
    // Find the form containing this input
    const form = changedInput.closest('form');
    if (!form) return;
    
    // Find all rubric rows in this form
    const rows = form.querySelectorAll('tr.rubric-row');
    let allComplete = true;
    
    rows.forEach(row => {
        const radios = row.querySelectorAll('.rubric-radio');
        const hasSelection = Array.from(radios).some(radio => radio.checked);
        
        if (!hasSelection) {
            allComplete = false;
        }
    });
    
    // Find save button for this form
    const formId = form.id;
    const saveButton = document.querySelector(`button[form="${formId}"]`);
    
    if (saveButton) {
        // Allow saving even if not all complete for IC (more flexible)
        saveButton.disabled = false;
        saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Initialize rubric forms on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial state for all checked radios
    document.querySelectorAll('.rubric-radio:checked').forEach(radio => {
        updateRubricSelection(radio);
    });
    
    // Add keyboard navigation support
    document.querySelectorAll('.rubric-radio').forEach(radio => {
        radio.addEventListener('keydown', function(e) {
            const row = this.closest('tr.rubric-row');
            const allRadios = Array.from(row.querySelectorAll('.rubric-radio'));
            const currentIndex = allRadios.indexOf(this);
            
            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                e.preventDefault();
                allRadios[currentIndex - 1].focus();
                allRadios[currentIndex - 1].click();
            } else if (e.key === 'ArrowRight' && currentIndex < allRadios.length - 1) {
                e.preventDefault();
                allRadios[currentIndex + 1].focus();
                allRadios[currentIndex + 1].click();
            }
        });
    });
});
</script>
@endsection
