# Instruksi Fix Role Owner to Pemilik

## Masalah

Swalayan CI4 telah memperbarui kode untuk menggunakan role 'pemilik' sebagai pengganti 'owner', namun database mungkin masih menggunakan role 'owner'. Ini menyebabkan login owner gagal karena ketidaksesuaian antara role di database dan kode aplikasi.

## Solusi

Kami telah menyediakan script `fix_owner_role.php` untuk memperbaiki masalah ini dengan cepat tanpa perlu mengatur ulang database. Script ini akan:

1. Mengubah definisi kolom `role` di tabel `karyawan` untuk mendukung nilai 'pemilik'
2. Mengupdate semua user dengan role 'owner' menjadi 'pemilik'

## Langkah-langkah Eksekusi

### Cara 1: Via Command Line

1. Buka Command Prompt (cmd) atau PowerShell
2. Arahkan ke direktori proyek:
   ```
   cd c:\xampp\htdocs\swalayan_ci4
   ```
3. Jalankan script dengan perintah:
   ```
   php fix_owner_role.php
   ```

### Cara 2: Via Browser

1. Buka browser
2. Akses URL: http://localhost/swalayan_ci4/fix_owner_role.php
3. Script akan otomatis berjalan dan menampilkan hasil

## Setelah Eksekusi

- Pastikan untuk login kembali menggunakan akun owner (sekarang dengan role 'pemilik')
- Jika saat login Anda diminta kode OTP tetapi tidak menerimanya, pastikan email yang terdaftar adalah email aktif Anda yang bisa menerima email
- Anda dapat mengubah email untuk OTP di menu "Profil & Email OTP" setelah login

## Kebutuhan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- XAMPP yang berjalan dengan layanan Apache dan MySQL aktif
