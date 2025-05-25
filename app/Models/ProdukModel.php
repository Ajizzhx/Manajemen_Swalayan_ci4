<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'produk_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false; 
    protected $protectFields    = true;
    protected $allowedFields    = [
        'produk_id',
        'nama',
        'harga',
        'stok',
        'kategori_id',
        'supplier_id',
        'kode_barcode', 
        'barcode_path', 
        'is_deleted' 
    ];


    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'is_deleted'; 
                                          

  
    public function getProdukWithDetails()
    {
        return $this->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
                    ->join('kategori', 'kategori.kategori_id = produk.kategori_id', 'left')
                    ->join('supplier', 'supplier.supplier_id = produk.supplier_id', 'left')
                    ->where('produk.is_deleted', 0) 
                    ->findAll();
    }

  
    public function findProdukWithDetails($id)
    {
        return $this->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
                    ->join('kategori', 'kategori.kategori_id = produk.kategori_id', 'left')
                    ->join('supplier', 'supplier.supplier_id = produk.supplier_id', 'left')
                    ->where('produk.produk_id', $id)
                    ->where('produk.is_deleted', 0) 
                    ->first(); 
    }
}
