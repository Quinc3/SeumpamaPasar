@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
{{-- Hero carousel --}}
<x-hero-carousel />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">
        {{-- Kategori --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base sm:text-lg font-semibold text-surface-900">Kategori</h2>
                <a href="{{ route('catalog.index') }}" class="text-xs text-primary-700 hover:underline">Lihat semua →</a>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-2">
                @foreach ($categories as $cat)
                    <a href="{{ route('catalog.index', ['kategori' => $cat->slug]) }}" class="group flex flex-col items-center justify-center bg-white border border-surface-200 rounded-lg py-4 hover:border-primary-400 hover:shadow-card-hover transition">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-50 text-primary-700 group-hover:bg-primary-100 mb-2">
                            <x-icon name="tag" size="md" />
                        </div>
                        <span class="text-xs sm:text-sm font-medium text-surface-900 text-center line-clamp-1 px-1">{{ $cat->name }}</span>
                        <span class="text-[10px] text-surface-500 mt-0.5">{{ $cat->products_count }} produk</span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Featured --}}
        @if ($featured->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base sm:text-lg font-semibold text-surface-900 flex items-center gap-2">
                        <x-icon name="bolt" size="md" class="text-amber-500" /> Produk Unggulan
                    </h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($featured as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Bestsellers --}}
        @if ($bestsellers->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base sm:text-lg font-semibold text-surface-900">Produk Terlaris</h2>
                    <a href="{{ route('catalog.index', ['sort' => 'terlaris']) }}" class="text-xs text-primary-700 hover:underline">Lihat semua →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($bestsellers as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Newest --}}
        @if ($newest->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base sm:text-lg font-semibold text-surface-900">Produk Terbaru</h2>
                    <a href="{{ route('catalog.index') }}" class="text-xs text-primary-700 hover:underline">Lihat semua →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($newest as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection