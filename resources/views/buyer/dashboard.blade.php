@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-4">
        <x-banner-simulation />

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900">Halo, {{ $user->name }}</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
            <p class="text-sm text-gray-600 mt-1">
                Tipe akun: <span class="font-medium">{{ $user->account_type->value }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-sm text-gray-600">Pesanan</div>
                <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $orderCount }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-sm text-gray-600">Transaksi Digital</div>
                <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $digitalTransactionCount }}</div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="text-sm text-gray-600">Alamat</div>
                <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $addressCount }}</div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
            Katalog, keranjang, dan riwayat pesanan akan tersedia pada Fase 3.
        </div>
    </div>
@endsection