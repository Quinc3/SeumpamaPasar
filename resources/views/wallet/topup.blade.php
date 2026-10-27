@extends('layouts.app')

@section('title', 'Top Up SeumpamaPay')

@section('content')
    <x-page-wrapper max="3xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-surface-900">Top Up Saldo</h1>
            <a href="{{ route('wallet.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← SeumpamaPay</a>
        </div>

        @if (session('error'))
            <x-alert variant="danger">{{ session('error') }}</x-alert>
        @endif

        @if (! empty($returnTo))
            <x-alert variant="info">
                Anda melakukan top up untuk menyelesaikan <strong>Checkout {{ $returnTo }}</strong>.
                Setelah top up berhasil, Anda akan diarahkan ke halaman pembayaran.
            </x-alert>
        @endif

        <x-card>
            <p class="text-sm text-surface-600">Saldo saat ini</p>
            <p class="text-2xl font-bold text-surface-900">Rp {{ number_format((float) $wallet->balance, 0, ',', '.') }}</p>
        </x-card>

        @if ($errors->any())
            <x-alert variant="danger">Periksa kembali isian form.</x-alert>
        @endif

        <form method="POST" action="{{ route('wallet.topup.submit') }}"
              x-data="{ amount: 50000, customMode: false, customAmount: '' }">
            @csrf
            @if (! empty($returnTo))
                <input type="hidden" name="return_to" value="{{ $returnTo }}">
            @endif

            {{-- Pilih nominal --}}
            <x-card>
                <h2 class="text-sm font-semibold text-surface-900 mb-3">Pilih Nominal</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ([50000, 100000, 250000, 500000, 1000000] as $preset)
                        <button type="button"
                                x-on:click="amount = {{ $preset }}; customMode = false"
                                :class="! customMode && amount === {{ $preset }} ? 'border-primary-500 bg-primary-50' : 'border-surface-200 bg-white hover:border-primary-300'"
                                class="p-3 rounded-md border text-center transition">
                            <span class="text-sm font-semibold text-surface-900">Rp {{ number_format($preset, 0, ',', '.') }}</span>
                        </button>
                    @endforeach
                    <button type="button"
                            x-on:click="customMode = true; amount = parseInt(customAmount) || 0"
                            :class="customMode ? 'border-primary-500 bg-primary-50' : 'border-surface-200 bg-white hover:border-primary-300'"
                            class="p-3 rounded-md border text-center transition col-span-2 sm:col-span-1">
                        <span class="text-sm font-semibold text-surface-900">Nominal Lain</span>
                    </button>
                </div>

                <div x-show="customMode" x-cloak class="mt-3">
                    <x-input-label for="custom_amount_input" value="Nominal (Rp)" />
                    <input type="number" id="custom_amount_input"
                           x-model="customAmount"
                           x-on:input="amount = parseInt(customAmount) || 0"
                           min="10000" max="10000000" step="10000"
                           placeholder="Min Rp 10.000"
                           class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
                    <p class="text-xs text-surface-500 mt-1">Minimal Rp 10.000, maksimal Rp 10.000.000</p>
                </div>

                <input type="hidden" name="amount" x-bind:value="amount">

                <div class="mt-3 p-3 bg-surface-50 border border-surface-200 rounded-md flex items-center justify-between">
                    <span class="text-sm text-surface-600">Nominal top up</span>
                    <span class="text-lg font-bold text-primary-700">Rp <span x-text="(amount || 0).toLocaleString('id-ID')"></span></span>
                </div>

                @error('amount') <x-input-error :messages="[$message]" class="mt-2" /> @enderror
            </x-card>

            {{-- Metode pembayaran --}}
            <x-card>
                <h2 class="text-sm font-semibold text-surface-900 mb-3">Metode Top Up</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ([
                        'va_bca' => 'BCA Virtual Account',
                        'va_bni' => 'BNI Virtual Account',
                        'va_bri' => 'BRI Virtual Account',
                        'va_mandiri' => 'Mandiri Virtual Account',
                        'va_bsi' => 'BSI Virtual Account',
                    ] as $value => $label)
                        <label class="flex items-center gap-2.5 p-2.5 border border-surface-200 rounded-md cursor-pointer hover:bg-surface-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                            <input type="radio" name="method" value="{{ $value }}" class="text-primary-600 focus:ring-primary-500" @checked($loop->first) required>
                            <span class="text-sm text-surface-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </x-card>

            {{-- Simulasi hasil --}}
            <x-card>
                <p class="text-sm text-surface-700 mb-3">Top up ini <strong>simulasi</strong>. Pilih hasil yang ingin disimulasikan:</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" name="outcome" value="success"
                            x-bind:disabled="!amount || amount < 10000"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-primary-700 text-white text-sm font-semibold rounded-md hover:bg-primary-800 disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-icon name="check-circle" size="sm" /> Simulasikan Berhasil
                    </button>
                    <button type="submit" name="outcome" value="fail"
                            x-bind:disabled="!amount || amount < 10000"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-icon name="x-circle" size="sm" /> Simulasikan Gagal
                    </button>
                </div>
            </x-card>
        </form>
    </x-page-wrapper>
@endsection