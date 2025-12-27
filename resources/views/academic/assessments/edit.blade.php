@extends('layouts.app')

@section('title', $courseName . ' – Edit Assessment')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('academic.ppe.assessments.index') }}" 
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
            <form action="{{ route('academic.ppe.assessments.update', $assessment) }}" method="POST">
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
                            required
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

                <!-- CLO Code -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        CLO Code <span class="text-red-500">*</span>
                    </label>
                    <select name="clo_code" 
                            id="clo_code"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        @foreach($cloCodes as $clo)
                            <option value="{{ $clo }}" {{ old('clo_code', $assessment->clo_code) === $clo ? 'selected' : '' }}>
                                {{ $clo }}
                            </option>
                        @endforeach
                    </select>
                    @error('clo_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Weight Percentage -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Weight Percentage <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="weight_percentage" 
                           value="{{ old('weight_percentage', $assessment->weight_percentage) }}"
                           required
                           min="0"
                           max="100"
                           step="0.01"
                           placeholder="e.g. 20.00"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the percentage weight (0-100)</p>
                    @error('weight_percentage')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Evaluator Role -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Evaluator Role <span class="text-red-500">*</span>
                    </label>
                    <select name="evaluator_role" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">Select Evaluator</option>
                        @foreach($evaluatorRoles as $key => $label)
                            <option value="{{ $key }}" {{ old('evaluator_role', $assessment->evaluator_role) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('evaluator_role')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
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

                <!-- Question Configuration (for Oral/Rubric types) -->
                <div id="rubricSection" class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6" style="display: {{ in_array($assessment->assessment_type, ['Oral', 'Rubric']) ? 'block' : 'none' }};">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Rubric Questions Configuration</h3>
                        <button type="button" 
                                onclick="addRubricQuestion()"
                                class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Question
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Define rubric questions for this assessment. Total weight of all questions must equal <strong>{{ number_format($assessment->weight_percentage, 2) }}%</strong>.
                    </p>
                    
                    <div id="rubricsContainer" class="space-y-4">
                        @if($rubrics && $rubrics->count() > 0)
                            @foreach($rubrics as $index => $rubric)
                                <div class="rubric-question border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50" draggable="false">
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
                    
                    <div class="mt-4 p-3 rounded-lg" id="rubricWeightValidation">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Total Rubric Weight:</span>
                            <span class="font-bold" id="totalRubricWeight">0.00%</span>
                        </div>
                        <div class="mt-2 text-xs" id="rubricWeightStatus"></div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('academic.ppe.assessments.index') }}" 
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

<script>
// CLO codes are fixed for the current course

let rubricIndex = {{ $rubrics ? $rubrics->count() : 0 }};
const assessmentWeight = {{ $assessment->weight_percentage }};
const cloCodes = @json($cloCodes);

function addRubricQuestion() {
    const container = document.getElementById('rubricsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'rubric-question border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50';
    questionDiv.setAttribute('draggable', 'false'); // Reordering disabled for rubrics
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
    const weightInputs = document.querySelectorAll('input[name*="[weight_percentage]"]');
    let total = 0;
    weightInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    const totalSpan = document.getElementById('totalRubricWeight');
    const statusDiv = document.getElementById('rubricWeightStatus');
    const validationDiv = document.getElementById('rubricWeightValidation');
    
    if (totalSpan) {
        totalSpan.textContent = total.toFixed(2) + '%';
    }
    
    if (statusDiv) {
        if (Math.abs(total - assessmentWeight) < 0.01) {
            statusDiv.innerHTML = '<span class="text-green-600 dark:text-green-400">✓ Total matches assessment weight</span>';
            validationDiv.className = 'mt-4 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800';
        } else {
            const difference = (assessmentWeight - total).toFixed(2);
            statusDiv.innerHTML = `<span class="text-red-600 dark:text-red-400">⚠ Difference: ${difference}% (must equal ${assessmentWeight}%)</span>`;
            validationDiv.className = 'mt-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
        }
    }
}

function toggleRubricSection() {
    const assessmentType = document.getElementById('assessment_type').value;
    const rubricSection = document.querySelector('.border-t.border-gray-200');
    if (rubricSection) {
        if (assessmentType === 'Oral' || assessmentType === 'Rubric') {
            rubricSection.style.display = 'block';
        } else {
            rubricSection.style.display = 'none';
        }
    }
}

// Initialize validation on page load
document.addEventListener('DOMContentLoaded', function() {
    validateRubricWeights();
    toggleRubricSection();
});
</script>
@endsection

