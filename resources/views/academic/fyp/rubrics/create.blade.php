@extends('layouts.app')

@section('title', 'Create Rubric Template')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-6">Create Rubric Template</h1>

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('academic.fyp.rubrics.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Template Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5]"
                               placeholder="e.g., Mid-Term Written Report Rubric"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Template Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="code" 
                               id="code" 
                               value="{{ old('code') }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5]"
                               placeholder="e.g., FYP_MID_WRITTEN"
                               required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">A unique identifier for this template (uppercase, underscores allowed)</p>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type and Phase -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="assessment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Assessment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="assessment_type" 
                                    id="assessment_type"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]"
                                    required>
                                <option value="">Select Type</option>
                                @foreach($assessmentTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('assessment_type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assessment_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phase" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Phase <span class="text-red-500">*</span>
                            </label>
                            <select name="phase" 
                                    id="phase"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]"
                                    required>
                                <option value="">Select Phase</option>
                                @foreach($phases as $value => $label)
                                    <option value="{{ $value }}" {{ old('phase') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('phase')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5]"
                                  placeholder="Optional description of this rubric template">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                        <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Active (template can be used for evaluations)
                        </label>
                    </div>

                    <!-- Performance Levels Info -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-2">Performance Levels</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mb-3">
                            Each rubric element will use these 5 fixed performance levels:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($performanceLevels as $level => $label)
                                <span class="px-3 py-1 text-sm font-medium rounded
                                    @if($level == 1) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @elseif($level == 2) bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300
                                    @elseif($level == 3) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($level == 4) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @endif">
                                    {{ $level }} - {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex items-center justify-end gap-4">
                    <a href="{{ route('academic.fyp.rubrics.index') }}" 
                       class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Create & Add Elements
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
