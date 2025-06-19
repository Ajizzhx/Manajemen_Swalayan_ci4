# Swalayan CI4 - Point of Sale System

## Tentang Swalayan CI4

Swalayan CI4 adalah sistem Point of Sale (POS) berbasis web yang dibangun menggunakan framework CodeIgniter 4. Aplikasi ini dirancang untuk membantu manajemen toko/swalayan dalam mengelola inventaris produk, transaksi penjualan, pelanggan, supplier, dan laporan keuangan.

## Panduan Lengkap

Untuk panduan lengkap instalasi dan konfigurasi, lihat file-file berikut:
- [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md) - Panduan instalasi detil
- [PANDUAN_SETUP_EMAIL_OTP.md](PANDUAN_SETUP_EMAIL_OTP.md) - Cara mengkonfigurasi email OTP

## Fitur Utama

- Manajemen produk dengan dukungan barcode
- Manajemen kategori produk
- Manajemen supplier
- Manajemen pelanggan dengan sistem loyalitas poin
- Transaksi penjualan dengan berbagai metode pembayaran
- Manajemen pengeluaran (expenses)
- Laporan keuangan dan analitik
- Manajemen karyawan dengan sistem role (admin, kasir, owner)
- Audit log untuk monitoring aktivitas

## Basis Framework

Aplikasi ini dibangun di atas framework CodeIgniter 4 yang light, fast, flexible dan secure.
Untuk informasi lebih lanjut tentang framework, kunjungi [situs resmi CodeIgniter](https://codeigniter.com).

Anda juga dapat membaca [user guide](https://codeigniter.com/user_guide/) untuk informasi lebih detail tentang framework yang digunakan.

## Langkah Instalasi

### 1. Persiapan

1. Pastikan XAMPP/WAMP sudah terinstal dan berjalan dengan baik
2. Pastikan PHP dan MySQL sudah teraktivasi
3. Pastikan Composer sudah terinstal pada sistem

### 2. Mengunduh Proyek

1. Clone atau unduh repositori ini ke direktori `htdocs` pada XAMPP (atau direktori publik web server Anda)
   ```
   git clone [url-repo] swalayan_ci4
   ```
   atau extract file zip ke direktori `htdocs/swalayan_ci4`

### 3. Instalasi Otomatis (Disarankan)

1. **Windows**: Jalankan file `setup.bat` dengan mengklik dua kali, lalu jalankan `setup_database.php`
   ```
   setup.bat
   php setup_database.php
   ```
2. **Linux/Mac**: Jalankan perintah berikut di terminal
   ```
   bash setup.sh
   php setup_database.php
   ```
3. Script-script ini akan otomatis:
   - Menginstal semua library yang diperlukan (setup.bat/setup.sh)
   - Membuat dan mengkonfigurasi database (setup_database.php)
   - Membuat user default (setup_database.php)

### 4. Instalasi Manual

1. Masuk ke direktori proyek
   ```
   cd swalayan_ci4
   ```

2. Instal library yang dibutuhkan dengan:
   ```
   php setup_libraries.php
   ```
   atau jika Composer sudah terinstal:
   ```
   composer install
   ```

3. Setup database dengan:
   ```
   php setup_database.php
   ```

4. Update email pemilik untuk OTP dengan:
   ```
   php setup_owner_email.php
   ```
   Masukkan email aktif yang bisa menerima OTP

### 3. Konfigurasi Database

1. Salin file `env` menjadi `.env`
   ```
   cp env .env
   ```
   atau pada Windows:
   ```
   copy env .env
   ```

2. Edit file `.env` dan sesuaikan konfigurasi database:
   ```
   database.default.hostname = localhost
   database.default.database = swalayan_db
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

### 4. Inisialisasi Database

#### Opsi 1: Menggunakan Script Setup Otomatis

1. Untuk pengguna Windows, jalankan:
   ```
   setup_db.bat
   ```

2. Untuk pengguna Linux/Mac, jalankan:
   ```
   sh setup_db.sh
   ```

#### Opsi 2: Setup Manual

1. Buat database baru bernama `swalayan_db` melalui phpMyAdmin atau command line MySQL
   ```
   CREATE DATABASE swalayan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

2. Jalankan migrasi database melalui command line:
   ```
   php spark migrate
   ```

3. Jalankan seeder untuk data awal:
   ```
   php spark db:seed InitialDataSeeder
   ```

#### Opsi 3: Menggunakan PHP Setup Script

1. Jalankan script PHP untuk setup database:
   ```
   php setup_database.php
   ```

### 5. Menjalankan Aplikasi

1. Pastikan web server (Apache) sudah berjalan
2. Buka browser dan akses aplikasi melalui URL:
   ```
   http://localhost/swalayan_ci4/public
   ```

3. Atau gunakan server pengembangan bawaan CodeIgniter:
   ```
   php spark serve
   ```
   dan akses melalui URL:
   ```
   http://localhost:8080
   ```

**Penting:** Sesuai dengan standar keamanan CodeIgniter 4, `index.php` berada di dalam folder *public*, bukan di root proyek. Untuk produksi, sebaiknya konfigurasi web server Anda untuk mengarah ke folder *public* proyek, bukan ke root proyek.

## Akses Login Default

Setelah proses setup database selesai, Anda bisa menggunakan akun default berikut untuk login:

### Pemilik (Owner):
- Email: owner@swalayan.com (sebaiknya update dengan email aktif Anda menggunakan script `setup_owner_email.php`)
- Password: owner123
- **PENTING**: Login sebagai pemilik memerlukan verifikasi OTP yang dikirim ke email. Anda perlu menjalankan `setup_owner_email.php` terlebih dahulu untuk update email pemilik, dan mengkonfigurasi pengiriman email OTP seperti dijelaskan di `PANDUAN_SETUP_EMAIL_OTP.md`

### Akun lain yang tersedia:

**Admin:**
- Email: admin@swalayan.com
- Password: admin123
- Memiliki akses ke manajemen produk, supplier, kategori, dan transaksi

**Kasir:**
- Email: kasir@swalayan.com
- Password: kasir123
- Memiliki akses terbatas hanya untuk transaksi penjualan

## Script Setup Tambahan

Beberapa script tambahan yang tersedia untuk mempermudah proses instalasi dan troubleshooting:

| Script | Fungsi | Cara Penggunaan |
|--------|--------|-----------------|
| **setup.bat / setup.sh** | Setup library otomatis | Klik dua kali atau `bash setup.sh` |
| **setup_libraries.php** | Menginstal semua library | `php setup_libraries.php` |
| **setup_database.php** | Membuat database dan tabel | `php setup_database.php` |
| **setup_owner_email.php** | Update email pemilik untuk OTP | `php setup_owner_email.php` |
| **fix_owner_role.php** | Memperbaiki role owner/pemilik | `php fix_owner_role.php` |
| **fix_auth.php** | Memperbaiki masalah pada Auth | `php fix_auth.php` |

## Struktur Database

Sistem menggunakan beberapa tabel utama:

1. **karyawan** - Manajemen karyawan/staff dengan berbagai role (pemilik, admin, kasir)
2. **transaksi** - Data transaksi penjualan
3. **detail_transaksi** - Detail item per transaksi
4. **produk** - Inventaris produk dengan dukungan barcode
5. **kategori** - Kategori produk
6. **supplier** - Data supplier
7. **pelanggan** - Data pelanggan dengan sistem poin loyalitas
8. **expenses** - Catatan pengeluaran
9. **audit_logs** - Log aktivitas sistem untuk keamanan dan monitoring

## Penggunaan Aplikasi

### Halaman Login
- Masukkan email dan password yang telah terdaftar
- Untuk akun pemilik, sistem akan meminta verifikasi OTP yang dikirimkan ke email
- Sistem akan mengarahkan ke dashboard sesuai role pengguna

### Dashboard Pemilik
- Akses penuh ke seluruh sistem termasuk laporan keuangan dan analitik
- Approval penghapusan transaksi
- Monitoring kinerja karyawan
- Pengaturan toko dan konfigurasi sistem

### Dashboard Admin
- Akses ke fitur-fitur utama aplikasi
- Manajemen produk, supplier, dan kategori
- Input dan edit data inventaris
- Laporan penjualan

### Dashboard Kasir
- Fokus pada transaksi penjualan
- Input data pelanggan baru
- Proses pembayaran dengan berbagai metode
- Cetak struk

## Panduan Pengaturan Email OTP

Untuk mengkonfigurasi sistem email OTP, ikuti langkah-langkah di file `PANDUAN_SETUP_EMAIL_OTP.md` yang berisi:
1. Cara membuat App Password di Gmail
2. Konfigurasi SMTP di `app/Config/Email.php`
3. Update email pemilik untuk menerima OTP
4. Troubleshooting masalah pengiriman OTP

## Troubleshooting

### Login & OTP Issues
- Jika tidak menerima email OTP saat login sebagai pemilik:
  1. Pastikan email pemilik sudah diupdate (`php setup_owner_email.php`)
  2. Pastikan konfigurasi SMTP di `app/Config/Email.php` sudah benar
  3. Cek folder spam di email pemilik

- Jika muncul error "Column 'user_id' cannot be null" saat login:
  ```
  php fix_auth.php
  ```

- Jika login gagal meski password benar:
  ```
  php fix_owner_role.php
  ```

### Issue Database
- Pastikan konfigurasi database pada file `.env` sudah benar
- Pastikan MySQL sudah berjalan
- Jika gagal migrasi, coba hapus database dan ulangi proses instalasi
  ```
  php setup_database.php
  ```

### Issue Permissions
- Pastikan direktori `writable` pada CodeIgniter memiliki izin tulis yang sesuai
- Folder writable harus bisa diakses (readable dan writeable) oleh web server

### Error 500
- Periksa log error di `writable/logs/`
- Aktifkan mode development pada `.env` dengan `CI_ENVIRONMENT = development`

Untuk panduan lengkap troubleshooting, lihat file `PANDUAN_INSTALASI.md`

## Library dan Dependencies

Aplikasi ini menggunakan beberapa library eksternal untuk mendukung fungsionalitasnya:

1. **endroid/qr-code**
   - Fungsi: Menghasilkan QR code untuk produk dan transaksi
   - Versi: ^6.0

2. **picqer/php-barcode-generator**
   - Fungsi: Menghasilkan barcode untuk produk
   - Versi: ^3.2

3. **phpoffice/phpspreadsheet**
   - Fungsi: Menghasilkan file Excel untuk laporan
   - Versi: ^4.2

Semua library di atas akan otomatis terinstal saat menjalankan `setup.bat`, `setup.sh`, atau `php setup_libraries.php`.

## Persyaratan Sistem

- PHP 8.1 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Ekstensi PHP:
  - intl
  - mbstring
  - json
  - mysqlnd
  - xml
  - gd
  - zip
- Composer (opsional - akan diunduh otomatis oleh script setup jika belum ada)
