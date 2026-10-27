@props(['slides' => null])

@php
    if ($slides === null) {
        $slides = [
            [
                'eyebrow' => 'Seumpama Pasar',
                'title' => 'Belanja retail & grosir dalam satu platform',
                'subtitle' => 'Harga retail untuk semua pembeli, harga grosir untuk bisnis terverifikasi. Plus diskon member 5% & komisi afiliasi.',
                'cta' => ['label' => 'Jelajahi Katalog', 'route' => route('catalog.index'), 'icon' => 'shopping-cart'],
                'secondary' => auth()->guest()
                    ? ['label' => 'Daftar Gratis', 'route' => route('register'), 'icon' => 'user']
                    : null,
                'bg' => 'from-primary-700 via-primary-600 to-primary-800',
            ],
            [
                'eyebrow' => 'Promo Spesial',
                'title' => 'Diskon hingga 25% untuk produk pilihan',
                'subtitle' => 'Hemat lebih banyak dengan diskon spesial minggu ini. Berlaku untuk semua pembeli retail.',
                'cta' => ['label' => 'Lihat Promo', 'route' => route('catalog.index', ['sort' => 'terlaris']), 'icon' => 'bolt'],
                'secondary' => null,
                'bg' => 'from-amber-600 via-orange-600 to-red-600',
            ],
            [
                'eyebrow' => 'Keuntungan Member',
                'title' => 'Jadi Member, dapatkan diskon 5% setiap transaksi',
                'subtitle' => 'Aktifkan gratis, aktif seumur akun. Diskon otomatis diterapkan di setiap checkout.',
                'cta' => [
                    'label' => auth()->check() ? 'Ke Akun' : 'Jadi Member',
                    'route' => auth()->check() ? route('buyer.dashboard') : route('register'),
                    'icon' => 'check-circle',
                ],
                'secondary' => null,
                'bg' => 'from-teal-700 via-emerald-600 to-cyan-700',
            ],
        ];
    }
@endphp

<div
    x-data="{
        active: 0,
        total: {{ count($slides) }},
        paused: false,
        touchStart: null,
        timer: null,
        init() {
            this.timer = setInterval(() => { if (!this.paused) this.next() }, 5000);
        },
        next() { this.active = (this.active + 1) % this.total },
        prev() { this.active = (this.active - 1 + this.total) % this.total },
        go(i) { this.active = i },
        onTouchStart(e) { this.touchStart = e.touches[0].clientX },
        onTouchEnd(e) {
            if (this.touchStart === null) return;
            const diff = this.touchStart - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) { diff > 0 ? this.next() : this.prev(); }
            this.touchStart = null;
        }
    }"
    x-on:mouseenter="paused = true"
    x-on:mouseleave="paused = false"
    x-on:touchstart.passive="onTouchStart($event)"
    x-on:touchend.passive="onTouchEnd($event)"
    class="relative overflow-hidden"
>
    {{-- Slide track --}}
    <div class="flex transition-transform duration-700 ease-out"
         :style="`transform: translateX(-${active * 100}%)`">
        @foreach ($slides as $slide)
            <div class="w-full shrink-0 bg-gradient-to-br {{ $slide['bg'] }} text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 min-h-[280px] sm:min-h-[320px] flex items-center">
                    <div class="max-w-2xl">
                        <p class="text-xs uppercase tracking-widest text-white/80 mb-2">{{ $slide['eyebrow'] }}</p>
                        <h1 class="text-3xl sm:text-4xl font-bold leading-tight">{{ $slide['title'] }}</h1>
                        <p class="mt-3 text-sm sm:text-base text-white/90 max-w-xl">{{ $slide['subtitle'] }}</p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <a href="{{ $slide['cta']['route'] }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-primary-700 rounded-md font-semibold text-sm hover:bg-surface-100 transition">
                                <x-icon :name="$slide['cta']['icon']" size="sm" /> {{ $slide['cta']['label'] }}
                            </a>
                            @if (! empty($slide['secondary']))
                                <a href="{{ $slide['secondary']['route'] }}" class="inline-flex items-center gap-2 px-4 py-2 border border-white/40 text-white rounded-md font-semibold text-sm hover:bg-white/10 transition">
                                    <x-icon :name="$slide['secondary']['icon']" size="sm" /> {{ $slide['secondary']['label'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Arrows --}}
    <button type="button" x-on:click="prev()"
            class="hidden sm:flex absolute left-3 top-1/2 -translate-y-1/2 h-10 w-10 items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white backdrop-blur-sm transition"
            aria-label="Slide sebelumnya">
        <x-icon name="chevron-right" size="md" class="rotate-180" />
    </button>
    <button type="button" x-on:click="next()"
            class="hidden sm:flex absolute right-3 top-1/2 -translate-y-1/2 h-10 w-10 items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white backdrop-blur-sm transition"
            aria-label="Slide berikutnya">
        <x-icon name="chevron-right" size="md" />
    </button>

    {{-- Dots --}}
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2">
        @foreach ($slides as $i => $slide)
            <button type="button" x-on:click="go({{ $i }})"
                    :class="active === {{ $i }} ? 'bg-white w-6' : 'bg-white/40 w-2 hover:bg-white/60'"
                    class="h-2 rounded-full transition-all duration-300"
                    aria-label="Slide {{ $i + 1 }}">
            </button>
        @endforeach
    </div>
</div>