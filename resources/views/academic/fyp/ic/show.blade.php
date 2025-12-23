@extends('layouts.app')

@section('title', 'FYP IC Evaluation - ' . $student->name)

@section('content')
@php
    // Define rubric templates for different assessment types
    $rubricTemplates = [
        'Mid-Term Report' => [
            'title' => 'FYP Mid-Term (Pt. 1) Written Report Evaluation Form',
            'instruction' => 'Please evaluate/rate the student\'s answer based on these criteria.',
            'rating_labels' => ['Aware', 'Limited', 'Fair', 'Good', 'Excellent'],
            'elements' => [
                ['name' => 'Problem Statement', 'keywords' => 'Clarity and Conciseness, Relevance, Specificity, Value-added Quality, and Originality'],
                ['name' => 'Project Objectives', 'keywords' => 'Clarity and Specificity, Alignment with Problem Statement, Measurability'],
                ['name' => 'Project Scope', 'keywords' => 'Clarity and Specificity, Breadth and Depth, Feasibility and Inclusivity'],
                ['name' => 'Project Flowchart', 'keywords' => 'Clarity and Specificity, Relevance to Project Goals, Measurability, and Timeliness'],
                ['name' => 'Project Gantt-Chart', 'keywords' => 'Clarity and Detail, Realism and Feasibility, Dependencies and Constraints, and Flexibility'],
                ['name' => 'Literature Survey', 'keywords' => 'Relevance of Sources, Depth of Coverage, Methodological Critique and Synthesis, Organization, and Integration with Objectives'],
                ['name' => 'Proposed Methodology', 'keywords' => 'Analysis Answering Objectives, Methods Validity, Feasibility of Data Collection and Analysis'],
            ],
        ],
        'Mid-Term Oral Presentation' => [
            'title' => 'FYP Mid-Term (Pt. 2) Oral Presentation Evaluation Form',
            'instruction' => 'Please evaluate/rate the student\'s presentation based on these criteria.',
            'rating_labels' => ['Aware', 'Limited', 'Fair', 'Good', 'Excellent'],
            'elements' => [
                ['name' => 'Presentation Structure', 'keywords' => 'Organization, Flow, and Logical Sequence'],
                ['name' => 'Content Knowledge', 'keywords' => 'Understanding of Topic, Depth of Explanation'],
                ['name' => 'Communication Skills', 'keywords' => 'Clarity, Confidence, Eye Contact, Voice Projection'],
                ['name' => 'Visual Aids', 'keywords' => 'Slide Design, Relevance, Readability'],
                ['name' => 'Time Management', 'keywords' => 'Adherence to Time Limit, Pacing'],
                ['name' => 'Q&A Response', 'keywords' => 'Ability to Answer Questions, Critical Thinking'],
            ],
        ],
        'Final Report' => [
            'title' => 'FYP Final Written Report Evaluation Form',
            'instruction' => 'Please evaluate/rate the student\'s final report based on these criteria.',
            'rating_labels' => ['Aware', 'Limited', 'Fair', 'Good', 'Excellent'],
            'elements' => [
                ['name' => 'Problem Statement & Objectives', 'keywords' => 'Clarity, Relevance, Specificity'],
                ['name' => 'Literature Review', 'keywords' => 'Depth, Coverage, Critical Analysis'],
                ['name' => 'Methodology', 'keywords' => 'Appropriateness, Validity, Reproducibility'],
                ['name' => 'Results & Analysis', 'keywords' => 'Accuracy, Interpretation, Presentation'],
                ['name' => 'Discussion', 'keywords' => 'Critical Analysis, Comparison with Literature'],
                ['name' => 'Conclusion & Recommendations', 'keywords' => 'Summary, Future Work, Contributions'],
                ['name' => 'Technical Writing', 'keywords' => 'Grammar, Formatting, Citations'],
            ],
        ],
        'Final Oral Presentation' => [
            'title' => 'FYP Final Oral Presentation Evaluation Form',
            'instruction' => 'Please evaluate/rate the student\'s final presentation based on these criteria.',
            'rating_labels' => ['Aware', 'Limited', 'Fair', 'Good', 'Excellent'],
            'elements' => [
                ['name' => 'Presentation Structure', 'keywords' => 'Organization, Flow, and Logical Sequence'],
                ['name' => 'Content Knowledge', 'keywords' => 'Mastery of Topic, Technical Depth'],
                ['name' => 'Communication Skills', 'keywords' => 'Clarity, Confidence, Professionalism'],
                ['name' => 'Visual Aids & Demo', 'keywords' => 'Slide Quality, System Demonstration'],
                ['name' => 'Time Management', 'keywords' => 'Adherence to Time Limit, Pacing'],
                ['name' => 'Q&A Response', 'keywords' => 'Technical Defense, Critical Thinking'],
            ],
        ],
    ];

    // Default template for unknown assessment types
    $defaultTemplate = [
        'title' => 'Evaluation Form',
        'instruction' => 'Please evaluate/rate the student\'s work based on these criteria.',
        'rating_labels' => ['Poor', 'Limited', 'Fair', 'Good', 'Excellent'],
        'elements' => [],
    ];
@endphp

<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('academic.fyp.ic.index', request()->query()) }}" 
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
                        <span class="text-gray-500 dark:text-gray-400 block text-xs">Company</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $student->company->company_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Summary -->
        @php
            $totalAssessments = $allIcAssessments->count();
            $completedAssessments = 0;
            
            foreach ($markAssessments as $assessment) {
                $mark = $marks->get($assessment->id);
                if ($mark && $mark->mark !== null) {
                    $completedAssessments++;
                }
            }
            
            foreach ($rubricAssessments as $assessment) {
                $hasAllRubrics = true;
                foreach ($assessment->rubrics as $rubric) {
                    if (!$rubricMarks->has($rubric->id)) {
                        $hasAllRubrics = false;
                        break;
                    }
                }
                if ($hasAllRubrics && $assessment->rubrics->count() > 0) {
                    $completedAssessments++;
                }
            }
            
            $progressPercent = $totalAssessments > 0 ? ($completedAssessments / $totalAssessments) * 100 : 0;
            $canEdit = Gate::allows('edit-ic-marks', $student);
        @endphp
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">IC Evaluation Progress</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $completedAssessments }} of {{ $totalAssessments }} assessments completed</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-3xl font-bold text-[#0084C5]">{{ number_format($totalContribution, 2) }}%</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">out of {{ number_format($totalIcWeight, 2) }}%</div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-gradient-to-r from-[#0084C5] to-[#00A86B] h-3 rounded-full transition-all duration-500" 
                         style="width: {{ min($progressPercent, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Assessment Cards Grid -->
        @php
            $phaseLabels = [
                'Mid-Term' => ['label' => 'Mid-Term Evaluation', 'icon' => '📝', 'color' => 'blue'],
                'Final' => ['label' => 'Final Evaluation', 'icon' => '🎓', 'color' => 'green'],
                'Progress' => ['label' => 'Progress Tracking', 'icon' => '📊', 'color' => 'purple'],
            ];
        @endphp

        @if($groupedAssessments->count() > 0 || $rubricAssessments->count() > 0)
            @foreach($groupedAssessments as $phase => $assessmentGroups)
                @php
                    $phaseInfo = $phaseLabels[$phase] ?? ['label' => $phase, 'icon' => '📋', 'color' => 'gray'];
                    $phaseTotalWeight = $assessmentsByPhase[$phase]->sum('weight_percentage');
                @endphp
                
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-2xl">{{ $phaseInfo['icon'] }}</span>
                        <div>
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">{{ $phaseInfo['label'] }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($phaseTotalWeight, 2) }}% Total Weight</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($assessmentGroups as $baseName => $assessmentsInGroup)
                            @php
                                // Calculate totals for the group
                                $groupTotalWeight = $assessmentsInGroup->sum('weight_percentage');
                                $groupContribution = 0;
                                $completedInGroup = 0;
                                $isLogbook = str_contains(strtolower($baseName), 'logbook');
                                
                                foreach ($assessmentsInGroup as $assessment) {
                                    $mark = $marks->get($assessment->id);
                                    if ($mark && $mark->mark !== null) {
                                        $completedInGroup++;
                                        if ($mark->max_mark > 0) {
                                            $groupContribution += ($mark->mark / $mark->max_mark) * $assessment->weight_percentage;
                                        }
                                    }
                                }
                                
                                $isCompleted = $completedInGroup === $assessmentsInGroup->count();
                                $isPartial = $completedInGroup > 0 && $completedInGroup < $assessmentsInGroup->count();
                                $firstAssessmentId = $assessmentsInGroup->first()->id;
                                
                                // Get the rubric template for this assessment type
                                $template = $rubricTemplates[$baseName] ?? $defaultTemplate;
                            @endphp
                            
                            @if($isLogbook)
                                {{-- Logbook Card - Links to Logbook Evaluation --}}
                                <a href="{{ route('academic.fyp.logbook.show', ['student' => $student->id]) }}"
                                   class="group block bg-white dark:bg-gray-800 rounded-xl border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm hover:shadow-lg hover:border-[#0084C5] transition-all duration-300 overflow-hidden">
                                    
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

                                    <div class="p-5 relative">
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight group-hover:text-[#0084C5]">
                                                {{ $baseName }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded">
                                                    Logbook
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Progress</span>
                                                <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $completedInGroup }}/{{ $assessmentsInGroup->count() }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Contribution</span>
                                                <span class="text-lg font-bold text-[#0084C5]">{{ number_format($groupContribution, 2) }}%</span>
                                                <span class="text-xs text-gray-400 block">of {{ number_format($groupTotalWeight, 2) }}%</span>
                                            </div>
                                        </div>

                                        <div class="w-full py-2.5 px-4 bg-[#0084C5] group-hover:bg-[#003A6C] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            {{ $isCompleted ? 'View Logbook' : 'Evaluate Logbook' }}
                                        </div>
                                    </div>
                                </a>
                            @elseif($assessmentsInGroup->count() > 1)
                                {{-- Grouped Assessment Card (Multiple CLOs) - Opens Modal with Rubric Form --}}
                                <div class="group relative bg-white dark:bg-gray-800 rounded-xl border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-700' : ($isPartial ? 'border-amber-300 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700') }} shadow-sm hover:shadow-lg hover:border-[#0084C5] transition-all duration-300 overflow-hidden"
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
                                        @elseif($isPartial)
                                            <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-full">
                                                {{ $completedInGroup }}/{{ $assessmentsInGroup->count() }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-full">
                                                Pending
                                            </span>
                                        @endif
                                    </div>

                                    <div class="p-5">
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight">
                                                {{ $baseName }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">
                                                    {{ $assessmentsInGroup->count() }} Components
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $assessmentsInGroup->first()->assessment_type }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Progress</span>
                                                <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $completedInGroup }}/{{ $assessmentsInGroup->count() }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Contribution</span>
                                                <span class="text-lg font-bold text-[#0084C5]">{{ number_format($groupContribution, 2) }}%</span>
                                                <span class="text-xs text-gray-400 block">of {{ number_format($groupTotalWeight, 2) }}%</span>
                                            </div>
                                        </div>

                                        @if($canEdit)
                                        <button @click="showModal = true" 
                                                class="w-full py-2.5 px-4 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            {{ $isCompleted ? 'Edit Scores' : 'Enter Scores' }}
                                        </button>
                                        @else
                                        <div class="w-full py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium rounded-lg text-center text-sm">
                                            View Only
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Modal with Rubric-Style Form -->
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
                                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col"
                                             @click.stop>
                                            <!-- Modal Header -->
                                            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-[#003A6C] to-[#0084C5]">
                                                <div class="text-white">
                                                    <h3 class="text-lg font-semibold">
                                                        {{ $template['title'] }}
                                                    </h3>
                                                    <p class="text-sm text-white/80 mt-1">
                                                        {{ $template['instruction'] }}
                                                        <span class="text-red-300 ml-1">*</span>
                                                    </p>
                                                </div>
                                                <button @click="showModal = false" class="p-2 hover:bg-white/20 rounded-full text-white">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="flex-1 overflow-y-auto">
                                                <form action="{{ route('academic.fyp.ic.store', $student) }}" method="POST" id="groupForm-{{ $firstAssessmentId }}">
                                                    @csrf
                                                    
                                                    <!-- Rubric Table -->
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full">
                                                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                                                <tr>
                                                                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300 w-2/5 border-b border-gray-200 dark:border-gray-600"></th>
                                                                    @foreach($template['rating_labels'] as $label)
                                                                    <th class="text-center py-4 px-2 text-xs font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-600">
                                                                        {{ $label }}
                                                                    </th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                                                @php
                                                                    // Collect components from ALL assessments in the group
                                                                    $allComponents = collect();
                                                                    foreach ($assessmentsInGroup as $assessment) {
                                                                        if ($assessment->components && $assessment->components->count() > 0) {
                                                                            $allComponents = $allComponents->merge($assessment->components);
                                                                        }
                                                                    }
                                                                    
                                                                    $hasComponents = $allComponents->count() > 0;
                                                                    $usedAssessmentsForComponents = [];
                                                                    
                                                                    if ($hasComponents) {
                                                                        // Use components from database (advanced features)
                                                                        // Sort by order, then by component name for consistency
                                                                        $components = $allComponents->sortBy(function($comp) {
                                                                            return [$comp->order, $comp->component_name];
                                                                        })->values();
                                                                        $sortedAssessments = $assessmentsInGroup->sortBy('clo_code')->keyBy('clo_code');
                                                                    } else {
                                                                        // Fall back to template system
                                                                        $components = collect($template['elements'])->map(function($el, $idx) {
                                                                            return (object)[
                                                                                'component_name' => $el['name'],
                                                                                'criteria_keywords' => $el['keywords'] ?? '',
                                                                                'order' => $idx
                                                                            ];
                                                                        });
                                                                        $sortedAssessments = $assessmentsInGroup->sortBy('clo_code')->values();
                                                                        $usedAssessmentIds = [];
                                                                    }
                                                                @endphp
                                                                @foreach($components as $index => $component)
                                                                    @php
                                                                        if ($hasComponents) {
                                                                            // Find assessment by CLO code from component
                                                                            $assessment = $sortedAssessments->get($component->clo_code);
                                                                            
                                                                            // If no exact CLO match, try to find any unused assessment
                                                                            if (!$assessment) {
                                                                                // Try to find an assessment that hasn't been used yet
                                                                                foreach ($sortedAssessments as $assess) {
                                                                                    if (!in_array($assess->id, $usedAssessmentsForComponents)) {
                                                                                        $assessment = $assess;
                                                                                        $usedAssessmentsForComponents[] = $assess->id;
                                                                                        break;
                                                                                    }
                                                                                }
                                                                            } else {
                                                                                // Track used assessments
                                                                                $usedAssessmentsForComponents[] = $assessment->id;
                                                                            }
                                                                            
                                                                            if ($assessment) {
                                                                                $mark = $marks->get($assessment->id);
                                                                                $currentMark = $mark?->mark ?? null;
                                                                            } else {
                                                                                $currentMark = null;
                                                                            }
                                                                            
                                                                            $componentName = $component->component_name;
                                                                            $componentKeywords = $component->criteria_keywords;
                                                                            $componentClo = $component->clo_code;
                                                                            $componentWeight = $component->weight_percentage;
                                                                        } else {
                                                                            // Template fallback - find assessment by matching
                                                                            $elementNameLower = strtolower($component->component_name);
                                                                            
                                                                            $assessment = null;
                                                                            foreach ($sortedAssessments as $assess) {
                                                                                if (in_array($assess->id, $usedAssessmentIds ?? [])) {
                                                                                    continue;
                                                                                }
                                                                                
                                                                                $desc = strtolower($assess->description ?? '');
                                                                                if ($desc === $elementNameLower || 
                                                                                    str_contains($desc, $elementNameLower) || 
                                                                                    str_contains($elementNameLower, $desc)) {
                                                                                    $assessment = $assess;
                                                                                    $usedAssessmentIds[] = $assess->id;
                                                                                    break;
                                                                                }
                                                                            }
                                                                            
                                                                            if (!$assessment && isset($sortedAssessments[$index])) {
                                                                                $assessment = $sortedAssessments[$index];
                                                                                $usedAssessmentIds[] = $assessment->id;
                                                                            }
                                                                            
                                                                            if ($assessment) {
                                                                                $mark = $marks->get($assessment->id);
                                                                                $currentMark = $mark?->mark ?? null;
                                                                                $componentClo = $assessment->clo_code;
                                                                                $componentWeight = $assessment->weight_percentage;
                                                                            } else {
                                                                                $currentMark = null;
                                                                                $componentClo = null;
                                                                                $componentWeight = null;
                                                                            }
                                                                            
                                                                            $componentName = $component->component_name;
                                                                            $componentKeywords = $component->criteria_keywords;
                                                                        }
                                                                    @endphp
                                                                    <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors {{ !$assessment ? 'opacity-60' : '' }} component-row" 
                                                                        data-component-id="{{ $hasComponents ? $component->id : '' }}"
                                                                        data-component-order="{{ $index }}">
                                                                        <td class="py-5 px-4">
                                                                            <div class="flex items-start gap-2">
                                                                                @if($hasComponents && auth()->user()->isAdmin())
                                                                                <div class="flex flex-col gap-1 mt-0.5">
                                                                                    <button type="button" 
                                                                                            onclick="moveComponentRowUp(this, '{{ $firstAssessmentId }}')"
                                                                                            class="text-[#0084C5] hover:text-[#003A6C] disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                                                                                            {{ $index === 0 ? 'disabled' : '' }}
                                                                                            title="Move Up">
                                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                                                        </svg>
                                                                                    </button>
                                                                                    <button type="button" 
                                                                                            onclick="moveComponentRowDown(this, '{{ $firstAssessmentId }}')"
                                                                                            class="text-[#0084C5] hover:text-[#003A6C] disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                                                                                            {{ $index === $components->count() - 1 ? 'disabled' : '' }}
                                                                                            title="Move Down">
                                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>
                                                                                @endif
                                                                                <div class="flex-1">
                                                                                    <div class="font-medium text-gray-900 dark:text-white text-sm">
                                                                                        {{ $componentName }}
                                                                                    </div>
                                                                                    @if($componentKeywords)
                                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                                                                        {{ $componentKeywords }}
                                                                                    </div>
                                                                                    @endif
                                                                                    @if(!$assessment)
                                                                                    <div class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                                                                        ⚠ Assessment not configured
                                                                                    </div>
                                                                                    @else
                                                                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                                                        {{ $componentClo }} • {{ number_format($componentWeight, 2) }}%
                                                                                    </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        @if($assessment)
                                                                            @for($score = 1; $score <= 5; $score++)
                                                                            <td class="py-4 px-3 text-center align-middle">
                                                                                <label class="inline-flex items-center justify-center cursor-pointer w-full py-3 rounded-md transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                                                                    <input type="radio" 
                                                                                           name="marks[{{ $assessment->id }}]" 
                                                                                           value="{{ $score }}"
                                                                                           {{ $currentMark == $score ? 'checked' : '' }}
                                                                                           class="w-5 h-5 text-[#0084C5] border-2 focus:ring-2 focus:ring-[#0084C5] focus:ring-offset-1 cursor-pointer transition-all
                                                                                           @if($currentMark == $score)
                                                                                               border-[#0084C5] dark:border-[#00A86B] bg-[#0084C5] dark:bg-[#00A86B]
                                                                                           @else
                                                                                               border-gray-300 dark:border-gray-600
                                                                                           @endif">
                                                                                    <input type="hidden" name="max_marks[{{ $assessment->id }}]" value="5">
                                                                                </label>
                                                                            </td>
                                                                            @endfor
                                                                        @else
                                                                            @for($score = 1; $score <= 5; $score++)
                                                                            <td class="py-5 px-2 text-center">
                                                                                <div class="w-5 h-5 border-2 border-gray-200 dark:border-gray-600 rounded-full mx-auto opacity-50"></div>
                                                                            </td>
                                                                            @endfor
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Modal Footer -->
                                            <div class="flex gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                                <button type="button" 
                                                        @click="showModal = false"
                                                        class="flex-1 py-3 px-4 bg-white hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors border border-gray-300 dark:border-gray-600">
                                                    Cancel
                                                </button>
                                                <button type="submit" 
                                                        form="groupForm-{{ $firstAssessmentId }}"
                                                        class="flex-1 py-3 px-4 bg-[#00A86B] hover:bg-[#008f5b] text-white font-medium rounded-lg transition-colors shadow-md">
                                                    Save All Scores
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Single Assessment Card --}}
                                @php
                                    $assessment = $assessmentsInGroup->first();
                                    $mark = $marks->get($assessment->id);
                                    $currentMark = $mark?->mark ?? null;
                                    $maxMark = $mark?->max_mark ?? 100;
                                    $contribution = $mark && $mark->mark !== null && $maxMark > 0
                                        ? ($mark->mark / $maxMark) * $assessment->weight_percentage
                                        : 0;
                                    $isSingleCompleted = $currentMark !== null;
                                @endphp
                                
                                <div class="group relative bg-white dark:bg-gray-800 rounded-xl border-2 {{ $isSingleCompleted ? 'border-green-300 dark:border-green-700' : 'border-gray-200 dark:border-gray-700' }} shadow-sm hover:shadow-lg hover:border-[#0084C5] transition-all duration-300 overflow-hidden"
                                     x-data="{ showModal: false }">
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        @if($isSingleCompleted)
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
                                            <h4 class="font-semibold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight">
                                                {{ $baseName }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">
                                                    {{ $assessment->clo_code }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $assessment->assessment_type }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Score</span>
                                                @if($isSingleCompleted)
                                                    <span class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($currentMark, 1) }}</span>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">/ {{ $maxMark }}</span>
                                                @else
                                                    <span class="text-xl font-bold text-gray-400">—</span>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Contribution</span>
                                                <span class="text-lg font-bold text-[#0084C5]">{{ number_format($contribution, 2) }}%</span>
                                                <span class="text-xs text-gray-400 block">of {{ number_format($assessment->weight_percentage, 2) }}%</span>
                                            </div>
                                        </div>

                                        @if($canEdit)
                                        <button @click="showModal = true" 
                                                class="w-full py-2.5 px-4 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            {{ $isSingleCompleted ? 'Edit Score' : 'Enter Score' }}
                                        </button>
                                        @else
                                        <div class="w-full py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium rounded-lg text-center text-sm">
                                            View Only
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Modal for Single Assessment - Rubric Style (matching AT Evaluation) -->
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
                                            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-[#003A6C] to-[#0084C5] flex-shrink-0">
                                                <div class="text-white">
                                                    <h3 class="text-xl font-bold mb-1">
                                                        {{ $baseName }} – Rubric Evaluation
                                                    </h3>
                                                    <p class="text-sm text-white/80">
                                                        Select ONE performance level per criterion.
                                                        <span class="text-red-300 ml-1">*</span>
                                                    </p>
                                                </div>
                                                <button @click="showModal = false" class="p-2 hover:bg-white/20 rounded-full text-white transition-colors">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Modal Body -->
                                            <div class="flex-1 overflow-y-auto" style="max-height: calc(80vh - 180px);">
                                                <form action="{{ route('academic.fyp.ic.store', $student) }}" method="POST" id="singleForm-{{ $assessment->id }}">
                                                    @csrf
                                                    
                                                    @php
                                                        // Get components from the assessment
                                                        $components = $assessment->components->sortBy('order');
                                                        $hasComponents = $components->count() > 0;
                                                        
                                                        // If no components, use a default template based on assessment name
                                                        if (!$hasComponents) {
                                                            $template = $rubricTemplates[$baseName] ?? $defaultTemplate;
                                                            $components = collect($template['elements'])->map(function($el, $idx) {
                                                                return (object)[
                                                                    'id' => null,
                                                                    'component_name' => $el['name'],
                                                                    'criteria_keywords' => $el['keywords'] ?? '',
                                                                    'order' => $idx,
                                                                    'clo_code' => null,
                                                                    'weight_percentage' => null
                                                                ];
                                                            });
                                                        }
                                                        
                                                        // Get existing component marks
                                                        $componentMarks = \App\Models\StudentAssessmentComponentMark::where('student_id', $student->id)
                                                            ->where('assessment_id', $assessment->id)
                                                            ->get()
                                                            ->keyBy('component_id');
                                                        
                                                        $ratingLabels = ['Aware', 'Limited', 'Fair', 'Good', 'Excellent'];
                                                    @endphp
                                                    
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
                                                                    <tr class="rubric-row border-b border-gray-100 dark:border-gray-700 hover:bg-blue-50/50 dark:hover:bg-gray-700/30 transition-colors" 
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
                                                    
                                                    <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
                                                    
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
                                                        <textarea name="remarks[{{ $assessment->id }}]"
                                                                  rows="3"
                                                                  class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] transition-all resize-none"
                                                                  placeholder="Add any overall comments or feedback...">{{ $mark?->remarks ?? '' }}</textarea>
                                                    </div>
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
                                                        form="singleForm-{{ $assessment->id }}"
                                                        class="px-6 py-2.5 bg-[#0084C5] hover:bg-[#0073a3] text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow">
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
            @endforeach

            <!-- Rubric-based Assessments (Oral Presentations) -->
            @if($rubricAssessments->count() > 0)
                @php
                    // Group rubric assessments by base name too
                    $groupedRubricAssessments = $rubricAssessments->groupBy(function($a) {
                        return preg_replace('/\s*\(CLO\d+\)\s*$/', '', $a->assessment_name);
                    });
                @endphp
                
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-2xl">🎤</span>
                        <div>
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Oral/Rubric Assessments</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($rubricAssessments->sum('weight_percentage'), 2) }}% Total Weight</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($groupedRubricAssessments as $baseName => $assessmentsInGroup)
                            @php
                                $totalRubrics = 0;
                                $completedRubrics = 0;
                                $assessmentContribution = 0;
                                $groupWeight = $assessmentsInGroup->sum('weight_percentage');
                                
                                foreach ($assessmentsInGroup as $assessment) {
                                    $totalRubrics += $assessment->rubrics->count();
                                    foreach ($assessment->rubrics as $rubric) {
                                        $rubricMark = $rubricMarks->get($rubric->id);
                                        if ($rubricMark) {
                                            $completedRubrics++;
                                            $assessmentContribution += $rubricMark->weighted_contribution;
                                        }
                                    }
                                }
                                
                                $isCompleted = $completedRubrics === $totalRubrics && $totalRubrics > 0;
                                $isPartial = $completedRubrics > 0 && $completedRubrics < $totalRubrics;
                                $firstAssessment = $assessmentsInGroup->first();
                            @endphp

                            <a href="{{ route('academic.fyp.ic.rubric', [$student, $firstAssessment]) }}"
                               class="group block bg-white dark:bg-gray-800 rounded-xl border-2 {{ $isCompleted ? 'border-green-300 dark:border-green-700' : ($isPartial ? 'border-amber-300 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700') }} shadow-sm hover:shadow-lg hover:border-[#0084C5] transition-all duration-300 overflow-hidden">
                                
                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3 z-10">
                                    @if($isCompleted)
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs font-medium rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Done
                                        </span>
                                    @elseif($isPartial)
                                        <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-full">
                                            {{ $completedRubrics }}/{{ $totalRubrics }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-full">
                                            Pending
                                        </span>
                                    @endif
                                </div>

                                <div class="p-5 relative">
                                    <div class="mb-4">
                                        <h4 class="font-semibold text-[#003A6C] dark:text-[#0084C5] pr-16 leading-tight group-hover:text-[#0084C5]">
                                            {{ $baseName }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded">
                                                {{ $totalRubrics }} Rubrics
                                            </span>
                                            @if($assessmentsInGroup->count() > 1)
                                            <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">
                                                {{ $assessmentsInGroup->count() }} CLOs
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Progress</span>
                                            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $completedRubrics }}/{{ $totalRubrics }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Contribution</span>
                                            <span class="text-lg font-bold text-[#0084C5]">{{ number_format($assessmentContribution, 2) }}%</span>
                                            <span class="text-xs text-gray-400 block">of {{ number_format($groupWeight, 2) }}%</span>
                                        </div>
                                    </div>

                                    <div class="w-full py-2.5 px-4 bg-[#0084C5] group-hover:bg-[#003A6C] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        {{ $isCompleted ? 'Review Rubrics' : 'Evaluate Rubrics' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- AT Evaluation Summary (Read-only) -->
            @if($atAssessments->count() > 0)
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">👨‍🏫</span>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Academic Tutor Evaluation</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Read-only summary</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-[#0084C5]">{{ number_format($atTotalContribution, 2) }}%</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 block">AT Contribution</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($atAssessments as $assessment)
                        @php
                            $atMark = $atMarks->get($assessment->id);
                            $atContribution = $atMark && $atMark->mark !== null && $atMark->max_mark > 0
                                ? ($atMark->mark / $atMark->max_mark) * $assessment->weight_percentage
                                : 0;
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $assessment->assessment_name }}</span>
                                @if($atMark && $atMark->mark !== null)
                                    <span class="text-sm font-semibold text-[#0084C5]">{{ number_format($atContribution, 2) }}%</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Assessments Configured</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    No assessments configured for FYP IC evaluation yet.<br>
                    Please contact an administrator to set up assessments.
                </p>
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
        if (allComplete) {
            saveButton.disabled = false;
            saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            // Allow saving even if not all complete for IC
            saveButton.disabled = false;
        }
    }
}

// Initialize rubric forms on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial state for all checked radios
    document.querySelectorAll('.rubric-radio:checked').forEach(radio => {
        updateRubricSelection(radio);
    });
});
</script>

@if(auth()->user()->isAdmin())
<script>
function moveComponentRowUp(button, assessmentId) {
    const row = button.closest('.component-row');
    const prevRow = row.previousElementSibling;
    if (prevRow && prevRow.classList.contains('component-row')) {
        const tbody = row.parentNode;
        tbody.insertBefore(row, prevRow);
        updateComponentRowOrder(assessmentId);
    }
}

function moveComponentRowDown(button, assessmentId) {
    const row = button.closest('.component-row');
    const nextRow = row.nextElementSibling;
    if (nextRow && nextRow.classList.contains('component-row')) {
        const tbody = row.parentNode;
        tbody.insertBefore(nextRow, row);
        updateComponentRowOrder(assessmentId);
    }
}

function updateComponentRowOrder(assessmentId) {
    const rows = document.querySelectorAll(`#groupForm-${assessmentId} .component-row`);
    const componentIds = [];
    
    rows.forEach((row, index) => {
        const componentId = row.getAttribute('data-component-id');
        if (componentId) {
            componentIds.push({
                id: componentId,
                order: index
            });
            
            // Update button states
            const upButton = row.querySelector('button[onclick*="moveComponentRowUp"]');
            const downButton = row.querySelector('button[onclick*="moveComponentRowDown"]');
            
            if (upButton) {
                upButton.disabled = index === 0;
            }
            if (downButton) {
                downButton.disabled = index === rows.length - 1;
            }
        }
    });
    
    // Save order via AJAX
    if (componentIds.length > 0) {
        fetch('{{ route("academic.fyp.assessments.reorder-components") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                components: componentIds
            })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optional: Show success message
                console.log('Component order updated');
            }
        })
        .catch(error => {
            console.error('Error updating component order:', error);
        });
    }
}
</script>
@endif
@endsection
