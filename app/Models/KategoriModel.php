<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'kategori_id';
    protected $useAutoIncrement = false; 
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false; 
    protected $protectFields    = true;
    protected $allowedFields    = ['kategori_id', 'nama', 'is_deleted']; 

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'is_deleted'; 
}
