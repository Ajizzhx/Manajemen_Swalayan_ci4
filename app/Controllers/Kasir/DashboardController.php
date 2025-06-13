<?php

namespace App\Controllers\Kasir;
use App\Controllers\BaseController;
use App\Models\TransaksiModel;

class DashboardController extends BaseController
{
    protected $transaksiModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->transaksiModel = new TransaksiModel();
        helper(['number']);
    }

    public function index()
    {
        $data['title'] = 'Dashboard Kasir'; 
        $kasir_id = $this->session->get('karyawan_id');
        $today = date('Y-m-d');

        
        $data['transaksi_hari_ini'] = $this->transaksiModel
            ->where('karyawan_id', $kasir_id)
            ->where('DATE(created_at)', $today)
            ->where('is_deleted', 0)
            ->countAllResults();

        
        $pendapatan = $this->transaksiModel
            ->selectSum('total_harga', 'total_pendapatan_hari_ini')
            ->where('karyawan_id', $kasir_id)
            ->where('DATE(created_at)', $today)
            ->where('is_deleted', 0)
            ->get()->getRow();
        $data['pendapatan_hari_ini'] = $pendapatan->total_pendapatan_hari_ini ?? 0;

       
        $data['kasir_rejected_request_count'] = $this->transaksiModel
            ->where('karyawan_id', $kasir_id)
            ->where('status_penghapusan', 'rejected')
            ->where('is_deleted', 0)
            ->countAllResults();


        return view('Backend/Kasir/dashboard', $data);
    }

}