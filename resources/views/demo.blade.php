@extends('layouts.guest')

@section('title', 'Akun Demo — ' . config('app.name'))

@section('content')
    <div class="max-w-3xl mx-auto py-6">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 mb-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-primary-700 text-white font-bold text-sm">SP</span>
                <span class="text-xl font-semibold text-surface-900">{{ config('app.name') }}</span>
            </div>
            <h1 class="text-2xl font-bold text-surface-900">Akun Demo</h1>
            <p class="text-sm text-surface-600 mt-2 max-w-lg mx-auto">
                Pilih salah satu akun untuk mencoba aplikasi. Setiap role memiliki hak akses yang berbeda.
            </p>
        </div>

        {{-- Error --}}
        @if (session('error'))
            <x-alert variant="danger" class="mb-4">{{ session('error') }}</x-alert>
        @endif

        {{-- Password info --}}
        <div class="mb-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start gap-2">
                <x-icon name="information-circle" size="md" class="text-blue-600 flex-shrink-0 mt-0.5" />
                <div class="text-xs text-blue-900">
                    <p class="font-semibold mb-0.5">Password semua akun</p>
                    <p>Password default: <code class="font-mono bg-blue-100 px-1.5 py-0.5 rounded">password</code></p>
                </div>
            </div>
        </div>

        {{-- Akun list --}}
        <div class="space-y-3">
            @foreach ($accounts as $acc)
                <form method="POST" action="{{ route('demo.login', $acc['email']) }}">
                    @csrf
                    <button type="submit" class="w-full text-left bg-white border border-surface-200 rounded-lg p-4 hover:shadow-card-hover hover:border-primary-300 transition group">
                        <div class="flex items-start gap-4">
                            <div class="rounded-md bg-{{ $acc['color'] }}-50 p-2.5 flex-shrink-0">
                                <x-icon :name="$acc['icon']" size="md" class="text-{{ $acc['color'] }}-700" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-surface-900">{{ $acc['role'] }}</p>
                                    <span class="text-[10px] font-mono text-surface-500 bg-surface-100 px-1.5 py-0.5 rounded">{{ $acc['email'] }}</span>
                                </div>
                                <p class="text-xs text-surface-600 mt-1 leading-relaxed">{{ $acc['desc'] }}</p>
                            </div>
                            <div class="flex-shrink-0 hidden sm:flex items-center gap-1 text-sm font-medium text-primary-700">
                                Login
                                <x-icon name="chevron-right" size="sm" />
                            </div>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        {{-- Manual login --}}
        <div class="mt-6 text-center text-xs text-surface-500">
            <p>Atau <a href="{{ route('login') }}" class="text-primary-700 hover:underline font-medium">masuk manual</a> dengan email + password.</p>
        </div>
    </div>
@endsection