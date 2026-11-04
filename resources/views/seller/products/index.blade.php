@extends('layouts.app')

@section('title', 'Produk Saya')

@section('content')
    <x-page-wrapper>
        @if (session('status'))
            <x-alert variant="success">{{ session('status') }}</x-alert>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
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

        {{-- Search & filter --}}
        <form method="GET" action="{{ route('seller.products.index') }}" class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <x-icon name="magnifying-glass" size="md" class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none" />
                <input type="search" name="q" value="{{ request('q') }}"
                    placeholder="Cari nama atau deskripsi produk…"
                    class="w-full pl-10 pr-3 py-2 rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <select name="status" class="rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="pending_review" @selected(request('status') === 'pending_review')>Menunggu Review</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                <option value="draft" @selected(request('status') === 'draft')>Draf</option>
                <option value="archived" @selected(request('status') === 'archived')>Diarsipkan</option>
            </select>
            <x-primary-button type="submit">Cari</x-primary-button>
            @if (request('q') || request('status'))
                <a href="{{ route('seller.products.index') }}">
                    <x-secondary-button type="button">Reset</x-secondary-button>
                </a>
            @endif
        </form>

        @if ($products->isEmpty())
            <x-card padding="none">
                <x-empty-state icon="tag"
                    title="{{ request('q') || request('status') ? 'Tidak ada produk yang cocok' : 'Belum ada produk' }}"
                    description="{{ request('q') || request('status') ? 'Coba ubah kata kunci atau filter.' : 'Tambahkan produk pertama Anda.' }}">
                    <x-slot:action>
                        @if (request('q') || request('status'))
                            <a href="{{ route('seller.products.index') }}">
                                <x-secondary-button type="button">Reset filter</x-secondary-button>
                            </a>
                        @else
                            <a href="{{ route('seller.products.create') }}">
                                <x-primary-button type="button">Tambah Produk</x-primary-button>
                            </a>
                        @endif
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @else
            <form method="POST" action="{{ route('seller.products.bulk-archive') }}"
                  onsubmit="return confirm('Arsipkan semua produk yang dipilih?')">
                @csrf
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <x-danger-button type="submit">Arsipkan yang dipilih</x-danger-button>
                    <span id="bulk-count" class="text-sm text-surface-500">0 dipilih</span>
                </div>

                <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-surface-50 text-left text-xs uppercase text-surface-600">
                                <tr>
                                    <th class="px-3 py-3 w-10">
                                        <input type="checkbox" id="select-all" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                    </th>
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
                                        <td class="px-3 py-3">
                                            @if ($p->status !== \App\Enums\ProductStatus::Archived)
                                                <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="bulk-checkbox rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                            @else
                                                <span class="inline-block w-4 h-4 rounded border border-surface-200 bg-surface-50"></span>
                                            @endif
                                        </td>
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
            </form>
            <div class="pt-2">{{ $products->links() }}</div>
        @endif
    </x-page-wrapper>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const boxes = document.querySelectorAll('.bulk-checkbox');
            const counter = document.getElementById('bulk-count');

            function updateCount() {
                const n = document.querySelectorAll('.bulk-checkbox:checked').length;
                if (counter) counter.textContent = n + ' dipilih';
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    boxes.forEach(cb => { cb.checked = selectAll.checked; });
                    updateCount();
                });
            }

            boxes.forEach(cb => cb.addEventListener('change', updateCount));
        });
    </script>
@endsection