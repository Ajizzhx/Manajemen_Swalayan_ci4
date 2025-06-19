# Cara Mengatur Email Pemilik untuk Autentikasi OTP

## Langkah-langkah Setup Email Gmail untuk OTP Login Pemilik

Sistem Swalayan POS menggunakan email pemilik untuk mengirimkan kode OTP (One-Time Password) saat login sebagai pemilik. Fitur ini meningkatkan keamanan akun pemilik yang memiliki akses ke semua data termasuk keuangan. Untuk mengaktifkan fitur ini, Anda perlu melakukan beberapa langkah konfigurasi pada akun Gmail yang akan digunakan.

### 1. Persiapan Akun Gmail

1. Pastikan Anda memiliki akun Gmail yang akan digunakan sebagai email pengirim OTP
   - Ini bisa akun Gmail yang sama dengan email pemilik atau akun terpisah
   - Akun Gmail ini akan digunakan sebagai PENGIRIM email OTP (bukan penerima)
2. Pastikan akun Gmail tersebut sudah mengaktifkan Two-Factor Authentication (2FA):
   - Buka [Google Account Security](https://myaccount.google.com/security)
   - Klik "2-Step Verification" dan aktifkan fitur ini

### 2. Membuat App Password di Google Account

1. Buka [Google Account Settings](https://myaccount.google.com/)
2. Pilih "Security" pada menu sebelah kiri
3. Di bagian "Signing in to Google", cari "2-Step Verification" dan klik pada opsi tersebut
4. Scroll ke bawah dan cari "App passwords"
5. Masukkan password akun Google Anda jika diminta
6. Pada halaman "App passwords":
   - Di dropdown "Select app", pilih "Mail" atau "Other" (dan beri nama "Swalayan POS")
   - Di dropdown "Select device", pilih "Other" dan masukkan nama (misalnya "Swalayan POS Server")
   - Klik "GENERATE"
7. Google akan menampilkan password 16 karakter yang dibagi menjadi 4 kelompok
8. Salin password tersebut (Anda tidak akan dapat melihatnya lagi nanti)

### 3. Konfigurasi Email di Aplikasi Swalayan POS

1. Buka file konfigurasi email di: `app/Config/Email.php`
2. Edit file tersebut dan ubah konfigurasi berikut:

```php
public string $protocol = 'smtp';
public string $SMTPHost = 'smtp.gmail.com';
public string $SMTPPort = 465;
public string $SMTPUser = 'your-gmail-address@gmail.com'; // Ganti dengan email Gmail Anda
public string $SMTPPass = 'xxxx xxxx xxxx xxxx'; // Ganti dengan App Password yang didapatkan dari langkah sebelumnya
public string $SMTPCrypto = 'ssl';
public string $fromEmail = 'your-gmail-address@gmail.com'; // Ganti dengan email Gmail yang sama
public string $fromName = 'Swalayan POS - Verifikasi Pemilik';
```

3. Simpan perubahan
4. **PENTING**: Email di atas ($SMTPUser dan $fromEmail) adalah email yang akan MENGIRIMKAN kode OTP, bukan email PENERIMA OTP

### 4. Mengubah Email Pemilik untuk Menerima OTP

Anda memiliki TIGA cara untuk mengubah email pemilik:

#### Cara 1: Menggunakan Script Khusus (TERMUDAH)
1. Jalankan script berikut di command line:
   ```
   php setup_owner_email.php
   ```
   atau melalui browser: http://localhost/swalayan_ci4/setup_owner_email.php
2. Masukkan email aktif yang ingin menerima kode OTP
3. Script akan otomatis mengupdate email di database

#### Cara 2: Mengubah Di Aplikasi Web
1. Login sebagai pemilik ke dalam sistem 
   (Jika belum bisa login karena masalah OTP, gunakan akun admin dulu)
2. Klik menu "Area Pemilik" > "Profil & Email OTP"
3. Pada form yang tersedia, masukkan email aktif yang ingin digunakan menerima OTP
4. Klik "Simpan Perubahan"
5. Email baru Anda akan langsung digunakan untuk pengiriman OTP pada login berikutnya

#### Cara 3: Mengubah Melalui Database (phpMyAdmin)
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Pilih database `swalayan_db`
3. Buka tabel `karyawan`
4. Cari record dengan role='pemilik' atau role='owner'
5. Klik Edit dan ubah kolom `email` ke email aktif Anda

> **PENTING**: Email pemilik adalah email yang akan MENERIMA kode OTP. Pastikan menggunakan email yang aktif dan dapat diakses, karena kode OTP akan dikirim ke email ini saat Anda login sebagai pemilik.

### 5. Test Pengiriman OTP

1. Coba login ke sistem dengan akun owner
2. Sistem akan mengirimkan kode OTP ke email yang terdaftar untuk akun owner
3. Periksa email dan masukkan kode OTP yang diterima

### 5. Test Pengiriman OTP

1. Logout dari sistem (jika sedang login)
2. Coba login sebagai pemilik dengan email yang sudah diperbarui
3. Masukkan password yang benar (default: owner123)
4. Sistem akan mengirimkan kode OTP ke email pemilik yang sudah diatur
5. Periksa email Anda dan masukkan kode OTP yang diterima di halaman verifikasi

### Troubleshooting

Jika Anda mengalami masalah dalam pengiriman OTP, periksa hal-hal berikut:

#### 1. Memastikan Email Config Sudah Benar
- Periksa kembali file `app/Config/Email.php`
- Pastikan App Password Gmail sudah benar (16 karakter dengan spasi)
- Pastikan alamat email SMTP ($SMTPUser) dan email pengirim ($fromEmail) sudah benar

#### 2. Jika OTP Tidak Terkirim
- Periksa file log di `writable/logs/` untuk melihat pesan error
- Periksa folder Spam/Junk di email pemilik
- Pastikan email pemilik di database sudah benar (tabel karyawan, kolom email)

#### 3. Masalah Port/Firewall
- Pastikan server Anda mengizinkan koneksi keluar ke smtp.gmail.com pada port 465 (SSL) atau 587 (TLS)
- Jika menggunakan port 587, ubah konfigurasi berikut di Email.php:
  ```php
  public string $SMTPPort = 587;
  public string $SMTPCrypto = 'tls';
  ```

#### 4. Masalah Login sebagai Pemilik
- Jika muncul error "Column 'user_id' cannot be null", jalankan script `php fix_auth.php`
- Jika login gagal meski password benar, jalankan `php fix_owner_role.php`

### Catatan Keamanan

- App Password memberikan akses ke akun Gmail Anda untuk mengirim email
- Jaga kerahasiaan App Password seperti Anda menjaga password reguler
- Jika tidak lagi digunakan, hapus App Password dari pengaturan Google Account Anda
- Perhatikan bahwa sistem ini menggunakan dua email berbeda:
  1. Email PENGIRIM OTP (diatur di Email.php) - akun yang perlu App Password
  2. Email PEMILIK (diubah dengan setup_owner_email.php) - akun yang menerima OTP
