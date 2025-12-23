<!-- Add New Lecturer Assignment -->
<div class="card-umpsa p-6 mb-6">
    <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Assign Lecturer to Course</h2>
    <form action="{{ route('admin.lecturers.assign.store') }}" method="POST" class="flex flex-wrap gap-4">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select Lecturer</label>
            <select name="lecturer_id" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                <option value="">-- Select Lecturer --</option>
                @foreach($lecturers as $lecturer)
                    <option value="{{ $lecturer->id }}">{{ $lecturer->name }} ({{ $lecturer->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select Course</label>
            <select name="course_type" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                <option value="">-- Select Course --</option>
                @foreach($courses as $code => $name)
                    <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded-lg transition-colors">
                Assign
            </button>
        </div>
    </form>
</div>

<!-- Lecturer Course Assignments Table -->
<div class="card-umpsa overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-[#003A6C]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Course Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Assigned Lecturers</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($courses as $code => $name)
                    @php
                        $assignments = $lecturerAssignments->get($code, collect());
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">
                            {{ $name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $code }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            @if($assignments->count() > 0)
                                <div class="space-y-2">
                                    @foreach($assignments as $assignment)
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                                            <span>{{ $assignment->lecturer->name }} ({{ $assignment->lecturer->email }})</span>
                                            <form action="{{ route('admin.lecturers.assign.remove', $assignment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('Remove {{ $assignment->lecturer->name }} from {{ $code }}?')"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-xs">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 italic">No lecturers assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <form action="{{ route('admin.lecturers.assign.store') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="course_type" value="{{ $code }}">
                                <select name="lecturer_id" onchange="if(this.value) { this.form.submit(); }" 
                                        class="text-sm px-3 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                    <option value="">Quick Assign...</option>
                                    @foreach($lecturers as $lecturer)
                                        @if(!$assignments->contains('lecturer_id', $lecturer->id))
                                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

