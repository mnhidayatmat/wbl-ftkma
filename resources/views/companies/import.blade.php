@extends('layouts.app')

@section('title', 'Import Companies')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-white">Import Companies</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Upload an Excel file to import multiple companies</p>
            </div>
            <a href="{{ route('admin.companies.index') }}"
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                Back
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Instructions Card -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">Before you import:</h3>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800 dark:text-blue-400">
                        <li>Download the Excel template using the button below</li>
                        <li>Fill in the company information following the template format</li>
                        <li>Required field: <strong>company_name</strong></li>
                        <li>Optional fields: category, pic_name, email, phone, address, city, state, etc.</li>
                        <li>Save the file and upload it using the form below</li>
                        <li>Duplicate companies (by name) will be skipped automatically</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Download Template Button -->
        <div class="mb-6">
            <a href="{{ route('admin.companies.template') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Excel Template
            </a>
        </div>

        <!-- Upload Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upload Excel File</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Select the filled Excel file to import</p>
            </div>

            <form action="{{ route('admin.companies.preview-import') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- File Upload -->
                <div class="mb-6">
                    <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Excel File <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Excel files only (XLSX, XLS, CSV) - Max 10MB</p>
                            </div>
                            <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required class="hidden"
                                   onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'No file selected'" />
                        </label>
                    </div>
                    <p id="file-name" class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center">No file selected</p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Companies
                    </button>
                    <a href="{{ route('admin.companies.index') }}"
                       class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
