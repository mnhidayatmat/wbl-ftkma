@extends('layouts.app')

@section('title', 'Template SCL - Documents')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Template SCL</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Student Confirmation Letter template for confirmed industrial placements</p>
    </div>

    <!-- Template Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Student Confirmation Letter (SCL)</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Official letter confirming student's placement at a company</p>
                    <div class="flex items-center gap-3 mt-3">
                        @if($template->word_template_path)
                        <span class="inline-flex items-center gap-1.5 text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2.5 py-1 rounded-full font-medium">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Template Uploaded
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 text-xs bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 px-2.5 py-1 rounded-full font-medium">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                            No Template
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.documents.scl.word-template') }}"
                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ $template->word_template_path ? 'Manage Template' : 'Upload Template' }}
                </a>
                @if($template->word_template_path)
                <a href="{{ route('admin.documents.scl.word-template.preview-docx') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Preview
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Auto-populated Variables -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Auto-populated Variables</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Automatically filled from student & placement data</p>
                </div>
            </div>

            <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                <!-- Student Name -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Student Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ student_name }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Student Matric -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Student Matric</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ student_matric }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Company Name -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Company Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ company_name }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Company Address -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Company Address</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ company_address }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- HR Name -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">HR/PIC Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ hr_name }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Group Start Date -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">WBL Start Date</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ group_start_date }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Group End Date -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">WBL End Date</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ group_end_date }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Academic Tutor -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Academic Tutor (AT)</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ academic_tutor_name }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Industry Coach -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Industry Coach (IC)</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">@{{ industry_coach_name }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>
            </div>
        </div>

        <!-- Manual Input Variables -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Manual Input Variables</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">To be filled by Coordinator/Admin</p>
                </div>
            </div>

            <form action="{{ route('admin.documents.scl.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                @if($errors->any())
                <div class="p-3 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                    <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- SCL Release Date -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="scl_release_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            SCL Release Date <span class="text-red-500">*</span>
                        </label>
                        <button type="button" onclick="document.getElementById('scl_release_date').value=''" class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="date" name="scl_release_date" id="scl_release_date"
                               value="{{ $template->settings['scl_release_date'] ?? '' }}"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-purple-600 dark:text-purple-400">@{{ scl_release_date }}</code></p>
                </div>

                <!-- SCL Reference Number -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="scl_reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            SCL Reference Number <span class="text-red-500">*</span>
                        </label>
                        <button type="button" onclick="document.getElementById('scl_reference_number').value=''" class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                        <input type="text" name="scl_reference_number" id="scl_reference_number"
                               value="{{ $template->settings['scl_reference_number'] ?? '' }}"
                               placeholder="e.g., UMPSA/PKU/SCL/2025/001"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-purple-600 dark:text-purple-400">@{{ scl_reference_number }}</code></p>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Director of UMPSA Career Centre</p>
                </div>

                <!-- Director Name -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="scl_director_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Director Name
                        </label>
                        <button type="button" onclick="document.getElementById('scl_director_name').value=''" class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="scl_director_name" id="scl_director_name"
                               value="{{ $template->settings['scl_director_name'] ?? '' }}"
                               placeholder="e.g., Dr. Ahmad bin Abdullah"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-purple-600 dark:text-purple-400">@{{ director_name }}</code></p>
                </div>

                <!-- Director Signature -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Director Signature
                    </label>

                    @if(isset($template->settings['scl_director_signature_path']))
                    <!-- Current Signature Preview -->
                    <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ Storage::url($template->settings['scl_director_signature_path']) }}"
                                     alt="Director Signature"
                                     class="h-12 object-contain bg-white rounded border border-gray-200 dark:border-gray-600 p-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Current signature</span>
                            </div>
                            <button type="button" onclick="deleteSclDirectorSignature()" class="text-red-500 hover:text-red-700 text-sm font-medium">
                                Remove
                            </button>
                        </div>
                    </div>
                    @endif

                    <div class="relative">
                        <input type="file" name="scl_director_signature" id="scl_director_signature" accept="image/png,image/jpeg,image/jpg" class="hidden" onchange="previewSignature(this)">
                        <label for="scl_director_signature" class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-purple-500 dark:hover:border-purple-400 transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span id="signatureFileName" class="text-gray-600 dark:text-gray-400">{{ isset($template->settings['scl_director_signature_path']) ? 'Upload new signature' : 'Upload signature image' }}</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG or JPG, max 2MB. Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-purple-600 dark:text-purple-400">@{{ director_signature }}</code></p>
                </div>

                <!-- Save Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCL Auto-Release Toggle -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SCL Auto-Release</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                        When enabled, SCL will automatically be generated and released when a student receives an offer letter.
                    </p>
                    @if($template->settings['scl_auto_release_enabled'] ?? false)
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center gap-1.5 text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2.5 py-1 rounded-full font-medium">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                Auto-Release Active
                            </span>
                            @if($template->settings['scl_auto_release_enabled_at'] ?? null)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    Enabled {{ \Carbon\Carbon::parse($template->settings['scl_auto_release_enabled_at'])->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center gap-1.5 text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 px-2.5 py-1 rounded-full font-medium">
                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                Auto-Release Disabled
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            <form action="{{ route('admin.documents.scl.toggle-auto-release') }}" method="POST">
                @csrf
                @if($template->settings['scl_auto_release_enabled'] ?? false)
                    <button type="submit"
                            onclick="return confirm('Disable SCL auto-release? Students will no longer receive SCL automatically when they get offer letters.')"
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        Disable Auto-Release
                    </button>
                @else
                    <button type="submit"
                            onclick="return confirm('Enable SCL auto-release? SCL will automatically be generated when students receive offer letters.')"
                            class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Enable SCL Auto-Release
                    </button>
                @endif
            </form>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">Ready to Generate SCL?</h3>
                <p class="text-purple-100 text-sm mt-1">Release SCL letters to students with confirmed placements</p>
            </div>
            <a href="{{ route('placement.index') }}"
               class="px-5 py-2.5 bg-white text-purple-600 font-semibold rounded-lg hover:bg-purple-50 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Release SCL to Students
            </a>
        </div>
    </div>
</div>

<!-- Hidden form for deleting director signature (outside main form to avoid nesting) -->
<form id="deleteSclSignatureForm" action="{{ route('admin.documents.scl.director-signature.delete') }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
function previewSignature(input) {
    const fileNameElement = document.getElementById('signatureFileName');
    if (input.files && input.files[0]) {
        fileNameElement.textContent = input.files[0].name;
        fileNameElement.classList.add('text-purple-600', 'dark:text-purple-400', 'font-medium');
    } else {
        fileNameElement.textContent = 'Upload signature image';
        fileNameElement.classList.remove('text-purple-600', 'dark:text-purple-400', 'font-medium');
    }
}

function deleteSclDirectorSignature() {
    if (confirm('Delete this signature?')) {
        document.getElementById('deleteSclSignatureForm').submit();
    }
}
</script>
@endsection
