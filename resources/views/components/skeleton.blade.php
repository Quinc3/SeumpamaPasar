@props(['width' => 'full', 'height' => '4', 'rounded' => 'md'])

@php
    $widths = ['full' => 'w-full', '3/4' => 'w-3/4', '1/2' => 'w-1/2', '1/3' => 'w-1/3', '1/4' => 'w-1/4'];
    $heights = ['3' => 'h-3', '4' => 'h-4', '5' => 'h-5', '6' => 'h-6', '8' => 'h-8', '10' => 'h-10', '12' => 'h-12'];
    $roundeds = ['none' => '', 'sm' => 'rounded-sm', 'md' => 'rounded-md', 'lg' => 'rounded-lg', 'full' => 'rounded-full'];

    $classes = 'animate-pulse bg-surface-200 '
        . ($widths[$width] ?? 'w-full') . ' '
        . ($heights[$height] ?? 'h-4') . ' '
        . ($roundeds[$rounded] ?? 'rounded-md');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}></div>