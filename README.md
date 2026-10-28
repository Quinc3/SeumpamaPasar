# Seumpama Pasar

Aplikasi e-commerce akademik dengan B2C + B2B, model Member/Affiliate/Seller, wallet SeumpamaPay, dan escrow simulasi.

---

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install && npm run build
php artisan storage:link
```

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=e_commerce
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://localhost:8000
```

Buat database `e_commerce` di MySQL/MariaDB, lalu:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

Buka [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## Akun Demo

| Email | Password | Peran |
|---|---|---|
| admin@example.test | password | Admin |
| seller@example.test | password | Seller individu (Toko Demo) |
| business@example.test | password | Seller bisnis (Grosir Demo) |
| buyer@example.test | password | Buyer (Member + Affiliate) |

---

## Demo Scenario (10–15 menit)

### 1. Buyer belanja
- Login `buyer@example.test` → katalog → tambah produk dari 2 toko berbeda
- `/keranjang` → grouped per seller, centang sebagian, checkout
- Pilih alamat + **SeumpamaPay** (kalau saldo cukup) / **VA BCA** (kalau tidak)
- Bayar Berhasil → order masuk ke seller, dana ditahan **escrow**

### 2. Seller proses
- Login `seller@example.test` → `/toko/pesanan` → **Proses** → **Kirim**
- Saldo tertahan terlihat di dashboard

### 3. Buyer konfirmasi → escrow cair
- Login buyer → `/akun/pesanan` → **Konfirmasi Terima**
- Saldo seller di SeumpamaPay bertambah

### 4. SeumpamaPay top up
- Login buyer → klik **SeumpamaPay** di header → **Top Up** → Rp 100.000 → **Simulasi Berhasil**
- Saldo bertambah, riwayat tercatat

### 5. B2B demo
- Login `business@example.test` → katalog → produk **Kaos Polos Grosir** (tier harga)
- Qty 24 → harga jadi **Rp 65.000/pcs** (bukan Rp 75.000)

### 6. B2B negative
- Login buyer personal (bukan bisnis) → akses URL produk B2B → **404**

### 7. Member benefit
- Login `buyer@example.test` (Member aktif) → checkout → diskon **5%** terlihat di produk B2C
- *(Diskon tidak berlaku di produk B2B — tier sudah diskon)*

### 8. Affiliate
- Login buyer → `/akun/afiliasi` → **Aktifkan** → dapat kode
- Salin link: `<url>/katalog?ref=KODE`
- Incognito → paste link → login user lain → beli → completed
- Kembali ke `/afiliasi` → komisi **5%** muncul

### 9. Seller self-registration
- Register user baru → `/profile` → **Buka Toko** → isi form → **pending**
- Login admin → **Kelola Penjual** → **Setujui**
- Login seller baru → **Produk** → **Produk Baru** → tunggu approve admin

### 10. Admin panel
- Login admin → dashboard analytics, moderasi, verifikasi bisnis, kelola penjual, afiliasi

---

## Batasan Simulasi

- Tidak ada transaksi uang sungguhan
- Tidak ada payment gateway / API eksternal
- Semua hasil pembayaran, refund, top up, escrow = logika internal
- Fitur layanan digital (listrik, pulsa, data) dihapus dari scope

---

## Testing

```bash
vendor/bin/phpunit
```

---

## Lisensi

Akademik — MIT.