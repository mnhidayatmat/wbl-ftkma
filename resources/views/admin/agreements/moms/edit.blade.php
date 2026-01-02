@extends('layouts.app')

@section('title', 'Edit Minute of Meeting')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-indigo-50 to-slate-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-600 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('admin.agreements.moms.index') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to MoMs
                </a>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Edit Minute of Meeting</h1>
                    <p class="text-purple-100">{{ $mom->title ?: 'MoM - ' . $mom->meeting_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.agreements.moms.update', $mom) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Meeting Details -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Meeting Details
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Title <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $mom->title) }}"
                                   placeholder="e.g., Faculty Meeting Q1 2026"
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Meeting Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="meeting_date" value="{{ old('meeting_date', $mom->meeting_date->format('Y-m-d')) }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Remarks <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <textarea name="remarks" rows="2"
                                  placeholder="Any additional notes about this meeting..."
                                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-gray-700 dark:text-white resize-none">{{ old('remarks', $mom->remarks) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Companies Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Companies Mentioned in MoM <span class="text-red-500">*</span>
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select all companies that are mentioned in this Minute of Meeting</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <input type="text" id="companySearch" placeholder="Search companies..."
                               class="w-full px-4 py-2 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="max-h-64 overflow-y-auto border-2 border-gray-200 dark:border-gray-600 rounded-xl p-3 space-y-2" id="companiesList">
                        @foreach($companies as $company)
                        <label class="company-item flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors border border-transparent hover:border-purple-200 dark:hover:border-purple-800"
                               data-name="{{ strtolower($company->company_name) }}">
                            <input type="checkbox" name="company_ids[]" value="{{ $company->id }}"
                                   {{ in_array($company->id, old('company_ids', $selectedCompanyIds)) ? 'checked' : '' }}
                                   class="w-5 h-5 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                            <div class="flex-1">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $company->company_name }}</span>
                                @if($company->industry_type)
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $company->industry_type }})</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">
                            <span id="selectedCount">0</span> company(ies) selected
                        </span>
                        <div class="flex gap-2">
                            <button type="button" onclick="selectAllCompanies()" class="text-purple-600 hover:text-purple-700 font-medium">Select All</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="deselectAllCompanies()" class="text-gray-600 hover:text-gray-700 font-medium">Deselect All</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Document -->
            @if($mom->document_path)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Current Document
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/30 dark:to-red-900/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                                    <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $mom->document_name ?: 'MoM Document' }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Uploaded {{ $mom->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.agreements.moms.download', $mom) }}"
                           class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold rounded-lg hover:shadow-lg transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Document Upload -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        {{ $mom->document_path ? 'Replace Document' : 'Upload Document' }}
                        @if(!$mom->document_path)<span class="text-red-500">*</span>@endif
                    </h2>
                </div>
                <div class="p-6">
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-purple-400 transition-colors" id="dropZone">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 font-medium mb-1">
                                Drop your PDF file here, or
                                <label for="document" class="text-purple-600 hover:text-purple-700 cursor-pointer underline">browse</label>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">PDF only, maximum 10MB{{ $mom->document_path ? '. Leave empty to keep current document.' : '' }}</p>
                            <input type="file" name="document" id="document" accept=".pdf" {{ !$mom->document_path ? 'required' : '' }} class="hidden">
                            <p id="fileName" class="text-sm text-purple-600 font-medium mt-3 hidden"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4">
                <button type="button" onclick="history.back()"
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update MoM
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // File upload handling
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('document');
    const fileNameDisplay = document.getElementById('fileName');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/pdf') {
            fileInput.files = files;
            showFileName(files[0].name);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            showFileName(e.target.files[0].name);
        }
    });

    function showFileName(name) {
        fileNameDisplay.textContent = 'Selected: ' + name;
        fileNameDisplay.classList.remove('hidden');
    }

    // Company search
    const searchInput = document.getElementById('companySearch');
    const companyItems = document.querySelectorAll('.company-item');

    searchInput.addEventListener('input', (e) => {
        const search = e.target.value.toLowerCase();
        companyItems.forEach(item => {
            const name = item.dataset.name;
            item.style.display = name.includes(search) ? 'flex' : 'none';
        });
    });

    // Selection counter
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('input[name="company_ids[]"]:checked');
        document.getElementById('selectedCount').textContent = checkboxes.length;
    }

    document.querySelectorAll('input[name="company_ids[]"]').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    // Initial count
    updateSelectedCount();

    function selectAllCompanies() {
        document.querySelectorAll('.company-item:not([style*="display: none"]) input[name="company_ids[]"]').forEach(cb => {
            cb.checked = true;
        });
        updateSelectedCount();
    }

    function deselectAllCompanies() {
        document.querySelectorAll('input[name="company_ids[]"]').forEach(cb => {
            cb.checked = false;
        });
        updateSelectedCount();
    }
</script>
@endsection
