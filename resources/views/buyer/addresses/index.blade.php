@extends('layouts.app')

@section('title', 'Alamat Saya')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-semibold text-surface-900">Alamat Saya</h1>
            <a href="{{ route('buyer.dashboard') }}" class="text-sm text-surface-600 hover:text-surface-900">← Akun</a>
        </div>

        @if (session('status'))
            <x-alert variant="success">Perubahan alamat disimpan.</x-alert>
        @endif

        @if ($addresses->isEmpty())
            <x-alert variant="info">Belum ada alamat. Tambah alamat di bawah untuk bisa checkout.</x-alert>
        @else
            <div class="space-y-2">
                @foreach ($addresses as $addr)
                    <x-card>
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-surface-900">{{ $addr->label }}</span>
                                    @if ($addr->is_default)
                                        <x-badge variant="primary">Utama</x-badge>
                                    @endif
                                </div>
                                <p class="text-sm text-surface-700 mt-1">{{ $addr->recipient_name }} — {{ $addr->phone }}</p>
                                <p class="text-sm text-surface-600">{{ $addr->line1 }}, {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                @unless ($addr->is_default)
                                    <form method="POST" action="{{ route('addresses.default', $addr->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-primary-700 hover:underline px-2 py-1">Jadikan Utama</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('addresses.destroy', $addr->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline px-2 py-1">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif

        <x-card>
            <h2 class="text-sm font-semibold text-surface-900 mb-3">Tambah Alamat Baru</h2>
            <form method="POST" action="{{ route('addresses.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @csrf
                <div>
                    <x-input-label for="label" value="Label (mis. Rumah, Kantor)" />
                    <x-text-input id="label" name="label" class="mt-1 block w-full" required />
                    @error('label') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div>
                    <x-input-label for="recipient_name" value="Nama Penerima" />
                    <x-text-input id="recipient_name" name="recipient_name" class="mt-1 block w-full" required />
                    @error('recipient_name') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div>
                    <x-input-label for="phone" value="Telepon" />
                    <x-text-input id="phone" name="phone" class="mt-1 block w-full" required />
                    @error('phone') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="line1" value="Alamat" />
                    <x-text-input id="line1" name="line1" class="mt-1 block w-full" required />
                    @error('line1') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div>
                    <x-input-label for="city" value="Kota" />
                    <x-text-input id="city" name="city" class="mt-1 block w-full" required />
                    @error('city') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div>
                    <x-input-label for="province" value="Provinsi" />
                    <x-text-input id="province" name="province" class="mt-1 block w-full" required />
                    @error('province') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div>
                    <x-input-label for="postal_code" value="Kode Pos" />
                    <x-text-input id="postal_code" name="postal_code" class="mt-1 block w-full" required />
                    @error('postal_code') <x-input-error :messages="[$message]" class="mt-1" /> @enderror
                </div>
                <div class="flex items-end gap-2">
                    <label class="inline-flex items-center gap-2 text-sm text-surface-700">
                        <input type="checkbox" name="is_default" value="1" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        Jadikan alamat utama
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <x-primary-button type="submit">Simpan Alamat</x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection