@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        @if (session('status') === 'member-activated')
            <x-alert variant="success">Selamat! Akun Anda sekarang aktif sebagai Member.</x-alert>
        @endif
        @if (session('status') === 'profile-updated')
            <x-alert variant="success">Profil berhasil diperbarui.</x-alert>
        @endif
        @if (session('status') === 'password-updated')
            <x-alert variant="success">Password berhasil diperbarui.</x-alert>
        @endif

        {{-- Header profil --}}
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <x-avatar :name="$user->name" size="xl" />
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-semibold text-surface-900 flex items-center gap-2 flex-wrap">
                        {{ $user->name }}
                        @if ($user->isMember()) <x-badge variant="primary">Member</x-badge> @endif
                        @if ($user->isVerifiedBusiness()) <x-badge variant="success">Bisnis</x-badge> @endif
                        @if ($user->role === \App\Enums\UserRole::Admin) <x-badge variant="warning">Admin</x-badge> @endif
                    </h1>
                    <p class="text-sm text-surface-600">{{ $user->email }}</p>
                    <p class="text-xs text-surface-500 mt-0.5">
                        Tipe akun: {{ $user->account_type?->value ?? 'personal' }}
                        @if ($user->isMember())
                            &middot; Member sejak {{ $user->member_since?->format('d M Y') }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('profile.edit') }}" class="shrink-0">
                    <x-secondary-button type="button">
                        <x-icon name="pencil-square" size="sm" class="mr-1.5" />
                        Edit Profil
                    </x-secondary-button>
                </a>
            </div>
        </x-card>

        {{-- Statistik --}}
        <div class="grid grid-cols-3 gap-3">
            <a href="{{ route('buyer.orders.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="text-xs text-surface-500 uppercase tracking-wide">Pesanan</div>
                    <div class="text-xl font-semibold text-surface-900 mt-1">{{ $orderCount }}</div>
                </x-card>
            </a>
            <a href="{{ route('addresses.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="text-xs text-surface-500 uppercase tracking-wide">Alamat</div>
                    <div class="text-xl font-semibold text-surface-900 mt-1">{{ $addressCount }}</div>
                </x-card>
            </a>
            <a href="{{ route('cart.index') }}" class="block">
                <x-card class="hover:shadow-card-hover transition">
                    <div class="text-xs text-surface-500 uppercase tracking-wide">Keranjang</div>
                    <div class="text-xl font-semibold text-surface-900 mt-1">{{ $cartCount }}</div>
                </x-card>
            </a>
        </div>

        {{-- Promo Member (kalau belum aktif) --}}
        @unless ($user->isMember())
            <x-card class="border-primary-200 bg-primary-50">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-start gap-3 flex-1">
                        <div class="rounded-full bg-primary-100 p-2 shrink-0">
                            <x-icon name="check-circle" size="md" class="text-primary-700" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-surface-900">Jadi Member, dapatkan diskon 5%</h3>
                            <p class="text-xs text-surface-600 mt-1">Aktifkan gratis, dapat diskon 5% otomatis di setiap transaksi. Aktif seumur akun.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('member.activate') }}" class="shrink-0">
                        @csrf
                        <x-primary-button type="submit">
                            <x-icon name="check" size="sm" class="mr-1" /> Aktifkan
                        </x-primary-button>
                    </form>
                </div>
            </x-card>
        @endunless

        {{-- Afiliasi --}}
        @if ($affiliate)
            <x-card>
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="rounded-md bg-primary-50 p-2 shrink-0">
                            <x-icon name="arrow-path" size="md" class="text-primary-700" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-surface-900">Afiliasi Aktif</div>
                            <p class="text-xs text-surface-500 mt-0.5">
                                Kode: <span class="font-mono font-semibold text-primary-700">{{ $affiliate->referral_code }}</span>
                                &middot; Komisi: <span class="font-semibold text-surface-900">Rp {{ number_format($commissionTotal, 0, ',', '.') }}</span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('affiliate.dashboard') }}" class="shrink-0">
                        <x-secondary-button type="button">Dashboard →</x-secondary-button>
                    </a>
                </div>
            </x-card>
        @else
            <x-card class="border-blue-200 bg-blue-50">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-start gap-3 flex-1">
                        <div class="rounded-full bg-blue-100 p-2 shrink-0">
                            <x-icon name="arrow-path" size="md" class="text-blue-700" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-surface-900">Aktifkan Afiliasi, dapat komisi 5%</h3>
                            <p class="text-xs text-surface-600 mt-1">Bagikan link referral Anda dan dapat komisi 5% dari setiap pesanan yang berhasil.</p>
                        </div>
                    </div>
                    <a href="{{ route('buyer.affiliate.show') }}" class="shrink-0">
                        <x-primary-button type="button">
                            <x-icon name="plus" size="sm" class="mr-1" /> Aktifkan
                        </x-primary-button>
                    </a>
                </div>
            </x-card>
        @endif

        {{-- Toko Saya (kalau seller) --}}
        @if ($sellerProfile)
            <x-card>
                <a href="{{ route('seller.dashboard') }}" class="flex items-center justify-between gap-3 hover:text-primary-700">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="rounded-md bg-primary-50 p-2 shrink-0">
                            <x-icon name="home" size="md" class="text-primary-700" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-surface-900 truncate">Toko: {{ $sellerProfile->store_name }}</div>
                            <div class="text-xs text-surface-500">{{ ucfirst($sellerProfile->seller_type?->value ?? '-') }} &middot; {{ $sellerProfile->status?->value ?? '-' }}</div>
                        </div>
                    </div>
                    <x-icon name="chevron-right" size="md" class="text-surface-400 shrink-0" />
                </a>
            </x-card>
        @endif

        {{-- Profil Bisnis (kalau punya) --}}
        @if ($businessProfile)
            <x-card>
                <a href="{{ route('buyer.business.show') }}" class="flex items-center justify-between gap-3 hover:text-primary-700">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="rounded-md {{ $user->isVerifiedBusiness() ? 'bg-green-50' : 'bg-amber-50' }} p-2 shrink-0">
                            <x-icon name="credit-card" size="md" class="{{ $user->isVerifiedBusiness() ? 'text-green-700' : 'text-amber-700' }}" />
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-surface-900 truncate">Bisnis: {{ $businessProfile->company_name }}</div>
                            <div class="text-xs text-surface-500">{{ $user->isVerifiedBusiness() ? 'Terverifikasi — akses harga grosir' : 'Menunggu verifikasi admin' }}</div>
                        </div>
                    </div>
                    <x-icon name="chevron-right" size="md" class="text-surface-400 shrink-0" />
                </a>
            </x-card>
        @endif

        {{-- Menu cepat --}}
        <x-card>
            <div class="space-y-1">
                <a href="{{ route('buyer.orders.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="truck" size="sm" /> Pesanan Saya</span>
                    <x-icon name="chevron-right" size="sm" class="text-surface-400" />
                </a>
                <a href="{{ route('addresses.index') }}" class="flex items-center justify-between text-sm font-medium text-surface-900 hover:text-primary-700 px-2 py-2 rounded-md hover:bg-surface-50">
                    <span class="flex items-center gap-2"><x-icon name="home" size="sm" /> Alamat Pengiriman</span>
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