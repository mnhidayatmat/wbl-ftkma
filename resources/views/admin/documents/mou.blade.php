@extends('layouts.app')

@section('title', 'Template MoU - Documents')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Template MoU</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Memorandum of Understanding template for company partnerships</p>
    </div>

    <!-- Template Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Memorandum of Understanding (MoU)</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Official agreement document between UMPSA and partner companies</p>
                    <div class="flex items-center gap-4 mt-3">
                        <span class="text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-1 rounded-full">Template Not Configured</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">Template Configuration Required</h3>
                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                    The MoU template is currently managed through uploaded documents in the Companies module.
                    To configure a PDF template, create a new blade file at:
                </p>
                <code class="block mt-2 text-xs bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded">
                    resources/views/admin/documents/pdf/mou.blade.php
                </code>
            </div>
        </div>
    </div>

    <!-- Current Process -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current MoU Process</h3>
        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">1</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Upload MoU Document</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Upload signed MoU PDF files through the Companies module</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">2</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Set Agreement Details</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Configure start date, end date, and agreement type</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">3</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Track Agreement Status</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Monitor active, expiring, and expired agreements</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.companies.index', ['view' => 'agreements']) }}"
               class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Manage Agreements
            </a>
            <a href="{{ route('admin.agreements.create') }}"
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Agreement
            </a>
        </div>
    </div>
</div>
@endsection
