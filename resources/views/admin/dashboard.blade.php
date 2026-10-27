@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <x-banner-simulation />

    {{-- Statistik utama --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <div class="text-xs text-surface-500 uppercase tracking-wide">Total Pendapatan</div>
            <x-price :amount="$totalRevenue" class="text-2xl mt-1 block" />
            <p class="text-xs text-surface-500 mt-1">Dari order berbayar</p>
        </x-card>
        <x-card>
            <div class="text-xs text-surface-500 uppercase tracking-wide">Total Order</div>
            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $orderCount }}</div>
        </x-card>
        <x-card>
            <div class="text-xs text-surface-500 uppercase tracking-wide">Komisi Terbayar</div>
            <x-price :amount="$totalCommission" class="text-2xl mt-1 block" />
            <p class="text-xs text-surface-500 mt-1">Simulasi</p>
        </x-card>
        <x-card>
            <div class="text-xs text-surface-500 uppercase tracking-wide">Pengguna</div>
            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $userCount }}</div>
        </x-card>
    </div>

    {{-- Counts sekunder --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <div class="text-xs text-surface-500">Penjual Aktif</div>
            <div class="text-lg font-semibold text-surface-900 mt-1">{{ $activeSellerCount }}</div>
        </x-card>
        <x-card>
            <div class="text-xs text-surface-500">Afiliasi Aktif</div>
            <div class="text-lg font-semibold text-surface-900 mt-1">{{ $activeAffiliateCount }}</div>
        </x-card>
        <x-card>
            <div class="text-xs text-surface-500">Produk Aktif</div>
            <div class="text-lg font-semibold text-surface-900 mt-1">{{ $activeProductCount }}</div>
        </x-card>
        <x-card>
            <div class="text-xs text-surface-500">Kategori</div>
            <div class="text-lg font-semibold text-surface-900 mt-1">{{ $categoryCount }}</div>
        </x-card>
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

    {{-- Top products + Top sellers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-3">Produk Terlaris</h2>
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

        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-3">Penjual Teratas</h2>
            @if ($topSellers->isEmpty())
                <p class="text-sm text-surface-500">Belum ada penjualan.</p>
            @else
                <div class="space-y-2">
                    @foreach ($topSellers as $i => $s)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono text-surface-400 w-5 shrink-0">{{ $i + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-surface-900 truncate">{{ $s->store_name }}</p>
                                <p class="text-xs text-surface-500">{{ $s->order_count }} order</p>
                            </div>
                            <x-price :amount="$s->total" class="text-sm shrink-0" />
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    {{-- Recent orders --}}
    <x-card>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-surface-900">Order Terbaru</h2>
            <a href="{{ route('admin.moderation.index') }}" class="text-xs text-primary-700 hover:underline">Moderasi →</a>
        </div>
        @if ($recentOrders->isEmpty())
            <p class="text-sm text-surface-500">Belum ada order.</p>
        @else
            <div class="divide-y divide-surface-100">
                @foreach ($recentOrders as $o)
                    <div class="py-2 flex items-center justify-between text-sm gap-3">
                        <div class="min-w-0">
                            <p class="text-surface-900 truncate">{{ $o->buyer->name }} · {{ $o->sellerProfile->store_name }}</p>
                            <p class="text-xs text-surface-500">{{ $o->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <x-price :amount="$o->subtotal" class="text-sm" />
                            <x-status-badge :status="$o->status->value" />
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
@endsection