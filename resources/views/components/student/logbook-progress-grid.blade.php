@props(['logbooks', 'totalMonths' => 6])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @for($month = 1; $month <= $totalMonths; $month++)
        @php
            $logbook = $logbooks->firstWhere('month', $month);
            $scoreLabels = [
                10 => 'Excellent',
                9 => 'Excellent',
                8 => 'Very Good',
                7 => 'Good',
                6 => 'Average',
                5 => 'Below Average',
                4 => 'Below Average',
                3 => 'Poor',
                2 => 'Poor',
                1 => 'Poor',
            ];
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-2
                    {{ $logbook && $logbook->score ? 'border-green-500 dark:border-green-600' : 'border-gray-300 dark:border-gray-600' }}">
            <div class="flex justify-between items-start mb-2">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Month {{ $month }}</span>
                @if($logbook && $logbook->score)
                    <span class="px-2 py-1 text-xs font-bold rounded
                                 {{ $logbook->score >= 8 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                    ($logbook->score >= 6 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                        {{ $logbook->score }}/10 - {{ $scoreLabels[$logbook->score] ?? 'N/A' }}
                    </span>
                @else
                    <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                        Not Evaluated
                    </span>
                @endif
            </div>
            @if($logbook && $logbook->remarks)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-3">
                    {{ $logbook->remarks }}
                </p>
            @endif
            @if($logbook && $logbook->evaluator)
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                    Evaluated by: {{ $logbook->evaluator->name }}
                </p>
            @endif
        </div>
    @endfor
</div>

@if($logbooks->isNotEmpty())
    <div class="mt-4 bg-[#003A6C]/5 dark:bg-[#003A6C]/20 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Completion</p>
                <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                    {{ $logbooks->count() }}/{{ $totalMonths }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Average Score</p>
                <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                    {{ $logbooks->avg('score') ? number_format($logbooks->avg('score'), 1) : '-' }}/10
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Score</p>
                <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                    {{ $logbooks->sum('score') ? number_format($logbooks->sum('score'), 0) : '-' }}/60
                </p>
            </div>
        </div>
    </div>
@endif
