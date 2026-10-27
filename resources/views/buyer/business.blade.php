@extends('layouts.app')

@section('title', 'Profil Bisnis')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Profil Bisnis</h1>
            <a href="{{ route('buyer.dashboard') }}" class="text-sm text-surface-600 hover:text-surface-900">← Akun</a>
        </div>

        @if (session('status') === 'business-submitted')
            <x-alert variant="success">Profil bisnis tersimpan. Menunggu verifikasi admin.</x-alert>
        @endif

        @if ($profile && $profile->verified_at)
            <x-alert variant="success">
                Profil bisnis terverifikasi. Anda dapat melihat harga grosir dan membeli produk B2B.
            </x-alert>
        @elseif ($profile)
            <x-alert variant="warning">
                Profil bisnis menunggu verifikasi admin. Sampai diverifikasi, Anda hanya dapat membeli produk B2C.
            </x-alert>
        @else
            <x-alert variant="info">
                Lengkapi profil bisnis untuk mengakses harga grosir dan produk B2B.
            </x-alert>
        @endif

        <x-card>
            <form method="POST" action="{{ route('buyer.business.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @csrf
                <div class="sm:col-span-2">
                    <x-input-label for="company_name" value="Nama Perusahaan" />
                    <x-text-input id="company_name" name="company_name" class="mt-1 block w-full"
                        :value="old('company_name', $profile?->company_name)" :disabled="$profile?->verified_at !== null" required />
                    @error('company_name') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div>
                    <x-input-label for="tax_id" value="NPWP (opsional)" />
                    <x-text-input id="tax_id" name="tax_id" class="mt-1 block w-full"
                        :value="old('tax_id', $profile?->tax_id)" :disabled="$profile?->verified_at !== null" />
                </div>
                <div>
                    <x-input-label for="phone" value="Telepon Bisnis" />
                    <x-text-input id="phone" name="phone" class="mt-1 block w-full"
                        :value="old('phone', $profile?->phone)" :disabled="$profile?->verified_at !== null" required />
                    @error('phone') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="address" value="Alamat Bisnis" />
                    <textarea id="address" name="address" rows="3" :disabled="$profile?->verified_at !== null"
                        class="mt-1 block w-full rounded-md border-surface-300 bg-white text-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-surface-100">{{ old('address', $profile?->address) }}</textarea>
                    @error('address') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                @unless ($profile?->verified_at)
                    <div class="sm:col-span-2">
                        <x-primary-button type="submit">
                            <x-icon name="check-circle" size="sm" class="mr-1.5" />
                            {{ $profile ? 'Perbarui Profil' : 'Simpan Profil' }}
                        </x-primary-button>
                    </div>
                @endunless
            </form>
        </x-card>
    </div>
@endsection