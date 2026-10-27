@extends('layouts.app')

@section('title', 'Pembayaran Simulasi')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <x-banner-simulation />

        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Pembayaran</h1>
            <a href="{{ route('cart.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← Keranjang</a>
        </div>

        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">Referensi</p>
                    <p class="font-mono text-surface-900 mt-0.5">{{ $checkout->reference }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">Metode</p>
                    <p class="text-surface-900 mt-0.5">{{ str_replace('_', ' ', $checkout->payment->method) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">Total</p>
                    <x-price :amount="$checkout->total_amount" class="text-lg" />
                </div>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-2">Rincian Pesanan</h2>
            @foreach ($checkout->orders as $order)
                <div class="py-2 border-t border-surface-100 first:border-t-0">
                    <p class="text-xs uppercase tracking-wide text-surface-500">{{ $order->sellerProfile->store_name }}</p>
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-2 text-sm mt-1">
                            <span class="text-surface-700 truncate">{{ $item->product_name }} ×{{ $item->quantity }}</span>
                            <x-price :amount="$item->subtotal" class="text-sm shrink-0" />
                        </div>
                    @endforeach
                </div>
            @endforeach
        </x-card>

        <x-card>
            <p class="text-sm text-surface-700 mb-3">
                Ini adalah <strong>pembayaran simulasi</strong>. Tidak ada uang sungguhan yang berpindah. Pilih hasil yang ingin disimulasikan:
            </p>
            <div class="flex flex-col sm:flex-row gap-2">
                <form method="POST" action="{{ route('checkout.pay.submit', $checkout->reference) }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="outcome" value="success">
                    <x-primary-button type="submit" class="w-full justify-center">
                        <x-icon name="check-circle" size="sm" class="mr-1.5" />
                        Simulasikan Berhasil
                    </x-primary-button>
                </form>
                <form method="POST" action="{{ route('checkout.pay.submit', $checkout->reference) }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="outcome" value="fail">
                    <x-danger-button type="submit" class="w-full justify-center">
                        <x-icon name="x-circle" size="sm" class="mr-1.5" />
                        Simulasikan Gagal
                    </x-danger-button>
                </form>
            </div>
        </x-card>
    </div>
@endsection