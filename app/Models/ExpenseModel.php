<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table            = 'expenses'; // Nama tabel di database Anda
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false; // Set true jika Anda menggunakan soft delete

    protected $allowedFields    = ['tanggal', 'kategori', 'deskripsi', 'jumlah'];

    // Dates
    protected $useTimestamps = true; // Otomatis mengisi created_at dan updated_at
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // Aktifkan jika menggunakan soft delete

    // Validation
    protected $validationRules      = [
        'tanggal'   => 'required|valid_date',
        'kategori'  => 'required|max_length[100]',
        'deskripsi' => 'permit_empty|max_length[255]',
        'jumlah'    => 'required|numeric|greater_than[0]',
    ];
    protected $validationMessages   = [
        'jumlah' => [
            'greater_than' => 'Jumlah pengeluaran harus lebih besar dari 0.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getExpensesByPeriod($startDate, $endDate)
    {
        return $this->where('tanggal >=', $startDate)
                    ->where('tanggal <=', $endDate)
                    ->orderBy('tanggal', 'ASC')
                    ->findAll();
    }
}