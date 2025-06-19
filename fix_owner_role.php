<?php

/**
 * Swalayan CI4 Owner Role Fix Script
 * This script updates the 'owner' role to 'pemilik' in the database
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'swalayan_db';

echo "Memulai proses update role owner ke pemilik...\n\n";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Terhubung ke database.\n\n";

try {
    // Check if column exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'karyawan'");
    if ($checkTable->num_rows == 0) {
        throw new Exception("Tabel karyawan tidak ditemukan!");
    }

    // Check if enum contains 'pemilik'
    $checkColumn = $conn->query("SHOW COLUMNS FROM `karyawan` LIKE 'role'");
    if ($checkColumn->num_rows > 0) {
        $row = $checkColumn->fetch_assoc();
        $type = $row['Type'];
        
        echo "Tipe data kolom role saat ini: " . $type . "\n";
        
        // ALTER the column to add 'pemilik' if it doesn't exist
        if (strpos($type, "pemilik") === false) {
            $conn->query("ALTER TABLE `karyawan` MODIFY COLUMN `role` ENUM('admin', 'kasir', 'owner', 'pemilik') NOT NULL DEFAULT 'kasir'");
            echo "Kolom role diupdate untuk menambahkan opsi 'pemilik'.\n";
        }
    } else {
        throw new Exception("Kolom 'role' tidak ditemukan di tabel karyawan!");
    }

    // Update owner to pemilik
    $result = $conn->query("UPDATE `karyawan` SET `role` = 'pemilik' WHERE `role` = 'owner'");
    
    if ($conn->affected_rows > 0) {
        echo "Berhasil mengubah " . $conn->affected_rows . " akun dari role 'owner' menjadi 'pemilik'.\n";
    } else {
        echo "Tidak ada akun dengan role 'owner' yang perlu diubah.\n";
    }
    
    echo "\n\nProses update selesai!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Close connection
$conn->close();

?>
