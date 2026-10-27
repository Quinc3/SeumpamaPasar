# Integrated E-Commerce Platform

Aplikasi e-commerce akademik dengan **2 jenis perdagangan** (B2C, B2B) dan **3 model bisnis** (Member, Affiliate, Seller). Semua transaksi bersifat simulasi — tidak ada uang sungguhan, payment gateway, atau API eksternal.

## Stack

- Laravel 12.70.1 · PHP 8.2.12
- Blade + Tailwind CSS v3 + Vite 7 + Alpine.js
- MariaDB 10.4 (atau MySQL 8.0+)
- PHPUnit 11.5
- Heroicons v2 (SVG lokal)

## Persyaratan

- PHP 8.2+, Composer 2.x, Node.js 18+, npm
- MySQL 8.0+ / MariaDB 10.4+
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `bcmath`, `curl`, `gd`

## Instalasi

```bash
git clone <repo> e-commerce
cd e-commerce

# Backend
composer install
cp .env.example .env
php artisan key:generate

# Frontend
npm install
npm run build

# Storage (untuk gambar produk)
php artisan storage:link
```

Edit `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_commerce
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://localhost:8000
```

Buat database di MySQL/MariaDB:

```sql
CREATE DATABASE e_commerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Migrasi + seed:

```bash
php artisan migrate
php artisan db:seed
```

## Menjalankan

```bash
php artisan serve
# Buka http://127.0.0.1:8000
```

## Akun Demo (dari seeder)

| Email | Password | Peran |
|---|---|---|
| admin@example.test | password | Admin |
| seller@example.test | password | Seller individu |
| business@example.test | password | Seller bisnis (terverifikasi) |
| buyer@example.test | password | Buyer personal |

## Fitur

### Jenis Perdagangan
- **B2C** — semua pembeli bisa beli produk B2C
- **B2B** — hanya pembeli bisnis terverifikasi; harga tier grosir + minimum qty

### Model Bisnis
- **Member** — diskon 5% otomatis setelah aktivasi di `/akun/member`
- **Affiliate** — komisi 5% simulasi dari order via referral link; aktivasi di `/akun/afiliasi`
- **Seller** — kelola produk & pesanan via `/toko`

### Alur Belanja
Katalog → Keranjang → Checkout → Pembayaran Simulasi → Order

### Simulasi Pembayaran
Pilih **Berhasil** atau **Gagal** di halaman pembayaran. Status order mengikuti:
- Berhasil → `pending_processing` → seller proses → seller kirim → buyer terima → `completed`
- Gagal → `cancelled`, stok tidak berubah

### Refund Simulasi
Seller dapat membatalkan order `pending_processing` → stok kembali, refund dicatat di `orders.refunded_amount` (tidak ada uang nyata berpindah).

### Affiliate
- Aktivasi: `/akun/afiliasi` → dapat kode referral 8 karakter
- Share link: `<url>?ref=KODE`
- Komisi: 5% × subtotal order saat berstatus `completed`
- Self-referral diblokir

### Admin
- Dashboard analytics (pendapatan, order, komisi, penjual teratas)
- Moderasi produk (approve/reject)
- Verifikasi bisnis
- Kelola afiliasi (suspend/activate)
- Kelola kategori

### Seller
- Dashboard analytics (pendapatan, produk terlaris)
- CRUD produk (draft → pending_review → active)
- Tier harga grosir (khusus B2B/Both)
- Kelola pesanan (proses, kirim, batalkan)

## Testing

```bash
vendor/bin/phpunit
```

## Struktur Dokumen

Spesifikasi proyek di `docs/`:

- `PROJECT_SPEC.md` — spec bisnis (v2.0)
- `IMPLEMENTATION_PLAN.md` — roadmap (v2.0)
- `AI_RULES.md` — aturan kerja AI (v2.0)
- `PROGRESS.md` — status proyek

## Batasan Simulasi

- Tidak ada payment gateway
- Tidak ada integrasi provider PPOB / digital
- Semua transaksi uang = simulasi
- Tabel `digital_*` di DB tidak dipakai (leftover Fase 1)

## Lisensi

Proyek akademik — MIT.
