<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        // Check if we're in a transaction and creating the first record for audit_logs
        // This helps avoid foreign key issues
        $this->db->query("SET FOREIGN_KEY_CHECKS=0");
        
        // Check if owner already exists
        $existingOwner = $this->db->table('karyawan')
            ->where('role', 'pemilik')
            ->orWhere('role', 'owner')
            ->get()
            ->getRow();
              if (!$existingOwner) {
            // Default untuk email owner dan nama
            $ownerEmail = 'owner@swalayan.com';
            $ownerName = 'Owner Swalayan';
            
            // ============================================
            // BAGIAN 1: COBA DAPATKAN EMAIL DARI CLI ARGS
            // ============================================
            global $argv;
            if (isset($argv) && count($argv) > 1) {
                for ($i = 1; $i < count($argv); $i++) {
                    if (strpos($argv[$i], '@') !== false && filter_var($argv[$i], FILTER_VALIDATE_EMAIL)) {
                        $ownerEmail = $argv[$i];
                        echo "Menggunakan email owner dari CLI parameter: $ownerEmail\n";
                        break;
                    }
                }
            }
            
            // ==================================================
            // BAGIAN 2: COBA DAPATKAN EMAIL DARI FILE SEMENTARA
            // ==================================================
            $tempFile = ROOTPATH . 'writable/temp_owner_email.php';
            if (file_exists($tempFile)) {
                try {
                    $config = include $tempFile;
                    if (isset($config['owner_email']) && !empty($config['owner_email'])) {
                        $ownerEmail = $config['owner_email'];
                        echo "Menggunakan email owner dari file konfigurasi: $ownerEmail\n";
                        
                        if (isset($config['owner_name']) && !empty($config['owner_name'])) {
                            $ownerName = $config['owner_name'];
                        }
                        
                        @unlink($tempFile); // Hapus file setelah digunakan
                    }
                } catch (\Throwable $e) {
                    // Abaikan error jika file tidak valid
                }
            } else {
                // Coba lokasi alternatif
                $altFile = ROOTPATH . 'temp_owner_email.php';
                if (file_exists($altFile)) {
                    try {
                        $config = include $altFile;
                        if (isset($config['owner_email']) && !empty($config['owner_email'])) {
                            $ownerEmail = $config['owner_email'];
                            echo "Menggunakan email owner dari file konfigurasi alternatif: $ownerEmail\n";
                            @unlink($altFile);
                        }
                    } catch (\Throwable $e) {
                        // Abaikan error jika file tidak valid
                    }
                }
            }
            
            // ==========================================
            // BAGIAN 3: TANYA MELALUI COMMAND LINE
            // ==========================================
            if (PHP_SAPI === 'cli') {
                echo "\nMasukkan email asli pemilik (owner) untuk menerima kode OTP (biarkan kosong untuk gunakan $ownerEmail): ";
                $handle = fopen("php://stdin", "r");
                $input = trim(fgets($handle));
                if (!empty($input)) {
                    $ownerEmail = $input;
                }
                fclose($handle);
            }
            
            // ==========================================
            // BAGIAN 4: SIMPAN DATA KE DATABASE
            // ==========================================            // Buat akun owner/pemilik
            $data = [
                'karyawan_id' => 'OWN' . uniqid(),
                'nama'        => $ownerName,
                'email'       => $ownerEmail,
                'password'    => hash('sha256', 'owner123'),
                'role'        => 'pemilik',
            ];

            $this->db->table('karyawan')->insert($data);
            echo "Akun Pemilik (Owner) berhasil dibuat. Email: $ownerEmail, Password: owner123\n";
            echo "PENTING: Email ini akan digunakan untuk menerima kode OTP saat login sebagai owner.\n";
              // File owner_email_used.php sudah tidak diperlukan karena tidak dibaca di tempat lain
        } else {
            echo "Akun Pemilik (Owner) sudah ada, tidak perlu dibuat ulang.\n";
        }

        // Check if admin already exists
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
        
        // Re-enable foreign key checks
        $this->db->query("SET FOREIGN_KEY_CHECKS=1");
    }
}
