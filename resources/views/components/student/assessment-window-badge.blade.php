@props(['window'])

@php
    $statusColors = [
        'open' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700',
        'closed' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900 dark:text-red-200 dark:border-red-700',
        'upcoming' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:border-blue-700',
        'disabled' => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600',
    ];
    $iconPaths = [
        'open' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'closed' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'upcoming' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'disabled' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
    ];
@endphp

<div class="border-2 {{ $statusColors[$window->status] ?? 'bg-gray-100 border-gray-200' }} rounded-lg p-4">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPaths[$window->status] ?? $iconPaths['disabled'] }}"></path>
            </svg>
            <span class="font-semibold">{{ strtoupper($window->evaluator_role) }} Evaluation</span>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$window->status] ?? 'bg-gray-100' }}">
            {{ ucfirst($window->status) }}
        </span>
    </div>
    @if($window->start_at && $window->end_at)
        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center mt-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            {{ \Carbon\Carbon::parse($window->start_at)->format('d M Y') }} - {{ \Carbon\Carbon::parse($window->end_at)->format('d M Y') }}
        </p>
    @endif
    @if($window->notes)
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2 italic">
            {{ $window->notes }}
        </p>
    @endif
</div>
