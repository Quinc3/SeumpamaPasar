@props(['workspace' => 'toko'])

@php
    $navs = [
        'toko' => [
            ['route' => 'seller.dashboard', 'pattern' => 'seller.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ['route' => 'seller.products.index', 'pattern' => 'seller.products.*', 'label' => 'Produk', 'icon' => 'tag'],
            ['route' => 'seller.orders.index', 'pattern' => 'seller.orders.*', 'label' => 'Pesanan', 'icon' => 'truck'],
        ],
        'afiliasi' => [
            ['route' => 'affiliate.dashboard', 'pattern' => 'affiliate.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
        ],
    ];
    $items = $navs[$workspace] ?? [];
    $label = $workspace === 'toko' ? 'Toko Saya' : 'Afiliasi';
@endphp

<div data-tour-seller-nav class="bg-white border-b border-surface-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-1 overflow-x-auto">
            <span class="hidden sm:inline-flex items-center gap-1 text-xs font-medium text-surface-500 pr-3 border-r border-surface-200 mr-2 py-3 shrink-0">
                <x-icon name="arrow-path" size="xs" />
                {{ $label }}
            </span>
            @foreach ($items as $item)
                <a href="{{ route($item['route']) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-3 text-sm whitespace-nowrap border-b-2 transition
                          {{ request()->routeIs($item['pattern']) ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-surface-600 hover:text-surface-900' }}">
                    <x-icon :name="$item['icon']" size="sm" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>