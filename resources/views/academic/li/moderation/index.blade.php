@extends('layouts.app')

@section('title', 'Industrial Training - Moderation')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Industrial Training - Moderation</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Review and apply moderation adjustments to student scores</p>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('academic.li.moderation.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('academic.li.moderation.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Moderation Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Supervisor Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">IC Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Final Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($studentsWithScores as $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $student->name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $student->matric_no }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $student->group->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                @if($student->moderation)
                                    <div class="flex items-center gap-1">
                                        <span class="text-gray-400 line-through text-xs">{{ number_format($student->supervisor_score, 2) }}%</span>
                                        <span class="text-[#0084C5] font-medium">{{ number_format($student->moderation->adjusted_supervisor_score, 2) }}%</span>
                                    </div>
                                @else
                                    {{ number_format($student->supervisor_score, 2) }}%
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                @if($student->moderation)
                                    <div class="flex items-center gap-1">
                                        <span class="text-gray-400 line-through text-xs">{{ number_format($student->ic_score, 2) }}%</span>
                                        <span class="text-[#00AEEF] font-medium">{{ number_format($student->moderation->adjusted_ic_score, 2) }}%</span>
                                    </div>
                                @else
                                    {{ number_format($student->ic_score, 2) }}%
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($student->moderation)
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-400 line-through">{{ number_format($student->final_score, 2) }}%</span>
                                        <span class="text-sm font-semibold text-[#0084C5]">{{ number_format($student->moderation->adjusted_final_score, 2) }}%</span>
                                    </div>
                                @else
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($student->final_score, 2) }}%</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($student->moderation)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        Moderated
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                        Not Moderated
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('academic.li.moderation.show', $student) }}"
                                   class="text-[#0084C5] hover:text-[#003A6C] transition-colors">
                                    {{ $student->moderation ? 'Edit' : 'Moderate' }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No students found.
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
