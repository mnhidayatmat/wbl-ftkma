@props(['title', 'score', 'maxScore', 'color' => 'primary', 'subtitle' => null])

@php
    $colorClasses = [
        'primary' => 'from-[#003A6C] to-[#0084C5]',
        'secondary' => 'from-[#0084C5] to-[#00AEEF]',
        'accent' => 'from-[#00AEEF] to-[#66C3FF]',
        'mixed' => 'from-[#003A6C] to-[#00AEEF]',
        'light' => 'from-[#0084C5] to-[#66C3FF]',
    ];
    $gradientClass = $colorClasses[$color] ?? $colorClasses['primary'];
    $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
@endphp

<div class="bg-gradient-to-br {{ $gradientClass }} rounded-xl p-6 text-white shadow-md hover:shadow-lg transition-shadow">
    <h3 class="text-sm font-semibold mb-2 opacity-90">{{ $title }}</h3>
    <p class="text-4xl font-bold mb-1">{{ number_format($score, 1) }}</p>
    <p class="text-xs opacity-75">out of {{ $maxScore }}</p>
    @if($subtitle)
        <p class="text-xs opacity-75 mt-1">{{ $subtitle }}</p>
    @endif
    <div class="mt-3 bg-white/20 rounded-full h-2">
        <div class="bg-white rounded-full h-2 transition-all duration-300"
             style="width: {{ min($percentage, 100) }}%"></div>
    </div>
</div>
