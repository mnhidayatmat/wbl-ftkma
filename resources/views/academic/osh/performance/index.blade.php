@extends('layouts.app')

@section('title', 'OSH Student Performance Overview')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">OSH Student Performance Overview</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Overall performance based on Lecturer ({{ number_format($lecturerTotalWeight + $lecturerRubricTotalWeight, 2) }}%) evaluation</p>
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
                        <a href="{{ route('academic.osh.performance.export.excel', request()->query()) }}" 
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export to Excel (.xlsx)
                        </a>
                        <a href="{{ route('academic.osh.performance.export.pdf', request()->query()) }}" 
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

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Progress Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Lecturer Weight</div>
                <div class="text-2xl font-black text-[#003A6C] dark:text-[#0084C5]">{{ number_format($lecturerTotalWeight + $lecturerRubricTotalWeight, 2) }}%</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-green-500">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Industry Coach Weight</div>
                <div class="text-2xl font-black text-green-600 dark:text-green-400">0.00%</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Weight</div>
                <div class="text-2xl font-black text-purple-600 dark:text-purple-400">100.00%</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('academic.osh.performance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Name or Matric No..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Programme Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Programme</label>
                    <select name="programme" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Programmes</option>
                        @foreach($programmes as $programme)
                            <option value="{{ $programme }}" {{ request('programme') == $programme ? 'selected' : '' }}>
                                {{ $programme }}
                            </option>
                        @endforeach
                    </select>
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

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit" 
                            class="flex-1 px-3 sm:px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('academic.osh.performance.index') }}" 
                       class="px-3 sm:px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm rounded-lg transition-colors whitespace-nowrap">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Students Performance Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-[#003A6C]">
                            <tr>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Name</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Matric No</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden xl:table-cell">Programme</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden xl:table-cell">Group</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Lecturer</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Final</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Updated</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($studentsWithPerformance as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 lg:hidden mt-1">{{ $student->matric_no }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden lg:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $student->matric_no }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden xl:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $student->programme ?? 'N/A' }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden xl:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $student->group->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ number_format($student->lecturer_score, 1) }}%<span class="text-gray-500">/{{ number_format($lecturerTotalWeight + $lecturerRubricTotalWeight, 2) }}%</span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="mt-1 max-w-[80px] sm:max-w-[100px] bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div class="bg-[#0084C5] h-1.5 rounded-full" 
                                             style="width: {{ min($student->lecturer_progress, 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-xs sm:text-sm font-bold text-[#003A6C] dark:text-[#0084C5] whitespace-nowrap">
                                        {{ number_format($student->final_score, 1) }}%<span class="text-gray-500">/100%</span>
                                    </div>
                                    <!-- Overall Progress Bar -->
                                    <div class="mt-1 max-w-[100px] sm:max-w-[120px] bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-[#0084C5] h-2 rounded-full" 
                                             style="width: {{ min($student->overall_progress, 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    @if($student->overall_status == 'completed')
                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 inline-flex text-xs leading-4 sm:leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Done
                                        </span>
                                    @elseif($student->overall_status == 'in_progress')
                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 inline-flex text-xs leading-4 sm:leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                            Progress
                                        </span>
                                    @else
                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 inline-flex text-xs leading-4 sm:leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                            Not Started
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden lg:table-cell text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                    @if($student->last_updated)
                                        <div class="whitespace-nowrap">{{ $student->last_updated->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $student->last_updated->format('H:i') }}</div>
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 sm:px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No students found</p>
                                    <p class="text-sm">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        @if($studentsWithPerformance->count() > 0)
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</div>
                <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mt-2">{{ $studentsWithPerformance->count() }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6">
                <div class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Completed</div>
                <div class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400 mt-1 sm:mt-2">
                    {{ $studentsWithPerformance->where('overall_status', 'completed')->count() }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6">
                <div class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</div>
                <div class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1 sm:mt-2">
                    {{ $studentsWithPerformance->where('overall_status', 'in_progress')->count() }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6">
                <div class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Not Started</div>
                <div class="text-xl sm:text-2xl font-bold text-gray-600 dark:text-gray-400 mt-1 sm:mt-2">
                    {{ $studentsWithPerformance->where('overall_status', 'not_started')->count() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
