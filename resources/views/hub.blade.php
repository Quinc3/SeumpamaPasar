@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-5xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8 space-y-4">
        @if (session('status') === 'member-activated')
            <x-alert variant="success">Selamat! Akun Anda sekarang aktif sebagai Member.</x-alert>
        @endif

        {{-- Profil --}}
        <div class="bg-white border border-surface-200 rounded-lg shadow-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-avatar :name="$user->name" size="lg" />
                <div>
                    <h1 class="text-lg font-semibold text-surface-900 flex items-center gap-2 flex-wrap">
                        {{ $user->name }}
                        @if ($user->isMember()) <x-badge variant="primary">Member</x-badge> @endif
                        @if ($user->isVerifiedBusiness()) <x-badge variant="success">Bisnis</x-badge> @endif
                        @if ($user->role === \App\Enums\UserRole::Admin) <x-badge variant="warning">Admin</x-badge> @endif
                    </h1>
                    <p class="text-sm text-surface-600">{{ $user->email }}</p>
                    <p class="text-xs text-surface-500 mt-0.5">
                        Tipe akun: {{ $user->account_type->value }}
                        @if ($user->isMember())
                            &middot; Member sejak {{ $user->member_since->format('d M Y') }}
                        @endif
                    </p>
                </div>
            </div>
            @unless ($user->isMember())
                <form method="POST" action="{{ route('member.activate') }}">
                    @csrf
                    <x-primary-button type="submit">
                        <x-icon name="check-circle" size="sm" class="mr-1.5" />
                        Aktifkan Member
                    </x-primary-button>
                </form>
            @endunless
        </div>

        {{-- Kartu kapabilitas kondisional --}}
        @php
            $caps = collect();
            if ($user->role === \App\Enums\UserRole::Admin) {
                $caps->push([
                    'title' => 'Admin Panel',
                    'desc' => 'Kelola pengguna, produk, penjual, dan laporan.',
                    'route' => route('admin.dashboard'),
                    'icon' => 'check-circle',
                    'variant' => 'primary',
                    'cta' => 'Buka Admin',
                ]);
            }
            if ($user->sellerProfile) {
                $caps->push([
                    'title' => 'Toko Saya: ' . $user->sellerProfile->store_name,
                    'desc' => $sellerProductCount . ' produk · ' . $sellerOrderCount . ' pesanan',
                    'route' => route('seller.dashboard'),
                    'icon' => 'tag',
                    'variant' => 'primary',
                    'cta' => 'Kelola Toko',
                ]);
            }
            if ($user->affiliateProfile) {
                $caps->push([
                    'title' => 'Afiliasi',
                    'desc' => 'Kode: ' . $user->affiliateProfile->referral_code . ' · Komisi: Rp ' . number_format($affiliateCommissionTotal, 0, ',', '.'),
                    'route' => route('affiliate.dashboard'),
                    'icon' => 'arrow-path',
                    'variant' => 'info',
                    'cta' => 'Buka Dashboard',
                ]);
            }
            if ($user->businessProfile) {
                $caps->push([
                    'title' => 'Profil Bisnis',
                    'desc' => $user->isVerifiedBusiness() ? 'Terverifikasi — akses harga grosir' : 'Menunggu verifikasi admin',
                    'route' => route('buyer.business.show'),
                    'icon' => 'credit-card',
                    'variant' => $user->isVerifiedBusiness() ? 'success' : 'warning',
                    'cta' => 'Lihat Profil',
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

        {{-- Statistik belanja --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('buyer.orders.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-surface-600">Pesanan</div>
                            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $orderCount }}</div>
                        </div>
                        <x-icon name="chevron-right" size="md" class="text-surface-400" />
                    </div>
                </x-card>
            </a>
            <a href="{{ route('addresses.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-surface-600">Alamat</div>
                            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $addressCount }}</div>
                        </div>
                        <x-icon name="chevron-right" size="md" class="text-surface-400" />
                    </div>
                </x-card>
            </a>
            <a href="{{ route('cart.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-surface-600">Keranjang</div>
                            <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $cartCount }}</div>
                        </div>
                        <x-icon name="chevron-right" size="md" class="text-surface-400" />
                    </div>
                </x-card>
            </a>
        </div>

        {{-- Menu --}}
        <x-card>
            <div class="space-y-1">
                <a href="{{ route('addresses.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="home" size="sm" /> Alamat Pengiriman</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('buyer.affiliate.show') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2">
                        <x-icon name="arrow-path" size="sm" /> Afiliasi
                        @if ($user->affiliateProfile)
                            <x-badge variant="primary">{{ $user->affiliateProfile->referral_code }}</x-badge>
                        @endif
                    </span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('catalog.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="tag" size="sm" /> Jelajahi Katalog</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
            </div>
        </x-card>
    </div>
@endsection