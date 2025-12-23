@extends('layouts.app')

@section('title', 'FYP Rubric Evaluation - ' . ($evaluatorRole === 'at' ? 'Academic Tutor' : 'Industry Coach'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                    FYP Rubric Evaluation
                    <span class="text-lg font-normal text-gray-500">
                        ({{ $evaluatorRole === 'at' ? 'Academic Tutor' : 'Industry Coach' }})
                    </span>
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Evaluate students using the {{ $evaluatorRole === 'at' ? 'AT' : 'IC' }} rubric</p>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">View as:</span>
                <a href="{{ route('academic.fyp.rubric-evaluation.index', ['role' => 'at']) }}" 
                   class="px-3 py-1.5 text-sm rounded-lg {{ $evaluatorRole === 'at' ? 'bg-[#0084C5] text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    AT
                </a>
                <a href="{{ route('academic.fyp.rubric-evaluation.index', ['role' => 'ic']) }}" 
                   class="px-3 py-1.5 text-sm rounded-lg {{ $evaluatorRole === 'ic' ? 'bg-[#0084C5] text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    IC
                </a>
            </div>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Assessment & Template Selection & Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assessment</label>
                    <select name="assessment" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                        <option value="">Select Assessment</option>
                        @foreach($assessments as $assessment)
                            <option value="{{ $assessment->id }}" {{ $selectedAssessment?->id == $assessment->id ? 'selected' : '' }}>
                                {{ $assessment->assessment_name }} ({{ $assessment->assessment_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rubric Template</label>
                    <select name="template" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                        <option value="">Select Template</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ $selectedTemplate?->id == $template->id ? 'selected' : '' }}>
                                {{ $template->phase }} {{ $template->assessment_type }} - {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Matric No"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Group</label>
                    <select name="group" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                        <option value="">All Status</option>
                        <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        @if($selectedAssessment)
            <!-- Assessment Info -->
            <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] rounded-xl shadow-md p-4 mb-6 text-white">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-1 bg-white/20 text-white text-xs font-medium rounded">{{ $selectedAssessment->assessment_type }}</span>
                            <span class="px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded">
                                {{ $evaluatorRole === 'at' ? 'AT' : 'IC' }}
                            </span>
                        </div>
                        <h2 class="text-lg font-semibold">{{ $selectedAssessment->assessment_name }}</h2>
                        @if($selectedAssessment->description)
                            <p class="text-sm text-blue-200 mt-1">{{ $selectedAssessment->description }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Components List -->
                @if($selectedAssessment->components->count() > 0)
                    <div class="mt-4 pt-4 border-t border-white/20">
                        <h3 class="text-sm font-semibold mb-3">Components with Rubrics:</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($selectedAssessment->components as $component)
                                <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-white/80">{{ $component->component_name }}</span>
                                        @if($component->clo_code)
                                            <span class="px-2 py-0.5 bg-white/20 text-white text-xs rounded">{{ $component->clo_code }}</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-white/70">
                                        Weight: <strong>{{ number_format($component->weight_percentage, 2) }}%</strong>
                                    </div>
                                    @if($component->criteria_keywords)
                                        <div class="text-xs text-white/60 mt-1">
                                            {{ Str::limit($component->criteria_keywords, 50) }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-4 pt-4 border-t border-white/20">
                        <p class="text-sm text-white/70">No components configured for this assessment.</p>
                    </div>
                @endif
            </div>
        @elseif($selectedTemplate)
            <!-- Template Info (Fallback) -->
            <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] rounded-xl shadow-md p-4 mb-6 text-white">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-1 bg-white/20 text-white text-xs font-medium rounded">{{ $selectedTemplate->phase }}</span>
                            <span class="px-2 py-1 bg-white/20 text-white text-xs font-medium rounded">{{ $selectedTemplate->assessment_type }}</span>
                            <span class="px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded">
                                {{ $evaluatorRole === 'at' ? 'AT' : 'IC' }} - {{ number_format($selectedTemplate->component_marks, 0) }}%
                            </span>
                        </div>
                        <h2 class="text-lg font-semibold">{{ $selectedTemplate->name }}</h2>
                        <p class="text-sm text-blue-200">
                            {{ $selectedTemplate->elements->count() }} elements | 
                            Contributes <strong>{{ number_format($selectedTemplate->component_marks, 0) }}%</strong> to overall FYP grade
                        </p>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('academic.fyp.rubrics.show', $selectedTemplate) }}" 
                       class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors text-sm">
                        View Rubric Structure
                    </a>
                    @endif
                </div>
            </div>

            <!-- Students Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Group</th>
                                @if($selectedTemplate)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Progress</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Score</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                @endif
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-[#003A6C] dark:text-[#0084C5]">{{ $student->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->matric_no }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $student->group->name ?? 'N/A' }}
                                    </td>
                                    @if($selectedTemplate)
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                @php
                                                    $progress = $student->total_elements > 0 
                                                        ? ($student->completed_elements / $student->total_elements) * 100 
                                                        : 0;
                                                @endphp
                                                <div class="bg-[#0084C5] h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $student->completed_elements }}/{{ $student->total_elements }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div>
                                            <span class="font-semibold text-[#0084C5]">{{ number_format($student->total_score, 2) }}%</span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            → {{ number_format($student->contribution_score, 2) }}% grade
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($student->evaluation_status == 'completed')
                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-xs font-medium rounded-full">
                                                Completed
                                            </span>
                                        @elseif($student->evaluation_status == 'in_progress')
                                            <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 text-xs font-medium rounded-full">
                                                In Progress
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-medium rounded-full">
                                                Not Started
                                            </span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="px-4 py-4 text-center">
                                        @if($selectedTemplate)
                                            <a href="{{ route('academic.fyp.rubric-evaluation.show', ['student' => $student, 'template' => $selectedTemplate->id]) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-medium rounded-lg transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Evaluate
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Select assessment/template</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $selectedTemplate ? '6' : '3' }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p class="text-lg font-medium">No students found</p>
                                        <p class="text-sm">Try adjusting your filters</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-2">No Assessments Available</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Please create an assessment with components first.</p>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('academic.fyp.assessments.create') }}" class="text-[#0084C5] hover:text-[#003A6C] font-medium">
                        Create Assessment →
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
