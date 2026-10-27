@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-page-wrapper>
        @if (session('status') === 'member-activated')
            <x-alert variant="success">Selamat! Akun Anda sekarang aktif sebagai Member.</x-alert>
        @endif

        {{-- Header user --}}
        <div class="bg-white border border-surface-200 rounded-lg shadow-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-avatar :name="$user->name" size="lg" />
                <div>
                    <h1 class="text-lg font-semibold text-surface-900 flex items-center gap-2 flex-wrap">
                        {{ $user->name }}
                        @if ($user->isMember()) <x-badge variant="primary">Member</x-badge> @endif
                        @if ($user->isVerifiedBusiness()) <x-badge variant="success">Bisnis</x-badge> @endif
                    </h1>
                    <p class="text-sm text-surface-600">{{ $user->email }}</p>
                </div>
            </div>
            <a href="{{ route('profile.show') }}">
                <x-secondary-button type="button">
                    <x-icon name="user" size="sm" class="mr-1.5" /> Lihat Profil
                </x-secondary-button>
            </a>
        </div>

        {{-- Kartu kapabilitas --}}
        @php
            $caps = collect();
            if ($user->sellerProfile && $user->sellerProfile->status === \App\Enums\SellerStatus::Active) {
                $caps->push([
                    'title' => 'Toko Saya: ' . $user->sellerProfile->store_name,
                    'desc' => $sellerProductCount . ' produk · ' . $sellerOrderCount . ' pesanan',
                    'route' => route('seller.dashboard'),
                    'icon' => 'home',
                    'cta' => 'Kelola Toko',
                ]);
            }
            if ($user->affiliateProfile) {
                $caps->push([
                    'title' => 'Afiliasi',
                    'desc' => 'Kode: ' . $user->affiliateProfile->referral_code . ' · Komisi: Rp ' . number_format($affiliateCommissionTotal, 0, ',', '.'),
                    'route' => route('affiliate.dashboard'),
                    'icon' => 'arrow-path',
                    'cta' => 'Buka Dashboard',
                ]);
            }
            if ($user->businessProfile) {
                $caps->push([
                    'title' => 'Profil Bisnis',
                    'desc' => $user->isVerifiedBusiness() ? 'Terverifikasi — akses harga grosir' : 'Menunggu verifikasi admin',
                    'route' => route('buyer.business.show'),
                    'icon' => 'credit-card',
                    'cta' => 'Lihat Profil',
                ]);
            }
            if ($user->sellerProfile && $user->sellerProfile->status !== \App\Enums\SellerStatus::Active) {
                $caps->push([
                    'title' => 'Pengajuan Toko',
                    'desc' => 'Status: ' . $user->sellerProfile->status->value,
                    'route' => route('buyer.seller.register.show'),
                    'icon' => 'home',
                    'cta' => 'Lihat Status',
                ]);
            }
        @endphp

        @if ($caps->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($caps as $cap)
                    <a href="{{ $cap['route'] }}" class="block">
                        <x-card class="hover:shadow-card-hover transition h-full">
                            <div class="flex items-start gap-3">
                                <div class="rounded-md bg-primary-50 p-2 shrink-0">
                                    <x-icon :name="$cap['icon']" size="md" class="text-primary-700" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-surface-900">{{ $cap['title'] }}</p>
                                    <p class="text-xs text-surface-600 mt-0.5">{{ $cap['desc'] }}</p>
                                    <p class="text-xs text-primary-700 font-medium mt-2">{{ $cap['cta'] }} →</p>
                                </div>
                            </div>
                        </x-card>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- SeumpamaPay --}}
        @php $walletBalance = (float) ($user->wallet?->balance ?? 0); @endphp
        <a href="{{ route('wallet.index') }}" class="block">
            <div class="rounded-xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white p-4 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-white/20 font-bold text-xs">SP</span>
                        <span class="text-xs font-semibold">SeumpamaPay</span>
                    </div>
                    <x-icon name="chevron-right" size="sm" class="text-white/60" />
                </div>
                <p class="text-xs text-white/70 uppercase tracking-wide mt-3">Saldo</p>
                <p class="text-xl font-bold">Rp {{ number_format($walletBalance, 0, ',', '.') }}</p>
            </div>
        </a>

        {{-- Statistik --}}
        <div class="grid grid-cols-3 gap-3">
            <a href="{{ route('buyer.orders.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-surface-500 uppercase tracking-wide">Pesanan</div>
                            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $orderCount }}</div>
                        </div>
                        <x-icon name="chevron-right" size="md" class="text-surface-400" />
                    </div>
                </x-card>
            </a>
            <a href="{{ route('cart.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-surface-500 uppercase tracking-wide">Keranjang</div>
                            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $cartCount }}</div>
                        </div>
                        <x-icon name="chevron-right" size="md" class="text-surface-400" />
                    </div>
                </x-card>
            </a>
            <a href="{{ route('addresses.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-surface-500 uppercase tracking-wide">Alamat</div>
                            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $addressCount }}</div>
                        </div>
                        <x-icon name="chevron-right" size="md" class="text-surface-400" />
                    </div>
                </x-card>
            </a>
        </div>

        {{-- Aksi cepat --}}
        <x-card>
            <div class="space-y-1">
                <a href="{{ route('catalog.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="tag" size="sm" /> Jelajahi Katalog</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('profile.show') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="user" size="sm" /> Profil & Pengaturan</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
            </div>
        </x-card>
    </x-page-wrapper>
@endsection