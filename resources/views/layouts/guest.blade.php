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
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
    <a href="{{ url('/') }}" class="mb-6 inline-flex items-center gap-2">
    <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-primary-700 text-white font-bold text-sm">SP</span>
    <span class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</span>
</a>
        <div class="w-full max-w-md bg-white border border-surface-200 rounded-lg shadow-card p-6 sm:p-8">
            @if (isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </div>

        @hasSection('footer')
            <div class="mt-6 text-sm text-surface-600">@yield('footer')</div>
        @endif
    </div>
</body>
</html>