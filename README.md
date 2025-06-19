# Swalayan CI4 - Point of Sale System

## Tentang Swalayan CI4

Swalayan CI4 adalah sistem Point of Sale (POS) berbasis web yang dibangun menggunakan framework CodeIgniter 4. Aplikasi ini dirancang untuk membantu manajemen toko/swalayan dalam mengelola inventaris produk, transaksi penjualan, pelanggan, supplier, dan laporan keuangan.

## Catatan Penting: Nama Direktori

Jika Anda mengunduh dari GitHub, nama direktori default adalah `Manajemen_Swalayan_ci4-main` bukan `swalayan_ci4`. Anda memiliki dua opsi:

1. **Mengubah nama direktori**:

   ```
   rename Manajemen_Swalayan_ci4-main swalayan_ci4
   ```

   atau

   ```
   mv Manajemen_Swalayan_ci4-main swalayan_ci4
   ```

2. **Menyesuaikan URL akses**:
   ```
   http://localhost/Manajemen_Swalayan_ci4-main/public/
   ```

Semua contoh dalam dokumentasi ini mengasumsikan direktori bernama `swalayan_ci4`, jadi sesuaikan path jika diperlukan.

## Panduan Lengkap

Untuk panduan lengkap instalasi dan konfigurasi, lihat file-file berikut:

- [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md) - Panduan instalasi detil
- [PANDUAN_SETUP_EMAIL_OTP.md](PANDUAN_SETUP_EMAIL_OTP.md) - Cara mengkonfigurasi email OTP

## Daftar Isi

1. [Tentang Swalayan CI4](#tentang-swalayan-ci4)
2. [Catatan Penting: Nama Direktori](#catatan-penting-nama-direktori)
3. [Panduan Lengkap](#panduan-lengkap)
4. [Fitur Utama](#fitur-utama)
5. [Basis Framework](#basis-framework)
6. [Tentang CodeIgniter 4 dan Instalasi Framework](#tentang-codeigniter-4-dan-instalasi-framework)
   - [Apa itu CodeIgniter 4?](#apa-itu-codeigniter-4)
   - [Keunggulan CodeIgniter 4](#keunggulan-codeigniter-4)
   - [Struktur Aplikasi CodeIgniter 4](#struktur-aplikasi-codeigniter-4)
   - [Instalasi Framework CI4](#instalasi-framework-ci4)
7. [Langkah Instalasi](#langkah-instalasi)
   - [1. Persiapan](#1-persiapan)
   - [2. Mengunduh Proyek](#2-mengunduh-proyek)
   - [3. Instalasi Otomatis (Disarankan)](#3-instalasi-otomatis-disarankan)
   - [4. Instalasi Manual](#4-instalasi-manual)
   - [5. Menjalankan Aplikasi](#5-menjalankan-aplikasi)
8. [Akses Login Default](#akses-login-default)
   - [Pemilik (Owner)](#pemilik-owner)
   - [Akun lain yang tersedia](#akun-lain-yang-tersedia)
9. [Script Setup Tambahan](#script-setup-tambahan)
10. [Struktur Database](#struktur-database)
11. [Kustomisasi dan Pengembangan Lanjutan](#kustomisasi-dan-pengembangan-lanjutan)
    - [Struktur MVC CodeIgniter 4](#struktur-mvc-codeigniter-4)
    - [Cara Menambahkan Fitur Baru](#cara-menambahkan-fitur-baru)
    - [Contoh Command CLI CodeIgniter](#contoh-command-cli-codeigniter)
    - [Tips Pengembangan CI4](#tips-pengembangan-ci4)
12. [Konfigurasi Server dan Routing](#konfigurasi-server-dan-routing)
    - [Konfigurasi Apache](#konfigurasi-apache)
    - [Konfigurasi Virtual Host (Opsional)](#konfigurasi-virtual-host-opsional)
    - [Path Alternatif untuk Repository GitHub](#path-alternatif-untuk-repository-github)
    - [Routing CodeIgniter 4](#routing-codeigniter-4)
13. [Cara Mengakses Aplikasi (Perbedaan URL)](#cara-mengakses-aplikasi-perbedaan-url)

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

## Tentang CodeIgniter 4 dan Instalasi Framework

### Apa itu CodeIgniter 4?

CodeIgniter 4 adalah framework PHP yang ringan, cepat, dan memiliki jejak kode yang minimal. Framework ini dirancang untuk pengembang yang membutuhkan toolkit sederhana dan elegan untuk membuat aplikasi web full-featured.

### Keunggulan CodeIgniter 4:

1. **Performa Tinggi** - Lebih cepat dibandingkan framework PHP lainnya
2. **Jejak Memori Rendah** - Hanya menggunakan resource yang dibutuhkan
3. **Dokumentasi Lengkap** - Panduan yang terstruktur dan mudah diikuti
4. **Minimal Konfigurasi** - Zero configuration, siap digunakan segera
5. **MVC Architecture** - Pemisahan logic aplikasi yang baik

### Struktur Aplikasi CodeIgniter 4

```
swalayan_ci4/
├── app/                    # Folder aplikasi utama
│   ├── Config/             # Konfigurasi aplikasi
│   ├── Controllers/        # Controller aplikasi
│   ├── Models/             # Model untuk database
│   ├── Views/              # Template view/tampilan
│   ├── Database/           # Migrasi dan seeder
│   ├── Filters/            # Filter untuk request
│   ├── Helpers/            # Helper functions
│   └── ...
├── public/                 # Webroot untuk deployment
│   ├── index.php           # Entry point aplikasi
│   ├── Assets/             # Asset statis (JS, CSS, img)
│   └── ...
├── writable/               # Folder untuk file temporary dan logs
├── vendor/                 # Library pihak ketiga (Composer)
├── spark                   # CLI tool CodeIgniter
└── composer.json           # Konfigurasi dependensi
```

### Instalasi Framework CI4

Biasanya, untuk instalasi framework CI4 dari awal, langkah-langkahnya adalah:

1. **Instalasi via Composer**:

   ```
   composer create-project codeigniter4/appstarter project-name
   ```

2. **Konfigurasi Environment**:

   - Salin file `env` menjadi `.env`
   - Set `CI_ENVIRONMENT = development` untuk debugging
   - Konfigurasi database dan preferensi aplikasi lainnya

3. **Jalankan Server Pengembangan**:

   ```
   php spark serve
   ```

4. **Buat Database dan Migrasi**:
   ```
   php spark migrate
   ```

Namun untuk project **Swalayan CI4** ini, semua konfigurasi dasar **sudah dilakukan** dan Anda cukup mengikuti langkah instalasi yang dijelaskan di bagian selanjutnya.

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

1. **Windows**: Jalankan file `setup.bat` dengan mengklik dua kali
   ```
   setup.bat
   ```
2. **Linux/Mac**: Jalankan perintah berikut di terminal
   ```
   bash setup.sh
   ```
3. Script-script ini akan otomatis:
   - Memeriksa keberadaan PHP dan MySQL
   - Menginstal semua library yang diperlukan
   - Membuat dan mengkonfigurasi database dan user default
   - Menampilkan langkah-langkah selanjutnya yang perlu dilakukan

> **CATATAN PENTING**: Jika Anda mengunduh dari GitHub, nama direktori default mungkin menjadi `Manajemen_Swalayan_ci4-main`. Anda dapat:
>
> 1. Mengubah nama direktori menjadi `swalayan_ci4`, ATAU
> 2. Menyesuaikan path di browser menjadi `http://localhost/Manajemen_Swalayan_ci4-main/public/`

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

### 3. Konfigurasi Database dan Environment

Swalayan CI4 menggunakan file konfigurasi lingkungan (environment) CodeIgniter 4 untuk menyimpan setting yang sensitif seperti kredensial database.

1. Salin file `env` menjadi `.env`

   ```
   cp env .env
   ```

   atau pada Windows:

   ```
   copy env .env
   ```

2. Edit file `.env` dan sesuaikan konfigurasi berikut:

   **Konfigurasi Environment**:

   ```
   # Setel ke 'development' untuk melihat detail error
   # Setel ke 'production' untuk deployment live
   CI_ENVIRONMENT = development
   ```

   **Konfigurasi Database**:

   ```
   database.default.hostname = localhost
   database.default.database = swalayan_db
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

   **Konfigurasi App URL (opsional)**:

   ```
   app.baseURL = 'http://localhost/swalayan_ci4/public/'
   ```

3. Jika Anda ingin mengubah nama database, pastikan untuk mengubah:

   - Konfigurasi di file `.env`
   - Parameter di `setup_database.php` (jika menggunakan script setup database)

4. File `.env` sudah ditambahkan ke `.gitignore` sehingga aman untuk menyimpan kredensial sensitif

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

   Jika Anda mengunduh dari GitHub dan tidak mengubah nama direktori:

   ```
   http://localhost/Manajemen_Swalayan_ci4-main/public
   ```

3. Atau gunakan server pengembangan bawaan CodeIgniter:

   ```
   php spark serve
   ```

   dan akses melalui URL:

   ```
   http://localhost:8080
   ```

   > **Penjelasan Penting**: URL `http://localhost:8080` dan `http://localhost/swalayan_ci4/public` keduanya valid tetapi menggunakan server web yang berbeda. URL pertama menggunakan server pengembangan bawaan CI4, sedangkan yang kedua menggunakan Apache melalui XAMPP. Lihat bagian [Cara Mengakses Aplikasi](#cara-mengakses-aplikasi-perbedaan-url) untuk penjelasan lengkap.

**Penting:** Sesuai dengan standar keamanan CodeIgniter 4, `index.php` berada di dalam folder _public_, bukan di root proyek. Untuk produksi, sebaiknya konfigurasi web server Anda untuk mengarah ke folder _public_ proyek, bukan ke root proyek.

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

Beberapa script yang tersedia untuk proses instalasi dan troubleshooting:

| Script                    | Fungsi                             | Cara Penggunaan                    |
| ------------------------- | ---------------------------------- | ---------------------------------- |
| **setup.bat / setup.sh**  | Setup lengkap (library + database) | Klik dua kali atau `bash setup.sh` |
| **setup_libraries.php**   | Hanya menginstal library           | `php setup_libraries.php`          |
| **setup_database.php**    | Hanya membuat database dan tabel   | `php setup_database.php`           |
| **setup_owner_email.php** | Update email pemilik untuk OTP     | `php setup_owner_email.php`        |
| **fix_owner_role.php**    | Memperbaiki role owner/pemilik     | `php fix_owner_role.php`           |
| **fix_auth.php**          | Memperbaiki masalah pada Auth      | `php fix_auth.php`                 |

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

## Kustomisasi dan Pengembangan Lanjutan

### Struktur MVC CodeIgniter 4

Aplikasi Swalayan CI4 mengikuti pola MVC (Model-View-Controller) sesuai standar CodeIgniter 4:

1. **Models** (`app/Models/`): Berisi logika data dan interaksi dengan database

   - Contoh: `ProdukModel.php`, `TransaksiModel.php`
   - Menggunakan Entity classes untuk representasi data yang lebih OOP

2. **Views** (`app/Views/`): Template untuk antarmuka pengguna

   - Menggunakan template engine bawaan CI4
   - Template utama di `Views/Backend/`
   - Berisi partial views yang reusable

3. **Controllers** (`app/Controllers/`): Menangani request HTTP dan alur aplikasi
   - Controllers utama di folder `Admin/`, `Kasir/`, dll.
   - Base controller di `BaseController.php`

### Cara Menambahkan Fitur Baru

1. **Menambahkan Tabel Database**:

   - Buat file migrasi baru: `php spark make:migration NamaTable`
   - Edit file migrasi di `app/Database/Migrations/`
   - Jalankan migrasi: `php spark migrate`

2. **Menambahkan Model**:

   - Buat model baru: `php spark make:model NamaModel`
   - Define relasi, validasi, dan metode CRUD

3. **Menambahkan Controller**:

   - Buat controller baru: `php spark make:controller NamaController`
   - Extend dari `BaseController` dan implement methods

4. **Menambahkan View**:
   - Buat file template baru di `app/Views/`
   - Gunakan layout yang sudah ada sebagai referensi

### Contoh Command CLI CodeIgniter

```bash
# Membuat controller baru
php spark make:controller Laporan

# Membuat model baru
php spark make:model LaporanModel

# Membuat filter autentikasi
php spark make:filter Auth

# Membuat entity
php spark make:entity Produk

# Melihat daftar rute
php spark routes

# Membuat migration
php spark make:migration CreateLaporanTable

# Menjalankan database seeder
php spark db:seed NamaSeeder
```

### Tips Pengembangan CI4

1. **Lingkungan Development**:

   - Set `CI_ENVIRONMENT = development` di `.env`
   - Aktifkan error display untuk debugging

2. **Debugging**:

   - Gunakan fungsi `dd()` atau `var_dump()` untuk debugging
   - Periksa logs di `writable/logs/`
   - Aktifkan toolbar debugging CI4 di `.env`

3. **Keamanan**:

   - Validasi semua input user dengan `validation` library
   - Gunakan prepared statements untuk query database
   - Implementasi CSRF protection

4. **Performance**:
   - Aktifkan caching jika diperlukan
   - Gunakan query builder untuk operasi database kompleks

## Konfigurasi Server dan Routing

### Konfigurasi Apache

CodeIgniter 4 menyimpan file `index.php` di dalam folder `public/`. Berikut cara konfigurasi Apache untuk CI4:

1. **File .htaccess di root project**

```
# Disable directory browsing
Options -Indexes

# ----------------------------------------------------------------------
# Rewrite engine
# ----------------------------------------------------------------------
RewriteEngine On

# If the file/dir exists, just serve it
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule (.*) - [L]

# Otherwise, serve the index.php
RewriteRule (.*) index.php [L]
```

2. **File .htaccess di folder public/**

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

### Konfigurasi Virtual Host (Opsional)

Untuk pengembangan lokal, Anda dapat menambahkan virtual host:

1. Edit file hosts (Windows: `C:\Windows\System32\drivers\etc\hosts`, Linux/Mac: `/etc/hosts`):

```
127.0.0.1   swalayan.local
```

2. Tambahkan konfigurasi virtual host di Apache:

```
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/swalayan_ci4/public"
    ServerName swalayan.local

    <Directory "C:/xampp/htdocs/swalayan_ci4/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Path Alternatif untuk Repository GitHub

Jika Anda mengunduh dari GitHub dan tidak mengubah nama direktori, gunakan konfigurasi berikut:

```
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/Manajemen_Swalayan_ci4-main/public"
    ServerName swalayan.local

    <Directory "C:/xampp/htdocs/Manajemen_Swalayan_ci4-main/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Routing CodeIgniter 4

Aplikasi ini menggunakan sistem routing CI4 yang terletak di file `app/Config/Routes.php`. Beberapa rute utama:

- `/` - Halaman login
- `/admin/dashboard` - Dashboard admin
- `/kasir/dashboard` - Dashboard kasir
- `/pemilik/dashboard` - Dashboard pemilik

## Cara Mengakses Aplikasi (Perbedaan URL)

Ada dua cara untuk mengakses aplikasi CodeIgniter 4, keduanya valid tapi menggunakan server web yang berbeda:

### 1. Menggunakan Apache/XAMPP (http://localhost/swalayan_ci4/public/)

```
http://localhost/swalayan_ci4/public/
```

atau jika menggunakan nama direktori GitHub:

```
http://localhost/Manajemen_Swalayan_ci4-main/public/
```

- **Bagaimana cara ini bekerja**: Apache menjadi web server yang melayani aplikasi
- **Kelebihan**: Lebih mirip dengan lingkungan produksi, mendukung .htaccess
- **Pengaturan**: Aplikasi ditempatkan di folder htdocs XAMPP
- **Prasyarat**: Apache harus berjalan melalui XAMPP/WAMP

### 2. Menggunakan Server Pengembangan CI4 (http://localhost:8080/)

```
http://localhost:8080/
```

- **Bagaimana cara ini bekerja**: CodeIgniter memiliki server pengembangan built-in
- **Cara menjalankan**: Gunakan perintah `php spark serve` dari folder proyek
- **Kelebihan**: Tidak memerlukan Apache, mudah untuk pengembangan cepat
- **Batasan**: Hanya untuk pengembangan, bukan untuk produksi
- **Port**: Secara default menggunakan port 8080

### Mana yang Sebaiknya Digunakan?

- **Untuk pengembangan awal/cepat**: `php spark serve` (http://localhost:8080/)
- **Untuk pengujian yang lebih mirip produksi**: Apache via XAMPP (http://localhost/swalayan_ci4/public/)
- **Untuk deployment produksi**: Gunakan Apache/Nginx dengan konfigurasi yang tepat

> **Catatan**: Dalam dokumentasi ini, kami lebih banyak mereferensikan URL Apache karena lebih cocok untuk lingkungan yang menyerupai produksi.
