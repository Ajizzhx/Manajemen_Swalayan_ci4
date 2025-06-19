# Panduan Instalasi Swalayan CI4

## Daftar Script Setup & Fungsinya

| Script | Platform | Fungsi | Cara Menggunakan |
|--------|----------|--------|-----------------|
| **setup.bat** | Windows | Setup lengkap (library + database) | Klik dua kali pada file |
| **setup.sh** | Linux/Mac | Setup lengkap (library + database) | `bash setup.sh` |
| **setup_libraries.php** | Semua | Hanya setup library | `php setup_libraries.php` |
| **setup_database.php** | Semua | Hanya setup database | `php setup_database.php` |
| **setup_owner_email.php** | Semua | Mengubah email pemilik untuk OTP | `php setup_owner_email.php` |
| **fix_owner_role.php** | Semua | Memperbaiki role pemilik | `php fix_owner_role.php` |
| **fix_auth.php** | Semua | Memperbaiki masalah user_id pada Auth | `php fix_auth.php` |

## Langkah-langkah Instalasi (Ringkas)

### Catatan Tentang Nama Direktori
Jika Anda mengunduh dari GitHub, nama direktori default mungkin `Manajemen_Swalayan_ci4-main` bukan `swalayan_ci4`. Dalam panduan ini, kami menggunakan `swalayan_ci4` untuk konsistensi. Anda dapat:
1. Mengubah nama direktori menjadi `swalayan_ci4`, ATAU
2. Menyesuaikan path di browser menjadi `http://localhost/Manajemen_Swalayan_ci4-main/public/`

### Cara Paling Mudah (Rekomendasi)
1. Pastikan XAMPP sudah terinstal dan berjalan (Apache & MySQL)
2. **Windows**: Klik dua kali pada file `setup.bat`  
   **Linux/Mac**: Jalankan perintah `bash setup.sh`
3. Script akan otomatis menginstal library dan database dalam satu langkah
4. Buka http://localhost/swalayan_ci4/public dan login (atau sesuaikan dengan nama direktori)
5. Perbarui email pemilik dengan menjalankan `php setup_owner_email.php`

## Langkah-langkah Instalasi (Detail)

### Persiapan Awal
1. Pastikan XAMPP sudah terinstal (minimal PHP 8.1, MySQL 5.7)
2. Pastikan layanan Apache dan MySQL sudah berjalan

### Instalasi One-Click (Library + Database)

#### Menggunakan Script All-in-One
1. **Windows**: Jalankan file `setup.bat` dengan mengklik dua kali, atau gunakan Command Prompt:
   ```
   setup.bat
   ```
   
   **Linux/Mac**: Jalankan file setup.sh:
   ```
   bash setup.sh
   ```

2. Script ini akan:
   - Memeriksa keberadaan PHP dan MySQL
   - Menginstal semua library yang diperlukan (mengunduh Composer jika perlu)
   - Membuat database dan tabel secara otomatis
   - Menampilkan langkah selanjutnya yang perlu dilakukan

3. Jika MySQL tidak tersedia atau tidak berjalan, script akan memberikan opsi untuk menginstal hanya library saja dan menunda setup database.

### Instalasi Manual (Jika Diperlukan)

#### Langkah 1: Instalasi Library Saja
1. Buka Command Prompt (cmd) atau PowerShell
2. Arahkan ke direktori proyek:
   ```
   cd c:\xampp\htdocs\swalayan_ci4
   ```
3. Jalankan script setup library dengan perintah:
   ```
   php setup_libraries.php
   ```
4. Script akan memeriksa keberadaan Composer, mengunduh jika perlu, dan menginstal semua library

#### Langkah 2: Instalasi Database Saja
1. Pastikan MySQL sudah berjalan
2. Jalankan script setup database dengan perintah:
   ```
   php setup_database.php
   ```
3. Script akan membuat database, tabel, dan user default secara otomatis

#### Cara 2: Menggunakan CodeIgniter Migration
1. Buka Command Prompt (cmd) atau PowerShell
2. Arahkan ke direktori proyek:
   ```
   cd c:\xampp\htdocs\swalayan_ci4
   ```
3. Jalankan perintah migrasi:
   ```
   php spark migrate
   ```
4. Jalankan seeder untuk membuat data awal:
   ```
   php spark db:seed InitialDataSeeder
   ```

#### Cara 3: Menggunakan phpMyAdmin
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Buat database baru bernama `swalayan_db`
3. Import file SQL dari direktori `c:\xampp\htdocs\swalayan_ci4\app\Database\Backups\swalayan_db.sql` (jika ada)
   atau jalankan script setup_database.php melalui browser

### Langkah 2: Instalasi Database

### Langkah 3: Akses Aplikasi Web
1. Buka browser
2. Akses URL dengan salah satu dari dua cara berikut:

   #### Menggunakan Apache/XAMPP (Direkomendasikan):
   - Jika direktori bernama `swalayan_ci4`: http://localhost/swalayan_ci4/public
   - Jika direktori bernama `Manajemen_Swalayan_ci4-main`: http://localhost/Manajemen_Swalayan_ci4-main/public
   
   #### Menggunakan Server Pengembangan CI4:
   - Jalankan terlebih dahulu: `php spark serve` dari terminal
   - Lalu akses: http://localhost:8080
   
3. Login menggunakan akun pemilik (owner) terlebih dahulu:

   **Pemilik (Owner):**
   - Email: [email yang Anda masukkan saat setup database] atau `owner@swalayan.com` (default)
   - Password: owner123

   > **SANGAT PENTING!** 
   > 
   > Jika saat setup database Anda diminta memasukkan email pemilik, pastikan menggunakan email asli dan aktif
   > yang dapat dipakai untuk menerima kode OTP. Jika email tidak valid, Anda tidak akan menerima kode OTP untuk login.
   > 
   > Jika ingin mengubah email pemilik, jalankan script `php setup_owner_email.php`

### Langkah 4: Konfigurasi Email OTP

Aplikasi menggunakan email OTP untuk keamanan login pemilik. Email default `owner@swalayan.com` tidak akan berfungsi untuk menerima kode OTP kecuali diubah ke email aktif Anda.

#### Cara 1: Update Email Pemilik Menggunakan Script
1. Jalankan script khusus untuk mengubah email pemilik:
   ```
   php setup_owner_email.php
   ```
   Atau melalui browser: http://localhost/swalayan_ci4/setup_owner_email.php
2. Masukkan email asli dan aktif yang Anda miliki
3. Email akan diperbarui dan siap digunakan untuk menerima OTP

#### Cara 2: Update Email dari Dalam Aplikasi
1. Login sebagai pemilik (Jika belum menerima OTP, lihat bagian Troubleshooting di bawah)
2. Navigasi ke menu "Area Pemilik" > "Profil & Email OTP"
3. Masukkan email aktif Anda dan simpan perubahan

#### Cara 3: Update Email Melalui phpMyAdmin
1. Buka phpMyAdmin dan navigasi ke database `swalayan_db`
2. Buka tabel `karyawan`
3. Cari record dengan `role` = 'pemilik' atau 'owner' 
4. Edit kolom `email` menjadi email valid Anda

### Langkah 5: Konfigurasi Akun Email Server

Untuk mengirim OTP, aplikasi perlu terhubung ke server SMTP. Lihat panduan lengkap di [PANDUAN_SETUP_EMAIL_OTP.md](./PANDUAN_SETUP_EMAIL_OTP.md).

1. Buat App Password di akun Gmail Anda
2. Edit file `app/Config/Email.php`
3. Ubah pengaturan berikut:
   ```php
   public string $SMTPUser = 'email_anda@gmail.com';
   public string $SMTPPass = 'xxxx xxxx xxxx xxxx'; // App Password dari Gmail
   ```

   **Akun lain yang tersedia:**
   
   **Admin:**
   - Email: admin@swalayan.com
   - Password: admin123

   **Kasir:**
   - Email: kasir@swalayan.com
   - Password: kasir123

### Akun Default

Setelah instalasi, beberapa akun pengguna sudah tersedia:

| Tipe Akun | Email | Password | Fitur Khusus |
|-----------|-------|----------|--------------|
| **Pemilik** | owner@swalayan.com* | owner123 | Memerlukan OTP, akses semua menu termasuk laporan keuangan |
| **Admin** | admin@swalayan.com | admin123 | Manajemen produk, kategori, supplier, transaksi |
| **Kasir** | kasir@swalayan.com | kasir123 | Transaksi penjualan, riwayat transaksi |

*) **PENTING**: Ganti dengan email aktif Anda menggunakan script `setup_owner_email.php` untuk menerima OTP

## Troubleshooting

### Masalah Login Pemilik & OTP
1. **Tidak menerima email OTP**:
   - Jalankan `php setup_owner_email.php` untuk memastikan email pemilik sudah diubah ke email aktif Anda
   - Cek konfigurasi SMTP di `app/Config/Email.php` (lihat PANDUAN_SETUP_EMAIL_OTP.md)
   - Cek folder spam/junk di email Anda
   - Pastikan Anda telah membuat "App Password" di Gmail seperti dijelaskan di PANDUAN_SETUP_EMAIL_OTP.md

2. **Error "Column 'user_id' cannot be null" saat login**:
   - Jalankan `php fix_auth.php` untuk memperbaiki masalah pada controller Auth

3. **Login gagal meski password benar**:
   - Jalankan `php fix_owner_role.php` untuk memperbaiki role user di database
   - Pastikan role adalah 'pemilik' (atau 'owner' - keduanya valid dalam sistem)

4. **Login sebagai pemilik tanpa OTP (jika belum bisa setup email)**:
   - Gunakan akun admin terlebih dahulu untuk akses sistem
   - Kemudian setup email OTP melalui phpMyAdmin

### Masalah Database
- Pastikan MySQL berjalan (cek di XAMPP Control Panel)
- Pastikan konfigurasi database di file `.env` sudah benar:
  ```
  database.default.hostname = localhost
  database.default.database = swalayan_db
  database.default.username = root
  database.default.password = 
  ```
- Jika error "Access denied", periksa username dan password MySQL
- Jika database tidak ditemukan, jalankan `php setup_database.php`

### Masalah Library
- Jika muncul error "Class not found", jalankan `php setup_libraries.php`
- Library utama yang dibutuhkan (diinstal otomatis oleh setup_libraries.php):
  - endroid/qr-code: QR Code generator
  - picqer/php-barcode-generator: Barcode generator
  - phpoffice/phpspreadsheet: Export Excel

### Error Aplikasi Lainnya
- Cek log error di `writable/logs/`
- Ubah `CI_ENVIRONMENT = development` di `.env` untuk melihat detail error
- Pastikan permission folder `writable` sudah benar (readable dan writeable)
- Jika muncul error pada halaman monitoring stok, jalankan `php fix_monitoring.php` (jika ada)

## Skenario Instalasi

### Skenario 1: Instalasi Baru (Komputer Pribadi)
1. Instal XAMPP (atau Apache+MySQL+PHP)
2. Clone atau download project ke folder htdocs
3. Jalankan `setup.bat` atau `bash setup.sh` 
4. Jalankan `php setup_database.php` untuk setup database
5. Jalankan `php setup_owner_email.php` untuk mengatur email pemilik
6. Setup email OTP sesuai panduan di PANDUAN_SETUP_EMAIL_OTP.md
7. Akses aplikasi di http://localhost/swalayan_ci4/public

### Skenario 2: Pemindahan ke Server/Hosting
1. Upload seluruh folder project ke server
2. Buat database baru di server
3. Import database dari backup SQL atau jalankan `php setup_database.php`
4. Update konfigurasi database di `.env` sesuai server
5. Jalankan `php setup_owner_email.php` untuk mengatur email pemilik
6. Setup email OTP sesuai panduan
7. Akses aplikasi melalui URL server

### Skenario 3: Penggunaan Multiple User (Beberapa Komputer)
1. Install di server utama yang bisa diakses oleh semua user
2. Pastikan database di-share ke semua pengguna
3. Setiap user akses aplikasi melalui URL server
4. Tidak perlu instalasi di setiap komputer client

Untuk informasi lebih lanjut, silakan hubungi tim pengembang.

Untuk informasi lebih lanjut, lihat README.md di direktori proyek.
