@props(['product'])

@php
    $image = $product->images->first();
@endphp

<a href="{{ route('catalog.show', $product->slug) }}" class="group block bg-white border border-surface-200 rounded-lg overflow-hidden hover:shadow-card-hover transition">
    <div class="aspect-square bg-surface-100 overflow-hidden">
        @if ($image)
            <img src="{{ Storage::url($image->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-surface-300">
                <x-icon name="tag" size="2xl" />
            </div>
        @endif
    </div>
    <div class="p-3">
        <h3 class="text-sm font-medium text-surface-900 line-clamp-2 leading-snug group-hover:text-primary-700 transition">
            {{ $product->name }}
        </h3>
        <p class="text-xs text-surface-500 mt-1 truncate">{{ $product->sellerProfile->store_name }}</p>
        <div class="mt-2 flex items-center justify-between gap-2">
            <x-price :amount="$product->price" class="text-sm" />
            @if ($product->stock > 0)
                <span class="text-[10px] text-surface-500">Stok {{ $product->stock }}</span>
            @else
                <span class="text-[10px] text-red-600 font-medium">Habis</span>
            @endif
        </div>
    </div>
</a>