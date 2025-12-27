@extends('layouts.app')

@section('title', 'Import Preview - Companies')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold heading-umpsa">Import Preview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Review your data before importing to the database</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Total Rows -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Rows</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Valid Rows -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Valid Rows</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['valid'] }}</p>
                </div>
            </div>
        </div>

        <!-- Invalid Rows -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Invalid Rows</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['invalid'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <form action="{{ route('admin.companies.confirm-import') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center justify-center gap-2" {{ $stats['valid'] == 0 ? 'disabled' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Confirm Import ({{ $stats['valid'] }} companies)
            </button>
        </form>

        <form action="{{ route('admin.companies.cancel-import') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </button>
        </form>
    </div>

    <!-- Preview Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <div class="max-h-[600px] overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-umpsa-primary sticky top-0 z-10">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Row</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Company Name</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">PIC Name</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Phone</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">City</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-96">Errors</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($previewData as $row)
                        <tr class="{{ $row['valid'] ? '' : 'bg-red-50 dark:bg-red-900/20' }}">
                            <!-- Row Number -->
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['row'] }}
                            </td>

                            <!-- Status -->
                            <td class="px-3 py-4 whitespace-nowrap">
                                @if($row['valid'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Valid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Invalid
                                    </span>
                                @endif
                            </td>

                            <!-- Company Name -->
                            <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['data']['company_name'] ?? '-' }}
                            </td>

                            <!-- Category -->
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['data']['category'] ?? '-' }}
                            </td>

                            <!-- PIC Name -->
                            <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['data']['pic_name'] ?? '-' }}
                            </td>

                            <!-- Email -->
                            <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['data']['email'] ?? '-' }}
                            </td>

                            <!-- Phone -->
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['data']['phone'] ?? '-' }}
                            </td>

                            <!-- City -->
                            <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['data']['city'] ?? '-' }}
                            </td>

                            <!-- Errors -->
                            <td class="px-3 py-4 text-sm">
                                @if(!$row['valid'])
                                    <ul class="list-disc list-inside text-red-600 dark:text-red-400 space-y-1">
                                        @foreach($row['errors'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-green-600 dark:text-green-400">No errors</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
