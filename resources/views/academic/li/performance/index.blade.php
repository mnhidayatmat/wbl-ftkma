@extends('layouts.app')

@section('title', 'Industrial Training – Student Performance Overview')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-4 sm:mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Industrial Training – Student Performance Overview</h1>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-2">Comprehensive view of all evaluation components and final marks</p>
            </div>

            @can('manage-settings')
            <!-- Export Button -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        @click.away="open = false"
                        :disabled="{{ $studentsWithPerformance->isEmpty() ? 'true' : 'false' }}"
                        class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Results
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-10 border border-gray-200 dark:border-gray-700"
                     style="display: none;">
                    <div class="py-1">
                        <a href="{{ route('academic.li.performance.export.excel', request()->query()) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export to Excel (.xlsx)
                        </a>
                        <a href="{{ route('academic.li.performance.export.pdf', request()->query()) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Export to PDF (.pdf)
                        </a>
                    </div>
                </div>
            </div>
            @endcan
        </div>

        <!-- Progress Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Supervisor Weight</div>
                <div class="text-2xl font-black text-[#003A6C] dark:text-[#0084C5]">{{ number_format($supervisorWeight, 2) }}%</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-green-500">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Industry Coach Weight</div>
                <div class="text-2xl font-black text-green-600 dark:text-green-400">{{ number_format($icWeight, 2) }}%</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Weight</div>
                <div class="text-2xl font-black text-purple-600 dark:text-purple-400">{{ number_format($totalWeight, 2) }}%</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
            <form method="GET" action="{{ route('academic.li.student-performance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Name or Matric No..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Group Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Group</label>
                    <select name="group" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 sm:py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors min-h-[44px]">
                        Apply Filters
                    </button>
                    <a href="{{ route('academic.li.student-performance.index') }}" 
                       class="px-4 py-2 sm:py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors min-h-[44px] flex items-center justify-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Performance Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric / Group</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Supervisor ({{ number_format($supervisorWeight, 1) }}%)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">IC ({{ number_format($icWeight, 1) }}%)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Final Score ({{ number_format($totalWeight, 1) }}%)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($studentsWithPerformance as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $student->name }}</div>
                                    <div class="text-xs text-gray-400 capitalize">{{ $student->programme ?? 'Unknown Programme' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->matric_no }}</div>
                                    <div class="text-[10px] font-bold text-gray-500 uppercase">{{ $student->group->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($student->supervisor_score, 2) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($student->ic_score, 2) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center justify-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                                        <span class="text-base font-black text-[#003A6C] dark:text-[#0084C5]">
                                            {{ number_format($student->final_score, 2) }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#0084C5] h-1.5 rounded-full" style="width: {{ $student->progress }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-500">{{ number_format($student->progress, 0) }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($student->status == 'not_started')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-gray-100 text-gray-400 uppercase tracking-tighter">Not Started</span>
                                    @elseif($student->status == 'in_progress')
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-amber-100 text-amber-600 uppercase tracking-tighter">In Progress</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-green-100 text-green-600 uppercase tracking-tighter">Completed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <p class="text-sm font-semibold">No performance data found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
