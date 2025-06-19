@echo off
echo ========================================
echo   Swalayan POS Setup - Windows Version
echo ========================================
echo.

rem Check for PHP installation
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo PHP tidak ditemukan. Pastikan PHP terinstal dan tersedia di PATH.
    echo Anda dapat mengunduh PHP dari: https://windows.php.net/download/
    pause
    exit /b 1
)

rem Check if MySQL service is running (try to connect with empty password)
echo Memeriksa koneksi MySQL...
mysql -u root -h localhost -e "SELECT 'Connection successful!'" >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo PERINGATAN: Tidak dapat terhubung ke MySQL.
    echo Pastikan MySQL/XAMPP sudah berjalan sebelum melanjutkan.
    echo.
    set /p continue="Apakah Anda ingin melanjutkan instalasi library saja? (y/n): "
    if /i "%continue%" NEQ "y" (
        echo Setup dibatalkan.
        pause
        exit /b 1
    )
    set SKIP_DB=1
) else (
    set SKIP_DB=0
)

echo.
echo LANGKAH 1: Menginstal Library...
echo.
php setup_libraries.php

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo Setup libraries gagal. Silakan periksa pesan error di atas.
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Setup Libraries Berhasil!
echo ========================================
echo.

if %SKIP_DB%==1 (
    echo.
    echo Database setup dilewati karena MySQL tidak tersedia.
    echo Jalankan 'php setup_database.php' setelah MySQL siap.
) else (
    echo.
    echo LANGKAH 2: Membuat Database...
    echo.
    php setup_database.php
    
    if %ERRORLEVEL% NEQ 0 (
        echo.
        echo Setup database gagal. Silakan periksa pesan error di atas.
        pause
        exit /b 1
    )
    
    echo.
    echo ========================================
    echo   Setup Database Berhasil!
    echo ========================================
)

echo.
echo ========================================
echo   LANGKAH SELANJUTNYA:
echo ========================================
echo.
echo 1. Update email pemilik untuk OTP dengan menjalankan:
echo    php setup_owner_email.php
echo.
echo 2. Konfigurasi email untuk pengiriman OTP di:
echo    app/Config/Email.php
echo.
echo 3. Akses aplikasi melalui: 
echo    http://localhost/swalayan_ci4/public/
echo    Jika direktori bernama Manajemen_Swalayan_ci4-main:
echo    http://localhost/Manajemen_Swalayan_ci4-main/public/
echo.

pause
