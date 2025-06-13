<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\TransaksiModel; 

class CekProdukController extends BaseController
{
    protected $produkModel;
    protected $transaksiModel; 

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->transaksiModel = new TransaksiModel(); 
        
    }

    public function index()
    {
        $data = [
            'produk_list' => $this->produkModel
                                ->select('produk_id, nama, harga, stok, kode_barcode')
                                ->where('is_deleted', 0)
                                ->orderBy('nama', 'ASC')
                                ->findAll()
        ];
        $data['title'] = 'Daftar Produk (Harga & Stok)';

        $kasir_id = session()->get('karyawan_id');
        $data['kasir_rejected_request_count'] = 0; // Default
        if ($kasir_id) {
            $data['kasir_rejected_request_count'] = $this->transaksiModel
                ->where('karyawan_id', $kasir_id)
                ->where('status_penghapusan', 'rejected')->where('is_deleted', 0)->countAllResults();
        }
        return view('Backend/Kasir/CekProduk/index', $data);
    }


}