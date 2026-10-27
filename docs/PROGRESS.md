# PROGRESS — Integrated E-Commerce Platform

> **Terakhir diperbarui:** 27 Oktober 2026.
> **Status proyek:** SEMUA FASE INTI SELESAI + UI polish selesai.

---

## 1. Status Umum

- **Proyek:** Integrated E-Commerce Platform
- **Pendekatan:** Pengembangan berkelanjutan.
- **Fase 1–8:** ✅ selesai & terverifikasi.
- **UI polish (Sesi A + B):** ✅ selesai.
- **Tes:** `vendor\bin\phpunit` → 32/32 PASS.
- **Kode aplikasi:** lengkap untuk demo akademik.

---

## 2. Dokumen Proyek

Semua di `docs/`:
- `PROJECT_SPEC.md` (v2.0)
- `IMPLEMENTATION_PLAN.md` (v2.0)
- `AI_RULES.md` (v2.0)
- `PROGRESS.md` (file ini)

Plus `README.md` di root untuk instruksi instalasi.

---

## 3. Keputusan Bisnis Final

- **2 jenis perdagangan:** B2C + B2B.
- **3 model:** Member, Affiliate, Seller.
- **Member:** diskon 5% flat + badge.
- **Affiliate:** 5% × subtotal, trigger `completed`, first-touch 30 hari, self-referral diblokir.
- **Digital services:** dihapus dari scope; 3 tabel DB dibiarkan unused.
- **Stack:** Laravel 12, Blade, Tailwind v3, Vite 7, Breeze, Alpine, MariaDB, PHPUnit.
- **UI:** stone base + deep teal, Figtree, Heroicons v2 via `<x-icon>`, mobile-first.

---

## 4. Kondisi Aktual Filesystem

| Item | Nilai |
|---|---|
| PHP | 8.2.12 |
| Laravel | 12.70.1 |
| Breeze | 2.4.2 |
| Tailwind | 3.4.19 |
| Vite | 7.3.7 |
| Alpine | 3.17.5 |
| MariaDB | 10.4.32, DB `e_commerce` |
| PHPUnit | 11.5.57 |
| Branch Git | `main` |

**Enums:** 12 (`UserRole`, `AccountType`, `SellerType`, `SellerStatus`, `ProductStatus`, `SalesChannel`, `CheckoutStatus`, `PaymentStatus`, `OrderStatus`, `CommissionStatus`, `DigitalServiceType`, `DigitalTransactionStatus`).

**Models:** 19.

**Migrations:** 23 (3 bawaan + 20 kustom).

**Policies:** 4.

**Middleware:** 3 (`EnsureRole`, `EnsureSellerActive`, `CaptureReferral`).

**Layouts (aktif):** `app`, `guest`, `admin`. Layout lama `seller` & `affiliate` dibiarkan sebagai arsip.

**Design system:** design tokens primary/surface, 31 ikon Heroicons v2, 20+ komponen Blade.

**Seeder:** `AdminUserSeeder`, `CategorySeeder`, `DemoUsersSeeder`, `CatalogDemoSeeder`.

**Tabel DB unused:** `digital_products`, `dummy_bills`, `digital_transactions`.

---

## 5. Status Fase

| Fase | Status |
|---|---|
| 1–8 | ✅ |
| R0.5–R3 (UI redesign) | ✅ |
| Sesi A (unify layout + hub + password toggle + copy) | ✅ |
| Sesi B (beranda Shopee-style) | ✅ |

---

## 6. Riwayat Commit (ringkas)

- `1fe4e49` — Initial Laravel 12 + Breeze skeleton
- `fbedc11` — Fase 2: fondasi
- `dae1c89` — R1: design tokens, ikon, komponen inti
- `cca6581` — R2: layouts & navigation
- `9b9fba4` — R3: Member activation
- `b922efe` — Fase 3.1: catalog + cart
- `19c8fbe` — Fase 3.2A: checkout + payment + address
- `f1310e1` — Fase 3.2B: order history + status
- `ec2e225` — Fase 4B: seller product CRUD + moderation
- *(Fase 5A/5B/7/8 — commit masing-masing)*
- *(Sesi A + B — commit final)*

---

## 7. Acceptance Criteria — Status

### Fungsional ✅
- Registrasi & auth buyer ✓
- Seller profile & business profile ✓
- Verifikasi bisnis oleh admin ✓
- Moderasi produk ✓
- Checkout multi-seller ✓
- Pembayaran sukses/gagal ✓
- Refund simulasi ✓
- Double-payment guard ✓

### B2B ✅
- Produk `b2b` tersembunyi untuk non-terverifikasi ✓
- `min_purchase_qty` berlaku untuk terverifikasi ✓
- Tier harga benar pada batas ✓
- Seller individual ditolak server-side untuk B2B ✓

### Member ✅
- Aktivasi via `/akun` ✓
- Diskon 5% di cart/checkout ✓
- Badge "Member" ✓

### Affiliate ✅
- Aktivasi + kode referral ✓
- Tracking via `?ref=` (session + cookie 30 hari) ✓
- Atribusi checkout ✓
- Komisi 5% saat `completed` ✓
- Self-referral diblokir ✓

### UX ✅
- Header Shopee-style (search besar, cart badge) ✓
- Hub universal `/dashboard` dengan kartu kapabilitas kondisional ✓
- Workspace sub-nav (toko/afiliasi) di layout unified ✓
- Password toggle (icon mata) di semua form ✓
- Copy button untuk kode/link afiliasi ✓
- Beranda dengan hero, kategori grid, produk unggulan/terlaris/terbaru ✓
- Product card dengan rating, terjual, badge diskon ✓
- Mobile-first: bottom nav buyer, drawer admin ✓

### Keamanan ✅
- Auth + role + seller.active middleware ✓
- Policy server-side ✓
- `$fillable` + `forceFill` untuk status ✓
- CSRF Laravel default ✓
- Self-referral & duplikasi komisi diblokir ✓

---

## 8. Batasan Simulasi (dinyatakan di UI)

- Banner "Mode Simulasi" di semua halaman transaksi.
- Label "Pengembalian dana (simulasi)" saat refund.
- Tidak ada integrasi PPOB, payment gateway, uang sungguhan.
- Tabel digital services tidak dipakai.
- `discount_price` & `rating` di produk adalah **kosmetik display** — tidak memengaruhi harga transaksi. Harga jual tetap `products.price` (B2C) atau tier (B2B).

---

## 9. Pengembangan Lanjutan (opsional, di luar scope)

- Diskon benar-benar berlaku di transaksi (bukan hanya display).
- Export laporan CSV/PDF.
- Advanced search + filter harga/rating.
- Multi-image produk.
- Notifikasi email order.
- Migrasi ke Tailwind v4.
- Hapus tabel `digital_*` (butuh approval eksplisit + migration baru).
- Wishlist, review produk, dsb.

---

## 10. Aturan Pembaruan Progres

- Update di akhir milestone.
- Catat: perubahan, hasil tes nyata, masalah tersisa, satu langkah berikutnya.
- Jangan catat kredensial/`.env`.
- Jangan timpa riwayat; tambahkan.