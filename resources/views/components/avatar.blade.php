@props(['name' => null, 'src' => null, 'size' => 'md'])

@php
    $sizes = [
        'xs' => 'h-6 w-6 text-xs',
        'sm' => 'h-8 w-8 text-sm',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-12 w-12 text-base',
        'xl' => 'h-16 w-16 text-lg',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $initials = '';
    if ($name) {
        $parts = preg_split('/\s+/', trim($name));
        $first = substr($parts[0] ?? '', 0, 1);
        $last = count($parts) > 1 ? substr(end($parts), 0, 1) : '';
        $initials = strtoupper($first . $last);
    }
@endphp

@if ($src)
    <img {{ $attributes->merge(['class' => "inline-block $sizeClass rounded-full object-cover bg-surface-100"]) }} src="{{ $src }}" alt="{{ $name ?? 'Avatar' }}">
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center justify-center $sizeClass rounded-full bg-primary-100 text-primary-700 font-semibold"]) }} aria-label="{{ $name ?? 'Avatar' }}">
        {{ $initials ?: '?' }}
    </span>
@endif