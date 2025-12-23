@extends('layouts.app')

@section('title', 'WBL Student Assignment')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                {{ $courses[$activeCourse] ?? 'WBL' }} - Assign Students
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Assign students to lecturers, supervisors, and industry coaches</p>
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

        <!-- Tab Navigation (only show if multiple courses) -->
        @if(count($courses) > 1)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md mb-6" x-data="{ activeTab: '{{ $activeCourse }}' }">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px overflow-x-auto">
                    @foreach($courses as $code => $name)
                    <button @click="activeTab = '{{ $code }}'; window.location.href = '{{ route('wbl.assign.index', ['course' => $code]) }}'"
                            :class="activeTab === '{{ $code }}' ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        {{ $name }}
                    </button>
                    @endforeach
                </nav>
            </div>
        </div>
        @endif

        <!-- Main Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md mb-6">
            <div class="p-6">
                <!-- Filters -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <form method="GET" action="{{ request()->url() }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Name or Matric No..."
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Filter by Group</label>
                            <select name="group" 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white">
                                <option value="">All Groups</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" 
                                    class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                                Filter
                            </button>
                            <a href="{{ request()->url() }}" 
                               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>

                @if($assignmentData['assignment_type'] === 'single_lecturer')
                <!-- Single Lecturer Assignment (IP, OSH, PPE) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-[#003A6C] dark:text-white mb-4">
                        Assign {{ $courses[$activeCourse] ?? $activeCourse }} Lecturer
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        All students taking {{ $courses[$activeCourse] ?? $activeCourse }} will be assigned to the selected lecturer.
                    </p>
                    @if(auth()->user()->isAdmin())
                    <form action="{{ match($activeCourse) {
                        'PPE' => route('ppe.assign-students.update'),
                        'IP' => route('ip.assign-students.update'),
                        'OSH' => route('osh.assign-students.update'),
                        default => route('ppe.assign-students.update'),
                    } }}" method="POST" class="flex items-end gap-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="course_type" value="{{ $activeCourse }}">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Select Lecturer
                            </label>
                            <select name="assignee_id" 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white">
                                <option value="">-- Select Lecturer --</option>
                                @foreach($assignmentData['assignees'] as $assignee)
                                    <option value="{{ $assignee->id }}" 
                                            {{ $assignmentData['current_lecturer'] == $assignee->id ? 'selected' : '' }}>
                                        {{ $assignee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                            Assign Lecturer
                        </button>
                        @if($assignmentData['current_lecturer'])
                        <form action="{{ match($activeCourse) {
                            'PPE' => route('ppe.assign-students.remove'),
                            'IP' => route('ip.assign-students.remove'),
                            'OSH' => route('osh.assign-students.remove'),
                            default => route('ppe.assign-students.remove'),
                        } }}" method="POST" onsubmit="return confirm('Remove lecturer assignment?')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="course_type" value="{{ $activeCourse }}">
                            <button type="submit" 
                                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                Remove
                            </button>
                        </form>
                        @endif
                    </form>
                    @else
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Current Lecturer:</strong>
                            @if($assignmentData['current_lecturer'])
                                @php
                                    $lecturer = \App\Models\User::find($assignmentData['current_lecturer']);
                                @endphp
                                <span class="font-semibold">{{ $lecturer?->name ?? 'N/A' }}</span>
                            @else
                                <span class="text-gray-600 dark:text-gray-400">Not assigned</span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Student List Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-white">
                            Students ({{ $students->total() }})
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-[#003A6C]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Current Assignment</th>
                                    @if(auth()->user()->isAdmin() && $assignmentData['assignment_type'] === 'individual')
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($students as $student)
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
                                        @php
                                            $currentAssignee = null;
                                            if ($assignmentData['assignment_type'] === 'single_lecturer') {
                                                // For single lecturer courses, show the course lecturer
                                                if ($assignmentData['current_lecturer']) {
                                                    $currentAssignee = \App\Models\User::find($assignmentData['current_lecturer']);
                                                }
                                            } elseif ($activeCourse === 'FYP' && $student->at_id) {
                                                $currentAssignee = \App\Models\User::find($student->at_id);
                                            } elseif ($activeCourse === 'IC' && $student->ic_id) {
                                                $currentAssignee = \App\Models\User::find($student->ic_id);
                                            } elseif ($activeCourse === 'LI') {
                                                $assignment = \App\Models\StudentCourseAssignment::where('student_id', $student->id)
                                                    ->where('course_type', 'Industrial Training')
                                                    ->first();
                                                $currentAssignee = $assignment?->lecturer;
                                            }
                                        @endphp
                                        @if($currentAssignee)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $currentAssignee->name }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Not Assigned
                                            </span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->isAdmin() && $assignmentData['assignment_type'] === 'individual')
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @php
                                            $updateRoute = match($activeCourse) {
                                                'FYP' => route('fyp.assign-students.update', $student),
                                                'LI' => route('li.assign-students.update', $student),
                                                'IC' => route('ic.assign-students.update', $student),
                                                default => route('fyp.assign-students.update', $student),
                                            };
                                            $removeRoute = match($activeCourse) {
                                                'FYP' => route('fyp.assign-students.remove', $student),
                                                'LI' => route('li.assign-students.remove', $student),
                                                'IC' => route('ic.assign-students.remove', $student),
                                                default => route('fyp.assign-students.remove', $student),
                                            };
                                        @endphp
                                        <form action="{{ $updateRoute }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="course_type" value="{{ $activeCourse }}">
                                            <select name="assignee_id" 
                                                    class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                                                <option value="">-- Select --</option>
                                                @foreach($assignmentData['assignees'] as $assignee)
                                                    <option value="{{ $assignee->id }}" 
                                                            {{ isset($assignmentData['assignments'][$student->id]) && $assignmentData['assignments'][$student->id] == $assignee->id ? 'selected' : '' }}>
                                                        {{ $assignee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                Assign
                                            </button>
                                            @if(isset($assignmentData['assignments'][$student->id]) && $assignmentData['assignments'][$student->id])
                                            <form action="{{ $removeRoute }}" method="POST" class="inline" onsubmit="return confirm('Remove assignment?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="course_type" value="{{ $activeCourse }}">
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                    Remove
                                                </button>
                                            </form>
                                            @endif
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdmin() && $assignmentData['assignment_type'] === 'individual' ? '5' : '4' }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No students found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        {{ $students->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

