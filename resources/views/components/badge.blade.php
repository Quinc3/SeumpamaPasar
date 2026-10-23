@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-gray-100 text-gray-800 border-gray-200',
        'info'    => 'bg-blue-50 text-blue-800 border-blue-200',
        'success' => 'bg-green-50 text-green-800 border-green-200',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
        'danger'  => 'bg-red-50 text-red-800 border-red-200',
    ];
    $classes = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 text-xs font-medium border rounded-full $classes"]) }}>
    {{ $slot }}
</span>