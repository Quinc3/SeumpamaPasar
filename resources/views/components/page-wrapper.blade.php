@props(['max' => '7xl', 'spacing' => 'space-y-6'])

<div {{ $attributes->merge(['class' => "max-w-{$max} mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 {$spacing}"]) }}>
    {{ $slot }}
</div>