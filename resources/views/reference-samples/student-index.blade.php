@extends('layouts.app')

@section('title', 'Reference Samples')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Reference Samples</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Download example files for resume, poster, and achievements</p>
        </div>

        <!-- Info Banner -->
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-semibold mb-1">How to use these samples:</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        <li>Click on any sample to download it</li>
                        <li>Use these as templates or references for your own work</li>
                        <li>Customize the content to match your own information and achievements</li>
                    </ul>
                </div>
            </div>
        </div>

        @if($samples->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No Reference Samples Available</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Check back later for example files</p>
        </div>
        @else
            @foreach($samples as $category => $categoryItems)
            <div class="mb-8">
                <!-- Category Header -->
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-3xl">
                        @if($category === 'resume') 📄
                        @elseif($category === 'poster') 🖼️
                        @elseif($category === 'achievement') 🏆
                        @else 📁
                        @endif
                    </span>
                    <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                        {{ ucfirst($category) }} Templates
                    </h2>
                    <span class="px-2 py-1 text-xs font-semibold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                        {{ $categoryItems->count() }} {{ Str::plural('sample', $categoryItems->count()) }}
                    </span>
                </div>

                <!-- Samples Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categoryItems as $sample)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <!-- Card Header -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-[#003A6C] dark:text-[#0084C5] mb-2">
                                {{ $sample->title }}
                            </h3>
                            @if($sample->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                {{ $sample->description }}
                            </p>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50">
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">File:</span>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium">{{ $sample->file_name }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Size:</span>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium">{{ $sample->file_size_formatted }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Type:</span>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium uppercase">{{ $sample->file_extension }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Downloads:</span>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium">{{ $sample->download_count }}</span>
                                </div>
                            </div>

                            <!-- Download Button -->
                            <a href="{{ route('reference-samples.download', $sample) }}"
                               class="w-full px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download
                            </a>
                        </div>

                        <!-- Card Footer -->
                        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                Uploaded {{ $sample->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @endif

        <!-- Help Section -->
        <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-sm text-yellow-700 dark:text-yellow-300">
                    <p class="font-semibold mb-1">Important Notice:</p>
                    <p>These samples are provided as references only. Please customize them with your own information and achievements. Do not copy content directly - use them as a guide for formatting and structure.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
