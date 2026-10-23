# IMPLEMENTATION_PLAN — Integrated E-Commerce Platform

> **Status:** Fase 1 selesai dan disetujui. Fase 2 belum dimulai.
>
> **Sumber kebenaran:** `PROJECT_SPEC.md`
>
> **Aturan:** Kerjakan satu fase secara bertahap. Jangan memulai fase berikutnya sebelum kriteria penyelesaian fase aktif terpenuhi dan hasilnya ditinjau.

---

## 1. Prinsip Implementasi

1. Baca `PROJECT_SPEC.md`, `IMPLEMENTATION_PLAN.md`, `AI_RULES.md`, dan `PROGRESS.md` sebelum memulai pekerjaan.
2. Audit kondisi proyek aktual sebelum mengubah kode.
3. Pertahankan keputusan final di `PROJECT_SPEC.md`. Jangan mengubah aturan bisnis tanpa persetujuan.
4. Kerjakan perubahan sekecil mungkin yang memenuhi kebutuhan fase aktif.
5. Hindari refactor, dependensi, abstraksi, dan fitur tambahan yang tidak diperlukan.
6. Jalankan pemeriksaan dan pengujian yang relevan setelah perubahan.
7. Jangan mengklaim fitur, migration, atau tes berhasil tanpa bukti aktual.
8. Perbarui `PROGRESS.md` setelah pekerjaan selesai atau sebelum menghentikan sesi.
9. Jika batas konteks atau limit AI tercapai, hentikan pekerjaan pada kondisi yang aman dan tuliskan langkah lanjutan secara spesifik.
10. Jangan mengerjakan beberapa fase sekaligus hanya untuk mempercepat penyelesaian.

---

## 2. Urutan Fase

| Fase | Nama | Status | Fokus |
|---|---|---|---|
| 1 | Discovery & Planning | Selesai | Spesifikasi, keputusan bisnis, batasan, dan acceptance criteria |
| 2 | Foundation | Belum dimulai | Audit proyek, autentikasi, skema dasar, role, middleware, layout |
| 3 | Marketplace B2C & C2C | Belum dimulai | Katalog, moderasi, keranjang, checkout, pembayaran simulasi |
| 4 | Marketplace B2B | Belum dimulai | Verifikasi bisnis, tier harga, minimum pembelian |
| 5 | Layanan Digital | Belum dimulai | Tagihan dummy, token listrik, pulsa, paket data |
| 6 | Dashboard & Laporan | Belum dimulai | Dashboard admin, seller, buyer, laporan |
| 7 | Integrasi, QA & Finalisasi | Belum dimulai | Pengujian integrasi, keamanan, konkurensi, UI, dokumentasi |

Status di atas adalah status perencanaan. Status implementasi aktual wajib mengikuti hasil audit dan bukti pekerjaan.

---

## 3. Fase 1 — Discovery & Planning

**Status:** Selesai dan disetujui.

### Cakupan

- Menentukan ruang lingkup aplikasi.
- Menentukan model B2C, B2B, dan C2C.
- Menentukan role, status, dan matriks otorisasi.
- Menyusun rancangan database.
- Menentukan alur checkout, pembayaran simulasi, stok, pembatalan, dan refund.
- Menentukan batasan keamanan dan acceptance criteria.

### Kriteria penyelesaian

- [x] `PROJECT_SPEC.md` tersedia sebagai sumber kebenaran.
- [x] Keputusan bisnis utama terdokumentasi.
- [x] Ruang lingkup dan batasan integrasi ditetapkan.
- [x] Fase berikutnya mengikuti spesifikasi yang disetujui.

Jangan mengulang fase ini kecuali ditemukan konflik spesifikasi atau pengguna meminta revisi.

---

## 4. Fase 2 — Foundation

**Status:** Belum dimulai.

### Tujuan

Memastikan proyek memiliki fondasi yang sesuai sebelum fitur marketplace dikembangkan.

### Tahap 2.1 — Audit proyek

- Periksa struktur direktori dan file penting.
- Verifikasi versi PHP, Laravel, Node.js, Composer, dan npm.
- Periksa `composer.json`, `package.json`, serta lockfile yang tersedia.
- Periksa migration, model, routes, controller, middleware, dan autentikasi.
- Periksa `.env.example` tanpa membaca atau membagikan `.env` asli.
- Periksa status dependensi dan tes yang tersedia.
- Identifikasi kode yang dapat dipertahankan, masalah kompatibilitas, dan pekerjaan yang belum ada.

**Batasan:** Audit tidak boleh mengubah kode, menghapus file, atau memasang dependensi.

**Output:** Laporan audit dan rencana perubahan berdasarkan kondisi aktual.

### Tahap 2.2 — Rencana fondasi

Setelah audit ditinjau:

- Tentukan struktur implementasi yang paling sederhana.
- Identifikasi migration atau fitur autentikasi yang sudah tersedia agar tidak dibuat ganda.
- Tentukan urutan perubahan dan dependensi antartugas.
- Identifikasi risiko kompatibilitas.
- Minta persetujuan pengguna sebelum memulai perubahan kode.

### Tahap 2.3 — Autentikasi dan identitas

Implementasikan atau sesuaikan autentikasi berdasarkan kondisi proyek:

- Registrasi dan login menggunakan mekanisme Laravel yang sesuai.
- Password disimpan menggunakan hashing yang aman.
- Kolom `role` dan `account_type`.
- Pengelolaan profil pengguna.
- Validasi akses halaman admin.
- Penanganan logout dan sesi.

Jangan menganggap Laravel Breeze belum terpasang sebelum audit.

### Tahap 2.4 — Skema dan model fondasi

Implementasikan secara bertahap berdasarkan rancangan `PROJECT_SPEC.md`:

- Identitas dan profil pengguna.
- Profil bisnis dan seller.
- Kategori dan produk.
- Keranjang.
- Checkout, order, item order, dan payment.
- Produk dan transaksi digital.
- Tabel pendukung lainnya ketika dibutuhkan oleh fase terkait.

Aturan:

- Audit migration yang sudah ada sebelum membuat migration baru.
- Jangan mengubah atau menghapus data yang ada tanpa persetujuan.
- Terapkan foreign key, unique constraint, index, dan tipe data yang sesuai.
- Periksa konsistensi enum/status terhadap `PROJECT_SPEC.md`.
- Jangan membuat seluruh fitur marketplace di fase ini.

### Tahap 2.5 — Otorisasi dan layout dasar

- Middleware admin.
- Pemeriksaan seller aktif.
- Policy untuk kepemilikan resource yang relevan.
- Layout Blade dasar.
- Integrasi Tailwind melalui Vite sesuai konfigurasi proyek.
- Komponen navigasi dan penanda "Mode Simulasi" jika sudah relevan.
- Pesan validasi dan error dasar.

### Kriteria penyelesaian Fase 2

- [ ] Audit proyek terdokumentasi.
- [ ] Rencana implementasi fondasi disetujui.
- [ ] Autentikasi berjalan sesuai hasil audit.
- [ ] Identitas dan role pengguna sesuai spesifikasi.
- [ ] Migration yang diimplementasikan dapat dijalankan pada database pengembangan yang benar.
- [ ] Relasi, foreign key, unique constraint, dan index penting diverifikasi.
- [ ] Middleware dan Policy dasar diuji.
- [ ] Layout dasar berjalan.
- [ ] Tidak ada dependensi yang ditambahkan tanpa alasan.
- [ ] Tes yang relevan lulus dan hasilnya tercatat.
- [ ] `PROGRESS.md` diperbarui.

Jangan memulai Fase 3 sebelum kriteria Fase 2 ditinjau dan disetujui.

---

## 5. Fase 3 — Marketplace B2C & C2C

**Status:** Belum dimulai.

### Tujuan

Membangun alur utama marketplace untuk produk B2C dan C2C tanpa mengaktifkan aturan harga grosir B2B terlebih dahulu.

### Cakupan

- Kategori dan katalog publik.
- Detail produk dan gambar.
- Pembuatan, pengubahan, pengarsipan, dan moderasi produk.
- Pendaftaran seller individu dan bisnis sesuai fondasi identitas.
- Keranjang.
- Pengelolaan alamat pengiriman.
- Checkout multi-seller.
- Snapshot item dan alamat.
- Payment simulasi.
- Pembentukan order per seller.
- Pengelolaan status pemenuhan pesanan.
- Riwayat order pembeli dan seller.
- Pembatalan checkout sebelum pembayaran.
- Pembatalan order yang sudah dibayar melalui `OrderCancellationService`, sesuai aturan spesifikasi.

### Batasan implementasi

- Ikuti aturan status dan transisi di `PROJECT_SPEC.md`.
- Terapkan transaksi database pada operasi stok dan pembayaran.
- Gunakan urutan lock global yang telah ditentukan.
- Jangan mengimplementasikan integrasi payment gateway.
- Jangan mengaktifkan pembelian B2B bagi pengguna yang belum terverifikasi.
- Jangan menggabungkan order dari beberapa seller menjadi satu order.
- Gunakan snapshot agar perubahan data katalog tidak mengubah riwayat order.

### Kriteria penyelesaian Fase 3

- [ ] Katalog hanya menampilkan produk dan seller yang aktif.
- [ ] Produk baru mengikuti alur moderasi.
- [ ] Buyer dapat mengelola keranjang dan alamat.
- [ ] Checkout membuat satu checkout dan order terpisah untuk setiap seller.
- [ ] Snapshot item, harga, dan alamat tersimpan dengan benar.
- [ ] Pembayaran simulasi menangani status sukses dan gagal.
- [ ] Pembayaran gagal tidak mengurangi stok.
- [ ] Pembayaran sukses mengurangi stok tepat satu kali.
- [ ] Order tidak dapat diakses atau diubah pengguna yang tidak berwenang.
- [ ] Pembatalan order memulihkan stok dan mencatat refund secara atomik.
- [ ] Pengujian alur normal dan kegagalan transaksi lulus.
- [ ] `PROGRESS.md` diperbarui dan hasil ditinjau.

---

## 6. Fase 4 — Marketplace B2B

**Status:** Belum dimulai.

### Tujuan

Menambahkan aturan pembelian bisnis tanpa merusak alur B2C dan C2C yang sudah berjalan.

### Cakupan

- Profil bisnis.
- Pengajuan dan verifikasi bisnis oleh admin.
- Produk dengan kanal `b2b` dan `both`.
- Pengelolaan tier harga grosir.
- Minimum pembelian.
- `PriceResolver`.
- Validasi akses harga dan produk di server.
- Validasi ulang kelayakan pembeli dan harga saat checkout serta pembayaran.

### Batasan implementasi

- Produk `b2b` tidak dapat dibeli oleh buyer personal atau bisnis yang belum terverifikasi.
- Produk `both` tetap bisa dibeli buyer non-terverifikasi dengan harga dasar.
- `min_purchase_qty` hanya berlaku bagi bisnis terverifikasi pada produk `b2b` dan `both`.
- Harga grosir dipilih berdasarkan tier terbesar dengan `min_quantity <= quantity`.
- Jika tidak ada tier yang cocok, gunakan harga dasar sesuai spesifikasi.
- Harga pada order yang sudah dibayar tetap mengikuti snapshot.
- Jangan menduplikasi logika resolusi harga pada banyak controller.

### Kriteria penyelesaian Fase 4

- [ ] Admin dapat memverifikasi dan mencabut verifikasi bisnis.
- [ ] Akses produk dan harga grosir sesuai status verifikasi.
- [ ] Buyer yang tidak memenuhi syarat ditolak oleh server.
- [ ] Minimum pembelian berlaku hanya pada kondisi yang ditentukan.
- [ ] Harga tier benar pada batas kuantitas.
- [ ] Perubahan harga atau tier divalidasi saat pembayaran.
- [ ] Pengujian regresi B2C dan C2C lulus.
- [ ] `PROGRESS.md` diperbarui dan hasil ditinjau.

---

## 7. Fase 5 — Layanan Digital Simulasi

**Status:** Belum dimulai.

### Tujuan

Menyediakan simulasi layanan digital menggunakan data internal tanpa integrasi provider eksternal.

### Cakupan

- Daftar produk digital demo.
- Seed data produk digital.
- Data tagihan listrik dummy.
- Form tagihan listrik pascabayar.
- Simulasi pembelian token listrik.
- Simulasi pulsa.
- Simulasi paket data.
- Validasi nomor meter dan nomor HP.
- Riwayat transaksi digital.
- Status transaksi dan referensi unik.
- Penanda simulasi pada UI.

### Batasan implementasi

- Tidak memanggil API provider eksternal.
- Tidak memproses uang sungguhan.
- Tidak menyimpan kredensial pembayaran.
- Hanya produk digital aktif yang dapat digunakan.
- Data tagihan harus berasal dari `dummy_bills`.
- Token harus diberi penanda dummy dan simulasi.
- Pengguna hanya dapat melihat transaksi miliknya, kecuali admin sesuai otorisasi.

### Kriteria penyelesaian Fase 5

- [ ] Seluruh jenis layanan yang ditentukan tersedia.
- [ ] Validasi input dilakukan di server.
- [ ] Data tagihan berasal dari database dummy.
- [ ] Referensi transaksi unik.
- [ ] Token listrik diberi penanda simulasi.
- [ ] Riwayat transaksi menerapkan pemeriksaan kepemilikan.
- [ ] Tidak ada panggilan ke API eksternal.
- [ ] `PROGRESS.md` diperbarui dan hasil ditinjau.

---

## 8. Fase 6 — Dashboard dan Laporan

**Status:** Belum dimulai.

### Tujuan

Menyediakan ringkasan operasional berdasarkan data database yang nyata.

### Cakupan

- Dashboard admin.
- Dashboard seller.
- Halaman akun dan riwayat pembeli.
- Ringkasan order dan transaksi.
- Laporan penjualan seller.
- Laporan transaksi admin.
- Filter berdasarkan rentang tanggal dan status jika relevan.

### Batasan implementasi

- Angka dashboard dihitung dari data aktual database.
- Pisahkan data global admin dan data milik seller.
- Seller tidak boleh membaca data penjualan seller lain.
- Order yang dibatalkan dan refund harus diperlakukan konsisten pada laporan.
- Jangan menambahkan grafik atau abstraksi kompleks tanpa manfaat yang jelas.

### Kriteria penyelesaian Fase 6

- [ ] Dashboard sesuai role pengguna.
- [ ] Data seller dibatasi pada toko sendiri.
- [ ] Laporan menggunakan query yang sesuai dan dapat diverifikasi.
- [ ] Nilai order dan refund ditampilkan secara konsisten.
- [ ] Filter laporan berfungsi.
- [ ] Pengujian otorisasi dan perhitungan laporan lulus.
- [ ] `PROGRESS.md` diperbarui dan hasil ditinjau.

---

## 9. Fase 7 — Integrasi, QA, dan Finalisasi

**Status:** Belum dimulai.

### Tujuan

Memastikan aplikasi memenuhi spesifikasi, aman digunakan untuk demonstrasi akademik, dan tidak memiliki inkonsistensi transaksi yang diketahui.

### Cakupan

- Pengujian acceptance criteria dari `PROJECT_SPEC.md`.
- Pengujian otorisasi dan IDOR.
- Pengujian transisi status.
- Pengujian snapshot.
- Pengujian stok, pembayaran, pembatalan, dan refund.
- Pengujian konkurensi menggunakan MySQL.
- Pengujian layanan digital.
- Pengujian UI responsif dan aksesibilitas.
- Pemeriksaan error log dan konfigurasi.
- Dokumentasi instalasi dan demonstrasi.

### Prioritas pengujian

1. Integritas payment, order, checkout, dan stok.
2. Otorisasi dan kepemilikan resource.
3. Validasi B2B dan harga tier.
4. Snapshot harga, item, dan alamat.
5. Refund/restock dan idempotensi.
6. Layanan digital.
7. Dashboard, laporan, dan UI.

### Kriteria penyelesaian Fase 7

- [ ] Seluruh acceptance criteria yang relevan sudah diuji.
- [ ] Hasil tes mencantumkan perintah yang dijalankan dan hasil aktual.
- [ ] Pengujian transaksi dan konkurensi dilakukan pada MySQL.
- [ ] Tidak ada bug kritis yang diketahui dan belum ditangani.
- [ ] Tidak ada integrasi eksternal yang melanggar ruang lingkup.
- [ ] Panduan instalasi dan konfigurasi tersedia.
- [ ] `.env` asli tidak disertakan.
- [ ] `PROJECT_SPEC.md`, `AI_RULES.md`, dan `PROGRESS.md` konsisten dengan implementasi aktual.
- [ ] Status final hanya ditandai selesai berdasarkan bukti.

---

## 10. Prosedur Kerja untuk Setiap Fase

Untuk setiap fase yang disetujui, ikuti urutan berikut:

1. Baca empat dokumen utama.
2. Periksa status aktual repositori dan pekerjaan terakhir.
3. Tentukan tugas terkecil yang dapat diselesaikan.
4. Jelaskan rencana perubahan dan file yang akan terdampak.
5. Untuk perubahan yang memengaruhi keputusan final, berhenti dan minta persetujuan.
6. Implementasikan hanya lingkup tugas yang disetujui.
7. Jalankan tes atau pemeriksaan yang relevan.
8. Periksa diff dan pastikan tidak ada perubahan tak relevan.
9. Laporkan file yang berubah, alasan, hasil tes, dan masalah tersisa.
10. Perbarui `PROGRESS.md`.
11. Berhenti ketika tugas atau fase selesai. Jangan otomatis melanjutkan ke fase berikutnya.

### Jika tes gagal

- Jangan menyembunyikan atau mengabaikan kegagalan.
- Tentukan apakah kegagalan disebabkan oleh perubahan terbaru atau kondisi sebelumnya.
- Perbaiki hanya dalam lingkup yang disetujui.
- Jika perbaikan memerlukan perubahan desain atau keputusan bisnis, jelaskan dan minta persetujuan.

### Jika sesi AI terputus

- Pastikan tidak ada perubahan parsial yang tidak tercatat.
- Perbarui `PROGRESS.md` dengan kondisi aktual.
- Catat pekerjaan terakhir yang terverifikasi.
- Catat tes terakhir yang dijalankan dan hasilnya.
- Tentukan satu langkah berikutnya yang spesifik.
- Pada sesi baru, audit ulang kondisi repositori sebelum melanjutkan.

---

## 11. Aturan Persetujuan

- Fase 1 telah selesai dan disetujui.
- Fase 2 belum dimulai dan memerlukan persetujuan pengguna setelah laporan audit serta rencana perubahan ditinjau.
- Fase berikutnya hanya dimulai setelah fase sebelumnya memenuhi kriteria penyelesaian dan pengguna menyetujui kelanjutannya.
- Perubahan terhadap keputusan final di `PROJECT_SPEC.md` memerlukan persetujuan baru.
- Perbaikan bug dalam ruang lingkup yang disetujui boleh dilakukan tanpa mengubah keputusan bisnis.
- Jika spesifikasi dan kondisi kode berbeda, dokumentasikan perbedaannya. Jangan diam-diam mengubah spesifikasi atau memaksakan perubahan kode sebelum penyebabnya dipahami.

---

## 12. Hubungan Antar-Dokumen

- `PROJECT_SPEC.md`: definisi kebutuhan, aturan bisnis, skema target, keamanan, dan acceptance criteria.
- `IMPLEMENTATION_PLAN.md`: urutan fase, cakupan kerja, dan kriteria penyelesaian.
- `AI_RULES.md`: aturan wajib untuk AI coding agent saat membaca, mengubah, dan menguji kode.
- `PROGRESS.md`: status aktual, keputusan yang telah disetujui, file yang berubah, hasil tes, blocker, dan langkah berikutnya.

Jika ditemukan konflik:

1. Jangan membuat keputusan bisnis baru secara otomatis.
2. Catat konflik yang ditemukan.
3. Gunakan `PROJECT_SPEC.md` sebagai acuan untuk keputusan final yang sudah disetujui.
4. Minta persetujuan pengguna jika konflik tidak dapat diselesaikan tanpa mengubah keputusan final.