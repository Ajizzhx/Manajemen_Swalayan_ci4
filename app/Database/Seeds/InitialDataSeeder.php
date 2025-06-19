<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $this->call('KaryawanSeeder');
        
        // Add example kategori
        $kategoris = [
            ['kategori_id' => 'KTG' . uniqid(), 'nama' => 'Makanan'],
            ['kategori_id' => 'KTG' . uniqid(), 'nama' => 'Minuman'],
            ['kategori_id' => 'KTG' . uniqid(), 'nama' => 'Peralatan Rumah Tangga'],
            ['kategori_id' => 'KTG' . uniqid(), 'nama' => 'Elektronik'],
            ['kategori_id' => 'KTG' . uniqid(), 'nama' => 'Toiletries']
        ];
        
        foreach ($kategoris as $kategori) {
            $this->db->table('kategori')->insert($kategori);
        }
        
        // Add example suppliers
        $suppliers = [
            ['supplier_id' => 'SUP' . uniqid(), 'nama' => 'PT Supplier Utama', 'alamat' => 'Jl. Utama No. 123', 'telepon' => '021-5551234'],
            ['supplier_id' => 'SUP' . uniqid(), 'nama' => 'CV Mitra Sejati', 'alamat' => 'Jl. Mitra No. 45', 'telepon' => '021-5559876'],
            ['supplier_id' => 'SUP' . uniqid(), 'nama' => 'PT Produk Indonesia', 'alamat' => 'Jl. Indonesia Raya No. 17', 'telepon' => '021-5557890'],
        ];
        
        foreach ($suppliers as $supplier) {
            $this->db->table('supplier')->insert($supplier);
        }
        
        echo "Initial data seeder successfully executed.\n";
    }
}
