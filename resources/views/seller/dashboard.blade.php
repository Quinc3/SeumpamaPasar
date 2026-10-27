@extends('layouts.seller')

@section('title', 'Dashboard Toko')

@section('content')
    <x-banner-simulation />

    {{-- Info toko --}}
    <div class="bg-white border border-surface-200 rounded-lg shadow-card p-4 flex items-center justify-between">
        <div>
            <div class="text-xs text-surface-500 uppercase tracking-wide">Toko</div>
            <div class="text-lg font-semibold text-surface-900">{{ $seller->store_name }}</div>
            <div class="text-sm text-surface-600">{{ ucfirst($seller->seller_type->value) }} &middot; Status: {{ $seller->status->value }}</div>
        </div>
        <x-badge :variant="$seller->status->value === 'active' ? 'success' : 'warning'">
            {{ $seller->status->value }}
        </x-badge>
    </div>

    {{-- Revenue --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <div class="text-xs text-surface-500 uppercase tracking-wide">Total Pendapatan</div>
            <x-price :amount="$totalRevenue" class="text-xl mt-1 block" />
        </x-card>
        <x-card>
            <div class="text-xs text-surface-500 uppercase tracking-wide">Pendapatan Bulan Ini</div>
            <x-price :amount="$thisMonthRevenue" class="text-xl mt-1 block" />
        </x-card>
        <a href="{{ route('seller.products.index') }}" class="block">
            <x-card class="hover:shadow-card-hover transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-surface-600">Produk</div>
                        <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $productCount }}</div>
                        <p class="text-xs text-surface-500 mt-0.5">{{ $activeProductCount }} aktif · {{ $pendingReviewCount }} review</p>
                    </div>
                    <x-icon name="chevron-right" size="md" class="text-surface-400" />
                </div>
            </x-card>
        </a>
        <a href="{{ route('seller.orders.index') }}" class="block">
            <x-card class="hover:shadow-card-hover transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-surface-600">Order Masuk</div>
                        <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $orderCount }}</div>
                        <p class="text-xs text-surface-500 mt-0.5">{{ $pendingProcessingCount }} menunggu proses</p>
                    </div>
                    <x-icon name="chevron-right" size="md" class="text-surface-400" />
                </div>
            </x-card>
        </a>
    </div>

    {{-- Order per status --}}
    <x-card>
        <h2 class="text-sm font-semibold text-surface-900 mb-3">Order per Status</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
            @foreach (['pending_payment', 'pending_processing', 'processing', 'shipped', 'completed', 'cancelled'] as $st)
                <div class="text-center p-3 bg-surface-50 rounded-md">
                    <x-status-badge :status="$st" />
                    <div class="text-lg font-semibold text-surface-900 mt-1.5">{{ $ordersByStatus[$st] ?? 0 }}</div>
                </div>
            @endforeach
        </div>
    </x-card>

    {{-- Produk terlaris --}}
    <x-card>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-surface-900">Produk Terlaris</h2>
            <a href="{{ route('seller.products.index') }}" class="text-xs text-primary-700 hover:underline">Kelola →</a>
        </div>
        @if ($topProducts->isEmpty())
            <p class="text-sm text-surface-500">Belum ada penjualan.</p>
        @else
            <div class="space-y-2">
                @foreach ($topProducts as $i => $p)
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-mono text-surface-400 w-5 shrink-0">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-surface-900 truncate">{{ $p->product_name }}</p>
                            <p class="text-xs text-surface-500">{{ $p->total_qty }} terjual</p>
                        </div>
                        <x-price :amount="$p->total_revenue" class="text-sm shrink-0" />
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
@endsection