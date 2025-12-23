@extends('layouts.app')

@section('title', 'PPE AT Evaluation - ' . ($group->name ?? 'Students'))

@section('content')
<div class="py-6">
        <div class="max-w-7xl mx-auto px-10">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <a href="{{ route('academic.ppe.groups.index') }}" class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Groups
                    </a>
                    <p class="text-gray-600 dark:text-gray-400">
                        Select a student to enter Academic Tutor marks (40% contribution)
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-[#003A6C]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">
                                        {{ $student->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $student->matric_no }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $student->company->company_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @can('edit-at-marks', $student)
                                        <a href="{{ route('academic.ppe.lecturer.show', $student) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors">
                                            Enter Marks / Edit Marks
                                        </a>
                                        @else
                                        <a href="{{ route('academic.ppe.lecturer.show', $student) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded-lg transition-colors">
                                            View Marks
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                @if(auth()->user()->isLecturer() && !auth()->user()->isAdmin())
                                                    No students assigned to you in this group.
                                                @else
                                                    No students found in this group.
                                                @endif
                                            </p>
                                            @if(auth()->user()->isLecturer() && !auth()->user()->isAdmin())
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Please contact an administrator to assign students to you as Academic Tutor.
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
@endsection
