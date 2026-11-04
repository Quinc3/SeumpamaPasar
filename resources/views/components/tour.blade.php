@props(['role'])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const role = '{{ $role }}';
    const key = 'tour_' + role + '_done';

    const tours = {
        buyer: [
            {
                popover: {
                    title: 'Selamat datang di Seumpama Pasar!',
                    description: 'Panduan singkat mengenal tombol-tombol utama. Klik "Berikutnya" untuk melanjutkan.',
                },
            },
            {
                element: '[data-tour-logo]',
                popover: {
                    title: 'Kembali ke Beranda',
                    description: 'Klik logo ini kapan saja untuk kembali ke halaman utama.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '[data-tour-search]',
                popover: {
                    title: 'Cari Produk',
                    description: 'Ketik nama produk atau kata kunci, lalu tekan Enter atau tombol search.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '[data-tour-wallet]',
                popover: {
                    title: 'SeumpamaPay',
                    description: 'Dompet digital Anda. Cek saldo, top up, dan lihat riwayat transaksi di sini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '[data-tour-cart]',
                popover: {
                    title: 'Keranjang',
                    description: 'Produk yang sudah Anda pilih. Angka di pojok menunjukkan jumlah item.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '[data-tour-user-menu]',
                popover: {
                    title: 'Menu Profil',
                    description: 'Akses profil, toko (kalau punya), afiliasi, dan logout di sini.',
                    side: 'bottom',
                    align: 'end',
                },
            },
            {
                element: '[data-tour-banner]',
                popover: {
                    title: 'Banner Promo',
                    description: 'Promo dan pengumuman terbaru. Geser otomatis, atau klik panah untuk navigasi.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '[data-tour-product-card]',
                popover: {
                    title: 'Kartu Produk',
                    description: 'Klik untuk lihat detail, harga, dan ulasan pembeli lain. Tambahkan ke keranjang dari halaman detail.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                popover: {
                    title: 'Siap mulai!',
                    description: 'Selamat berbelanja. Kalau lupa, buka menu profil dan pilih "Panduan" untuk melihat tour ini lagi.',
                },
            },
        ],

        seller: [
            {
                popover: {
                    title: 'Selamat datang di Toko Anda!',
                    description: 'Panduan singkat mengelola toko. Klik "Berikutnya" untuk melanjutkan.',
                },
            },
            {
                element: '[data-tour-seller-saldo]',
                popover: {
                    title: 'Saldo Tersedia',
                    description: 'Uang dari pesanan yang sudah selesai. Bisa dipakai untuk berbelanja di Seumpama Pasar.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '[data-tour-seller-tertahan]',
                popover: {
                    title: 'Saldo Tertahan (Escrow)',
                    description: 'Uang dari pesanan yang belum selesai. Cair otomatis setelah pembeli konfirmasi terima.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '[data-tour-seller-nav]',
                popover: {
                    title: 'Navigasi Toko',
                    description: 'Kelola Produk, Pesanan, dan lihat Profil dari menu ini.',
                    side: 'bottom',
                    align: 'center',
                },
            },
            {
                element: '[data-tour-seller-stats]',
                popover: {
                    title: 'Statistik Pesanan',
                    description: 'Ringkasan jumlah pesanan per status. Klik "Pesanan" di menu untuk memproses.',
                    side: 'top',
                    align: 'start',
                },
            },
            {
                popover: {
                    title: 'Siap berjualan!',
                    description: 'Tambahkan produk pertama di menu Produk. Selamat berjualan!',
                },
            },
        ],

        admin: [
            {
                popover: {
                    title: 'Panel Admin',
                    description: 'Panduan singkat mengelola platform. Klik "Berikutnya" untuk melanjutkan.',
                },
            },
            {
                element: '[data-tour-admin-sidebar]',
                popover: {
                    title: 'Navigasi Admin',
                    description: 'Kelola penjual, verifikasi bisnis, moderasi produk, dan afiliasi dari sidebar ini.',
                    side: 'right',
                    align: 'start',
                },
            },
            {
                element: '[data-tour-admin-stats]',
                popover: {
                    title: 'Statistik Utama',
                    description: 'Total pendapatan, order, komisi, dan pengguna. Angka diambil langsung dari database.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                element: '[data-tour-admin-escrow]',
                popover: {
                    title: 'Escrow Aktif',
                    description: 'Dana yang sedang ditahan platform, menunggu konfirmasi pembeli.',
                    side: 'bottom',
                    align: 'start',
                },
            },
            {
                popover: {
                    title: 'Siap mengelola!',
                    description: 'Gunakan sidebar untuk navigasi. Selamat bekerja.',
                },
            },
        ],
    };

    const steps = tours[role] || [];
    if (steps.length === 0) return;

    function startTour() {
        const driverObj = window.Driver({
            showProgress: true,
            progressText: '@{{current}} dari @{{total}}',
            nextBtnText: 'Berikutnya',
            prevBtnText: 'Sebelumnya',
            doneBtnText: 'Selesai',
            allowClose: true,
            overlayClickBehavior: () => {},
            steps: steps,
            onHighlighted: (element, step, opts) => {
                // Coba beberapa cara untuk detect step index (kompatibel lintas versi)
                let index = 0;
                if (opts && opts.driver) {
                    if (typeof opts.driver.getActiveIndex === 'function') {
                        index = opts.driver.getActiveIndex();
                    } else if (typeof opts.state !== 'undefined' && opts.state.activeIndex !== undefined) {
                        index = opts.state.activeIndex;
                    } else if (typeof opts.index !== 'undefined') {
                        index = opts.index;
                    }
                } else if (typeof step === 'object' && step.index !== undefined) {
                    index = step.index;
                }
                document.body.setAttribute('data-driver-step', String(index));
            },
            onDestroyed: () => {
                document.body.removeAttribute('data-driver-step');
                localStorage.setItem(key, '1');
            },
        });
        driverObj.drive();
    }

    window.addEventListener('tour:start', (e) => {
        if (e.detail === role) startTour();
    });

    if (!localStorage.getItem(key)) {
        setTimeout(() => {
            const firstElementStep = steps.find(s => s.element);
            if (firstElementStep && document.querySelector(firstElementStep.element)) {
                startTour();
            }
        }, 1000);
    }
});
</script>