@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
    <div class="max-w-4xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8 space-y-4">
        <x-banner-simulation />

        @if (session('status') === 'member-activated')
            <x-alert variant="success">Selamat! Akun Anda sekarang aktif sebagai Member.</x-alert>
        @endif

        {{-- Kartu profil --}}
        <div class="bg-white border border-surface-200 rounded-lg shadow-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-avatar :name="$user->name" size="lg" />
                <div>
                    <h2 class="text-lg font-semibold text-surface-900 flex items-center gap-2">
                        {{ $user->name }}
                        @if ($user->isMember())
                            <x-badge variant="primary">Member</x-badge>
                        @endif
                        @if ($user->isVerifiedBusiness())
                            <x-badge variant="success">Bisnis</x-badge>
                        @endif
                    </h2>
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

        {{-- Benefit member --}}
        @unless ($user->isMember())
            <x-card>
                <div class="flex items-start gap-3">
                    <x-icon name="information-circle" size="lg" class="text-primary-700 flex-shrink-0" />
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Manfaat Member</h3>
                        <p class="text-sm text-surface-600 mt-1">
                            Dapatkan <strong>diskon 5%</strong> otomatis untuk setiap transaksi. Tanpa biaya, aktif seumur akun.
                        </p>
                    </div>
                </div>
            </x-card>
        @endunless

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
        </div>

        {{-- Menu --}}
        <x-card>
            <div class="space-y-1">
                <a href="{{ route('addresses.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="home" size="sm" /> Alamat Pengiriman</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('buyer.business.show') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2">
                        <x-icon name="credit-card" size="sm" /> Profil Bisnis
                        @if ($user->isVerifiedBusiness())
                            <x-badge variant="success">Terverifikasi</x-badge>
                        @elseif ($user->businessProfile)
                            <x-badge variant="warning">Menunggu</x-badge>
                        @endif
                    </span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('buyer.affiliate.show') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2">
                        <x-icon name="arrow-path" size="sm" /> Afiliasi
                        @php $aff = $user->affiliateProfile; @endphp
@if ($aff)
    <x-badge variant="primary">{{ $aff->referral_code }}</x-badge>
@endif
                    </span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('catalog.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="tag" size="sm" /> Jelajahi Katalog</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('cart.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="shopping-cart" size="sm" /> Keranjang Saya</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
            </div>
        </x-card>
    </div>
@endsection