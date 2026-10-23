# AI_RULES.md — Aturan untuk AI Coding Agent

> Dokumen ini mengikat setiap AI agent yang bekerja pada proyek ini. Tujuannya menjaga konsistensi spesifikasi, keamanan data, dan kelanjutan pekerjaan antar-sesi.

## 1. Prinsip Dasar

1. **Jangan mengarang.** Jangan mengklaim file, migration, model, route, controller, atau tes sudah ada atau berjalan sebelum diverifikasi.
2. **Audit sebelum implementasi.** Periksa kondisi proyek sebelum perubahan kode pertama. Audit ulang bagian yang relevan pada tugas berikutnya.
3. **Jangan melompati fase.** Ikuti `IMPLEMENTATION_PLAN.md`. Perpindahan fase memerlukan persetujuan eksplisit pengguna.
4. **Jangan memperluas scope.** Jangan menambah fitur, package, teknologi, endpoint, atau abstraksi yang tidak dibutuhkan oleh fase aktif.
5. **Jangan menyembunyikan masalah.** Laporkan error apa adanya. Jangan menggunakan `try-catch` kosong, menonaktifkan validasi, atau membuat fallback yang menutupi kegagalan.
6. **Jangan mengulang pekerjaan tanpa alasan.** Periksa kode dan progres yang sudah ada sebelum membuat implementasi baru.
7. **Utamakan perubahan minimal.** Ubah hanya bagian yang diperlukan untuk menyelesaikan tugas tanpa merusak fungsi yang sudah berjalan.
8. **Jangan mengasumsikan pekerjaan selesai.** Gunakan bukti dari kode, hasil perintah, dan tes aktual.

## 2. Dokumen Acuan

Sebelum mengerjakan tugas, baca dokumen yang tersedia dan relevan:

- `PROJECT_SPEC.md`: spesifikasi fitur, aturan bisnis, skema data, keamanan, dan kriteria penerimaan.
- `IMPLEMENTATION_PLAN.md`: urutan fase, ruang lingkup, dan kriteria penyelesaian.
- `AI_RULES.md`: aturan kerja AI coding agent.
- `PROGRESS.md`: kondisi pekerjaan terakhir, keputusan yang telah disetujui, hasil tes, masalah tersisa, dan langkah berikutnya.

Jika dokumen belum ada atau isinya tidak lengkap, laporkan kondisi tersebut. Jangan mengarang isinya atau menganggap implementasi sudah dilakukan.

Jika terdapat konflik, gunakan spesifikasi dan keputusan yang telah disetujui sebagai acuan. Jangan mengambil keputusan bisnis baru secara sepihak.

## 3. Audit Proyek

Sebelum perubahan kode pertama, periksa:

- Versi PHP, Laravel, Node.js, dan ekstensi PHP yang dibutuhkan.
- Keberadaan `composer.json`, `package.json`, `artisan`, `routes/web.php`, folder `app/`, serta `database/migrations/`.
- Struktur folder yang relevan dengan tugas.
- Status Git: branch aktif, perubahan lokal, dan file yang belum dilacak.
- Keberadaan `.env.example`. Baca hanya file contoh tersebut jika diperlukan.
- Migration, model, controller, service, route, middleware, policy, dan tes yang sudah tersedia.
- Dependensi serta konfigurasi yang relevan.
- Kondisi database dan lingkungan pengujian jika diperlukan dan tersedia.

Laporkan ringkasan temuan sebelum implementasi.

**Batasan audit:**

- Jangan meminta, membaca, menampilkan, atau menyimpan isi `.env` asli.
- Jangan menimpa perubahan lokal milik pengguna.
- Jangan menjalankan perintah yang berpotensi menghapus data atau mengubah database produksi.
- Jangan melakukan migrasi, instalasi package, atau perubahan kode hanya untuk keperluan audit tanpa persetujuan yang diperlukan.
- Jika ditemukan konflik dengan spesifikasi, berhenti pada bagian yang terdampak dan minta keputusan pengguna.

Audit awal tidak perlu diulang sepenuhnya pada setiap tugas. Periksa ulang kondisi aktual dan bagian yang relevan sebelum melanjutkan pekerjaan.

## 4. Larangan Keras

1. Jangan meminta, membaca, atau menyimpan kredensial, token, rahasia, atau isi `.env` asli.
2. Jangan menambahkan payment gateway, integrasi PPOB, API eksternal, microservices, atau integrasi lain yang tidak disetujui.
3. Jangan menambahkan repository pattern atau abstraksi yang tidak memiliki kebutuhan nyata.
4. Jangan mengubah file yang tidak relevan dengan tugas aktif.
5. Jangan mengganti nama file, folder, class, atau teknologi yang sudah berjalan tanpa alasan teknis yang jelas.
6. Jangan menaikkan atau menurunkan versi framework dan dependensi tanpa kebutuhan kompatibilitas yang terverifikasi.
7. Jangan mengklaim tes lulus tanpa menjalankannya dan memeriksa hasil aktual.
8. Jangan meninggalkan TODO, placeholder, data dummy, atau implementasi sementara tanpa alasan dan penjelasan.
9. Jangan menggunakan emoji sebagai ikon antarmuka.
10. Jangan mencatat password, token, kredensial, atau PII yang tidak diperlukan dalam log.
11. Jangan menghapus atau menimpa data, perubahan lokal, migration, maupun implementasi yang sudah ada tanpa memeriksa dampaknya.
12. Jangan menambahkan fitur di luar ruang lingkup proyek hanya karena dianggap lebih baik oleh AI.

## 5. Kewajiban Implementasi

### 5.1 Validasi dan otorisasi

- Lakukan validasi server-side untuk setiap input yang diterima.
- Gunakan Form Request atau mekanisme validasi Laravel yang sesuai dengan kompleksitas tugas.
- Terapkan otorisasi server-side melalui Policy, Gate, atau middleware yang tepat.
- Jangan menganggap tombol yang disembunyikan di UI sebagai mekanisme otorisasi.
- Pastikan pengguna hanya dapat mengakses dan mengubah data yang menjadi haknya.
- Gunakan CSRF protection sesuai mekanisme framework.
- Escape output Blade untuk mencegah XSS. Jangan menggunakan raw HTML untuk konten pengguna tanpa sanitasi yang tepat.
- Terapkan rate limiting pada login dan endpoint sensitif sesuai kebutuhan.

### 5.2 Database dan integritas data

- Gunakan transaksi database untuk operasi yang mengubah stok, pembayaran, pesanan, pembatalan, atau refund secara saling bergantung.
- Gunakan `DECIMAL` untuk nilai uang, bukan floating-point.
- Gunakan foreign key, unique constraint, index, dan constraint lain jika memang dibutuhkan untuk menjaga integritas data.
- Validasi status terkini sebelum melakukan transisi status.
- Gunakan operasi atomik dan row locking ketika diperlukan untuk mencegah race condition.
- Jangan mengandalkan validasi di aplikasi saja untuk menjamin keunikan data yang harus unik.
- Jangan membuat migration duplikat sebelum memeriksa skema yang sudah ada.
- Jangan mengedit migration lama yang sudah digunakan pada lingkungan bersama tanpa memeriksa dampaknya dan meminta persetujuan bila diperlukan.
- Jangan menjalankan `migrate:fresh`, `db:wipe`, atau perintah destruktif lainnya tanpa persetujuan eksplisit dan kepastian bahwa data yang terdampak boleh dihapus.

### 5.3 Aturan pembayaran, pesanan, dan stok

Ikuti alur dan status yang ditetapkan dalam `PROJECT_SPEC.md`.

- `payments.status` merupakan sumber kebenaran untuk status pembayaran.
- Validasi status sebelum mengubah pembayaran, pesanan, checkout, atau stok.
- Operasi yang sama tidak boleh memproses pembayaran, membatalkan pesanan, mengembalikan stok, atau melakukan refund lebih dari sekali.
- Kegagalan pembayaran tidak boleh menyebabkan status pembayaran, checkout, dan pesanan menjadi tidak konsisten.
- Periksa hasil conditional update. Jika transisi dari `pending` gagal karena status sudah berubah, jangan melanjutkan perubahan pada data terkait seolah-olah transisi berhasil.
- Gunakan snapshot harga, nama produk, jumlah, subtotal, dan alamat sesuai spesifikasi.
- Jangan mengambil harga terbaru untuk menggantikan snapshot pesanan yang sudah terbentuk.
- Refund simulasi harus mengikuti aturan status, nominal, dan pengembalian stok yang ditentukan dalam spesifikasi.

**Urutan penguncian database:**

- Untuk alur pembayaran yang mengunci ketiga jenis data, ikuti urutan global `payments → products → orders`.
- Untuk `OrderCancellationService`, ikuti urutan `products → orders` karena alur tersebut tidak mengubah data pembayaran.
- Jangan membuat urutan locking baru yang berpotensi menimbulkan deadlock. Jika operasi baru memerlukan penguncian lintas tabel, analisis interaksinya dengan transaksi yang sudah ada terlebih dahulu.
- Validasi ulang status dan kondisi bisnis setelah memperoleh lock yang diperlukan.

### 5.4 File dan konfigurasi

- Validasi unggahan berdasarkan MIME type, ekstensi, dan ukuran file.
- Simpan file melalui mekanisme storage Laravel yang sesuai.
- Jangan mempercayai nama file atau MIME type dari browser tanpa validasi.
- Jangan menaruh kredensial atau konfigurasi rahasia ke dalam source code.
- Jangan mengubah konfigurasi global hanya untuk menyelesaikan masalah lokal tanpa memahami dampaknya.

## 6. Alur Kerja Setiap Tugas

1. **Baca dokumen.** Periksa `PROJECT_SPEC.md`, `IMPLEMENTATION_PLAN.md`, `AI_RULES.md`, dan `PROGRESS.md`.
2. **Periksa progres aktual.** Cocokkan `PROGRESS.md` dengan kode, migration, route, dan tes yang benar-benar ada.
3. **Audit bagian relevan.** Periksa dependensi, implementasi terkait, dan perubahan lokal.
4. **Tentukan ruang lingkup.** Jelaskan tujuan tugas, file yang akan dibuat atau diubah, serta alasan perubahan.
5. **Periksa kebutuhan persetujuan.** Jika tugas memerlukan perubahan scope, keputusan bisnis baru, perubahan teknologi, atau perpindahan fase, berhenti dan minta persetujuan.
6. **Implementasikan secara bertahap.** Gunakan perubahan minimal dan ikuti gaya serta pola proyek yang sudah berjalan.
7. **Verifikasi perubahan.** Jalankan tes yang relevan dan periksa hasil perintah aktual.
8. **Periksa dampak samping.** Pastikan fitur terkait tidak rusak dan perubahan tidak melanggar spesifikasi.
9. **Perbarui `PROGRESS.md`.** Catat perubahan aktual, hasil tes, masalah tersisa, dan langkah berikutnya.
10. **Laporkan hasil.** Berikan ringkasan yang dapat diverifikasi dan jelaskan hal yang belum selesai.

### Ketentuan persetujuan Fase 2

Fase 1 telah selesai dan disetujui. Audit kondisi proyek untuk Fase 2 boleh dilakukan tanpa mengubah kode.

Setelah audit, AI harus menyampaikan temuan dan rencana implementasi Fase 2 untuk ditinjau pengguna.

**Jangan memulai implementasi Fase 2 sebelum pengguna memberikan persetujuan eksplisit.**

Persetujuan untuk satu tugas tidak otomatis menjadi persetujuan untuk seluruh fase atau fitur lain.

## 7. Pelaporan Hasil

Setiap laporan akhir tugas harus mencakup:

- **Ringkasan:** hasil yang dicapai.
- **File berubah:** file yang dibuat, diubah, atau dihapus.
- **Alasan perubahan:** fungsi setiap perubahan utama.
- **Perintah yang dijalankan:** misalnya perintah tes, build, atau migrasi.
- **Hasil verifikasi:** status lulus, gagal, atau tidak dijalankan berdasarkan hasil aktual.
- **Asumsi:** asumsi yang digunakan dan apakah sudah disetujui.
- **Masalah tersisa:** error, keterbatasan, TODO, atau risiko yang belum terselesaikan.
- **Verifikasi manual:** langkah yang perlu dilakukan pengguna, jika ada.
- **Langkah berikutnya:** pekerjaan yang masih berada dalam ruang lingkup dan memerlukan persetujuan jika relevan.

Jika tes tidak dijalankan, jelaskan alasannya. Jika perintah gagal, laporkan kegagalan tersebut tanpa menyebut pekerjaan sebagai berhasil sepenuhnya.

## 8. Gaya Kode

- Ikuti PSR-12 dan konvensi Laravel yang sesuai dengan versi proyek.
- Gunakan nama class, method, variabel, dan file yang deskriptif dalam bahasa Inggris.
- Gunakan bahasa Indonesia untuk teks antarmuka pengguna.
- Pertahankan controller tetap fokus pada HTTP request dan response.
- Tempatkan logika bisnis yang kompleks atau digunakan bersama pada service yang memang diperlukan.
- Gunakan Form Request untuk validasi yang cukup kompleks atau dapat digunakan kembali.
- Gunakan enum PHP untuk status jika sesuai dengan versi PHP, pola proyek, dan kebutuhan penyimpanan. Jangan membuat enum atau class tambahan hanya untuk status yang sederhana tanpa manfaat nyata.
- Hindari duplikasi logika bisnis, tetapi jangan membuat abstraksi generik sebelum ada kebutuhan yang jelas.
- Jangan menambahkan komentar yang hanya mengulang kode.
- Jangan menggunakan `try-catch` untuk menutupi error.
- Tangani exception hanya jika ada tindakan pemulihan, translasi error, rollback, atau penanganan yang jelas dan benar.
- Pertahankan pola arsitektur yang sudah berjalan kecuali terdapat alasan teknis yang kuat untuk mengubahnya.
- Prioritaskan UI yang konsisten, responsif, dan dapat digunakan tanpa mengorbankan keamanan atau integritas data.

## 9. Penanganan Konflik dan Penyimpangan

Jika menemukan konflik antara instruksi terbaru, spesifikasi, implementasi aktual, dan rencana fase:

1. Identifikasi bagian yang bertentangan.
2. Jelaskan dampaknya terhadap fitur atau integritas data.
3. Jangan memilih keputusan bisnis baru secara sepihak.
4. Berhenti pada bagian yang terdampak dan minta keputusan pengguna.
5. Setelah keputusan disetujui, catat keputusan dan alasannya di `PROGRESS.md` jika berdampak pada pekerjaan berikutnya.

Instruksi terbaru tidak otomatis mengizinkan perubahan scope atau pengabaian keputusan yang telah disetujui jika maksudnya belum jelas.

## 10. Kelanjutan Antar-Sesi AI

Saat melanjutkan pekerjaan dari sesi sebelumnya:

1. Baca keempat dokumen proyek.
2. Verifikasi progres aktual, bukan hanya mengandalkan catatan.
3. Identifikasi tugas terakhir yang benar-benar selesai.
4. Periksa hasil tes terakhir dan masalah yang belum terselesaikan.
5. Lanjutkan dari langkah berikutnya yang telah disetujui.
6. Jangan mengulang pekerjaan yang sudah benar tanpa alasan teknis.
7. Jangan melanjutkan fase yang belum memperoleh persetujuan.
8. Perbarui `PROGRESS.md` sebelum mengakhiri sesi.

Jika konteks tidak cukup untuk melanjutkan dengan aman, jelaskan informasi yang belum tersedia dan minta klarifikasi yang diperlukan saja.

## 11. Urutan Prioritas

Jika beberapa pertimbangan bertentangan, gunakan urutan berikut:

1. Keamanan dan integritas data.
2. Spesifikasi serta keputusan bisnis yang telah disetujui.
3. Konsistensi status dan kebenaran logika bisnis.
4. Fungsionalitas yang dapat diverifikasi.
5. Kemudahan pemeliharaan dan keterbacaan kode.
6. Konsistensi serta kualitas UI.
7. Optimasi tambahan yang memiliki manfaat terukur.

Jangan mengorbankan prioritas yang lebih tinggi hanya untuk membuat implementasi terlihat lebih lengkap atau canggih.