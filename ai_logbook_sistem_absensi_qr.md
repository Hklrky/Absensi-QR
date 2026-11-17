<h1 align="center">AI LOGBOOK PENGEMBANGAN PROYEK</h1>
<h4 align="center">Dokumentasi Pemanfaatan AI sebagai Asisten Kerangka, Desain Antarmuka, dan Debugging pada Proyek Sistem Absensi QR Code.</h4>

---

## 🎯 Ruang Lingkup & Batasan Penggunaan AI

Dalam pengembangan proyek **Sistem Absensi QR Code** berbasis PHP ini, kecerdasan artifisial (AI) **tidak digunakan untuk membuat proyek secara penuh (full-code)**. Peran AI dibatasi secara spesifik hanya sebagai alat bantu teknis pada bagian-bagian berikut:

1. **Penyusunan Kerangka Dasar:** Memberikan referensi struktur awal koneksi API PHP ke database MySQL dan kerangka integrasi library QR Code.
2. **Eksplorasi Gaya Tampilan:** Membantu merancang konsep CSS bertema *Comic / Pop Art* serta tata letak terpisah antara halaman publik dan halaman pengelola.
3. **Bantuan Perbaikan Error:** Membantu melacak penyebab pesan *error* spesifik yang muncul selama proses integrasi di server cPanel dan pengujian perangkat mobile.

Seluruh konfigurasi database cPanel, manajemen aset gambar, pengaturan keamanan kredensial, pengujian langsung, serta penyesuaian alur logika akhir dikerjakan dan diintegrasikan secara mandiri.

---

## 🏗️ Tahap 1: Penyusunan Kerangka Sistem & Arsitektur

### Fokus Bantuan AI
AI dimintai bantuan untuk merancang kerangka arsitektur sederhana yang memisahkan antara antarmuka pengguna (HTML/CSS/JS) dengan pemrosesan database (PHP/MySQL) di lingkungan hosting cPanel.

### Hal yang Didiskusikan
* Pemisahan akses antara halaman **Scan Publik (`scan.html`)** tanpa login dan halaman **Dashboard Admin (`admin.php`)** yang terproteksi sesi (*session*).
* Kerangka kerja validasi QR Code khusus menggunakan kunci rahasia (*Secret Key*) agar QR Code sembarangan tidak dapat terbaca sebagai absensi.
* Konsep pembuatan kartu QR dinamis menggunakan HTML5 Canvas yang dapat menyisipkan logo dari direktori server (`image/`) tepat di bagian tengah matriks QR.

### Contoh Prompt yang Digunakan
> *"Buatkan kerangka sistem absensi scan QR code menggunakan HTML, CSS, JavaScript, dan database MySQL cPanel. Pisahkan halaman scan tanpa login dengan halaman admin yang memiliki fitur generator kartu QR berlogo di tengah."*

### Modifikasi & Pengerjaan Mandiri
Kerangka kode yang diberikan AI tidak langsung diterapkan mentah-mentah. Konfigurasi koneksi database (`$host`, `$db_name`, `$username`, `$password`), penentuan nilai `$SECRET_KEY`, pembuatan struktur tabel di phpMyAdmin cPanel, serta pengunggahan aset logo ke folder `image/` dilakukan dan disesuaikan secara mandiri sesuai kebutuhan server.

---

## 🎨 Tahap 2: Eksplorasi Tampilan Antarmuka (Comic Style UI)

### Fokus Bantuan AI
AI dimanfaatkan untuk menerjemahkan konsep visual *Comic / Pop Art* ke dalam aturan CSS agar tampilan aplikasi web tidak terlihat kaku dan memiliki karakter visual yang kuat.

### Elemen Desain yang Diadaptasi
* Penggunaan tipografi bergaya komik.
* Penerapan garis tepi hitam tegas (`border: 3px solid #111`) dan bayangan padat (*hard box-shadow*) pada setiap komponen kartu, tombol, dan tabel.
* Tata letak responsif untuk dashboard admin yang memuat fitur filter tanggal, input absensi manual, manajemen hapus data, dan pratinjau kartu QR.

### Modifikasi & Pengerjaan Mandiri
Pemilihan palet warna akhir, penyesuaian ukuran elemen agar nyaman dilihat di layar ponsel maupun desktop, serta penataan navigasi antar-halaman diatur ulang secara mandiri setelah diuji coba langsung di browser.

---

## 🛠️ Tahap 3: Penanganan Galat & Perbaikan Bug (Debugging)

### Fokus Bantuan AI
Selama proses pemasangan file di cPanel dan pengujian kamera pada perangkat seluler, ditemukan beberapa kendala teknis. AI digunakan sebagai rekan diskusi untuk mendiagnosis pesan *error* dan mencari solusi perbaikannya.

### Daftar Bug yang Diperbaiki Bersama AI

1. **Kesalahan Sintaks PHP (`PHP Parse error: Unclosed '(' does not match ']'`)**
   * **Masalah:** Terjadi kesalahan penulisan variabel ganda (`$stmtCheck$stmtCheck->fetch`) pada `api.php` yang menyebabkan server gagal memproses permintaan.
   * **Solusi:** Membersihkan baris kode yang mengalami salah ketik agar fungsi *prepared statement* PDO berjalan normal.

2. **Kendala Login Administrator**
   * **Masalah:** Akun admin gagal masuk ke dashboard meskipun data sudah ada di tabel `users`.
   * **Solusi:** Menggunakan fungsi `password_hash()` bawaan PHP untuk memastikan format enkripsi kata sandi di database cocok dengan fungsi `password_verify()` saat proses login.

3. **Error Kolom Database Tidak Ditemukan (`SQLSTATE[42S22]: Unknown column 'status'`)**
   * **Masalah:** Penambahan fitur status kehadiran (Hadir, Izin, Sakit) memicu *Fatal Error* karena tabel `absensi` lama di cPanel belum memiliki kolom `status`.
   * **Solusi:** Menjalankan perintah `ALTER TABLE` di phpMyAdmin cPanel untuk menambahkan kolom `status` secara mandiri.

4. **Bug Kamera Scanner di Perangkat Mobile (Layar Hitam & Tampilan Terbalik)**
   * **Masalah:** Kamera pada ponsel menampilkan layar hitam, memunculkan tombol kontrol bawaan yang mengganggu, serta orientasi kamera depan yang terbalik.
   * **Solusi:**
     * Mengubah pemanggilan library dari `Html5QrcodeScanner` standar menjadi `Html5Qrcode` dengan pemilihan otomatis kamera depan (*front/user camera*).
     * Menambahkan aturan CSS `transform: scaleX(-1)` agar tampilan kamera tidak terbalik dan menyembunyikan tombol kontrol yang tidak diperlukan.
     * Memperbaiki penanganan blok `try-catch` saat mem-parsing JSON agar pemindaian QR Code yang salah format dapat memunculkan peringatan dengan akurat.

5. **Konfigurasi Clean URL (`.htaccess`)**
   * **Masalah:** URL masih menampilkan ekstensi file (`.php` / `.html`).
   * **Solusi:** Menyusun aturan `RewriteRule` pada file `.htaccess` di cPanel agar halaman dapat diakses tanpa ekstensi file.

---

## 💡 Tahap 4: Pemahaman Kode & Evaluasi Akhir

### Pembelajaran yang Diperoleh
Melalui proses tanya-jawab dan perbaikan bug bersama AI, diperoleh pemahaman mendalam mengenai:
* Bagaimana *Frontend* (JavaScript `fetch API`) berkomunikasi secara asinkron dengan *Backend* (PHP) menggunakan format JSON.
* Pentingnya pengaturan *Error Correction Level* (`'H'`) pada QR Code agar kode tetap terbaca oleh kamera meskipun bagian tengahnya tertutup oleh gambar logo.
* Cara membaca *error log* PHP di cPanel untuk melacak baris kode dan struktur tabel MySQL yang bermasalah.

### Kesimpulan
Dalam proyek ini, AI berfungsi efektif sebagai **akselerator pembuatan kerangka, referensi desain CSS, dan asisten debugging**. Keputusan arsitektur, pengelolaan database MySQL di cPanel, manajemen file server, serta pengujian fungsionalitas akhir tetap dikendalikan dan divalidasi sepenuhnya oleh pengembang.