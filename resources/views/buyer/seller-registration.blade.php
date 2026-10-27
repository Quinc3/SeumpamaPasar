@extends('layouts.app')

@section('title', 'Buka Toko')

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-surface-900">Buka Toko</h1>
            <a href="{{ route('profile.show') }}" class="text-sm text-surface-600 hover:text-surface-900">← Profil</a>
        </div>

        @if ($seller)
            @if ($seller->status === \App\Enums\SellerStatus::Pending)
                <x-alert variant="warning">
                    Toko <strong>{{ $seller->store_name }}</strong> sedang menunggu verifikasi admin. Anda akan bisa mengelola produk setelah disetujui.
                </x-alert>
            @elseif ($seller->status === \App\Enums\SellerStatus::Active)
                <x-alert variant="success">
                    Toko <strong>{{ $seller->store_name }}</strong> sudah aktif.
                </x-alert>
                <a href="{{ route('seller.dashboard') }}">
                    <x-primary-button type="button" class="w-full justify-center">
                        <x-icon name="home" size="sm" class="mr-1.5" /> Buka Dashboard Toko
                    </x-primary-button>
                </a>
            @elseif ($seller->status === \App\Enums\SellerStatus::Suspended)
                <x-alert variant="danger">
                    Toko <strong>{{ $seller->store_name }}</strong> ditangguhkan oleh admin. Hubungi admin untuk informasi lebih lanjut.
                </x-alert>
            @endif

            <x-card>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-surface-600">Nama Toko</span>
                        <span class="font-medium text-surface-900">{{ $seller->store_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-surface-600">Jenis</span>
                        <span class="text-surface-900">{{ ucfirst($seller->seller_type->value) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-surface-600">Status</span>
                        <x-status-badge :status="$seller->status->value" />
                    </div>
                </div>
            </x-card>
        @else
            <x-alert variant="info">
                Isi data toko Anda. Setelah disetujui admin, Anda bisa mengelola produk dan menerima pesanan.
            </x-alert>

            @if ($errors->any())
                <x-alert variant="danger">Periksa kembali isian form.</x-alert>
            @endif

            <x-card>
                <form method="POST" action="{{ route('buyer.seller.register.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="store_name" value="Nama Toko" />
                        <x-text-input id="store_name" name="store_name" :value="old('store_name')" class="mt-1 block w-full" required />
                        @error('store_name') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                    </div>

                    <div>
                        <x-input-label for="seller_type" value="Jenis Toko" />
                        <select id="seller_type" name="seller_type" class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500" required>
                            <option value="individual" @selected(old('seller_type') === 'individual')>Individu (C2C / perorangan)</option>
                            <option value="business" @selected(old('seller_type') === 'business') {{ $businessVerified ? '' : 'disabled' }}>Bisnis (B2B & B2C)</option>
                        </select>
                        @unless ($businessVerified)
                            <p class="text-xs text-surface-500 mt-1">
                                Untuk membuka toko bisnis, lengkapi <a href="{{ route('buyer.business.show') }}" class="text-primary-700 underline">Profil Bisnis</a> dan tunggu verifikasi admin.
                            </p>
                        @endunless
                        @error('seller_type') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi Toko" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500" required>{{ old('description') }}</textarea>
                        <p class="text-xs text-surface-500 mt-1">Ceritakan singkat tentang toko Anda (maks 500 karakter).</p>
                        @error('description') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <x-primary-button type="submit">
                            <x-icon name="check-circle" size="sm" class="mr-1.5" />
                            Kirim Pengajuan
                        </x-primary-button>
                        <a href="{{ route('profile.show') }}">
                            <x-secondary-button type="button">Batal</x-secondary-button>
                        </a>
                    </div>
                </form>
            </x-card>
        @endif
    </div>
@endsection