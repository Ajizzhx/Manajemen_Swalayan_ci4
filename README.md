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
  - [mysqli](https://www.php.net/manual/en/mysqli.installation.php) untuk koneksi MySQL
  - [pdo_mysql](https://www.php.net/manual/en/ref.pdo-mysql.php) untuk PDO MySQL
  - [libcurl](http://php.net/manual/en/curl.requirements.php) untuk HTTP\CURLRequest
  - [fileinfo](https://www.php.net/manual/en/fileinfo.installation.php) untuk menangani file Excel/CSV
- MySQL atau MariaDB
- Composer

> [!PENTING]

### Instalasi dan Persiapan XAMPP

XAMPP adalah solusi paket server web yang menyediakan Apache, MySQL, PHP, dan Perl. Untuk menginstal dan menyiapkan XAMPP:

1. **Install XAMPP**

   - Download XAMPP dari [situs resmi Apache Friends](https://www.apachefriends.org/download.html)
   - Pilih versi dengan PHP 8.1 atau lebih tinggi
   - Jalankan installer dan ikuti petunjuk instalasi

2. **Menjalankan XAMPP**

   - Buka XAMPP Control Panel
   - Aktifkan modul Apache dan MySQL dengan mengklik tombol "Start"
   - Pastikan kedua modul berjalan (status warna hijau)

3. **Mengaktifkan Ekstensi PHP**

   - Di XAMPP Control Panel, klik "Config" pada baris Apache, lalu pilih "PHP (php.ini)"
   - Pastikan ekstensi berikut tidak dikomentari (hapus tanda `;` di awal baris jika ada):
     ```
     extension=intl
     extension=mbstring
     extension=mysqli
     extension=pdo_mysql
     extension=curl
     extension=fileinfo
     ```
   - Simpan file dan restart Apache di XAMPP Control Panel

4. **Persiapan Database**
   - Buka phpMyAdmin melalui http://localhost/phpmyadmin
   - Klik "Database" di menu atas
   - Buat database baru dengan nama `swalayan_db`
   - Pilih collation `utf8mb4_unicode_ci`
   - Klik "Create"

### Instalasi Composer

Aplikasi ini membutuhkan Composer untuk pengelolaan dependensi. Jika belum menginstal Composer, ikuti langkah-langkah berikut:

1. Download installer Composer dari [getcomposer.org](https://getcomposer.org/download/)
2. Untuk Windows, unduh dan jalankan Composer-Setup.exe
3. Untuk Linux/Mac, ikuti petunjuk instalasi CLI di situs Composer

Setelah instalasi, verifikasi dengan menjalankan:

```
composer --version
```

## Panduan Instalasi

> Aplikasi ini menggunakan sistem OTP untuk login sebagai owner. Anda memerlukan email yang valid dan pengaturan SMTP yang benar untuk menerima kode OTP.

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



### Instalasi Manual (Cara Alternatif)

Jika terjadi masalah pada instalasi Otomatis, silahkan gunakan cara alternatif ini, berikut instruksinya:

#### 1. Download/Clone Project

```
git clone https://github.com/Ajizzhx/swalayan_ci4.git
```

Tempatkan folder project ini di htdocs XAMPP (misal: `C:/xampp/htdocs/swalayan_ci4`)

> [!IMPORTANT]
> Pastikan XAMPP sudah terinstal dan server Apache serta MySQL sudah aktif sebelum melanjutkan langkah berikutnya.

#### 2. Install Dependency melalui Composer

```
cd swalayan_ci4
composer install
```

#### 3. Copy & Edit File Environment

```
cp env .env
```

Kemudian edit file `.env` untuk mengatur konfigurasi database dan aplikasi:

- Ubah `CI_ENVIRONMENT` sesuai kebutuhan (development/production)
- Sesuaikan `app.baseURL`
- Konfigurasi database

#### 4. Konfigurasi Database

- Buat database baru di phpMyAdmin/MySQL, misal: `swalayan_db`
- Edit bagian database di file `.env`:
  ```
  database.default.hostname = localhost
  database.default.database = swalayan_db
  database.default.username = root
  database.default.password =
  database.default.DBDriver = MySQLi
  ```

#### 5. Migrasi & Seeder Database

Jalankan perintah berikut di terminal/cmd dari folder project:

```
php spark migrate
php spark db:seed InitialDataSeeder
```

#### 6. Konfigurasi Email untuk OTP (Wajib untuk Login Owner)

Edit bagian email di file `.env` atau di `app/Config/Email.php`:

```
email.fromEmail = 'noreply@swalayanci4.com'
email.fromName = 'Swalayan CI4'
email.SMTPHost = 'smtp.gmail.com'
email.SMTPUser = 'your-email@gmail.com'
email.SMTPPass = 'your-app-password'
email.SMTPPort = 465
email.SMTPCrypto = 'ssl'
```

#### 7. Menjalankan Website

```
php spark serve
```

Buka http://localhost:8080 di browser

## Penggunaan

## Memulai Aplikasi

### Menggunakan PHP Built-in Server

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

## Penjelasan File Instalasi

Proses instalasi pada aplikasi ini menggunakan beberapa file otomatisasi untuk memudahkan setup. Berikut penjelasan masing-masing file:

### install.php

File ini bertugas sebagai script utama untuk persiapan aplikasi dengan fungsi:

- Memeriksa versi PHP dan ekstensi yang diperlukan
- Memverifikasi keberadaan dan versi Composer
- Menginstal dependensi PHP menggunakan Composer
- Membuat file `.env` dengan menyalin dari `env`
- Mengkonfigurasi URL dasar aplikasi
- Mempersiapkan konfigurasi email

### setup-database.php

File ini bertanggung jawab untuk konfigurasi database dengan fungsi:

- Memeriksa keberadaan file `.env`
- Mengonfirmasi/mengubah konfigurasi database (host, nama, user, password)
- Memperbarui file `.env` dengan konfigurasi database yang baru
- Mengonfigurasi email SMTP untuk verifikasi OTP
- Membuat database jika belum ada
- Menjalankan migrasi untuk membuat struktur tabel
- Mengisi data awal dengan seeder
- Membuat akun owner dengan email yang ditentukan

### install.bat

Batch file untuk Windows yang menjalankan seluruh proses instalasi secara otomatis:

- Memeriksa ketersediaan PHP
- Menjalankan `install.php` untuk konfigurasi aplikasi
- Menjalankan `check-env.php` untuk validasi file `.env`
- Menjalankan `setup-database.php` untuk konfigurasi database
- Menampilkan informasi akses setelah instalasi selesai

> Panduan ini hanya untuk instalasi dan setup awal. Untuk pengembangan lebih lanjut, silakan sesuaikan sesuai kebutuhan Anda.

