@extends('layouts.app')

@section('title', 'IP Reports')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">IP Reports</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Generate and download comprehensive performance reports</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Students</div>
                <div class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $totalStudents }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Groups</div>
                <div class="text-3xl font-bold text-[#0084C5]">{{ $totalGroups }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Companies</div>
                <div class="text-3xl font-bold text-[#00AEEF]">{{ $totalCompanies }}</div>
            </div>
        </div>

        <!-- Report Options -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Full Cohort Report -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-[#003A6C] rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Full Cohort Results</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">All students in IP course</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('academic.ip.reports.cohort', ['format' => 'excel']) }}" 
                       class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors text-center">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('academic.ip.reports.cohort', ['format' => 'pdf']) }}" 
                       class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors text-center">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        PDF
                    </a>
                </div>
            </div>

            <!-- Group-Wise Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-[#0084C5] rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Group-Wise Results</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Results by student group</p>
                    </div>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @php
                        $groups = \App\Models\WblGroup::orderBy('name')->get();
                    @endphp
                    @forelse($groups as $group)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $group->name }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('academic.ip.reports.group', ['group' => $group->id, 'format' => 'excel']) }}" 
                               class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded transition-colors">
                                Excel
                            </a>
                            <a href="{{ route('academic.ip.reports.group', ['group' => $group->id, 'format' => 'pdf']) }}" 
                               class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition-colors">
                                PDF
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No groups found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Company-Wise Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-[#00AEEF] rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Company-Wise Results</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Results by company</p>
                    </div>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @php
                        $companies = \App\Models\Company::whereHas('students')->orderBy('company_name')->get();
                    @endphp
                    @forelse($companies as $company)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->company_name }}</span>
                        <div class="flex gap-2">
                            <a href="{{ route('academic.ip.reports.company', ['company' => $company->id, 'format' => 'excel']) }}" 
                               class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded transition-colors">
                                Excel
                            </a>
                            <a href="{{ route('academic.ip.reports.company', ['company' => $company->id, 'format' => 'pdf']) }}" 
                               class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition-colors">
                                PDF
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No companies found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Lecturer Contribution Report -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-600 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lecturer Contribution Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Lecturer contribution breakdown</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Coming soon...</p>
            </div>

            <!-- IC Contribution Report -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-orange-600 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">IC Contribution Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">IC contribution breakdown</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Coming soon...</p>
            </div>
        </div>
    </div>
</div>
@endsection
