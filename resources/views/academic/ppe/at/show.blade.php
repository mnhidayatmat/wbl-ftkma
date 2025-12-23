@extends('layouts.app')

@section('title', 'PPE AT Evaluation - ' . $student->name)

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold heading-umpsa">AT Evaluation</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $student->name }} ({{ $student->matric_no }})</p>
        </div>
        <a href="{{ route('academic.ppe.lecturer.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition-colors">Back</a>
    </div>
</div>

<form action="{{ route('academic.ppe.lecturer.store', $student) }}" method="POST">
    @csrf
    
    <div class="card-umpsa p-6 mb-6">
        <h2 class="text-xl font-semibold text-umpsa-primary mb-4">CLO1 Assessments (40% Total)</h2>
        
        <div class="space-y-4">
            @forelse($settings as $setting)
                @php
                    $mark = $marks->get($setting->id);
                    $currentMark = $mark?->mark ?? '';
                    $contribution = $mark && $mark->mark !== null 
                        ? ($mark->mark / $setting->max_mark) * $setting->weight 
                        : 0;
                @endphp
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-umpsa-deep-blue dark:text-gray-200">{{ $setting->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                CLO: {{ $setting->clo }} | Weight: {{ number_format($setting->weight, 2) }}% | Max: {{ number_format($setting->max_mark, 2) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Contribution</div>
                            <div class="text-lg font-bold text-umpsa-secondary">{{ number_format($contribution, 2) }}%</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @can('edit-at-marks', $student)
                        <input 
                            type="number" 
                            name="marks[{{ $setting->id }}]" 
                            value="{{ $currentMark }}"
                            step="0.01"
                            min="0"
                            max="{{ $setting->max_mark }}"
                            class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal"
                            placeholder="Enter mark"
                        >
                        @else
                        <input 
                            type="number" 
                            value="{{ $currentMark }}"
                            step="0.01"
                            readonly
                            disabled
                            class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md"
                        >
                        @endcan
                        <span class="text-sm text-gray-600 dark:text-gray-400">/ {{ number_format($setting->max_mark, 2) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">No assessment settings found. Please configure settings first.</p>
            @endforelse
        </div>
    </div>

    <div class="card-umpsa p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-umpsa-primary">Total AT Contribution</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Out of 40%</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-umpsa-secondary">{{ number_format($totalContribution, 2) }}%</div>
            </div>
        </div>
    </div>

    @can('edit-at-marks', $student)
    <!-- Sticky Save Button -->
    <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 -mx-4 sm:-mx-6 lg:mx-0 shadow-lg mt-6">
        <div class="max-w-5xl mx-auto flex justify-end px-4 sm:px-6 lg:px-10">
            <button type="submit" 
                    class="px-6 py-3 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors shadow-md">
                Save Marks
            </button>
        </div>
    </div>
    @endcan
</form>
@endsection

