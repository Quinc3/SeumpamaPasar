# PROGRESS — Integrated E-Commerce Platform

> **Terakhir diperbarui:** 27 Oktober 2026.

---

## 1. Status Umum

- **Proyek:** Integrated E-Commerce Platform
- **Pendekatan:** Pengembangan berkelanjutan (bukan dari nol); revisi scope 27 Okt 2026.
- **Fase 1 (Discovery):** ✅ selesai & disetujui.
- **Fase 2 (Fondasi):** ✅ selesai & terverifikasi.
- **R0.5 (cleanup dep):** ✅ committed `fd55f12`.
- **R1 (design system + komponen inti):** ✅ committed `dae1c89`.
- **R2 (layouts & navigation):** ⏳ sedang berjalan.
- **Revisi spec v2.0 (B2C+B2B+Member+Affiliate+Seller):** ✅ disetujui 27 Okt 2026.
- **Kode aplikasi:** ada fondasi Laravel 12 + Breeze + 16 model + 19 migration + 4 policy + 2 middleware + design system R1.
- **Tes:** `vendor\bin\phpunit` → 25/25 PASS (baseline terakhir).

---

## 2. Dokumen Proyek

Semua di `docs/`:
- `PROJECT_SPEC.md` (v2.0 — 27 Okt 2026)
- `IMPLEMENTATION_PLAN.md` (v2.0 — 27 Okt 2026)
- `AI_RULES.md` (v2.0 — 27 Okt 2026)
- `PROGRESS.md` (file ini)

---

## 3. Keputusan Bisnis Final (v2.0)

- **2 jenis perdagangan:** B2C + B2B (C2C tidak terpisah; seller individu = bagian B2C).
- **3 model:** Member, Affiliate, Seller.
- **Member:** diskon 5% flat + badge; aktivasi manual; tanpa poin/tier.
- **Affiliate:** 5% × subtotal, trigger `completed`, first-touch 30 hari, self-referral diblokir, 1 order → 1 komisi.
- **Digital services:** hapus dari scope; 3 tabel DB dibiarkan unused.
- **Stack:** tetap Laravel 12, Blade, Tailwind v3, Vite 7, Breeze, Alpine, MariaDB, PHPUnit.
- **UI:** stone base + deep teal, Figtree, Heroicons v2 via `<x-icon>`, mobile-first.

Detail lengkap: `PROJECT_SPEC.md`.

---

## 4. Kondisi Aktual Filesystem (per 23 Okt 2026, terakhir diaudit)

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

**Enums:** 11 (`UserRole`, `AccountType`, `SellerType`, `SellerStatus`, `ProductStatus`, `SalesChannel`, `CheckoutStatus`, `PaymentStatus`, `OrderStatus`, `DigitalServiceType`, `DigitalTransactionStatus`).

**Models:** 16 (User, BusinessProfile, SellerProfile, Address, Category, Product, ProductImage, WholesalePriceTier, CartItem, Checkout, Order, OrderItem, Payment, DigitalProduct, DummyBill, DigitalTransaction).

**Migrations:** 19 (3 bawaan + 16 kustom).

**Policies:** 4 (Product, Order, Checkout, DigitalTransaction).

**Middleware:** 2 (EnsureRole, EnsureSellerActive).

**Design system (R1):**
- Tokens: primary (teal 50–950), surface (stone 50–950), shadow-card/card-hover/dropdown/modal, max-w-8xl.
- Base CSS: background stone, ::selection teal, :focus-visible ring.
- Ikon: `<x-icon name="…" size="xs|sm|md|lg|xl|2xl">` — 31 Heroicons v2 Outline.
- Komponen: `primary-button`, `secondary-button`, `danger-button`, `text-input`, `input-label`, `input-error`, `card`, `alert`, `badge` (soft/solid), `status-badge`, `empty-state`, `skeleton`, `avatar`, `icon`.

**Tabel DB unused (dibiarkan):** `digital_products`, `dummy_bills`, `digital_transactions`.

**Seeder:** `AdminUserSeeder`, `CategorySeeder`, `DigitalProductSeeder` (data usang, dibiarkan), `DummyBillSeeder` (data usang, dibiarkan), `DemoUsersSeeder`.

---

## 5. Status Fase

| Fase | Status |
|---|---|
| 1 | ✅ |
| 2 | ✅ |
| R0.5 | ✅ |
| R1 | ✅ |
| **R2** | **⏳ aktif** |
| R3 | ⬜ |
| R4 | ⬜ |
| 3 | ⬜ |
| 4 | ⬜ |
| 5 (Affiliate) | ⬜ |
| 6 (Member) | ⬜ |
| 7 (Analytics) | ⬜ |
| 8 (QA) | ⬜ |

---

## 6. Riwayat

**Fase 1** — Planning & spec (disetujui).
**Fase 2** — Fondasi: enum, model, migration, seeder, middleware, policy, routes, dashboard placeholder.
**R0.5** — Hapus `@tailwindcss/vite` (unused). Commit `fd55f12`.
**R1** — Design tokens, base CSS, 31 ikon Heroicons, restyle Breeze components, komponen inti (`card`, `alert`, `badge`, `status-badge`, `empty-state`, `skeleton`, `avatar`, `icon`). Commit `dae1c89`.
**27 Okt 2026** — Revisi scope v2.0: B2C+B2B+Member+Affiliate+Seller. Digital services dihapus. 4 dokumen disinkronkan.

---

## 7. Yang Sedang Dikerjakan (R2)

- File R2 dasar sudah dikirim: 4 layout (`guest`, `app`, `admin`, `seller`), 5 komponen nav (`nav-item`, `sidebar-nav`, `bottom-nav`, `drawer`, `user-menu`).
- **Pending:** tambah menu Affiliate + halaman pengaturan multi-profil ringkas.
- Setelah R2 selesai: commit, lanjut R3.

---

## 8. Langkah Berikutnya

1. Paste 4 dokumen v2.0 (dari pesan ini).
2. Konfirmasi `git status` singkat.
3. R2 final (tambah menu Affiliate).
4. R3 → R4 → Fase 3 → 4 → 5 → 6 → 7 → 8.

---

## 9. Aturan Pembaruan Progres

- Update di akhir milestone, bukan tiap file.
- Catat: perubahan, hasil tes nyata, masalah tersisa, satu langkah berikutnya.
- Jangan catat kredensial/`.env`.
- Jangan timpa riwayat; tambahkan.