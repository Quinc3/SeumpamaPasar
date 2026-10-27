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

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('seller.products.index') }}" class="block">
            <x-card class="hover:shadow-card-hover transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-surface-600">Produk</div>
                        <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $productCount }}</div>
                    </div>
                    <x-icon name="chevron-right" size="md" class="text-surface-400" />
                </div>
            </x-card>
        </a>

        <a href="{{ route('seller.products.index') }}" class="block">
            <x-card class="hover:shadow-card-hover transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-surface-600">Produk Aktif</div>
                        <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $activeProductCount }}</div>
                    </div>
                    <x-icon name="chevron-right" size="md" class="text-surface-400" />
                </div>
            </x-card>
        </a>

        <a href="{{ route('seller.products.index') }}" class="block">
            <x-card class="hover:shadow-card-hover transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-surface-600">Menunggu Review</div>
                        <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $pendingReviewCount }}</div>
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
                    </div>
                    <x-icon name="chevron-right" size="md" class="text-surface-400" />
                </div>
            </x-card>
        </a>
    </div>

    {{-- Menu cepat --}}
    <x-card>
        <div class="space-y-1">
            <a href="{{ route('seller.products.create') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                <span class="flex items-center gap-2"><x-icon name="plus" size="sm" /> Tambah Produk Baru</span>
                <x-icon name="chevron-right" size="sm" class="text-surface-400" />
            </a>
            <a href="{{ route('seller.products.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                <span class="flex items-center gap-2"><x-icon name="tag" size="sm" /> Kelola Produk</span>
                <x-icon name="chevron-right" size="sm" class="text-surface-400" />
            </a>
            <a href="{{ route('seller.orders.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                <span class="flex items-center gap-2"><x-icon name="truck" size="sm" /> Pesanan Masuk</span>
                <x-icon name="chevron-right" size="sm" class="text-surface-400" />
            </a>
        </div>
    </x-card>
@endsection