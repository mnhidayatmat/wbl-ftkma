@extends('layouts.app')

@section('title', 'Import Preview')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <a href="{{ route('admin.agreements.import') }}" class="text-[#0084C5] hover:underline text-sm mb-2 inline-block">
                ← Back to Import
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Import Preview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Review the data before importing</p>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                <div class="text-3xl font-bold text-green-600">{{ count($validRows) }}</div>
                <div class="text-sm text-green-700 dark:text-green-400">Valid Rows (Ready to Import)</div>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="text-3xl font-bold text-red-600">{{ count($invalidRows) }}</div>
                <div class="text-sm text-red-700 dark:text-red-400">Invalid Rows (Will be Skipped)</div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="text-3xl font-bold text-blue-600">{{ count($validRows) + count($invalidRows) }}</div>
                <div class="text-sm text-blue-700 dark:text-blue-400">Total Rows</div>
            </div>
        </div>

        @if(isset($mappedColumns) && count($mappedColumns) > 0)
        <!-- Column Mapping Info -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Detected Column Mappings:</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($mappedColumns as $field => $header)
                <span class="px-2 py-1 text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded">
                    <span class="font-medium text-[#0084C5]">{{ ucwords(str_replace('_', ' ', $field)) }}</span>
                    <span class="text-gray-500">←</span>
                    "{{ $header }}"
                </span>
                @endforeach
            </div>
        </div>
        @endif

        @if(count($invalidRows) > 0)
        <!-- Invalid Rows -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-red-500 px-6 py-3">
                <h3 class="text-lg font-semibold text-white">Invalid Rows ({{ count($invalidRows) }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Row</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Company</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Errors</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($invalidRows as $row)
                        <tr class="bg-red-50 dark:bg-red-900/10">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['row_number'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['data']['company_name'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['data']['agreement_type'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">
                                <ul class="list-disc list-inside">
                                    @foreach($row['errors'] as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(count($validRows) > 0)
        <!-- Valid Rows -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-green-500 px-6 py-3">
                <h3 class="text-lg font-semibold text-white">Valid Rows ({{ count($validRows) }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Row</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Company</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ref No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Start</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">End</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($validRows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['row_number'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['company_name'] ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ strtoupper($row['agreement_type'] ?? '') == 'MOU' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ strtoupper($row['agreement_type'] ?? '') == 'MOA' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ strtoupper($row['agreement_type'] ?? '') == 'LOI' ? 'bg-orange-100 text-orange-800' : '' }}">
                                    {{ strtoupper($row['agreement_type'] ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ Str::limit($row['agreement_title'] ?? '-', 30) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['reference_no'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['start_date'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['end_date'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Import Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <form action="{{ route('admin.agreements.import.execute') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="skip_invalid" value="1" checked
                               class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Skip duplicate agreements (recommended)</span>
                    </label>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.agreements.import') }}" 
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                        Cancel & Re-upload
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Import {{ count($validRows) }} Records
                    </button>
                </div>
            </form>
        </div>
        @else
        <!-- No Valid Rows -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 text-center">
            <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">No Valid Data to Import</h3>
            <p class="text-sm text-yellow-700 dark:text-yellow-400 mb-4">
                All rows in your file have validation errors. Please fix the issues and try again.
            </p>
            <a href="{{ route('admin.agreements.import') }}" 
               class="inline-block px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                Fix and Re-upload
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

