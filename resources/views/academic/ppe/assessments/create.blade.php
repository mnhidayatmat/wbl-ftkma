@php
    $routePrefix = 'academic.ppe.assessments';
@endphp
@extends('layouts.app')

@section('title', $courseName . ' – Create Assessment')

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
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $courseName }} – Create Assessment</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Define a new assessment for {{ $courseName }}</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
            <form action="{{ route($routePrefix . '.store') }}" method="POST">
                @csrf

                <!-- Course Code (Hidden - fixed to current course) -->
                <input type="hidden" name="course_code" value="{{ $courseCode }}">

                <!-- Assessment Name -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Assessment Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="assessment_name" 
                           value="{{ old('assessment_name') }}"
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
                            <option value="{{ $key }}" {{ old('assessment_type') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('assessment_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Multiple CLOs Section -->
                <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg p-5 bg-white dark:bg-gray-800 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-1">CLO Configuration</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Add multiple CLOs for this assessment</p>
                        </div>
                        <button type="button" 
                                onclick="addCloRow()"
                                class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm hover:shadow">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add CLO
                        </button>
                    </div>
                    
                    <div id="closContainer" class="space-y-4">
                        <!-- Default first CLO row -->
                        <div class="clo-row border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30">
                            <input type="hidden" name="clos[0][order]" value="0">
                            <div class="flex items-center justify-end mb-3">
                                <button type="button" 
                                        onclick="removeCloRow(this)"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hidden">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        CLO Code <span class="text-red-500">*</span>
                    </label>
                                    <select name="clos[0][clo_code]" 
                                            id="clo_code_0"
                            required
                                            onchange="updateTotalCloWeight(); updateComponentCloCodes();"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="">Select CLO</option>
                                        @foreach($cloCodes as $cloCode)
                                            <option value="{{ $cloCode }}" {{ old('clos.0.clo_code') === $cloCode ? 'selected' : '' }}>
                                                {{ $cloCode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Weight Percentage <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                                           name="clos[0][weight_percentage]" 
                                           id="weight_percentage_0"
                                           value="{{ old('clos.0.weight_percentage') }}"
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
                </div>

                    <!-- Total Weight Display -->
                    <div class="mt-5 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total CLO Weight:</span>
                            <span class="text-base font-bold text-[#003A6C] dark:text-[#0084C5]" id="totalCloWeight">0.00%</span>
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
                            <!-- Default first evaluator row -->
                            <div class="evaluator-row border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Evaluator Role <span class="text-red-500">*</span>
                    </label>
                                        <select name="evaluators[0][role]" 
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
                                               name="evaluators[0][total_score]" 
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
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hidden">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Total Evaluator Score:</span>
                            <span class="text-base font-bold text-[#003A6C] dark:text-[#0084C5]" id="totalEvaluatorScore">0.00%</span>
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
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active (Assessment will appear in evaluation forms)</span>
                    </label>
                </div>

                <!-- Rubric Configuration (for Oral/Rubric/Report types) -->
                <div id="rubricSection" class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6" style="display: none;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Rubric Configuration</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Configure components for this assessment. Total weight must equal <strong><span id="assessmentWeightDisplay">0.00</span>%</strong>.
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
                            Add Component
                        </button>
                    </div>
                    
                    <!-- Components Container -->
                    <div id="componentsContainer" class="space-y-4">
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
                <div id="logbookSection" class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6" style="display: none;">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-2">Logbook Configuration</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Configure evaluation components by months/periods or weeks for tracking student progress over time.
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
                                    <select id="logbookPeriodType" 
                                            onchange="updateLogbookPeriodType()"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="months" selected>Months/Periods</option>
                                        <option value="weeks">Weeks</option>
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
                                           value="6"
                                           onchange="generateLogbookComponents()"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="logbookPeriodTypeHelp">Total number of evaluation periods (e.g., 6 for 6 months)</p>
                                </div>
                                
                                <!-- Scale Type -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Evaluation Scale <span class="text-red-500">*</span>
                                    </label>
                                    <select id="logbookScaleType" 
                                            onchange="updateLogbookScale()"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                        <option value="1-5">1-5 Scale (Poor → Excellent)</option>
                                        <option value="1-10" selected>1-10 Scale (Poor → Excellent)</option>
                                        <option value="1-100">1-100 Scale (Percentage)</option>
                                        <option value="custom">Custom Scale</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Custom Scale Configuration -->
                            <div id="customScaleConfig" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4" style="display: none;">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Minimum Score <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="logbookScaleMin"
                                           min="0"
                                           value="1"
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
                                           value="10"
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
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300" id="logbookAutoDistributeLabel">Auto-distribute weights equally across all periods</span>
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
                                id="addLogbookComponentBtn"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span id="addLogbookComponentBtnText">Add Period Manually</span>
                        </button>
                    </div>
                    
                    <!-- Logbook Components Container -->
                    <div id="logbookComponentsContainer" class="space-y-4">
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
                        Create Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// CLO codes are fixed for the current course

let componentIndex = 0;
let assessmentWeight = 0;
const oldComponents = @json(old('components', []));
const oldLogbookComponents = @json(old('logbook_components', []));
const cloCodes = @json($cloCodes);

// CLO Management Functions
function addCloRow() {
    const container = document.getElementById('closContainer');
    const currentCount = container.querySelectorAll('.clo-row').length;
    const cloDiv = document.createElement('div');
    cloDiv.className = 'clo-row border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30';
    cloDiv.innerHTML = `
        <input type="hidden" name="clos[${currentCount}][order]" value="${currentCount}">
        <div class="flex items-center justify-end mb-3">
            <button type="button" 
                    onclick="removeCloRow(this)"
                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    CLO Code <span class="text-red-500">*</span>
                </label>
                <select name="clos[${currentCount}][clo_code]" 
                        id="clo_code_${currentCount}"
                        required
                        onchange="updateTotalCloWeight()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Select CLO</option>
                    ${cloCodes.map(clo => `<option value="${clo}">${clo}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Weight Percentage <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="clos[${currentCount}][weight_percentage]" 
                       id="weight_percentage_${currentCount}"
                       required
                       min="0"
                       max="100"
                       step="0.01"
                       placeholder="e.g. 20.00"
                       onchange="updateTotalCloWeight()"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
            </div>
        </div>
    `;
    container.appendChild(cloDiv);
    
    // Show delete buttons when there are multiple CLOs
    const totalRows = container.querySelectorAll('.clo-row').length;
    container.querySelectorAll('.clo-row').forEach((row) => {
        const deleteButton = row.querySelector('button[onclick*="removeCloRow"]');
        if (deleteButton) {
            if (totalRows > 1) {
                deleteButton.classList.remove('hidden');
            } else {
                deleteButton.classList.add('hidden');
            }
        }
    });
    
    // Add event listeners to the new row
    const newWeightInput = cloDiv.querySelector('input[name*="[weight_percentage]"]');
    const newCloSelect = cloDiv.querySelector('select[name*="[clo_code]"]');
    
    if (newWeightInput) {
        newWeightInput.addEventListener('change', function() {
            updateTotalCloWeight();
        });
    }
    if (newCloSelect) {
        newCloSelect.addEventListener('change', function() {
            updateComponentCloCodes();
            updateTotalCloWeight();
        });
    }
    
    updateTotalCloWeight();
    validateEvaluatorTotal();
}

function removeCloRow(button) {
    const container = document.getElementById('closContainer');
    if (container.querySelectorAll('.clo-row').length > 1) {
        button.closest('.clo-row').remove();
        // Renumber CLOs
        const rows = container.querySelectorAll('.clo-row');
        rows.forEach((row, index) => {
            // Update order and input names
            const orderInput = row.querySelector('input[name*="[order]"]');
            if (orderInput) {
                orderInput.value = index;
                orderInput.name = `clos[${index}][order]`;
            }
            // Update all inputs in this row
            row.querySelectorAll('input, select').forEach(input => {
                if (input.name && input.name.includes('clos[')) {
                    input.name = input.name.replace(/clos\[\d+\]/, `clos[${index}]`);
                    // Update IDs
                    if (input.id) {
                        if (input.id.includes('clo_code_')) {
                            input.id = `clo_code_${index}`;
                        } else if (input.id.includes('weight_percentage_')) {
                            input.id = `weight_percentage_${index}`;
                        }
                    }
                }
            });
        });
        
        // Show/hide delete buttons based on remaining count
        const remainingCount = container.querySelectorAll('.clo-row').length;
        container.querySelectorAll('.clo-row').forEach((row) => {
            const deleteButton = row.querySelector('button[onclick*="removeCloRow"]');
            if (deleteButton) {
                if (remainingCount > 1) {
                    deleteButton.classList.remove('hidden');
    } else {
                    deleteButton.classList.add('hidden');
                }
            }
        });
        
        updateTotalCloWeight();
        updateComponentCloCodes();
    } else {
        alert('At least one CLO is required.');
    }
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

    // Update global assessment weight target for components
    assessmentWeight = total;
    const weightDisplay = document.getElementById('assessmentWeightDisplay');
    if (weightDisplay) {
        weightDisplay.textContent = total.toFixed(2);
    }
    
    // Re-validate components against new total
    if (typeof validateComponentWeights === 'function') {
        validateComponentWeights();
    }
    
    // Update logbook weights if we are in logbook mode
    if (typeof validateLogbookWeights === 'function') {
        validateLogbookWeights();
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
        
        // Clear existing options except the first one
        select.innerHTML = '<option value="">Select CLO</option>';
        
        // Add available CLO codes
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
    componentDiv.className = 'component-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30';
    
    const availableCloCodes = getAllSelectedCloCodes();
    const defaultClo = getDefaultCloCode();
    
    // Build CLO options HTML
    const cloOptionsHtml = availableCloCodes.map(clo => 
        `<option value="${clo}" ${clo === (data?.clo_code || defaultClo) ? 'selected' : ''}>${clo}</option>`
    ).join('');
    
    componentDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">Component ${currentCount + 1}</span>
            <button type="button" onclick="removeComponent(this)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
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
        <input type="hidden" name="components[${currentCount}][order]" value="${currentCount}" class="component-order">
    `;
    container.appendChild(componentDiv);
    componentIndex++;
    validateComponentWeights();
}


function removeComponent(button) {
    if (confirm('Are you sure you want to remove this component?')) {
        button.closest('.component-item').remove();
        // Renumber components
        const components = document.querySelectorAll('.component-item');
        components.forEach((component, index) => {
            const titleSpan = component.querySelector('.text-sm.font-semibold');
            if (titleSpan) {
                titleSpan.textContent = `Component ${index + 1}`;
            }
            // Update order hidden input
            const orderInput = component.querySelector('input[name*="[order]"]');
            if (orderInput) {
                orderInput.value = index;
            }
        });
        validateComponentWeights();
    }
}

// Weight validation for components
function validateComponentWeights() {
    const weightInputs = document.querySelectorAll('#componentsContainer input[name*="components"][name*="[weight_percentage]"]');
    let total = 0;
    weightInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    updateWeightValidation(total);
}

function updateWeightValidation(total) {
    const totalSpan = document.getElementById('totalWeight');
    const statusDiv = document.getElementById('weightStatus');
    const validationDiv = document.getElementById('weightValidation');
    
    if (totalSpan) {
        totalSpan.textContent = total.toFixed(2) + '%';
    }
    
    if (statusDiv && assessmentWeight > 0) {
        if (Math.abs(total - assessmentWeight) < 0.05) {
            statusDiv.innerHTML = '<span class="text-green-600 dark:text-green-400 font-medium">✓ Total matches assessment weight</span>';
            if (validationDiv) validationDiv.className = 'mt-4 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800';
        } else {
            const difference = (assessmentWeight - total).toFixed(2);
            statusDiv.innerHTML = `<span class="text-red-600 dark:text-red-400 font-medium">⚠ Difference: ${difference}% (must equal ${assessmentWeight}%)</span>`;
            if (validationDiv) validationDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
        }
    }
}

// Default component presets for each assessment type
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
    'Report': [], // Report has no defaults - user creates custom
    'Oral': [], // Oral has no defaults - user creates custom
    'Rubric': [] // Rubric has no defaults - user creates custom
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
        
        // Auto-populate default components if container is empty and presets exist
        if (componentsContainer && defaultComponentPresets[assessmentType] && defaultComponentPresets[assessmentType].length > 0) {
            const existingComponents = componentsContainer.querySelectorAll('.component-item');
            if (existingComponents.length === 0) {
                // Clear any old components first
                componentsContainer.innerHTML = '';
                componentIndex = 0;
                
                // Get available CLO codes
                const availableCloCodes = getAllSelectedCloCodes();
                if (availableCloCodes.length > 0) {
                    // Populate default components
                    defaultComponentPresets[assessmentType].forEach(preset => {
                        // Map preset CLO to available CLO (use first available if preset CLO not found)
                        const presetClo = availableCloCodes.includes(preset.clo_code) 
                            ? preset.clo_code 
                            : availableCloCodes[0];
                        
                        addComponent({
                            component_name: preset.component_name,
                            clo_code: presetClo,
                            weight_percentage: preset.weight_percentage,
                            criteria_keywords: preset.criteria_keywords
                        });
                    });
                }
            }
        }
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize total CLO weight display
    updateTotalCloWeight();
    
    // Initialize evaluator total score
    
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
    
    // Watch for CLO weight changes
    document.querySelectorAll('#closContainer input[name*="[weight_percentage]"]').forEach(input => {
        input.addEventListener('change', function() {
            updateTotalCloWeight();
        });
    });
    
    toggleRubricSection();

    // Repopulate components if old data exists
    if (oldComponents) {
        // Handle object (if keys are indices) or array
        Object.values(oldComponents).forEach(component => {
            addComponent(component);
        });
    }
    
    // Initialize evaluator validation
    validateEvaluatorTotal();
    
    // Initialize logbook scale configuration
    updateLogbookScale();
    
    // Initialize logbook period type
    if (typeof updateLogbookPeriodType === 'function') {
        updateLogbookPeriodType();
    }
    
    // Repopulate logbook components if old data exists
    if (oldLogbookComponents && oldLogbookComponents.length > 0) {
        oldLogbookComponents.forEach(component => {
            addLogbookComponent(component);
        });
    }
});

// Evaluator Management Functions
let evaluatorIndex = 1;

function addEvaluator() {
    const container = document.getElementById('evaluatorsContainer');
    const currentCount = evaluatorIndex;
    const evaluatorDiv = document.createElement('div');
    evaluatorDiv.className = 'evaluator-row border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-800';
    
    evaluatorDiv.innerHTML = `
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

// ========== LOGBOOK COMPONENT FUNCTIONS ==========
let logbookComponentIndex = 0;

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

// Update period type UI elements
function updateLogbookPeriodType() {
    const periodType = document.getElementById('logbookPeriodType')?.value || 'months';
    const periodTypeLabel = document.getElementById('logbookPeriodTypeLabel');
    const periodTypeHelp = document.getElementById('logbookPeriodTypeHelp');
    const numberOfInput = document.getElementById('logbookNumberOfMonths');
    const addBtnText = document.getElementById('addLogbookComponentBtnText');
    const autoDistributeLabel = document.getElementById('logbookAutoDistributeLabel');
    
    if (periodType === 'weeks') {
        if (periodTypeLabel) periodTypeLabel.textContent = 'Number of Weeks';
        if (periodTypeHelp) periodTypeHelp.textContent = 'Total number of evaluation weeks (e.g., 12 for 12 weeks)';
        if (numberOfInput) {
            numberOfInput.max = '52';
            if (parseInt(numberOfInput.value) > 52) numberOfInput.value = '52';
        }
        if (addBtnText) addBtnText.textContent = 'Add Week Manually';
        if (autoDistributeLabel) autoDistributeLabel.textContent = 'Auto-distribute weights equally across all weeks';
    } else {
        if (periodTypeLabel) periodTypeLabel.textContent = 'Number of Months/Periods';
        if (periodTypeHelp) periodTypeHelp.textContent = 'Total number of evaluation periods (e.g., 6 for 6 months)';
        if (numberOfInput) {
            numberOfInput.max = '24';
            if (parseInt(numberOfInput.value) > 24) numberOfInput.value = '24';
        }
        if (addBtnText) addBtnText.textContent = 'Add Period Manually';
        if (autoDistributeLabel) autoDistributeLabel.textContent = 'Auto-distribute weights equally across all periods';
    }
}

// Generate logbook components based on number of months or weeks
function generateLogbookComponents() {
    const periodType = document.getElementById('logbookPeriodType')?.value || 'months';
    const numberOfPeriods = parseInt(document.getElementById('logbookNumberOfMonths')?.value || 6);
    const availableCloCodes = getAllSelectedCloCodes();
    
    if (availableCloCodes.length === 0) {
        alert('Please configure at least one CLO in the CLO Configuration section first.');
        return;
    }
    
    // Validate based on period type
    if (periodType === 'weeks') {
        if (numberOfPeriods < 1 || numberOfPeriods > 52) {
            alert('Number of weeks must be between 1 and 52.');
            return;
        }
    } else {
        if (numberOfPeriods < 1 || numberOfPeriods > 24) {
            alert('Number of months must be between 1 and 24.');
            return;
        }
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
    const componentDiv = document.createElement('div');
    componentDiv.className = 'logbook-component-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30';
    
    const defaultClo = getDefaultCloCode();
    const scale = getLogbookScale();
    
    // Determine period type and default label
    const periodType = document.getElementById('logbookPeriodType')?.value || 'months';
    const defaultDurationLabel = data?.duration_label || (periodType === 'weeks' ? `Week ${periodNumber}` : `Month ${periodNumber}`);
    
    // Build CLO options HTML
    const cloOptionsHtml = availableCloCodes.map(clo => 
        `<option value="${clo}" ${clo === (data?.clo_code || defaultClo) ? 'selected' : ''}>${clo}</option>`
    ).join('');
    
    // Build scale HTML
    const scaleHtml = buildLogbookScaleHtml(currentCount, scale.min, scale.max);
    
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
                       placeholder="${periodType === 'weeks' ? 'e.g. Week 1, W1, Week 1-2' : 'e.g. Month 1, M1, Week 1-4'}"
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
    
    const totalSpan = document.getElementById('logbookTotalWeight');
    const statusDiv = document.getElementById('logbookWeightStatus');
    const validationDiv = document.getElementById('logbookWeightValidation');
    
    if (totalSpan) {
        totalSpan.textContent = total.toFixed(2) + '%';
    }
    
    if (statusDiv && assessmentWeight > 0) {
        if (Math.abs(total - assessmentWeight) < 0.01) {
            statusDiv.innerHTML = '<span class="text-[#0084C5] dark:text-[#0084C5] font-medium">✓ Total matches assessment weight</span>';
            if (validationDiv) validationDiv.className = 'mt-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600';
        } else {
            const difference = (assessmentWeight - total).toFixed(2);
            statusDiv.innerHTML = `<span class="text-red-600 dark:text-red-400 font-medium">⚠ Difference: ${difference}% (must equal ${assessmentWeight}%)</span>`;
            if (validationDiv) validationDiv.className = 'mt-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
        }
    }
}
</script>
@endsection

