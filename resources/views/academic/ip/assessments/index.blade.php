@php
    $routePrefix = 'academic.ip.assessments';
@endphp
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
            <a href="{{ route($routePrefix . '.create') }}" 
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
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-[#003A6C] dark:bg-gray-800">
                            <tr>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Assessment Name
                                </th>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    CLO
                                </th>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Weight %
                                </th>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">
                                    Evaluator
                                </th>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 sm:px-6 lg:px-8 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($assessments as $assessmentName => $assessmentGroup)
                                @php
                                    $firstAssessment = $assessmentGroup->first();
                                    
                                    // Aggregate all CLOs from all assessments in the group
                                    $allClos = $assessmentGroup->flatMap(function($assessment) {
                                        if ($assessment->clos && $assessment->clos->count() > 0) {
                                            return $assessment->clos->map(function($clo) use ($assessment) {
                                                $clo->evaluator_role = $assessment->evaluator_role;
                                                $clo->is_active = $assessment->is_active;
                                                $clo->assessment_id = $assessment->id;
                                                return $clo;
                                            });
                                        } else {
                                            // Fallback for assessments without explicit CLO records
                                            return collect([(object)[
                                                'id' => null,
                                                'clo_code' => $assessment->clo_code, 
                                                'weight_percentage' => $assessment->weight_percentage, 
                                                'evaluator_role' => $assessment->evaluator_role, 
                                                'is_active' => $assessment->is_active,
                                                'assessment_id' => $assessment->id
                                            ]]);
                                        }
                                    });

                                    // Calculate total group weight
                                    $groupTotalWeight = $allClos->sum('weight_percentage');
                                @endphp
                                <!-- Assessment Header Row -->
                                <tr class="bg-gray-50 dark:bg-gray-800/50 border-l-4 border-[#0084C5]">
                                    <td class="px-4 sm:px-6 lg:px-8 py-4" colspan="7">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-3">
                                                        <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $assessmentName }}</h3>
                                                        <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-md text-xs font-semibold">
                                                            {{ $firstAssessment->assessment_type }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            {{ $allClos->count() }} {{ $allClos->count() === 1 ? 'CLO' : 'CLOs' }}
                                                        </span>
                                                        <span class="text-xs text-gray-400">•</span>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            Total: {{ number_format($groupTotalWeight, 2) }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('academic.ip.assessments.edit', $firstAssessment) }}" 
                                                   class="p-2 text-[#0084C5] hover:bg-[#0084C5]/10 dark:hover:bg-[#0084C5]/20 rounded-lg transition-colors" title="Edit Assessment">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('academic.ip.assessments.destroy', $firstAssessment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this assessment and all its CLOs? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete Assessment">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- CLO Rows -->
                                @foreach($allClos as $clo)
                                    @php
                                        $cloCode = $clo->clo_code ?? '';
                                        $weight = $clo->weight_percentage ?? 0;
                                        $evaluator = $clo->evaluator_role ?? $firstAssessment->evaluator_role ?? '';
                                        $isActive = isset($clo->is_active) ? $clo->is_active : ($firstAssessment->is_active ?? true);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-4 sm:px-6 lg:px-8 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-[#0084C5]/40"></div>
                                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $assessmentName }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 lg:px-8 py-3.5">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $firstAssessment->assessment_type }}</span>
                                        </td>
                                        <td class="px-4 sm:px-6 lg:px-8 py-3.5">
                                            <span class="inline-flex items-center px-2.5 py-1 bg-gradient-to-r from-[#0084C5]/10 to-[#0084C5]/5 dark:from-[#0084C5]/20 dark:to-[#0084C5]/10 text-[#0084C5] dark:text-[#00A8E8] rounded-md text-xs font-semibold border border-[#0084C5]/20">
                                                {{ $cloCode }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 lg:px-8 py-3.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($weight, 2) }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">%</span>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 lg:px-8 py-3.5 hidden md:table-cell">
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-xs font-medium">
                                                {{ ucfirst(str_replace('_', ' ', $evaluator)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 lg:px-8 py-3.5">
                                            @if($isActive)
                                                <span class="inline-flex items-center px-2.5 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-full text-xs font-semibold">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full text-xs font-semibold">
                                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 lg:px-8 py-3.5">
                                            <div class="flex items-center gap-1.5">
                                                <form action="{{ route('academic.ip.assessments.toggle-active', $clo->assessment_id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                                                            title="{{ $isActive ? 'Deactivate' : 'Activate' }}">
                                                        @if($isActive)
                                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>
                                                <a href="{{ route('academic.ip.assessments.edit', $clo->assessment_id) }}" 
                                                   class="p-1.5 text-[#0084C5] hover:bg-[#0084C5]/10 rounded-md transition-colors"
                                                   title="Edit Item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                    </div>
                                </td>
                            </tr>
                                @endforeach
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 sm:px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">No assessments defined yet</p>
                                        <a href="{{ route($routePrefix . '.create') }}" 
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

