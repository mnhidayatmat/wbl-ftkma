@extends('layouts.app')

@section('title', 'LI - Assign Students')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">LI Student Assignment</h1>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-2">Assign a single Lecturer to all Learning Integration students</p>
            </div>
            <a href="{{ route('academic.li.assign-students.export') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-[#003A6C]">
                <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Students</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['with_lecturer'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">With Lecturer</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['ic_assigned'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">IC Assigned</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-amber-500">
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['ic_unassigned'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">IC Pending</div>
            </div>
        </div>

        <!-- Lecturer Assignment Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#003A6C] to-[#0084C5] flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">LI Lecturer Assignment</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Assign a single lecturer to supervise all LI students</p>
                </div>
            </div>

            <!-- Current Lecturer Status -->
            <div class="mb-6 p-4 rounded-lg {{ $currentLecturer ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($currentLecturer)
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center">
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ substr($currentLecturer->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Lecturer</p>
                                <p class="text-lg font-bold text-green-700 dark:text-green-300">{{ $currentLecturer->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $currentLecturer->email }}</p>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-800 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Lecturer</p>
                                <p class="text-lg font-bold text-amber-700 dark:text-amber-300">Not Assigned</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Please assign a lecturer below</p>
                            </div>
                        @endif
                    </div>
                    @if($currentLecturer)
                        <form action="{{ route('academic.li.assign-students.clear-lecturer') }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure you want to remove the lecturer assignment from all students?');">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors text-sm font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Clear Assignment
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Assign Lecturer Form -->
            <form action="{{ route('academic.li.assign-students.assign-lecturer') }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select Lecturer</label>
                    <select name="lecturer_id" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">-- Choose a Lecturer --</option>
                        @foreach($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" {{ $currentLecturer && $currentLecturer->id == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->name }} ({{ $lecturer->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-2 bg-[#003A6C] hover:bg-[#002D54] text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Assign to All Students
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card about IC Assignment -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300">About Industry Coach (IC) Assignment</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                        For LI module, Industry Coaches are assigned by students during their registration process.
                        The IC assignment shown below is set by each student when they register for Learning Integration.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('academic.li.assign-students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
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

                <!-- IC Status Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">IC Status</label>
                    <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="ic_assigned" {{ request('status') == 'ic_assigned' ? 'selected' : '' }}>IC Assigned</option>
                        <option value="ic_unassigned" {{ request('status') == 'ic_unassigned' ? 'selected' : '' }}>IC Not Assigned</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Show</label>
                    <select name="per_page"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors min-h-[42px]">
                        Filter
                    </button>
                    <a href="{{ route('academic.li.assign-students.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors min-h-[42px] flex items-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Programme</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden md:table-cell">Group</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Lecturer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Industry Coach</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Company</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <!-- No -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ method_exists($students, 'firstItem') ? $students->firstItem() + $index : $index + 1 }}
                                    </div>
                                </td>

                                <!-- Student Name -->
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">{{ $student->name }}</div>
                                </td>

                                <!-- Matric No -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->matric_no }}</div>
                                </td>

                                <!-- Programme -->
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->programme ?? 'N/A' }}</div>
                                </td>

                                <!-- Group -->
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->group->name ?? 'N/A' }}</div>
                                </td>

                                <!-- Lecturer -->
                                <td class="px-4 py-3">
                                    @if($student->academicTutor)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ $student->academicTutor->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Not Assigned
                                        </span>
                                    @endif
                                </td>

                                <!-- Industry Coach -->
                                <td class="px-4 py-3">
                                    @if($student->industryCoach)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ $student->industryCoach->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Company -->
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->company->company_name ?? 'N/A' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No students found</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($students, 'links'))
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
