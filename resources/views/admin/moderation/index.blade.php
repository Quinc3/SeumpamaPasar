@extends('layouts.admin')

@section('title', 'Moderasi Produk')

@section('content')
    @if (session('status'))
        <x-alert variant="success">Perubahan disimpan.</x-alert>
    @endif

    @if ($products->isEmpty())
        <x-card padding="none">
            <x-empty-state icon="check-circle" title="Tidak ada produk menunggu review" description="Produk baru dari seller akan muncul di sini." />
        </x-card>
    @else
        <div class="space-y-2">
            @foreach ($products as $p)
                <x-card>
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-surface-900">{{ $p->name }}</p>
                                <x-status-badge :status="$p->status->value" />
                                <x-badge variant="neutral">{{ strtoupper($p->sales_channel->value) }}</x-badge>
                            </div>
                            <p class="text-xs text-surface-500 mt-0.5">
                                {{ $p->sellerProfile->store_name }} · {{ $p->sellerProfile->user->email }} · {{ $p->category->name }}
                            </p>
                            <p class="text-sm text-surface-700 mt-2 line-clamp-2">{{ $p->description }}</p>
                            <p class="text-xs text-surface-500 mt-1">
                                <x-price :amount="$p->price" class="text-xs" /> · Stok {{ $p->stock }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 shrink-0">
                            <form method="POST" action="{{ route('admin.moderation.approve', $p->id) }}">
                                @csrf
                                <x-primary-button type="submit" class="w-full justify-center">
                                    <x-icon name="check" size="sm" class="mr-1.5" /> Setujui
                                </x-primary-button>
                            </form>
                            <form method="POST" action="{{ route('admin.moderation.reject', $p->id) }}" x-data="{ showNote: false }">
                                @csrf
                                <div x-show="showNote" x-cloak class="space-y-1">
                                    <textarea name="rejection_note" rows="2" placeholder="Alasan penolakan" class="w-full text-xs rounded-md border-surface-300 focus:border-primary-500 focus:ring-primary-500" required></textarea>
                                    <x-danger-button type="submit" class="w-full justify-center">Kirim Penolakan</x-danger-button>
                                </div>
                                <button type="button" x-show="!showNote" x-on:click="showNote = true" class="w-full text-xs text-red-600 hover:underline">Tolak</button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
        <div class="pt-2">{{ $products->links() }}</div>
    @endif
@endsection