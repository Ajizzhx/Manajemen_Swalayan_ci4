<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table            = 'transaksi'; 
    protected $primaryKey       = 'transaksi_id';
    protected $useAutoIncrement = false; 
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false; 

    protected $allowedFields    = [
        'transaksi_id',   
        'pelanggan_id',   
        'karyawan_id',
        'total_harga',
        'uang_bayar',
        'kembalian',
        'metode_pembayaran',
        'total_diskon', 
        'is_deleted',
        'dibatalkan_oleh_karyawan_id',
        'alasan_pembatalan',
        'tanggal_dibatalkan',
        'status_penghapusan',
        'alasan_penolakan_owner'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'is_deleted'; 

    // Validation
    protected $validationRules      = [
        'transaksi_id'      => 'required|alpha_dash|is_unique[transaksi.transaksi_id,transaksi_id,{id}]', 
        'karyawan_id'       => 'required', 
        'pelanggan_id'      => 'permit_empty', 
        'total_harga'       => 'required|numeric|greater_than_equal_to[0]',
        'uang_bayar'        => 'required|numeric|greater_than_equal_to[0]',
        'kembalian'         => 'required|numeric',
        'total_diskon'      => 'permit_empty|numeric|greater_than_equal_to[0]',
        'metode_pembayaran' => 'required|in_list[tunai,debit,kredit,qris]',
        
    ];
    protected $validationMessages   = [
        'transaksi_id' => [
            'required'  => 'Kode transaksi harus diisi.',
            'is_unique' => 'Kode transaksi sudah ada.',
            'alpha_dash'=> 'Kode transaksi hanya boleh berisi karakter alpha-numeric, underscore, dan dash.'
        ],
        'karyawan_id' => [
            'is_unique' => 'Kode transaksi sudah ada.',
            'alpha_dash'=> 'Kode transaksi hanya boleh berisi karakter alpha-numeric, underscore, dan dash.'
        ],
        'metode_pembayaran' => [
            'required' => 'Metode pembayaran harus dipilih.',
            'in_list'  => 'Metode pembayaran tidak valid.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}