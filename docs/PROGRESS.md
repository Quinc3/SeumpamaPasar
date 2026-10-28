# PROGRESS — Seumpama Pasar (Integrated E-Commerce Platform)

> **Terakhir diperbarui:** 28 Oktober 2026.
> **Status:** SEMUA FITUR SELESAI — SIAP DEMO AKADEMIK.

---

## 1. Status Umum

- **Nama Aplikasi:** Seumpama Pasar
- **Model:** B2C + B2B + Member + Affiliate + Seller
- **Fase:** 1–8 ✅ | UI Redesign (R1–R3) ✅ | Wallet + Escrow ✅
- **Tes:** `vendor\bin\phpunit` → 32/32 PASS
- **Kode:** lengkap, siap demo

---

## 2. Stack

- Laravel 12.70.1 · PHP 8.2.12
- Blade + Tailwind v3 + Vite 7 + Alpine.js
- MariaDB 10.4 (MySQL 8.0+ kompatibel)
- PHPUnit 11.5
- Heroicons v2 (SVG lokal, MIT)

---

## 3. Fitur Lengkap

### Marketplace
- Katalog publik dengan search, filter kategori, sort (terbaru/termurah/termahal/terlaris)
- Detail produk dengan rating, badge diskon, produk terlaris
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
- Saldo dengan top up (5 bank VA, preset + custom)
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

### Admin
- Dashboard analytics (revenue, order, commission, escrow)
- Moderasi produk (approve/reject)
- Verifikasi bisnis
- Kelola penjual (approve/suspend/activate)
- Kelola afiliasi (suspend/activate)
- Admin isolation: hanya akses `/admin`, tidak bisa belanja/jualan

---

## 4. Akun Demo (Seeder)

| Email | Password | Peran |
|---|---|---|
| admin@example.test | password | Admin |
| seller@example.test | password | Seller individu |
| business@example.test | password | Seller bisnis (verified) |
| buyer@example.test | password | Buyer personal (member + affiliate) |

---

## 5. Riwayat Commit (dari akhir ke awal)

- `cdbb151` feat(escrow): hold funds, release to seller, refund on cancel
- `56ef8fa` fix(wallet): correct form tag syntax in topup view
- `...` SeumpamaPay wallet core, topup, checkout integration
- `...` Multi payment method + countdown
- `...` Cart grouped per seller + bulk checkout
- `...` Affiliate commission + dashboard
- `...` Seller registration + admin approval
- `...` Design system R1–R3 + layout unification
- `1fe4e49` Initial Laravel 12 + Breeze skeleton

---

## 6. Acceptance Criteria — Status

Semua ✅ kecuali yang ditandai:

- Registrasi, login, verifikasi email → ✅
- Katalog, cart, checkout, payment → ✅
- B2C, B2B (tier, min qty), Member, Affiliate, Seller → ✅
- Wallet SeumpamaPay + escrow → ✅
- Multi payment method + countdown → ✅
- Admin dashboard + moderasi + verifikasi → ✅
- 32/32 test PASS → ✅

---

## 7. Batasan Simulasi

- Tidak ada payment gateway nyata
- Tidak ada integrasi PPOB / digital services (dihapus dari scope v2.0)
- Semua transaksi uang = simulasi internal
- Tabel `digital_*` di DB tidak dipakai (leftover)
- `discount_price` & `rating` produk = kosmetik display, tidak memengaruhi transaksi

---

## 8. Pengembangan Lanjutan (Di Luar Scope)

- Fitur tarik saldo seller ke bank (skip untuk demo)
- Komisi platform dari escrow (default 0%)
- Wishlist, review produk, flash sale
- Export laporan CSV/PDF
- Migrasi Tailwind v4

---

## 9. Cara Demo

Lihat `README.md` bagian **Demo Scenario**.