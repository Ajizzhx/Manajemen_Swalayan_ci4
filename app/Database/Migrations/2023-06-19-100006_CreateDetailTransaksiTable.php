<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailTransaksiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'detail_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'transaksi_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'produk_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'harga_saat_itu' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'sub_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
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
        
        $this->forge->addPrimaryKey('detail_id');
        $this->forge->addForeignKey('transaksi_id', 'transaksi', 'transaksi_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produk_id', 'produk', 'produk_id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('detail_transaksi');
    }

    public function down()
    {
        $this->forge->dropForeignKey('detail_transaksi', 'detail_transaksi_transaksi_id_foreign');
        $this->forge->dropForeignKey('detail_transaksi', 'detail_transaksi_produk_id_foreign');
        $this->forge->dropTable('detail_transaksi');
    }
}
