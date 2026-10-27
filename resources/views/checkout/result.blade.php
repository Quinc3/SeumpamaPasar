@extends('layouts.app')

@section('title', 'Hasil Pembayaran')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        @php
            $isPaid = $checkout->payment->status === \App\Enums\PaymentStatus::Paid;
        @endphp

        @if ($isPaid)
            <x-alert variant="success">
                Pembayaran simulasi berhasil. Pesanan diteruskan ke penjual.
            </x-alert>
        @else
            <x-alert variant="danger">
                Pembayaran simulasi gagal.
                @if ($checkout->payment->failure_reason === 'insufficient_stock')
                    Stok berubah sejak checkout. Silakan buat pesanan baru.
                @elseif ($checkout->payment->failure_reason === 'price_changed')
                    Harga produk berubah sejak checkout. Silakan buat pesanan baru.
                @else
                    Silakan coba lagi dengan checkout baru.
                @endif
            </x-alert>
        @endif

        <x-card>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-surface-600">Referensi</span>
                    <span class="font-mono">{{ $checkout->reference }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-surface-600">Status pembayaran</span>
                    <x-status-badge :status="$checkout->payment->status->value" />
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-surface-600">Status checkout</span>
                    <x-status-badge :status="$checkout->status->value" />
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-surface-600">Total</span>
                    <x-price :amount="$checkout->total_amount" class="text-sm" />
                </div>
            </div>
        </x-card>

        @if ($isPaid)
            <x-card>
                @php
                    $uid = str_pad((string) $checkout->user_id, 4, '0', STR_PAD_LEFT);
                    $cid = str_pad((string) $checkout->id, 8, '0', STR_PAD_LEFT);
                    $method = $checkout->payment->method;
                @endphp
                <div class="flex items-start gap-3">
                    <x-icon name="check-circle" size="lg" class="text-green-600 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-surface-900">Pembayaran diterima</p>
                        @if ($method === 'demo_va')
                            <p class="text-xs text-surface-600 mt-1">Via Virtual Account BCA <span class="font-mono">{{ trim(chunk_split('8808' . $uid . $cid, 4, ' ')) }}</span></p>
                        @elseif ($method === 'demo_ewallet')
                            <p class="text-xs text-surface-600 mt-1">Via SeumpamaPay <span class="font-mono">0812-{{ $uid }}-{{ substr($cid, 4) }}</span></p>
                        @elseif ($method === 'demo_transfer')
                            <p class="text-xs text-surface-600 mt-1">Via Transfer BCA <span class="font-mono">1234567890</span></p>
                        @endif
                    </div>
                </div>
            </x-card>
        @endif

        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-2">Pesanan</h2>
            @foreach ($checkout->orders as $order)
                <div class="flex justify-between items-start gap-2 py-2 border-t border-surface-100 first:border-t-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-surface-900">{{ $order->sellerProfile->store_name }}</p>
                        <p class="text-xs text-surface-500">{{ $order->items->count() }} item</p>
                    </div>
                    <div class="text-right">
                        <x-price :amount="$order->subtotal" class="text-sm" />
                        <div class="mt-1"><x-status-badge :status="$order->status->value" /></div>
                    </div>
                </div>
            @endforeach
        </x-card>

        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('buyer.dashboard') }}" class="flex-1">
                <x-secondary-button type="button" class="w-full justify-center">Kembali ke Akun</x-secondary-button>
            </a>
            <a href="{{ route('catalog.index') }}" class="flex-1">
                <x-primary-button type="button" class="w-full justify-center">Lanjut Belanja</x-primary-button>
            </a>
        </div>
    </div>
@endsection