@extends('layouts.app')

@section('title', 'Pembayaran Simulasi')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <x-banner-simulation />

        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Pembayaran</h1>
            <a href="{{ route('cart.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← Keranjang</a>
        </div>

        @php
            $user = $checkout->user;
            $uid = str_pad((string) $user->id, 4, '0', STR_PAD_LEFT);
            $cid = str_pad((string) $checkout->id, 8, '0', STR_PAD_LEFT);
            $method = $checkout->payment->method;
            $vaNumber = chunk_split('8808' . $uid . $cid, 4, ' ');
            $ewalletNumber = '0812-' . $uid . '-' . substr($cid, 4);
        @endphp

        <x-card>
            {{-- Tujuan pembayaran berdasarkan metode --}}
            @if ($method === 'demo_va')
                <div class="text-center pb-4 border-b border-surface-200 mb-4">
                    <p class="text-xs uppercase tracking-wide text-surface-500">Virtual Account — BCA</p>
                    <p class="text-2xl sm:text-3xl font-mono font-bold text-primary-700 tracking-wider mt-2">{{ trim($vaNumber) }}</p>
                    <p class="text-xs text-surface-500 mt-1.5">a.n. {{ $user->name }}</p>
                </div>
            @elseif ($method === 'demo_ewallet')
                <div class="text-center pb-4 border-b border-surface-200 mb-4">
                    <div class="inline-flex items-center gap-2 justify-center">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-primary-700 text-white text-[10px] font-bold">SP</span>
                        <span class="text-sm font-semibold text-surface-900">SeumpamaPay</span>
                    </div>
                    <p class="text-2xl sm:text-3xl font-mono font-bold text-primary-700 tracking-wider mt-2">{{ $ewalletNumber }}</p>
                    <p class="text-xs text-surface-500 mt-1.5">a.n. {{ $user->name }}</p>
                </div>
            @elseif ($method === 'demo_transfer')
                <div class="pb-4 border-b border-surface-200 mb-4">
                    <p class="text-xs uppercase tracking-wide text-surface-500">Transfer Bank — BCA</p>
                    <p class="text-xl font-mono font-bold text-primary-700 mt-1">1234567890</p>
                    <p class="text-sm text-surface-700 mt-1">a.n. PT Seumpama Pasar Nusantara</p>
                    <p class="text-xs text-surface-500 mt-3">Berita transfer:</p>
                    <p class="font-mono text-sm text-surface-900 mt-0.5 break-all bg-surface-50 px-2 py-1 rounded">{{ $checkout->reference }}</p>
                </div>
            @endif

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