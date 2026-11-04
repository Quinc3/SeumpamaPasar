@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Detail Pesanan</h1>
            <a href="{{ route('buyer.orders.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← Pesanan</a>
        </div>

        @if (session('status') === 'order-completed')
            <x-alert variant="success">Pesanan selesai. Terima kasih telah berbelanja — Anda dapat memberi ulasan di bawah.</x-alert>
        @endif
        @if (session('status') === 'review-submitted')
            <x-alert variant="success">Terima kasih, ulasan Anda sudah terkirim.</x-alert>
        @endif
        @if (session('error'))
            <x-alert variant="danger">{{ session('error') }}</x-alert>
        @endif

        {{-- Header order + invoice --}}
        <x-card>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-surface-500 font-mono">{{ $order->checkout->reference }}</p>
                    <p class="text-sm font-semibold text-surface-900 mt-1">{{ $order->sellerProfile->store_name }}</p>
                    <p class="text-xs text-surface-500 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <x-status-badge :status="$order->status->value" />
            </div>

            <div class="mt-3 pt-3 border-t border-surface-200 flex flex-wrap gap-2">
                <a href="{{ route('buyer.orders.invoice', $order->id) }}">
                    <x-secondary-button type="button">
                        <x-icon name="arrow-right" size="sm" class="rotate-90 mr-1.5" />
                        Download Invoice PDF
                    </x-secondary-button>
                </a>
            </div>
        </x-card>

        {{-- Produk + review inline --}}
        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-3">Produk</h2>
            <div class="divide-y divide-surface-100">
                @foreach ($order->items as $item)
                    @php
                        $product = $item->product;
                        $isActiveProduct = $product && $product->status === \App\Enums\ProductStatus::Active;
                        $hasReviewedThis = $product && in_array($item->product_id, $reviewedProductIds, true);
                        $canReviewThis = $order->status === \App\Enums\OrderStatus::Completed
                            && $isActiveProduct
                            && ! $hasReviewedThis;
                    @endphp
                    <div class="py-3">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0 flex-1">
                                @if ($isActiveProduct)
                                    <a href="{{ route('catalog.show', $product->slug) }}" class="text-sm font-medium text-surface-900 hover:text-primary-700 hover:underline">
                                        {{ $item->product_name }}
                                    </a>
                                @else
                                    <span class="text-sm font-medium text-surface-900">{{ $item->product_name }}</span>
                                    @if ($product)
                                        <span class="text-xs text-surface-400 ml-1">(tidak lagi tersedia)</span>
                                    @endif
                                @endif
                                <p class="text-xs text-surface-500 mt-0.5">
                                    ×{{ $item->quantity }} · <x-price :amount="$item->unit_price" class="text-xs" />
                                </p>
                            </div>
                            <x-price :amount="$item->subtotal" class="text-sm shrink-0" />
                        </div>

                        @if ($canReviewThis)
                            <div x-data="{ open: false, rating: 0 }" class="mt-2">
                                <button type="button" x-on:click="open = !open"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:underline">
                                    <x-icon name="pencil-square" size="xs" />
                                    <span x-show="!open">Beri Ulasan</span>
                                    <span x-show="open" x-cloak>Tutup</span>
                                </button>

                                <form method="POST" action="{{ route('reviews.store', $product->slug) }}"
                                      x-show="open" x-cloak class="mt-2 p-3 bg-surface-50 border border-surface-200 rounded-md space-y-2">
                                    @csrf
                                    <div>
                                        <label class="block text-xs text-surface-600 mb-1">Rating</label>
                                        <div class="flex gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" x-on:click="rating = {{ $i }}"
                                                        :class="rating >= {{ $i }} ? 'text-amber-500' : 'text-surface-300'"
                                                        class="hover:text-amber-400 transition p-0.5">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"/>
                                                    </svg>
                                                </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" x-bind:value="rating">
                                    </div>
                                    <div>
                                        <textarea name="comment" rows="2" placeholder="Komentar (opsional)"
                                                  class="w-full rounded-md border-surface-300 bg-white text-xs focus:border-primary-500 focus:ring-primary-500"></textarea>
                                    </div>
                                    <button type="submit" x-bind:disabled="rating === 0"
                                            class="text-xs px-3 py-1.5 bg-primary-700 text-white font-semibold rounded-md hover:bg-primary-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Kirim Ulasan
                                    </button>
                                </form>
                            </div>
                        @elseif ($hasReviewedThis)
                            <p class="mt-2 text-xs text-green-700 inline-flex items-center gap-1">
                                <x-icon name="check-circle" size="xs" /> Anda sudah mengulas produk ini
                            </p>
                        @elseif ($order->status !== \App\Enums\OrderStatus::Completed && $isActiveProduct)
                            <p class="mt-2 text-xs text-surface-400">Ulasan tersedia setelah pesanan selesai.</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="border-t border-surface-200 mt-2 pt-3 flex justify-between items-baseline">
                <span class="text-sm font-semibold text-surface-900">Subtotal</span>
                <x-price :amount="$order->subtotal" class="text-lg" />
            </div>
        </x-card>

        {{-- Alamat --}}
        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-2">Alamat Pengiriman</h2>
            <p class="text-sm text-surface-700">{{ $order->shipping_recipient_name }} — {{ $order->shipping_phone }}</p>
            <p class="text-sm text-surface-600">{{ $order->shipping_line1 }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
        </x-card>

        {{-- Escrow --}}
        @if ($order->isEscrowHeld())
            <x-alert variant="info">
                <div class="flex items-start gap-2">
                    <x-icon name="banknotes" size="sm" class="text-blue-600 flex-shrink-0 mt-0.5" />
                    <span class="text-xs">
                        Dana <strong>Rp {{ number_format($order->escrow_amount, 0, ',', '.') }}</strong> ditahan oleh Seumpama Pasar sampai Anda mengonfirmasi penerimaan.
                    </span>
                </div>
            </x-alert>
        @elseif ($order->released_at && $order->status === \App\Enums\OrderStatus::Completed)
            <x-alert variant="success">
                <span class="text-xs">Dana telah diteruskan ke penjual. Terima kasih telah berbelanja.</span>
            </x-alert>
        @endif

        {{-- Refund --}}
        @if ($order->refunded_at)
            <x-card class="border-blue-200 bg-blue-50">
                <div class="flex items-start gap-3">
                    <div class="rounded-full bg-blue-100 p-2 shrink-0">
                        <x-icon name="arrow-path" size="md" class="text-blue-700" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-surface-900">Refund simulasi</p>
                        <p class="text-xs text-surface-700 mt-1">
                            Dana sebesar <strong>Rp {{ number_format($order->refunded_amount, 0, ',', '.') }}</strong> telah dikembalikan ke <strong>SeumpamaPay</strong> a.n. <strong>{{ $order->buyer->name }}</strong>.
                        </p>
                        <p class="text-[11px] text-surface-500 mt-1.5">
                            Ini hanya pencatatan simulasi — tidak ada uang sungguhan yang berpindah.
                        </p>
                    </div>
                </div>
            </x-card>
        @endif

        {{-- Tombol konfirmasi --}}
        @if ($order->status === \App\Enums\OrderStatus::Shipped)
            <form method="POST" action="{{ route('buyer.orders.complete', $order->id) }}">
                @csrf
                <x-primary-button type="submit" class="w-full justify-center">
                    <x-icon name="check-circle" size="sm" class="mr-1.5" />
                    Konfirmasi Terima Pesanan
                </x-primary-button>
            </form>
        @endif
    </div>
@endsection