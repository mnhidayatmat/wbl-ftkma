@extends('layouts.app')

@section('title', 'FYP Reports')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP Reports</h1>
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
                        <p class="text-sm text-gray-600 dark:text-gray-400">All students in FYP course</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('academic.fyp.reports.cohort', ['format' => 'excel']) }}" 
                       class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors text-center">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('academic.fyp.reports.cohort', ['format' => 'pdf']) }}" 
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
                            <a href="{{ route('academic.fyp.reports.group', ['group' => $group->id, 'format' => 'excel']) }}" 
                               class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded transition-colors">
                                Excel
                            </a>
                            <a href="{{ route('academic.fyp.reports.group', ['group' => $group->id, 'format' => 'pdf']) }}" 
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
                            <a href="{{ route('academic.fyp.reports.company', ['company' => $company->id, 'format' => 'excel']) }}" 
                               class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded transition-colors">
                                Excel
                            </a>
                            <a href="{{ route('academic.fyp.reports.company', ['company' => $company->id, 'format' => 'pdf']) }}" 
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

            <!-- Assessment by CLO Report -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 lg:col-span-2">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assessment by CLO Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Detailed assessment breakdown organized by Course Learning Outcomes (CLO)</p>
                    </div>
                </div>

                <form action="{{ route('academic.fyp.reports.clo-assessment') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Academic Session Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Academic Session
                            </label>
                            <select name="session" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Sessions</option>
                                @php
                                    $currentYear = date('Y');
                                    $sessions = [];
                                    for ($i = 0; $i < 5; $i++) {
                                        $year = $currentYear - $i;
                                        $nextYear = $year + 1;
                                        $sessions[] = "{$year}/{$nextYear}";
                                    }
                                @endphp
                                @foreach($sessions as $session)
                                    <option value="{{ $session }}">{{ $session }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Group Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Group
                            </label>
                            <select name="group_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Groups</option>
                                @php
                                    $groups = \App\Models\WblGroup::orderBy('name')->get();
                                @endphp
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Company
                            </label>
                            <select name="company_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Companies</option>
                                @php
                                    $companies = \App\Models\Company::whereHas('students')->orderBy('company_name')->get();
                                @endphp
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Report Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Report Type
                            </label>
                            <select name="report_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                                <option value="individual">Individual Students</option>
                                <option value="batch">Batch Summary</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" name="format" value="excel"
                                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export to Excel
                        </button>
                        <button type="submit" name="format" value="pdf"
                                class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Export to PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
