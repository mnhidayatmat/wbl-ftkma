@extends('layouts.app')

@section('title', 'View Rubric - ' . $rubric->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4 flex items-center justify-between">
            <a href="{{ route('academic.fyp.rubrics.index') }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Rubric Templates
            </a>
            @if(!$rubric->is_locked)
                <a href="{{ route('academic.fyp.rubrics.edit', $rubric) }}" 
                   class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                    Edit Rubric
                </a>
            @endif
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Rubric Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 {{ $rubric->phase == 'Mid-Term' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }} text-sm font-medium rounded">
                            {{ $rubric->phase }}
                        </span>
                        <span class="px-3 py-1 {{ $rubric->assessment_type == 'Written' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }} text-sm font-medium rounded">
                            {{ $rubric->assessment_type }}
                        </span>
                        @if($rubric->is_locked)
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded">
                                🔒 Locked
                            </span>
                        @endif
                        @if(!$rubric->is_active)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm font-medium rounded">
                                Inactive
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $rubric->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400 font-mono">{{ $rubric->code }}</p>
                    @if($rubric->description)
                        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $rubric->description }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold {{ $isWeightValid ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ number_format($totalWeight, 2) }}%
                    </div>
                    <div class="text-sm {{ $isWeightValid ? 'text-green-600' : 'text-yellow-600' }}">
                        @if($isWeightValid)
                            ✓ Total Weight Valid
                        @else
                            ⚠️ Total Weight Invalid
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Levels Legend -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Performance Levels</h3>
            <div class="flex flex-wrap gap-2">
                @foreach(\App\Models\FYP\FypRubricTemplate::PERFORMANCE_LEVELS as $level => $label)
                    <span class="px-3 py-1.5 text-sm font-medium rounded-lg
                        @if($level == 1) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                        @elseif($level == 2) bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300
                        @elseif($level == 3) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                        @elseif($level == 4) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                        @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                        @endif">
                        {{ $level }} - {{ $label }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Rubric Grid by CLO -->
        @foreach($elementsByClo as $cloCode => $elements)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden mb-6">
                <div class="p-4 bg-[#003A6C] text-white">
                    <h2 class="font-semibold">{{ $cloCode }} Elements ({{ $elements->sum('weight_percentage') }}% Total)</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-48">Element</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-16">Weight</th>
                                @foreach(\App\Models\FYP\FypRubricTemplate::PERFORMANCE_LEVELS as $level => $label)
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase
                                        @if($level == 1) text-red-600 dark:text-red-400
                                        @elseif($level == 2) text-orange-600 dark:text-orange-400
                                        @elseif($level == 3) text-yellow-600 dark:text-yellow-400
                                        @elseif($level == 4) text-blue-600 dark:text-blue-400
                                        @else text-green-600 dark:text-green-400
                                        @endif">
                                        {{ $level }}<br><span class="text-[10px] normal-case">{{ $label }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($elements as $element)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-mono rounded">
                                                {{ $element->element_code }}
                                            </span>
                                        </div>
                                        <div class="font-medium text-gray-900 dark:text-gray-200">{{ $element->name }}</div>
                                        @if($element->description)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $element->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="font-semibold text-[#0084C5]">{{ number_format($element->weight_percentage, 1) }}%</span>
                                    </td>
                                    @foreach(\App\Models\FYP\FypRubricTemplate::PERFORMANCE_LEVELS as $level => $label)
                                        @php
                                            $descriptor = $element->levelDescriptors->firstWhere('level', $level);
                                        @endphp
                                        <td class="px-4 py-4 text-xs text-gray-600 dark:text-gray-400 
                                            @if($level == 1) bg-red-50 dark:bg-red-900/10
                                            @elseif($level == 2) bg-orange-50 dark:bg-orange-900/10
                                            @elseif($level == 3) bg-yellow-50 dark:bg-yellow-900/10
                                            @elseif($level == 4) bg-blue-50 dark:bg-blue-900/10
                                            @else bg-green-50 dark:bg-green-900/10
                                            @endif">
                                            @if($descriptor)
                                                <div class="max-w-[150px]">{{ Str::limit($descriptor->descriptor, 100) }}</div>
                                            @else
                                                <span class="text-gray-400">Not defined</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if($rubric->elements->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-2">No Elements Defined</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">This rubric template has no elements yet.</p>
                @if(!$rubric->is_locked)
                    <a href="{{ route('academic.fyp.rubrics.edit', $rubric) }}" class="text-[#0084C5] hover:text-[#003A6C] font-medium">
                        Add Elements →
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
