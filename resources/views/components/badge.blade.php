@props(['variant' => 'neutral', 'style' => 'soft'])

@php
    $soft = [
        'neutral' => 'bg-surface-100 text-surface-700 border-surface-200',
        'primary' => 'bg-primary-50 text-primary-700 border-primary-200',
        'info'    => 'bg-blue-50 text-blue-700 border-blue-200',
        'success' => 'bg-green-50 text-green-700 border-green-200',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
        'danger'  => 'bg-red-50 text-red-700 border-red-200',
    ];
    $solid = [
        'neutral' => 'bg-surface-700 text-white border-surface-700',
        'primary' => 'bg-primary-700 text-white border-primary-700',
        'info'    => 'bg-blue-600 text-white border-blue-600',
        'success' => 'bg-green-600 text-white border-green-600',
        'warning' => 'bg-amber-500 text-white border-amber-500',
        'danger'  => 'bg-red-600 text-white border-red-600',
    ];
    $classMap = $style === 'solid' ? $solid : $soft;
    $classes = $classMap[$variant] ?? $classMap['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 text-xs font-medium border rounded-full $classes"]) }}>
    {{ $slot }}
</span>