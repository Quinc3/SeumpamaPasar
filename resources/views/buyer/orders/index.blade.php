@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Pesanan Saya</h1>
            <a href="{{ route('buyer.dashboard') }}" class="text-sm text-surface-600 hover:text-surface-900">← Akun</a>
        </div>

        @if ($orders->isEmpty())
            <x-card padding="none">
                <x-empty-state icon="truck" title="Belum ada pesanan" description="Riwayat pesanan Anda akan muncul di sini.">
                    <x-slot:action>
                        <a href="{{ route('catalog.index') }}">
                            <x-primary-button type="button">Jelajahi Katalog</x-primary-button>
                        </a>
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @else
            <div class="space-y-2">
                @foreach ($orders as $order)
                    <a href="{{ route('buyer.orders.show', $order->id) }}" class="block">
                        <x-card class="hover:shadow-card-hover transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs text-surface-500 font-mono">{{ $order->checkout->reference }}</p>
                                    <p class="text-sm font-semibold text-surface-900 mt-0.5">{{ $order->sellerProfile->store_name }}</p>
                                    <p class="text-xs text-surface-600 mt-0.5">{{ $order->items->count() }} item &middot; {{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <x-price :amount="$order->subtotal" class="text-sm" />
                                    <div class="mt-1"><x-status-badge :status="$order->status->value" /></div>
                                </div>
                            </div>
                        </x-card>
                    </a>
                @endforeach
            </div>
            <div class="pt-2">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection