@props(['href' => '#', 'icon' => null, 'active' => false])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-2.5 px-3 py-2 rounded-md text-sm transition ' . ($active ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-surface-700 hover:bg-surface-100')]) }}>
    @if ($icon)
        <x-icon :name="$icon" size="md" class="{{ $active ? 'text-primary-700' : 'text-surface-500' }}" />
    @endif
    <span>{{ $slot }}</span>
</a>