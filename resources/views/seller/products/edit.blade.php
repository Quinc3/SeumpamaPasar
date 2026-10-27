@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
    @if (session('status'))
        <x-alert variant="success">Perubahan disimpan.</x-alert>
    @endif
    @if ($errors->any())
        <x-alert variant="danger">Periksa kembali isian form.</x-alert>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-surface-900">Edit Produk</h2>
        <a href="{{ route('seller.products.index') }}" class="text-sm text-surface-600 hover:text-surface-900">← Produk</a>
    </div>

    <x-card>
        <form method="POST" action="{{ route('seller.products.update', $product->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4"
              x-data="{ channel: '{{ old('sales_channel', $product->sales_channel->value) }}' }">
            @csrf
            @method('PATCH')
            <div class="sm:col-span-2">
                <x-input-label for="name" value="Nama Produk" />
                <x-text-input id="name" name="name" :value="old('name', $product->name)" class="mt-1 block w-full" required />
                @error('name') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div>
                <x-input-label for="category_id" value="Kategori" />
                <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500" required>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="price" value="Harga (Rp)" />
                <x-text-input id="price" name="price" type="number" min="1000" step="100" :value="old('price', $product->price)" class="mt-1 block w-full" required />
                @error('price') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div>
                <x-input-label for="stock" value="Stok" />
                <x-text-input id="stock" name="stock" type="number" min="0" :value="old('stock', $product->stock)" class="mt-1 block w-full" required />
                @error('stock') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div>
                <x-input-label for="sales_channel" value="Kanal Penjualan" />
                <select id="sales_channel" name="sales_channel" x-model="channel" class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="b2c">B2C (Retail)</option>
                    @if ($seller->seller_type === \App\Enums\SellerType::Business)
                        <option value="b2b">B2B (Grosir)</option>
                        <option value="both">Keduanya</option>
                    @endif
                </select>
                @error('sales_channel') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div x-show="channel !== 'b2c'" x-cloak>
                <x-input-label for="min_purchase_qty" value="Minimum Pembelian" />
                <x-text-input id="min_purchase_qty" name="min_purchase_qty" type="number" min="1" :value="old('min_purchase_qty', $product->min_purchase_qty)" class="mt-1 block w-full" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="description" value="Deskripsi" />
                <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500" required>{{ old('description', $product->description) }}</textarea>
                @error('description') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="image" value="Ganti Gambar (opsional)" />
                @if ($product->images->isNotEmpty())
                    <div class="mt-1 flex items-center gap-3">
                        <img src="{{ Storage::url($product->images->first()->path) }}" alt="" class="h-16 w-16 rounded-md object-cover border border-surface-200">
                        <span class="text-xs text-surface-500">Upload gambar baru untuk mengganti.</span>
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-surface-700 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-primary-50 file:text-primary-700 file:text-sm file:font-medium hover:file:bg-primary-100">
                @error('image') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <x-primary-button type="submit">Simpan Perubahan</x-primary-button>
                <a href="{{ route('seller.products.index') }}">
                    <x-secondary-button type="button">Kembali</x-secondary-button>
                </a>
            </div>
        </form>
    </x-card>

    {{-- Tier Harga Grosir --}}
    @if ($product->sales_channel->value !== 'b2c')
        <x-card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-surface-900">Tier Harga Grosir</h3>
                <span class="text-xs text-surface-500">Harga dasar: <x-price :amount="$product->price" class="text-xs" /></span>
            </div>

            @if (session('status') === 'tier-added' || session('status') === 'tier-deleted')
                <x-alert variant="success" class="mb-3">Tier diperbarui.</x-alert>
            @endif

            @if ($product->wholesalePriceTiers->isEmpty())
                <p class="text-sm text-surface-500 mb-3">Belum ada tier. Tambahkan tier pertama di bawah.</p>
            @else
                <div class="divide-y divide-surface-100 border border-surface-200 rounded-md mb-3">
                    @foreach ($product->wholesalePriceTiers->sortBy('min_quantity') as $tier)
                        <div class="flex items-center justify-between p-3 text-sm">
                            <div>
                                <span class="font-medium">≥ {{ $tier->min_quantity }} pcs</span>
                                <span class="text-surface-500"> · </span>
                                <x-price :amount="$tier->price" class="text-sm" />
                            </div>
                            <form method="POST" action="{{ route('seller.products.tiers.destroy', ['product' => $product->id, 'tier' => $tier->id]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('seller.products.tiers.store', $product->id) }}" class="flex flex-col sm:flex-row items-end gap-2">
                @csrf
                <div class="flex-1">
                    <x-input-label for="min_quantity" value="Min. Qty" />
                    <x-text-input id="min_quantity" name="min_quantity" type="number" min="1" class="mt-1 block w-full" required />
                    @error('min_quantity') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div class="flex-1">
                    <x-input-label for="tier_price" value="Harga/pcs (Rp)" />
                    <x-text-input id="tier_price" name="price" type="number" min="100" step="100" class="mt-1 block w-full" required />
                    @error('price') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <x-primary-button type="submit">Tambah Tier</x-primary-button>
            </form>
        </x-card>
    @endif

    {{-- Arsip --}}
    @if ($product->status !== \App\Enums\ProductStatus::Archived)
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-surface-900">Arsipkan Produk</p>
                    <p class="text-xs text-surface-500">Produk tidak akan tampil di katalog. Riwayat pesanan tetap aman.</p>
                </div>
                <form method="POST" action="{{ route('seller.products.archive', $product->id) }}"
                      onsubmit="return confirm('Arsipkan produk ini?')">
                    @csrf
                    <x-danger-button type="submit">Arsipkan</x-danger-button>
                </form>
            </div>
        </x-card>
    @else
        <x-alert variant="info">Produk ini sudah diarsipkan.</x-alert>
    @endif
@endsection