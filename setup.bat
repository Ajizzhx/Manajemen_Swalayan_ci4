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

echo Menjalankan setup libraries...
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
echo LANGKAH SELANJUTNYA:
echo.
echo Jalankan 'php setup_database.php' untuk setup database
echo Kemudian akses aplikasi melalui: http://localhost/swalayan_ci4/public/
echo.
echo Tip: Pastikan untuk mengubah email pemilik dengan menjalankan:
echo php setup_owner_email.php
echo.

pause
