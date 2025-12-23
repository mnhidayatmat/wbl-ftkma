@extends('layouts.app')

@section('title', 'Assessment Management')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold heading-umpsa">Assessment Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Define and manage assessments for all courses</p>
            </div>
            <a href="{{ route('admin.assessments.create', ['course' => $courseCode]) }}" 
               class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
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

        <!-- Course Selection Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                @foreach($courseCodes as $code => $name)
                    <a href="{{ route('admin.assessments.index', ['course' => $code]) }}" 
                       class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm {{ $courseCode === $code ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        {{ $code }} - {{ $name }}
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- Total Percentage Warning -->
        @php
            $isPerfect = abs($totalPercentage - 100) < 0.01;
            $isOver = $totalPercentage > 100.01;
        @endphp
        <div class="mb-6 p-4 rounded-lg {{ $isOver ? 'bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300' : ($isPerfect ? 'bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-300') }}">
            <div class="flex items-center justify-between">
                <div>
                    <strong>Total Weight Percentage:</strong> {{ number_format($totalPercentage, 2) }}%
                    @if($isOver)
                        <span class="ml-2 text-red-600 dark:text-red-400">⚠️ Exceeds 100%</span>
                    @elseif($isPerfect)
                        <span class="ml-2 text-green-600 dark:text-green-400">✓ Perfect</span>
                    @else
                        <span class="ml-2 text-yellow-600 dark:text-yellow-400">⚠️ Below 100%</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assessments Table -->
        <div class="card-umpsa overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Assessment Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">CLO</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Weight %</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Evaluator</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($assessments as $assessment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#003A6C] dark:text-[#0084C5]">
                                    {{ $assessment->assessment_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $assessment->assessment_type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    <span class="px-2 py-1 bg-[#0084C5]/10 dark:bg-[#0084C5]/20 text-[#0084C5] rounded">{{ $assessment->clo_code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    <strong>{{ number_format($assessment->weight_percentage, 2) }}%</strong>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ ucfirst(str_replace('_', ' ', $assessment->evaluator_role)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($assessment->is_active)
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full text-xs font-semibold">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full text-xs font-semibold">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.assessments.edit', $assessment) }}" 
                                       class="text-[#0084C5] hover:text-[#003A6C] transition-colors">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.assessments.toggle-active', $assessment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" 
                                                class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors"
                                                title="{{ $assessment->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($assessment->is_active)
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.assessments.destroy', $assessment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No assessments defined for {{ $courseCodes[$courseCode] }} yet. 
                                    <a href="{{ route('admin.assessments.create', ['course' => $courseCode]) }}" class="text-[#0084C5] hover:text-[#003A6C] underline">Create one now</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

