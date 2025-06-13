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
        'pelanggan_id', 'nama', 'email', 'telepon', 'alamat', 'diskon_persen', 'is_deleted', 'poin', 'no_ktp' 
    ];

    // Dates
    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Validation
    protected $validationRules      = [
        'nama' => 'required|alpha_space|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|max_length[100]|is_unique[pelanggan.email,pelanggan_id,{id}]',
        'no_ktp' => 'required|numeric|exact_length[16]|is_unique[pelanggan.no_ktp,pelanggan_id,{id}]', // KTP number must be exactly 16 digits
        'telepon' => 'permit_empty|numeric|max_length[20]|is_unique[pelanggan.telepon,pelanggan_id,{id}]',
        'alamat' => 'permit_empty|max_length[255]',
        'diskon_persen' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'poin' => 'permit_empty|numeric|greater_than_equal_to[0]'
    ];
    protected $validationMessages   = [
        'nama' => [
            'required' => 'Nama pelanggan harus diisi.',
            'alpha_space' => 'Nama pelanggan hanya boleh berisi karakter alfabet dan spasi.',
            
        ],
        'email' => [
            'required' => 'Email pelanggan harus diisi.',
            'is_unique' => 'Email sudah terdaftar.',
        ],        'no_ktp' => [
            'required' => 'No KTP wajib diisi.',
            'numeric' => 'No KTP hanya boleh berisi angka.',
            'is_unique' => 'No KTP sudah terdaftar sebagai member lain.',
            'exact_length' => 'No KTP harus terdiri dari 16 digit (masukkan semua 16 angka tanpa spasi atau karakter lain).'
        ],
        'telepon' => [
            'is_unique' => 'Nomor telepon sudah terdaftar.',
            'numeric' => 'Nomor telepon hanya boleh berisi angka.'
        ],
        'diskon_persen' => [
            'numeric' => 'Diskon persen harus berupa angka.',
            'greater_than_equal_to' => 'Diskon persen tidak boleh kurang dari 0.',
            'less_than_equal_to' => 'Diskon persen tidak boleh lebih dari 100.'
        ],
        'poin' => [
            'numeric' => 'Poin harus berupa angka.',
            'greater_than_equal_to' => 'Poin tidak boleh kurang dari 0.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}