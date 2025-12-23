@props(['variant' => 'default', 'size' => 'md'])

@php
    $variants = [
        'default' => 'bg-umpsa-primary text-white',
        'group-1' => 'bg-umpsa-primary text-white',
        'group-2' => 'bg-umpsa-secondary text-white',
        'group-3' => 'bg-umpsa-accent text-white',
        'group-4' => 'bg-umpsa-primary text-white opacity-90',
        'group-5' => 'bg-umpsa-secondary text-white opacity-90',
        'success' => 'bg-green-500 text-white',
        'warning' => 'bg-yellow-500 text-white',
        'danger' => 'bg-red-500 text-white',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ];

    $classes = ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full font-semibold ' . $classes]) }}>
    {{ $slot }}
</span>

