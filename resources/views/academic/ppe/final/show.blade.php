@extends('layouts.app')

@section('title', 'Professional Practice and Ethics (BTD4122) - ' . $student->name)

@section('content')
<div class="py-6">
        <div class="max-w-4xl mx-auto px-10">
            <div class="mb-6">
                <a href="{{ route('academic.ppe.final.index', ['group' => $student->group_id]) }}" 
                   class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Students
                </a>
                <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Professional Practice and Ethics (BTD4122)</h1>
                <p class="text-base text-gray-600 dark:text-gray-400 font-medium">{{ $student->name }} ({{ $student->matric_no }})</p>
            </div>

            <!-- AT Contribution (40%) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Academic Tutor (AT) Contribution - 40%</h2>
                
                <div class="space-y-3 mb-4">
                    @foreach($atBreakdown as $item)
                        <div class="flex items-center justify-between p-4 bg-[#F4F7FC] dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-[#003A6C] dark:text-[#0084C5]">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ number_format($item['raw_mark'], 2) }} / {{ number_format($item['max_mark'], 2) }} 
                                    (Weight: {{ number_format($item['weight'], 2) }}%)
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-[#0084C5]">{{ number_format($item['contribution'], 2) }}%</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex items-center justify-between">
                        <p class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Total AT Contribution</p>
                        <p class="text-2xl font-bold text-[#0084C5]">{{ number_format($atTotalContribution, 2) }}%</p>
                    </div>
                </div>
            </div>

            <!-- IC Contribution (60%) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Industry Coach (IC) Contribution - 60%</h2>
                
                <div class="space-y-3 mb-4">
                    @foreach($icBreakdown as $item)
                        <div class="flex items-center justify-between p-4 bg-[#F4F7FC] dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-[#003A6C] dark:text-[#0084C5]">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Rubric: {{ $item['rubric_value'] }} / {{ $item['max_rubric'] }} 
                                    (Weight: {{ number_format($item['weight'], 2) }}%)
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-[#0084C5]">{{ number_format($item['contribution'], 2) }}%</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex items-center justify-between">
                        <p class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Total IC Contribution</p>
                        <p class="text-2xl font-bold text-[#0084C5]">{{ number_format($icTotalContribution, 2) }}%</p>
                    </div>
                </div>
            </div>

            <!-- Final Score -->
            <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] rounded-xl shadow-md p-8 text-center">
                <p class="text-white/90 text-sm mb-2">Final Score</p>
                <p class="text-5xl font-bold text-white mb-2">{{ number_format($finalScore, 2) }}%</p>
                <p class="text-white/80 text-sm">AT ({{ number_format($atTotalContribution, 2) }}%) + IC ({{ number_format($icTotalContribution, 2) }}%)</p>
            </div>
        </div>
    </div>
@endsection
