@props(['variant' => 'info', 'dismissible' => false])

@php
    $variants = [
        'info'    => [
            'wrapper' => 'bg-blue-50 border-blue-200 text-blue-900',
            'icon'    => 'information-circle',
            'iconCls' => 'text-blue-600',
        ],
        'success' => [
            'wrapper' => 'bg-green-50 border-green-200 text-green-900',
            'icon'    => 'check-circle',
            'iconCls' => 'text-green-600',
        ],
        'warning' => [
            'wrapper' => 'bg-amber-50 border-amber-200 text-amber-900',
            'icon'    => 'exclamation-triangle',
            'iconCls' => 'text-amber-600',
        ],
        'danger'  => [
            'wrapper' => 'bg-red-50 border-red-200 text-red-900',
            'icon'    => 'x-circle',
            'iconCls' => 'text-red-600',
        ],
    ];
    $config = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "border rounded-md px-4 py-3 flex items-start gap-3 text-sm {$config['wrapper']}"]) }}>
    <x-icon :name="$config['icon']" size="md" class="flex-shrink-0 mt-0.5 {{ $config['iconCls'] }}" />
    <div class="flex-1 min-w-0">
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button type="button" class="flex-shrink-0 -m-1 p-1 rounded hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-current" aria-label="Tutup notifikasi">
            <x-icon name="x-mark" size="sm" />
        </button>
    @endif
</div>