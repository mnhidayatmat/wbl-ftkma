@extends('layouts.app')

@section('title', 'FYP Rubric Templates')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP Rubric Templates</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage rubric templates for FYP assessments</p>
            </div>
            <a href="{{ route('academic.fyp.rubrics.create') }}" 
               class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Rubric Template
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                        <option value="">All Types</option>
                        <option value="Written" {{ request('type') == 'Written' ? 'selected' : '' }}>Written Report</option>
                        <option value="Oral" {{ request('type') == 'Oral' ? 'selected' : '' }}>Oral Presentation</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phase</label>
                    <select name="phase" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                        <option value="">All Phases</option>
                        <option value="Mid-Term" {{ request('phase') == 'Mid-Term' ? 'selected' : '' }}>Mid-Term</option>
                        <option value="Final" {{ request('phase') == 'Final' ? 'selected' : '' }}>Final</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5]">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Rubric Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 {{ $template->is_active ? 'bg-[#003A6C]' : 'bg-gray-400' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-white">{{ $template->name }}</h3>
                                <p class="text-sm text-blue-200">{{ $template->code }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="px-2 py-1 text-xs font-bold rounded bg-yellow-400 text-yellow-900">
                                    {{ strtoupper($template->evaluator_role) }} - {{ number_format($template->component_marks, 0) }}%
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded {{ $template->phase == 'Mid-Term' ? 'bg-yellow-500 text-yellow-900' : 'bg-green-500 text-green-900' }}">
                                    {{ $template->phase }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-4">
                        @if($template->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $template->description }}</p>
                        @endif

                        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Elements:</span>
                                <span class="ml-1 font-medium text-gray-900 dark:text-gray-200">{{ $template->element_count }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Total Weight:</span>
                                <span class="ml-1 font-medium {{ $template->is_weight_valid ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($template->calculated_weight, 2) }}%
                                </span>
                            </div>
                        </div>

                        @if(!$template->is_weight_valid)
                            <div class="mb-4 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded text-xs text-yellow-700 dark:text-yellow-300">
                                ⚠️ Total weight must equal 100%
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if($template->is_locked)
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 text-xs rounded">
                                        🔒 Locked
                                    </span>
                                @endif
                                @if(!$template->is_active)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('academic.fyp.rubrics.show', $template) }}" 
                                   class="text-[#0084C5] hover:text-[#003A6C] transition-colors" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @if(!$template->is_locked)
                                    <a href="{{ route('academic.fyp.rubrics.edit', $template) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif
                                <form action="{{ route('academic.fyp.rubrics.duplicate', $template) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 transition-colors" title="Duplicate">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-2">No Rubric Templates Found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first rubric template.</p>
                        <a href="{{ route('academic.fyp.rubrics.create') }}" class="text-[#0084C5] hover:text-[#003A6C] font-medium">
                            Create Rubric Template →
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
