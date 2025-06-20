# Swalayan CI4 Application

## Tentang Aplikasi

Aplikasi Swalayan CI4 adalah sistem manajemen toko swalayan berbasis web yang dibangun dengan framework CodeIgniter 4. Aplikasi ini memiliki fitur lengkap untuk manajemen produk, transaksi, karyawan, pelanggan, laporan, dan lainnya.

## Persyaratan Sistem

- PHP versi 8.1 atau lebih tinggi
- Ekstensi PHP yang dibutuhkan:
  - [intl](http://php.net/manual/en/intl.requirements.php)
  - [mbstring](http://php.net/manual/en/mbstring.installation.php)
  - json (diaktifkan secara default)
  - [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) untuk MySQL
  - [libcurl](http://php.net/manual/en/curl.requirements.php) untuk HTTP\CURLRequest
- MySQL atau MariaDB
- Composer

> [!PENTING]
> Aplikasi ini menggunakan sistem OTP untuk login sebagai owner. Anda memerlukan email yang valid dan pengaturan SMTP yang benar untuk menerima kode OTP.

## Panduan Instalasi

### Instalasi Otomatis (Rekomendasi untuk Windows)

1. Clone repository ini ke komputer Anda:

   ```
   git clone https://github.com/Ajizzhx/swalayan_ci4.git
   cd swalayan_ci4
   ```

2. Jalankan file instalasi batch:

   ```
   install.bat
   ```

3. Ikuti petunjuk yang muncul untuk mengkonfigurasi aplikasi dan database.

   > [!NOTE]
   > Instalasi otomatis sudah termasuk mengubah file `env` menjadi `.env`, menginstal semua dependensi, dan mengatur database.

### Instalasi Manual

1. Clone repository ini ke komputer Anda:

   ```
   git clone https://github.com/Ajizzhx/swalayan_ci4.git
   cd swalayan_ci4
   ```

2. Ubah nama file `env` menjadi `.env`:

   ```
   # Di Windows
   rename env .env

   # Di Linux/Mac
   mv env .env
   ```

3. Jalankan skrip instalasi PHP:

   ```
   php install.php
   ```

4. Jalankan skrip pembuatan database:

   ```
   php setup-database.php
   ```

5. Ikuti petunjuk yang muncul untuk mengkonfigurasi aplikasi dan database.

## Penggunaan

## Memulai Aplikasi

1. Setelah instalasi selesai, jalankan server development:

   ```
   php spark serve
   ```

2. Buka browser dan akses URL berikut:

   ```
   http://localhost:8080
   ```

3. Login sesuai dengan peran:
   - **Owner**: Gunakan email yang telah didaftarkan saat instalasi dan password default `owner123`
     - Login owner menggunakan verifikasi OTP yang dikirim ke email
   - **Admin**: admin@swalayan.com (password: admin123)
   - **Kasir**: kasir@swalayan.com (password: kasir123)

## Fitur Utama

- Manajemen produk dan kategori
- Manajemen supplier dan pelanggan
- Transaksi penjualan dengan barcode scanner
- Laporan penjualan dan stok
- Manajemen pengguna dengan peran berbeda (owner, admin, kasir)
- Audit log untuk keamanan

## Konfigurasi Server Production

Untuk deployment ke server production, pastikan:

1. Server web (Apache/Nginx) dikonfigurasi untuk mengarah ke folder `public/`
2. Pastikan folder `writable/` bisa ditulis oleh server web
3. Sesuaikan file `.env` dengan konfigurasi production yang benar:
   - Ubah `CI_ENVIRONMENT` menjadi `production`
   - Sesuaikan `app.baseURL` dengan domain Anda
   - Konfigurasi database dan email dengan benar

## Informasi Penting

- Login owner menggunakan sistem OTP yang dikirim ke email. Pastikan email yang digunakan adalah email valid dan dapat diakses.
- Untuk mengubah konfigurasi email, edit file `app/Config/Email.php` atau gunakan script instalasi.
- Backup database secara berkala untuk menghindari kehilangan data.

## Tingkatan Pengguna (User Levels)

Aplikasi ini memiliki 4 tingkatan pengguna (role) dengan hak akses yang berbeda. Berikut tampilan menu sidebar untuk masing-masing role:

### 1. Pemilik (Owner)

Pemilik memiliki akses penuh ke seluruh sistem sebagai pengguna level tertinggi.

**Menu Sidebar:**

- **Dashboard**: Statistik penjualan dan inventori secara keseluruhan
- **Laporan & Analisis**:
  - Riwayat Transaksi: Melihat seluruh transaksi yang pernah dilakukan
  - Pendapatan: Analisis pendapatan harian dan periode
  - Produk Terlaris: Melihat statistik produk paling banyak terjual
- **Area Pemilik** (Menu Khusus):
  - Laporan Keuangan: Laporan laba/rugi dengan pengelolaan pengeluaran
  - Log Audit: Pemantauan aktivitas sistem secara menyeluruh
  - Kelola Karyawan: Manajemen akun admin, kasir, dan kepala toko
  - Persetujuan Transaksi: Menyetujui/menolak penghapusan transaksi

**Fitur Keamanan Khusus:**

- Login dengan verifikasi OTP (One-Time Password) melalui email

### 2. Kepala Toko

Kepala toko memiliki akses ke fitur monitoring untuk pengawasan operasional.

**Menu Sidebar:**

- **Dashboard**: Ringkasan operasional toko
- **Monitoring** (Menu Khusus):
  - Monitoring Stok: Pemantauan real-time status stok produk
  - Monitoring Penjualan: Analisis performa penjualan harian dan bulanan
  - Monitoring Kasir: Evaluasi kinerja kasir dengan statistik transaksi
- **Laporan & Analisis**:
  - Riwayat Transaksi: Data transaksi lengkap
  - Pendapatan: Analisis pendapatan per periode
  - Produk Terlaris: Statistik produk terlaris

### 3. Administrator

Admin memiliki akses untuk mengelola operasional bisnis harian.

**Menu Sidebar:**

- **Dashboard**: Statistik penjualan dan inventori
- **Kelola Data Master** (Menu Khusus):
  - Produk: Tambah, edit, hapus data produk
  - Kategori: Pengaturan kategori produk
  - Supplier: Manajemen data supplier
  - Membership: Kelola data pelanggan/member
- **Monitoring**:
  - Monitoring Stok: Pemantauan real-time status stok produk
  - Monitoring Penjualan: Analisis performa penjualan harian dan bulanan
  - Monitoring Kasir: Evaluasi kinerja kasir dengan statistik transaksi
- **Laporan & Analisis**:
  - Riwayat Transaksi: Melihat riwayat transaksi
  - Produk Terlaris: Statistik produk terlaris
  - Log Audit: Pemantauan aktivitas sistem

### 4. Kasir

Kasir memiliki akses terbatas untuk melayani pelanggan di kasir.

**Menu Sidebar:**

- **Dashboard Kasir**: Ringkasan transaksi dan penjualan harian
- **Transaksi Penjualan**: Menu utama untuk proses penjualan dengan scanner barcode
- **Riwayat Transaksi**: Transaksi yang telah dilakukan oleh kasir tersebut
- **Cek Harga & Stok**: Pencarian cepat untuk informasi produk

**Fitur Khusus Kasir:**

- Proses checkout dengan berbagai metode pembayaran
- Pencarian produk dan pelanggan
- Permintaan penghapusan transaksi (memerlukan persetujuan pemilik)

## Dukungan

Jika mengalami masalah atau pertanyaan, silakan buat issue di repository GitHub ini.
