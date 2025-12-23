<!-- Filters -->
<div class="card-umpsa p-6 mb-6">
    <form method="GET" action="{{ route('admin.students.assign') }}" class="flex flex-wrap gap-4">
        <input type="hidden" name="tab" value="students">
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
            <a href="{{ route('admin.students.assign', ['tab' => 'students']) }}" class="ml-2 px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Current AT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Current IC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Assign AT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Assign IC</th>
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
                            {{ $student->group->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $student->academicTutor->name ?? 'Not Assigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $student->industryCoach->name ?? 'Not Assigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.students.assign.update', $student) }}" method="POST" class="inline" id="at-form-{{ $student->id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="at_id" id="at-hidden-{{ $student->id }}" value="{{ $student->at_id ?? '' }}">
                                <input type="hidden" name="ic_id" value="{{ $student->ic_id ?? '' }}">
                                <select name="at_id" onchange="document.getElementById('at-hidden-{{ $student->id }}').value = this.value || ''; this.form.submit();" 
                                        class="text-sm px-3 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Select AT --</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ $student->at_id == $lecturer->id ? 'selected' : '' }}>
                                            {{ $lecturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.students.assign.update', $student) }}" method="POST" class="inline" id="ic-form-{{ $student->id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="at_id" value="{{ $student->at_id ?? '' }}">
                                <input type="hidden" name="ic_id" id="ic-hidden-{{ $student->id }}" value="{{ $student->ic_id ?? '' }}">
                                <select name="ic_id" onchange="document.getElementById('ic-hidden-{{ $student->id }}').value = this.value || ''; this.form.submit();" 
                                        class="text-sm px-3 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Select IC --</option>
                                    @foreach($industryCoaches as $ic)
                                        <option value="{{ $ic->id }}" {{ $student->ic_id == $ic->id ? 'selected' : '' }}>
                                            {{ $ic->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <form action="{{ route('admin.students.assign.update', $student) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="at_id" value="">
                                <input type="hidden" name="ic_id" value="">
                                <button type="submit" onclick="return confirm('Remove all assignments for {{ $student->name }}?')" 
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Reset
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
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

