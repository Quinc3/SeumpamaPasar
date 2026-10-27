# PROJECT_SPEC — Integrated E-Commerce Platform

> **Versi:** 2.0 — Revisi 27 Oktober 2026 (scope: B2C + B2B + Member + Affiliate + Seller)
> **Status:** Sumber kebenaran untuk Fase 2 dan seterusnya.
> **Fase 1:** selesai & disetujui. **Revisi v2.0:** disetujui 27 Oktober 2026.
> **Kondisi kode:** sudah ada fondasi (Laravel 12, Breeze, 16 model, 19 migration). Detail: `PROGRESS.md`.

---

## 1. Tujuan dan Ruang Lingkup

Aplikasi e-commerce terintegrasi untuk proyek akademik dengan **dua jenis perdagangan** dan **tiga model bisnis**:

### Jenis perdagangan
1. **B2C** — retail kepada konsumen.
2. **B2B** — grosir/bisnis dengan tier harga & minimum order quantity.

### Model bisnis
1. **Member** — pelanggan dengan benefit diskon simulasi.
2. **Affiliate** — pemasar dengan referral link dan komisi simulasi.
3. **Seller** — penjual di marketplace (individu atau bisnis).

Reseller/dropship = pengembangan lanjutan, **di luar scope**.

### Ruang lingkup
- Katalog, pencarian, keranjang, checkout multi-seller.
- B2B tier pricing & minimum quantity.
- Pesanan, stok, pembayaran simulasi, pembatalan & refund simulasi.
- Akun pelanggan, membership, affiliate, seller.
- Referral & commission tracking.
- Moderasi produk & verifikasi bisnis oleh admin.
- Dashboard per role.

### Di luar ruang lingkup
- Payment gateway / uang sungguhan.
- Integrasi PPOB / API eksternal.
- Microservices.
- Perhitungan ongkir, pajak, biaya pembayaran.
- Multi-currency.
- **Layanan digital** (tagihan, token, pulsa, data) — **dihapus dari scope**, tabel DB dibiarkan (unused).
- C2C sebagai jenis perdagangan terpisah — dijembatani oleh `seller_type=individual` di dalam B2C.
- Reseller / dropship.

### Batasan simulasi
- Semua pembayaran & komisi berlabel **SIMULASI**.
- Tidak menyimpan kredensial pembayaran.
- Refund & komisi adalah pencatatan internal, bukan uang nyata.

---

## 2. Stack Teknologi

| Aspek | Nilai |
|---|---|
| PHP | 8.2.12 |
| Framework | Laravel 12.70.1 |
| Auth starter | Breeze 2.4.2 |
| Frontend | Blade + Tailwind CSS v3.4.19 + Vite 7 |
| JS interaktif | Alpine 3.4.2 (sudah ada) |
| Database | MariaDB 10.4.32, DB `e_commerce` |
| Ikon | Heroicons v2 Outline — via `<x-icon>` (custom SVG, lisensi MIT) |
| Testing | PHPUnit 11.5 |
| Deployment | lokal, `php artisan serve` |

**Dilarang:** menambah React/Next/microservices/API terpisah/repository pattern/library baru tanpa kebutuhan jelas. Jangan membaca `.env` asli. Jangan menjalankan `migrate:fresh`/`db:wipe` tanpa approval eksplisit.

---

## 3. Struktur Pengguna

### 3.1 Atribut identity
- `users.role` ∈ {`admin`, `user`}.
- `users.account_type` ∈ {`personal`, `business`}.
- `users.member_since` (nullable timestamp) — diisi saat user aktifkan member.
- `business_profiles.verified_at` (nullable) — verifikasi akun bisnis.
- `seller_profiles.seller_type` ∈ {`individual`, `business`}.
- `seller_profiles.status` ∈ {`pending`, `active`, `suspended`}.
- `affiliate_profiles.status` ∈ {`active`, `suspended`}.

### 3.2 Kapabilitas paralel (bukan mutually exclusive)
Satu user **boleh memiliki beberapa profil sekaligus**:
- User identity selalu ada (registrasi).
- `business_profiles` (opsional) → membuka akses B2B.
- `seller_profiles` (opsional) → membuka akses toko.
- `affiliate_profiles` (opsional) → membuka referral.
- `users.member_since` (opsional) → benefit member.

**Tidak ada role baru** di `users.role`. Kapabilitas = keberadaan profil + status.

### 3.3 Matriks otorisasi

| Kapabilitas | Admin | Buyer (personal) | Business buyer | Member | Affiliate | Seller | Seller bisnis |
|---|---|---|---|---|---|---|---|
| Lihat katalog publik | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Harga grosir & tier | – | – | ✓ | – | – | – | – |
| Kelola profil sendiri | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Kelola produk sendiri | – | – | – | – | – | ✓ | ✓ |
| Set tier harga grosir | – | – | – | – | – | – | ✓ |
| Kelola pesanan sebagai seller | – | – | – | – | – | ✓ (own) | ✓ (own) |
| Batalkan order `pending_processing` | ✓ (all) | – | – | – | – | ✓ (own) | ✓ (own) |
| Moderasi produk | ✓ | – | – | – | – | – | – |
| Verifikasi bisnis | ✓ | – | – | – | – | – | – |
| Approve/suspend seller | ✓ | – | – | – | – | – | – |
| Approve/suspend affiliate | ✓ | – | – | – | – | – | – |
| Gunakan referral | – | – | – | – | ✓ | – | – |
| Lihat komisi sendiri | – | – | – | – | ✓ | – | – |
| Lihat semua komisi | ✓ | – | – | – | – | – | – |
| Lihat benefit member | – | – | – | ✓ | – | – | – |

Semua otorisasi server-side via Policy + middleware.

---

## 4. Aturan Bisnis — B2C, B2B, Member, Affiliate, Seller

### 4.1 Sales channel & seller type

| `seller_type` | `sales_channel` yang boleh |
|---|---|
| `individual` | hanya `b2c` |
| `business` | `b2c`, `b2b`, `both` |

`seller_type=business` **wajib** memiliki `business_profiles.verified_at ≠ NULL`.

### 4.2 Matriks pembelian

| `sales_channel` | Pembeli personal | Bisnis belum verified | Bisnis verified |
|---|---|---|---|
| `b2c` | Boleh, harga dasar | Boleh, harga dasar | Boleh, harga dasar |
| `b2b` | **Ditolak** (404 di URL) | **Ditolak** (404) | Boleh, min qty berlaku |
| `both` | Boleh, harga dasar | Boleh, harga dasar | Boleh, min qty berlaku |

Tidak ada fallback dari `b2b` ke harga konsumen.

### 4.3 Harga & kuantitas
- `min_purchase_qty` hanya untuk pembeli bisnis terverifikasi pada produk `b2b`/`both`.
- Resolusi harga: tier dengan `min_quantity` terbesar yang `≤ qty`; jika tidak ada, `products.price`.
- Tier wajib: `min_quantity ≥ 1`, `price > 0`, `price < products.price`.

### 4.4 Member
- Aktivasi: user membuka `/akun/member` → klik "Aktifkan Member" → `users.member_since = now()`.
- Benefit: **diskon 5%** otomatis di subtotal checkout (per `checkouts`, bukan per order).
- Badge "Member" tampil di top bar buyer & `/akun`.
- Diskon dihitung di `CheckoutService`, bukan di UI.
- Member tetap bisa membeli sebagai personal atau business (dual-capability).
- **Tidak ada** sistem poin, tier berjenjang, atau kartu member fisik.

### 4.5 Affiliate

**Profil:**
- 1:1 dengan `users`. Dibuat saat user klik "Aktifkan Affiliate" di `/akun`.
- `referral_code` (unique) — 8 karakter alfanumerik uppercase, dibuat otomatis.
- Default status: `active`.

**Atribusi:**
- **First-touch**, window **30 hari**.
- Saat visitor baru mendarat dengan `?ref=CODE`, simpan di session (`affiliate_ref_code`) + cookie (`affiliate_ref_code`, 30 hari).
- Saat checkout, `referrals` dibuat dengan:
  - `affiliate_id`, `referred_user_id` (nullable jika guest → jadi null), `referral_code`, `session_id`, `landed_at`.
- **Self-referral diblokir:** jika `buyer_id === affiliate_profiles.user_id` → tidak buat referral.
- **Duplikasi:** 1 checkout hanya di-atribusikan ke 1 affiliate (yang tercatat di session).
- **Override:** klik link affiliate baru dalam window overwrite kode lama (first-touch yang berlaku saat window aktif).

**Komisi:**
- Rate: **5% × orders.subtotal** (setelah diskon member jika ada, sebelum refund).
- Trigger: saat `orders.status = completed`.
- Buat 1 baris di `commissions` per order × affiliate (unique constraint).
- Status komisi: `pending` (order belum completed) → `approved` (order completed) → `cancelled` (order cancelled sebelum completed).
- Bila order `cancelled` setelah `completed` (tidak mungkin per aturan) → tidak ada jalur.
- Refund (order dibatalkan di `pending_processing`) sebelum completed → tidak ada komisi.

**Anti-fraud tambahan:**
- Order yang dibuat oleh affiliate sendiri tidak dihitung.
- Order dengan email pembeli sama dengan email affiliate tidak dihitung.
- Semua guard di `CommissionService`, bukan hanya di UI.

---

## 5. Database

### 5.1 Tabel existing (dari Fase 2)
`users`, `business_profiles`, `seller_profiles`, `addresses`, `categories`, `products`, `product_images`, `wholesale_price_tiers`, `cart_items`, `checkouts`, `orders`, `order_items`, `payments`, `digital_products`, `dummy_bills`, `digital_transactions`.

**Tabel digital_* dibiarkan** (tidak di-drop) — unused, tidak diimplementasikan.

### 5.2 Migration baru (Fase 5 - Affiliate, Fase 6 - Member)

**`affiliate_profiles`**
- `id`
- `user_id` (FK unique → `users`, restrictOnDelete)
- `referral_code` (unique, 8 char)
- `status` enum(`active`, `suspended`)
- timestamps

**`referrals`**
- `id`
- `affiliate_id` (FK → `affiliate_profiles`)
- `referred_user_id` (FK nullable → `users`)
- `referral_code` (string)
- `session_id` (string)
- `landed_at` (timestamp)
- `checkout_id` (FK nullable → `checkouts`)
- timestamps
- index `(affiliate_id, created_at)`, index `referred_user_id`

**`commissions`**
- `id`
- `affiliate_id` (FK → `affiliate_profiles`)
- `order_id` (FK unique → `orders`)
- `amount` decimal(15,2)
- `status` enum(`pending`, `approved`, `cancelled`)
- `approved_at` (nullable timestamp)
- `cancelled_at` (nullable timestamp)
- `cancel_reason` (nullable string)
- timestamps
- index `(affiliate_id, status)`

**`users`** — tambah kolom `member_since` (nullable timestamp).

### 5.3 Snapshot & constraint
- Snapshot `order_items` dan `orders.shipping_*` tetap (tidak berubah).
- FK deletion policy: `restrictOnDelete` default; `commissions.order_id` restrict; `referrals.checkout_id` `nullOnDelete`.
- Money: `DECIMAL(15,2)`, tidak floating-point.

---

## 6. Alur Transaksi

### 6.1 Checkout
1. Buyer menambahkan produk (keranjang).
2. Sistem memvalidasi kelayakan pembeli (B2B), kanal, `min_purchase_qty`.
3. Sistem menghitung ulang harga per item (retail / tier).
4. Sistem menerapkan **diskon member 5%** jika buyer aktif member:
   - Diskon dihitung dari subtotal checkout.
   - Diskon didistribusikan proporsional ke `orders.subtotal` tiap seller.
5. Sistem memeriksa stok (soft), alamat (wajib ada).
6. Sistem membuat `checkouts`, N `orders`, `order_items` dalam 1 transaksi DB.
7. Snapshot: harga, nama, alamat.
8. `payments` dibuat dengan status `pending`.
9. **Jika ada referral aktif di session:** catat `referrals` untuk checkout ini (1 affiliate per checkout).

### 6.2 Pembayaran simulasi
- Urutan lock global: `payments → products (id ASC) → orders (id ASC)`.
- Idempotensi: request kedua pada payment yang sudah diproses → `409 Conflict`.
- Sukses: decrement stok, `payments.status=paid`, semua `orders=pending_processing`, `checkouts.status=paid`, `orders.stock_deducted_at=now()`.
- Gagal: rollback, lalu `recordPaymentFailure` (transaksi terpisah), `payments=failed`, `orders=cancelled`, `checkouts=cancelled`.
- Validasi ulang: harga produk harus sama dengan snapshot, stok cukup, produk & seller aktif, kelayakan buyer masih valid.

### 6.3 Pembatalan & refund (tidak berubah dari Fase 1)
- `OrderCancellationService` — penjual pemilik atau admin.
- Lock produk dulu, lalu order; urutan konsisten.
- Refund & restock atomik.
- `refunded_amount = orders.subtotal`.
- `payments.status` tetap `paid`; `checkouts.status` tetap `paid`.
- **Efek ke affiliate:** jika order `cancelled` (dari `pending_processing`), `commissions.status=cancelled` (jika sudah ada row) — belum ada karena komisi baru dibuat saat `completed`.
- Bila order `cancelled` setelah `completed` (tidak mungkin per aturan) → **tidak ada** jalur — ini justru alasan aturan pembatalan dibatasi.

### 6.4 Alur komisi
- Saat order berpindah ke `completed` (pembeli konfirmasi terima):
  - `CommissionService::awardForOrder($order)` dijalankan **dalam transaksi DB**.
  - Cek: `orders.status = completed` (dilakukan pembeli), referral ada, bukan self-referral, bukan email sama.
  - Buat `commissions` dengan `amount = subtotal × 0.05`, `status=approved`, `approved_at=now()`.
  - Unique constraint `commissions.order_id` mencegah duplikasi.

### 6.5 Status transaksi

**`orders.status`** (tidak berubah dari Fase 1):
`pending_payment → pending_processing → processing → shipped → completed` atau `pending_processing → cancelled`.

**`commissions.status`:**
`pending → approved` (saat order completed) atau `pending → cancelled` (saat order cancelled).

**`affiliate_profiles.status`:** `active` ⇄ `suspended` (admin).

**`users.member_since`:** NULL → timestamp (aktif). Tidak bisa dibatalkan via UI (bisa manual via DB oleh admin).

---

## 7. Keamanan

Wajib:
- Validasi server-side (FormRequest).
- Otorisasi via Policy + middleware.
- IDOR prevention (cek kepemilikan).
- CSRF, escaping Blade, mass assignment eksplisit.
- Validasi upload gambar (mime, size, ekstensi).
- Rate limit login & pembayaran.
- Transaksi DB untuk stok/pembayaran/refund/komisi.
- **Self-referral & fraud guard di `CommissionService`.**
- Tidak menyimpan PII di log.
- Jangan minta/baca `.env` asli.

---

## 8. Acceptance Criteria

### 8.1 Fungsional dasar (dari Fase 1)
- [ ] Registrasi & auth buyer.
- [ ] Pengajuan seller profile & business profile.
- [ ] Verifikasi bisnis oleh admin.
- [ ] Moderasi produk (`pending_review → active/rejected`).
- [ ] Checkout multi-seller (N orders).
- [ ] Pembayaran sukses/gagal.
- [ ] Pembatalan & refund dengan `refunded_amount = subtotal`.
- [ ] Double-payment → `409`, stok tidak berubah dua kali.

### 8.2 B2B
- [ ] Produk `b2b` tidak tampil untuk non-verifikasi.
- [ ] `min_purchase_qty` hanya untuk bisnis terverifikasi.
- [ ] Tier harga terpilih benar pada batas.

### 8.3 Member
- [ ] Aktivasi member via `/akun/member`.
- [ ] Diskon 5% teraplikasi di checkout member.
- [ ] Badge "Member" tampil di UI.
- [ ] Non-member tidak mendapat diskon.

### 8.4 Affiliate
- [ ] Aktivasi affiliate → dapat referral code.
- [ ] Klik link dengan `?ref=CODE` menyimpan atribusi (session+cookie).
- [ ] Checkout dengan referral mencatat `referrals`.
- [ ] Order completed → komisi 5% dibuat otomatis.
- [ ] Order cancelled sebelum completed → tidak ada komisi (atau `cancelled` jika sudah ada).
- [ ] Self-referral diblokir (order tidak dikomisikan).
- [ ] Duplikasi komisi untuk 1 order ditolak (unique constraint).
- [ ] Dashboard affiliate menampilkan komisi nyata dari DB.

### 8.5 UX
- [ ] Mobile-first: bottom nav buyer, drawer admin/seller.
- [ ] Tidak ada horizontal overflow di 375 px.
- [ ] Loading, empty, error, success state.
- [ ] Ikon konsisten (Heroicons).

---

## 9. Keputusan Final (disetujui 23 Okt 2026, direvisi 27 Okt 2026)

Fase 1 (dari versi sebelumnya) tetap berlaku untuk backend core (checkout, payment, refund, urutan lock, snapshot). Tambahan v2.0:

23. **2 jenis perdagangan:** B2C + B2B. C2C sebagai jenis terpisah **dihapus**; seller individu diperlakukan sebagai bagian B2C.
24. **3 model bisnis:** Member, Affiliate, Seller.
25. **Digital services** (tagihan, token, pulsa, data) **dihapus dari scope**; 3 tabel DB dibiarkan unused.
26. **Member:** diskon flat 5%, tanpa poin/tier berjenjang.
27. **Affiliate:** komisi 5% × subtotal, trigger `completed`, first-touch 30 hari, self-referral diblokir, 1 order → 1 komisi.
28. Tabel baru: `affiliate_profiles`, `referrals`, `commissions`; kolom baru: `users.member_since`.

---

## 10. Keputusan yang Masih Terbuka

1. **Auto-expire checkout `pending_payment`:** default tidak ada TTL.
2. **Pembatalan checkout sebelum bayar:** default membatalkan seluruh checkout.
3. **Notifikasi pembatalan:** default badge di riwayat.
4. **Batas gambar produk:** default 5 gambar, max 2 MB.
5. **Wewenang admin force-cancel:** default hanya `pending_processing`.
6. **Batas komisi affiliate:** default tidak ada (tanpa cap). Bisa ditambah bila ada kebutuhan.
7. **Membership lifetime:** default seumur akun. Bisa ditambah expiry bila perlu.
8. **Pencatatan alasan penolakan seller:** status `suspended`, alasan di luar enum.