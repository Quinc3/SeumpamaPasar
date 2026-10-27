@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-surface-900">Edit Profil</h1>
            <a href="{{ route('profile.show') }}" class="text-sm text-surface-600 hover:text-surface-900">← Profil</a>
        </div>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-card>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-card>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-card>
    </div>
@endsection