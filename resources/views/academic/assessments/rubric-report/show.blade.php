@extends('layouts.app')

@section('title', 'Rubric Report - ' . $assessment->assessment_name)

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
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Assessment Rubric Report</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $courseName }} - {{ $assessment->assessment_name }}</p>
                </div>
                <div class="flex items-center gap-2 mt-4 md:mt-0">
                    @if($rubricReport->isManualInput())
                        <a href="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.rubric-report.export-pdf', $assessment) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Export PDF
                        </a>
                    @else
                        <a href="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.rubric-report.download', $assessment) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download File
                        </a>
                    @endif
                    <a href="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.rubric-report.edit', $assessment) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#0084C5] hover:bg-[#003A6C] rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
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

        <!-- Report Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Input Type</span>
                    <p class="font-medium text-gray-900 dark:text-white">
                        @if($rubricReport->isManualInput())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Manual Fill Up
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                File Upload
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Created By</span>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $rubricReport->creator->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Created At</span>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $rubricReport->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>

            @if($rubricReport->isFileUpload())
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">File Name</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $rubricReport->file_name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">File Size</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ number_format($rubricReport->file_size / 1024, 2) }} KB</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Uploaded By</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $rubricReport->uploader->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($rubricReport->isManualInput())
            <!-- Rubric Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-[#003A6C] text-left text-xs font-semibold text-white uppercase tracking-wider w-1/6">
                                    Element
                                </th>
                                @foreach($ratingLevels as $level => $info)
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider w-1/6
                                        @if($info['color'] === 'red') bg-red-500 text-white
                                        @elseif($info['color'] === 'orange') bg-orange-500 text-white
                                        @elseif($info['color'] === 'yellow') bg-yellow-400 text-gray-900
                                        @elseif($info['color'] === 'blue') bg-blue-500 text-white
                                        @elseif($info['color'] === 'green') bg-green-500 text-white
                                        @endif">
                                        {{ $info['label'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($rubricReport->elements as $element)
                                <tr>
                                    <td class="px-4 py-4 align-top">
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $loop->iteration }}. {{ strtoupper($element->element_name) }}
                                        </div>
                                        @if($element->criteria_keywords)
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic">
                                                {{ $element->criteria_keywords }}
                                            </div>
                                        @endif
                                        @if($element->weight_percentage)
                                            <div class="mt-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    Weight: {{ number_format($element->weight_percentage, 1) }}%
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    @foreach($ratingLevels as $level => $info)
                                        @php
                                            $descriptor = $element->descriptors->where('level', $level)->first();
                                        @endphp
                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 align-top
                                            @if($info['color'] === 'red') bg-red-50 dark:bg-red-900/20
                                            @elseif($info['color'] === 'orange') bg-orange-50 dark:bg-orange-900/20
                                            @elseif($info['color'] === 'yellow') bg-yellow-50 dark:bg-yellow-900/20
                                            @elseif($info['color'] === 'blue') bg-blue-50 dark:bg-blue-900/20
                                            @elseif($info['color'] === 'green') bg-green-50 dark:bg-green-900/20
                                            @endif">
                                            {{ $descriptor->descriptor ?? '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No elements defined for this rubric report.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- File Preview Message -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 border border-gray-200 dark:border-gray-700 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">File Uploaded</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">
                    The rubric form has been uploaded as a file. Click the download button above to view it.
                </p>
                <div class="mt-4">
                    <a href="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.rubric-report.download', $assessment) }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download {{ $rubricReport->file_name }}
                    </a>
                </div>
            </div>
        @endif

        <!-- Delete Button -->
        <div class="mt-6 flex justify-end">
            <form action="{{ route('academic.' . strtolower($assessment->course_code) . '.assessments.rubric-report.destroy', $assessment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this rubric report? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Rubric Report
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
