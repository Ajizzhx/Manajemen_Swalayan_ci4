<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        echo "\n\nMasukkan email asli pemilik (owner) untuk menerima kode OTP (biarkan kosong untuk menggunakan default): ";
        $ownerEmail = 'owner@swalayan.com';
        
        // Get input from command line
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        if (!empty($line)) {
            $ownerEmail = $line;
        }
        fclose($handle);
        
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

        // Default admin account
        $data = [
            'karyawan_id' => 'ADM' . uniqid(),
            'nama'        => 'Administrator',
            'email'       => 'admin@swalayan.com',
            'password'    => hash('sha256', 'admin123'),
            'role'        => 'admin',
        ];

        $this->db->table('karyawan')->insert($data);

        // Default cashier account
        $data = [
            'karyawan_id' => 'KSR' . uniqid(),
            'nama'        => 'Kasir',
            'email'       => 'kasir@swalayan.com',
            'password'    => hash('sha256', 'kasir123'),
            'role'        => 'kasir',
        ];

        $this->db->table('karyawan')->insert($data);
        
        echo "Karyawan seeder successfully executed.\n";
    }
}
