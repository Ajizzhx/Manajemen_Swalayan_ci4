<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KaryawanSeeder extends Seeder
{    public function run()
    {
        // Check if owner already exists
        $existingOwner = $this->db->table('karyawan')
            ->where('role', 'pemilik')
            ->orWhere('role', 'owner')
            ->get()
            ->getRow();
            
        if (!$existingOwner) {
            $ownerEmail = 'owner@swalayan.com';
            
            // Try to read email from temp file created by setup_database.php
            $tempFile = ROOTPATH . 'writable/temp_owner_email.php';
            if (file_exists($tempFile)) {
                $config = include $tempFile;
                if (isset($config['owner_email']) && !empty($config['owner_email'])) {
                    $ownerEmail = $config['owner_email'];
                    echo "Menggunakan email owner dari pengaturan sebelumnya: $ownerEmail\n";
                    // Delete the temp file after using it
                    @unlink($tempFile);
                }
            } else {
                echo "\n\nMasukkan email asli pemilik (owner) untuk menerima kode OTP (biarkan kosong untuk menggunakan default): ";
                // Get input from command line
                $handle = fopen("php://stdin", "r");
                $line = trim(fgets($handle));
                if (!empty($line)) {
                    $ownerEmail = $line;
                }
                fclose($handle);
            }
            
            // Default owner account (created first)
            $data = [
                'karyawan_id' => 'OWN' . uniqid(),
                'nama'        => 'Pemilik Toko',
                'email'       => $ownerEmail,
                'password'    => hash('sha256', 'owner123'),
                'role'        => 'pemilik',
            ];

            $this->db->table('karyawan')->insert($data);
            
            echo "Akun Pemilik (Owner) berhasil dibuat. Email: $ownerEmail, Password: owner123\n";
            echo "PENTING: Email ini akan digunakan untuk menerima kode OTP saat login sebagai owner.\n";
        } else {
            echo "Akun Pemilik (Owner) sudah ada, tidak perlu dibuat ulang.\n";
        }// Check if admin already exists
        $existingAdmin = $this->db->table('karyawan')
            ->where('role', 'admin')
            ->get()
            ->getRow();
            
        if (!$existingAdmin) {
            // Default admin account
            $data = [
                'karyawan_id' => 'ADM' . uniqid(),
                'nama'        => 'Administrator',
                'email'       => 'admin@swalayan.com',
                'password'    => hash('sha256', 'admin123'),
                'role'        => 'admin',
            ];

            $this->db->table('karyawan')->insert($data);
            echo "Akun Administrator berhasil dibuat. Email: admin@swalayan.com, Password: admin123\n";
        } else {
            echo "Akun Administrator sudah ada, tidak perlu dibuat ulang.\n";
        }

        // Check if cashier already exists
        $existingKasir = $this->db->table('karyawan')
            ->where('role', 'kasir')
            ->get()
            ->getRow();
            
        if (!$existingKasir) {
            // Default cashier account
            $data = [
                'karyawan_id' => 'KSR' . uniqid(),
                'nama'        => 'Kasir',
                'email'       => 'kasir@swalayan.com',
                'password'    => hash('sha256', 'kasir123'),
                'role'        => 'kasir',
            ];

            $this->db->table('karyawan')->insert($data);
            echo "Akun Kasir berhasil dibuat. Email: kasir@swalayan.com, Password: kasir123\n";
        } else {
            echo "Akun Kasir sudah ada, tidak perlu dibuat ulang.\n";
        }
        
        echo "Karyawan seeder successfully executed.\n";
    }
}
