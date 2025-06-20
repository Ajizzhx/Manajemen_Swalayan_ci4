<?php

/**
 * Swalayan CI4 Database Setup Script
 * This script creates the database and all required tables for the Swalayan CI4 application
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'swalayan_db';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to MySQL server.\n";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if ($conn->query($sql) === TRUE) {
    echo "Database '$database' created or already exists.\n";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);
echo "Selected database '$database'.\n";

// Array of SQL statements to create tables
$tables = [];

// Karyawan table
$tables[] = "CREATE TABLE IF NOT EXISTS `karyawan` (
    `karyawan_id` VARCHAR(36) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'kasir', 'owner', 'pemilik') NOT NULL DEFAULT 'kasir',
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `last_login` DATETIME NULL DEFAULT NULL,
    `last_activity` DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`karyawan_id`),
    UNIQUE INDEX `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Kategori table
$tables[] = "CREATE TABLE IF NOT EXISTS `kategori` (
    `kategori_id` VARCHAR(36) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kategori_id`),
    UNIQUE INDEX `nama_UNIQUE` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Supplier table
$tables[] = "CREATE TABLE IF NOT EXISTS `supplier` (
    `supplier_id` VARCHAR(36) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `alamat` TEXT NULL,
    `telepon` VARCHAR(20) NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Produk table
$tables[] = "CREATE TABLE IF NOT EXISTS `produk` (
    `produk_id` VARCHAR(36) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `harga` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `stok` INT NOT NULL DEFAULT 0,
    `kategori_id` VARCHAR(36) NULL,
    `supplier_id` VARCHAR(36) NULL,
    `kode_barcode` VARCHAR(50) NULL,
    `barcode_path` VARCHAR(255) NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`produk_id`),
    INDEX `fk_produk_kategori_idx` (`kategori_id`),
    INDEX `fk_produk_supplier_idx` (`supplier_id`),
    UNIQUE INDEX `kode_barcode_UNIQUE` (`kode_barcode`),
    CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`kategori_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_produk_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Pelanggan table
$tables[] = "CREATE TABLE IF NOT EXISTS `pelanggan` (
    `pelanggan_id` VARCHAR(36) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `telepon` VARCHAR(20) NULL,
    `alamat` TEXT NULL,
    `diskon_persen` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `poin` INT NOT NULL DEFAULT 0,
    `no_ktp` VARCHAR(16) NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`pelanggan_id`),
    UNIQUE INDEX `email_UNIQUE` (`email`),
    UNIQUE INDEX `no_ktp_UNIQUE` (`no_ktp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Transaksi table
$tables[] = "CREATE TABLE IF NOT EXISTS `transaksi` (
    `transaksi_id` VARCHAR(36) NOT NULL,
    `pelanggan_id` VARCHAR(36) NULL,
    `karyawan_id` VARCHAR(36) NOT NULL,
    `total_harga` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `uang_bayar` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `kembalian` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `metode_pembayaran` ENUM('tunai', 'debit', 'kredit', 'qris') NOT NULL DEFAULT 'tunai',
    `total_diskon` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `dibatalkan_oleh_karyawan_id` VARCHAR(36) NULL,
    `alasan_pembatalan` TEXT NULL,
    `tanggal_dibatalkan` DATETIME NULL,
    `status_penghapusan` VARCHAR(30) NULL,
    `alasan_penolakan_owner` TEXT NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`transaksi_id`),
    INDEX `fk_transaksi_pelanggan_idx` (`pelanggan_id`),
    INDEX `fk_transaksi_karyawan_idx` (`karyawan_id`),
    INDEX `fk_transaksi_pembatalan_idx` (`dibatalkan_oleh_karyawan_id`),
    CONSTRAINT `fk_transaksi_pelanggan` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`pelanggan_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_transaksi_karyawan` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`karyawan_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_transaksi_pembatalan` FOREIGN KEY (`dibatalkan_oleh_karyawan_id`) REFERENCES `karyawan` (`karyawan_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Detail Transaksi table
$tables[] = "CREATE TABLE IF NOT EXISTS `detail_transaksi` (
    `detail_id` VARCHAR(36) NOT NULL,
    `transaksi_id` VARCHAR(36) NOT NULL,
    `produk_id` VARCHAR(36) NOT NULL,
    `jumlah` INT NOT NULL,
    `harga_saat_itu` DECIMAL(12,2) NOT NULL,
    `sub_total` DECIMAL(12,2) NOT NULL,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`detail_id`),
    INDEX `fk_detail_transaksi_idx` (`transaksi_id`),
    INDEX `fk_detail_produk_idx` (`produk_id`),
    CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`transaksi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_detail_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Expenses table
$tables[] = "CREATE TABLE IF NOT EXISTS `expenses` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `tanggal` DATE NOT NULL,
    `kategori` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT NULL,
    `jumlah` DECIMAL(12,2) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Audit Logs table
$tables[] = "CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` VARCHAR(36) NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `fk_audit_user_idx` (`user_id`),
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `karyawan` (`karyawan_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

// Execute all SQL statements
$success = true;
foreach ($tables as $sql) {
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating table: " . $conn->error . "\n";
        echo "SQL: " . $sql . "\n";
        $success = false;
        break;
    }
}

if ($success) {
    echo "All tables created successfully!\n";
    
    // Create a config file to store the owner email for later use by seeder
    echo "\n\nMasukkan email asli pemilik (owner) untuk menerima kode OTP: ";
    $ownerEmail = '';
    
    if (PHP_SAPI === 'cli') {
        // If run from command line
        $ownerEmail = trim(fgets(STDIN));
    } else {
        // If run from browser, use a default but remind user to change it
        $ownerEmail = 'owner@swalayan.com';
        echo "<p style='color:red;font-weight:bold;'>PENTING: Gunakan halaman profil owner setelah login untuk mengubah email ke alamat email yang valid untuk menerima OTP.</p>";
    }
    
    // Store the owner email in a temporary file for the seeder to use
    if (!empty($ownerEmail)) {
        $configContent = "<?php\n";
        $configContent .= "// Auto-generated file - Do not edit manually\n";
        $configContent .= "return [\n";
        $configContent .= "    'owner_email' => '" . addslashes($ownerEmail) . "'\n";
        $configContent .= "];\n";
          $tempFilePath = __DIR__ . '/writable/temp_owner_email.php';
        
        // Pastikan direktori writable ada dan dapat ditulis
        if (!is_dir(dirname($tempFilePath))) {
            mkdir(dirname($tempFilePath), 0777, true);
        }
        
        // Coba tulis file
        if (file_put_contents($tempFilePath, $configContent)) {
            echo "Owner email saved for the seeder.\n";
        } else {
            echo "PERINGATAN: Tidak dapat menyimpan email owner ke file sementara.\n";
            echo "Pastikan folder 'writable' dapat ditulis.\n";
            
            // Simpan ke file alternatif jika writable tidak dapat diakses
            $altTempPath = __DIR__ . '/temp_owner_email.php';
            if (file_put_contents($altTempPath, $configContent)) {
                echo "Email owner disimpan di lokasi alternatif.\n";
            }
        }
    }
    
    echo "Database setup complete. Default users will be created by the seeder.\n";
}

// Close connection
$conn->close();

echo "\n\nDatabase setup complete. You can now use the application.\n";
?>
