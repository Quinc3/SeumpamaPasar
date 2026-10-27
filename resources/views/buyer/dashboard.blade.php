@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
    <div class="max-w-4xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8 space-y-4">
        <x-banner-simulation />

        @if (session('status') === 'member-activated')
            <x-alert variant="success">Selamat! Akun Anda sekarang aktif sebagai Member.</x-alert>
        @endif

        <div class="bg-white border border-surface-200 rounded-lg shadow-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-avatar :name="$user->name" size="lg" />
                <div>
                    <h2 class="text-lg font-semibold text-surface-900 flex items-center gap-2">
                        {{ $user->name }}
                        @if ($user->isMember())
                            <x-badge variant="primary">Member</x-badge>
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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-card>
                <div class="text-sm text-surface-600">Pesanan</div>
                <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $orderCount }}</div>
            </x-card>
            <x-card>
                <div class="text-sm text-surface-600">Alamat</div>
                <div class="text-2xl font-semibold text-surface-900 mt-1">{{ $addressCount }}</div>
            </x-card>
        </div>

        <x-card>
            <p class="text-sm text-surface-600">
                Katalog, keranjang, dan riwayat pesanan akan tersedia pada Fase 3.
            </p>
        </x-card>
    </div>
@endsection