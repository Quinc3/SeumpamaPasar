@props(['amount', 'class' => ''])

<span {{ $attributes->merge(['class' => "font-semibold text-surface-900 $class"]) }}>
    Rp {{ number_format((float) $amount, 0, ',', '.') }}
</span>