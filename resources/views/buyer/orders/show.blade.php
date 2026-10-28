@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Detail Pesanan</h1>
            <a href="{{ route('buyer.orders.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← Pesanan</a>
        </div>

        @if (session('status'))
            <x-alert variant="success">Aksi berhasil.</x-alert>
        @endif
        @if (session('error'))
            <x-alert variant="danger">{{ session('error') }}</x-alert>
        @endif

        <x-card>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs text-surface-500 font-mono">{{ $order->checkout->reference }}</p>
                    <p class="text-sm font-semibold text-surface-900 mt-1">{{ $order->sellerProfile->store_name }}</p>
                </div>
                <x-status-badge :status="$order->status->value" />
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-3">Produk</h2>
            <div class="space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex justify-between items-start gap-2 py-2 border-t border-surface-100 first:border-t-0">
                        <div class="min-w-0">
                            <p class="text-sm text-surface-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-surface-500">×{{ $item->quantity }} · <x-price :amount="$item->unit_price" class="text-xs" /></p>
                        </div>
                        <x-price :amount="$item->subtotal" class="text-sm shrink-0" />
                    </div>
                @endforeach
            </div>
            <div class="border-t border-surface-200 mt-3 pt-3 flex justify-between items-baseline">
                <span class="text-sm font-semibold text-surface-900">Subtotal</span>
                <x-price :amount="$order->subtotal" class="text-lg" />
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-2">Alamat Pengiriman</h2>
            <p class="text-sm text-surface-700">{{ $order->shipping_recipient_name }} — {{ $order->shipping_phone }}</p>
            <p class="text-sm text-surface-600">{{ $order->shipping_line1 }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
        </x-card>

        @if ($order->isEscrowHeld())
            <x-alert variant="info">
                <div class="flex items-start gap-2">
                    <x-icon name="banknotes" size="sm" class="text-blue-600 flex-shrink-0 mt-0.5" />
                    <span class="text-xs">
                        Dana <strong>Rp {{ number_format($order->escrow_amount, 0, ',', '.') }}</strong> ditahan oleh Seumpama Pasar sampai Anda mengonfirmasi penerimaan. Setelah itu dana diteruskan ke penjual.
                    </span>
                </div>
            </x-alert>
        @elseif ($order->released_at && $order->status === \App\Enums\OrderStatus::Completed)
            <x-alert variant="success">
                <span class="text-xs">Dana telah diteruskan ke penjual. Terima kasih telah berbelanja.</span>
            </x-alert>
        @endif

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