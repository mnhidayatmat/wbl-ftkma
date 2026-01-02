@extends('layouts.app')

@section('title', 'Recruitment Pool')

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

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold heading-umpsa">Recruitment Pool</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Filter, export, and share student profiles with recruiters</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('recruitment.handovers.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Handover History
            </a>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6"
         x-data="{ showAdvanced: {{ (request('skills') || request('interests') || request('preferred_industry') || request('preferred_location')) ? 'true' : 'false' }} }">
        <form method="GET" action="{{ route('recruitment.pool.index') }}" id="filter-form">
            <!-- Row 1: Programme (Compact Pills) + Search + CGPA -->
            <div class="space-y-4">
                <!-- Programme Filter - Compact Checkbox Pills -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Programme</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($programmes as $programme)
                            @php $shortCode = \App\Models\Student::getProgrammeShortCode($programme); @endphp
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="programmes[]" value="{{ $programme }}"
                                    {{ in_array($programme, request('programmes', [])) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <span class="px-3 py-1.5 text-sm font-medium rounded-full border-2 transition-all
                                    peer-checked:bg-umpsa-primary peer-checked:text-white peer-checked:border-umpsa-primary
                                    bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600
                                    hover:border-umpsa-secondary hover:bg-umpsa-secondary/10">
                                    {{ $shortCode }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Row 2: Search, CGPA Range, Resume Status, Placement Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" name="search"
                            value="{{ request('search') }}"
                            placeholder="Name or Matric No"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                    </div>

                    <!-- CGPA Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CGPA Range</label>
                        <div class="flex gap-2 items-center">
                            <input type="number" name="cgpa_min" step="0.01" min="0" max="4"
                                value="{{ request('cgpa_min') }}"
                                placeholder="Min"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                            <span class="text-gray-500 dark:text-gray-400">-</span>
                            <input type="number" name="cgpa_max" step="0.01" min="0" max="4"
                                value="{{ request('cgpa_max') }}"
                                placeholder="Max"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                        </div>
                    </div>

                    <!-- Resume Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Resume Status</label>
                        <select name="resume_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                            <option value="">All</option>
                            <option value="approved" {{ request('resume_status') === 'approved' ? 'selected' : '' }}>Recommended</option>
                            <option value="with_resume" {{ request('resume_status') === 'with_resume' ? 'selected' : '' }}>Has Resume</option>
                        </select>
                    </div>

                    <!-- Exclude Students with Offers Toggle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Availability</label>
                        <div class="flex items-center h-[42px]">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="exclude_offers" value="0">
                                <input type="checkbox" name="exclude_offers" value="1"
                                       {{ request('exclude_offers', '1') === '1' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-umpsa-primary/30 dark:peer-focus:ring-umpsa-secondary/30 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-umpsa-primary"></div>
                                <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">Exclude students with offers</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Toggle Advanced Filters -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <button type="button" @click="showAdvanced = !showAdvanced"
                        class="text-sm text-umpsa-primary hover:text-umpsa-secondary font-medium inline-flex items-center gap-1">
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        <span x-text="showAdvanced ? 'Hide Advanced Filters' : 'Show Advanced Filters'"></span>
                    </button>
                </div>

                <!-- Advanced Filters Section -->
                <div x-show="showAdvanced" x-collapse class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Skills Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skills</label>
                            <input type="text" name="skills[]"
                                value="{{ implode(', ', request('skills', [])) }}"
                                placeholder="e.g., Python, SQL"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Comma-separated</p>
                        </div>

                        <!-- Interests Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Interests</label>
                            @if($allInterests->count() > 0)
                                <select name="interests[]" multiple class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary" size="3">
                                    @foreach($allInterests as $interest)
                                        <option value="{{ $interest }}" {{ in_array($interest, request('interests', [])) ? 'selected' : '' }}>
                                            {{ $interest }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ctrl/Cmd + click for multiple</p>
                            @else
                                <input type="text" name="interests[]"
                                    value="{{ implode(', ', request('interests', [])) }}"
                                    placeholder="e.g., Automotive, Manufacturing"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Comma-separated</p>
                            @endif
                        </div>

                        <!-- Preferred Industry Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Industry</label>
                            @if($allIndustries->count() > 0)
                                <select name="preferred_industry[]" multiple class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary" size="3">
                                    @foreach($allIndustries as $industry)
                                        <option value="{{ $industry }}" {{ in_array($industry, request('preferred_industry', [])) ? 'selected' : '' }}>
                                            {{ $industry }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ctrl/Cmd + click for multiple</p>
                            @else
                                <input type="text" name="preferred_industry[]"
                                    value="{{ implode(', ', request('preferred_industry', [])) }}"
                                    placeholder="e.g., Oil & Gas, Aerospace"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Comma-separated</p>
                            @endif
                        </div>

                        <!-- Preferred Location Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Location</label>
                            @if($allLocations->count() > 0)
                                <select name="preferred_location[]" multiple class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary" size="3">
                                    @foreach($allLocations as $location)
                                        <option value="{{ $location }}" {{ in_array($location, request('preferred_location', [])) ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ctrl/Cmd + click for multiple</p>
                            @else
                                <input type="text" name="preferred_location[]"
                                    value="{{ implode(', ', request('preferred_location', [])) }}"
                                    placeholder="e.g., Kuala Lumpur, Johor"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Comma-separated</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-4 py-2 bg-umpsa-primary hover:bg-umpsa-secondary text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ route('recruitment.pool.index') }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition-colors">
                    Clear All
                </a>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-2 mb-4">
        <button onclick="bulkAction('excel')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" id="export-excel-btn" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export to Excel
        </button>
        <button onclick="bulkAction('pdf')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" id="export-pdf-btn" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Export Catalog (PDF)
        </button>
        <button onclick="bulkAction('resumes')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" id="download-resumes-btn" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Resumes (ZIP)
        </button>
        <button onclick="openEmailModal()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" id="email-recruiter-btn" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            Email to Recruiter
        </button>
    </div>

    <!-- Selected Count -->
    <div id="selection-info" class="mb-4 text-sm text-gray-600 dark:text-gray-400 hidden">
        <span id="selected-count">0</span> student(s) selected
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-umpsa-primary focus:ring-umpsa-primary">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <a href="{{ route('recruitment.pool.index', array_merge(request()->all(), ['sort_by' => 'matric_no', 'sort_dir' => request('sort_by') === 'matric_no' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-umpsa-primary">
                                Matric No
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <a href="{{ route('recruitment.pool.index', array_merge(request()->all(), ['sort_by' => 'group', 'sort_dir' => request('sort_by') === 'group' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-umpsa-primary">
                                Group
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <a href="{{ route('recruitment.pool.index', array_merge(request()->all(), ['sort_by' => 'name', 'sort_dir' => request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-umpsa-primary">
                                Name
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <a href="{{ route('recruitment.pool.index', array_merge(request()->all(), ['sort_by' => 'programme', 'sort_dir' => request('sort_by') === 'programme' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-umpsa-primary">
                                Programme
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <a href="{{ route('recruitment.pool.index', array_merge(request()->all(), ['sort_by' => 'cgpa', 'sort_dir' => request('sort_by') === 'cgpa' && request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-umpsa-primary">
                                CGPA
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Skills</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Resume</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300 text-umpsa-primary focus:ring-umpsa-primary">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $student->matric_no }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                @if($student->group)
                                    <span class="px-2 py-1 bg-umpsa-primary/10 text-umpsa-primary dark:bg-umpsa-secondary/20 dark:text-umpsa-accent text-xs font-semibold rounded">
                                        {{ $student->group->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No Group</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $student->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" title="{{ $student->programme }}">{{ $student->programme_short }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                <span class="font-semibold">{{ $student->cgpa ? number_format($student->cgpa, 2) : 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                @if($student->skills && count($student->skills) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($student->skills, 0, 3) as $skill)
                                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded">{{ $skill }}</span>
                                        @endforeach
                                        @if(count($student->skills) > 3)
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">+{{ count($student->skills) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">No skills listed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($student->resumeInspection?->status === 'RECOMMENDED')
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">Approved</span>
                                @elseif($student->resume_pdf_path)
                                    <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs rounded-full">Submitted</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded-full">None</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($student->placementTracking)
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">{{ $student->placementTracking->status }}</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded-full">Not Started</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('admin.students.show', $student->id) }}" class="text-umpsa-primary hover:text-umpsa-secondary font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No students found matching your criteria.
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

<!-- Email to Recruiter Modal -->
<div id="email-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Send to Recruiter</h3>
            <form id="email-form" method="POST" action="{{ route('recruitment.email-recruiter') }}">
                @csrf
                <div id="email-student-ids"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company *</label>
                        <select name="company_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                            <option value="">Select Company</option>
                            @foreach(\App\Models\Company::orderBy('company_name')->get() as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recruiter Email(s) *</label>
                        <input type="text" name="recruiter_emails" required placeholder="recruiter@company.com, hr@company.com" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary">
                        <p class="text-xs text-gray-500 mt-1">Separate multiple emails with commas</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message (Optional)</label>
                        <textarea name="message" rows="4" placeholder="Additional message to the recruiter..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-umpsa-primary"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Include Attachments</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="include_excel" value="1" checked class="rounded border-gray-300 text-umpsa-primary focus:ring-umpsa-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Excel List</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="include_pdf" value="1" checked class="rounded border-gray-300 text-umpsa-primary focus:ring-umpsa-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">PDF Catalog</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="include_resumes" value="1" checked class="rounded border-gray-300 text-umpsa-primary focus:ring-umpsa-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Resumes (ZIP)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit" class="px-4 py-2 bg-umpsa-primary hover:bg-umpsa-secondary text-white font-semibold rounded-lg transition-colors">
                        Send Email
                    </button>
                    <button type="button" onclick="closeEmailModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Select All Checkbox
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectionInfo();
});

// Individual Checkboxes
document.querySelectorAll('.student-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectionInfo);
});

function updateSelectionInfo() {
    const checked = document.querySelectorAll('.student-checkbox:checked');
    const count = checked.length;

    document.getElementById('selected-count').textContent = count;
    document.getElementById('selection-info').classList.toggle('hidden', count === 0);

    // Enable/disable action buttons
    const buttons = ['export-excel-btn', 'export-pdf-btn', 'download-resumes-btn', 'email-recruiter-btn'];
    buttons.forEach(btnId => {
        document.getElementById(btnId).disabled = count === 0;
    });
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
}

function bulkAction(action) {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one student');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';

    if (action === 'excel') {
        form.action = '{{ route("recruitment.export.excel") }}';
    } else if (action === 'pdf') {
        form.action = '{{ route("recruitment.export.pdf") }}';
    } else if (action === 'resumes') {
        form.action = '{{ route("recruitment.export.resumes") }}';
    }

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

function openEmailModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one student');
        return;
    }

    // Add hidden inputs for selected students
    const container = document.getElementById('email-student-ids');
    container.innerHTML = '';
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        container.appendChild(input);
    });

    document.getElementById('email-modal').classList.remove('hidden');
}

function closeEmailModal() {
    document.getElementById('email-modal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('email-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEmailModal();
    }
});
</script>
@endpush
