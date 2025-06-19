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

echo "Menjalankan setup libraries..."
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
echo "LANGKAH SELANJUTNYA:"
echo
echo "Jalankan 'php setup_database.php' untuk setup database"
echo "Kemudian akses aplikasi melalui: http://localhost/swalayan_ci4/public/"
echo
echo "Tip: Pastikan untuk mengubah email pemilik dengan menjalankan:"
echo "php setup_owner_email.php"
echo

read -p "Tekan Enter untuk keluar..."
