@extends('layouts.app')

@section('title', 'Dashboard Afiliasi')

@section('content')
<x-page-wrapper>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-card>
            <p class="text-xs uppercase tracking-wide text-surface-500">Kode Referral</p>
            <p class="text-lg font-mono font-bold text-primary-700 mt-1">{{ $affiliate->referral_code }}</p>
            <p class="text-xs text-surface-500 mt-2 break-all">{{ url('/') }}?ref={{ $affiliate->referral_code }}</p>
        </x-card>
        <x-card>
            <p class="text-xs uppercase tracking-wide text-surface-500">Total Komisi (Disetujui)</p>
            <x-price :amount="$approvedSum" class="text-2xl" />
            @if ($pendingSum > 0)
                <p class="text-xs text-surface-500 mt-1">Pending: <x-price :amount="$pendingSum" class="text-xs" /></p>
            @endif
        </x-card>
    </div>

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

    <x-card>
        <h2 class="text-sm font-semibold text-surface-900 mb-3">Referral</h2>
        @if ($affiliate->referrals->isEmpty())
            <p class="text-sm text-surface-500">Belum ada referral tercatat.</p>
        @else
            <p class="text-sm text-surface-700">Total referral: <strong>{{ $affiliate->referrals->count() }}</strong></p>
        @endif
    </x-card>
</x-page-wrapper>
@endsection