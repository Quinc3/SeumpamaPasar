<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-50 font-sans antialiased text-surface-900" x-data="{ sidebar: false }">
    <div x-show="sidebar" x-on:click="sidebar = false" x-transition.opacity class="fixed inset-0 bg-black/40 z-40 md:hidden" style="display: none;" aria-hidden="true"></div>

    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-surface-200 flex flex-col transform transition-transform duration-200 -translate-x-full md:translate-x-0" :class="sidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="h-16 flex items-center justify-between px-4 border-b border-surface-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
    <span class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-primary-700 text-white font-bold text-xs">SP</span>
    <span class="text-sm font-semibold tracking-tight">Admin</span>
</a>
            <button type="button" x-on:click="sidebar = false" class="p-1 -mr-1 rounded-md md:hidden" aria-label="Tutup menu">
                <x-icon name="x-mark" size="md" class="text-surface-600" />
            </button>
        </div>

        <x-sidebar-nav>
            <x-nav-item href="{{ route('admin.dashboard') }}" icon="home" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-item>
            <x-nav-item href="#" icon="tag">Kategori</x-nav-item>
            <x-nav-item href="{{ route('admin.moderation.index') }}" icon="check-circle" :active="request()->routeIs('admin.moderation.*')">Moderasi Produk</x-nav-item>
            <x-nav-item href="#" icon="truck">Pesanan</x-nav-item>
            <x-nav-item href="#" icon="user-circle">Pengguna</x-nav-item>
            <x-nav-item href="{{ route('admin.business.index') }}" icon="check-circle" :active="request()->routeIs('admin.business.*')">Verifikasi Bisnis</x-nav-item>
            <x-nav-item href="{{ route('admin.affiliates.index') }}" icon="arrow-path" :active="request()->routeIs('admin.affiliates.*')">Afiliasi</x-nav-item>
            <x-nav-item href="#" icon="banknotes">Laporan</x-nav-item>
        </x-sidebar-nav>

        <div class="p-3 border-t border-surface-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full inline-flex items-center gap-2.5 px-3 py-2 text-sm text-surface-700 hover:bg-surface-100 rounded-md">
                    <x-icon name="arrow-right" size="md" class="rotate-180 text-surface-500" />
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="md:pl-64">
        <header class="sticky top-0 z-30 h-16 bg-white border-b border-surface-200 flex items-center px-4 sm:px-6 lg:px-8">
            <button type="button" x-on:click="sidebar = true" class="p-2 -ml-2 mr-2 rounded-md md:hidden" aria-label="Buka menu">
                <x-icon name="bars-3" size="lg" class="text-surface-700" />
            </button>
            <h1 class="text-base font-semibold text-surface-900 truncate">@yield('title', 'Admin')</h1>
            <div class="ml-auto flex items-center gap-2">
                @auth
                    <x-user-menu />
                @endauth
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 space-y-6">
            @yield('content')
        </main>
    </div>
</body>
</html>