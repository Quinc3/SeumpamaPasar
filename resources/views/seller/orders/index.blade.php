@extends('layouts.app')

@section('title', 'Pesanan Masuk')

@section('content')
<x-page-wrapper>
    @if ($orders->isEmpty())
        <x-card padding="none">
            <x-empty-state icon="truck" title="Belum ada pesanan" description="Pesanan dari pembeli akan muncul di sini." />
        </x-card>
    @else
        <div class="space-y-2">
            @foreach ($orders as $order)
                <a href="{{ route('seller.orders.show', $order->id) }}" class="block">
                    <x-card class="hover:shadow-card-hover transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs text-surface-500 font-mono">{{ $order->checkout->reference }}</p>
                                <p class="text-sm font-semibold text-surface-900 mt-0.5">{{ $order->buyer->name }}</p>
                                <p class="text-xs text-surface-600 mt-0.5">{{ $order->items->count() }} item · {{ $order->created_at->format('d M Y, H:i') }}</p>
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
</x-page-wrapper>
@endsection