@extends('layouts.app')

@section('title', 'Reference Sample Details')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Reference Sample Details</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">View reference sample information</p>
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

        <!-- Sample Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Header with Icon and Title -->
                <div class="flex items-start gap-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="text-5xl">{{ $referenceSample->category_icon }}</div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">
                            {{ $referenceSample->title }}
                        </h2>
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="px-3 py-1 text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                                {{ $referenceSample->category_display }}
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $referenceSample->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                {{ $referenceSample->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($referenceSample->description)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $referenceSample->description }}</p>
                </div>
                @endif

                <!-- File Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">File Information</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">File Name:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $referenceSample->file_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">File Size:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $referenceSample->file_size_formatted }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">File Type:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200 uppercase">{{ $referenceSample->file_extension }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Downloads:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $referenceSample->download_count }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Upload Information</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Uploaded By:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $referenceSample->uploader->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Upload Date:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $referenceSample->created_at->format('d M Y, h:i A') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Last Updated:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $referenceSample->updated_at->format('d M Y, h:i A') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Display Order:</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $referenceSample->display_order }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('reference-samples.download', $referenceSample) }}"
                       class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download File
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->hasRole('coordinator'))
                    <a href="{{ route('reference-samples.edit', $referenceSample) }}"
                       class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>

                    <form action="{{ route('reference-samples.destroy', $referenceSample) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this sample? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
