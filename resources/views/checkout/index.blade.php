@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <h1 class="text-xl sm:text-2xl font-semibold text-surface-900 mb-4">Checkout</h1>

        @error('checkout')
            <x-alert variant="danger" class="mb-4">{{ $message }}</x-alert>
        @enderror

        @if ($addresses->isEmpty())
            <x-alert variant="warning" class="mb-4">
                Anda belum punya alamat. <a href="{{ route('addresses.index') }}" class="underline font-semibold">Tambah alamat dulu</a>.
            </x-alert>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @csrf
            @foreach ($selectedIds ?? [] as $cid)
                <input type="hidden" name="cart_item_ids[]" value="{{ $cid }}">
            @endforeach

            <div class="lg:col-span-2 space-y-4">
                <x-card>
                    <h2 class="text-sm font-semibold text-surface-900 mb-3">Alamat Pengiriman</h2>
                    @if ($addresses->isEmpty())
                        <p class="text-sm text-surface-600">Belum ada alamat.</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($addresses as $addr)
                                <label class="flex items-start gap-3 p-3 border border-surface-200 rounded-md cursor-pointer hover:bg-surface-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                    <input type="radio" name="address_id" value="{{ $addr->id }}" class="mt-1 text-primary-600 focus:ring-primary-500" @checked($defaultAddress?->id === $addr->id) required>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-surface-900">
                                            {{ $addr->label }}
                                            @if ($addr->is_default) <x-badge variant="primary" class="ml-1">Utama</x-badge> @endif
                                        </p>
                                        <p class="text-sm text-surface-700">{{ $addr->recipient_name }} — {{ $addr->phone }}</p>
                                        <p class="text-xs text-surface-600">{{ $addr->line1 }}, {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <x-card>
                    <h2 class="text-sm font-semibold text-surface-900 mb-3">Metode Pembayaran</h2>
                    <div class="space-y-4">
                        @foreach ($methods as $groupName => $groupMethods)
                            <div>
                                <p class="text-xs uppercase tracking-wide text-surface-500 mb-2">{{ $groupName }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($groupMethods as $value => $label)
                                        <label class="flex items-center gap-2.5 p-2.5 border border-surface-200 rounded-md cursor-pointer hover:bg-surface-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                                            <input type="radio" name="method" value="{{ $value }}" class="text-primary-600 focus:ring-primary-500"
                                                   @checked($loop->parent->first && $loop->first) required>
                                            <span class="text-sm text-surface-900">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <div class="space-y-4">
            <x-card>
                    <h2 class="text-sm font-semibold text-surface-900 mb-3">Ringkasan</h2>
                    <div class="space-y-2 text-sm">
                    @foreach ($lineItems as $li)
                            <div class="flex justify-between items-start gap-2">
                                <div class="min-w-0">
                                    <p class="text-surface-700 truncate">
                                        {{ $li['cart_item']->product->name }} ×{{ $li['cart_item']->quantity }}
                                    </p>
                                    @if ($li['applies_tier'] || $isMember)
                                        <div class="flex gap-1 mt-0.5">
                                            @if ($li['applies_tier'])
                                                <x-badge variant="primary">Grosir</x-badge>
                                            @endif
                                            @if ($isMember)
                                                <x-badge variant="success">Member</x-badge>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <x-price :amount="$li['line_total']" class="text-sm shrink-0" />
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-surface-200 mt-3 pt-3 flex justify-between items-baseline">
                        <span class="text-sm font-semibold text-surface-900">Total</span>
                        <x-price :amount="$total" class="text-lg" />
                    </div>
                </x-card>

                <x-primary-button type="submit" class="w-full justify-center" :disabled="$addresses->isEmpty()">
                    <x-icon name="credit-card" size="sm" class="mr-1.5" />
                    Lanjut ke Pembayaran
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection