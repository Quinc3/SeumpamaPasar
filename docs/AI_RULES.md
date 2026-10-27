# AI_RULES — Aturan Kerja AI Coding Agent

> **Versi:** 2.0 — 27 Oktober 2026.
> Empat RULE inti + workflow hemat token.

---

## RULE 1 — Audit Berbasis Kebutuhan

Periksa file & dependensi yang **relevan** sebelum perubahan. Jangan baca ulang seluruh proyek. Audit ringkas: nama file, struktur folder, status git, konfigurasi yang akan diubah.

## RULE 2 — Jujur & Terverifikasi

Jangan klaim file/fitur/tes selesai tanpa bukti. Bedakan **fakta** (dari output), **asumsi** (dari konteks), dan **belum diperiksa**. Jangan mengarang output terminal.

## RULE 3 — Minimal & Terarah

- Hindari refactor besar tanpa kebutuhan.
- Jangan buat file baru yang tidak dipakai.
- Jangan tambah package tanpa manfaat jelas.
- Jangan ubah di luar scope fase aktif.
- Jangan menimpa pekerjaan pengguna.
- Kalau harus ubah banyak file, kelompokkan per tujuan.

## RULE 4 — Dokumentasi & Approval Proporsional

- Perbarui `PROGRESS.md` **di akhir milestone**, bukan setiap file.
- Approval **wajib** untuk: perubahan scope bisnis, perubahan destruktif (drop tabel, reset DB, hapus source), milestone akhir fase.
- Approval **tidak wajib** untuk: file individual dalam scope yang sudah disetujui, fix bug dalam scope, penyelesaian UI dalam design system yang disetujui.
- Bila ragu antara minta approval atau tidak: pilih yang **tidak** meminta approval untuk pekerjaan kecil dalam scope, dan **minta** untuk yang menyentuh keamanan/transaksi/data.

---

## Workflow Hemat Token

1. **Jangan minta konfirmasi per-file** dalam scope yang sudah disetujui.
2. **Audit ringkas** — hanya file yang relevan.
3. **Jangan kirim ulang dokumen panjang** jika tidak ada perubahan; kirim diff/section yang berubah.
4. **Ringkas laporan**: temuan, perubahan, hasil tes, risiko — poin-poin.
5. **Batch delivery** — kirim semua file fase dalam 1-2 pesan, bukan bertahap per file.
6. **Tes minimal yang cukup**: baseline + tes relevan. Full suite di milestone.
7. **Jangan buat route/komponen/helper/dokumentasi** yang tidak dipakai.
8. **Jangan tanya klarifikasi** jika keputusan sudah ada di spec.
9. **Ketidakpastian kecil**: default paling sederhana + dokumentasi asumsi.
10. **Ketidakpastian besar** (keamanan, transaksi, data, destruktif): berhenti & tanya.
11. **Jangan mengarang output terminal.**
12. **Tanpa akses terminal**: minta hanya output yang perlu, dengan command ringkas.
13. **Jangan baca `.env`.**
14. **Jangan jalankan** `migrate:fresh`/`db:wipe`/hapus source tanpa approval eksplisit.

---

## Larangan Keras

1. Jangan minta/baca/simpan kredensial, token, `.env`.
2. Jangan tambah payment gateway, PPOB, API eksternal, microservices.
3. Jangan pakai repository pattern atau abstraksi tanpa kebutuhan.
4. Jangan ubah file di luar scope tugas aktif.
5. Jangan ganti nama file/folder/class tanpa alasan.
6. Jangan naik/turun versi framework tanpa alasan kompatibilitas.
7. Jangan klaim tes lulus tanpa bukti.
8. Jangan tinggalkan TODO/placeholder tanpa penjelasan.
9. Jangan pakai emoji sebagai ikon UI.
10. Jangan catat PII/kredensial di log.
11. Jangan hapus/timpa data pengguna tanpa periksa dampak.
12. Jangan tambah fitur di luar scope karena "menurut saya lebih baik".

---

## Kewajiban Implementasi

**Validasi & otorisasi:**
- Server-side validation (FormRequest).
- Policy + middleware.
- Cek kepemilikan (IDOR prevention).
- CSRF, escaping Blade, mass assignment eksplisit.
- Rate limit login, pembayaran, endpoint sensitif.

**Database & integritas:**
- Transaksi DB untuk operasi multi-tabel yang harus atomik (stok, payment, refund, commission).
- DECIMAL untuk uang.
- FK, unique, index sesuai kebutuhan.
- Validasi status sebelum transisi.
- Jangan `migrate:fresh`/`db:wipe` tanpa approval.

**Alur pembayaran & pesanan:**
- Ikuti `PROJECT_SPEC.md §6`.
- `payments.status` = sumber kebenaran.
- Urutan lock: `payments → products (id ASC) → orders (id ASC)`.
- Idempoten: request kedua pada payment yang sudah diproses → `409`.
- Snapshot: harga, nama, alamat — immutable pasca-checkout.

**UI/UX (dari revisi UI):**
- Mobile-first; mobile ≠ desktop miniatur.
- Layout mobile dirancang independen (bottom nav, drawer).
- Konsisten dengan design system: stone base + deep teal, Figtree, Heroicons via `<x-icon>`.
- Setiap halaman: loading, empty, error, success, disabled, validation state.
- Tidak ada horizontal overflow di 375 px.
- Tidak ada emoji sebagai ikon.
- Aksesibilitas: kontras ≥ 4.5:1, keyboard nav, focus ring.

**File & konfigurasi:**
- Validasi upload (mime, ekstensi, size).
- Jangan simpan kredensial di source.

---

## Gaya Kode

- PSR-12, konvensi Laravel.
- Nama class/method/variabel Inggris; UI Indonesia.
- Controller fokus HTTP; logika bisnis di Service.
- FormRequest untuk validasi kompleks.
- Enum PHP untuk status.
- Jangan `try-catch` untuk menutupi error.
- Komentar hanya bila perlu.

---

## Konflik & Penyimpangan

Bila instruksi terbaru bertentangan dengan `PROJECT_SPEC.md`:
1. Identifikasi konflik.
2. Berhenti pada bagian terdampak.
3. Minta keputusan pengguna.
4. Setelah disetujui, dokumentasikan di `PROGRESS.md`.

---

## Prioritas

1. Keamanan & integritas data.
2. Spesifikasi & keputusan bisnis.
3. Konsistensi status & logika.
4. Fungsionalitas yang dapat diverifikasi.
5. Kemudahan pemeliharaan.
6. Kualitas UI.
7. Optimasi.