<?php

/**
 * Helper Script untuk Memperbaiki Masalah Seeder
 * Script ini membantu mengatasi masalah seeder tidak bisa berjalan di beberapa komputer
 */

// Fungsi untuk memastikan folder writable dapat diakses dan ditulis
function check_writable_directory() {
    // Cek folder writable
    $writablePath = __DIR__ . '/writable';
    
    echo "Memeriksa folder writable...\n";
    
    if (!is_dir($writablePath)) {
        echo "Folder writable tidak ditemukan. Mencoba membuat...\n";
        if (!mkdir($writablePath, 0777, true)) {
            echo "GAGAL: Tidak dapat membuat folder writable!\n";
        } else {
            echo "Berhasil membuat folder writable.\n";
        }
    }
    
    // Cek apakah folder dapat ditulis
    if (is_dir($writablePath) && !is_writable($writablePath)) {
        echo "PERINGATAN: Folder writable tidak dapat ditulis!\n";
        echo "Silakan set izin pada folder '$writablePath' menjadi 777 (chmod 777).\n";
        return false;
    } else if (is_dir($writablePath)) {
        echo "Folder writable siap digunakan.\n";
        return true;
    }
    
    return false;
}

// Fungsi untuk memperbaiki masalah owner email
function fix_owner_email() {
    echo "\nMasukkan email owner/pemilik untuk menerima kode OTP: ";
    $email = trim(fgets(STDIN));
    
    if (empty($email)) {
        $email = 'owner@swalayan.com';
        echo "Menggunakan email default: $email\n";
    }
    
    // Simpan email ke database jika database tersedia
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'swalayan_db';
    
    try {
        $conn = new mysqli($host, $username, $password, $database);
        
        if ($conn->connect_error) {
            throw new Exception("Koneksi database gagal: " . $conn->connect_error);
        }
        
        // Cek apakah ada user owner/pemilik
        $sql = "SELECT karyawan_id, email FROM karyawan WHERE role = 'pemilik' OR role = 'owner' LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            // Update email yang sudah ada
            $row = $result->fetch_assoc();
            $karyawanId = $row['karyawan_id'];
            $oldEmail = $row['email'];
            
            $updateSql = "UPDATE karyawan SET email = '$email' WHERE karyawan_id = '$karyawanId'";
            
            if ($conn->query($updateSql) === TRUE) {
                echo "Berhasil memperbarui email owner dari '$oldEmail' ke '$email'\n";
            } else {
                echo "Gagal memperbarui email: " . $conn->error . "\n";
            }
        } else {
            echo "Tidak dapat menemukan user owner/pemilik di database.\n";
            
            // Buat file konfigurasi yang bisa digunakan oleh seeder
            $configContent = "<?php\n";
            $configContent .= "// Auto-generated file - Do not edit manually\n";
            $configContent .= "return [\n";
            $configContent .= "    'owner_email' => '" . addslashes($email) . "'\n";
            $configContent .= "];\n";
            
            $mainPath = __DIR__ . '/writable/temp_owner_email.php';
            $altPath = __DIR__ . '/temp_owner_email.php';
            
            if (file_put_contents($mainPath, $configContent) || file_put_contents($altPath, $configContent)) {
                echo "Email tersimpan untuk digunakan oleh seeder.\n";
                echo "Jalankan 'php spark db:seed KaryawanSeeder' untuk menambahkan user owner.\n";
            } else {
                echo "Gagal menyimpan file konfigurasi email.\n";
            }
        }
        
        $conn->close();
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        
        // Simpan ke file konfigurasi sebagai fallback
        $configContent = "<?php\n";
        $configContent .= "// Auto-generated file - Do not edit manually\n";
        $configContent .= "return [\n";
        $configContent .= "    'owner_email' => '" . addslashes($email) . "'\n";
        $configContent .= "];\n";
        
        file_put_contents(__DIR__ . '/temp_owner_email.php', $configContent);
        echo "Email disimpan untuk digunakan nanti.\n";
    }
}

// Main section
echo "===========================================\n";
echo "Swalayan CI4 - Helper untuk Setup Database \n";
echo "===========================================\n\n";

check_writable_directory();
fix_owner_email();

echo "\nSelesai! Silakan jalankan setup.bat/setup.sh kembali atau jalankan seeder secara manual.\n";
?>
