@extends('layouts.app')

@section('title', 'IP IC Evaluation - ' . $student->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('academic.ip.ic.index', request()->query()) }}" 
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

        @if($isViewOnly ?? false)
            <div class="mb-4 p-4 bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-300 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span class="font-medium">View Only Mode</span> - You are viewing this evaluation as IP Coordinator. Editing is not available.
            </div>
        @endif

        <!-- Student Info Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $student->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">{{ $student->matric_no }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2 border border-gray-100 dark:border-gray-600">
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Group</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->group->name ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-2 border border-gray-100 dark:border-gray-600">
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Company (from IC)</span>
                        <span class="font-medium text-gray-900 dark:text-white truncate max-w-[150px]" title="{{ $student->industryCoach->company->company_name ?? 'N/A' }}">
                            {{ $student->industryCoach->company->company_name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Summary -->
        @php
            $totalAssessments = $allIcAssessments->count();
            $completedCount = 0;
            foreach($allIcAssessments as $assessment) {
                // Check if marked
                $mark = $marks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedCount++;
                } else {
                     // Check rubric marks if no main mark
                     if($assessment->rubrics->isNotEmpty()) {
                        $allRubricsMarked = true;
                        foreach($assessment->rubrics as $rubric) {
                            if(!$rubricMarks->has($rubric->id)) {
                                $allRubricsMarked = false;
                                break;
                            }
                        }
                        if($allRubricsMarked) $completedCount++;
                     }
                }
            }
            $progressPercent = $totalAssessments > 0 ? ($completedCount / $totalAssessments) * 100 : 0;
            $canEdit = !($isViewOnly ?? false) && Gate::allows('edit-ic-marks', $student);
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8 border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                     <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Industry Coach Evaluation Progress</h2>
                     <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $completedCount }} of {{ $totalAssessments }} assessments completed</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <div class="text-4xl font-black text-[#0084C5] tracking-tight">{{ number_format($totalContribution, 2) }}%</div>
                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Contribution</div>
                    </div>
                </div>
            </div>
             <div class="mt-6">
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-4 overflow-hidden shadow-inner p-1">
                    <div class="bg-gradient-to-r from-[#0084C5] to-[#00A86B] h-full rounded-full transition-all duration-700" 
                         style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </div>

        <!-- Assessments by CLO -->
        @forelse($assessmentsByClo as $cloCode => $cloAssessments)
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-[#0084C5]/10 rounded-lg flex items-center justify-center text-[#0084C5]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $cloCode }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $cloAssessments->sum(function($a) {
                                $eval = $a->evaluators->firstWhere('evaluator_role', 'ic');
                                return $eval ? $eval->total_score : 0;
                            }) }}% Collective Weight
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($cloAssessments as $assessment)
                        @php
                            $icEvaluator = $assessment->evaluators->firstWhere('evaluator_role', 'ic');
                            $weight = $icEvaluator ? $icEvaluator->total_score : 0;
                            
                            // Check if this is a logbook assessment
                            $isLogbook = str_contains(strtolower($assessment->assessment_name), 'logbook') || $assessment->assessment_type === 'Logbook';
                            
                            $mark = $marks->get($assessment->id);
                            $isCompleted = false;
                            $myContribution = 0;
                            $scoreLabel = "-";
                            $maxLabel = "";

                            // For logbook, check logbook evaluations
                            if ($isLogbook) {
                                $logbookEvaluations = \App\Models\IpLogbookEvaluation::forStudent($student->id)->get();
                                $logbookComponents = $assessment->components->whereNotNull('duration_label');
                                $totalPeriods = $logbookComponents->count() ?: 6;
                                $completedPeriods = $logbookEvaluations->whereNotNull('score')->count();
                                $totalScore = $logbookEvaluations->sum('score');
                                $maxPossibleScore = $totalPeriods * 10;
                                $isCompleted = $completedPeriods === $totalPeriods && $totalPeriods > 0;
                            } elseif ($assessment->components->isNotEmpty()) {
                                $isCompleted = $mark && $mark->mark !== null;
                                if ($isCompleted) {
                                    $myContribution = ($mark->mark / 5) * $weight;
                                    $scoreLabel = number_format($mark->mark, 2);
                                    $maxLabel = " / 5.00";
                                }
                            } elseif ($assessment->rubrics->isNotEmpty()) {
                                $rubricDone = 0;
                                $rubricWeightSum = 0;
                                foreach($assessment->rubrics as $rubric) {
                                    $rMark = $rubricMarks->get($rubric->id);
                                    if($rMark) {
                                        $rubricDone++;
                                        $myContribution += $rMark->weighted_contribution;
                                    }
                                }
                                $isCompleted = $rubricDone === $assessment->rubrics->count() && $rubricDone > 0;
                                if ($isCompleted) {
                                    $scoreLabel = "Done";
                                    $maxLabel = "";
                                }
                            } else {
                                $isCompleted = $mark && $mark->mark !== null;
                                if ($isCompleted) {
                                    $myContribution = ($mark->mark / $mark->max_mark) * $weight;
                                    $scoreLabel = number_format($mark->mark, 2);
                                    $maxLabel = " / " . number_format($mark->max_mark, 2);
                                }
                            }
                        @endphp
                        
                        @if($isLogbook)
                            {{-- Logbook Card - Links to Logbook Evaluation --}}
                            <a href="{{ route('academic.ip.logbook.show', ['student' => $student->id]) }}"
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
                                                <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $completedPeriods ?? 0 }}/{{ $totalPeriods ?? 6 }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Total Score</span>
                                                <span class="text-lg font-bold text-[#0084C5]">{{ $totalScore ?? 0 }}/{{ $maxPossibleScore ?? 60 }}</span>
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
                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-700' : 'border-gray-100 dark:border-gray-700' }} shadow-sm hover:shadow-xl hover:border-[#0084C5] transition-all duration-300 overflow-hidden"
                             x-data="{ showModal: false }">
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4 z-10">
                                @if($isCompleted)
                                    <span class="px-2.5 py-1 bg-green-50 dark:bg-green-950/30 text-green-600 dark:text-green-400 text-[10px] font-bold uppercase tracking-wider rounded-full border border-green-200 dark:border-green-800 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Complete
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-50 dark:bg-gray-900/30 text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-wider rounded-full border border-gray-200 dark:border-gray-800">
                                        Pending
                                    </span>
                                @endif
                            </div>

                            <div class="p-6">
                                <!-- Assessment Title -->
                                <div class="mb-5">
                                    <h4 class="font-bold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight group-hover:text-[#0084C5] transition-colors text-lg">
                                        {{ $assessment->assessment_name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded border border-blue-100 dark:border-blue-800 uppercase tracking-tighter">
                                            {{ number_format($weight, 2) }}% Weight
                                        </span>
                                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                            {{ $assessment->assessment_type }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Student Submission Section -->
                                @if($assessment->allow_submission)
                                    <div class="mb-5">
                                        <x-submission-viewer :assessment="$assessment" :student="$student" :compact="true" />
                                    </div>
                                @endif

                                <!-- Score & Contribution -->
                                <div class="flex items-center justify-between mb-6 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Score</span>
                                        <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $scoreLabel }}</span>
                                        <span class="text-xs font-bold text-gray-400">{{ $maxLabel }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Contribution</span>
                                        <span class="text-lg font-bold text-[#0084C5]">{{ number_format($myContribution, 2) }}%</span>
                                    </div>
                                </div>

                                <!-- Evaluate Button -->
                                @if($canEdit)
                                    <button @click="showModal = true"
                                            class="w-full py-3 px-4 bg-gradient-to-r from-[#0084C5] to-[#003A6C] hover:from-[#003A6C] hover:to-[#002850] text-white font-bold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg active:scale-95 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        {{ $isCompleted ? 'Edit Evaluation' : 'Start Evaluation' }}
                                    </button>
                                @else
                                     <button @click="showModal = true"
                                            class="w-full py-3 px-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Score
                                    </button>
                                @endif
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
                                        <form action="{{ route('academic.ip.ic.store', $student) }}" method="POST" id="form-{{ $assessment->id }}">
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
                                                                            @if($canEdit)
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
                                                                            @endif
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
                                                                                   {{ !$canEdit ? 'disabled' : '' }}
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
                                                            Overall Remarks (Optional)
                                                        </span>
                                                    </label>
                                                    <textarea name="remarks"
                                                              rows="3"
                                                              {{ !$canEdit ? 'readonly disabled' : '' }}
                                                              class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] transition-all resize-none"
                                                              placeholder="Add any overall comments or feedback...">{{ $mark?->remarks ?? '' }}</textarea>
                                                </div>
                                            @elseif($assessment->rubrics->isNotEmpty())
                                                <!-- Legacy Rubrics (keep existing structure for backward compatibility) -->
                                                <div class="p-6 space-y-6">
                                                    @foreach($assessment->rubrics as $rubric)
                                                         @php
                                                            $rMark = $rubricMarks->get($rubric->id);
                                                            $rScore = $rMark?->rubric_score;
                                                        @endphp
                                                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
                                                            <div class="mb-3">
                                                                 <h5 class="font-bold text-gray-900 dark:text-white">{{ $rubric->question_code }} {{ $rubric->question_title }}</h5>
                                                                 <p class="text-xs text-gray-500">Range: {{ $rubric->rubric_min }} - {{ $rubric->rubric_max }}</p>
                                                            </div>
                                                            <div class="flex flex-wrap gap-2">
                                                                @for($i=$rubric->rubric_min; $i<=$rubric->rubric_max; $i++)
                                                                     <label class="cursor-pointer">
                                                                        <input type="radio" name="rubric_scores[{{ $rubric->id }}]" value="{{ $i }}" {{ $rScore == $i ? 'checked' : '' }} {{ !$canEdit ? 'disabled' : '' }} class="peer sr-only">
                                                                        <div class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 peer-checked:bg-[#0084C5] peer-checked:text-white peer-checked:border-[#0084C5] transition-all">
                                                                            {{ $i }}
                                                                        </div>
                                                                    </label>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    <!-- Overall Remarks -->
                                                    <div class="mt-6">
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Overall Remarks</label>
                                                        <textarea name="remarks" rows="3" {{ !$canEdit ? 'readonly disabled' : '' }} class="w-full border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white p-3">{{ $mark?->remarks }}</textarea>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Simple Mark Input (keep existing structure) -->
                                                <div class="p-6">
                                                    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl">
                                                        <div class="text-center mb-8">
                                                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Maximum Score</div>
                                                            <div class="text-4xl font-black text-[#003A6C] dark:text-[#0084C5]">100%</div>
                                                        </div>
                                                        <div class="relative group/input">
                                                            <input type="number" 
                                                                   name="mark" 
                                                                   step="0.01" 
                                                                   min="0" 
                                                                   max="100"
                                                                   value="{{ $mark->mark ?? '' }}"
                                                                   {{ !$canEdit ? 'readonly disabled' : '' }}
                                                                   class="w-full px-6 py-5 text-4xl font-black border-4 border-gray-100 dark:border-gray-700 rounded-2xl focus:ring-8 focus:ring-[#0084C5]/10 focus:border-[#0084C5] dark:bg-gray-900 dark:text-white transition-all text-center tracking-tight"
                                                                   placeholder="0">
                                                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-3xl font-black text-gray-200">%</span>
                                                        </div>
                                                        <input type="hidden" name="max_mark" value="100">
                                                    </div>
                                                    
                                                    <!-- Overall Remarks -->
                                                    <div class="mt-6">
                                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Overall Remarks</label>
                                                        <textarea name="remarks" rows="3" {{ !$canEdit ? 'readonly disabled' : '' }} class="w-full border-gray-300 dark:border-gray-600 rounded-lg focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white p-3">{{ $mark?->remarks }}</textarea>
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
                                        @if($canEdit)
                                        <button type="submit" 
                                                form="form-{{ $assessment->id }}"
                                                id="saveBtn-{{ $assessment->id }}"
                                                class="px-6 py-2.5 bg-[#0084C5] hover:bg-[#0073a3] text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed">
                                            Save Evaluation
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- END MODAL -->
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
             <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-md p-20 text-center border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2">No Assessments Found</h3>
                <p class="text-gray-500 dark:text-gray-400 font-medium">There are no Industry Coach assessments currently assigned to this student.</p>
            </div>
        @endforelse
        
         <!-- Read-Only AT Marks Section -->
        @if($atAssessments->isNotEmpty())
            <div class="mt-16 border-t-2 border-dashed border-gray-100 dark:border-gray-700 pt-12" x-data="{ atExpanded: false }">
                <button @click="atExpanded = !atExpanded" 
                        class="w-full bg-white dark:bg-gray-800 rounded-3xl border-2 border-gray-100 dark:border-gray-700 p-8 flex flex-col md:flex-row items-center justify-between hover:shadow-2xl transition-all duration-500 group relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-[#0084C5]"></div>
                    
                    <div class="flex items-center gap-6 mb-6 md:mb-0">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700 text-3xl flex items-center justify-center rounded-2xl group-hover:scale-110 group-hover:bg-blue-50 transition-all duration-300 shadow-sm border border-gray-100 dark:border-gray-600">🎓</div>
                        <div class="text-left">
                            <h3 class="text-2xl font-black text-[#003A6C] dark:text-[#0084C5] tracking-tight">Academic Tutor Evaluation</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">External Review Results (Read-Only)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-10">
                        <div class="text-right">
                            <span class="text-10px font-extrabold text-gray-400 uppercase tracking-widest block mb-1">Total Impact</span>
                            <span class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($atTotalContribution, 2) }}</span>
                            <span class="text-lg font-black text-[#0084C5]">%</span>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center border-2 border-gray-100 dark:border-gray-600 transition-all duration-300" :class="{ 'bg-[#0084C5] border-[#0084C5] rotate-180': atExpanded }">
                            <svg class="w-7 h-7 text-gray-300 transition-all" :class="{ 'text-white': atExpanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </button>
                
                <div x-show="atExpanded" 
                     x-collapse
                     class="mt-6">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border-2 border-gray-100 dark:border-gray-700 overflow-hidden shadow-2xl">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.25em] text-gray-400 border-b border-gray-50 dark:border-gray-700">Assessment Name</th>
                                        <th class="py-6 px-4 text-[10px] font-black uppercase tracking-[0.25em] text-gray-400 border-b border-gray-50 dark:border-gray-700 text-center">Outcome</th>
                                        <th class="py-6 px-8 text-[10px] font-black uppercase tracking-[0.25em] text-gray-400 border-b border-gray-50 dark:border-gray-700 text-right">Contribution</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    @foreach($atAssessments as $atAssessment)
                                        @php
                                            $atMark = $atMarks->get($atAssessment->id);
                                            $evaluator = $atAssessment->evaluators->firstWhere('evaluator_role', 'at');
                                            $atWeight = $evaluator ? $evaluator->total_score : $atAssessment->weight_percentage;
                                            
                                            $resContribution = $atMark && $atMark->mark !== null && $atMark->max_mark > 0
                                                ? ($atMark->mark / $atMark->max_mark) * $atWeight
                                                : 0;
                                        @endphp
                                        <tr class="hover:bg-blue-50/20 dark:hover:bg-gray-900/30 transition-colors duration-200">
                                            <td class="py-6 px-8">
                                                <div class="font-extrabold text-[#003A6C] dark:text-white text-lg leading-tight">{{ $atAssessment->assessment_name }}</div>
                                                <div class="flex items-center gap-3 mt-2">
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-[#0084C5] bg-[#0084C5]/5 border border-[#0084C5]/20 px-2 py-0.5 rounded">CLO: {{ $atAssessment->clo_code }}</span>
                                                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-gray-400 px-2 py-0.5 rounded border border-gray-100 dark:border-gray-700">Weight: {{ number_format($atWeight, 2) }}%</span>
                                                </div>
                                            </td>
                                            <td class="py-6 px-4 text-center">
                                                @if($atMark && $atMark->mark !== null)
                                                    <span class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($atMark->mark, 2) }}</span>
                                                    <span class="text-xs font-bold text-gray-400">/ {{ number_format($atMark->max_mark, 2) }}</span>
                                                @else
                                                    <span class="text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase italic tracking-widest">Pending Evaluation</span>
                                                @endif
                                            </td>
                                            <td class="py-6 px-8 text-right">
                                                <div class="text-2xl font-black text-[#0084C5] tracking-tight">{{ number_format($resContribution, 2) }}%</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
