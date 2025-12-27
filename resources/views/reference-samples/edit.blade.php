@extends('layouts.app')

@section('title', 'Edit Reference Sample')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Edit Reference Sample</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Update reference sample details</p>
                </div>
                <a href="{{ route('reference-samples.index') }}"
                   class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Please fix the following errors:</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Current File Info -->
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-semibold mb-1">Current File:</p>
                    <p>{{ $referenceSample->file_name }} ({{ $referenceSample->file_size_formatted }})</p>
                    <p class="text-xs mt-1">Leave file upload empty to keep the current file</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <form action="{{ route('reference-samples.update', $referenceSample) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  x-data="{
                      fileName: '',
                      fileSize: 0,
                      previewUrl: null,
                      category: '{{ old('category', $referenceSample->category) }}'
                  }">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title', $referenceSample->title) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"
                               placeholder="e.g., Professional Resume Template">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category"
                                name="category"
                                x-model="category"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                            <option value="resume">Resume</option>
                            <option value="poster">Poster</option>
                            <option value="achievement">Achievement</option>
                            <option value="other">Other</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"
                                  placeholder="Brief description of this reference sample...">{{ old('description', $referenceSample->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload (Optional for edit) -->
                    <div>
                        <label for="file" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Replace File (Optional)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-[#0084C5] transition-colors">
                            <div class="space-y-2 text-center w-full">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                    <label for="file" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-[#0084C5] hover:text-[#003A6C] focus-within:outline-none px-2">
                                        <span>Upload a new file</span>
                                        <input id="file"
                                               name="file"
                                               type="file"
                                               accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png"
                                               class="sr-only"
                                               @change="
                                                   fileName = $event.target.files[0]?.name || '';
                                                   fileSize = ($event.target.files[0]?.size / 1024 / 1024).toFixed(2) || 0;
                                                   if ($event.target.files[0]?.type.startsWith('image/')) {
                                                       previewUrl = URL.createObjectURL($event.target.files[0]);
                                                   }
                                               ">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    PDF, DOC, DOCX, PPT, PPTX, JPG, PNG up to 10MB
                                </p>
                                <div x-show="fileName" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <p class="text-sm text-blue-700 dark:text-blue-300 font-medium" x-text="fileName"></p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1" x-text="fileSize + ' MB'"></p>
                                </div>
                                <div x-show="previewUrl" class="mt-4">
                                    <img :src="previewUrl" class="mx-auto max-h-48 rounded-lg" alt="Preview">
                                </div>
                            </div>
                        </div>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Order -->
                    <div>
                        <label for="display_order" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Display Order
                        </label>
                        <input type="number"
                               id="display_order"
                               name="display_order"
                               value="{{ old('display_order', $referenceSample->display_order) }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                        @error('display_order')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="is_active"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $referenceSample->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                        <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Active (visible to students)
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end gap-3">
                    <a href="{{ route('reference-samples.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Sample
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
