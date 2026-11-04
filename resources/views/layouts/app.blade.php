<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-50 font-sans antialiased text-surface-900">
    @php
        $u = auth()->user();
        $cartCount = $u ? $u->cartItems()->sum('quantity') : 0;
        $walletBalance = $u && $u->role !== \App\Enums\UserRole::Admin
            ? (float) ($u->wallet?->balance ?? 0)
            : 0;
    @endphp

    <header class="sticky top-0 z-30 bg-primary-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-white text-primary-700 font-bold text-sm">SP</span>
                    <span class="hidden sm:block text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
                </a>

                <form method="GET" action="{{ route('catalog.index') }}" class="flex-1">
                    <div class="relative">
                        <input type="search" name="q" value="{{ request('q') }}"
                            placeholder="Cari produk…"
                            class="w-full px-4 py-2 pr-12 rounded-md bg-white text-surface-900 text-sm focus:outline-none focus:ring-2 focus:ring-white/60">
                        <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 p-1.5 rounded bg-primary-700 text-white hover:bg-primary-800" aria-label="Cari">
                            <x-icon name="magnifying-glass" size="sm" />
                        </button>
                    </div>
                </form>

                <div class="flex items-center gap-1">
                    @unless ($u?->role === \App\Enums\UserRole::Admin)
                        {{-- SeumpamaPay (hanya untuk non-admin) --}}
                        @auth
                            <a href="{{ route('wallet.index') }}"
                               class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md hover:bg-white/10 text-sm font-medium"
                               title="SeumpamaPay">
                                <x-icon name="banknotes" size="md" />
                                <span class="hidden md:inline font-semibold">Rp {{ number_format($walletBalance, 0, ',', '.') }}</span>
                            </a>
                        @endauth

                        {{-- Cart --}}
                        <a href="{{ $u ? route('cart.index') : route('login') }}" class="relative p-2 rounded-md hover:bg-white/10" aria-label="Keranjang">
                            <x-icon name="shopping-cart" size="lg" />
                            @if ($cartCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-white text-primary-700 text-[10px] font-semibold flex items-center justify-center">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                        </a>
                    @endunless

                    @auth
                        <x-user-menu />
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex px-3 py-1.5 text-sm font-medium hover:bg-white/10 rounded-md">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex px-3 py-1.5 text-sm font-semibold bg-white text-primary-700 hover:bg-surface-100 rounded-md">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @auth
        @if (request()->routeIs('seller.*'))
            <x-workspace-nav workspace="toko" />
        @elseif (request()->routeIs('affiliate.*'))
            <x-workspace-nav workspace="afiliasi" />
        @endif
    @endauth

    <main class="pb-20 md:pb-8">
        @if (isset($header))
            {{ $header }}
        @endif
        @hasSection('header')
            @yield('header')
        @endif

        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <x-bottom-nav class="md:hidden" />

    <div x-data="{ show: false, msg: '' }"
         x-on:show-toast.window="msg = $event.detail; show = true; setTimeout(() => show = false, 2000)"
         x-show="show" x-transition
         class="fixed bottom-24 md:bottom-6 left-1/2 -translate-x-1/2 z-50 bg-surface-900 text-white text-sm px-4 py-2 rounded-md shadow-lg"
         style="display: none;">
        <span x-text="msg"></span>
    </div>
</body>
</html>