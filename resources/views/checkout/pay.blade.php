@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4"
         x-data="countdown('{{ $checkout->payment->expires_at?->toIso8601String() }}')"
         x-init="init()">
        @php
            $user = $checkout->user;
            $uid = str_pad((string) $user->id, 4, '0', STR_PAD_LEFT);
            $cid = str_pad((string) $checkout->id, 8, '0', STR_PAD_LEFT);
            $method = $checkout->payment->method;

            // Prefix bank VA
            $vaBanks = [
                'va_bca' => ['bank' => 'BCA', 'prefix' => '8808'],
                'va_bni' => ['bank' => 'BNI', 'prefix' => '8810'],
                'va_bri' => ['bank' => 'BRI', 'prefix' => '8888'],
                'va_mandiri' => ['bank' => 'Mandiri', 'prefix' => '8911'],
                'va_bsi' => ['bank' => 'BSI', 'prefix' => '9001'],
            ];

            if (isset($vaBanks[$method])) {
                $bankName = $vaBanks[$method]['bank'];
                $vaNumber = chunk_split($vaBanks[$method]['prefix'] . $uid . $cid, 4, ' ');
            } elseif (str_starts_with($method, 'retail_')) {
                $retailCode = strtoupper(substr($method, 7)) . $uid . substr($cid, 0, 6);
            } elseif ($method === 'ewallet_seumpamapay' || str_starts_with($method, 'ewallet_')) {
                $ewalletNumber = '0812-' . $uid . '-' . substr($cid, 4);
            } elseif ($method === 'demo_transfer') {
                $vaNumber = null;
            }
        @endphp

        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Pembayaran</h1>
            <a href="{{ route('cart.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← Keranjang</a>
        </div>

        {{-- Countdown --}}
        @if ($checkout->payment->expires_at)
            <x-card class="border-amber-200 bg-amber-50">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-sm">
                        <x-icon name="exclamation-triangle" size="md" class="text-amber-600" />
                        <span class="text-surface-700">Selesaikan pembayaran dalam</span>
                    </div>
                    <span class="text-lg font-mono font-bold text-amber-700" x-text="formatted"></span>
                </div>
            </x-card>
        @endif

        <x-card>
            @if (isset($vaBanks[$method]))
                <div class="text-center pb-4 border-b border-surface-200 mb-4">
                    <p class="text-xs uppercase tracking-wide text-surface-500">Virtual Account {{ $bankName }}</p>
                    <p class="text-2xl sm:text-3xl font-mono font-bold text-primary-700 tracking-wider mt-2">{{ trim($vaNumber) }}</p>
                    <p class="text-xs text-surface-500 mt-1.5">a.n. {{ $user->name }}</p>
                    <p class="text-xs text-surface-400 mt-1">Simulasi — tidak ada transaksi nyata</p>
                </div>
            @elseif (str_starts_with($method, 'retail_'))
                <div class="text-center pb-4 border-b border-surface-200 mb-4">
                    <p class="text-xs uppercase tracking-wide text-surface-500">
                        Kode Pembayaran {{ strtoupper(str_replace('retail_', '', $method)) }}
                    </p>
                    <p class="text-2xl sm:text-3xl font-mono font-bold text-primary-700 tracking-wider mt-2">{{ $retailCode }}</p>
                    <p class="text-xs text-surface-500 mt-1.5">Tunjukkan kode ini di kasir {{ ucfirst(str_replace('retail_', '', $method)) }}</p>
                    <p class="text-xs text-surface-400 mt-1">Simulasi — tidak ada transaksi nyata</p>
                </div>
                @elseif (str_starts_with($method, 'ewallet_') && $method !== 'ewallet_seumpamapay')
                @php $provider = ucfirst(str_replace('ewallet_', '', $method)); @endphp
                <div class="text-center pb-4 border-b border-surface-200 mb-4">
                    <div class="inline-flex items-center gap-2 justify-center">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded bg-surface-300 text-white text-[10px] font-bold">{{ strtoupper(substr($provider, 0, 2)) }}</span>
                        <span class="text-sm font-semibold text-surface-900">{{ $provider }}</span>
                    </div>
                    <p class="text-sm text-surface-600 mt-3">Buka aplikasi <strong>{{ $provider }}</strong> di HP Anda untuk melanjutkan pembayaran.</p>
                    <p class="text-xs text-surface-400 mt-1">Simulasi — tidak ada transaksi nyata</p>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">Referensi</p>
                    <p class="font-mono text-surface-900 mt-0.5 break-all">{{ $checkout->reference }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">Metode</p>
                    <p class="text-surface-900 mt-0.5">{{ ucwords(str_replace(['_', 'va ', 'retail ', 'ewallet '], [' ', '', '', ''], $method)) }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">Total</p>
                    <x-price :amount="$checkout->total_amount" class="text-lg" />
                </div>
            </div>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-2">Rincian Pesanan</h2>
            @foreach ($checkout->orders as $order)
                <div class="py-2 border-t border-surface-100 first:border-t-0">
                    <p class="text-xs uppercase tracking-wide text-surface-500">{{ $order->sellerProfile->store_name }}</p>
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-2 text-sm mt-1">
                            <span class="text-surface-700 truncate">{{ $item->product_name }} ×{{ $item->quantity }}</span>
                            <x-price :amount="$item->subtotal" class="text-sm shrink-0" />
                        </div>
                    @endforeach
                </div>
            @endforeach
        </x-card>

        <x-card>
            <p class="text-sm text-surface-700 mb-3">
                Pilih hasil yang ingin disimulasikan:
            </p>
            <div class="flex flex-col sm:flex-row gap-2">
                <form method="POST" action="{{ route('checkout.pay.submit', $checkout->reference) }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="outcome" value="success">
                    <x-primary-button type="submit" class="w-full justify-center">
                        <x-icon name="check-circle" size="sm" class="mr-1.5" />
                        Simulasikan Berhasil
                    </x-primary-button>
                </form>
                <form method="POST" action="{{ route('checkout.pay.submit', $checkout->reference) }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="outcome" value="fail">
                    <x-danger-button type="submit" class="w-full justify-center">
                        <x-icon name="x-circle" size="sm" class="mr-1.5" />
                        Simulasikan Gagal
                    </x-danger-button>
                </form>
            </div>
        </x-card>
    </div>

    <script>
        function countdown(expiresAtIso) {
            return {
                expiresAt: expiresAtIso,
                remaining: 0,
                timer: null,
                init() {
                    if (!this.expiresAt) return;
                    const end = new Date(this.expiresAt).getTime();
                    const tick = () => {
                        this.remaining = Math.max(0, Math.floor((end - Date.now()) / 1000));
                        if (this.remaining === 0) {
                            clearInterval(this.timer);
                            window.location.reload();
                        }
                    };
                    tick();
                    this.timer = setInterval(tick, 1000);
                },
                get formatted() {
                    const h = Math.floor(this.remaining / 3600);
                    const m = Math.floor((this.remaining % 3600) / 60);
                    const s = this.remaining % 60;
                    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                }
            };
        }
    </script>
@endsection