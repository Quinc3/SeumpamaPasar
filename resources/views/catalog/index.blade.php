@extends('layouts.app')

@section('title', 'Katalog')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Katalog Produk</h1>
            <p class="text-sm text-surface-600 mt-0.5">{{ $products->total() }} produk ditemukan</p>
        </div>

        <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
            <div class="relative flex-1">
                <x-icon name="magnifying-glass" size="md" class="absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none" />
                <input type="search" name="q" value="{{ $search ?? '' }}" placeholder="Cari produk…"
                    class="w-full pl-10 pr-3 py-2 rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <select name="kategori" class="rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(($catSlug ?? '') === $cat->slug)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="sort" class="rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="terbaru" @selected(($sort ?? '') === 'terbaru')>Terbaru</option>
                <option value="terlaris" @selected(($sort ?? '') === 'terlaris')>Terlaris</option>
                <option value="termurah" @selected(($sort ?? '') === 'termurah')>Harga Termurah</option>
                <option value="termahal" @selected(($sort ?? '') === 'termahal')>Harga Termahal</option>
            </select>
            <x-primary-button type="submit">Filter</x-primary-button>
        </form>

        @if ($products->isEmpty())
            <x-empty-state
                icon="magnifying-glass"
                title="Tidak ada produk"
                description="Coba ubah kata kunci atau filter." />
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="pt-2">{{ $products->links() }}</div>
        @endif
    </div>
@endsection