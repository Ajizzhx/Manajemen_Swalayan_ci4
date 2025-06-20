# PANDUAN PENYELESAIAN MASALAH SETUP

Dokumen ini berisi panduan penyelesaian masalah yang mungkin terjadi saat setup aplikasi Swalayan CI4.

## Masalah: Seeder Tidak Berjalan / Email Owner Tidak Tersimpan

Jika setelah menjalankan `setup.bat` atau `setup.sh` data seeder tidak masuk ke database (khususnya data pengguna/karyawan), ikuti langkah-langkah berikut:

### Solusi 1: Menggunakan Helper Script

1. Jalankan script helper dengan perintah:
   ```
   php setup_seeder_helper.php
   ```

2. Ikuti petunjuk dan masukkan email pemilik/owner yang diinginkan.

3. Setelah selesai, jalankan seeder secara manual:
   ```
   php spark db:seed InitialDataSeeder
   ```

### Solusi 2: Memperbaiki Izin Folder 'writable'

Untuk pengguna Windows:

1. Buka Command Prompt sebagai Administrator
2. Arahkan ke folder aplikasi: `cd path\ke\swalayan_ci4`
3. Jalankan perintah: `attrib +r +a writable /s /d`
4. Buat folder yang diperlukan:
   ```
   mkdir writable\cache writable\logs writable\session writable\uploads
   ```

Untuk pengguna Linux/Mac:

1. Buka Terminal
2. Arahkan ke folder aplikasi: `cd path/ke/swalayan_ci4`
3. Jalankan perintah: `chmod -R 777 writable/`
4. Buat folder yang diperlukan:
   ```
   mkdir -p writable/cache writable/logs writable/session writable/uploads
   ```

### Solusi 3: Setup Manual

Jika solusi di atas belum berhasil, Anda dapat melakukan setup secara manual:

1. Jalankan `setup_database.php` untuk membuat database dan tabel:
   ```
   php setup_database.php
   ```

2. Jalankan migrasi:
   ```
   php spark migrate
   ```

3. Buat file konfigurasi email owner:
   ```php
   // Simpan dalam file temp_owner_email.php
   <?php
   return [
       'owner_email' => 'email_anda@example.com'
   ];
   ```

4. Jalankan seeder untuk pengguna:
   ```
   php spark db:seed KaryawanSeeder
   ```

5. Jalankan seeder untuk data lain:
   ```
   php spark db:seed InitialDataSeeder
   ```

## Informasi Tambahan

Jika Anda masih mengalami masalah, pastikan:

1. MySQL/MariaDB server berjalan dan dapat diakses dengan username 'root' tanpa password.
2. PHP diinstal dan dapat diakses melalui baris perintah (command line).
3. Ekstensi PHP yang diperlukan sudah diaktifkan (mysqli, intl, json, mbstring).
4. Folder 'writable' dan subfolder-nya dapat ditulis oleh PHP/web server.
