@extends('layouts.app')

@section('title', 'Edit Rubric Template - ' . $rubric->name)

@section('content')
<div class="py-6" x-data="rubricEditor()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('academic.fyp.rubrics.index') }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Rubric Templates
            </a>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Template Info (Left Sidebar) -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Template Settings</h2>
                    
                    <form action="{{ route('academic.fyp.rubrics.update', $rubric) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $rubric->name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                            </div>

                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $rubric->code) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="assessment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                    <select name="assessment_type" id="assessment_type"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                                        @foreach($assessmentTypes as $value => $label)
                                            <option value="{{ $value }}" {{ $rubric->assessment_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="phase" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phase</label>
                                    <select name="phase" id="phase"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                                        @foreach($phases as $value => $label)
                                            <option value="{{ $value }}" {{ $rubric->phase == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" id="description" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">{{ old('description', $rubric->description) }}</textarea>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $rubric->is_active ? 'checked' : '' }}
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                                Update Template
                            </button>
                        </div>
                    </form>

                    <!-- Weight Summary -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weight Summary</h3>
                        <div class="p-3 rounded-lg {{ $isWeightValid ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }}">
                            <div class="text-2xl font-bold {{ $isWeightValid ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ number_format($totalWeight, 2) }}%
                            </div>
                            <div class="text-sm {{ $isWeightValid ? 'text-green-600' : 'text-yellow-600' }}">
                                @if($isWeightValid)
                                    ✓ Perfect (100%)
                                @elseif($totalWeight > 100)
                                    ⚠️ Exceeds 100%
                                @else
                                    ⚠️ Below 100% ({{ number_format(100 - $totalWeight, 2) }}% remaining)
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Elements (Main Content) -->
            <div class="lg:col-span-2">
                <!-- Add Element Form -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Add New Element</h2>
                    
                    <form action="{{ route('academic.fyp.rubrics.add-element', $rubric) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                                <input type="text" name="element_code" value="{{ old('element_code') }}" placeholder="E1"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Element Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Problem Statement"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Weight %</label>
                                <input type="number" name="weight_percentage" value="{{ old('weight_percentage') }}" step="0.01" min="0" max="100" placeholder="10.00"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CLO</label>
                                <select name="clo_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                                    @foreach($cloCodes as $clo)
                                        <option value="{{ $clo }}" {{ old('clo_code') == $clo ? 'selected' : '' }}>{{ $clo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                                <input type="text" name="description" value="{{ old('description') }}" placeholder="Brief description of what's being assessed"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                            </div>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Element
                        </button>
                    </form>
                </div>

                <!-- Existing Elements -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">
                            Rubric Elements ({{ $rubric->elements->count() }})
                        </h2>
                    </div>

                    @if($rubric->elements->count() > 0)
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($rubric->elements as $element)
                                <div class="p-4" x-data="{ editing: false, showDescriptors: false }">
                                    <!-- Element Header -->
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-mono rounded">
                                                    {{ $element->element_code }}
                                                </span>
                                                <span class="px-2 py-1 bg-[#0084C5]/10 text-[#0084C5] text-xs font-medium rounded">
                                                    {{ $element->clo_code }}
                                                </span>
                                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 text-xs font-medium rounded">
                                                    {{ number_format($element->weight_percentage, 2) }}%
                                                </span>
                                            </div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-200">{{ $element->name }}</h3>
                                            @if($element->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $element->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 ml-4">
                                            <button @click="showDescriptors = !showDescriptors" 
                                                    class="text-[#0084C5] hover:text-[#003A6C] text-sm font-medium">
                                                <span x-text="showDescriptors ? 'Hide Levels' : 'Edit Levels'"></span>
                                            </button>
                                            <button @click="editing = !editing" 
                                                    class="text-yellow-600 hover:text-yellow-800 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('academic.fyp.rubrics.delete-element', [$rubric, $element]) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this element?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Element Form -->
                                    <div x-show="editing" x-collapse class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <form action="{{ route('academic.fyp.rubrics.update-element', [$rubric, $element]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
                                                    <input type="text" name="element_code" value="{{ $element->element_code }}"
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                                    <input type="text" name="name" value="{{ $element->name }}"
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Weight %</label>
                                                    <input type="number" name="weight_percentage" value="{{ $element->weight_percentage }}" step="0.01" min="0" max="100"
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]" required>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CLO</label>
                                                    <select name="clo_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                                                        @foreach($cloCodes as $clo)
                                                            <option value="{{ $clo }}" {{ $element->clo_code == $clo ? 'selected' : '' }}>{{ $clo }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="md:col-span-3">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                                    <input type="text" name="description" value="{{ $element->description }}"
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <button type="submit" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                                                    Save Changes
                                                </button>
                                                <button type="button" @click="editing = false" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Level Descriptors -->
                                    <div x-show="showDescriptors" x-collapse class="mt-4">
                                        <form action="{{ route('academic.fyp.rubrics.update-descriptors', [$rubric, $element]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Level</th>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Label</th>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Score</th>
                                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descriptor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                        @foreach($performanceLevels as $level => $defaultLabel)
                                                            @php
                                                                $descriptor = $element->levelDescriptors->firstWhere('level', $level);
                                                            @endphp
                                                            <tr class="@if($level == 1) bg-red-50 dark:bg-red-900/10
                                                                       @elseif($level == 2) bg-orange-50 dark:bg-orange-900/10
                                                                       @elseif($level == 3) bg-yellow-50 dark:bg-yellow-900/10
                                                                       @elseif($level == 4) bg-blue-50 dark:bg-blue-900/10
                                                                       @else bg-green-50 dark:bg-green-900/10
                                                                       @endif">
                                                                <td class="px-3 py-2">
                                                                    <input type="hidden" name="descriptors[{{ $level }}][level]" value="{{ $level }}">
                                                                    <span class="font-medium text-gray-900 dark:text-gray-200">{{ $level }}</span>
                                                                </td>
                                                                <td class="px-3 py-2">
                                                                    <input type="text" name="descriptors[{{ $level }}][label]" 
                                                                           value="{{ $descriptor?->label ?? $defaultLabel }}"
                                                                           class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded focus:outline-none focus:ring-1 focus:ring-[#0084C5]" required>
                                                                </td>
                                                                <td class="px-3 py-2">
                                                                    <input type="number" name="descriptors[{{ $level }}][score_value]" 
                                                                           value="{{ $descriptor?->score_value ?? $level }}" step="0.1" min="0" max="10"
                                                                           class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded focus:outline-none focus:ring-1 focus:ring-[#0084C5]" required>
                                                                </td>
                                                                <td class="px-3 py-2">
                                                                    <textarea name="descriptors[{{ $level }}][descriptor]" rows="2"
                                                                              class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded focus:outline-none focus:ring-1 focus:ring-[#0084C5]" required>{{ $descriptor?->descriptor ?? "Performance level {$level} - {$defaultLabel}" }}</textarea>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="mt-4 flex items-center gap-2">
                                                <button type="submit" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                                                    Save Descriptors
                                                </button>
                                                <button type="button" @click="showDescriptors = false" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                                                    Close
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-2">No Elements Yet</h3>
                            <p class="text-gray-500 dark:text-gray-400">Add elements above to define the rubric criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function rubricEditor() {
    return {
        // Add any Alpine.js functionality here
    }
}
</script>
@endsection
