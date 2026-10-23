@extends('layouts.seller')

@section('title', 'Dashboard Toko')

@section('content')
    <x-banner-simulation />

    <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-500 uppercase tracking-wide">Toko</div>
            <div class="text-lg font-semibold text-gray-900">{{ $seller->store_name }}</div>
            <div class="text-sm text-gray-600">{{ ucfirst($seller->seller_type->value) }} &middot; Status: {{ $seller->status->value }}</div>
        </div>
        <x-badge :variant="$seller->status->value === 'active' ? 'success' : 'warning'">
            {{ $seller->status->value }}
        </x-badge>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Produk</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $productCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Produk Aktif</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $activeProductCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Menunggu Review</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $pendingReviewCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Order Masuk</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $orderCount }}</div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
        Manajemen produk dan pesanan akan tersedia pada Fase 3. Saat ini hanya menampilkan ringkasan.
    </div>
@endsection