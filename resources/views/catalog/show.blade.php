@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <nav class="text-sm text-surface-500 mb-4 flex items-center gap-1.5 flex-wrap">
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
            {{-- Gambar --}}
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

            {{-- Info produk --}}
            <div class="space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-surface-500">{{ $product->category->name }}</p>
                    <h1 class="text-xl sm:text-2xl font-semibold text-surface-900 mt-1">{{ $product->name }}</h1>
                    <a href="#" class="inline-flex items-center gap-1 text-sm text-primary-700 hover:underline mt-1">
                        <x-icon name="user-circle" size="sm" />
                        {{ $product->sellerProfile->store_name }}
                    </a>
                </div>

                {{-- Rating summary --}}
                <div class="flex items-center gap-2 text-sm">
                    <div class="flex items-center text-amber-500">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= round($product->rating) ? 'text-amber-500' : 'text-surface-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="font-semibold text-surface-900">{{ number_format((float) $product->rating, 1) }}</span>
                    <span class="text-surface-500">·</span>
                    <span class="text-surface-600">{{ $reviewCount }} ulasan</span>
                    <span class="text-surface-500">·</span>
                    <span class="text-surface-600">{{ $product->sold_count }} terjual</span>
                </div>

                {{-- Harga --}}
                <div class="flex flex-col gap-2">
                    <div class="flex items-baseline gap-2 flex-wrap">
                        <x-price :amount="$displayUnitPrice" class="text-2xl" />
                        @if ($isMember && $memberDiscount > 0)
                            <span class="text-xs bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full font-medium">
                                Diskon Member 5%
                            </span>
                        @endif
                        @if ($product->stock > 0)
                            <span class="text-xs text-surface-500">Stok {{ $product->stock }}</span>
                        @else
                            <span class="text-xs text-red-600 font-medium">Habis</span>
                        @endif
                    </div>

                    @if ($isVerified && $product->sales_channel !== \App\Enums\SalesChannel::B2C)
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <x-badge variant="primary">Harga Grosir</x-badge>
                            @if ($product->min_purchase_qty > 1)
                                <span class="text-surface-600">Min. pembelian: <strong>{{ $product->min_purchase_qty }}</strong></span>
                            @endif
                        </div>
                        @if ($product->wholesalePriceTiers->isNotEmpty())
                            <div class="bg-surface-50 border border-surface-200 rounded-md p-2 mt-1">
                                <p class="text-xs font-medium text-surface-700 mb-1">Tier Harga:</p>
                                <table class="w-full text-xs">
                                    <tbody>
                                        @foreach ($product->wholesalePriceTiers->sortByDesc('min_quantity') as $tier)
                                            <tr class="border-t border-surface-100 first:border-t-0">
                                                <td class="py-1 text-surface-600">≥ {{ $tier->min_quantity }} pcs</td>
                                                <td class="py-1 text-right font-medium text-surface-900">
                                                    Rp {{ number_format((float) $tier->price, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- CTA --}}
                @if (auth()->user()?->role === \App\Enums\UserRole::Admin)
                    <x-alert variant="info">Anda login sebagai admin. Hanya dapat melihat, tidak dapat membeli.</x-alert>
                @elseif ($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add', $product->slug) }}" class="pt-2">
                        @csrf
                        <x-input-label for="quantity" value="Jumlah" />
                        <div class="flex items-center gap-2 mt-1">
                            <input type="number" name="quantity" id="quantity"
                                value="{{ max(1, $resolved['min_qty']) }}"
                                min="{{ max(1, $resolved['min_qty']) }}"
                                max="{{ $product->stock }}"
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

        {{-- Tabs: Deskripsi | Detail | Ulasan --}}
        <div class="mt-8" x-data="{ tab: 'deskripsi' }">
            <div class="flex border-b border-surface-200 overflow-x-auto">
                <button type="button" x-on:click="tab='deskripsi'"
                        :class="tab === 'deskripsi' ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-surface-600 hover:text-surface-900'"
                        class="px-4 py-3 text-sm border-b-2 whitespace-nowrap">
                    Deskripsi
                </button>
                <button type="button" x-on:click="tab='detail'"
                        :class="tab === 'detail' ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-surface-600 hover:text-surface-900'"
                        class="px-4 py-3 text-sm border-b-2 whitespace-nowrap">
                    Detail Produk
                </button>
                <button type="button" x-on:click="tab='ulasan'"
                        :class="tab === 'ulasan' ? 'border-primary-700 text-primary-700 font-semibold' : 'border-transparent text-surface-600 hover:text-surface-900'"
                        class="px-4 py-3 text-sm border-b-2 whitespace-nowrap">
                    Ulasan ({{ $reviewCount }})
                </button>
            </div>

            {{-- Tab Deskripsi --}}
            <div x-show="tab === 'deskripsi'" class="py-6">
                <x-card>
                    <p class="text-sm text-surface-700 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                </x-card>
            </div>

            {{-- Tab Detail --}}
            <div x-show="tab === 'detail'" x-cloak class="py-6">
                <x-card>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-surface-500">Kategori</dt>
                            <dd class="text-surface-900 mt-0.5">{{ $product->category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-surface-500">Penjual</dt>
                            <dd class="text-surface-900 mt-0.5">{{ $product->sellerProfile->store_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-surface-500">Kanal</dt>
                            <dd class="text-surface-900 mt-0.5">{{ strtoupper($product->sales_channel->value) }}</dd>
                        </div>
                        <div>
                            <dt class="text-surface-500">Stok</dt>
                            <dd class="text-surface-900 mt-0.5">{{ $product->stock }} unit</dd>
                        </div>
                        <div>
                            <dt class="text-surface-500">Terjual</dt>
                            <dd class="text-surface-900 mt-0.5">{{ $product->sold_count }} unit</dd>
                        </div>
                        <div>
                            <dt class="text-surface-500">Min. Pembelian</dt>
                            <dd class="text-surface-900 mt-0.5">{{ $product->min_purchase_qty }} unit</dd>
                        </div>
                    </dl>
                </x-card>
            </div>

            {{-- Tab Ulasan (read-only) --}}
            <div x-show="tab === 'ulasan'" x-cloak class="py-6 space-y-4">
                @if ($reviewCount > 0)
                    <x-card>
                        <div class="flex items-start gap-6">
                            <div class="text-center">
                                <p class="text-4xl font-bold text-surface-900">{{ number_format((float) $product->rating, 1) }}</p>
                                <div class="flex justify-center text-amber-500 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= round($product->rating) ? 'text-amber-500' : 'text-surface-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-xs text-surface-500 mt-1">{{ $reviewCount }} ulasan</p>
                            </div>
                            <div class="flex-1">
                                @for ($star = 5; $star >= 1; $star--)
                                    @php $count = $ratingBreakdown[$star] ?? 0; $pct = $reviewCount ? round(($count / $reviewCount) * 100) : 0; @endphp
                                    <div class="flex items-center gap-2 text-xs mb-1">
                                        <span class="w-6 text-surface-600">{{ $star }}★</span>
                                        <div class="flex-1 h-1.5 bg-surface-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-500" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="w-8 text-right text-surface-600">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </x-card>
                @endif

                @if ($product->reviews->isEmpty())
                    <x-card padding="none">
                        <x-empty-state icon="user-circle" title="Belum ada ulasan" description="Pembeli yang sudah menyelesaikan pesanan dapat memberi ulasan." />
                    </x-card>
                @else
                    <div class="space-y-3">
                        @foreach ($product->reviews as $review)
                            <x-card>
                                <div class="flex items-start gap-3">
                                    <x-avatar :name="$review->user->name" size="md" />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <p class="text-sm font-semibold text-surface-900">{{ $review->user->name }}</p>
                                            <p class="text-xs text-surface-400">{{ $review->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex items-center text-amber-500 mt-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'text-amber-500' : 'text-surface-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @if ($review->comment)
                                            <p class="text-sm text-surface-700 mt-2 leading-relaxed whitespace-pre-line">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
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