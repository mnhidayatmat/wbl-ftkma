@extends('layouts.app')

@section('title', 'Students')

@section('content')
<!-- Success/Error Messages -->
@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
    </div>
@endif

@if(session('warning'))
    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
        <p class="text-yellow-800 dark:text-yellow-200">{{ session('warning') }}</p>
        @if(session('import_errors'))
            <details class="mt-3">
                <summary class="cursor-pointer text-sm font-semibold text-yellow-900 dark:text-yellow-100">View Error Details</summary>
                <ul class="mt-2 ml-4 list-disc text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </details>
        @endif
    </div>
@endif

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold heading-umpsa">Students</h1>
        <div class="flex flex-col sm:flex-row gap-2">
            <!-- Import Excel Button -->
            <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2 whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Import Excel
            </button>
            <a href="{{ route('admin.students.create') }}" class="btn-umpsa-primary whitespace-nowrap">
                Add New Student
            </a>
        </div>
    </div>

    <!-- Tab Group Component -->
    <x-tab-group 
        :tabs="$tabs" 
        :activeTab="$activeTab" 
        :baseUrl="route('admin.students.index')"
    />

    <!-- Search Bar -->
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.students.index') }}" class="flex gap-2">
            @if(request('group'))
                <input type="hidden" name="group" value="{{ request('group') }}">
            @endif
            @if(request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            @if(request('sort_dir'))
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            @endif
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Search by name or matric number..." 
                    class="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary transition-colors"
                >
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button 
                type="submit" 
                class="px-6 py-2 bg-umpsa-primary text-white rounded-lg hover:bg-umpsa-secondary transition-colors font-semibold"
            >
                Search
            </button>
            @if(request('search'))
                <a 
                    href="{{ route('admin.students.index', array_merge($currentParams ?? [], ['search' => null])) }}" 
                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors font-semibold flex items-center"
                >
                    Clear
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block card-umpsa overflow-hidden">
    @php
        // Build sort URLs
        $buildSortUrl = function($column) use ($baseUrl, $currentParams, $sortBy, $sortDirection) {
            $params = array_merge($currentParams ?? [], [
                'sort_by' => $column,
                'sort_dir' => ($sortBy === $column && $sortDirection === 'asc') ? 'desc' : 'asc'
            ]);
            return $baseUrl . '?' . http_build_query($params);
        };

        // Get sort icon
        $getSortIcon = function($column) use ($sortBy, $sortDirection) {
            if ($sortBy !== $column) {
                return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';
            }
            return $sortDirection === 'asc' 
                ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>'
                : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
        };

        $columns = [
            ['label' => 'Name', 'sortable' => true, 'sortUrl' => $buildSortUrl('name'), 'sortIcon' => $getSortIcon('name')],
            ['label' => 'Matric No', 'sortable' => true, 'sortUrl' => $buildSortUrl('matric_no'), 'sortIcon' => $getSortIcon('matric_no')],
            ['label' => 'Programme', 'sortable' => true, 'sortUrl' => $buildSortUrl('programme'), 'sortIcon' => $getSortIcon('programme')],
            ['label' => 'Group', 'sortable' => false],
            ['label' => 'Company', 'sortable' => true, 'sortUrl' => $buildSortUrl('company_id'), 'sortIcon' => $getSortIcon('company_id')],
            ['label' => 'Actions', 'sortable' => false],
        ];
    @endphp

    <x-table :columns="$columns">
        @forelse($students as $student)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-umpsa-deep-blue dark:text-gray-200">
                    {{ $student->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                    {{ $student->matric_no }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                    {{ $student->programme }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($student->group)
                        @php
                            $groupNumber = (int) filter_var($student->group->name, FILTER_SANITIZE_NUMBER_INT);
                            $badgeVariant = 'group-' . (($groupNumber % 5) + 1);
                        @endphp
                        <x-badge :variant="$badgeVariant" size="sm">
                            {{ $student->group->name }}
                        </x-badge>
                    @else
                        <span class="text-gray-400 dark:text-gray-500">N/A</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                    {{ $student->company->company_name ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.students.show', $student) }}" class="text-umpsa-teal hover:text-umpsa-royal-blue transition-colors">View</a>
                        <a href="{{ route('admin.students.edit', ['student' => $student, 'page' => $students->currentPage(), 'group' => request('group')]) }}" class="text-umpsa-royal-blue hover:text-umpsa-deep-blue transition-colors">Edit</a>
                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-lg font-medium">No students found</p>
                        @if(request('search') || request('group'))
                            <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
                        @endif
                    </div>
                </td>
            </tr>
        @endforelse
    </x-table>
</div>

<!-- Mobile Card View -->
<div class="md:hidden space-y-4">
    @forelse($students as $student)
        <div class="card-umpsa p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-1">
                        {{ $student->name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $student->matric_no }}
                    </p>
                </div>
                @if($student->group)
                    @php
                        $groupNumber = (int) filter_var($student->group->name, FILTER_SANITIZE_NUMBER_INT);
                        $badgeVariant = 'group-' . (($groupNumber % 5) + 1);
                    @endphp
                    <x-badge :variant="$badgeVariant" size="sm">
                        {{ $student->group->name }}
                    </x-badge>
                @endif
            </div>
            
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm">
                    <span class="font-medium text-gray-700 dark:text-gray-300 w-24">Programme:</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $student->programme }}</span>
                </div>
                <div class="flex items-center text-sm">
                    <span class="font-medium text-gray-700 dark:text-gray-300 w-24">Company:</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $student->company->company_name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.students.show', $student) }}" class="flex-1 text-center px-3 py-2 bg-umpsa-primary text-white rounded-lg hover:bg-umpsa-secondary transition-colors text-sm font-semibold">
                    View
                </a>
                <a href="{{ route('admin.students.edit', ['student' => $student, 'page' => $students->currentPage(), 'group' => request('group')]) }}" class="flex-1 text-center px-3 py-2 bg-umpsa-secondary text-white rounded-lg hover:bg-umpsa-accent transition-colors text-sm font-semibold">
                    Edit
                </a>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-semibold">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="card-umpsa p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">No students found</p>
            @if(request('search') || request('group'))
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Try adjusting your filters</p>
            @endif
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($students->hasPages())
    <div class="mt-6">
        {{ $students->links() }}
    </div>
@endif

<!-- Import Modal -->
<div id="import-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200">Import Students from Excel</h3>
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.students.preview-import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Select Excel File
                    </label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Accepted formats: .xlsx, .xls, .csv (Max: 10MB)
                        </p>
                        <a href="{{ route('admin.students.template') }}" class="inline-flex items-center gap-1 text-xs font-medium text-umpsa-teal hover:text-umpsa-deep-blue dark:text-umpsa-accent dark:hover:text-umpsa-teal transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download Template
                        </a>
                    </div>
                </div>

                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                    <p class="text-sm text-blue-800 dark:text-blue-200 font-semibold mb-2">Excel File Requirements:</p>
                    <ul class="text-xs text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                        <li>First row must contain column headers</li>
                        <li>Required columns: <strong>name, matric_no</strong></li>
                        <li>Optional columns: programme, group, company, cgpa, ic_number, mobile_phone, parent_name, parent_phone_number, next_of_kin, next_of_kin_phone_number, home_address, background, skills, interests, preferred_industry, preferred_location</li>
                        <li>Empty fields will be left blank in the database</li>
                        <li>Group name must match existing group in system (if provided)</li>
                        <li>Skills can be comma-separated (e.g., "PHP, Laravel, MySQL")</li>
                    </ul>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-umpsa-primary">
                        Import Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
