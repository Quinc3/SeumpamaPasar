@extends('layouts.app')

@section('title', 'Produk Saya')

@section('content')
<x-page-wrapper>
    @if (session('status'))
        <x-alert variant="success">Perubahan disimpan.</x-alert>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-surface-900">Produk</h2>
        @if ($products->isNotEmpty())
            <a href="{{ route('seller.products.create') }}">
                <x-primary-button type="button">
                    <x-icon name="plus" size="sm" class="mr-1.5" />
                    Produk Baru
                </x-primary-button>
            </a>
        @endif
    </div>

    @if ($products->isEmpty())
        <x-card padding="none">
            <x-empty-state icon="tag" title="Belum ada produk" description="Tambahkan produk pertama Anda.">
                <x-slot:action>
                    <a href="{{ route('seller.products.create') }}">
                        <x-primary-button type="button">Tambah Produk</x-primary-button>
                    </a>
                </x-slot:action>
            </x-empty-state>
        </x-card>
    @else
        <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-surface-50 text-left text-xs uppercase text-surface-600">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Stok</th>
                            <th class="px-4 py-3">Kanal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($products as $p)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-900">{{ $p->name }}</p>
                                    <p class="text-xs text-surface-500">{{ $p->category->name }}</p>
                                </td>
                                <td class="px-4 py-3"><x-price :amount="$p->price" class="text-sm" /></td>
                                <td class="px-4 py-3 text-surface-700">{{ $p->stock }}</td>
                                <td class="px-4 py-3"><x-badge variant="neutral">{{ strtoupper($p->sales_channel->value) }}</x-badge></td>
                                <td class="px-4 py-3"><x-status-badge :status="$p->status->value" /></td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('seller.products.edit', $p->id) }}" class="text-xs text-primary-700 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-2">{{ $products->links() }}</div>
    @endif
</x-page-wrapper>
@endsection