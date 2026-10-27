<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-50 font-sans antialiased text-surface-900">
    <header class="sticky top-0 z-30 bg-white border-b border-surface-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center gap-2 sm:gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-primary-700 text-white font-bold">E</span>
                    <span class="hidden sm:block text-base font-semibold tracking-tight">{{ config('app.name') }}</span>
                </a>

                <nav class="hidden md:flex items-center gap-1 text-sm ml-4">
    <a href="{{ url('/') }}" class="px-3 py-2 rounded-md hover:bg-surface-100 {{ request()->is('/') ? 'text-primary-700 font-semibold' : 'text-surface-700' }}">Beranda</a>
    <a href="{{ route('catalog.index') }}" class="px-3 py-2 rounded-md hover:bg-surface-100 {{ request()->routeIs('catalog.*') ? 'text-primary-700 font-semibold' : 'text-surface-700' }}">Katalog</a>
</nav>

                <div class="flex-1"></div>

                <div class="flex items-center gap-1">
                    <button type="button" class="p-2 rounded-md hover:bg-surface-100" aria-label="Cari">
                        <x-icon name="magnifying-glass" size="md" class="text-surface-700" />
                    </button>
                    @auth
    @php $cartCount = auth()->user()->cartItems()->sum('quantity'); @endphp
    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-md hover:bg-surface-100" aria-label="Keranjang">
        <x-icon name="shopping-cart" size="md" class="text-surface-700" />
        @if ($cartCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-primary-700 text-white text-[10px] font-semibold flex items-center justify-center">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
        @endif
    </a>
@else
    <a href="{{ route('login') }}" class="p-2 rounded-md hover:bg-surface-100" aria-label="Keranjang">
        <x-icon name="shopping-cart" size="md" class="text-surface-700" />
    </a>
@endauth

                    @auth
                        <x-user-menu />
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-100 rounded-md">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex px-3 py-2 text-sm font-semibold text-white bg-primary-700 hover:bg-primary-800 rounded-md">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

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
</body>
</html>