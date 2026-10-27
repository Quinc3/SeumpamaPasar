@extends('layouts.admin')

@section('title', 'Kelola Penjual')

@section('content')
@if (session('status'))
    <x-alert variant="success">{{ session('status') }}</x-alert>
@endif

    @if ($sellers->isEmpty())
        <x-card padding="none">
            <x-empty-state icon="home" title="Belum ada penjual" description="Pengajuan toko akan muncul di sini." />
        </x-card>
    @else
        <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-surface-50 text-left text-xs uppercase text-surface-600">
                        <tr>
                            <th class="px-4 py-3">Toko</th>
                            <th class="px-4 py-3">Pemilik</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($sellers as $s)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-900">{{ $s->store_name }}</p>
                                    <p class="text-xs text-surface-500">{{ $s->products->count() }} produk</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-surface-900">{{ $s->user->name }}</p>
                                    <p class="text-xs text-surface-500">{{ $s->user->email }}</p>
                                </td>
                                <td class="px-4 py-3"><x-badge variant="neutral">{{ ucfirst($s->seller_type->value) }}</x-badge></td>
                                <td class="px-4 py-3"><x-status-badge :status="$s->status->value" /></td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    @if ($s->status === \App\Enums\SellerStatus::Pending)
                                        <form method="POST" action="{{ route('admin.sellers.approve', $s->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-primary-700 hover:underline font-medium">Setujui</button>
                                        </form>
                                    @elseif ($s->status === \App\Enums\SellerStatus::Active)
                                        <form method="POST" action="{{ route('admin.sellers.suspend', $s->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-600 hover:underline">Tangguhkan</button>
                                        </form>
                                    @elseif ($s->status === \App\Enums\SellerStatus::Suspended)
                                        <form method="POST" action="{{ route('admin.sellers.activate', $s->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-primary-700 hover:underline font-medium">Aktifkan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-2">{{ $sellers->links() }}</div>
    @endif
@endsection