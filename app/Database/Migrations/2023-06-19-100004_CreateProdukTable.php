<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdukTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'produk_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'stok' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'kategori_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'supplier_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'kode_barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'barcode_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_deleted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'default'    => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'on update'  => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        
        $this->forge->addPrimaryKey('produk_id');
        $this->forge->addUniqueKey('kode_barcode');
        $this->forge->addForeignKey('kategori_id', 'kategori', 'kategori_id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('supplier_id', 'supplier', 'supplier_id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('produk');
    }

    public function down()
    {
        $this->forge->dropForeignKey('produk', 'produk_kategori_id_foreign');
        $this->forge->dropForeignKey('produk', 'produk_supplier_id_foreign');
        $this->forge->dropTable('produk');
    }
}
