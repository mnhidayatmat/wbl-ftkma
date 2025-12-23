@extends('layouts.app')

@section('title', 'Industrial Training - Result Finalisation')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Industrial Training - Result Finalisation</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Lock student results to prevent further edits by evaluators</p>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Students</div>
                <div class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $totalStudents }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Finalised</div>
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $finalisedCount }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Not Finalised</div>
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $notFinalisedCount }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('academic.li.finalisation.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                        <option value="">All</option>
                        <option value="finalised" {{ request('status') === 'finalised' ? 'selected' : '' }}>Finalised</option>
                        <option value="not_finalised" {{ request('status') === 'not_finalised' ? 'selected' : '' }}>Not Finalised</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('academic.li.finalisation.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Finalisation Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Company</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Finalised By</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Finalised At</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $student)
                        @php
                            $finalisation = $finalisations->get($student->id);
                        @endphp
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
                                {{ $student->company->company_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($finalisation && $finalisation->is_finalised)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        Finalised
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                        Not Finalised
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $finalisation && $finalisation->finaliser ? $finalisation->finaliser->name : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $finalisation && $finalisation->finalised_at ? $finalisation->finalised_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                @if(!$finalisation || !$finalisation->is_finalised)
                                    <form action="{{ route('academic.li.finalisation.student', $student) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to finalise results for {{ $student->name }}? This action cannot be undone and will prevent further edits by evaluators.');">
                                        @csrf
                                        <button type="submit"
                                                class="text-[#0084C5] hover:text-[#003A6C] transition-colors">
                                            Finalise
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">Locked</span>
                                @endif
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

        <!-- Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Bulk Finalisation</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Finalise by Group -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Finalise by Group</h3>
                    <form action="{{ route('academic.li.finalisation.group') }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to finalise results for all students in the selected group? This action cannot be undone.');">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Group</label>
                            <select name="group_id"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                <option value="">Select a group...</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                            <textarea name="notes"
                                      rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                            Finalise Group
                        </button>
                    </form>
                </div>

                <!-- Finalise Entire Course -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Finalise Entire Course</h3>
                    <form action="{{ route('academic.li.finalisation.course') }}"
                          method="POST"
                          onsubmit="return confirm('WARNING: This will finalise results for ALL students in the Industrial Training course. This action cannot be undone. Are you absolutely sure?');">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                            <textarea name="notes"
                                      rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="confirm"
                                       value="1"
                                       required
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    I confirm that I want to finalise all results
                                </span>
                            </label>
                        </div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                            Finalise Entire Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
