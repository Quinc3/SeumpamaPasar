@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <x-banner-simulation />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Total Pengguna</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $userCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Penjual Aktif</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $activeSellerCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Produk Aktif</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $activeProductCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Total Order</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $orderCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Kategori</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $categoryCount }}</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600">Layanan Digital Aktif</div>
            <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $activeDigitalProductCount }}</div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
        Fitur lengkap dashboard akan tersedia pada Fase 6. Angka di atas diambil langsung dari database.
    </div>
@endsection