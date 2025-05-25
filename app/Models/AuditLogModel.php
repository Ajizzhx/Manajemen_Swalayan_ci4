<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs'; // Sesuaikan dengan nama tabel log audit Anda
    protected $primaryKey       = 'id'; // Sesuaikan dengan primary key tabel Anda
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; // Bisa juga 'array'
    protected $useSoftDeletes   = false;

    // Kolom yang diizinkan untuk diisi atau diupdate melalui model
    // Sesuaikan dengan kolom-kolom di tabel audit_logs Anda
    protected $allowedFields    = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'created_at' // Jika Anda tidak menggunakan $useTimestamps
    ];

    // Dates
    protected $useTimestamps = true; // Jika tabel Anda memiliki kolom created_at dan updated_at
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at'; // Nama kolom untuk timestamp pembuatan
    protected $updatedField  = ''; // Kosongkan jika tidak ada kolom updated_at untuk log

    // Anda bisa menambahkan validasi jika diperlukan
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
}