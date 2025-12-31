@extends('layouts.app')

@section('title', 'Template MoU - Documents')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Template MoU</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Memorandum of Understanding Word template for company partnerships</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Word Template Upload -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">MoU Word Template</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Upload a Microsoft Word template for automatic MoU generation</p>
                    </div>
                </div>

                @if($template->word_template_path)
                <!-- Template Uploaded -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-green-800 dark:text-green-200">{{ $template->word_template_original_name }}</p>
                            <p class="text-sm text-green-600 dark:text-green-400">Microsoft Word Document (.docx)</p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">Active</span>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('admin.documents.mou.preview') }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview PDF
                        </a>
                        <a href="{{ route('admin.documents.mou.word-template.download') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download Original
                        </a>
                        <form action="{{ route('admin.documents.mou.word-template.delete') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this template?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium rounded-lg transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Replace Template -->
                <details class="group">
                    <summary class="cursor-pointer text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Replace Template
                    </summary>
                    <div class="mt-4">
                        <form action="{{ route('admin.documents.mou.word-template.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-orange-500 dark:hover:border-orange-400 transition-colors">
                                <input type="file" name="word_template" id="word_template" accept=".docx" required class="hidden" onchange="this.form.submit()">
                                <label for="word_template" class="cursor-pointer">
                                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Choose a new .docx file</p>
                                </label>
                            </div>
                        </form>
                    </div>
                </details>
                @else
                <!-- No Template - Upload Form -->
                <form action="{{ route('admin.documents.mou.word-template.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-orange-500 dark:hover:border-orange-400 transition-colors">
                        <input type="file" name="word_template" id="word_template" accept=".docx" required class="hidden" onchange="this.form.submit()">
                        <label for="word_template" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Word Template</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Click to choose a .docx file or drag and drop</p>
                        </label>
                    </div>
                </form>
                @endif
            </div>

            <!-- How to Use -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">How to Use</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400">1</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Upload Word Template</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Upload a .docx file with placeholders using ${variable_name} format</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400">2</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Fill Company MoU Variables</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Go to Company Details → Agreements tab and fill in the manual input variables</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-orange-600 dark:text-orange-400">3</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Generate MoU Document</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Click "Generate MoU" to create a customized document for that company</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Variables -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Available Variables</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Use these placeholders in your Word template:</p>

                <div class="space-y-4">
                    <!-- Manual Input Variables -->
                    <div>
                        <h4 class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider mb-2">Manual Input</h4>
                        <div class="space-y-2">
                            @foreach($variables as $var => $desc)
                                @if(str_contains($var, 'company_number') || str_contains($var, 'shortname') || str_contains($var, 'signed') || str_contains($var, 'witness'))
                                <div class="bg-orange-50 dark:bg-orange-900/20 rounded p-2">
                                    <code class="text-xs text-orange-700 dark:text-orange-300 font-mono">{{ $var }}</code>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $desc }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Auto-populated Variables -->
                    <div>
                        <h4 class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider mb-2">Auto-populated</h4>
                        <div class="space-y-2">
                            @foreach($variables as $var => $desc)
                                @if(str_contains($var, 'company_name') || str_contains($var, 'hr_') || str_contains($var, 'company_address') || str_contains($var, 'current_date'))
                                <div class="bg-green-50 dark:bg-green-900/20 rounded p-2">
                                    <code class="text-xs text-green-700 dark:text-green-300 font-mono">{{ $var }}</code>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $desc }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <strong>Tip:</strong> Use <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">${variable:upper}</code> for uppercase values.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
