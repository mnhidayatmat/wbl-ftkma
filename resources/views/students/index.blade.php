@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold heading-umpsa">Students</h1>
        <a href="{{ route('students.create') }}" class="btn-umpsa-primary whitespace-nowrap">
            Add New Student
        </a>
    </div>

    <!-- Tab Group Component -->
    <x-tab-group 
        :tabs="$tabs" 
        :activeTab="$activeTab" 
        :baseUrl="route('students.index')"
    />

    <!-- Search Bar -->
    <div class="mb-6">
        <form method="GET" action="{{ route('students.index') }}" class="flex gap-2">
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
                    href="{{ route('students.index', array_merge($currentParams ?? [], ['search' => null])) }}" 
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
                        <a href="{{ route('students.show', $student) }}" class="text-umpsa-teal hover:text-umpsa-royal-blue transition-colors">View</a>
                        <a href="{{ route('students.edit', $student) }}" class="text-umpsa-royal-blue hover:text-umpsa-deep-blue transition-colors">Edit</a>
                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline">
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
                <a href="{{ route('students.show', $student) }}" class="flex-1 text-center px-3 py-2 bg-umpsa-primary text-white rounded-lg hover:bg-umpsa-secondary transition-colors text-sm font-semibold">
                    View
                </a>
                <a href="{{ route('students.edit', $student) }}" class="flex-1 text-center px-3 py-2 bg-umpsa-secondary text-white rounded-lg hover:bg-umpsa-accent transition-colors text-sm font-semibold">
                    Edit
                </a>
                <form action="{{ route('students.destroy', $student) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
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
@endsection
