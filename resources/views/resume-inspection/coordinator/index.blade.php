@extends('layouts.app')

@section('title', 'Resume Inspection - Coordinator')

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Resume Inspection Review</h1>
            <p class="text-gray-600 dark:text-gray-400">Review and approve student resumes and portfolios</p>
        </div>
        <a href="{{ route('reference-samples.index') }}"
           class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <span>Manage Reference Samples</span>
        </a>
    </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Total Students</div>
                <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Not Submitted</div>
                <div class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['not_submitted'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Pending Review</div>
                <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Approved</div>
                <div class="text-2xl font-bold text-green-600 mt-1">{{ $stats['approved'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Revision Required</div>
                <div class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['revision_required'] }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <form method="GET" action="{{ route('coordinator.resume.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Group Filter -->
                <div>
                    <label for="group" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Group</label>
                    <select name="group" id="group" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $filters['group'] == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="NOT_SUBMITTED" {{ $filters['status'] == 'NOT_SUBMITTED' ? 'selected' : '' }}>Not Submitted</option>
                        <option value="PENDING" {{ $filters['status'] == 'PENDING' ? 'selected' : '' }}>Pending Review</option>
                        <option value="PASSED" {{ $filters['status'] == 'PASSED' ? 'selected' : '' }}>Approved</option>
                        <option value="REVISION_REQUIRED" {{ $filters['status'] == 'REVISION_REQUIRED' ? 'selected' : '' }}>Revision Required</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] }}" 
                           placeholder="Name or Matric No" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('coordinator.resume.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C] dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Resume Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Uploaded At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $student)
                            @php
                                $inspection = $student->resumeInspection;
                                $status = $inspection ? $inspection->status : null;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->matric_no }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $student->group ? $student->group->name : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(!$inspection || !$inspection->resume_file_path)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Not Submitted
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $inspection->status_badge_color }}">
                                            {{ $inspection->status_display }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($inspection && $inspection->resume_file_path)
                                        {{ $inspection->created_at->format('d M Y, h:i A') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($inspection && $inspection->resume_file_path)
                                        <div class="flex gap-2 items-center">
                                            <a href="{{ route('coordinator.resume.download-document', $inspection) }}" 
                                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                View Document
                                            </a>
                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                            <a href="{{ route('coordinator.resume.review', $inspection) }}" 
                                               class="text-[#0084C5] hover:text-[#003A6C] dark:text-[#0084C5] dark:hover:text-[#00A86B] font-medium">
                                                Review
                                            </a>
                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                            <form method="POST" action="{{ route('coordinator.resume.reset', $inspection) }}" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to reset this student\'s resume inspection? This will delete the uploaded document and clear all review data. The student will need to resubmit.');">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                    Reset
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($inspection)
                                        <div class="flex gap-2 items-center">
                                            <span class="text-gray-400">No document</span>
                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                            <form method="POST" action="{{ route('coordinator.resume.reset', $inspection) }}" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to reset this student\'s resume inspection? This will clear all review data.');">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                                    Reset
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-400">No document</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No students found matching the filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
</div>
@endsection
