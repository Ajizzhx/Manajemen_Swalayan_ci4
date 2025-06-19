<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'transaksi_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'pelanggan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'karyawan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'uang_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'kembalian' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'metode_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['tunai', 'debit', 'kredit', 'qris'],
                'default'    => 'tunai',
            ],
            'total_diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'dibatalkan_oleh_karyawan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'alasan_pembatalan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'tanggal_dibatalkan' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'status_penghapusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'alasan_penolakan_owner' => [
                'type'       => 'TEXT',
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
        
        $this->forge->addPrimaryKey('transaksi_id');
        $this->forge->addForeignKey('pelanggan_id', 'pelanggan', 'pelanggan_id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'karyawan_id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('dibatalkan_oleh_karyawan_id', 'karyawan', 'karyawan_id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        $this->forge->dropForeignKey('transaksi', 'transaksi_pelanggan_id_foreign');
        $this->forge->dropForeignKey('transaksi', 'transaksi_karyawan_id_foreign');
        $this->forge->dropForeignKey('transaksi', 'transaksi_dibatalkan_oleh_karyawan_id_foreign');
        $this->forge->dropTable('transaksi');
    }
}
