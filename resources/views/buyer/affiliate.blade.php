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
                <p class="text-xs uppercase tracking-wide text-surface-500 mb-2">Kode Referral</p>
                <div x-data="{
                        copy(text, label) {
                            const done = () => window.dispatchEvent(new CustomEvent('show-toast', { detail: label + ' tersalin' }));
                            if (navigator.clipboard) {
                                navigator.clipboard.writeText(text).then(done).catch(done);
                            } else {
                                const ta = document.createElement('textarea');
                                ta.value = text; document.body.appendChild(ta); ta.select();
                                document.execCommand('copy'); document.body.removeChild(ta);
                                done();
                            }
                        }
                    }"
                    class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-mono font-bold text-primary-700">{{ $profile->referral_code }}</span>
                        <button type="button" x-on:click="copy('{{ $profile->referral_code }}', 'Kode')"
                                class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:bg-primary-50 border border-primary-200 rounded-md px-2 py-1">
                            <x-icon name="check" size="xs" /> Salin
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <code class="flex-1 text-xs text-surface-600 bg-surface-50 border border-surface-200 rounded px-2 py-1.5 break-all">{{ url('/') }}?ref={{ $profile->referral_code }}</code>
                        <button type="button" x-on:click="copy('{{ url('/') }}?ref={{ $profile->referral_code }}', 'Link')"
                                class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:bg-primary-50 border border-primary-200 rounded-md px-2 py-1 shrink-0">
                            <x-icon name="check" size="xs" /> Salin
                        </button>
                    </div>
                </div>
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