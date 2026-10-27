@extends('layouts.app')

@section('title', 'Dashboard Afiliasi')

@section('content')
    <x-page-wrapper>
        {{-- Kode & Link Referral --}}
        <x-card x-data="{
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
            }">
            <p class="text-xs uppercase tracking-wide text-surface-500 mb-2">Kode Referral</p>
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl font-mono font-bold text-primary-700">{{ $affiliate->referral_code }}</span>
                <button type="button" x-on:click="copy('{{ $affiliate->referral_code }}', 'Kode')"
                        class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:bg-primary-50 border border-primary-200 rounded-md px-2 py-1"
                        title="Salin kode">
                    <x-icon name="copy" size="sm" /> Salin
                </button>
            </div>

            <p class="text-xs uppercase tracking-wide text-surface-500 mb-2">Link Referral</p>
            <div class="flex items-center gap-2">
                <code class="flex-1 text-xs text-surface-600 bg-surface-50 border border-surface-200 rounded px-2 py-1.5 break-all">
                    {{ url('/katalog') }}?ref={{ $affiliate->referral_code }}
                </code>
                <button type="button" x-on:click="copy('{{ url('/katalog') }}?ref={{ $affiliate->referral_code }}', 'Link')"
                        class="inline-flex items-center gap-1 text-xs font-medium text-primary-700 hover:bg-primary-50 border border-primary-200 rounded-md px-2 py-1 shrink-0"
                        title="Salin link">
                    <x-icon name="copy" size="sm" /> Salin
                </button>
            </div>
        </x-card>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-card>
                <p class="text-xs uppercase tracking-wide text-surface-500">Total Komisi (Disetujui)</p>
                <x-price :amount="$approvedSum" class="text-2xl mt-1 block" />
                @if ($pendingSum > 0)
                    <p class="text-xs text-surface-500 mt-1">Pending: <x-price :amount="$pendingSum" class="text-xs" /></p>
                @endif
            </x-card>
            <x-card>
                <p class="text-xs uppercase tracking-wide text-surface-500">Total Referral</p>
                <p class="text-2xl font-semibold text-surface-900 mt-1">{{ $affiliate->referrals->count() }}</p>
            </x-card>
        </div>

        {{-- Riwayat Komisi --}}
        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-3">Riwayat Komisi</h2>
            @if ($affiliate->commissions->isEmpty())
                <x-empty-state icon="banknotes" title="Belum ada komisi" description="Komisi muncul saat pesanan lewat link Anda berstatus Selesai." />
            @else
                <div class="divide-y divide-surface-100">
                    @foreach ($affiliate->commissions->sortByDesc('id') as $c)
                        <div class="py-2 flex items-center justify-between text-sm">
                            <div>
                                <p class="font-medium text-surface-900">Order #{{ $c->order_id }}</p>
                                <p class="text-xs text-surface-500">{{ $c->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <x-price :amount="$c->amount" class="text-sm font-semibold" />
                                <div class="mt-0.5"><x-status-badge :status="$c->status->value" /></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </x-page-wrapper>
@endsection