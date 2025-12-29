@extends('layouts.app')

@section('title', 'FYP - Assign Students')

@section('content')
<div class="py-4 sm:py-6" x-data="assignStudents()">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-4 sm:mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Assign Students</h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-2">Assign Academic Tutors to FYP students</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-[#003A6C]">
                <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Students</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['fully_assigned'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Fully Assigned</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['at_assigned'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">AT Assigned</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['ic_assigned'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">IC Assigned</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-amber-500">
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['unassigned'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">IC Pending</div>
            </div>
        </div>

        <!-- Info Card about IC Assignment -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300">About Industry Coach (IC) Assignment</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                        For FYP module, Industry Coaches are assigned by students during their registration process.
                        The IC assignment shown below is set by each student when they register for the Final Year Project.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6">
            <form method="GET" action="{{ route('academic.fyp.assign-students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
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
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assignment Status</label>
                    <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="fully_assigned" {{ request('status') == 'fully_assigned' ? 'selected' : '' }}>Fully Assigned</option>
                        <option value="at_assigned" {{ request('status') == 'at_assigned' ? 'selected' : '' }}>AT Assigned</option>
                        <option value="ic_assigned" {{ request('status') == 'ic_assigned' ? 'selected' : '' }}>IC Assigned</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partially Assigned</option>
                        <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Show</label>
                    <select name="per_page"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors min-h-[42px]">
                        Filter
                    </button>
                    <a href="{{ route('academic.fyp.assign-students.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors min-h-[42px] flex items-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Assignment Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6" x-show="selectedStudents.length > 0" x-cloak>
            <form id="bulk-form" method="POST" action="{{ route('academic.fyp.assign-students.bulk-update') }}">
                @csrf
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#0084C5] text-white font-bold text-sm" x-text="selectedStudents.length"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">students selected</span>
                    </div>

                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Bulk AT Select -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Assign Academic Tutor</label>
                            <select name="bulk_at_id" x-model="bulkAtId"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm">
                                <option value="">-- Select AT --</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Apply Button -->
                        <div class="flex items-end">
                            <template x-for="id in selectedStudents" :key="id">
                                <input type="hidden" name="student_ids[]" :value="id">
                            </template>
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors"
                                    :disabled="!bulkAtId"
                                    :class="{ 'opacity-50 cursor-not-allowed': !bulkAtId }">
                                Apply Bulk Assignment
                            </button>
                        </div>
                    </div>

                    <button type="button" @click="clearSelection()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox"
                                       @change="toggleAll($event.target.checked)"
                                       :checked="allSelected"
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Matric No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Programme</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden md:table-cell">Group</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Academic Tutor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Industry Coach</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden lg:table-cell">Company</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <!-- Checkbox -->
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                           value="{{ $student->id }}"
                                           @change="toggleStudent({{ $student->id }}, $event.target.checked)"
                                           :checked="selectedStudents.includes({{ $student->id }})"
                                           class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                                </td>

                                <!-- Student Name -->
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">{{ $student->name }}</div>
                                </td>

                                <!-- Matric No -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->matric_no }}</div>
                                </td>

                                <!-- Programme -->
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->programme ?? 'N/A' }}</div>
                                </td>

                                <!-- Group -->
                                <td class="px-4 py-3 hidden md:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->group->name ?? 'N/A' }}</div>
                                </td>

                                <!-- AT Dropdown -->
                                <td class="px-4 py-3">
                                    <form action="{{ route('academic.fyp.assign-students.update', $student) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="at_id"
                                                onchange="this.form.submit()"
                                                class="w-full min-w-[150px] px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white {{ $student->at_id ? 'bg-green-50 dark:bg-green-900/20' : 'bg-amber-50 dark:bg-amber-900/20' }}">
                                            <option value="">-- Select AT --</option>
                                            @foreach($lecturers as $lecturer)
                                                <option value="{{ $lecturer->id }}" {{ $student->at_id == $lecturer->id ? 'selected' : '' }}>
                                                    {{ $lecturer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <!-- Industry Coach (Student-assigned) -->
                                <td class="px-4 py-3">
                                    @if($student->industryCoach)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ $student->industryCoach->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pending
                                        </span>
                                        @if($student->company && $student->company->pic_name)
                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                PIC: {{ $student->company->pic_name }}
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                <!-- Company (IC's Company) -->
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    @if($student->industryCoach && $student->industryCoach->company)
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $student->industryCoach->company->company_name }}</div>
                                        @if($student->industryCoach->company->address)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-start gap-1">
                                                <svg class="w-3 h-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>{{ $student->industryCoach->company->address }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No students found</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($students, 'links'))
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function assignStudents() {
        return {
            selectedStudents: [],
            bulkAtId: '',

            get allSelected() {
                const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                return checkboxes.length > 0 && this.selectedStudents.length === checkboxes.length;
            },

            toggleAll(checked) {
                const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                this.selectedStudents = [];

                if (checked) {
                    checkboxes.forEach(cb => {
                        cb.checked = true;
                        this.selectedStudents.push(parseInt(cb.value));
                    });
                } else {
                    checkboxes.forEach(cb => cb.checked = false);
                }
            },

            toggleStudent(studentId, checked) {
                if (checked) {
                    if (!this.selectedStudents.includes(studentId)) {
                        this.selectedStudents.push(studentId);
                    }
                } else {
                    this.selectedStudents = this.selectedStudents.filter(id => id !== studentId);
                }
            },

            clearSelection() {
                this.selectedStudents = [];
                this.bulkAtId = '';
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => cb.checked = false);
            }
        }
    }
</script>
@endpush
@endsection
