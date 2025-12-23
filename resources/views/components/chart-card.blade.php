@props(['title', 'chartId', 'chartType' => 'line', 'height' => '300'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200 ease-in-out p-6">
    <h3 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-6">{{ $title }}</h3>
    <div class="relative" style="height: {{ $height }}px;">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>

