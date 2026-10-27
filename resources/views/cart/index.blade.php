@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <h1 class="text-xl sm:text-2xl font-semibold text-surface-900 mb-4">Keranjang</h1>

        @if (session('status'))
            <x-alert variant="success" class="mb-4">Keranjang diperbarui.</x-alert>
        @endif

        @if ($items->isEmpty())
            <x-card padding="none">
                <x-empty-state icon="shopping-cart" title="Keranjang masih kosong" description="Mulai belanja dari katalog.">
                    <x-slot:action>
                        <a href="{{ route('catalog.index') }}">
                            <x-primary-button type="button">Jelajahi Katalog</x-primary-button>
                        </a>
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @else
            <div class="bg-white border border-surface-200 rounded-lg divide-y divide-surface-200">
            @foreach ($lineItems as $li)
                        @php $item = $li['cart_item']; $p = $item->product; @endphp
                        <div class="p-3 sm:p-4 flex gap-3">
                            <a href="{{ route('catalog.show', $p->slug) }}" class="shrink-0 w-20 h-20 bg-surface-100 rounded-md overflow-hidden">
                                @php $img = $p->images->first(); @endphp
                                @if ($img)
                                    <img src="{{ Storage::url($img->path) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-surface-300">
                                        <x-icon name="tag" size="lg" />
                                    </div>
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('catalog.show', $p->slug) }}" class="text-sm font-medium text-surface-900 hover:text-primary-700 line-clamp-2">{{ $p->name }}</a>
                                <p class="text-xs text-surface-500 mt-0.5">{{ $p->sellerProfile->store_name }}</p>

                                <div class="mt-1.5 flex flex-col gap-0.5">
                                    {{-- Harga satuan --}}
                                    <div class="flex items-center gap-2 flex-wrap text-xs text-surface-600">
                                        @if ($li['applies_tier'])
                                            <span class="text-primary-700 font-medium">
                                                Grosir Rp {{ number_format($li['unit_price'] / ($isMember ? 0.95 : 1), 0, ',', '.') }}/pcs
                                            </span>
                                            @if ($isMember)
                                                <span>· Member 5% → <span class="font-medium text-surface-900">Rp {{ number_format($li['unit_price'], 0, ',', '.') }}/pcs</span></span>
                                            @endif
                                        @else
                                            <span>Rp {{ number_format($li['unit_price'], 0, ',', '.') }}/pcs</span>
                                            @if ($isMember)
                                                <span class="text-green-700">(Member 5%)</span>
                                            @endif
                                        @endif
                                    </div>

                                    {{-- Subtotal per baris --}}
                                    <div class="text-sm font-semibold text-surface-900 mt-0.5">
                                        <x-price :amount="$li['line_total']" class="text-sm" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end justify-between gap-2">
                                <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="{{ max(1, $li['min_qty']) }}" max="{{ $p->stock }}"
                                        class="w-16 text-sm text-right rounded-md border-surface-300 bg-white focus:border-primary-500 focus:ring-primary-500">
                                    <button type="submit" class="p-1.5 text-surface-500 hover:text-surface-900" aria-label="Perbarui">
                                        <x-icon name="arrow-path" size="sm" />
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
            </div>

            <x-card class="mt-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-surface-600">Subtotal</p>
                        <x-price :amount="$subtotal" class="text-xl" />
                    </div>
                    <a href="{{ route('checkout.index') }}">
    <x-primary-button type="button">
        <x-icon name="credit-card" size="sm" class="mr-1.5" />
        Checkout
    </x-primary-button>
</a>
                </div>
            </x-card>
        @endif
    </div>
@endsection