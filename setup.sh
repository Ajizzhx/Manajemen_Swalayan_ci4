#!/bin/bash

echo "========================================"
echo "  Swalayan POS Setup - Linux/Mac Version"
echo "========================================"
echo

# Check for PHP installation
if ! command -v php &> /dev/null; then
    echo "PHP tidak ditemukan. Pastikan PHP terinstal dan tersedia di PATH."
    echo "Untuk Ubuntu/Debian: sudo apt-get install php"
    echo "Untuk macOS: brew install php"
    exit 1
fi

# Check if MySQL is running
echo "Memeriksa koneksi MySQL..."
if ! command -v mysql &> /dev/null; then
    echo
    echo "PERINGATAN: MySQL tidak terinstal atau tidak ditemukan."
    echo "Pastikan MySQL/MariaDB sudah terinstal dan berjalan."
    echo
    read -p "Apakah Anda ingin melanjutkan instalasi library saja? (y/n): " continue
    if [ "$continue" != "y" ] && [ "$continue" != "Y" ]; then
        echo "Setup dibatalkan."
        exit 1
    fi
    SKIP_DB=1
else
    if ! mysql -u root -h localhost -e "SELECT 'Connection successful!'" &> /dev/null; then
        echo
        echo "PERINGATAN: Tidak dapat terhubung ke MySQL."
        echo "Pastikan MySQL/XAMPP sudah berjalan sebelum melanjutkan."
        echo
        read -p "Apakah Anda ingin melanjutkan instalasi library saja? (y/n): " continue
        if [ "$continue" != "y" ] && [ "$continue" != "Y" ]; then
            echo "Setup dibatalkan."
            exit 1
        fi
        SKIP_DB=1
    else
        SKIP_DB=0
    fi
fi

echo
echo "LANGKAH 1: Menginstal Library..."
echo
php setup_libraries.php

if [ $? -ne 0 ]; then
    echo
    echo "Setup libraries gagal. Silakan periksa pesan error di atas."
    exit 1
fi

echo
echo "========================================"
echo "  Setup Libraries Berhasil!"
echo "========================================"
echo

if [ "$SKIP_DB" == "1" ]; then
    echo
    echo "Database setup dilewati karena MySQL tidak tersedia."
    echo "Jalankan 'php setup_database.php' setelah MySQL siap."
else
    echo
    echo "LANGKAH 2: Membuat Database..."
    echo
    php setup_database.php
    
    if [ $? -ne 0 ]; then
        echo
        echo "Setup database gagal. Silakan periksa pesan error di atas."
        exit 1
    fi
    
    echo
    echo "========================================"
    echo "  Setup Database Berhasil!"
    echo "========================================"
    
    # Show the actual owner credentials
    php show_owner_credentials.php
fi

echo
echo "========================================"
echo "  LANGKAH SELANJUTNYA:"
echo "========================================"
echo
echo "1. Jika perlu update email pemilik untuk OTP, jalankan:"
echo "   php setup_owner_email.php"
echo
echo "2. Konfigurasi email untuk pengiriman OTP di:"
echo "   app/Config/Email.php"
echo
echo "3. Akses aplikasi melalui:" 
echo "   http://localhost/swalayan_ci4/public/"
echo "   Jika direktori bernama Manajemen_Swalayan_ci4-main:"
echo "   http://localhost/Manajemen_Swalayan_ci4-main/public/"
echo

read -p "Tekan Enter untuk keluar..."
