@php
    $routePrefix = 'academic.ip.assessments';
@endphp
@extends('layouts.app')

@section('title', $courseName . ' – Edit Assessment')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route($routePrefix . '.index') }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Assessments
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $courseName }} – Edit Assessment</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Update assessment details for {{ $courseName }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <form action="{{ route('academic.ip.assessments.update', $assessment) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Course Code (Hidden - fixed to current course) -->
                <input type="hidden" name="course_code" value="{{ $courseCode }}">

                <!-- Assessment Name -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Assessment Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="assessment_name" 
                           value="{{ old('assessment_name', $assessment->assessment_name) }}"
                           required
                           placeholder="e.g. Assignment 1, Final Report, Presentation"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    @error('assessment_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assessment Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Assessment Type <span class="text-red-500">*</span>
                    </label>
                    <select name="assessment_type" 
                            id="assessment_type"
                            required
                            onchange="toggleRubricSection()"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">Select Type</option>
                        @foreach($assessmentTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('assessment_type', $assessment->assessment_type) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('assessment_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Multiple CLOs Section -->
                <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">CLO Configuration</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add multiple CLOs for this assessment</p>
                        </div>
                        <button type="button" 
                                onclick="addCloRow()"
                                class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add CLO
                        </button>
                    </div>
                    
                    <div id="closContainer" class="space-y-4">
                        @php
                            $assessmentClos = $assessment->clos->count() > 0 ? $assessment->clos : collect([(object)['id' => null, 'clo_code' => $assessment->clo_code, 'weight_percentage' => $assessment->weight_percentage, 'order' => 0]]);
                        @endphp
                        @foreach($assessmentClos as $index => $clo)
                            <div class="clo-row border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50">
                                <input type="hidden" name="clos[{{ $index }}][id]" value="{{ $clo->id ?? '' }}">
                                <input type="hidden" name="clos[{{ $index }}][order]" value="{{ $index }}">
                                <div class="flex items-center justify-end mb-3">
                                    @if($assessmentClos->count() > 1)
                                    <button type="button" 
                                            onclick="removeCloRow(this)"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        CLO Code <span class="text-red-500">*</span>
                    </label>
                                        <select name="clos[{{ $index }}][clo_code]" 
                            required
                                                onchange="updateTotalCloWeight(); updateComponentCloCodes();"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                            <option value="">Select CLO</option>
                                            @foreach($cloCodes as $cloCode)
                                                <option value="{{ $cloCode }}" {{ ($clo->clo_code ?? $clo->clo_code) === $cloCode ? 'selected' : '' }}>
                                                    {{ $cloCode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            Weight Percentage (%) <span class="text-red-500">*</span>
                    </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">All CLO weightages must sum to 100%</p>
                    <input type="number" 
                                               name="clos[{{ $index }}][weight_percentage]" 
                                               value="{{ $clo->weight_percentage ?? $clo->weight_percentage }}"
                           required
                           min="0"
                           max="100"
                           step="0.01"
                           placeholder="e.g. 20.00"
                                               onchange="updateTotalCloWeight()"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                </div>

                    <!-- Total Weight Display -->
                    <div class="mt-5 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total CLO Weight:</span>
                            <span class="text-base font-bold text-[#003A6C] dark:text-[#0084C5]" id="totalCloWeight">{{ $assessment->clos->sum('weight_percentage') ?: $assessment->weight_percentage }}%</span>
                        </div>
                    </div>
                    
                    <!-- Evaluators Section -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5] mb-1">Evaluators</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Total evaluator scores must equal total CLO weight</p>
                            </div>
                            <button type="button" 
                                    onclick="addEvaluator()"
                                    class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Evaluator
                            </button>
                        </div>
                        
                        <div id="evaluatorsContainer" class="space-y-3">
                            <!-- Load all evaluators from database -->
                            @php
                                $assessmentEvaluators = $assessment->evaluators->count() > 0 
                                    ? $assessment->evaluators 
                                    : collect([(object)['id' => null, 'evaluator_role' => $assessment->evaluator_role, 'total_score' => $assessment->clos->sum('weight_percentage') ?: $assessment->weight_percentage, 'order' => 0]]);
                            @endphp
                            @foreach($assessmentEvaluators as $index => $evaluator)
                                <div class="evaluator-row border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-800">
                                    <input type="hidden" name="evaluators[{{ $index }}][id]" value="{{ $evaluator->id ?? '' }}">
                                    <input type="hidden" name="evaluators[{{ $index }}][order]" value="{{ $index }}">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Evaluator Role <span class="text-red-500">*</span>
                    </label>
                                            <select name="evaluators[{{ $index }}][role]" 
                            required
                                                    onchange="validateEvaluatorTotal()"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                                <option value="">Select Role</option>
                        @foreach($evaluatorRoles as $key => $label)
                                                    <option value="{{ $key }}" {{ old('evaluators.'.$index.'.role', $evaluator->evaluator_role ?? $evaluator->evaluator_role) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                                        </div>
                                        <div class="w-32">
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                Total Score % <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" 
                                                   name="evaluators[{{ $index }}][total_score]" 
                                                   required
                                                   min="0"
                                                   max="100"
                                                   step="0.01"
                                                   placeholder="0.00"
                                                   value="{{ old('evaluators.'.$index.'.total_score', $evaluator->total_score ?? $evaluator->total_score) }}"
                                                   onchange="validateEvaluatorTotal()"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <button type="button" 
                                                    onclick="removeEvaluator(this)"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 {{ $assessmentEvaluators->count() > 1 ? '' : 'hidden' }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Evaluator Score:</span>
                            <span class="text-base font-bold text-[#003A6C] dark:text-[#0084C5]" id="totalEvaluatorScore">{{ $assessmentEvaluators->sum('total_score') }}%</span>
                        </div>
                        <div class="mt-2 text-xs" id="evaluatorValidation"></div>
                    </div>
                    
                    @error('clos')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('clos.*')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $assessment->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active (Assessment will appear in evaluation forms)</span>
                    </label>
                </div>

                <!-- Rubric Configuration (for all types except Logbook) - Components Only -->
                <div id="rubricSection" class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6" style="display: {{ $assessment->assessment_type !== 'Logbook' ? 'block' : 'none' }};">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Rubric Configuration</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Configure rubrics or components for this assessment. Total weight must equal the sum of all CLO weights.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Add Button -->
                    <div class="mb-4 flex justify-end">
                        <button type="button" 
                                id="addItemButton"
                                onclick="addComponent()"
                                class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span id="addButtonText">Add Component</span>
                        </button>
                    </div>
                    
                    <!-- Questions Container (Not used for IP yet, keeping for consistency with FYP structure if needed) -->
                    <div id="rubricsContainer" class="space-y-4" style="display: none;">
                        @if($rubrics && $rubrics->count() > 0)
                            @foreach($rubrics as $index => $rubric)
                                <div class="rubric-question border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50" 
                                     draggable="false" 
                                     style="cursor: default !important; user-select: none !important; -webkit-user-drag: none !important;"
                                     ondragstart="return false;"
                                     ondragover="return false;"
                                     ondrop="return false;"
                                     data-no-reorder="true">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">Question {{ $index + 1 }}</span>
                                        <button type="button" 
                                                onclick="removeRubricQuestion(this)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Question Code</label>
                                            <input type="text" 
                                                   name="rubrics[{{ $index }}][question_code]" 
                                                   value="{{ $rubric->question_code }}"
                                                   placeholder="Q1, Q2, Q3..."
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">CLO Code</label>
                                            <select name="rubrics[{{ $index }}][clo_code]" 
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                                @foreach($cloCodes as $clo)
                                                    <option value="{{ $clo }}" {{ $rubric->clo_code === $clo ? 'selected' : '' }}>{{ $clo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Question Title</label>
                                            <input type="text" 
                                                   name="rubrics[{{ $index }}][question_title]" 
                                                   value="{{ $rubric->question_title }}"
                                                   required
                                                   placeholder="e.g. Engineering Ethics & Public Responsibility"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Question Description</label>
                                            <textarea name="rubrics[{{ $index }}][question_description]" 
                                                      rows="2"
                                                      placeholder="Detailed question description..."
                                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">{{ $rubric->question_description }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Weight %</label>
                                            <input type="number" 
                                                   name="rubrics[{{ $index }}][weight_percentage]" 
                                                   value="{{ $rubric->weight_percentage }}"
                                                   required
                                                   step="0.01"
                                                   min="0"
                                                   max="{{ $assessment->weight_percentage }}"
                                                   onchange="validateRubricWeights()"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Min Score</label>
                                                <input type="number" 
                                                       name="rubrics[{{ $index }}][rubric_min]" 
                                                       value="{{ $rubric->rubric_min }}"
                                                       min="1"
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Max Score</label>
                                                <input type="number" 
                                                       name="rubrics[{{ $index }}][rubric_max]" 
                                                       value="{{ $rubric->rubric_max }}"
                                                       min="1"
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                            </div>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Example Answer / Guidance (Optional)</label>
                                            <textarea name="rubrics[{{ $index }}][example_answer]" 
                                                      rows="2"
                                                      placeholder="Optional guidance or example answer..."
                                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">{{ $rubric->example_answer }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Components Container -->
                    <div id="componentsContainer" class="space-y-4">
                        @if($components && $components->count() > 0)
                            @foreach($components as $index => $component)
                                <div class="component-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50 cursor-move" 
                                     data-order="{{ $index }}"
                                     draggable="true">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <div class="flex flex-col gap-1">
                                                <button type="button" 
                                                        onclick="moveComponentUp(this)"
                                                        class="text-[#0084C5] hover:text-[#003A6C] disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                                                        {{ $index === 0 ? 'disabled' : '' }}
                                                        title="Move Up">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                </button>
                                                <button type="button" 
                                                        onclick="moveComponentDown(this)"
                                                        class="text-[#0084C5] hover:text-[#003A6C] disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                                                        {{ $index === $components->count() - 1 ? 'disabled' : '' }}
                                                        title="Move Down">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                            </svg>
                                            <span class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">Component {{ $index + 1 }}</span>
                                        </div>
                                        <button type="button" 
                                                onclick="removeComponent(this)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="hidden" name="components[{{ $index }}][id]" value="{{ $component->id }}">
                                    <input type="hidden" name="components[{{ $index }}][order]" value="{{ $index }}" class="component-order">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                Component Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="components[{{ $index }}][component_name]" 
                                                   value="{{ $component->component_name }}"
                                                   required
                                                   placeholder="e.g. Problem Statement, Project Objectives"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                Criteria Keywords
                                            </label>
                                            <textarea name="components[{{ $index }}][criteria_keywords]" 
                                                      rows="2"
                                                      placeholder="e.g. Clarity and Conciseness, Relevance, Specificity, Value-added Quality, and Originality"
                                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">{{ $component->criteria_keywords }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                CLO Code <span class="text-red-500">*</span>
                                            </label>
                                            @php
                                                // Get all CLOs for this assessment
                                                $assessmentClos = $assessment->clos->count() > 0 
                                                    ? $assessment->clos->pluck('clo_code')->toArray()
                                                    : [$assessment->clo_code];
                                            @endphp
                                            <select name="components[{{ $index }}][clo_code]" 
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                                <option value="">Select CLO</option>
                                                @foreach($assessmentClos as $cloCode)
                                                    <option value="{{ $cloCode }}" {{ ($component->clo_code ?? $assessment->clo_code) === $cloCode ? 'selected' : '' }}>
                                                        {{ $cloCode }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select from assessment CLOs</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                Weight % <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" 
                                                   name="components[{{ $index }}][weight_percentage]" 
                                                   value="{{ $component->weight_percentage }}"
                                                   required
                                                   step="0.01"
                                                   min="0"
                                                   onchange="validateComponentWeights()"
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Weight Validation (Unified) -->
                    <div class="mt-4 p-3 rounded-lg" id="weightValidation">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Total Weight:</span>
                            <span class="font-bold" id="totalWeight">0.00%</span>
                        </div>
                        <div class="mt-2 text-xs" id="weightStatus"></div>
                    </div>
                </div>

                <!-- Logbook Configuration (for Logbook type) -->
                <div id="logbookSection" class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6" style="display: {{ $assessment->assessment_type === 'Logbook' ? 'block' : 'none' }};">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-2">Logbook Configuration</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Configure monthly evaluation components for tracking student progress over time.
                        </p>
                        
                        <!-- Assessment-Level Configuration -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-5 border border-gray-200 dark:border-gray-700 mb-6">
                            <h4 class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Logbook Settings</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <!-- Period Type Selection -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Period Type <span class="text-red-500">*</span>
                                    </label>
                                    @php
                                        // Detect period type from existing components
                                        $detectedPeriodType = 'months';
                                        if (isset($logbookComponents) && $logbookComponents && $logbookComponents->count() > 0) {
                                            $firstLabel = strtolower($logbookComponents->first()->duration_label ?? '');
                                            if (strpos($firstLabel, 'week') !== false) {
                                                $detectedPeriodType = 'weeks';
                                            }
                                        }
                                    @endphp
                                    <select id="logbookPeriodType" 
                                            onchange="updateLogbookPeriodType()"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="months" {{ $detectedPeriodType === 'months' ? 'selected' : '' }}>Months/Periods</option>
                                        <option value="weeks" {{ $detectedPeriodType === 'weeks' ? 'selected' : '' }}>Weeks</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select whether to configure by months or weeks</p>
                                </div>
                                
                                <!-- Number of Periods (Months or Weeks) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        <span id="logbookPeriodTypeLabel">Number of Months/Periods</span> <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="logbookNumberOfMonths"
                                           min="1"
                                           max="52"
                                           value="{{ isset($logbookComponents) && $logbookComponents->count() > 0 ? $logbookComponents->count() : 6 }}"
                                           onchange="generateLogbookComponents()"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="logbookPeriodTypeHelp">Total number of evaluation periods (e.g., 6 for 6 months)</p>
                                </div>
                                
                                <!-- Scale Type -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Evaluation Scale <span class="text-red-500">*</span>
                                    </label>
                                    @php
                                        $firstLogbookComponent = isset($logbookComponents) && $logbookComponents && $logbookComponents->count() > 0 ? $logbookComponents->first() : null;
                                        $scaleMin = $firstLogbookComponent ? ($firstLogbookComponent->rubric_scale_min ?? 1) : 1;
                                        $scaleMax = $firstLogbookComponent ? ($firstLogbookComponent->rubric_scale_max ?? 10) : 10;
                                        $scaleType = '1-10';
                                        if ($scaleMin == 1 && $scaleMax == 5) $scaleType = '1-5';
                                        elseif ($scaleMin == 1 && $scaleMax == 10) $scaleType = '1-10';
                                        elseif ($scaleMin == 1 && $scaleMax == 100) $scaleType = '1-100';
                                        elseif ($scaleMin != 1 || $scaleMax != 10) $scaleType = 'custom';
                                    @endphp
                                    <select id="logbookScaleType" 
                                            onchange="updateLogbookScale()"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="1-5" {{ $scaleType === '1-5' ? 'selected' : '' }}>1-5 Scale (Poor → Excellent)</option>
                                        <option value="1-10" {{ $scaleType === '1-10' ? 'selected' : '' }}>1-10 Scale (Poor → Excellent)</option>
                                        <option value="1-100" {{ $scaleType === '1-100' ? 'selected' : '' }}>1-100 Scale (Percentage)</option>
                                        <option value="custom" {{ $scaleType === 'custom' ? 'selected' : '' }}>Custom Scale</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Custom Scale Configuration -->
                            <div id="customScaleConfig" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4" style="display: {{ $scaleType === 'custom' ? 'block' : 'none' }};">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Minimum Score <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="logbookScaleMin"
                                           min="0"
                                           value="{{ $scaleMin }}"
                                           onchange="updateLogbookScale()"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Maximum Score <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="logbookScaleMax"
                                           min="1"
                                           value="{{ $scaleMax }}"
                                           onchange="updateLogbookScale()"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                </div>
                            </div>
                            
                            <!-- Auto-Distribute Weights -->
                            <div class="flex items-center gap-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           id="logbookAutoDistribute"
                                           checked
                                           onchange="autoDistributeLogbookWeights()"
                                           class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Auto-distribute weights equally across all months</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <button type="button" 
                                    onclick="generateLogbookComponents()"
                                    class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Generate Components
                            </button>
                        </div>
                        <button type="button" 
                                onclick="addLogbookComponent()"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span id="addLogbookComponentBtnText">Add Period Manually</span>
                        </button>
                    </div>
                    
                    <!-- Logbook Components Container -->
                    <div id="logbookComponentsContainer" class="space-y-4">
                        @if(isset($logbookComponents) && $logbookComponents && $logbookComponents->count() > 0)
                            @foreach($logbookComponents as $index => $component)
                                <!-- Existing logbook components will be populated by JavaScript -->
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Logbook Weight Validation -->
                    <div class="mt-4 p-3 rounded-lg" id="logbookWeightValidation">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Total Weight:</span>
                            <span class="font-bold" id="logbookTotalWeight">0.00%</span>
                        </div>
                        <div class="mt-2 text-xs" id="logbookWeightStatus"></div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route($routePrefix . '.index') }}" 
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Update Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Explicitly disable reordering for rubric questions */
.rubric-question {
    cursor: default !important;
    user-select: none !important;
    -webkit-user-drag: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    pointer-events: auto !important;
}

.rubric-question * {
    cursor: default !important;
    -webkit-user-drag: none !important;
}

/* Ensure rubric questions cannot be dragged even if draggable attribute is set */
.rubric-question[draggable="true"],
.rubric-question[data-no-reorder="true"] {
    draggable: false !important;
    cursor: default !important;
}

/* Prevent any drag styling on rubric questions */
.rubric-question:active,
.rubric-question:hover {
    cursor: default !important;
}

/* Ensure rubric questions never get component-item styling */
.rubric-question.component-item {
    cursor: default !important;
    -webkit-user-drag: none !important;
}
</style>

<script>
// CLO codes are fixed for the current course

let rubricIndex = {{ $rubrics ? $rubrics->count() : 0 }};
let componentIndex = {{ $components ? $components->count() : 0 }};
let logbookComponentIndex = {{ isset($logbookComponents) && $logbookComponents ? $logbookComponents->count() : 0 }};
let assessmentWeight = {{ $assessment->clos->sum('weight_percentage') ?: $assessment->weight_percentage }};
const cloCodes = @json($cloCodes);
@php
    // Get all CLOs for this assessment for JavaScript
    $assessmentClosForJs = $assessment->clos->count() > 0 
        ? $assessment->clos->pluck('clo_code')->toArray()
        : [$assessment->clo_code];
@endphp
const assessmentCloCodes = @json($assessmentClosForJs);

function addRubricQuestion() {
    const container = document.getElementById('rubricsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'rubric-question border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50';
    // Ensure it does NOT have component-item class (which enables dragging)
    questionDiv.classList.remove('component-item');
    questionDiv.setAttribute('draggable', 'false'); // Reordering disabled for rubrics
    questionDiv.setAttribute('data-no-reorder', 'true');
    
    // Explicitly prevent drag events on newly created rubric questions
    questionDiv.addEventListener('dragstart', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    });
    questionDiv.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    });
    questionDiv.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    });
    questionDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">Question ${rubricIndex + 1}</span>
            <button type="button" onclick="removeRubricQuestion(this)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Question Code</label>
                <input type="text" name="rubrics[${rubricIndex}][question_code]" value="Q${rubricIndex + 1}" placeholder="Q1, Q2, Q3..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">CLO Code</label>
                <select name="rubrics[${rubricIndex}][clo_code]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                    ${cloCodes.map(clo => `<option value="${clo}">${clo}</option>`).join('')}
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Question Title</label>
                <input type="text" name="rubrics[${rubricIndex}][question_title]" required placeholder="e.g. Engineering Ethics & Public Responsibility" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Question Description</label>
                <textarea name="rubrics[${rubricIndex}][question_description]" rows="2" placeholder="Detailed question description..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Weight %</label>
                <input type="number" name="rubrics[${rubricIndex}][weight_percentage]" required step="0.01" min="0" max="${assessmentWeight}" onchange="validateRubricWeights()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Min Score</label>
                    <input type="number" name="rubrics[${rubricIndex}][rubric_min]" value="1" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Max Score</label>
                    <input type="number" name="rubrics[${rubricIndex}][rubric_max]" value="5" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Example Answer / Guidance (Optional)</label>
                <textarea name="rubrics[${rubricIndex}][example_answer]" rows="2" placeholder="Optional guidance or example answer..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm"></textarea>
            </div>
        </div>
    `;
    container.appendChild(questionDiv);
    rubricIndex++;
    validateRubricWeights();
}

function removeRubricQuestion(button) {
    if (confirm('Are you sure you want to remove this question?')) {
        button.closest('.rubric-question').remove();
        validateRubricWeights();
        // Renumber questions
        const questions = document.querySelectorAll('.rubric-question');
        questions.forEach((q, index) => {
            q.querySelector('.text-sm.font-semibold').textContent = `Question ${index + 1}`;
        });
    }
}

function validateRubricWeights() {
    const weightInputs = document.querySelectorAll('#rubricsContainer input[name*="rubrics"][name*="[weight_percentage]"]');
    let total = 0;
    weightInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    updateWeightValidation(total, 'rubric');
}

function validateComponentWeights() {
    const weightInputs = document.querySelectorAll('#componentsContainer input[name*="components"][name*="[weight_percentage]"]');
    let total = 0;
    weightInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    updateWeightValidation(total, 'component');
}

function updateTotalCloWeight() {
    const weightInputs = document.querySelectorAll('#closContainer input[name*="[weight_percentage]"]');
    let total = 0;
    weightInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    const totalSpan = document.getElementById('totalCloWeight');
    if (totalSpan) {
        totalSpan.textContent = total.toFixed(2) + '%';
    }

    // Sync with global assessmentWeight for logbook use
    assessmentWeight = total;
    updateWeightDisplay();
    
    // Update logbook weights if we are in logbook mode
    if (typeof validateLogbookWeights === 'function') {
        validateLogbookWeights();
    }
    
    // Re-validate components against new total
    if (typeof validateComponentWeights === 'function') {
        validateComponentWeights();
    }
}

// Get all selected CLO codes from the CLO Configuration section
function getAllSelectedCloCodes() {
    const cloSelects = document.querySelectorAll('#closContainer select[name*="[clo_code]"]');
    const cloCodes = [];
    cloSelects.forEach(select => {
        if (select.value) {
            cloCodes.push(select.value);
        }
    });
    return cloCodes;
}

// Get the first selected CLO code as default
function getDefaultCloCode() {
    const cloCodes = getAllSelectedCloCodes();
    return cloCodes.length > 0 ? cloCodes[0] : '';
}

// Update component CLO code options when assessment CLOs change
function updateComponentCloCodes() {
    const availableCloCodes = getAllSelectedCloCodes();
    const defaultClo = availableCloCodes.length > 0 ? availableCloCodes[0] : '';
    
    // Update all component CLO selects
    document.querySelectorAll('#componentsContainer select[name*="[clo_code]"]').forEach(select => {
        const currentValue = select.value;
        const previousValue = select.getAttribute('data-previous-value') || currentValue;
        
        // Clear existing options except the first one
        select.innerHTML = '<option value="">Select CLO</option>';
        
        // Add available CLO codes
        availableCloCodes.forEach(cloCode => {
            const option = document.createElement('option');
            option.value = cloCode;
            option.textContent = cloCode;
            if (cloCode === previousValue) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    });

    // Also update logbook component CLO selects
    document.querySelectorAll('#logbookComponentsContainer select[name*="[clo_code]"]').forEach(select => {
        const currentValue = select.value;
        
        select.innerHTML = '<option value="">Select CLO</option>';
        availableCloCodes.forEach(cloCode => {
            const option = document.createElement('option');
            option.value = cloCode;
            option.textContent = cloCode;
            if (cloCode === currentValue || (!currentValue && cloCode === defaultClo)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    });
}

function updateWeightValidation(total, type) {
    const totalCloWeight = parseFloat(document.getElementById('totalCloWeight')?.textContent?.replace('%', '') || 0);
    const statusDiv = type === 'logbook' ? document.getElementById('logbookWeightStatus') : document.getElementById('weightStatus');
    const validationDiv = type === 'logbook' ? document.getElementById('logbookWeightValidation') : document.getElementById('weightValidation');
    const totalSpan = type === 'logbook' ? document.getElementById('logbookTotalWeight') : document.getElementById('totalWeight');
    
    if (totalSpan) {
        totalSpan.textContent = total.toFixed(2) + '%';
    }
    
    if (totalCloWeight === 0) {
        if (statusDiv) statusDiv.innerHTML = '<span class="text-gray-500 italic">Configure CLOs first to validate weight</span>';
        if (validationDiv) validationDiv.className = 'mt-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-800';
        return;
    }

    if (Math.abs(total - totalCloWeight) < 0.05) {
        if (statusDiv) statusDiv.innerHTML = '<span class="text-green-600 dark:text-green-400 font-medium">✓ Total matches CLO weight</span>';
        if (validationDiv) validationDiv.className = 'mt-4 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800';
    } else {
        const difference = (totalCloWeight - total).toFixed(2);
        if (statusDiv) statusDiv.innerHTML = `<span class="text-red-600 dark:text-red-400 font-medium">⚠ Difference: ${difference}% (must equal ${totalCloWeight.toFixed(2)}%)</span>`;
        if (validationDiv) validationDiv.className = 'mt-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
    }
}

function addComponent(data = null) {
    // If not repopulating (manual add), verify CLOs exist
    if (!data) {
        const availableCloCodes = getAllSelectedCloCodes();
        if (availableCloCodes.length === 0) {
            alert('Please configure at least one CLO in the CLO Configuration section first.');
            return;
        }
    }
    
    const container = document.getElementById('componentsContainer');
    const currentCount = componentIndex;
    const componentDiv = document.createElement('div');
    componentDiv.className = 'component-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50 cursor-move';
    componentDiv.setAttribute('draggable', 'true');
    componentDiv.setAttribute('data-order', currentCount);
    
    const availableCloCodes = getAllSelectedCloCodes();
    const defaultClo = getDefaultCloCode();
    
    // Build CLO options HTML
    const cloOptionsHtml = availableCloCodes.map(clo => 
        `<option value="${clo}" ${clo === (data?.clo_code || defaultClo) ? 'selected' : ''}>${clo}</option>`
    ).join('');
    
    componentDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="flex flex-col gap-1">
                    <button type="button" 
                            onclick="moveComponentUp(this)"
                            class="text-[#0084C5] hover:text-[#003A6C] disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                            ${currentCount === 0 ? 'disabled' : ''}
                            title="Move Up">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button type="button" 
                            onclick="moveComponentDown(this)"
                            class="text-[#0084C5] hover:text-[#003A6C] disabled:text-gray-300 disabled:cursor-not-allowed transition-colors"
                            title="Move Down">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                </svg>
                <span class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">Component ${currentCount + 1}</span>
            </div>
            <button type="button" onclick="removeComponent(this)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
        <input type="hidden" name="components[${currentCount}][order]" value="${currentCount}" class="component-order">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Component Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="components[${currentCount}][component_name]" 
                       value="${data?.component_name || ''}"
                       required
                       placeholder="e.g. Problem Statement, Project Objectives"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Criteria Keywords
                </label>
                <textarea name="components[${currentCount}][criteria_keywords]" 
                          rows="2"
                          placeholder="e.g. Clarity and Conciseness, Relevance, Specificity, Value-added Quality, and Originality"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">${data?.criteria_keywords || ''}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    CLO Code <span class="text-red-500">*</span>
                </label>
                <select name="components[${currentCount}][clo_code]" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select CLO</option>
                    ${cloOptionsHtml}
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select from configured CLOs</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Weight % <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="components[${currentCount}][weight_percentage]" 
                       value="${data?.weight_percentage || ''}"
                       required
                       step="0.01"
                       min="0"
                       onchange="validateComponentWeights()"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
        </div>
    `;
    container.appendChild(componentDiv);
    componentIndex++;
    setupComponentDragAndDrop(componentDiv);
    updateComponentOrder();
    validateComponentWeights();
}

function removeComponent(button) {
    if (confirm('Are you sure you want to remove this component?')) {
        button.closest('.component-item').remove();
        updateComponentOrder();
        validateComponentWeights();
    }
}

function moveComponentUp(button) {
    const component = button.closest('.component-item');
    const prevComponent = component.previousElementSibling;
    if (prevComponent && prevComponent.classList.contains('component-item')) {
        component.parentNode.insertBefore(component, prevComponent);
        updateComponentOrder();
    }
}

function moveComponentDown(button) {
    const component = button.closest('.component-item');
    const nextComponent = component.nextElementSibling;
    if (nextComponent && nextComponent.classList.contains('component-item')) {
        component.parentNode.insertBefore(nextComponent, component);
        updateComponentOrder();
    }
}

function updateComponentOrder() {
    // Only update order for component-item, NOT for rubric-question
    const components = document.querySelectorAll('.component-item:not(.rubric-question)');
    components.forEach((component, index) => {
        const orderInput = component.querySelector('.component-order');
        if (orderInput) {
            orderInput.value = index;
            orderInput.name = orderInput.name.replace(/\[\d+\]/, `[${index}]`);
        }
        
        component.querySelectorAll('input, select, textarea').forEach(input => {
            if (input.name && input.name.includes('components[')) {
                input.name = input.name.replace(/components\[\d+\]/, `components[${index}]`);
            }
        });
        
        const numberSpan = component.querySelector('.text-sm.font-semibold');
        if (numberSpan) {
            numberSpan.textContent = `Component ${index + 1}`;
        }
        
        const upButton = component.querySelector('button[onclick*="moveComponentUp"]');
        const downButton = component.querySelector('button[onclick*="moveComponentDown"]');
        
        if (upButton) {
            upButton.disabled = index === 0;
        }
        if (downButton) {
            downButton.disabled = index === components.length - 1;
        }
        
        component.setAttribute('data-order', index);
    });
}

function setupComponentDragAndDrop(element) {
    // Only setup drag-and-drop for component-item, NOT for rubric-question
    if (element.classList.contains('rubric-question')) {
        return; // Skip rubric questions - reordering is disabled
    }
    
    element.addEventListener('dragstart', function(e) {
        // Double-check it's not a rubric question
        if (this.classList.contains('rubric-question')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
        this.classList.add('opacity-50');
    });
    
    element.addEventListener('dragend', function(e) {
        this.classList.remove('opacity-50');
    });
    
    element.addEventListener('dragover', function(e) {
        // Prevent drag-over on rubric questions
        if (this.classList.contains('rubric-question')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    });
    
    element.addEventListener('dragenter', function(e) {
        // Don't highlight rubric questions
        if (this.classList.contains('rubric-question')) {
            return;
        }
        this.classList.add('border-[#0084C5]', 'border-2');
    });
    
    element.addEventListener('dragleave', function(e) {
        // Don't process dragleave for rubric questions
        if (this.classList.contains('rubric-question')) {
            return;
        }
        this.classList.remove('border-[#0084C5]', 'border-2');
    });
    
    element.addEventListener('drop', function(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        this.classList.remove('border-[#0084C5]', 'border-2');
        
        // Only allow dropping component-item elements, not rubric-question
        const draggedElement = document.querySelector('.component-item.opacity-50');
        if (draggedElement && draggedElement !== this && !draggedElement.classList.contains('rubric-question')) {
            // Ensure both are component-item, not rubric-question
            if (this.classList.contains('component-item') && !this.classList.contains('rubric-question')) {
                const allComponents = Array.from(document.querySelectorAll('.component-item:not(.rubric-question)'));
                const draggedIndex = allComponents.indexOf(draggedElement);
                const dropIndex = allComponents.indexOf(this);
                
                if (draggedIndex >= 0 && dropIndex >= 0 && draggedIndex !== dropIndex) {
                    if (draggedIndex < dropIndex) {
                        this.parentNode.insertBefore(draggedElement, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedElement, this);
                    }
                    
                    updateComponentOrder();
                }
            }
        }
        return false;
    });
}

// Default component presets for each assessment type (same as create view)
const defaultComponentPresets = {
    'Assignment': [
        { component_name: 'Problem Statement', clo_code: 'CLO4', weight_percentage: 12.5, criteria_keywords: 'Clarity and Conciseness, Relevance, Specificity' },
        { component_name: 'Objectives & Scope', clo_code: 'CLO4', weight_percentage: 12.5, criteria_keywords: 'Clear objectives, Well-defined scope' },
        { component_name: 'Literature Review', clo_code: 'CLO5', weight_percentage: 12.5, criteria_keywords: 'Relevance, Depth, Critical analysis' },
        { component_name: 'Methodology', clo_code: 'CLO6', weight_percentage: 12.5, criteria_keywords: 'Appropriateness, Clarity, Feasibility' },
        { component_name: 'Analysis & Discussion', clo_code: 'CLO6', weight_percentage: 12.5, criteria_keywords: 'Depth, Critical thinking, Evidence-based' },
        { component_name: 'Conclusion', clo_code: 'CLO7', weight_percentage: 12.5, criteria_keywords: 'Summary, Implications, Future work' },
        { component_name: 'Writing & Formatting', clo_code: 'CLO5', weight_percentage: 12.5, criteria_keywords: 'Grammar, Style, Format compliance' },
        { component_name: 'Originality & Ethics', clo_code: 'CLO5', weight_percentage: 12.5, criteria_keywords: 'Originality, Citation, Ethical considerations' }
    ],
    'Presentation': [
        { component_name: 'Slide Design & Structure', clo_code: 'CLO5', weight_percentage: 20, criteria_keywords: 'Visual appeal, Organization, Clarity' },
        { component_name: 'Technical Content Accuracy', clo_code: 'CLO1', weight_percentage: 25, criteria_keywords: 'Accuracy, Depth, Relevance' },
        { component_name: 'Delivery & Communication', clo_code: 'CLO2', weight_percentage: 25, criteria_keywords: 'Clarity, Pace, Engagement' },
        { component_name: 'Confidence & Engagement', clo_code: 'CLO2', weight_percentage: 15, criteria_keywords: 'Confidence, Body language, Audience interaction' },
        { component_name: 'Q&A Handling', clo_code: 'CLO6', weight_percentage: 15, criteria_keywords: 'Understanding, Response quality, Critical thinking' }
    ],
    'Observation': [
        { component_name: 'Attendance & Commitment', clo_code: 'CLO7', weight_percentage: 25, criteria_keywords: 'Punctuality, Regular attendance, Dedication' },
        { component_name: 'Professional Conduct', clo_code: 'CLO7', weight_percentage: 25, criteria_keywords: 'Ethics, Professionalism, Respect' },
        { component_name: 'Task Execution Quality', clo_code: 'CLO1', weight_percentage: 25, criteria_keywords: 'Quality, Accuracy, Timeliness' },
        { component_name: 'Initiative & Responsibility', clo_code: 'CLO7', weight_percentage: 25, criteria_keywords: 'Proactiveness, Ownership, Problem-solving' }
    ],
    'Project': [
        { component_name: 'Problem Identification', clo_code: 'CLO4', weight_percentage: 15, criteria_keywords: 'Clarity, Relevance, Significance' },
        { component_name: 'Design / Development Process', clo_code: 'CLO6', weight_percentage: 25, criteria_keywords: 'Methodology, Planning, Documentation' },
        { component_name: 'Implementation Quality', clo_code: 'CLO1', weight_percentage: 25, criteria_keywords: 'Code quality, Functionality, Best practices' },
        { component_name: 'Testing & Validation', clo_code: 'CLO6', weight_percentage: 20, criteria_keywords: 'Test coverage, Validation, Quality assurance' },
        { component_name: 'Final Output / Product', clo_code: 'CLO7', weight_percentage: 15, criteria_keywords: 'Completeness, Quality, Deliverables' }
    ],
    'Quiz': [
        { component_name: 'Concept Understanding', clo_code: 'CLO5', weight_percentage: 50, criteria_keywords: 'Fundamental concepts, Theory comprehension' },
        { component_name: 'Application of Knowledge', clo_code: 'CLO6', weight_percentage: 50, criteria_keywords: 'Practical application, Problem-solving' }
    ],
    'Test': [
        { component_name: 'Fundamental Knowledge', clo_code: 'CLO5', weight_percentage: 40, criteria_keywords: 'Core concepts, Theory mastery' },
        { component_name: 'Analytical Thinking', clo_code: 'CLO6', weight_percentage: 30, criteria_keywords: 'Analysis, Critical evaluation' },
        { component_name: 'Problem Solving', clo_code: 'CLO6', weight_percentage: 30, criteria_keywords: 'Solution approach, Correctness' }
    ],
    'Final Exam': [
        { component_name: 'Knowledge Mastery', clo_code: 'CLO5', weight_percentage: 35, criteria_keywords: 'Comprehensive understanding, Depth' },
        { component_name: 'Critical Analysis', clo_code: 'CLO6', weight_percentage: 35, criteria_keywords: 'Critical thinking, Evaluation' },
        { component_name: 'Problem Solving & Synthesis', clo_code: 'CLO6', weight_percentage: 30, criteria_keywords: 'Integration, Synthesis, Application' }
    ],
    'Report': [],
    'Oral': [],
    'Rubric': []
};

function toggleRubricSection() {
    const assessmentType = document.getElementById('assessment_type').value;
    const rubricSection = document.getElementById('rubricSection');
    const logbookSection = document.getElementById('logbookSection');
    const componentsContainer = document.getElementById('componentsContainer');
    
    // Hide both sections first
    if (rubricSection) rubricSection.style.display = 'none';
    if (logbookSection) logbookSection.style.display = 'none';
    
    if (assessmentType === 'Logbook') {
        // Show Logbook section
        if (logbookSection) logbookSection.style.display = 'block';
    } else if (assessmentType) {
        // Show Rubric/Component section for ALL assessment types (except Logbook)
        if (rubricSection) rubricSection.style.display = 'block';
        
        // Note: In edit view, we don't auto-populate defaults since assessment already exists
        // User can manually add components if needed
    }
    updateWeightDisplay();
}

function updateWeightDisplay() {
    // Use total CLO weight instead of single weight input
    const totalCloWeightText = document.getElementById('totalCloWeight')?.textContent?.replace('%', '') || '0';
    const weightDisplay = document.getElementById('assessmentWeightDisplay');
    if (weightDisplay) {
        const weight = parseFloat(totalCloWeightText) || 0;
        weightDisplay.textContent = weight.toFixed(2);
        assessmentWeight = weight;
    }
}

// Evaluator Management Functions
let evaluatorIndex = {{ $assessmentEvaluators->count() }};

function addEvaluator() {
    const container = document.getElementById('evaluatorsContainer');
    const currentCount = evaluatorIndex;
    const evaluatorDiv = document.createElement('div');
    evaluatorDiv.className = 'evaluator-row border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-800';
    
    evaluatorDiv.innerHTML = `
        <input type="hidden" name="evaluators[${currentCount}][id]" value="">
        <input type="hidden" name="evaluators[${currentCount}][order]" value="${currentCount}">
        <div class="flex items-center gap-3">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Evaluator Role <span class="text-red-500">*</span>
                </label>
                <select name="evaluators[${currentCount}][role]" 
                        required
                        onchange="validateEvaluatorTotal()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select Role</option>
                    @foreach($evaluatorRoles as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Total Score % <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="evaluators[${currentCount}][total_score]" 
                       required
                       min="0"
                       max="100"
                       step="0.01"
                       placeholder="0.00"
                       onchange="validateEvaluatorTotal()"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div class="flex items-end pb-2">
                <button type="button" 
                        onclick="removeEvaluator(this)"
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(evaluatorDiv);
    evaluatorIndex++;
    
    // Show delete buttons when there are multiple evaluators
    const totalRows = container.querySelectorAll('.evaluator-row').length;
    container.querySelectorAll('.evaluator-row').forEach((row) => {
        const deleteButton = row.querySelector('button[onclick*="removeEvaluator"]');
        if (deleteButton) {
            if (totalRows > 1) {
                deleteButton.classList.remove('hidden');
        } else {
                deleteButton.classList.add('hidden');
            }
        }
    });
    
    validateEvaluatorTotal();
}

function removeEvaluator(button) {
    const container = document.getElementById('evaluatorsContainer');
    const rows = container.querySelectorAll('.evaluator-row');
    
    if (rows.length > 1) {
        button.closest('.evaluator-row').remove();
        
        // Renumber evaluators
        const remainingRows = container.querySelectorAll('.evaluator-row');
        remainingRows.forEach((row, index) => {
            row.querySelectorAll('input, select').forEach(input => {
                if (input.name && input.name.includes('evaluators[')) {
                    input.name = input.name.replace(/evaluators\[\d+\]/, `evaluators[${index}]`);
                }
            });
            // Update order hidden input
            const orderInput = row.querySelector('input[name*="[order]"]');
            if (orderInput) {
                orderInput.value = index;
            }
        });
        
        // Show/hide delete buttons based on remaining count
        const remainingCount = container.querySelectorAll('.evaluator-row').length;
        container.querySelectorAll('.evaluator-row').forEach((row) => {
            const deleteButton = row.querySelector('button[onclick*="removeEvaluator"]');
            if (deleteButton) {
                if (remainingCount > 1) {
                    deleteButton.classList.remove('hidden');
                } else {
                    deleteButton.classList.add('hidden');
                }
            }
        });
        
        validateEvaluatorTotal();
    } else {
        alert('At least one evaluator is required.');
    }
}

function validateEvaluatorTotal() {
    const totalCloWeight = parseFloat(document.getElementById('totalCloWeight')?.textContent?.replace('%', '') || 0);
    const evaluatorInputs = document.querySelectorAll('#evaluatorsContainer input[name*="[total_score]"]');
    const totalEvaluatorScoreSpan = document.getElementById('totalEvaluatorScore');
    const validationDiv = document.getElementById('evaluatorValidation');
    
    let totalEvaluatorScore = 0;
    evaluatorInputs.forEach(input => {
        totalEvaluatorScore += parseFloat(input.value) || 0;
    });
    
    if (totalEvaluatorScoreSpan) {
        totalEvaluatorScoreSpan.textContent = totalEvaluatorScore.toFixed(2) + '%';
    }
    
    if (!validationDiv) return;
    
    if (totalCloWeight === 0) {
        validationDiv.innerHTML = '<span class="text-gray-500 dark:text-gray-400">Configure CLOs first</span>';
        return;
    }
    
    const difference = Math.abs(totalEvaluatorScore - totalCloWeight);
    
    if (difference < 0.01) {
        validationDiv.innerHTML = '<span class="text-[#0084C5] dark:text-[#0084C5] font-medium">✓ Total evaluator scores match total CLO weight</span>';
    } else {
        const diff = (totalCloWeight - totalEvaluatorScore).toFixed(2);
        if (totalEvaluatorScore < totalCloWeight) {
            validationDiv.innerHTML = `<span class="text-red-600 dark:text-red-400 font-medium">⚠ Total evaluator scores must equal total CLO weight (Current: ${totalEvaluatorScore.toFixed(2)}%, Need: ${diff}% more)</span>`;
        } else {
            validationDiv.innerHTML = `<span class="text-red-600 dark:text-red-400 font-medium">⚠ Total evaluator scores exceed total CLO weight (Current: ${totalEvaluatorScore.toFixed(2)}%, Excess: ${Math.abs(diff)}%)</span>`;
        }
    }
}

// Initialize validation on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize total CLO weight display
    updateTotalCloWeight();
    
    // Watch for CLO weight changes
    document.querySelectorAll('#closContainer input[name*="[weight_percentage]"]').forEach(input => {
        input.addEventListener('change', function() {
            updateTotalCloWeight();
            validateEvaluatorTotal();
        });
    });
    
    // Watch for CLO code changes to update component CLO options
    document.querySelectorAll('#closContainer select[name*="[clo_code]"]').forEach(select => {
        select.addEventListener('change', function() {
            updateComponentCloCodes();
            updateTotalCloWeight();
        });
    });
    
    toggleRubricSection();
    
    document.querySelectorAll('.component-item').forEach(component => {
        setupComponentDragAndDrop(component);
    });
    
    updateComponentOrder();
    validateComponentWeights();
    validateEvaluatorTotal();
    
    // Initialize logbook period type
    if (typeof updateLogbookPeriodType === 'function') {
        updateLogbookPeriodType();
    }
    
    // Initialize logbook scale configuration
    updateLogbookScale();
    
    // Repopulate logbook components if they exist
    @if(isset($logbookComponents) && $logbookComponents->count() > 0)
        const existingLogbookComponents = @json($logbookComponents);
        if (typeof addLogbookComponent === 'function' && existingLogbookComponents && existingLogbookComponents.length > 0) {
            // Clear container and reset index before populating
            const container = document.getElementById('logbookComponentsContainer');
            if (container) {
                container.innerHTML = '';
            }
            logbookComponentIndex = 0;
            
            // Sort by order to maintain correct sequence
            const sortedComponents = existingLogbookComponents.sort((a, b) => (a.order || 0) - (b.order || 0));
            
            sortedComponents.forEach(component => {
                addLogbookComponent({
                    id: component.id,
                    duration_label: component.duration_label,
                    clo_code: component.clo_code,
                    weight_percentage: component.weight_percentage,
                    criteria_keywords: component.criteria_keywords || '',
                    order: component.order,
                    rubric_scale_min: component.rubric_scale_min,
                    rubric_scale_max: component.rubric_scale_max
                });
            });
            
            // Validate weights after populating
            if (typeof validateLogbookWeights === 'function') {
                validateLogbookWeights();
            }
        }
    @endif
});

// ========== LOGBOOK COMPONENT FUNCTIONS ==========
// Initialize logbookComponentIndex from existing components count
logbookComponentIndex = {{ isset($logbookComponents) && $logbookComponents ? $logbookComponents->count() : 0 }};

// Get current logbook scale configuration
function getLogbookScale() {
    const scaleType = document.getElementById('logbookScaleType')?.value || '1-10';
    let min = 1, max = 10;
    
    if (scaleType === '1-5') {
        min = 1; max = 5;
    } else if (scaleType === '1-10') {
        min = 1; max = 10;
    } else if (scaleType === '1-100') {
        min = 1; max = 100;
    } else if (scaleType === 'custom') {
        min = parseInt(document.getElementById('logbookScaleMin')?.value || 1);
        max = parseInt(document.getElementById('logbookScaleMax')?.value || 10);
    }
    
    return { min, max, type: scaleType };
}

// Update scale configuration UI
function updateLogbookScale() {
    const scaleType = document.getElementById('logbookScaleType')?.value || '1-10';
    const customConfig = document.getElementById('customScaleConfig');
    
    if (scaleType === 'custom') {
        if (customConfig) customConfig.style.display = 'block';
    } else {
        if (customConfig) customConfig.style.display = 'none';
    }
    
    // Update all existing logbook components with new scale
    updateAllLogbookComponentScales();
}

// Update scale display for all existing logbook components
function updateAllLogbookComponentScales() {
    const components = document.querySelectorAll('.logbook-component-item');
    components.forEach((component, index) => {
        const scaleContainer = component.querySelector('.logbook-scale-container');
        if (scaleContainer) {
            const scale = getLogbookScale();
            const scaleHtml = buildLogbookScaleHtml(index, scale.min, scale.max);
            scaleContainer.innerHTML = scaleHtml;
        }
    });
}

// Build scale HTML based on min/max
function buildLogbookScaleHtml(componentIndex, min, max) {
    let scaleHtml = '';
    const range = max - min;
    const isLargeScale = range > 20;
    
    if (isLargeScale) {
        // For large scales (like 1-100), use a number input instead of radio buttons
        return `
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-red-500 dark:text-red-400 uppercase tracking-wide">Min: ${min}</span>
                <input type="number" 
                       name="logbook_components[${componentIndex}][rubric_score]" 
                       min="${min}"
                       max="${max}"
                       placeholder="Enter score"
                       class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm text-center">
                <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Max: ${max}</span>
            </div>
        `;
    } else {
        // For smaller scales, use radio buttons
        for (let i = min; i <= max; i++) {
            scaleHtml += `
                <label class="flex flex-col items-center cursor-pointer group">
                    <input type="radio" 
                           name="logbook_components[${componentIndex}][rubric_score]" 
                           value="${i}"
                           class="w-5 h-5 text-[#0084C5] border-gray-300 focus:ring-[#0084C5] focus:ring-2">
                    <span class="mt-1 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-[#0084C5]">${i}</span>
                </label>
            `;
        }
        
        return `
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-red-500 dark:text-red-400 uppercase tracking-wide">Poor</span>
                <div class="flex-1 mx-4">
                    <div class="flex justify-between items-center">
                        ${scaleHtml}
                    </div>
                </div>
                <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Excellent</span>
            </div>
        `;
    }
}

// Update period type labels and help text
function updateLogbookPeriodType() {
    const periodType = document.getElementById('logbookPeriodType')?.value || 'months';
    const periodTypeLabel = document.getElementById('logbookPeriodTypeLabel');
    const periodTypeHelp = document.getElementById('logbookPeriodTypeHelp');
    const numberOfMonthsInput = document.getElementById('logbookNumberOfMonths');
    const addButtonText = document.getElementById('addLogbookComponentBtnText');
    
    if (periodType === 'weeks') {
        if (periodTypeLabel) periodTypeLabel.textContent = 'Number of Weeks';
        if (periodTypeHelp) periodTypeHelp.textContent = 'Total number of evaluation weeks (e.g., 12 for 12 weeks)';
        if (numberOfMonthsInput) {
            numberOfMonthsInput.max = '52';
            if (parseInt(numberOfMonthsInput.value) > 52) numberOfMonthsInput.value = '52';
        }
        if (addButtonText) addButtonText.textContent = 'Add Week Manually';
    } else {
        if (periodTypeLabel) periodTypeLabel.textContent = 'Number of Months/Periods';
        if (periodTypeHelp) periodTypeHelp.textContent = 'Total number of evaluation periods (e.g., 6 for 6 months)';
        if (numberOfMonthsInput) {
            numberOfMonthsInput.max = '24';
            if (parseInt(numberOfMonthsInput.value) > 24) numberOfMonthsInput.value = '24';
        }
        if (addButtonText) addButtonText.textContent = 'Add Period Manually';
    }
}

// Generate logbook components based on number of periods
function generateLogbookComponents() {
    const numberOfPeriods = parseInt(document.getElementById('logbookNumberOfMonths')?.value || 6);
    const periodType = document.getElementById('logbookPeriodType')?.value || 'months';
    const availableCloCodes = getAllSelectedCloCodes();
    
    if (availableCloCodes.length === 0) {
        alert('Please configure at least one CLO in the CLO Configuration section first.');
        return;
    }
    
    const maxPeriods = periodType === 'weeks' ? 52 : 24;
    if (numberOfPeriods < 1 || numberOfPeriods > maxPeriods) {
        alert(`Number of ${periodType === 'weeks' ? 'weeks' : 'months'} must be between 1 and ${maxPeriods}.`);
        return;
    }
    
    // Clear existing components
    const container = document.getElementById('logbookComponentsContainer');
    container.innerHTML = '';
    logbookComponentIndex = 0;
    
    // Get assessment weight for auto-distribution
    const totalWeight = assessmentWeight || parseFloat(document.getElementById('totalCloWeight')?.textContent?.replace('%', '') || 0);
    const weightPerPeriod = totalWeight / numberOfPeriods;
    const defaultClo = getDefaultCloCode();
    
    // Generate components
    for (let i = 0; i < numberOfPeriods; i++) {
        const durationLabel = periodType === 'weeks' ? `Week ${i + 1}` : `Month ${i + 1}`;
        addLogbookComponent({
            duration_label: durationLabel,
            clo_code: defaultClo,
            weight_percentage: weightPerPeriod.toFixed(2),
            criteria_keywords: '',
            order: i
        });
    }
    
    // Always auto-distribute after generation to ensure exact sum matching assessment total
    autoDistributeLogbookWeights();
}

// Auto-distribute weights equally
function autoDistributeLogbookWeights() {
    const components = document.querySelectorAll('.logbook-component-item');
    if (components.length === 0) return;
    
    const targetTotal = assessmentWeight || parseFloat(document.getElementById('totalCloWeight')?.textContent?.replace('%', '') || 0);
    const weightPerComponent = targetTotal / components.length;
    let currentSum = 0;
    
    components.forEach((component, index) => {
        const weightInput = component.querySelector('input[name*="[weight_percentage]"]');
        if (weightInput) {
            if (index === components.length - 1) {
                // Last component gets the remainder to ensure exact sum
                const remainder = (targetTotal - currentSum).toFixed(2);
                weightInput.value = remainder;
            } else {
                const weight = parseFloat(weightPerComponent.toFixed(2));
                weightInput.value = weight.toFixed(2);
                currentSum += weight;
            }
        }
    });
    
    validateLogbookWeights();
}

function addLogbookComponent(data = null) {
    const availableCloCodes = getAllSelectedCloCodes();
    
    // If not repopulating (manual add), verify CLOs exist
    if (!data && availableCloCodes.length === 0) {
        alert('Please configure at least one CLO in the CLO Configuration section first.');
        return;
    }
    
    const container = document.getElementById('logbookComponentsContainer');
    const currentCount = logbookComponentIndex;
    const periodNumber = currentCount + 1;
    const periodType = document.getElementById('logbookPeriodType')?.value || 'months';
    const componentDiv = document.createElement('div');
    componentDiv.className = 'logbook-component-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30';
    
    const defaultClo = getDefaultCloCode();
    // Use scale from data if provided, otherwise get from UI
    const scale = data?.rubric_scale_min !== undefined && data?.rubric_scale_max !== undefined
        ? { min: data.rubric_scale_min, max: data.rubric_scale_max }
        : getLogbookScale();
    
    // Build CLO options HTML
    const cloOptionsHtml = availableCloCodes.map(clo => 
        `<option value="${clo}" ${clo === (data?.clo_code || defaultClo) ? 'selected' : ''}>${clo}</option>`
    ).join('');
    
    // Build scale HTML
    const scaleHtml = buildLogbookScaleHtml(currentCount, scale.min, scale.max);
    
    // Determine default duration label based on period type
    const defaultDurationLabel = data?.duration_label || (periodType === 'weeks' ? `Week ${periodNumber}` : `Month ${periodNumber}`);
    
    componentDiv.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">Period ${periodNumber}</span>
            <button type="button" onclick="removeLogbookComponent(this)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Duration Label <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="logbook_components[${currentCount}][duration_label]" 
                       value="${defaultDurationLabel}"
                       required
                       placeholder="e.g. Month 1, M1, Week 1-4"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    CLO Code <span class="text-red-500">*</span>
                </label>
                <select name="logbook_components[${currentCount}][clo_code]" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select CLO</option>
                    ${cloOptionsHtml}
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Weight % <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="logbook_components[${currentCount}][weight_percentage]" 
                       value="${data?.weight_percentage || ''}"
                       required
                       step="0.01"
                       min="0"
                       onchange="validateLogbookWeights()"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Criteria Keywords
                </label>
                <input type="text" 
                       name="logbook_components[${currentCount}][criteria_keywords]" 
                       value="${data?.criteria_keywords || ''}"
                       placeholder="e.g. Progress, Effort, Quality"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
        </div>
        
        <!-- Scale Rating -->
        <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                    Score (${scale.min}-${scale.max} Scale)
                </label>
                <button type="button" onclick="clearLogbookScale(this, ${currentCount})" class="text-xs text-gray-500 hover:text-[#0084C5] dark:text-gray-400 dark:hover:text-[#0084C5]">
                    Clear selection
                </button>
            </div>
            <div class="logbook-scale-container">
                ${scaleHtml}
            </div>
        </div>
        
        ${data?.id ? `<input type="hidden" name="logbook_components[${currentCount}][id]" value="${data.id}">` : ''}
        <input type="hidden" name="logbook_components[${currentCount}][order]" value="${data?.order ?? currentCount}" class="logbook-component-order">
        <input type="hidden" name="logbook_components[${currentCount}][rubric_scale_min]" value="${scale.min}">
        <input type="hidden" name="logbook_components[${currentCount}][rubric_scale_max]" value="${scale.max}">
    `;
    container.appendChild(componentDiv);
    logbookComponentIndex++;
    validateLogbookWeights();
}

function removeLogbookComponent(button) {
    if (confirm('Are you sure you want to remove this period?')) {
        button.closest('.logbook-component-item').remove();
        // Renumber components
        const components = document.querySelectorAll('.logbook-component-item');
        components.forEach((component, index) => {
            const titleSpan = component.querySelector('.text-sm.font-semibold');
            if (titleSpan) {
                titleSpan.textContent = `Period ${index + 1}`;
            }
            // Update order hidden input
            const orderInput = component.querySelector('input[name*="[order]"]');
            if (orderInput) {
                orderInput.value = index;
            }
        });
        validateLogbookWeights();
    }
}

function clearLogbookScale(button, componentIndex) {
    const container = button.closest('.logbook-component-item');
    const scaleInput = container.querySelector(`input[name="logbook_components[${componentIndex}][rubric_score]"]`);
    if (scaleInput) {
        if (scaleInput.type === 'radio') {
            scaleInput.checked = false;
        } else {
            scaleInput.value = '';
        }
    }
}

function validateLogbookWeights() {
    const weightInputs = document.querySelectorAll('#logbookComponentsContainer input[name*="[weight_percentage]"]');
    let total = 0;
    weightInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    updateWeightValidation(total, 'logbook');
}

// Initialization and Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    toggleRubricSection();
    
    // Initialize total CLO weight
    updateTotalCloWeight();

    // Watch for CLO weight changes
    document.querySelectorAll('#closContainer input[name*="[weight_percentage]"]').forEach(input => {
        input.addEventListener('input', function() {
            updateTotalCloWeight();
            validateComponentWeights();
        });
    });

    // Watch for CLO code changes
    document.querySelectorAll('#closContainer select[name*="[clo_code]"]').forEach(select => {
        select.addEventListener('change', function() {
            updateComponentCloCodes();
        });
    });
    
    // Watch for component weight changes
    const componentsContainer = document.getElementById('componentsContainer');
    if (componentsContainer) {
        componentsContainer.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="weight_percentage"]')) {
                validateComponentWeights();
            }
        });
    }

    // Setup drag and drop for existing components (NOT for rubric questions)
    document.querySelectorAll('.component-item:not(.rubric-question)').forEach(component => {
        setupComponentDragAndDrop(component);
    });
    
    // Explicitly disable drag-and-drop for all rubric questions
    document.querySelectorAll('.rubric-question').forEach(rubric => {
        rubric.setAttribute('draggable', 'false');
        // Prevent any drag events
        rubric.addEventListener('dragstart', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
        rubric.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
        rubric.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    });
});
</script>
@endsection

