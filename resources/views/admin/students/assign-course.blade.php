<!-- Filters -->
<div class="card-umpsa p-6 mb-6">
    <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">{{ $courseName }} ({{ $courseCode }})</h2>
    <form method="GET" action="{{ route('admin.students.assign') }}" class="flex flex-wrap gap-4">
        <input type="hidden" name="course" value="{{ $courseCode }}">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Name or Matric No..."
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Filter by Group</label>
            <select name="group" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                <option value="">All Groups</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.students.assign', ['course' => $courseCode]) }}" class="ml-2 px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Students Table -->
<div class="card-umpsa overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-[#003A6C]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Current Lecturer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                        @if($courseCode === 'FYP')
                            ASSIGN ACADEMIC TUTOR (AT)
                        @elseif($courseCode === 'Industrial Training')
                            ASSIGN SUPERVISOR LI
                        @else
                            ASSIGN LECTURER
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($students as $student)
                    @php
                        $assignment = $studentCourseAssignments->get($student->id) ?? null;
                        $currentLecturer = $assignment?->lecturer;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">
                            {{ $student->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $student->matric_no }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $student->group->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            @if($courseCode === 'FYP')
                                {{ $currentLecturer?->name ?? 'Not Assigned' }} @if($currentLecturer) <span class="text-xs text-gray-500">(AT)</span> @endif
                            @elseif($courseCode === 'Industrial Training')
                                {{ $currentLecturer?->name ?? 'Not Assigned' }} @if($currentLecturer) <span class="text-xs text-gray-500">(Supervisor LI)</span> @endif
                            @else
                                {{ $currentLecturer?->name ?? 'Not Assigned' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.students.course-assign.update', $student) }}" method="POST" class="inline" id="lecturer-form-{{ $student->id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="course_type" value="{{ $courseCode }}">
                                <select name="lecturer_id" onchange="this.form.submit();" 
                                        class="text-sm px-3 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                    @if($courseCode === 'FYP')
                                        <option value="">-- Select Academic Tutor (AT) --</option>
                                    @elseif($courseCode === 'Industrial Training')
                                        <option value="">-- Select Supervisor LI --</option>
                                    @else
                                        <option value="">-- Select Lecturer --</option>
                                    @endif
                                    @foreach($courseLecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ $currentLecturer?->id == $lecturer->id ? 'selected' : '' }}>
                                            {{ $lecturer->name }} ({{ $lecturer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($assignment)
                                <form action="{{ route('admin.students.course-assign.remove', $assignment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Remove assignment for {{ $student->name }} from {{ $courseName }}?')" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Remove
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $students->links() }}
    </div>
</div>

