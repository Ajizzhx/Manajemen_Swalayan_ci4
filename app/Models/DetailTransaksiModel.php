<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table            = 'detail_transaksi'; 
    protected $primaryKey       = 'detail_id';
    protected $useAutoIncrement = false; 
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false; 

    protected $allowedFields    = [
        'detail_id', 'transaksi_id', 'produk_id', 'jumlah', 'harga_saat_itu', 'sub_total', 'is_deleted' 
    ];

    // Dates
    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'is_deleted';
    
    // Validation
    protected $validationRules      = [
        'transaksi_id'   => 'required', 
        'produk_id'      => 'required', 
        'jumlah'         => 'required|integer|greater_than[0]',
        'harga_saat_itu' => 'required|numeric',
        'sub_total'      => 'required|numeric',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}