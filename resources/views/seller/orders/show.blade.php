@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    @if (session('status'))
        <x-alert variant="success">Aksi berhasil.</x-alert>
    @endif
    @if (session('error'))
        <x-alert variant="danger">{{ session('error') }}</x-alert>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-surface-900">Pesanan {{ $order->checkout->reference }}</h2>
        <a href="{{ route('seller.orders.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← Pesanan</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <x-card>
                <h3 class="text-sm font-semibold text-surface-900 mb-3">Produk</h3>
                @foreach ($order->items as $item)
                    <div class="flex justify-between items-start gap-2 py-2 border-t border-surface-100 first:border-t-0">
                        <div class="min-w-0">
                            <p class="text-sm text-surface-900">{{ $item->product_name }}</p>
                            <p class="text-xs text-surface-500">×{{ $item->quantity }} · <x-price :amount="$item->unit_price" class="text-xs" /></p>
                        </div>
                        <x-price :amount="$item->subtotal" class="text-sm shrink-0" />
                    </div>
                @endforeach
                <div class="border-t border-surface-200 mt-3 pt-3 flex justify-between">
                    <span class="text-sm font-semibold text-surface-900">Subtotal</span>
                    <x-price :amount="$order->subtotal" class="text-lg" />
                </div>
            </x-card>

            <x-card>
                <h3 class="text-sm font-semibold text-surface-900 mb-2">Pengiriman</h3>
                <p class="text-sm text-surface-700">{{ $order->shipping_recipient_name }} — {{ $order->shipping_phone }}</p>
                <p class="text-sm text-surface-600">{{ $order->shipping_line1 }}, {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
            </x-card>
        </div>

        <div class="space-y-3">
            <x-card>
                <p class="text-xs uppercase tracking-wide text-surface-500 mb-2">Status</p>
                <x-status-badge :status="$order->status->value" />
            </x-card>

            @if ($order->status === \App\Enums\OrderStatus::PendingProcessing)
                <form method="POST" action="{{ route('seller.orders.process', $order->id) }}">
                    @csrf
                    <x-primary-button type="submit" class="w-full justify-center">
                        <x-icon name="arrow-path" size="sm" class="mr-1.5" />
                        Proses Pesanan
                    </x-primary-button>
                </form>
                <form method="POST" action="{{ route('seller.orders.cancel', $order->id) }}" onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan dan refund simulasi dicatat.')">
                    @csrf
                    <x-danger-button type="submit" class="w-full justify-center">
                        <x-icon name="x-circle" size="sm" class="mr-1.5" />
                        Batalkan Pesanan
                    </x-danger-button>
                </form>
            @elseif ($order->status === \App\Enums\OrderStatus::Processing)
                <form method="POST" action="{{ route('seller.orders.ship', $order->id) }}">
                    @csrf
                    <x-primary-button type="submit" class="w-full justify-center">
                        <x-icon name="truck" size="sm" class="mr-1.5" />
                        Tandai Dikirim
                    </x-primary-button>
                </form>
            @endif
        </div>
    </div>
@endsection