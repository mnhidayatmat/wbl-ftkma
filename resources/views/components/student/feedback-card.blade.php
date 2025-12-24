@props(['title', 'content', 'author' => null, 'date' => null, 'type' => 'default'])

@php
    $typeColors = [
        'success' => 'border-green-500 bg-green-50 dark:bg-green-900/20 dark:border-green-600',
        'warning' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-600',
        'info' => 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-600',
        'default' => 'border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600',
    ];
@endphp

<div class="border-l-4 {{ $typeColors[$type] ?? $typeColors['default'] }} rounded-lg p-4 shadow-sm">
    <div class="flex items-start justify-between mb-2">
        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h4>
        @if($date)
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
            </span>
        @endif
    </div>
    <div class="text-sm text-gray-700 dark:text-gray-300 mb-2 whitespace-pre-line">
        {{ $content }}
    </div>
    @if($author)
        <div class="flex items-center mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $author }}</p>
        </div>
    @endif
</div>
