<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\KaryawanModel;
use App\Models\PelangganModel;
use App\Models\TransaksiModel;
use App\Models\SupplierModel;

class DashboardController extends BaseController
{
    protected $produkModel;
    protected $kategoriModel;
    protected $karyawanModel;
    protected $supplierModel;
    protected $pelangganModel;
    protected $transaksiModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->karyawanModel = new KaryawanModel();
        $this->supplierModel = new SupplierModel();
        $this->pelangganModel = new PelangganModel();
        $this->transaksiModel = new TransaksiModel();
        helper(['number']); 
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['user_role'] = session()->get('role'); // Tambahkan peran pengguna ke data
        $data['total_produk'] = $this->produkModel->where('is_deleted', 0)->countAllResults();
        $data['total_kategori'] = $this->kategoriModel->where('is_deleted', 0)->countAllResults();
        $data['total_karyawan'] = $this->karyawanModel->where('is_deleted', 0)->countAllResults(); 
        $data['total_supplier'] = $this->supplierModel->where('is_deleted', 0)->countAllResults();
        // Statistik Tambahan

        // Total Penjualan Harian
        $today = date('Y-m-d');
        $pendapatanHarian = $this->transaksiModel
            ->selectSum('total_harga', 'total_pendapatan_hari_ini')
            ->where('DATE(created_at)', $today)
            ->where('is_deleted', 0)
            ->get()->getRow();
        $data['pendapatan_harian'] = $pendapatanHarian->total_pendapatan_hari_ini ?? 0;

        // Produk Stok Rendah 
        $batas_stok_rendah = 5;
        $data['produk_stok_rendah'] = $this->produkModel
            ->where('stok <=', $batas_stok_rendah)
            ->where('is_deleted', 0)
            ->countAllResults();
        $data['batas_stok_rendah'] = $batas_stok_rendah; 

        // Pelanggan Aktif
        $data['pelanggan_aktif'] = $this->pelangganModel->where('is_deleted', 0)->countAllResults();

        // Notifikasi Persetujuan Hapus Transaksi (hanya untuk pemilik)
        $data['transaksi_pending_approval_count'] = 0; // Inisialisasi
        if ($data['user_role'] === 'pemilik') {
            $data['transaksi_pending_approval_count'] = $this->transaksiModel
                ->where('status_penghapusan', 'pending_approval')
                ->where('is_deleted', 0) // Pastikan transaksi belum di-soft delete secara umum
                ->countAllResults();
        }

        return view('Backend/Admin/dashboard', $data);
    }
}