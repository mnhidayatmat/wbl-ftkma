@extends('layouts.app')

@section('title', 'Template MoU - Documents')

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

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Template MoU</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Memorandum of Understanding template for company partnerships</p>
    </div>

    <!-- Template Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-[#00A86B] to-[#008F5B] rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Memorandum of Understanding (MoU)</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Official partnership agreement document for companies</p>
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
                <a href="{{ route('admin.documents.mou.word-template') }}"
                   class="px-4 py-2 bg-[#00A86B] hover:bg-[#008F5B] text-white font-medium rounded-lg transition-colors flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ $template->word_template_path ? 'Manage Template' : 'Upload Template' }}
                </a>
                @if($template->word_template_path)
                <a href="{{ route('admin.documents.mou.preview') }}"
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
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Auto-populated Variables</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Automatically filled from company data</p>
                </div>
            </div>

            <div class="space-y-3">
                <!-- Company Name -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Company Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${company_name}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Company Address -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Company Address</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${company_address}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- HR/PIC Name -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">HR/PIC Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${hr_name}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- HR Email -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">HR/PIC Email</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${hr_email}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- HR Phone -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">HR/PIC Phone</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${hr_phone}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>

                <!-- Current Date -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white dark:bg-gray-600 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Current Date</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${current_date}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-600 px-2 py-0.5 rounded">Auto</span>
                </div>
            </div>
        </div>

        <!-- Manual Input Variables -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 bg-[#00A86B]/10 dark:bg-[#00A86B]/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#00A86B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Manual Input Variables</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Default values for MoU generation</p>
                </div>
            </div>

            <form action="{{ route('admin.documents.mou.update') }}" method="POST" class="space-y-4">
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

                <!-- Company/MoU Number -->
                <div>
                    <label for="mou_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        MoU Reference Number
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                        <input type="text" name="mou_number" id="mou_number"
                               value="{{ $template->settings['mou_number'] ?? '' }}"
                               placeholder="e.g., MOU/UMPSA/2025/001"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${company_number}</code></p>
                </div>

                <!-- Company Short Name -->
                <div>
                    <label for="company_shortname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Company Short Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <input type="text" name="company_shortname" id="company_shortname"
                               value="{{ $template->settings['company_shortname'] ?? '' }}"
                               placeholder="e.g., TMJ"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${company_shortname}</code></p>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Signatory Details</p>
                </div>

                <!-- Signed Behalf Name -->
                <div>
                    <label for="signed_behalf_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Signed Behalf Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="signed_behalf_name" id="signed_behalf_name"
                               value="{{ $template->settings['signed_behalf_name'] ?? '' }}"
                               placeholder="e.g., Dato' Ahmad bin Ibrahim"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${signed_behalf_name}</code></p>
                </div>

                <!-- Signed Behalf Position -->
                <div>
                    <label for="signed_behalf_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Signed Behalf Position
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="text" name="signed_behalf_position" id="signed_behalf_position"
                               value="{{ $template->settings['signed_behalf_position'] ?? '' }}"
                               placeholder="e.g., Chief Executive Officer"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${signed_behalf_position}</code></p>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Witness Details</p>
                </div>

                <!-- Witness Name -->
                <div>
                    <label for="witness_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Witness Name
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="witness_name" id="witness_name"
                               value="{{ $template->settings['witness_name'] ?? '' }}"
                               placeholder="e.g., Encik Mohd Hafiz bin Osman"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${witness_name}</code></p>
                </div>

                <!-- Witness Position -->
                <div>
                    <label for="witness_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Witness Position
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input type="text" name="witness_position" id="witness_position"
                               value="{{ $template->settings['witness_position'] ?? '' }}"
                               placeholder="e.g., General Manager"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${witness_position}</code></p>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">UMPSA Staff</p>
                </div>

                <!-- UMPSA Liaison Officer -->
                <div>
                    <label for="liaison_officer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        UMPSA Liaison Officer
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="text" name="liaison_officer" id="liaison_officer"
                               value="{{ $template->settings['liaison_officer'] ?? '' }}"
                               placeholder="e.g., Encik Ahmad bin Ali"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${liaison_officer}</code></p>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">University Leadership</p>
                </div>

                <!-- Vice Chancellor -->
                <div>
                    <label for="vc_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Vice Chancellor (Naib Canselor)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="vc_name" id="vc_name"
                               value="{{ $template->settings['vc_name'] ?? 'Professor Dr. Yatimah Alias' }}"
                               placeholder="Professor Dr. Yatimah Alias"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${vc_name}</code></p>
                </div>

                <!-- Deputy Vice Chancellor -->
                <div>
                    <label for="dvc_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Deputy VC (Academic & International)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="dvc_name" id="dvc_name"
                               value="{{ $template->settings['dvc_name'] ?? 'Professor Dato Ir. Ts. Dr. Ahmad Ziad Sulaiman' }}"
                               placeholder="Professor Dato Ir. Ts. Dr. Ahmad Ziad Sulaiman"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-[#00A86B] focus:border-[#00A86B] transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Variable: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-[#00A86B] dark:text-[#00A86B]">${dvc_name}</code></p>
                </div>

                <!-- Save Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-[#00A86B] hover:bg-[#008F5B] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Default Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- How to Use Section -->
    <div class="bg-gradient-to-r from-[#00A86B] to-[#008F5B] rounded-xl shadow-md p-6 text-white">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold">How to Generate MoU</h3>
                <div class="mt-3 space-y-2 text-green-100 text-sm">
                    <p><strong>1.</strong> Upload a Word template (.docx) with variable placeholders using <code class="bg-white/20 px-1 rounded">${variable_name}</code> format</p>
                    <p><strong>2.</strong> The default values above will be used when generating MoU documents</p>
                    <p><strong>3.</strong> Go to Company Details → Agreements tab to generate MoU for specific companies</p>
                    <p><strong>Tip:</strong> Use <code class="bg-white/20 px-1 rounded">${variable:upper}</code> for uppercase values in your template</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
