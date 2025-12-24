@extends('layouts.app')

@section('title', 'Reference Samples')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#003A6C] to-[#0084C5] flex items-center justify-center">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Reference Samples</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Download sample files to guide your work</p>
        </div>
    </div>

    <!-- Reference Samples by Category -->
    @if($samples->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No reference samples available</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Reference samples will appear here when uploaded by coordinators.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($samples as $category => $categorySamples)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <!-- Category Header -->
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">
                                @if($category === 'resume') 📄
                                @elseif($category === 'poster') 🖼️
                                @elseif($category === 'achievement') 🏆
                                @else 📁
                                @endif
                            </span>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    @if($category === 'resume') Resume Samples
                                    @elseif($category === 'poster') Poster Samples
                                    @elseif($category === 'achievement') Achievement Samples
                                    @else Other Samples
                                    @endif
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $categorySamples->count() }} {{ Str::plural('sample', $categorySamples->count()) }} available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sample List -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($categorySamples as $sample)
                            <div class="group hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <a href="{{ route('reference-samples.download', $sample) }}" class="flex items-center justify-between px-6 py-4">
                                    <div class="flex items-center gap-4 flex-1">
                                        <!-- File Icon -->
                                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-br from-[#003A6C] to-[#0084C5] flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>

                                        <!-- Sample Info -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-[#0084C5] dark:group-hover:text-[#00AEEF] transition-colors">
                                                {{ $sample->title }}
                                            </h3>
                                            @if($sample->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $sample->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-2">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="font-medium">{{ strtoupper($sample->file_extension) }}</span> • {{ $sample->file_size_formatted }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    <svg class="w-3 h-3 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    {{ $sample->download_count }} downloads
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Download Button -->
                                    <div class="flex-shrink-0 ml-4">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-600 group-hover:bg-[#0084C5] dark:group-hover:bg-[#0084C5] flex items-center justify-center transition-colors">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Warning Notice -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-l-4 border-amber-500 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200">Important Notice</h4>
                        <p class="text-sm text-amber-800 dark:text-amber-300 mt-1">
                            <strong>For reference only. Do NOT copy directly.</strong>
                            <br>
                            These samples are provided as guidelines to help you understand the structure and format. Your work must be original and reflect your own achievements and experiences.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Tips -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200">Tips for Using These Samples</h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-300 mt-2 space-y-1 list-disc list-inside">
                            <li>Study the structure and organization of the samples</li>
                            <li>Adapt the format to showcase your unique experiences</li>
                            <li>Ensure all information is accurate and truthful</li>
                            <li>Proofread carefully before submission</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
