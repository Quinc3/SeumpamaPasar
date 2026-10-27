@auth
@php $user = auth()->user(); @endphp
<div x-data="{ open: false }" class="relative">
    <button type="button" x-on:click="open = !open" x-on:click.outside="open = false" class="flex items-center gap-1.5 p-1 rounded-md hover:bg-white/10" aria-label="Menu pengguna">
        <x-avatar :name="$user->name" size="sm" class="ring-2 ring-white/30" />
        <x-icon name="chevron-down" size="sm" class="hidden sm:block" />
    </button>

    <div x-show="open" x-transition.origin.top.right class="absolute right-0 mt-2 w-64 bg-white text-surface-900 border border-surface-200 rounded-md shadow-dropdown z-40" style="display: none;" role="menu">
        <div class="px-4 py-3 border-b border-surface-200">
            <div class="text-sm font-semibold text-surface-900 truncate">{{ $user->name }}</div>
            <div class="text-xs text-surface-500 truncate">{{ $user->email }}</div>
            <div class="flex items-center gap-1 mt-1.5 flex-wrap">
                @if ($user->isMember()) <x-badge variant="primary">Member</x-badge> @endif
                @if ($user->isVerifiedBusiness()) <x-badge variant="success">Bisnis</x-badge> @endif
                @if ($user->role === \App\Enums\UserRole::Admin) <x-badge variant="warning">Admin</x-badge> @endif
            </div>
        </div>

        <div class="py-1">
            @if ($user->role === \App\Enums\UserRole::Admin)
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">
                    <x-icon name="check-circle" size="sm" class="text-amber-600" /> Admin Panel
                </a>
            @else
                <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">
                    <x-icon name="user" size="sm" class="text-surface-500" /> Profil Saya
                </a>
                @if ($user->sellerProfile && $user->sellerProfile->status === \App\Enums\SellerStatus::Active)
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">
                        <x-icon name="home" size="sm" class="text-surface-500" /> Toko Saya
                    </a>
                @endif
                @if ($user->affiliateProfile)
                    <a href="{{ route('affiliate.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">
                        <x-icon name="arrow-path" size="sm" class="text-surface-500" /> Afiliasi
                    </a>
                @endif
            @endif
        </div>

        <div class="border-t border-surface-200 py-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-sm text-surface-700 hover:bg-surface-50">
                    <x-icon name="arrow-right" size="sm" class="rotate-180 text-surface-500" /> Keluar
                </button>
            </form>
        </div>
    </div>
</div>
@endauth