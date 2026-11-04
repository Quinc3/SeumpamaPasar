# PROGRESS — Seumpama Pasar (Integrated E-Commerce Platform)

> **Terakhir diperbarui:** 4 November 2026.
> **Status:** SEMUA FITUR SELESAI — SIAP DEMO AKADEMIK.

---

## 1. Status Umum

- **Nama Aplikasi:** Seumpama Pasar
- **Model:** B2C + B2B + Member + Affiliate + Seller
- **Fase:** 1–8 ✅ | UI Redesign (R1–R3) ✅ | Wallet + Escrow ✅ | Reviews + PDF + Search ✅
- **Tes:** `vendor\bin\phpunit` → 32/32 PASS
- **Kode:** lengkap, siap demo

---

## 2. Stack

- Laravel 12.70.1 · PHP 8.2.12
- Blade + Tailwind v3 + Vite 7 + Alpine.js
- MariaDB 10.4 (MySQL 8.0+ kompatibel)
- PHPUnit 11.5
- Heroicons v2 (SVG lokal, MIT)
- barryvdh/laravel-dompdf v3.1 (PDF invoice)

---

## 3. Fitur Lengkap

### Marketplace
- Katalog publik dengan search, filter kategori, sort (terbaru/termurah/termahal/terlaris)
- Detail produk dengan **3 tab**: Deskripsi | Detail Produk | Ulasan
- Rating, badge diskon, produk terlaris, produk featured di beranda
- Keranjang **grouped per seller** dengan bulk select checkout
- Checkout multi-seller (N order per checkout)
- 11 metode pembayaran simulasi: 5 bank VA, 2 retail (Indomaret/Alfamart), 4 e-wallet (SeumpamaPay/OVO/GoPay/DANA)
- Countdown 24 jam per pembayaran + auto-expire
- Riwayat pesanan buyer & seller dengan status tracking
- Bulk actions: multi-order status update (seller), multi-product archive

### Model Bisnis
- **Member:** diskon 5% flat untuk B2C, aktivasi manual, badge
- **Affiliate:** 5% × subtotal saat completed, first-touch 30 hari, self-referral diblokir, referral code + link copy
- **Seller:** self-registration dengan admin approval, dashboard analytics, tier harga grosir B2B

### B2B
- Verifikasi business profile oleh admin
- Tier harga grosir (min qty → harga khusus)
- Min order quantity
- Produk B2B hanya untuk business terverifikasi

### SeumpamaPay (Wallet)
- Saldo dengan top up (5 bank VA, preset + custom, batas Rp 10 juta)
- Bayar checkout langsung debit saldo
- Auto refund ke saldo saat order cancelled
- Riwayat transaksi lengkap (top up, payment, refund)
- Tombol di header dengan saldo live

### Escrow
- Dana ditahan platform saat bayar
- Release ke seller saat buyer completed order
- Refund ke buyer saat order cancelled
- Widget escrow di dashboard admin + seller
- Info banner escrow di halaman pesanan buyer

### Reviews (BARU)
- Ulasan hanya dari **halaman pesanan** (verified purchase check)
- Form inline per produk di order `completed`
- Rating 1–5 bintang + komentar opsional
- Unique constraint: 1 review per user per produk
- Rating agregat produk otomatis ter-update
- Tab ulasan di product page hanya read-only (summary + list)

### PDF Invoice (BARU)
- Download invoice per order (dari halaman order buyer)
- Format A4 portrait dengan logo SP, tabel produk, footer simulasi
- Nama file: `Invoice-CHK-XXXXXXXX-XXXXXX.pdf`
- Package: barryvdh/laravel-dompdf

### Seller Product Search (BARU)
- Search by nama atau deskripsi
- Filter by status (aktif, pending review, rejected, draft, archived)
- Kombinasi search + filter
- Tombol reset

### Admin
- Dashboard analytics (revenue, order, commission, escrow)
- Moderasi produk (approve/reject)
- Verifikasi bisnis
- Kelola penjual (approve/suspend/activate)
- Kelola afiliasi (suspend/activate)
- Admin isolation: hanya akses `/admin`, tidak bisa belanja/jualan

---

## 4. Konten Visual

### Hero Banner AI
- 3 slide dengan model AI (friendly premium)
- Disimpan di `public/images/banners/banner-{1,2,3}.jpg`
- Overlay gradient dari kiri (teks terbaca)
- Autoplay 5 detik, swipe mobile, dots, arrows

### Produk dengan Gambar
- 48 produk di-seed via `MassProductSeeder` (12 elektronik, 10 fashion, 8 rumah tangga, 5 buku, 5 kesehatan, 6 B2B, 2 featured extra)
- Semua produk sudah punya gambar AI (dari Bing/DALL-E)
- Disimpan di `storage/app/public/products/{slug}.jpg`
- Seeder auto-detect gambar dari nama slug

---

## 5. Akun Demo (Seeder)

| Email | Password | Peran |
|---|---|---|
| admin@seumpama.com | password | Admin |
| seller@seumpama.com | password | Seller individu (Toko Demo) |
| business@seumpama.com | password | Seller bisnis verified (Grosir Demo) |
| buyer@seumpama.com | password | Buyer personal (member + affiliate) |

---

## 6. Riwayat Commit (dari akhir ke awal)

- `add579a` feat: product reviews, PDF invoice, seller search, product page tabs
- `6dfbe65` feat(banner): AI-generated hero banners with model photos
- `946ac4b` docs: final README and PROGRESS for completed project
- `cdbb151` feat(escrow): hold funds, release to seller, refund on cancel
- `56ef8fa` fix(wallet): correct form tag syntax in topup view
- `b534e18` docs: final README
- *(wallet core, multi payment, cart grouping, affiliate, seller registration, design system)*
- `1fe4e49` Initial Laravel 12 + Breeze skeleton

---

## 7. Acceptance Criteria — Status

Semua ✅:

- Registrasi, login, verifikasi email → ✅
- Katalog, cart, checkout, payment → ✅
- B2C, B2B (tier, min qty), Member, Affiliate, Seller → ✅
- Wallet SeumpamaPay + escrow → ✅
- Multi payment method + countdown → ✅
- Reviews (verified purchase) → ✅
- PDF invoice → ✅
- Seller search + filter → ✅
- Admin dashboard + moderasi + verifikasi → ✅
- 32/32 test PASS → ✅

---

## 8. Batasan Simulasi

- Tidak ada payment gateway nyata
- Tidak ada integrasi PPOB / digital services (dihapus dari scope v2.0)
- Semua transaksi uang = simulasi internal
- Tabel `digital_*` di DB tidak dipakai (leftover)
- `discount_price` & `rating` produk = kosmetik display, tidak memengaruhi transaksi
- Review rating agregat memengaruhi `products.rating` (tampilan), tapi tidak mengubah harga

---

## 9. Pengembangan Lanjutan (Di Luar Scope)

- Fitur tarik saldo seller ke bank
- Komisi platform dari escrow (default 0%)
- Wishlist, flash sale countdown
- Export laporan CSV/PDF
- Migrasi Tailwind v4
- Notifikasi email order
- Multi-image produk (saat ini 1 gambar per produk)

---

## 10. Cara Demo

Lihat `README.md` bagian **Demo Scenario**.