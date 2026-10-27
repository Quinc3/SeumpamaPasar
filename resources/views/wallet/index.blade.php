@extends('layouts.app')

@section('title', 'SeumpamaPay')

@section('content')
    <x-page-wrapper max="3xl">
        @if (session('status') === 'topup-success')
            <x-alert variant="success">
                Top up berhasil! Saldo bertambah Rp {{ number_format(session('topup_amount', 0), 0, ',', '.') }}.
            </x-alert>
        @endif
        @if (session('error'))
            <x-alert variant="danger">{{ session('error') }}</x-alert>
        @endif

        {{-- Kartu saldo --}}
        <div class="rounded-xl bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-white/20 font-bold text-sm">SP</span>
                    <span class="text-sm font-semibold">SeumpamaPay</span>
                </div>
                <x-icon name="banknotes" size="md" class="text-white/80" />
            </div>
            <p class="text-xs text-white/70 uppercase tracking-wide mt-4">Saldo</p>
            <p class="text-3xl font-bold mt-1">Rp {{ number_format((float) $wallet->balance, 0, ',', '.') }}</p>
            <p class="text-xs text-white/60 mt-1">
                Maks. saldo Rp {{ number_format(\App\Services\WalletService::MAX_BALANCE, 0, ',', '.') }}
            </p>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('wallet.topup') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-primary-700 text-sm font-semibold rounded-md hover:bg-surface-100">
                    <x-icon name="plus" size="sm" /> Top Up
                </a>
            </div>
        </div>

        {{-- Riwayat --}}
        <x-card>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-surface-900">Riwayat Transaksi</h2>
            </div>

            @if ($transactions->isEmpty())
                <x-empty-state icon="banknotes" title="Belum ada transaksi" description="Top up saldo untuk mulai bertransaksi." />
            @else
                <div class="divide-y divide-surface-100">
                    @foreach ($transactions as $tx)
                        @php
                            $isCredit = in_array($tx->type, ['topup', 'refund'], true);
                            $labels = [
                                'topup'   => ['Top Up',       'plus',           'success'],
                                'payment' => ['Pembayaran',   'shopping-cart',  'danger'],
                                'refund'  => ['Refund',       'arrow-path',     'info'],
                            ];
                            [$label, $icon, $variant] = $labels[$tx->type] ?? ['Transaksi', 'banknotes', 'neutral'];
                        @endphp
                        <div class="py-3 flex items-start gap-3">
                            <div class="rounded-full p-2 shrink-0 bg-{{ $variant === 'success' ? 'green' : ($variant === 'danger' ? 'red' : 'blue') }}-50">
                                <x-icon :name="$icon" size="md" class="text-{{ $variant === 'success' ? 'green' : ($variant === 'danger' ? 'red' : 'blue') }}-600" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-surface-900">{{ $label }}</p>
                                <p class="text-xs text-surface-500 truncate">{{ $tx->description }}</p>
                                <p class="text-xs text-surface-400">{{ $tx->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-semibold {{ $isCredit ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $isCredit ? '+' : '-' }} Rp {{ number_format((float) $tx->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-surface-500">Saldo: Rp {{ number_format((float) $tx->balance_after, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pt-3">{{ $transactions->links() }}</div>
            @endif
        </x-card>
    </x-page-wrapper>
@endsection