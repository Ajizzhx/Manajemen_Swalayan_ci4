<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePelangganTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pelanggan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'telepon' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'alamat' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'diskon_persen' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'poin' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'no_ktp' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
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
        
        $this->forge->addPrimaryKey('pelanggan_id');
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('no_ktp');
        $this->forge->createTable('pelanggan');
    }

    public function down()
    {
        $this->forge->dropTable('pelanggan');
    }
}
