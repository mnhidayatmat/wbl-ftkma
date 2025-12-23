@props(['title', 'value', 'change' => null, 'changeType' => 'positive', 'icon' => null, 'iconBg' => 'bg-gray-100'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 ease-in-out p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-medium text-gray-600 dark:text-gray-400">{{ $title }}</h3>
        @if($icon)
            <div class="{{ $iconBg }} dark:bg-gray-700 rounded-lg p-2.5">
                {!! $icon !!}
            </div>
        @endif
    </div>
    
    <div class="mb-3">
        <p class="text-3xl font-bold text-[#00A86B] dark:text-[#00A86B]">{{ $value }}</p>
    </div>
    
    @if($change !== null)
        <div class="flex items-center space-x-1">
            @if($changeType === 'positive')
                <svg class="w-4 h-4 text-[#00A86B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span class="text-sm font-medium text-[#00A86B]">{{ $change }}</span>
            @else
                <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
                <span class="text-sm font-medium text-red-500 dark:text-red-400">{{ $change }}</span>
            @endif
            <span class="text-sm text-gray-500 dark:text-gray-400">vs previous period</span>
        </div>
    @endif
</div>

