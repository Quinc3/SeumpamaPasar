@auth
@php $user = auth()->user(); @endphp
<div x-data="{ open: false }" class="relative">
    <button type="button" x-on:click="open = !open" x-on:click.outside="open = false" class="flex items-center gap-1.5 p-1 rounded-md hover:bg-surface-100" aria-label="Menu pengguna">
        <x-avatar :name="$user->name" size="sm" />
        <x-icon name="chevron-down" size="sm" class="hidden sm:block text-surface-600" />
    </button>

    <div x-show="open" x-transition.origin.top.right class="absolute right-0 mt-2 w-60 bg-white border border-surface-200 rounded-md shadow-dropdown z-40" style="display: none;" role="menu">
        <div class="px-4 py-3 border-b border-surface-200">
            <div class="text-sm font-semibold text-surface-900 truncate">{{ $user->name }}</div>
            <div class="text-xs text-surface-500 truncate">{{ $user->email }}</div>
        </div>
        <div class="py-1">
            <a href="{{ route('buyer.dashboard') }}" class="block px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">Akun Saya</a>
            @if ($user->sellerProfile)
                <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">Toko Saya</a>
            @endif
            @if ($user->role === \App\Enums\UserRole::Admin)
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">Admin</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">Profil</a>
        </div>
        <div class="border-t border-surface-200 py-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">Keluar</button>
            </form>
        </div>
    </div>
</div>
@endauth