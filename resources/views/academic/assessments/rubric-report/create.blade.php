@extends('layouts.app')

@section('title', 'Create Rubric Report - ' . $assessment->assessment_name)

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.index') }}"
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Assessments
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Create Rubric Report</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $courseName }} - {{ $assessment->assessment_name }}</p>
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

        @if (session('info'))
            <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700">
                {{ session('info') }}
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700 lg:p-8">
            <!-- Input Type Selection -->
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                    Select Input Type <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none transition-all input-type-option" id="manual-option">
                        <input type="radio" name="input_type" value="manual" class="sr-only" checked onchange="toggleInputType()">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">Manual Fill Up</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    Create elements and rating descriptors through a form
                                </span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-[#0084C5] check-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none transition-all input-type-option" id="file-option">
                        <input type="radio" name="input_type" value="file" class="sr-only" onchange="toggleInputType()">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900 dark:text-white">Upload File</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    Upload a PDF/Excel file containing the rubric form
                                </span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-[#0084C5] check-icon hidden" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </label>
                </div>
            </div>

            <!-- Manual Form -->
            <form action="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.rubric-report.store', $assessment) }}" method="POST" enctype="multipart/form-data" id="rubric-form">
                @csrf
                <input type="hidden" name="input_type" id="hidden-input-type" value="manual">

                <!-- Manual Input Section -->
                <div id="manual-section">
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Rubric Elements</h3>
                            <button type="button" onclick="addElement()"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-[#0084C5] hover:bg-[#003A6C] rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Element
                            </button>
                        </div>

                        <div id="elements-container">
                            <!-- Elements will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div id="file-section" class="hidden">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Upload Rubric Form <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-[#0084C5] hover:text-[#003A6C] focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input type="file" name="file" class="sr-only" accept=".pdf,.xlsx,.xls,.doc,.docx" onchange="showFileName(this)">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PDF, XLSX, XLS, DOC, DOCX up to 10MB</p>
                                <p id="file-name" class="text-sm font-medium text-[#0084C5] mt-2 hidden"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-[#0084C5] hover:bg-[#003A6C] rounded-lg transition-colors">
                        Save Rubric Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Element Template -->
<template id="element-template">
    <div class="element-item bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4 border border-gray-200 dark:border-gray-600">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-medium text-gray-700 dark:text-gray-300">Element <span class="element-number">1</span></h4>
            <button type="button" onclick="removeElement(this)" class="text-red-500 hover:text-red-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Element Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="elements[INDEX][element_name]" required placeholder="e.g., Problem Statement"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Weight (%)
                </label>
                <input type="number" name="elements[INDEX][weight_percentage]" step="0.01" min="0" max="100" placeholder="e.g., 10"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Criteria Keywords
            </label>
            <textarea name="elements[INDEX][criteria_keywords]" rows="2" placeholder="e.g., Clarity and Conciseness, Relevance, Specificity..."
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white"></textarea>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Rating Descriptors <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-3">
                @foreach($ratingLevels as $level => $info)
                    <div class="rating-column">
                        <div class="text-center mb-2">
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                @if($info['color'] === 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($info['color'] === 'orange') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                @elseif($info['color'] === 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($info['color'] === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($info['color'] === 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @endif">
                                {{ $info['label'] }}
                            </span>
                        </div>
                        <textarea name="elements[INDEX][descriptors][{{ $level - 1 }}][descriptor]" required rows="3" placeholder="Enter {{ $info['label'] }} descriptor..."
                                  class="w-full px-2 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white"></textarea>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
    let elementCount = 0;

    function toggleInputType() {
        const manualRadio = document.querySelector('input[name="input_type"][value="manual"]');
        const manualOption = document.getElementById('manual-option');
        const fileOption = document.getElementById('file-option');
        const manualSection = document.getElementById('manual-section');
        const fileSection = document.getElementById('file-section');
        const hiddenInput = document.getElementById('hidden-input-type');

        if (manualRadio.checked) {
            manualOption.classList.add('border-[#0084C5]', 'bg-[#0084C5]/5');
            manualOption.classList.remove('border-gray-300', 'dark:border-gray-600');
            manualOption.querySelector('.check-icon').classList.remove('hidden');

            fileOption.classList.remove('border-[#0084C5]', 'bg-[#0084C5]/5');
            fileOption.classList.add('border-gray-300', 'dark:border-gray-600');
            fileOption.querySelector('.check-icon').classList.add('hidden');

            manualSection.classList.remove('hidden');
            fileSection.classList.add('hidden');
            hiddenInput.value = 'manual';
        } else {
            fileOption.classList.add('border-[#0084C5]', 'bg-[#0084C5]/5');
            fileOption.classList.remove('border-gray-300', 'dark:border-gray-600');
            fileOption.querySelector('.check-icon').classList.remove('hidden');

            manualOption.classList.remove('border-[#0084C5]', 'bg-[#0084C5]/5');
            manualOption.classList.add('border-gray-300', 'dark:border-gray-600');
            manualOption.querySelector('.check-icon').classList.add('hidden');

            fileSection.classList.remove('hidden');
            manualSection.classList.add('hidden');
            hiddenInput.value = 'file';
        }
    }

    function addElement() {
        const template = document.getElementById('element-template');
        const container = document.getElementById('elements-container');
        const clone = template.content.cloneNode(true);

        // Update INDEX placeholders
        clone.querySelectorAll('[name*="[INDEX]"]').forEach(el => {
            el.name = el.name.replace('INDEX', elementCount);
        });

        // Update element number
        clone.querySelector('.element-number').textContent = elementCount + 1;

        container.appendChild(clone);
        elementCount++;
        updateElementNumbers();
    }

    function removeElement(button) {
        const element = button.closest('.element-item');
        element.remove();
        updateElementNumbers();
    }

    function updateElementNumbers() {
        const elements = document.querySelectorAll('.element-item');
        elements.forEach((el, index) => {
            el.querySelector('.element-number').textContent = index + 1;
        });
    }

    function showFileName(input) {
        const fileNameEl = document.getElementById('file-name');
        if (input.files.length > 0) {
            fileNameEl.textContent = 'Selected: ' + input.files[0].name;
            fileNameEl.classList.remove('hidden');
        } else {
            fileNameEl.classList.add('hidden');
        }
    }

    // Initialize with one element
    document.addEventListener('DOMContentLoaded', function() {
        toggleInputType();
        addElement();
    });
</script>
@endpush
@endsection
