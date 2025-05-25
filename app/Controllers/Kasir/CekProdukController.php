<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel; 

class CekProdukController extends BaseController
{
    protected $produkModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Produk (Harga & Stok)',
            'produk_list' => $this->produkModel
                                ->select('produk_id, nama, harga, stok, kode_barcode')
                                ->where('is_deleted', 0)
                                ->orderBy('nama', 'ASC')
                                ->findAll()
        ];
        return view('Backend/Kasir/CekProduk/index', $data);
    }


}