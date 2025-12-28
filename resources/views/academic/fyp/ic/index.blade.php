@extends('layouts.app')

@section('title', 'FYP – Industry Coach Evaluation')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP – Industry Coach Evaluation</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Enter rubric scores for assigned students (60% contribution)</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('academic.fyp.ic.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
                    <a href="{{ route('academic.fyp.ic.index') }}" 
                       class="px-3 sm:px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm rounded-lg transition-colors whitespace-nowrap">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-[#003A6C]">
                            <tr>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Name</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Matric No</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden xl:table-cell">Group</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden xl:table-cell">Company</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Score</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Updated</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($studentsWithStatus as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">{{ $student->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 lg:hidden mt-1">{{ $student->matric_no }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden lg:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $student->matric_no }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden xl:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $student->group->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden xl:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $student->company->company_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    @if($student->evaluation_status == 'not_started')
                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                            Not Started
                                        </span>
                                    @elseif($student->evaluation_status == 'in_progress')
                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300">
                                            Progress
                                        </span>
                                    @else
                                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">
                                            Done
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ number_format($student->total_contribution, 1) }}%</span>
                                        <span class="text-gray-500">/{{ number_format($totalIcWeight ?? 60, 0) }}%</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden lg:table-cell text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                                    @if($student->last_updated)
                                        <div class="whitespace-nowrap">{{ $student->last_updated->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $student->last_updated->format('H:i') }}</div>
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                    @if(auth()->user()->isAdmin() || (auth()->user()->isIndustry() && $student->ic_id == auth()->id()))
                                        <a href="{{ route('academic.fyp.ic.show', $student) }}"
                                           class="inline-flex items-center px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors">
                                            {{ $student->evaluation_status == 'not_started' ? 'Evaluate' : 'Edit' }}
                                        </a>
                                    @else
                                        <a href="{{ route('academic.fyp.ic.show', $student) }}"
                                           class="inline-flex items-center px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition-colors">
                                            View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 sm:px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            @if(auth()->user()->isIndustry() && !auth()->user()->isAdmin())
                                                No students assigned to you.
                                            @else
                                                No students found.
                                            @endif
                                        </p>
                                        @if(auth()->user()->isIndustry() && !auth()->user()->isAdmin())
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Please contact an administrator to assign students to you as Industry Coach.
                                            </p>
                                        @endif
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
</div>
@endsection
