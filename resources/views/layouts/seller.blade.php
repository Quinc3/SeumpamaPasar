<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden md:flex w-64 bg-white border-r border-gray-200 flex-col">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <a href="{{ route('seller.dashboard') }}" class="text-lg font-semibold text-gray-900">
                    {{ config('app.name') }} <span class="text-xs font-normal text-gray-500">Toko</span>
                </a>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1">
                <a href="{{ route('seller.dashboard') }}"
                   class="block px-3 py-2 text-sm rounded-md hover:bg-gray-100 {{ request()->routeIs('seller.dashboard') ? 'bg-gray-100 font-semibold text-gray-900' : 'text-gray-700' }}">
                    Dashboard
                </a>
            </nav>
            <div class="p-3 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-gray-100">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>
        <main class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <h1 class="text-base font-semibold text-gray-900">@yield('title', 'Toko')</h1>
                <span class="text-sm text-gray-600">{{ auth()->user()->name ?? '' }}</span>
            </header>
            <div class="p-6 space-y-4">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>