@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <nav class="text-sm text-surface-500 mb-4 flex items-center gap-1.5">
            <a href="{{ route('catalog.index') }}" class="hover:text-surface-900">Katalog</a>
            <x-icon name="chevron-right" size="xs" />
            <a href="{{ route('catalog.index', ['kategori' => $product->category->slug]) }}" class="hover:text-surface-900">{{ $product->category->name }}</a>
            <x-icon name="chevron-right" size="xs" />
            <span class="text-surface-900 truncate">{{ $product->name }}</span>
        </nav>

        @if (session('status') === 'cart-added')
            <x-alert variant="success" class="mb-4">Produk ditambahkan ke keranjang.</x-alert>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10">
            <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
                <div class="aspect-square bg-surface-100">
                    @php $image = $product->images->first(); @endphp
                    @if ($image)
                        <img src="{{ Storage::url($image->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-surface-300">
                            <x-icon name="tag" size="2xl" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">{{ $product->category->name }}</p>
                    <h1 class="text-xl sm:text-2xl font-semibold text-surface-900 mt-1">{{ $product->name }}</h1>
                    <a href="#" class="inline-flex items-center gap-1 text-sm text-primary-700 hover:underline mt-1">
                        <x-icon name="user-circle" size="sm" />
                        {{ $product->sellerProfile->store_name }}
                    </a>
                </div>

                <div class="flex items-baseline gap-2">
                    <x-price :amount="$product->price" class="text-2xl" />
                    @if ($product->stock > 0)
                        <span class="text-xs text-surface-500">Stok {{ $product->stock }}</span>
                    @else
                        <span class="text-xs text-red-600 font-medium">Habis</span>
                    @endif
                </div>

                <p class="text-sm text-surface-700 leading-relaxed">{{ $product->description }}</p>

                @if ($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add', $product->slug) }}" class="pt-2">
                        @csrf
                        <x-input-label for="quantity" value="Jumlah" />
                        <div class="flex items-center gap-2 mt-1">
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                                class="w-24 rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500">
                            <x-primary-button type="submit">
                                <x-icon name="shopping-cart" size="sm" class="mr-1.5" />
                                Tambah ke Keranjang
                            </x-primary-button>
                        </div>
                        @error('quantity')
                            <x-input-error :messages="[$message]" class="mt-2" />
                        @enderror
                    </form>
                @else
                    <x-alert variant="warning">Produk sedang habis.</x-alert>
                @endif
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-10">
                <h2 class="text-base font-semibold text-surface-900 mb-3">Produk Serupa</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection