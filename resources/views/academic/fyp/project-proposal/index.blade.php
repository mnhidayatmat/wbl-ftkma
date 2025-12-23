@extends('layouts.app')

@section('title', 'FYP Project Proposals')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP Project Proposals</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Review and manage student project proposals</p>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total</div>
                <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Draft</div>
                <div class="text-2xl font-bold text-gray-600">{{ $stats['draft'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Pending Review</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['submitted'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Approved</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Needs Revision</div>
                <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('academic.fyp.proposals.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Student name, matric no, or project title..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Pending Review</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Needs Revision</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('academic.fyp.proposals.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Proposals Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Project Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Company</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Academic Tutor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($proposals as $proposal)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        @if($proposal->student->image_path)
                                            <img src="{{ asset('storage/' . $proposal->student->image_path) }}"
                                                 alt="{{ $proposal->student->name }}"
                                                 class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $proposal->student->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $proposal->student->matric_no }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $proposal->project_title }}">
                                    {{ $proposal->project_title ?: '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $proposal->student->company->company_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $proposal->student->academicTutor->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($proposal->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @elseif($proposal->status === 'submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                    @elseif($proposal->status === 'approved') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                    @elseif($proposal->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                    @endif">
                                    {{ $proposal->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $proposal->submitted_at ? $proposal->submitted_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('academic.fyp.proposals.show', $proposal) }}"
                                   class="text-[#0084C5] hover:text-[#003A6C] transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No proposals found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($proposals->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $proposals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
