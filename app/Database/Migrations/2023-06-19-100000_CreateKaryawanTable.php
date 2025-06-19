<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKaryawanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'karyawan_id' => [
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
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'kasir', 'owner', 'pemilik'],
                'default'    => 'kasir',
            ],
            'is_deleted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'last_login' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'last_activity' => [
                'type'       => 'DATETIME',
                'null'       => true,
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
        
        $this->forge->addPrimaryKey('karyawan_id');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('karyawan');
    }
}
