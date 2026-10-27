# IMPLEMENTATION_PLAN — Integrated E-Commerce Platform

> **Versi:** 2.0 — Revisi 27 Oktober 2026.
> **Fase aktif:** R2 (Layouts & Navigation).
> **Prinsip:** batch delivery, tanpa approval per-file. Approval hanya di milestone & perubahan destruktif.

---

## 1. Prinsip Kerja

1. Baca `PROJECT_SPEC.md`, `AI_RULES.md`, `PROGRESS.md` sebelum tugas.
2. Audit file relevan (bukan seluruh proyek).
3. Perubahan minimal; jangan ganti kode yang berfungsi.
4. Tes: baseline + tes relevan per perubahan; full suite di milestone.
5. Jangan jalanin `migrate:fresh`/`db:wipe` tanpa approval.
6. Update `PROGRESS.md` di akhir milestone, bukan setiap file.

---

## 2. Urutan Fase

| Fase | Nama | Status |
|---|---|---|
| 1 | Discovery & Planning | ✅ |
| 2 | Fondasi (auth, skema dasar, role) | ✅ |
| R0–R1 | UI redesign: tokens, komponen inti | ✅ |
| **R2** | **Layouts & navigation** | **⏳ aktif** |
| R3 | Auth polish + halaman pengaturan multi-profil | ⬜ |
| R4 | Dashboard shell 4 workspace | ⬜ |
| 3 | Marketplace B2C (katalog, cart, checkout, order, refund) | ⬜ |
| 4 | B2B (verifikasi bisnis, tier, min qty) | ⬜ |
| 5 | Affiliate (referral, attribution, komisi) | ⬜ |
| 6 | Member (aktivasi, diskon) | ⬜ |
| 7 | Dashboard analytics & laporan | ⬜ |
| 8 | QA & docs final | ⬜ |

---

## 3. Fase R2 — Layouts & Navigation

**Tujuan:** 4 layout utama + nav desktop/mobile.

**Scope:**
- `layouts/guest`, `layouts/app`, `layouts/admin`, `layouts/seller` — sudah dikirim (dari sesi sebelumnya, tanpa menu Affiliate).
- Tambah menu Affiliate di `x-bottom-nav` (buyer mobile), `layouts/admin/seller` (nav sidebar), dan `x-user-menu` (dropdown).
- Nav mobile: bottom nav buyer + drawer untuk admin/seller/affiliate.
- Halaman pengaturan multi-profil: ringkas (link ke /akun/member, /akun/afiliasi, /akun/bisnis) — belum implementasi form penuh.

**Kriteria selesai:**
- [ ] `/login`, `/admin`, `/toko`, `/akun`, `/afiliasi` render dengan layout benar.
- [ ] Mobile 375 px: drawer berfungsi, bottom nav tampil, tidak overflow.
- [ ] `vendor\bin\phpunit` → 25/25 PASS.
- [ ] Commit R2.

---

## 4. Fase R3 — Auth + Pengaturan Multi-Profil

**Scope:**
- Polish 6 halaman auth (login, register, forgot, reset, verify, confirm).
- Polish 3 partial profile (edit info, update password, delete user).
- Halaman `/akun` — tampil kapabilitas user (member, seller, affiliate, bisnis) + tombol aktivasi.
- Halaman `/akun/member` — aktivasi + info benefit.
- Halaman `/akun/afiliasi` — aktivasi + link referral + saldo komisi.
- Form pengajuan seller profile.
- Form pengajuan business profile.

**Kriteria:**
- [ ] Semua auth page pakai layout guest baru.
- [ ] `/akun` menampilkan status kapabilitas user nyata dari DB.
- [ ] Aktivasi member/affiliate bekerja (`users.member_since`, `affiliate_profiles` terbuat).
- [ ] Test PASS + commit.

---

## 5. Fase R4 — Dashboard Shell

**Scope:** Kerangka dashboard 4 workspace dengan data nyata dari DB, tanpa fitur interaktif penuh (itu tugas fase berikutnya).

- `/admin` — statistik ringkas (users, sellers, products, orders, commissions).
- `/toko` — statistik seller (products, orders pending, sales).
- `/akun` — ringkasan buyer/member (orders, digital transactions [tabel unused], alamat).
- `/afiliasi` — statistik affiliate (clicks, conversions, commissions).

**Kriteria:** semua widget angka dari query DB nyata, tidak hardcoded. Test + commit.

---

## 6. Fase 3 — Marketplace B2C

**Scope:**
- Katalog publik: search, filter, sort, pagination.
- Halaman detail produk.
- Keranjang.
- Checkout + alamat (snapshot).
- `CheckoutService` (multi-seller, member diskon, referral attribution).
- `PaymentService` / `recordPaymentFailure`.
- `OrderCancellationService`.
- `CommissionService` (dipicu saat completed).
- `OrderStatusService` (transisi `processing → shipped → completed`).
- Riwayat & detail pesanan (buyer + seller).
- Moderasi produk (admin).
- CRUD kategori (admin).

**Kriteria:** alur beli → bayar → proses → selesai berjalan end-to-end. Feature test untuk checkout, payment gagal, refund, member discount, referral attribution. Test PASS.

---

## 7. Fase 4 — B2B

**Scope:**
- `business_profiles` form + verifikasi admin.
- Produk `b2b`/`both` (seller bisnis).
- `wholesale_price_tiers` CRUD.
- `PriceResolver` final (tier + min qty).
- Filter katalog B2B (hanya verified).
- Validasi checkout B2B.

**Kriteria:** produk `b2b` 404 untuk non-verified; `min_purchase_qty` berlaku; tier resolusi benar. Test PASS.

---

## 8. Fase 5 — Affiliate

**Scope:**
- Migration `affiliate_profiles`, `referrals`, `commissions`.
- Model + Policy.
- Aktivasi affiliate (via `/akun/afiliasi` di R3, di sini diimplementasi penuh).
- Referral link middleware/cookie (30 hari, first-touch).
- Atribusi checkout (create `referrals`).
- `CommissionService` (award + guards).
- Dashboard affiliate (link, komisi, performa).
- Admin: approve/suspend affiliate, lihat semua komisi.

**Kriteria:**
- [ ] Referral attribution bekerja (cookie + session).
- [ ] Komisi dibuat saat order completed.
- [ ] Self-referral diblokir.
- [ ] Unique `order_id` di `commissions` mencegah duplikasi.
- [ ] Test PASS + commit.

---

## 9. Fase 6 — Member

**Scope:**
- Migration kolom `users.member_since`.
- Aktivasi member (via `/akun/member`).
- Diskon 5% di `CheckoutService` (distribusi proporsional ke `orders.subtotal`).
- Badge member di top bar + `/akun`.
- Riwayat transaksi member.

**Kriteria:**
- [ ] Member aktif dapat diskon 5% di checkout.
- [ ] Non-member tidak dapat diskon.
- [ ] Badge tampil konsisten.
- [ ] Test PASS + commit.

---

## 10. Fase 7 — Dashboard Analytics & Laporan

**Scope:**
- Laporan penjualan admin (filter tanggal, seller, produk).
- Laporan seller (penjualan toko sendiri).
- Laporan affiliate (komisi per bulan).
- Grafik minimal (angka + tabel, tanpa grafik kompleks).

**Kriteria:** angka sesuai DB. Test PASS.

---

## 11. Fase 8 — QA & Docs

**Scope:**
- Audit otorisasi (setiap policy, test positif & negatif).
- Uji seluruh acceptance criteria di `PROJECT_SPEC.md §8`.
- README lengkap (install, seed, test).
- Dokumentasi batasan simulasi.
- Commit final.

**Kriteria:** semua acceptance criteria lulus atau dilaporkan sebagai known issue. Demo end-to-end dari DB baru.

---

## 12. Aturan Approval

- Approval wajib: milestone akhir, perubahan scope bisnis, perubahan destruktif (drop tabel, reset DB), penambahan dependensi.
- **Tidak perlu approval** untuk: penyelesaian file dalam scope fase aktif, fix bug dalam scope, penyesuaian UI dalam design system yang sudah disetujui.
- Bila ragu, gunakan default paling sederhana & dokumentasikan asumsi.