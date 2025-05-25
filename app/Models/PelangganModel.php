<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table            = 'pelanggan';
    protected $primaryKey       = 'pelanggan_id';
    protected $useAutoIncrement = false; 
    protected $returnType       = 'object'; 
    protected $useSoftDeletes   = false; 
    protected $allowedFields    = [
        'pelanggan_id', 'nama', 'email', 'telepon', 'alamat', 'diskon_persen', 'is_deleted' 
    ];

    // Dates
    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Validation
    protected $validationRules      = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'email' => 'permit_empty|valid_email|max_length[100]|is_unique[pelanggan.email,pelanggan_id,{id}]',
        'telepon' => 'permit_empty|max_length[20]|is_unique[pelanggan.telepon,pelanggan_id,{id}]',
        'alamat' => 'permit_empty|max_length[255]',
        'diskon_persen' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];
    protected $validationMessages   = [
        'nama' => [
            'required' => 'Nama pelanggan harus diisi.',
        ],
        'email' => [
            'is_unique' => 'Email sudah terdaftar.',
        ],
        'telepon' => [
            'is_unique' => 'Nomor telepon sudah terdaftar.',
        ],
        'diskon_persen' => [
            'numeric' => 'Diskon persen harus berupa angka.',
            'greater_than_equal_to' => 'Diskon persen tidak boleh kurang dari 0.',
            'less_than_equal_to' => 'Diskon persen tidak boleh lebih dari 100.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}