@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
    <x-page-wrapper max="5xl">
        <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Keranjang</h1>

        @if (session('status'))
            <x-alert variant="success">Keranjang diperbarui.</x-alert>
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
            <div x-data="cartForm()" class="space-y-4">

                {{-- Bulk toolbar --}}
                <div class="flex flex-wrap items-center justify-between gap-2 bg-white border border-surface-200 rounded-lg p-3">
                    <label class="inline-flex items-center gap-2 text-sm text-surface-700 cursor-pointer">
                        <input type="checkbox" x-on:change="toggleAll($event.target.checked)"
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        Pilih semua
                    </label>
                    <span class="text-sm text-surface-600"><span x-text="selected.length"></span> produk dipilih</span>
                </div>

                @error('checkout') <x-alert variant="danger">{{ $message }}</x-alert> @enderror
                @error('cart') <x-alert variant="danger">{{ $message }}</x-alert> @enderror

                {{-- Grup per seller --}}
                @foreach ($groups as $sellerId => $group)
                    <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
                        {{-- Header toko --}}
                        <div class="flex items-center justify-between gap-2 px-4 py-3 bg-surface-50 border-b border-surface-200">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                       x-on:change="toggleSeller({{ $sellerId }}, $event.target.checked)"
                                       class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                <x-icon name="home" size="sm" class="text-surface-600" />
                                <span class="text-sm font-semibold text-surface-900">{{ $group['seller']->store_name }}</span>
                            </label>
                            <span class="text-xs text-surface-500">{{ $group['items']->count() }} produk</span>
                        </div>

                        {{-- Item per toko --}}
                        <div class="divide-y divide-surface-100">
                            @foreach ($group['items'] as $li)
                                @php $item = $li['cart_item']; $p = $item->product; @endphp
                                <div class="p-3 sm:p-4 flex items-start gap-3">
                                    <div class="pt-1">
                                    <input type="checkbox"
                                               value="{{ $item->id }}"
                                               data-seller="{{ $sellerId }}"
                                               data-line-total="{{ $li['line_total'] }}"
                                               x-on:change="syncItem($event.target)"
                                               class="cart-item-checkbox rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                    </div>
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
                                        <div class="mt-1 flex items-center gap-2 flex-wrap text-sm">
                                            <x-price :amount="$li['line_total']" class="font-semibold" />
                                            @if ($li['applies_tier']) <x-badge variant="primary">Grosir</x-badge> @endif
                                            @if ($isMember && $p->sales_channel === \App\Enums\SalesChannel::B2C) <x-badge variant="success">Member</x-badge> @endif
                                        </div>
                                        <p class="text-xs text-surface-500 mt-0.5">Rp {{ number_format($li['unit_price'], 0, ',', '.') }} × {{ $item->quantity }}</p>
                                    </div>
                                    <div class="flex flex-col items-end justify-between gap-2 shrink-0">
                                        {{-- Update form (standalone, tidak nested) --}}
                                        <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-1">
                                            @csrf @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="{{ max(1, $li['min_qty']) }}" max="{{ $p->stock }}"
                                                   class="w-16 text-sm text-right rounded-md border-surface-300 bg-white focus:border-primary-500 focus:ring-primary-500">
                                            <button type="submit" class="p-1.5 text-surface-500 hover:text-surface-900" aria-label="Perbarui">
                                                <x-icon name="arrow-path" size="sm" />
                                            </button>
                                        </form>
                                        {{-- Remove form (standalone, tidak nested) --}}
                                        <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-700">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between px-4 py-3 bg-surface-50 border-t border-surface-200 text-sm">
                            <span class="text-surface-600">Subtotal toko</span>
                            <x-price :amount="$group['subtotal']" class="font-semibold" />
                        </div>
                    </div>
                @endforeach

                {{-- Total + Checkout (tanpa form, button via JS) --}}
                <x-card>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <p class="text-sm text-surface-600">Total</p>
                            <span class="text-xl font-semibold text-surface-900" x-text="formatRp(totalDisplay)">Rp 0</span>
                        </div>
                        <button type="button" x-on:click="goCheckout()" x-bind:disabled="selected.length === 0"
                                class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-primary-700 text-white text-sm font-semibold rounded-md hover:bg-primary-800 disabled:opacity-50 disabled:cursor-not-allowed">
                            <x-icon name="credit-card" size="sm" />
                            Checkout (<span x-text="selected.length"></span>)
                        </button>
                    </div>
                </x-card>
            </div>
        @endif
    </x-page-wrapper>

    @if ($items->isNotEmpty())
    <script>
            function cartForm() {
                return {
                    selected: [],

                    get totalDisplay() {
                        return this.selected.reduce((sum, id) => {
                            const cb = document.querySelector(`.cart-item-checkbox[value="${id}"]`);
                            const lineTotal = cb ? parseFloat(cb.dataset.lineTotal) : 0;
                            return sum + (isNaN(lineTotal) ? 0 : lineTotal);
                        }, 0);
                    },

                    formatRp(n) {
                        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
                    },

                    syncItem(cb) {
                        const id = parseInt(cb.value);
                        const idx = this.selected.indexOf(id);
                        if (cb.checked && idx === -1) this.selected.push(id);
                        if (!cb.checked && idx !== -1) this.selected.splice(idx, 1);
                    },

                    toggleSeller(sellerId, checked) {
                        document.querySelectorAll(`.cart-item-checkbox[data-seller="${sellerId}"]`).forEach(cb => {
                            cb.checked = checked;
                            const id = parseInt(cb.value);
                            const idx = this.selected.indexOf(id);
                            if (checked && idx === -1) this.selected.push(id);
                            if (!checked && idx !== -1) this.selected.splice(idx, 1);
                        });
                    },

                    toggleAll(checked) {
                        document.querySelectorAll('.cart-item-checkbox').forEach(cb => { cb.checked = checked; });
                        if (checked) {
                            this.selected = Array.from(document.querySelectorAll('.cart-item-checkbox')).map(cb => parseInt(cb.value));
                        } else {
                            this.selected = [];
                        }
                    },

                    goCheckout() {
                        if (this.selected.length === 0) return;
                        const params = this.selected.map(id => 'cart_item_ids[]=' + id).join('&');
                        window.location.href = '{{ route('checkout.index') }}?' + params;
                    }
                };
            }
        </script>
    @endif
@endsection