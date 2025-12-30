@extends('layouts.app')

@section('title', 'Template SAL - Documents')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Template SAL</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Student Application Letter template for industrial placement applications</p>
    </div>

    <!-- Template Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Student Application Letter (SAL)</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Official letter issued to students for applying to companies</p>
                    <div class="flex items-center gap-4 mt-3">
                        <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full">Active Template</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Template file: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">placement/pdf/sal.blade.php</code></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Variables -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Template Variables</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">The following variables are automatically populated when generating SAL:</p>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="text-left px-4 py-2 text-gray-700 dark:text-gray-300 font-medium">Variable</th>
                        <th class="text-left px-4 py-2 text-gray-700 dark:text-gray-300 font-medium">Description</th>
                        <th class="text-left px-4 py-2 text-gray-700 dark:text-gray-300 font-medium">Example</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-2"><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-blue-600 dark:text-blue-400">$student->name</code></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Student's full name</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-500">{{ $sampleStudent->name ?? 'John Doe' }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2"><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-blue-600 dark:text-blue-400">$student->matric_no</code></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Student's matric number</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-500">{{ $sampleStudent->matric_no ?? 'TM210001' }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2"><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-blue-600 dark:text-blue-400">$student->program</code></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Student's program</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-500">{{ $sampleStudent->program ?? 'Bachelor of Technology' }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2"><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-blue-600 dark:text-blue-400">$student->ic_no</code></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Student's IC number</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-500">{{ $sampleStudent->ic_no ?? '010101-01-0001' }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2"><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-blue-600 dark:text-blue-400">$wblDuration</code></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">WBL training duration</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-500">6 months</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2"><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-blue-600 dark:text-blue-400">$generatedAt</code></td>
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Date of letter generation</td>
                        <td class="px-4 py-2 text-gray-500 dark:text-gray-500">{{ now()->format('d F Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.documents.sal.designer') }}"
               class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Template
            </a>
            <a href="{{ route('admin.documents.sal.preview') }}" target="_blank"
               class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Preview Template
            </a>
            <a href="{{ route('placement.index') }}"
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Release SAL to Students
            </a>
        </div>
    </div>
</div>
@endsection
