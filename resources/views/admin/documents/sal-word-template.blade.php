@extends('layouts.app')

@section('title', 'SAL Word Template - Documents')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Success/Error Messages -->
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
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.documents.sal') }}" class="hover:text-[#003A6C] dark:hover:text-[#0084C5]">Template SAL</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Word Template</span>
        </div>
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">SAL Word Template</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Upload a Microsoft Word template for automatic SAL generation</p>
    </div>

    @if($template->word_template_path)
    <!-- Current Template Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $template->word_template_original_name }}</h2>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2.5 py-1 rounded-full font-medium flex-shrink-0">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        Active
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Microsoft Word Document (.docx)</p>

                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('admin.documents.sal.word-template.preview-docx') }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#003A6C] hover:bg-[#002855] text-white font-medium rounded-lg transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview PDF
                    </a>
                    <a href="{{ route('admin.documents.sal.word-template.download') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Original
                    </a>
                    <form action="{{ route('admin.documents.sal.word-template.delete') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this template?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium rounded-lg transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Replace Template -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Replace Template</h3>

        <form action="{{ route('admin.documents.sal.word-template.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="file" name="word_template" id="word_template_replace" accept=".docx" class="hidden" onchange="updateFileNameReplace(this)">
                    <label for="word_template_replace" class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-[#003A6C] dark:hover:border-[#0084C5] transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span id="replaceFileName" class="text-gray-600 dark:text-gray-400">Choose a new .docx file</span>
                    </label>
                </div>
                <button type="submit" class="px-4 py-3 bg-[#003A6C] hover:bg-[#002855] text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Replace
                </button>
            </div>
        </form>
    </div>
    @else
    <!-- Upload Template (No template yet) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Word Template</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Upload a .docx file with variable placeholders</p>
            </div>
        </div>

        <form action="{{ route('admin.documents.sal.word-template.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-[#003A6C] dark:hover:border-[#0084C5] transition-colors">
                <input type="file" name="word_template" id="word_template" accept=".docx" class="hidden" onchange="updateFileName(this)">
                <label for="word_template" class="cursor-pointer">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-700 dark:text-gray-300 font-medium">Click to upload Word template</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">.docx files only, max 10MB</p>
                        <p id="selectedFileName" class="text-sm text-[#003A6C] dark:text-[#0084C5] font-medium hidden"></p>
                    </div>
                </label>
            </div>

            <button type="submit" class="mt-4 w-full px-4 py-2.5 bg-[#003A6C] hover:bg-[#002855] text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Upload Template
            </button>
        </form>
    </div>
    @endif

    <!-- Variables Reference -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Variable Placeholders</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Use these in your Word template</p>
            </div>
        </div>

        <!-- Formatting Tips -->
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4">
            <div class="flex gap-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-amber-800 dark:text-amber-200">
                    <p class="font-medium">Formatting Tips:</p>
                    <p class="mt-1 text-xs"><strong>Bold:</strong> Apply bold formatting to the variable in Word - the replaced text inherits it.</p>
                    <p class="mt-1 text-xs"><strong>UPPERCASE:</strong> Use purple variables with <code class="bg-amber-100 dark:bg-amber-900 px-1 rounded">:upper</code> suffix for capital letters.</p>
                </div>
            </div>
        </div>

        @php
            $wordVariables = \App\Http\Controllers\Admin\DocumentTemplateController::getWordTemplateVariables();
        @endphp

        <!-- Two Column Layout: Standard (Left) | Uppercase (Right) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left Column: Standard Variables -->
            <div>
                <h4 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    Standard Variables
                </h4>
                <div class="space-y-2">
                    @foreach($wordVariables as $variable => $description)
                    <div class="flex items-center gap-2 py-2 px-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg group">
                        <code class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded text-xs font-mono flex-shrink-0">{{ $variable }}</code>
                        <button type="button" onclick="copyVariable('{{ $variable }}')" class="p-1 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="Copy to clipboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                        <span class="text-xs text-gray-600 dark:text-gray-400 flex-1 truncate" title="{{ $description }}">{{ $description }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Column: Uppercase Variables -->
            <div>
                <h4 class="text-sm font-medium text-purple-700 dark:text-purple-300 mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                    UPPERCASE Variables
                </h4>
                <div class="space-y-2">
                    @foreach($wordVariables as $variable => $description)
                    @php
                        $upperVar = str_replace('}', ':upper}', $variable);
                    @endphp
                    <div class="flex items-center gap-2 py-2 px-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg group">
                        <code class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded text-xs font-mono flex-shrink-0">{{ $upperVar }}</code>
                        <button type="button" onclick="copyVariable('{{ $upperVar }}')" class="p-1 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors" title="Copy to clipboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                        <span class="text-xs text-gray-600 dark:text-gray-400 flex-1 truncate" title="{{ $description }}">{{ $description }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Toast notification for copy -->
        <div id="copyToast" class="fixed bottom-4 right-4 bg-gray-900 dark:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 flex items-center gap-2 z-50">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm">Copied to clipboard!</span>
        </div>
    </div>

    <!-- Back Button -->
    <a href="{{ route('admin.documents.sal') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to SAL Overview
    </a>
</div>

<script>
function updateFileName(input) {
    const fileNameElement = document.getElementById('selectedFileName');
    if (input.files && input.files[0]) {
        fileNameElement.textContent = input.files[0].name;
        fileNameElement.classList.remove('hidden');
    } else {
        fileNameElement.classList.add('hidden');
    }
}

function updateFileNameReplace(input) {
    const fileNameElement = document.getElementById('replaceFileName');
    if (input.files && input.files[0]) {
        fileNameElement.textContent = input.files[0].name;
        fileNameElement.classList.add('text-[#003A6C]', 'dark:text-[#0084C5]', 'font-medium');
    } else {
        fileNameElement.textContent = 'Choose a new .docx file';
        fileNameElement.classList.remove('text-[#003A6C]', 'dark:text-[#0084C5]', 'font-medium');
    }
}

function copyVariable(text) {
    navigator.clipboard.writeText(text).then(() => {
        showCopyToast();
    }).catch(err => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showCopyToast();
    });
}

function showCopyToast() {
    const toast = document.getElementById('copyToast');
    toast.classList.remove('translate-y-full', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');

    setTimeout(() => {
        toast.classList.add('translate-y-full', 'opacity-0');
        toast.classList.remove('translate-y-0', 'opacity-100');
    }, 2000);
}
</script>
@endsection
