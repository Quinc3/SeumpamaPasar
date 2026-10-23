@props(['padding' => 'md'])

@php
    $paddings = [
        'none' => '',
        'sm'   => 'p-4',
        'md'   => 'p-6',
        'lg'   => 'p-8',
    ];
    $paddingClass = $paddings[$padding] ?? $paddings['md'];
@endphp

<div {{ $attributes->merge(['class' => "bg-white border border-surface-200 rounded-lg shadow-card $paddingClass"]) }}>
    {{ $slot }}
</div>