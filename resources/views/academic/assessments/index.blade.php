@extends('layouts.app')

@section('title', $courseName . ' – Assessment Management')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $courseName }} – Assessment Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Define and manage assessments for {{ $courseName }}</p>
            </div>
            <a href="{{ route('academic.ppe.assessments.create') }}" 
               class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Assessment
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

        <!-- Total Percentage Warning -->
        @php
            $isPerfect = abs($totalPercentage - 100) < 0.01;
            $isOver = $totalPercentage > 100.01;
        @endphp
        <div class="mb-5 p-3 rounded-lg border {{ $isOver ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : ($isPerfect ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800') }}">
            <div class="flex items-center gap-2.5">
                @if($isOver)
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @elseif($isPerfect)
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @endif
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium {{ $isOver ? 'text-red-900 dark:text-red-200' : ($isPerfect ? 'text-green-900 dark:text-green-200' : 'text-amber-900 dark:text-amber-200') }}">Total Weight Percentage:</span>
                    <span class="text-sm font-semibold {{ $isOver ? 'text-red-700 dark:text-red-300' : ($isPerfect ? 'text-green-700 dark:text-green-300' : 'text-amber-700 dark:text-amber-300') }}">{{ number_format($totalPercentage, 2) }}%</span>
                    @if($isOver)
                        <span class="text-xs {{ $isOver ? 'text-red-700 dark:text-red-300' : '' }}">(Exceeds 100%)</span>
                    @elseif($isPerfect)
                        <span class="text-xs {{ $isPerfect ? 'text-green-700 dark:text-green-300' : '' }}">(Perfect)</span>
                    @else
                        <span class="text-xs {{ $isOver ? '' : 'text-amber-700 dark:text-amber-300' }}">(Below 100%)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assessments Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-[#003A6C]">
                            <tr>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Assessment Name</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Type</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">CLO</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Weight %</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider hidden md:table-cell">Evaluator</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th class="px-3 sm:px-4 lg:px-6 py-2 sm:py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($assessments as $assessment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">{{ $assessment->assessment_name }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ $assessment->assessment_type }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <span class="px-2 py-1 bg-[#0084C5]/10 dark:bg-[#0084C5]/20 text-[#0084C5] rounded text-xs font-medium">{{ $assessment->clo_code }}</span>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($assessment->weight_percentage, 2) }}%</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4 hidden md:table-cell">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $assessment->evaluator_role)) }}</div>
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    @if($assessment->is_active)
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full text-xs font-semibold">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full text-xs font-semibold">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('academic.ppe.assessments.edit', $assessment) }}" 
                                           class="text-[#0084C5] hover:text-[#003A6C] transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('academic.ppe.assessments.toggle-active', $assessment) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors"
                                                    title="{{ $assessment->is_active ? 'Deactivate' : 'Activate' }}">
                                                @if($assessment->is_active)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                        <form action="{{ route('academic.ppe.assessments.destroy', $assessment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 sm:px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">No assessments defined yet</p>
                                        <a href="{{ route('academic.ppe.assessments.create') }}" 
                                           class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Create Assessment
                                        </a>
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
</div>
@endsection

