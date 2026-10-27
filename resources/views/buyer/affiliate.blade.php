@extends('layouts.app')

@section('title', 'Afiliasi')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Afiliasi</h1>
            <a href="{{ route('buyer.dashboard') }}" class="text-sm text-surface-600 hover:text-surface-900">← Akun</a>
        </div>

        @if (session('status') === 'affiliate-activated')
            <x-alert variant="success">Selamat! Akun afiliasi Anda aktif.</x-alert>
        @endif

        @if ($profile)
            <x-card>
                <p class="text-xs uppercase tracking-wide text-surface-500 mb-1">Kode Referral</p>
                <p class="text-2xl font-mono font-bold text-primary-700">{{ $profile->referral_code }}</p>
                <p class="text-xs text-surface-500 mt-2">
                    Bagikan link: <span class="font-mono">{{ url('/') }}?ref={{ $profile->referral_code }}</span>
                </p>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-surface-900">Dashboard Afiliasi</p>
                        <p class="text-xs text-surface-500">Lihat komisi dan performa referral Anda.</p>
                    </div>
                    <a href="{{ route('affiliate.dashboard') }}">
                        <x-primary-button type="button">Buka Dashboard</x-primary-button>
                    </a>
                </div>
            </x-card>
        @else
            <x-card>
                <h2 class="text-sm font-semibold text-surface-900">Aktifkan Akun Afiliasi</h2>
                <p class="text-sm text-surface-600 mt-1">
                    Dapatkan <strong>komisi 5%</strong> simulasi dari setiap pesanan yang berhasil melalui link referral Anda. Gratis, tanpa biaya.
                </p>
                <form method="POST" action="{{ route('buyer.affiliate.activate') }}" class="mt-3">
                    @csrf
                    <x-primary-button type="submit">
                        <x-icon name="check-circle" size="sm" class="mr-1.5" />
                        Aktifkan Afiliasi
                    </x-primary-button>
                </form>
            </x-card>
        @endif
    </div>
@endsection