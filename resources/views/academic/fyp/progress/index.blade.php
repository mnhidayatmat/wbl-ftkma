@extends('layouts.app')

@section('title', 'FYP Evaluation Progress')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP Evaluation Progress</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Monitor evaluation completion across groups, programmes, and companies</p>
        </div>

        <!-- Overall Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Students -->
            <div class="bg-gradient-to-br from-[#003A6C] to-[#0084C5] rounded-xl shadow-md p-6 text-white">
                <div class="text-sm font-medium opacity-90 mb-1">Total Students</div>
                <div class="text-3xl font-bold">{{ $totalStudents }}</div>
            </div>

            <!-- AT Completed -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">AT Completed</div>
                    <div class="text-lg font-bold text-[#0084C5]">{{ $atCompleted }}</div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                    <div class="bg-[#0084C5] h-3 rounded-full transition-all" style="width: {{ min($atProgress, 100) }}%"></div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($atProgress, 1) }}%</div>
            </div>

            <!-- IC Completed -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">IC Completed</div>
                    <div class="text-lg font-bold text-[#00AEEF]">{{ $icCompleted }}</div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                    <div class="bg-[#00AEEF] h-3 rounded-full transition-all" style="width: {{ min($icProgress, 100) }}%"></div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($icProgress, 1) }}%</div>
            </div>

            <!-- Pending Evaluations -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Pending Evaluations</div>
                <div class="space-y-1">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">AT:</span>
                        <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ $pendingAt }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">IC:</span>
                        <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ $pendingIc }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Progress by Group</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Total Students</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">AT Completed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">AT Progress</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Completed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($groupStats as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stat['group']->name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['at_completed'] }} / {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                                        <div class="bg-[#0084C5] h-2 rounded-full" style="width: {{ min($stat['at_progress'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format($stat['at_progress'], 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['ic_completed'] }} / {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                                        <div class="bg-[#00AEEF] h-2 rounded-full" style="width: {{ min($stat['ic_progress'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format($stat['ic_progress'], 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No groups found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Programme Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Progress by Programme</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Programme</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Total Students</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">AT Completed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">AT Progress</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Completed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($programmeStats as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stat['programme'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['at_completed'] }} / {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                                        <div class="bg-[#0084C5] h-2 rounded-full" style="width: {{ min($stat['at_progress'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format($stat['at_progress'], 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['ic_completed'] }} / {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                                        <div class="bg-[#00AEEF] h-2 rounded-full" style="width: {{ min($stat['ic_progress'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format($stat['ic_progress'], 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No programmes found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Company Breakdown -->
        @if($companyStats->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Progress by Company</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Company</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Total Students</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">AT Completed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">AT Progress</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Completed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Progress</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($companyStats as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stat['company']->company_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['at_completed'] }} / {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                                        <div class="bg-[#0084C5] h-2 rounded-full" style="width: {{ min($stat['at_progress'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format($stat['at_progress'], 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $stat['ic_completed'] }} / {{ $stat['total'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2 mr-2">
                                        <div class="bg-[#00AEEF] h-2 rounded-full" style="width: {{ min($stat['ic_progress'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format($stat['ic_progress'], 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No companies found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
