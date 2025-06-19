<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupplierTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'supplier_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'alamat' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'telepon' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
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
        
        $this->forge->addPrimaryKey('supplier_id');
        $this->forge->createTable('supplier');
    }

    public function down()
    {
        $this->forge->dropTable('supplier');
    }
}
