@php
    $items = [
        ['href' => url('/'),          'label' => 'Beranda', 'icon' => 'home',          'active' => request()->is('/')],
        ['href' => route('catalog.index'), 'label' => 'Katalog', 'icon' => 'tag',      'active' => request()->routeIs('catalog.*')],
        ['href' => auth()->check() ? route('cart.index') : route('login'), 'label' => 'Keranjang', 'icon' => 'shopping-cart', 'active' => request()->routeIs('cart.*')],
        ['href' => auth()->check() ? route('buyer.dashboard') : route('login'), 'label' => 'Akun', 'icon' => 'user', 'active' => request()->is('akun*') || request()->is('profile*')],
    ];
@endphp

<nav {{ $attributes->merge(['class' => 'fixed bottom-0 inset-x-0 z-30 bg-white border-t border-surface-200']) }}>
    <div class="grid grid-cols-4">
        @foreach ($items as $item)
            <a href="{{ $item['href'] }}" class="flex flex-col items-center gap-0.5 py-2 {{ $item['active'] ? 'text-primary-700' : 'text-surface-600 hover:text-surface-900' }}">
                <x-icon :name="$item['icon']" size="md" />
                <span class="text-[10px] font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>