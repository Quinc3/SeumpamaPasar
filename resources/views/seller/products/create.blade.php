@extends('layouts.app')

@section('title', 'Produk Baru')

@section('content')
<x-page-wrapper>
    @if ($errors->any())
        <x-alert variant="danger">Periksa kembali isian form.</x-alert>
    @endif

    <h2 class="text-lg font-semibold text-surface-900">Tambah Produk</h2>

    <x-card>
        <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4"
              x-data="{ channel: '{{ old('sales_channel', 'b2c') }}' }">
            @csrf
            <div class="sm:col-span-2">
                <x-input-label for="name" value="Nama Produk" />
                <x-text-input id="name" name="name" :value="old('name')" class="mt-1 block w-full" required />
                @error('name') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div>
                <x-input-label for="category_id" value="Kategori" />
                <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">— Pilih —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div>
                <x-input-label for="price" value="Harga (Rp)" />
                <x-text-input id="price" name="price" type="number" min="1000" step="100" :value="old('price')" class="mt-1 block w-full" required />
                @error('price') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div>
                <x-input-label for="stock" value="Stok" />
                <x-text-input id="stock" name="stock" type="number" min="0" :value="old('stock', 0)" class="mt-1 block w-full" required />
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
                <x-text-input id="min_purchase_qty" name="min_purchase_qty" type="number" min="1" :value="old('min_purchase_qty', 1)" class="mt-1 block w-full" />
                @error('min_purchase_qty') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="description" value="Deskripsi" />
                <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500" required>{{ old('description') }}</textarea>
                @error('description') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="image" value="Gambar Produk (opsional)" />
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-surface-700 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-primary-50 file:text-primary-700 file:text-sm file:font-medium hover:file:bg-primary-100">
                <p class="text-xs text-surface-500 mt-1">JPG/PNG/WebP, maks 2 MB.</p>
                @error('image') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <x-primary-button type="submit">
                    <x-icon name="check" size="sm" class="mr-1.5" />
                    Simpan & Ajukan Review
                </x-primary-button>
                <a href="{{ route('seller.products.index') }}">
                    <x-secondary-button type="button">Batal</x-secondary-button>
                </a>
            </div>
            <p class="sm:col-span-2 text-xs text-surface-500">
                Produk akan berstatus "Menunggu Review" dan tampil di katalog setelah disetujui admin.
            </p>
        </form>
    </x-card>
</x-page-wrapper>
@endsection