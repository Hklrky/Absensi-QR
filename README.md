<div align="center">
  <h1>Absensi QR — Sistem Absensi Otomatis</h1>
  <p><b>Aplikasi web absensi berbasis QR Code terenkripsi dengan antarmuka unik bergaya Pop Art / Comic Style!</b></p>

  <p>
    <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
    <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
    <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript" />
    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  </p>
</div>

---

## 📖 Tentang Proyek
**Absensi QR** adalah sistem pencatatan kehadiran digital yang dirancang untuk memudahkan proses absensi menggunakan pemindaian **QR Code**. Proyek ini memisahkan area pemindaian publik dengan halaman pengelolaan administrator demi keamanan dan kemudahan akses di berbagai perangkat (terutama smartphone).



---

## ✨ Fitur Unggulan

### 📷 1. Halaman Publik Scanner (`scan.html`)
* **Tanpa Login:** Pengguna cukup membuka halaman scanner dan langsung mengarahkan kartu QR ke kamera.
* **Auto Front-Camera (Kamera Depan):** Otomatis mendeteksi dan mengaktifkan kamera depan perangkat dengan tampilan cermin (*mirror*) yang nyaman serta antarmuka bersih tanpa tombol mengganggu.
* **Validasi Keamanan QR:** Menggunakan format payload JSON dengan `SECRET_KEY` khusus sehingga QR Code sembarangan dari luar sistem akan langsung ditolak.
* **Anti-Absen Ganda:** Membatasi pencatatan maksimal **1 kali scan per hari** untuk setiap pengguna.

### 💥 2. Dashboard Administrator (`admin.php`)
* **Sistem Autentikasi Aman:** Dilengkapi halaman login terproteksi menggunakan *PHP Session* dan enkripsi password (`password_verify`).
* **Custom QR Card Generator:** Membuat kartu absensi digital bergaya komik lengkap dengan **logo instansi/sekolah di bagian tengah QR Code** yang diambil langsung dari folder `image/` di server, serta siap diunduh dalam format `PNG`.
* **Rekap & Filter Harian:** Memantau data kehadiran secara *real-time* berdasarkan tanggal tertentu.
* **Manajemen Data & Input Manual:** Admin dapat menambahkan kehadiran secara manual (Hadir, Izin, Sakit) maupun menghapus data yang tidak sesuai.

---

## 📂 Struktur Proyek

```text
Absensi-QR/
├── image/              # Folder penyimpanan logo
├── admin/
    └── index.php       # Dashboard administrator
├── api/
    └── api.php         # Backend API penghubung frontend ke database MySQL
├── css/
    └── style.css       # Stylesheet utama bergaya Comic / Pop Art
├── .htaccess           # Konfigurasi URL bersih 
└── index.html           # Halaman pemindai QR Code publik

```

---

## 🚀 Panduan Instalasi & Penerapan (cPanel)

### 1. Persiapan Database MySQL
1. Buat database baru dan user database di **MySQL Databases** cPanel.
2. Buka **phpMyAdmin**, pilih database tersebut, lalu jalankan perintah SQL berikut pada tab **SQL**:

```sql
CREATE TABLE `absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `status` varchar(20) DEFAULT 'Hadir',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun Default Admin (Username: admin | Password: password123)
INSERT INTO `users` (`username`, `password`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

```

### 2. Konfigurasi Backend (`api.php`)
Sesuaikan kredensial database dan kunci rahasia QR Code di bagian atas file `api.php`:

```php
$host       = 'localhost';
$db_name    = 'nama_database_kamu';
$username   = 'user_database_kamu';
$password   = 'password_database_kamu';
$SECRET_KEY = 'KODEPANJANG'; // Pastikan sama dengan SECRET_KEY di scan.html & admin.php

```

### 3. Persiapan Folder & SSL
* Buat folder bernama `image` di dalam direktori proyek dan unggah file logo (`.png`/`.jpg`) ke dalamnya.
* Pastikan domain menggunakan **HTTPS** agar browser mengizinkan akses kamera pada perangkat pengguna.

---

## 🔐 Akses Default
* **Halaman Scanner:** `https://domainkamu.com/scan`
* **Halaman Admin:** `https://domainkamu.com/admin`
  * **Username:** `admin`
  * **Password:** `password123` *(Segera ubah setelah login pertama kali)*

---



<p align="center">Dibuat dengan ⚡ semangat ngoding & kreativitas.</p>
