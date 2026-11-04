@props(['product', 'isTourTarget' => false])

@php
    $image = $product->images->first();
    $hasDiscount = $product->hasDiscount();
    $displayPrice = $hasDiscount ? (float) $product->discount_price : (float) $product->price;
@endphp

<a href="{{ route('catalog.show', $product->slug) }}" @if($isTourTarget) data-tour-product-card @endif
   class="group block bg-white border border-surface-200 rounded-lg overflow-hidden hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200">
    <div class="relative aspect-square bg-surface-100 overflow-hidden">
        @if ($image)
            <img src="{{ Storage::url($image->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-surface-300">
                <x-icon name="tag" size="2xl" />
            </div>
        @endif

        @if ($hasDiscount)
            <div class="absolute top-2 left-2 inline-flex flex-col items-center bg-red-500 text-white px-2 py-1 rounded-md shadow-sm">
                <span class="text-[10px] font-bold leading-none">-{{ $product->discountPercent() }}%</span>
            </div>
        @endif
        @if ($product->is_featured && ! $hasDiscount)
            <div class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-sm">
                TERLARIS
            </div>
        @endif
    </div>

    <div class="p-3">
        <h3 class="text-sm font-medium text-surface-900 line-clamp-2 leading-snug group-hover:text-primary-700 transition min-h-[2.5rem]">
            {{ $product->name }}
        </h3>

        <div class="mt-1.5 flex items-center gap-1.5 text-xs text-surface-500">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-amber-500">
                <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
            </svg>
            <span class="font-medium text-surface-900">{{ number_format((float) $product->rating, 1) }}</span>
            <span>·</span>
            <span>{{ $product->sold_count }} terjual</span>
        </div>

        <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
            @if ($hasDiscount)
                <span class="text-[11px] text-surface-400 line-through">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</span>
            @endif
            <x-price :amount="$displayPrice" class="text-sm font-bold text-primary-700" />
        </div>

        <p class="text-[11px] text-surface-500 mt-1 truncate">{{ $product->sellerProfile->store_name }}</p>
    </div>
</a>