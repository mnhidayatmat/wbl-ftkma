@extends('layouts.app')

@section('title', 'Import Agreements')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <a href="{{ route('admin.agreements.index') }}" class="text-[#0084C5] hover:underline text-sm mb-2 inline-block">
                ← Back to Agreements
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Import MoU / MoA / LOI</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Bulk import agreement records from Excel file</p>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <!-- Instructions -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3">Import Instructions</h3>
            <ol class="list-decimal list-inside space-y-2 text-sm text-blue-700 dark:text-blue-300">
                <li>Download the <a href="{{ route('admin.agreements.template') }}" class="underline font-semibold">Excel template</a> first</li>
                <li>Fill in the agreement data following the template format</li>
                <li>Required columns: <strong>Company Name</strong>, <strong>Agreement Type</strong> (MoU/MoA/LOI)</li>
                <li>If a company doesn't exist, it will be auto-created</li>
                <li>Duplicate agreements (same company + type + reference no) will be skipped</li>
                <li>Upload the completed file below</li>
            </ol>
        </div>

        <!-- Expected Columns -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Expected Excel Columns</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="px-4 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">Column</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">Required</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Company Name</td>
                            <td class="px-4 py-2"><span class="text-red-500">Yes</span></td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Name of the company</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Agreement Type</td>
                            <td class="px-4 py-2"><span class="text-red-500">Yes</span></td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">MoU, MoA, or LOI</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Agreement Title</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Title or description of the agreement</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Reference No</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Official reference number (used for duplicate detection)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Start Date</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Format: YYYY-MM-DD or DD/MM/YYYY</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">End Date</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Format: YYYY-MM-DD or DD/MM/YYYY</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Signed Date</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Date when agreement was signed</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Status</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Active, Expired, Terminated, Pending, Draft (auto-determined if empty)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Faculty</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Faculty name</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Programme</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Programme name</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Industry Type</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Used when creating new company</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Address</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Company address (for new companies)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Email</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Company email (for new companies)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Contact Person</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">PIC name (for new companies)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">Remarks</td>
                            <td class="px-4 py-2">No</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">Additional notes</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <form action="{{ route('admin.agreements.import.preview') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Select Excel File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('file') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accepted formats: .xlsx, .xls, .csv (Max: 10MB)</p>
                    @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.agreements.template') }}" 
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Template
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Preview Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

