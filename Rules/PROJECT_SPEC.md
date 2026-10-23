# PROJECT_SPEC — Integrated E-Commerce Platform

> **Status dokumen:** Sumber kebenaran untuk Fase 2 dan seterusnya.
>
> **Status proyek:** Fase 1 (Discovery & Planning) selesai dan disetujui.
>
> **Kondisi kode:** Proyek belum diaudit. Belum ada klaim bahwa kode, migration, model, atau tes sudah tersedia atau berjalan. Semua deskripsi implementasi di dokumen ini merupakan spesifikasi yang direncanakan, bukan bukti implementasi.

---

## 1. Tujuan dan Ruang Lingkup

Aplikasi e-commerce untuk proyek akademik yang mendukung tiga model bisnis dalam satu aplikasi modular monolith:

- **B2C** — bisnis menjual kepada konsumen.
- **B2B** — bisnis menjual kepada bisnis lain dengan harga grosir bertingkat dan minimum pembelian.
- **C2C** — individu menjual kepada individu lain.

Aplikasi juga menyediakan modul simulasi layanan digital:

- Tagihan listrik pascabayar (dummy).
- Token listrik prabayar (dummy).
- Pulsa.
- Paket data.

### 1.1 Ruang lingkup

- Katalog produk, keranjang, checkout, dan pesanan per penjual.
- Pembayaran simulasi.
- Moderasi produk oleh admin.
- Verifikasi akun bisnis oleh admin.
- Dashboard admin dan penjual serta halaman akun pembeli.
- Laporan berdasarkan data yang tersimpan di database.
- Riwayat transaksi digital milik masing-masing pengguna.
- Simulasi pembatalan, pencatatan refund, dan pengembalian stok sesuai aturan dokumen ini.

### 1.2 Di luar ruang lingkup

- Payment gateway atau pemrosesan uang sungguhan.
- Integrasi API PPOB atau provider layanan digital.
- Microservices.
- Perhitungan ongkir, pajak, atau biaya pembayaran.
- Multi-currency.
- Notifikasi eksternal melalui email atau push notification.
- API publik untuk aplikasi mobile.

Perubahan ruang lingkup memerlukan keputusan baru sebelum implementasi.

### 1.3 Batasan simulasi

- Semua pembayaran harus diberi label **SIMULASI**.
- Token listrik menggunakan kode dummy yang diawali `DUMMY-` dan diberi penanda `SIMULASI`.
- Tidak menyimpan nomor kartu, CVV, password bank, PIN, atau kredensial sensitif.
- Pencatatan refund tidak memindahkan uang sungguhan.
- Seluruh transaksi layanan digital merupakan simulasi internal.

---

## 2. Stack Teknologi dan Batasan Teknis

| Aspek | Keputusan |
|---|---|
| Bahasa | PHP 8.2+ |
| Framework | Laravel versi stabil yang kompatibel; asumsi awal Laravel 12.x, diverifikasi saat audit |
| Database | MySQL 8.0+ |
| Frontend | Blade + Tailwind CSS melalui Vite |
| Auth starter | Laravel Breeze (Blade) |
| Testing | PHPUnit atau Pest, mengikuti kondisi proyek |
| Deployment target | Lingkungan lokal untuk demonstrasi akademik |

### 2.1 Asumsi yang wajib diverifikasi

Sebelum implementasi Fase 2, audit:

- Versi PHP dan Laravel.
- Versi Node.js dan package manager.
- Ketersediaan ekstensi PHP yang dibutuhkan, termasuk `pdo_mysql` dan ekstensi pemrosesan gambar yang relevan.
- Struktur folder dan file yang sudah ada.
- Kondisi autentikasi, route, migration, model, dan konfigurasi database.
- Status dependensi Composer dan npm.

Jika proyek sudah ada, pertahankan versi dan struktur yang kompatibel. Jangan menaikkan atau menurunkan versi tanpa alasan teknis yang jelas.

### 2.2 Batasan dependensi dan arsitektur

- Jangan menambah package Composer atau npm tanpa alasan yang dijelaskan.
- Jangan menggunakan package permission pihak ketiga; gunakan Policy, middleware, dan kolom identitas yang ditentukan dalam dokumen ini.
- Jangan menerapkan repository pattern atau abstraksi tambahan tanpa kebutuhan konkret.
- Gunakan modular monolith dengan service class untuk logika bisnis yang memang perlu digunakan bersama.
- Hindari duplikasi aturan bisnis antara controller, job, dan service.
- Jangan membangun fitur di luar ruang lingkup hanya untuk mengantisipasi kebutuhan yang belum disetujui.

---

## 3. Role Pengguna dan Matriks Otorisasi

### 3.1 Atribut identitas

- `users.role` ∈ `admin`, `user`.
- `users.account_type` ∈ `personal`, `business`.
- `business_profiles.verified_at`: `NULL` berarti belum terverifikasi.
- `seller_profiles.seller_type` ∈ `individual`, `business`.
- `seller_profiles.status` ∈ `pending`, `active`, `suspended`.

Status `rejected` tidak digunakan untuk `seller_profiles`. Pendaftaran penjual yang ditolak menggunakan status `suspended` disertai pencatatan alasan penolakan pada mekanisme yang ditentukan saat implementasi.

### 3.2 Definisi peran efektif

- **Admin:** `users.role = admin`.
- **Pembeli konsumen:** pengguna dengan `role = user` yang merupakan akun personal atau akun bisnis yang belum terverifikasi.
- **Pembeli bisnis terverifikasi:** pengguna dengan `role = user`, `account_type = business`, dan `business_profiles.verified_at IS NOT NULL`.
- **Penjual individu (C2C):** memiliki `seller_profiles` dengan `seller_type = individual` dan `status = active`.
- **Penjual bisnis (B2C/B2B):** memiliki `seller_profiles` dengan `seller_type = business`, `status = active`, serta profil bisnis terverifikasi.

Status seller yang aktif tidak menggantikan pemeriksaan kepemilikan akun, dan verifikasi bisnis tidak otomatis mengaktifkan profil seller.

### 3.3 Matriks otorisasi

| Kapabilitas | Admin | Buyer personal | Buyer bisnis terverifikasi | Seller individu | Seller bisnis |
|---|---|---|---|---|---|
| Lihat katalog publik | Ya | Ya | Ya | Ya | Ya |
| Lihat harga grosir dan tier | Tidak | Tidak | Ya | Tidak | Tidak |
| Kelola profil dan alamat sendiri | Ya | Ya | Ya | Ya | Ya |
| Kelola produk sendiri | Tidak | Tidak | Tidak | Ya | Ya |
| Mengatur tier harga grosir | Tidak | Tidak | Tidak | Tidak | Ya |
| Mengatur `min_purchase_qty` | Tidak | Tidak | Tidak | Tidak | Ya |
| Lihat pesanan sendiri sebagai pembeli | Semua | Milik sendiri | Milik sendiri | Milik sendiri | Milik sendiri |
| Lihat pesanan toko sendiri | Semua | Tidak | Tidak | Milik sendiri | Milik sendiri |
| Ubah status pemenuhan pesanan | Tidak | Tidak | Tidak | Milik sendiri | Milik sendiri |
| Batalkan order `pending_processing` | Semua | Tidak | Tidak | Milik sendiri | Milik sendiri |
| Moderasi produk | Ya | Tidak | Tidak | Tidak | Tidak |
| Kelola kategori | Ya | Tidak | Tidak | Tidak | Tidak |
| Verifikasi akun bisnis | Ya | Tidak | Tidak | Tidak | Tidak |
| Approve atau suspend seller | Ya | Tidak | Tidak | Tidak | Tidak |
| Kelola konfigurasi layanan digital | Ya | Tidak | Tidak | Tidak | Tidak |
| Gunakan layanan digital simulasi | Ya | Ya | Ya | Ya | Ya |
| Lihat laporan admin | Ya | Tidak | Tidak | Tidak | Tidak |
| Lihat laporan penjual | Tidak | Tidak | Tidak | Toko sendiri | Toko sendiri |

Semua otorisasi wajib diperiksa di server menggunakan Policy dan middleware yang sesuai. Menyembunyikan tombol di UI bukan mekanisme keamanan.

Admin yang bertransaksi sebagai pembeli tetap mengikuti aturan transaksi sesuai kebijakan akun dan fitur yang diterapkan; hak admin tidak boleh secara otomatis melewati validasi integritas transaksi.

---

## 4. Aturan B2C, B2B, C2C, Harga Grosir, dan Verifikasi Bisnis

### 4.1 Batasan `sales_channel`

- `seller_type = individual` hanya boleh memiliki produk dengan `sales_channel = b2c`.
- `seller_type = business` boleh memiliki produk dengan `sales_channel = b2c`, `b2b`, atau `both`.

Aturan ini harus divalidasi pada server saat membuat atau memperbarui produk.

### 4.2 Matriks pembelian

| `sales_channel` | Pembeli personal | Bisnis belum terverifikasi | Bisnis terverifikasi |
|---|---|---|---|
| `b2c` | Boleh, harga dasar | Boleh, harga dasar | Boleh, harga dasar |
| `b2b` | Ditolak; produk tidak ditampilkan dan URL produk mengembalikan 404 | Ditolak; produk tidak ditampilkan dan URL produk mengembalikan 404 | Boleh, aturan minimum pembelian berlaku |
| `both` | Boleh, harga dasar | Boleh, harga dasar | Boleh, aturan minimum pembelian berlaku |

Tidak ada fallback dari produk `b2b` ke harga konsumen untuk pembeli yang tidak memenuhi syarat. Produk `both` dapat dibeli oleh konsumen menggunakan harga dasar, tetapi akses harga grosir hanya tersedia untuk bisnis terverifikasi.

### 4.3 Harga dan kuantitas

- `min_purchase_qty` berlaku hanya untuk pembeli bisnis terverifikasi pada produk `b2b` atau `both`.
- Pembeli personal dan bisnis yang belum terverifikasi menggunakan minimum kuantitas 1.
- Jika pembeli bisnis terverifikasi melakukan checkout dengan kuantitas di bawah `min_purchase_qty`, checkout ditolak dengan pesan:

  `Jumlah minimum pembelian untuk produk ini adalah X.`

#### Resolusi harga

Untuk pembeli bisnis terverifikasi pada produk `b2b` atau `both`:

1. Ambil tier harga yang memenuhi `min_quantity <= quantity`.
2. Pilih tier dengan `min_quantity` terbesar yang memenuhi syarat.
3. Jika tidak ada tier yang memenuhi syarat, gunakan `products.price`.

Untuk pembeli lainnya, gunakan `products.price`.

Ketentuan tier:

- `min_quantity >= 1`.
- `price > 0`.
- `price < products.price`.
- `unique(product_id, min_quantity)`.

Harga dihitung oleh satu komponen logika bisnis, misalnya `PriceResolver`, yang digunakan secara konsisten oleh katalog, keranjang, checkout, dan validasi pembayaran.

#### Perubahan status verifikasi bisnis

- Checkout yang belum dibayar wajib memvalidasi ulang kelayakan pembeli ketika pembayaran dicoba.
- Jika verifikasi bisnis dicabut dan pembeli tidak lagi memenuhi syarat, pembayaran checkout yang menggunakan aturan B2B harus gagal dan dicatat sebagai kegagalan pembayaran.
- Order yang sudah dibayar tetap menggunakan snapshot harga dan kuantitas yang telah disimpan.

### 4.4 Moderasi produk

Transisi produk:

- `draft → pending_review` ketika penjual mengirim produk untuk ditinjau.
- `pending_review → active` ketika admin menyetujui dan seller masih aktif.
- `pending_review → rejected` ketika admin menolak; `rejection_note` wajib diisi.
- `rejected → pending_review` ketika penjual mengirim ulang produk setelah revisi.
- `active → archived` ketika produk diarsipkan.

Ketentuan:

- Produk `active` hanya boleh tampil jika `seller_profiles.status = active`.
- Query katalog wajib memastikan seller aktif.
- Produk seller yang disuspend tidak tampil di katalog dan tidak dapat dipesan.
- Akses manajemen toko oleh seller yang disuspend ditolak.
- Seller yang disuspend tidak dapat mengubah status pemenuhan order.
- Produk dengan `order_items` tidak boleh dihapus permanen; gunakan status `archived`.
- Produk `archived` tidak tampil di katalog dan tidak dapat dipesan, tetapi snapshot pesanan lama tetap tersedia.

---

## 5. Rancangan Database dan Relasi

Skema berikut merupakan rancangan target. Struktur final migration wajib disesuaikan dengan hasil audit proyek dan tetap mempertahankan seluruh aturan integritas dokumen ini.

### 5.1 Identitas dan profil

**`users`**

- `id`
- `name`
- `email` (unique)
- `password`
- `role` (`admin`, `user`)
- `account_type` (`personal`, `business`)
- `email_verified_at` (nullable)
- `timestamps`

**`business_profiles`**

- `id`
- `user_id` (FK, unique)
- `company_name`
- `tax_id` (nullable)
- `phone`
- `address`
- `verified_at` (nullable)
- `verified_by` (FK ke `users`, nullable)
- `timestamps`

**`seller_profiles`**

- `id`
- `user_id` (FK, unique)
- `seller_type` (`individual`, `business`)
- `store_name` (unique)
- `description`
- `status` (`pending`, `active`, `suspended`)
- `timestamps`

**`addresses`**

- `id`
- `user_id` (FK)
- `label`
- `recipient_name`
- `phone`
- `line1`
- `city`
- `province`
- `postal_code`
- `is_default`
- `timestamps`

### 5.2 Katalog

**`categories`**

- `id`
- `name`
- `slug` (unique)
- `parent_id` (self-FK, nullable)
- `timestamps`

**`products`**

- `id`
- `seller_profile_id` (FK)
- `category_id` (FK)
- `name`
- `slug` (unique)
- `description`
- `price` (`decimal(15,2)`)
- `stock` (unsigned integer)
- `sales_channel` (`b2c`, `b2b`, `both`)
- `min_purchase_qty` (unsigned integer, default 1)
- `status` (`draft`, `pending_review`, `active`, `rejected`, `archived`)
- `rejection_note` (nullable)
- `timestamps`

Index yang direncanakan:

- `(status, category_id)`
- `(seller_profile_id, status)`
- `(status, sales_channel)`

**`product_images`**

- `id`
- `product_id` (FK)
- `path`
- `sort_order`
- `timestamps`

**`wholesale_price_tiers`**

- `id`
- `product_id` (FK)
- `min_quantity` (unsigned integer)
- `price` (`decimal(15,2)`)
- unique `(product_id, min_quantity)`

### 5.3 Keranjang

**`cart_items`**

- `id`
- `user_id` (FK)
- `product_id` (FK)
- `quantity`
- unique `(user_id, product_id)`
- `timestamps`

Keranjang bukan sumber kebenaran harga atau stok. Harga, status produk, kelayakan pembeli, minimum kuantitas, dan stok harus divalidasi ulang saat checkout dan pembayaran.

### 5.4 Checkout dan pesanan

**`checkouts`**

- `id`
- `user_id` (FK)
- `reference` (unique)
- `total_amount` (`decimal(15,2)`)
- `status` (`pending_payment`, `paid`, `cancelled`)
- `timestamps`

**`orders`**

- `id`
- `checkout_id` (FK)
- `seller_profile_id` (FK)
- `buyer_id` (FK ke `users`)
- `subtotal` (`decimal(15,2)`)
- `status` (`pending_payment`, `pending_processing`, `processing`, `shipped`, `completed`, `cancelled`)
- `shipping_recipient_name`
- `shipping_phone`
- `shipping_line1`
- `shipping_city`
- `shipping_province`
- `shipping_postal_code`
- `stock_deducted_at` (nullable)
- `stock_restored_at` (nullable)
- `refunded_amount` (`decimal(15,2)`, default 0)
- `refunded_at` (nullable)
- `timestamps`

Index yang direncanakan:

- `(seller_profile_id, status)`
- `(buyer_id, status)`
- `(checkout_id, status)`

**`order_items`**

- `id`
- `order_id` (FK)
- `product_id` (FK nullable, `nullOnDelete`)
- `product_name` (snapshot)
- `unit_price` (`decimal(15,2)`, snapshot)
- `quantity`
- `subtotal` (`decimal(15,2)`, snapshot)
- `timestamps`

`product_id` boleh menjadi `NULL` jika produk dihapus melalui mekanisme yang diizinkan setelah tidak lagi diperlukan sebagai referensi aktif. Riwayat tetap menggunakan snapshot item.

**`payments`**

- `id`
- `checkout_id` (FK, unique)
- `method` (`demo_transfer`, `demo_ewallet`, `demo_va`)
- `status` (`pending`, `paid`, `failed`)
- `reference` (unique)
- `paid_at` (nullable)
- `failed_at` (nullable)
- `failure_reason` (nullable)
- `timestamps`

Satu checkout hanya memiliki satu record payment simulasi. Status payment merupakan sumber kebenaran untuk menentukan apakah checkout masih dapat diproses.

### 5.5 Layanan digital

**`digital_products`**

- `id`
- `type` (`electricity_bill`, `electricity_token`, `pulse`, `data_package`)
- `code` (unique)
- `name`
- `provider` (nullable)
- `nominal_value` (`decimal`, nullable)
- `base_price` (`decimal(15,2)`)
- `metadata` (JSON)
- `is_active` (boolean)
- `timestamps`

**`dummy_bills`**

- `id`
- `meter_number`
- `customer_name`
- `period` (contoh `2025-01`)
- `amount` (`decimal(15,2)`)
- `timestamps`
- unique `(meter_number, period)`

Satu nomor meter boleh memiliki beberapa periode tagihan, tetapi tidak boleh memiliki dua record untuk periode yang sama.

**`digital_transactions`**

- `id`
- `user_id` (FK)
- `digital_product_id` (FK nullable)
- `type` (snapshot tipe produk)
- `customer_number`
- `customer_name` (nullable)
- `amount` (`decimal(15,2)`)
- `status` (`pending`, `success`, `failed`)
- `reference` (unique)
- `token_code` (nullable, dummy)
- `failure_reason` (nullable)
- `metadata` (JSON)
- `timestamps`

Index yang direncanakan:

- `(user_id, created_at)`

Data sensitif seperti nomor meter dan nomor HP tidak boleh dicatat dalam log aplikasi secara penuh.

### 5.6 Sistem

**`activity_logs`**

- `id`
- `user_id` (FK nullable)
- `action`
- `subject_type`
- `subject_id`
- `metadata` (JSON)
- `created_at`

Log tidak menyimpan password, token autentikasi, nomor kartu, PIN, atau PII yang tidak diperlukan.

### 5.7 Constraint dan aturan penghapusan

- Foreign key menggunakan kebijakan pembatasan penghapusan yang sesuai, umumnya `restrictOnDelete()`, kecuali relasi yang secara eksplisit menggunakan `nullOnDelete()`.
- `users` tidak dihapus permanen melalui alur aplikasi normal.
- Produk yang sudah memiliki `order_items` tidak boleh dihapus permanen melalui UI.
- Seller yang memiliki produk atau riwayat pesanan tidak boleh dihapus permanen melalui UI; gunakan suspend.
- Produk yang diarsipkan tetap mempertahankan riwayat transaksi.
- Foreign key `order_items.product_id` boleh menjadi `NULL`; snapshot item harus tetap dapat ditampilkan.

### 5.8 Aturan snapshot dan nilai uang

- `order_items` menyimpan snapshot `product_name`, `unit_price`, `quantity`, dan `subtotal`.
- `orders.shipping_*` menyimpan snapshot alamat penerima pada saat checkout.
- Perubahan harga produk, nama produk, tier harga, atau alamat setelah checkout tidak mengubah snapshot.
- Jika pembeli belum memiliki alamat pengiriman, checkout diblokir dengan pesan:

  `Lengkapi alamat pengiriman terlebih dahulu.`

- Gunakan tipe `DECIMAL` untuk nilai uang, bukan floating-point.
- `order_items.subtotal = unit_price × quantity`.
- `orders.subtotal = jumlah subtotal item dalam order tersebut`.
- `checkouts.total_amount = jumlah subtotal seluruh order pada checkout`.
- Seluruh perhitungan nilai uang harus menggunakan aturan pembulatan yang konsisten.

---

## 6. Checkout, Pembayaran, Stok, Pembatalan, Refund, dan Layanan Digital

### 6.1 Alur checkout

1. Pembeli memasukkan produk ke keranjang dengan kuantitas minimal 1.
2. Sistem memvalidasi kelayakan pembeli, kanal penjualan, dan `min_purchase_qty`.
3. Saat checkout, sistem menghitung ulang harga menggunakan aturan §4.3.
4. Sistem memeriksa stok awal. Pemeriksaan ini bersifat awal karena stok dapat berubah sebelum pembayaran.
5. Sistem memastikan pembeli memiliki alamat pengiriman.
6. Sistem membuat satu record `checkouts`, N record `orders` (satu per seller), dan record `order_items` untuk setiap item.
7. Sistem menyimpan snapshot harga, nama produk, kuantitas, subtotal, dan alamat.
8. `checkouts.total_amount` merupakan jumlah seluruh subtotal order dan tidak berubah setelah checkout dibuat.
9. Sistem membuat satu record `payments` dengan status `pending`.
10. Pembuatan checkout dan record turunannya dilakukan dalam transaksi database agar tidak menghasilkan checkout parsial.

Jika validasi gagal sebelum checkout berhasil dibuat, sistem tidak boleh meninggalkan record checkout atau order yang tidak konsisten.

### 6.2 Alur pembayaran simulasi

Endpoint konseptual: `POST /checkout/{ref}/pay`.

**Urutan lock global untuk transaksi multi-tabel:**

`payments → products (ORDER BY id ASC) → orders (ORDER BY id ASC)`

Aturan penerapan:

- Lock hanya baris yang dibutuhkan.
- Jika transaksi melibatkan beberapa tabel, ikuti urutan lock global tersebut.
- Jika mengunci beberapa produk atau order, kunci berdasarkan `id ASC`.
- Setelah lock diperoleh, validasi ulang kondisi bisnis.
- Operasi yang tidak mengubah suatu tabel tidak perlu mengunci tabel tersebut.
- Urutan lock mengurangi risiko deadlock, tetapi tidak menjamin deadlock mustahil terjadi.

#### Langkah pembayaran

1. Mulai transaksi database.
2. Lock record payment yang sesuai dengan checkout menggunakan `SELECT ... FOR UPDATE`.
3. Pastikan checkout tersebut dimiliki pembeli yang melakukan permintaan.
4. Periksa status payment. Jika bukan `pending`, batalkan transaksi dan kembalikan `409 Conflict`.
5. Lock semua produk terkait berdasarkan `id ASC`.
6. Lock semua order terkait berdasarkan `id ASC`.
7. Validasi ulang seluruh kondisi berikut di dalam transaksi:
   - Semua order masih berstatus `pending_payment`.
   - Semua produk masih `active`.
   - Semua seller terkait masih `active`.
   - Pembeli masih memenuhi syarat kanal penjualan.
   - Kuantitas masih memenuhi `min_purchase_qty` jika berlaku.
   - Stok mencukupi untuk seluruh item.
   - Harga hasil `PriceResolver`, menggunakan tipe pembeli dan kuantitas yang sama dengan checkout, masih sama dengan `order_items.unit_price`.
   - Seluruh nilai snapshot dan total checkout konsisten.
8. Jalankan salah satu cabang berikut.

#### Cabang sukses

Jika validasi lolos dan pembeli memilih simulasi berhasil:

- Kurangi stok untuk setiap item dalam transaksi yang sama.
- Isi `orders.stock_deducted_at`.
- Ubah `orders.status` dari `pending_payment` menjadi `pending_processing`.
- Ubah `payments.status` dari `pending` menjadi `paid`.
- Isi `payments.paid_at`.
- Ubah `checkouts.status` dari `pending_payment` menjadi `paid`.
- Commit transaksi.

Seluruh perubahan harus berhasil bersama. Jika terjadi kegagalan, seluruh transaksi harus rollback.

#### Cabang gagal

Jika pembeli memilih simulasi gagal atau validasi bisnis gagal:

- Rollback transaksi pembayaran terlebih dahulu.
- Jangan mengurangi stok.
- Jangan mengubah status order atau checkout di transaksi yang sudah di-rollback.
- Jalankan `recordPaymentFailure` dalam transaksi terpisah sesuai §6.3.
- Kembalikan pesan sesuai alasan kegagalan dan arahkan pembeli membuat checkout baru.

Pesan yang ditampilkan:

- Stok tidak mencukupi: `Stok berubah sejak checkout. Pembayaran disimulasikan gagal; silakan ulangi pesanan.`
- Harga berubah: `Harga produk berubah sejak checkout. Pembayaran disimulasikan gagal; silakan buat checkout baru.`
- Produk atau seller tidak aktif: `Produk atau penjual tidak lagi tersedia. Pembayaran disimulasikan gagal; silakan buat checkout baru.`
- Kelayakan B2B berubah: `Akun tidak lagi memenuhi syarat pembelian grosir. Pembayaran disimulasikan gagal; silakan buat checkout baru.`

Checkout yang sudah gagal dan berstatus `cancelled` tidak dapat dibayar ulang. Pembeli harus membuat checkout baru.

### 6.3 Pencatatan kegagalan pembayaran — `recordPaymentFailure`

**Prinsip:** `payments.status` merupakan sumber kebenaran. Order dan checkout hanya boleh dibatalkan jika perubahan payment dari `pending` ke `failed` benar-benar berhasil.

Gunakan transaksi database terpisah:

1. Mulai transaksi.
2. Lock payment terkait dengan `SELECT ... FOR UPDATE`.
3. Jika payment tidak ditemukan, batalkan transaksi dan tangani sebagai resource tidak ditemukan.
4. Jika `payments.status != pending`, rollback atau selesaikan sebagai no-op tanpa mengubah order maupun checkout.
5. Update payment menggunakan kondisi tambahan `WHERE status = 'pending'`, dengan mengisi:
   - `status = failed`
   - `failure_reason`
   - `failed_at`
6. Jika jumlah baris yang terpengaruh bukan 1, jangan mengubah order atau checkout. Batalkan transaksi atau tangani sebagai no-op yang aman.
7. Lock order terkait berdasarkan `id ASC`, setelah payment terkunci.
8. Pastikan order terkait masih `pending_payment`. Jika status tidak konsisten, jangan menimpa status order yang sudah berubah; rollback dan catat kondisi tersebut untuk investigasi.
9. Ubah order yang masih `pending_payment` menjadi `cancelled`.
10. Ubah checkout menjadi `cancelled` hanya jika masih `pending_payment`.
11. Commit transaksi.

Kode alasan kegagalan harus konsisten, misalnya:

- `buyer_selected_failure`
- `insufficient_stock`
- `price_changed`
- `product_inactive`
- `seller_inactive`
- `buyer_not_eligible`
- `minimum_quantity_not_met`
- `checkout_validation_failed`

Kode tersebut merupakan nilai internal. Pesan yang ditampilkan kepada pengguna tetap dalam bahasa Indonesia.

Jika payment sudah `paid` atau `failed`, pemanggilan berikutnya tidak boleh mengubah order atau checkout. Jika operasi pembayaran sukses dan gagal berlangsung bersamaan, lock payment menentukan urutan proses; hasil akhirnya harus konsisten dan tidak boleh menghasilkan payment `paid` dengan order yang dibatalkan oleh alur kegagalan pembayaran.

### 6.4 Pembatalan order, refund, dan restock — `OrderCancellationService`

Penjual pemilik order atau admin menggunakan service yang sama.

#### Precondition

- Pengguna berwenang: admin atau penjual pemilik order.
- Order berstatus `pending_processing`.

Otorisasi harus dilakukan di server. Precondition harus divalidasi ulang setelah lock diperoleh karena status dapat berubah akibat request bersamaan.

#### Transaksi pembatalan

1. Baca `order_items` untuk mengumpulkan `product_id` unik. Pembacaan awal ini bukan lock dan hanya digunakan untuk menentukan kandidat produk.
2. Mulai transaksi database.
3. Lock produk terkait dengan urutan `id ASC`.
4. Lock record order dengan `SELECT ... FOR UPDATE`.
5. Validasi ulang:
   - Jika order sudah `cancelled`, kembalikan hasil `already_cancelled` tanpa mengulang restock atau refund.
   - Jika status bukan `pending_processing`, batalkan transaksi dengan hasil `invalid_status`.
   - Jika `stock_deducted_at IS NULL`, perlakukan sebagai kondisi integritas yang tidak valid.
   - Jika `refunded_at IS NOT NULL`, tolak pemrosesan refund ulang.
   - Jika `stock_restored_at IS NOT NULL`, tolak restock ulang.
6. Pastikan produk dan item order sesuai dengan data yang digunakan untuk pengembalian stok.
7. Tambahkan stok kembali sesuai kuantitas item.
8. Isi `stock_restored_at`.
9. Isi `refunded_amount = orders.subtotal`.
10. Isi `refunded_at`.
11. Ubah `orders.status` menjadi `cancelled`.
12. Commit transaksi.

Semua perubahan stok, refund, dan status order harus berhasil bersama atau rollback bersama.

Jika produk sudah tidak tersedia sebagai record aktif, pengembalian stok tidak boleh diam-diam diabaikan. Kondisi tersebut harus ditangani sebagai masalah integritas data dan transaksi dibatalkan kecuali mekanisme pengarsipan tetap mempertahankan record produk.

#### Ketentuan refund

- `payments.status` tidak diubah dan tetap `paid`.
- `checkouts.status` tidak diubah dan tetap `paid`.
- Refund dicatat per order.
- `refunded_amount = orders.subtotal`.
- Tidak ada ongkir, pajak, atau biaya pembayaran.
- Label UI:

  `Pengembalian dana (simulasi) — tidak ada uang sungguhan yang berpindah.`

Pemanggilan service kedua kali tidak boleh menambah stok atau mencatat refund kedua.

### 6.5 Aturan pembatalan

| Pelaku | Kondisi | Diizinkan |
|---|---|---|
| Pembeli | Checkout `pending_payment` | Membatalkan seluruh checkout |
| Pembeli | Checkout sudah dibayar | Tidak |
| Penjual pemilik | Order `pending_processing` | Ya, melalui `OrderCancellationService` |
| Penjual pemilik | Order `processing`, `shipped`, `completed`, atau `cancelled` | Tidak |
| Admin | Order `pending_processing` | Ya, melalui `OrderCancellationService` |
| Admin | Order `processing`, `shipped`, `completed`, atau `cancelled` | Tidak |

Pembatalan checkout sebelum pembayaran harus menggunakan transaksi database, mengunci payment sebelum order sesuai urutan lock global, serta hanya membatalkan checkout jika payment masih `pending`. Jika payment sudah `paid`, pembatalan sebelum bayar harus ditolak.

Pembatalan order setelah pembayaran tidak boleh dilakukan dengan mengubah seluruh checkout menjadi `cancelled`. Order lain pada checkout yang sama tetap berdiri sendiri.

### 6.6 Layanan digital simulasi

Semua proses dijalankan oleh `DigitalServiceRunner` atau service internal setara. Tidak ada pemanggilan API eksternal.

| Layanan | Input | Validasi | Sumber data | Output simulasi |
|---|---|---|---|---|
| Tagihan listrik pascabayar | Nomor meter dan periode | Format nomor; kombinasi meter-periode harus tersedia | `dummy_bills` | Nilai tagihan dan referensi `DUMMY-BILL-...` |
| Token listrik prabayar | Nomor meter dan nominal | Nomor meter valid; nominal tersedia di `digital_products` | `digital_products` | Token dummy berawalan `DUMMY-TOKEN-` dan penanda simulasi |
| Pulsa | Nomor HP, operator, nominal | Format HP Indonesia; produk demo aktif | `digital_products` | Referensi `DUMMY-PULSE-...` |
| Paket data | Nomor HP, operator, paket | Format HP Indonesia; produk demo aktif | `digital_products` | Referensi `DUMMY-DATA-...` |

Ketentuan:

- Hanya produk digital aktif yang dapat digunakan.
- Input divalidasi di server.
- Setiap permintaan yang diproses menghasilkan referensi unik.
- Riwayat transaksi hanya dapat dilihat pemiliknya dan admin sesuai otorisasi.
- Semua output diberi penanda simulasi.
- Tidak ada pembayaran uang nyata.

---

## 7. Status Transaksi dan Transisi yang Diizinkan

### 7.1 `checkouts.status`

Transisi:

- `pending_payment → paid` ketika payment menjadi `paid`.
- `pending_payment → cancelled` ketika payment menjadi `failed` atau pembeli membatalkan checkout sebelum pembayaran.

Status `paid` dan `cancelled` bersifat final.

### 7.2 `payments.status`

Transisi:

- `pending → paid`
- `pending → failed`

Status `paid` dan `failed` bersifat final. Tidak ada status `refunded`.

### 7.3 `orders.status`

Transisi:

- `pending_payment → pending_processing` ketika pembayaran sukses dan validasi lolos.
- `pending_payment → cancelled` ketika payment gagal atau checkout dibatalkan sebelum pembayaran.
- `pending_processing → processing` ketika penjual mulai memproses pesanan.
- `pending_processing → cancelled` ketika penjual atau admin membatalkan order melalui `OrderCancellationService`.
- `processing → shipped` ketika penjual mengirim pesanan.
- `shipped → completed` ketika pembeli mengonfirmasi penerimaan.

Tidak ada transisi ke `cancelled` setelah order masuk ke status `processing`, `shipped`, atau `completed`.

Refund simulasi hanya terjadi pada transisi `pending_processing → cancelled`.

### 7.4 `seller_profiles.status`

Status yang digunakan:

- `pending`
- `active`
- `suspended`

Transisi:

- `pending → active` ketika admin menyetujui pendaftaran seller.
- `pending → suspended` ketika admin menolak atau menonaktifkan pendaftaran seller.
- `active → suspended` ketika admin menangguhkan seller.
- `suspended → active` ketika admin mengaktifkan kembali seller setelah pemeriksaan.

Status `rejected` tidak digunakan pada tabel ini. Jika diperlukan, alasan penolakan dicatat secara terpisah tanpa memperluas enum status yang telah disepakati.

### 7.5 `products.status`

Transisi:

- `draft → pending_review`
- `pending_review → active`
- `pending_review → rejected`
- `rejected → pending_review`
- `active → archived`

Produk hanya dapat diaktifkan jika seller berstatus `active`. Produk yang sudah memiliki riwayat order tidak boleh dihapus permanen melalui alur aplikasi normal.

### 7.6 `digital_transactions.status`

Transisi:

- `pending → success`
- `pending → failed`

Status terminal tidak boleh diubah kembali tanpa keputusan desain baru.

---

## 8. Keamanan

- Gunakan validasi server-side melalui FormRequest atau mekanisme validasi Laravel yang sesuai.
- Terapkan otorisasi melalui Policy dan middleware, termasuk middleware admin dan pemeriksaan seller aktif.
- Cegah IDOR dengan memvalidasi kepemilikan resource untuk setiap akses.
- Gunakan proteksi CSRF Laravel.
- Gunakan Eloquent atau query parameterized; hindari raw SQL tanpa binding.
- Atur mass assignment secara eksplisit melalui `$fillable` atau mekanisme setara yang aman.
- Gunakan escaping Blade (`{{ }}`) untuk konten pengguna.
- Validasi upload berdasarkan MIME, ekstensi, ukuran, dan nama file yang dibuat server.
- Simpan gambar di `storage/app/public` dan gunakan mekanisme storage Laravel.
- Batas gambar produk: maksimal 5 gambar, 2 MB per gambar, format JPG/JPEG, PNG, atau WebP.
- Terapkan rate limit pada endpoint login, pembayaran, dan endpoint sensitif lainnya.
- Validasi status dan transisi status di server, bukan hanya di UI.
- Gunakan transaksi database untuk operasi stok, pembayaran, checkout, pembatalan, dan refund.
- Redaksi nomor meter, nomor HP, alamat, `tax_id`, dan PII lain dari log aplikasi.
- Jangan menyimpan password, token autentikasi, PIN, CVV, atau kredensial pembayaran.
- Jangan meminta atau membagikan `.env` asli. Audit konfigurasi melalui `.env.example` dan informasi yang telah disanitasi.
- Jangan mengklaim keamanan konkurensi hanya berdasarkan penggunaan transaksi; pengujian harus mencakup request bersamaan pada MySQL.

---

## 9. Acceptance Criteria

Seluruh kriteria berikut harus diuji minimal pada Fase 7. Status checklist hanya boleh diubah menjadi selesai setelah tersedia bukti implementasi dan hasil pengujian.

### 9.1 Fungsional

- [ ] Registrasi dan autentikasi buyer berjalan.
- [ ] Pengajuan profil seller individu dan bisnis berjalan.
- [ ] Verifikasi bisnis oleh admin mengaktifkan akses harga grosir.
- [ ] Produk C2C dan B2C mengikuti moderasi.
- [ ] Produk berstatus `rejected` tidak tampil di katalog.
- [ ] Checkout menolak kuantitas di bawah `min_purchase_qty` untuk buyer bisnis pada produk B2B.
- [ ] `PriceResolver` menghasilkan harga tier yang benar pada batas kuantitas.
- [ ] Checkout multi-penjual menghasilkan N order berdasarkan seller.
- [ ] Pembatalan satu order tidak membatalkan order lain dalam checkout yang sama.
- [ ] Pembayaran ganda menghasilkan satu hasil final; request yang tidak lagi valid menerima `409 Conflict`.
- [ ] Buyer tidak dapat mengakses order, checkout, atau resource pribadi milik pengguna lain.
- [ ] Seller tidak dapat mengubah produk atau order seller lain.
- [ ] Non-admin tidak dapat mengakses fitur admin.
- [ ] Tagihan listrik menggunakan data `dummy_bills`.
- [ ] Token listrik selalu ditandai dummy dan simulasi.
- [ ] Input layanan digital dengan format tidak valid ditolak.
- [ ] Riwayat transaksi digital hanya menampilkan transaksi yang diizinkan untuk pengguna tersebut.
- [ ] UI responsif pada lebar 375 px, tablet, dan desktop.
- [ ] Banner "Mode Simulasi" tampil pada semua halaman transaksi.

### 9.2 Aturan B2B, B2C, dan C2C

- [ ] Produk `sales_channel = b2b` tidak tampil untuk buyer personal dan bisnis yang belum terverifikasi.
- [ ] Akses langsung ke halaman produk B2B oleh buyer yang tidak memenuhi syarat menghasilkan 404.
- [ ] `CartService` dan `CheckoutService` menolak pembelian B2B yang tidak memenuhi syarat.
- [ ] `min_purchase_qty` hanya berlaku pada buyer bisnis terverifikasi untuk produk `b2b` atau `both`.
- [ ] Harga tier yang tepat dipilih pada batas kuantitas, termasuk ketika kuantitas sama dengan `min_quantity`.
- [ ] Perubahan verifikasi bisnis sebelum pembayaran divalidasi ulang.
- [ ] Penjual individu tidak dapat membuat produk dengan kanal `b2b` atau `both`.

### 9.3 Integritas stok dan transaksi

- [ ] Perubahan harga setelah checkout membuat pembayaran gagal dan stok tidak berubah.
- [ ] Perubahan tier grosir setelah checkout membuat pembayaran gagal jika harga hasil resolusi berubah.
- [ ] Dua request pembayaran bersamaan hanya menghasilkan satu decrement stok.
- [ ] Request pembayaran kedua setelah status berubah tidak mengubah transaksi dan menerima respons konflik yang sesuai.
- [ ] Refund dan restock tidak dapat diproses dua kali.
- [ ] `stock_restored_at` dan `refunded_at` hanya terisi sekali.
- [ ] Admin tidak dapat membatalkan order berstatus `processing`, `shipped`, atau `completed`.
- [ ] Seller tidak dapat membatalkan order milik seller lain.
- [ ] Jika operasi restock gagal, seluruh transaksi pembatalan rollback.
- [ ] Saat rollback pembatalan, status order tetap `pending_processing`, `stock_restored_at` tetap NULL, dan `refunded_at` tetap NULL.
- [ ] Perubahan harga produk atau penghapusan alamat tidak mengubah snapshot `order_items` dan `orders.shipping_*`.
- [ ] Checkout dengan produk yang tidak aktif atau seller yang tidak aktif gagal tanpa mengurangi stok.
- [ ] `UPDATE payments ... WHERE status = 'pending'` yang tidak mengubah satu baris pun tidak boleh diikuti perubahan order atau checkout.
- [ ] Hasil akhir request pembayaran sukses dan gagal yang berjalan bersamaan selalu konsisten.
- [ ] Refund `refunded_amount` sama dengan `orders.subtotal`, tanpa komponen lain.
- [ ] Pembatalan checkout sebelum pembayaran tidak dapat membatalkan checkout yang payment-nya sudah `paid`.
- [ ] Order dari seller berbeda dapat dibatalkan secara independen sesuai statusnya.
- [ ] Lock beberapa produk dan order menggunakan urutan `id ASC`.
- [ ] Pengujian konkurensi dilakukan pada MySQL, bukan hanya mengandalkan SQLite.
- [ ] Kegagalan transaksi dan deadlock tidak meninggalkan perubahan stok atau status parsial.

### 9.4 Kualitas dan UI

- [ ] Setiap form memiliki label, validasi, dan pesan error yang jelas.
- [ ] Loading, empty, error, dan disabled state tersedia pada interaksi yang relevan.
- [ ] Kontras teks memenuhi rasio minimal 4.5:1 jika berlaku untuk teks normal.
- [ ] Dialog dan interaksi utama dapat digunakan dengan navigasi keyboard.
- [ ] Tidak ada emoji yang digunakan sebagai ikon antarmuka.
- [ ] Semua alur transaksi memberi penanda simulasi secara jelas.
- [ ] Tidak ada fitur yang diklaim selesai tanpa implementasi dan pengujian yang dapat diverifikasi.

---

## 10. Keputusan Final Fase 1

Keputusan berikut sudah disetujui dan tidak boleh diubah tanpa persetujuan baru:

1. Laravel + MySQL, modular monolith, Blade + Tailwind, dan Breeze.
2. Satu tabel `users` menggunakan `role` dan `account_type`; kapabilitas seller dikelola melalui `seller_profiles`.
3. Produk `b2b` tidak dapat dibeli oleh pembeli yang belum terverifikasi; tidak ada fallback ke harga konsumen.
4. `min_purchase_qty` hanya berlaku untuk buyer bisnis terverifikasi pada produk `b2b` atau `both`.
5. `payments.status` hanya terdiri dari `pending`, `paid`, dan `failed`.
6. Tidak ada status `refunded` pada payment.
7. Refund dicatat per order melalui `orders.refunded_amount` dan `orders.refunded_at`.
8. `checkouts.status` hanya terdiri dari `pending_payment`, `paid`, dan `cancelled`.
9. Pembeli tidak dapat membatalkan order setelah pembayaran berhasil.
10. Pembatalan order berbayar hanya diperbolehkan dari `pending_processing`, oleh seller pemilik atau admin, melalui `OrderCancellationService`.
11. `OrderCancellationService` mengunci produk terlebih dahulu, kemudian order, serta memvalidasi ulang status setelah lock.
12. Urutan lock global transaksi multi-tabel adalah `payments → products → orders`; baris dalam setiap kelompok produk/order dikunci berdasarkan `id ASC`.
13. Refund dan restock merupakan satu transaksi atomik.
14. Snapshot harga, item, dan alamat bersifat immutable setelah checkout.
15. Harga, stok, kelayakan buyer, status produk, dan status seller divalidasi ulang saat pembayaran.
16. `recordPaymentFailure` hanya mengubah payment `pending → failed`; order dan checkout hanya dibatalkan jika perubahan tersebut berhasil dan status terkait masih valid.
17. `dummy_bills` memiliki unique constraint pada `(meter_number, period)`.
18. Produk hanya tersedia jika produk dan seller sama-sama aktif.
19. Hard delete produk dengan riwayat order dilarang melalui alur aplikasi normal.
20. Rumus refund adalah `refunded_amount = orders.subtotal`, tanpa ongkir atau pajak.
21. File `.env` asli tidak pernah diminta atau dibagikan.
22. Status dan checklist implementasi harus didasarkan pada bukti aktual, bukan hanya isi dokumen.

---

## 11. Keputusan yang Masih Terbuka

Hal-hal berikut belum final. Gunakan nilai default sampai ada keputusan baru.

1. **Auto-expire checkout `pending_payment`:** default tidak ada TTL.
2. **Pembatalan checkout oleh buyer sebelum pembayaran:** default membatalkan seluruh checkout, bukan satu order.
3. **Notifikasi pembatalan kepada buyer:** default hanya badge atau informasi pada halaman riwayat.
4. **Cakupan `activity_logs`:** opsional pada Fase 7, tanpa PII yang tidak diperlukan.
5. **Batas gambar produk:** default 5 gambar, maksimal 2 MB per gambar, format JPG/JPEG, PNG, dan WebP.
6. **Wewenang admin terhadap order berbayar yang macet:** default sama dengan seller, hanya dapat membatalkan order `pending_processing`.
7. **Versi framework:** asumsi awal Laravel 12.x, harus diverifikasi pada audit Fase 2.
8. **Pencatatan alasan penolakan pendaftaran seller:** status menggunakan `suspended`; kolom atau mekanisme pencatatan alasan akan ditentukan saat audit dan implementasi tanpa menambahkan status `rejected` ke `seller_profiles`.

Setiap perubahan keputusan final harus disetujui terlebih dahulu, lalu diperbarui secara konsisten di `PROJECT_SPEC.md`, `IMPLEMENTATION_PLAN.md`, `AI_RULES.md`, dan `PROGRESS.md` jika relevan.