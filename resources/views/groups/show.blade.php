@extends('layouts.app')

@section('title', 'Group Details')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Group Details</h1>
        @if($group->isCompleted())
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">This group has been completed and archived.</p>
        @endif
    </div>
    <div>
        @if(auth()->user()->isAdmin())
            @if($group->isActive())
                <form action="{{ route('admin.groups.mark-completed', $group) }}" method="POST" class="inline mr-2" onsubmit="return confirm('Mark this group as completed?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-colors">
                        Close Group
                    </button>
                </form>
            @else
                <form action="{{ route('admin.groups.reopen', $group) }}" method="POST" class="inline mr-2" onsubmit="return confirm('Reopen this group?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                        Reopen Group
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.groups.edit', $group) }}" class="btn-umpsa-primary mr-2">Edit</a>
        @endif
        <a href="{{ route('admin.groups.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition-colors">Back</a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Group Name</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->name }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $group->status_badge_color }}">
                {{ $group->status_display }}
            </span>
            @if($group->completed_at)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Completed: {{ $group->completed_at->format('d M Y') }}</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->start_date->format('d M Y') }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">End Date</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->end_date->format('d M Y') }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Total Students</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->students->count() }}</p>
        </div>
    </div>
</div>

@if($group->students->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-[#003A6C] to-[#0084C5] border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-white">Students in this Group</h2>
            <p class="text-sm text-white/80 mt-1">Organized by programme ({{ $studentsByProgramme->count() }} programmes)</p>
        </div>

        <div class="p-6 space-y-4">
            @foreach($studentsByProgramme as $programme => $students)
                <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <!-- Programme Header -->
                    <button
                        @click="open = !open"
                        class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-[#003A6C] to-[#0084C5] text-white font-bold text-sm">
                                {{ $students->count() }}
                            </span>
                            <div class="text-left">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $programme }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $students->count() }} {{ Str::plural('student', $students->count()) }}</p>
                            </div>
                        </div>
                        <svg
                            :class="{ 'rotate-180': open }"
                            class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Students Table -->
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="border-t border-gray-200 dark:border-gray-700"
                    >
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Matric No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Company</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($students as $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-[#003A6C] to-[#0084C5] flex items-center justify-center text-white text-xs font-semibold">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $student->matric_no }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            @if($student->company)
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <span>{{ $student->company->company_name }}</span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                    No company assigned
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No students in this group</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Students will appear here once they are assigned to this group.</p>
    </div>
@endif
@endsection
