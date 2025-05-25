<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\KaryawanModel;

class LaporanController extends BaseController
{
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $db;

    protected $karyawanModel; 

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->karyawanModel = new KaryawanModel(); 
        $this->db = \Config\Database::connect();
        helper(['url', 'number', 'date', 'custom']); 
    }

    // --- Riwayat Transaksi (Admin View) ---
    public function transaksi()
    {
        $data['title'] = 'Laporan Riwayat Transaksi';

        $builder = $this->db->table('transaksi as t');
        
        $builder->select("t.*, COALESCE(p.nama, 'Pelanggan Umum') as nama_pelanggan, k.nama as nama_kasir", false);
        $builder->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left');
        $builder->join('karyawan k', 'k.karyawan_id = t.karyawan_id', 'left');
        $builder->where('t.is_deleted', 0);

        
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $kasir_id = $this->request->getGet('kasir_id');
        $metode_pembayaran = $this->request->getGet('metode_pembayaran');
        $search_id_transaksi = $this->request->getGet('search_id_transaksi');

        // Filter berdasarkan ID Transaksi
        if ($search_id_transaksi) {
            $builder->like('t.transaksi_id', $search_id_transaksi, 'both'); // Cari di kolom transaksi_id
        }

        // Filter berdasarkan tanggal
        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('DATE(t.created_at) >=', $tanggal_awal);
            $builder->where('DATE(t.created_at) <=', $tanggal_akhir);
        } elseif ($tanggal_awal) {
            $builder->where('DATE(t.created_at)', $tanggal_awal);
        }

        // Filter berdasarkan kasir
        if ($kasir_id) {
            $builder->where('t.karyawan_id', $kasir_id);
        }
 
        // Filter berdasarkan metode pembayaran
        if ($metode_pembayaran) {
            $builder->where('t.metode_pembayaran', $metode_pembayaran);
        }
        $builder->orderBy('t.created_at', 'DESC');

        $data['riwayat_transaksi'] = $builder->get()->getResultArray();
        
        // Data untuk filter dropdowns/inputs
        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;
        $data['selected_kasir_id'] = $kasir_id;
        $data['selected_metode_pembayaran'] = $metode_pembayaran;
        $data['selected_id_transaksi'] = $search_id_transaksi;
        $data['kasir_list'] = $this->karyawanModel->where('role', 'kasir')->where('is_deleted', 0)->findAll();
        $data['current_query_string'] = http_build_query($this->request->getGet());
        $data['metode_pembayaran_list'] = $this->transaksiModel->select('metode_pembayaran')->distinct()->where('is_deleted', 0)->where('metode_pembayaran IS NOT NULL')->where("metode_pembayaran != ''")->findAll();

        return view('Backend/Admin/Laporan/transaksi_index', $data);
    }
     // --- Export Riwayat Transaksi ke Excel (CSV) ---
    public function exportTransaksiExcel()
    {
        // Ambil filter dari request GET
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $kasir_id = $this->request->getGet('kasir_id');
        $metode_pembayaran = $this->request->getGet('metode_pembayaran');
        $search_id_transaksi = $this->request->getGet('search_id_transaksi');

        $builder = $this->db->table('transaksi as t');
        $builder->select("t.transaksi_id, t.created_at, COALESCE(p.nama, 'Pelanggan Umum') as nama_pelanggan, k.nama as nama_kasir, t.total_harga, t.uang_bayar, t.kembalian, t.metode_pembayaran", false);
        $builder->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left');
        $builder->join('karyawan k', 'k.karyawan_id = t.karyawan_id', 'left');
        $builder->where('t.is_deleted', 0);

        // Terapkan filter yang sama
        if ($search_id_transaksi) {
            $builder->like('t.transaksi_id', $search_id_transaksi, 'both');
        }
        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('DATE(t.created_at) >=', $tanggal_awal);
            $builder->where('DATE(t.created_at) <=', $tanggal_akhir);
        } elseif ($tanggal_awal) {
            $builder->where('DATE(t.created_at)', $tanggal_awal);
        }
        if ($kasir_id) {
            $builder->where('t.karyawan_id', $kasir_id);
        }
        if ($metode_pembayaran) {
            $builder->where('t.metode_pembayaran', $metode_pembayaran);
        }

        $builder->orderBy('t.created_at', 'DESC');

        $transaksiData = $builder->get()->getResultArray();

        // Siapkan data untuk CSV
        $filename = 'laporan_transaksi_admin_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header CSV
        fputcsv($output, ['ID/Kode Transaksi', 'Tanggal & Waktu', 'Member', 'Kasir', 'Total Harga', 'Uang Bayar', 'Kembalian', 'Metode Bayar']);

        // Data CSV
        $bulan_indo = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

        foreach ($transaksiData as $transaksi) {
            $timestamp = strtotime($transaksi['created_at']);
            $tanggal_formatted = date('d', $timestamp) . ' ' . $bulan_indo[(int)date('n', $timestamp)] . ' ' . date('Y, H:i', $timestamp);

            fputcsv($output, [
                $transaksi['transaksi_id'],
                $tanggal_formatted,
                $transaksi['nama_pelanggan'] ?: 'Umum',
                $transaksi['nama_kasir'] ?: 'N/A',
                $transaksi['total_harga'], 
                $transaksi['uang_bayar'],
                $transaksi['kembalian'],
                ucwords(str_replace('_', ' ', $transaksi['metode_pembayaran'] ?? 'N/A')),
            ]);
        }

        fclose($output);
        exit(); 
    }

    public function detailTransaksi($transaksi_id = null)
    {
        if (!$transaksi_id) {
            session()->setFlashdata('error', 'ID Transaksi tidak valid.');
            return redirect()->back();
        }

        $transaksi = $this->db->table('transaksi as t')
            ->select("t.*, COALESCE(p.nama, 'Pelanggan Umum') as nama_pelanggan, p.diskon_persen as diskon_pelanggan_saat_transaksi, k.nama as nama_kasir", false) // Tambah p.diskon_persen
            ->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left')
            ->join('karyawan k', 'k.karyawan_id = t.karyawan_id', 'left')
            ->where('t.transaksi_id', $transaksi_id)
            ->where('t.is_deleted', 0)
            ->get()->getRowArray();

        if (!$transaksi) {
            session()->setFlashdata('error', 'Detail transaksi tidak ditemukan.');
            return redirect()->to(site_url('admin/laporan/transaksi'));
        }

        $detailItems = $this->db->table('detail_transaksi as dt')
            ->select('dt.*, pr.nama as nama_produk, pr.kode_barcode')
            ->join('produk pr', 'pr.produk_id = dt.produk_id') 
            ->where('dt.transaksi_id', $transaksi_id)
            ->where('dt.is_deleted', 0)
            ->get()->getResultArray();

        $data['title'] = 'Detail Transaksi #' . esc($transaksi['transaksi_id']);
        $data['transaksi'] = $transaksi;
        $data['detail_items'] = $detailItems;

        return view('Backend/Admin/Laporan/transaksi_detail', $data);
    }

    // --- Hitung Total Pendapatan Harian ---
    public function pendapatan()
    {
        $data['title'] = 'Laporan Pendapatan';
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d'); // Default hari ini
        $tanggal_awal_range = $this->request->getGet('tanggal_awal_range');
        $tanggal_akhir_range = $this->request->getGet('tanggal_akhir_range');

        log_message('debug', '[LaporanPendapatan] Filter Diterima: tanggal=' . $tanggal . ', tanggal_awal_range=' . $tanggal_awal_range . ', tanggal_akhir_range=' . $tanggal_akhir_range);

        $totalPendapatan = 0;
        $data['pendapatan_per_hari'] = []; // Inisialisasi

        if ($tanggal_awal_range && $tanggal_akhir_range) {
            
            log_message('debug', '[LaporanPendapatan] Mode: Rentang Tanggal.'); 
            $rangeBuilder = $this->transaksiModel->select('SUM(total_harga) as total, DATE(created_at) as tanggal')
                                                ->where('transaksi.is_deleted', 0) 
                                                ->where('DATE(created_at) >=', $tanggal_awal_range)
                                                ->where('DATE(created_at) <=', $tanggal_akhir_range);
            $data['filter_type'] = 'range';
            $data['tanggal_awal_range'] = $tanggal_awal_range;
            $data['tanggal_akhir_range'] = $tanggal_akhir_range;
            
            $resultRange = $rangeBuilder->groupBy('DATE(created_at)')->orderBy('DATE(created_at)', 'ASC')->findAll();
            log_message('debug', '[LaporanPendapatan-Range] Query: ' . str_replace(["\r", "\n"], ' ', $this->db->getLastQuery()->getQuery()));
            log_message('debug', '[LaporanPendapatan-Range] Result Count: ' . count($resultRange));
            log_message('debug', '[LaporanPendapatan-Range] Result: ' . json_encode($resultRange));

            if ($resultRange) {
                foreach($resultRange as $row) {
                    if (is_object($row) && isset($row->tanggal) && isset($row->total)) {
                        $data['pendapatan_per_hari'][] = ['tanggal' => $row->tanggal, 'total' => (float)$row->total];
                        $totalPendapatan += (float)$row->total;
                    } elseif (is_array($row) && isset($row['tanggal']) && isset($row['total'])) {
                        $data['pendapatan_per_hari'][] = ['tanggal' => $row['tanggal'], 'total' => (float)$row['total']];
                        $totalPendapatan += (float)$row['total'];
                    }
                }
            }
        } else {
            
            log_message('debug', '[LaporanPendapatan] Mode: Tanggal Tunggal.');
            
            $singleDateBuilder = $this->transaksiModel->select('SUM(total_harga) as total') 
                                                    ->where('transaksi.is_deleted', 0)
                                                    ->where('DATE(created_at)', $tanggal);
            
            $resultSingle = $singleDateBuilder->first(); 
            log_message('debug', '[LaporanPendapatan-Single] Query: ' . str_replace(["\r", "\n"], ' ', $this->db->getLastQuery()->getQuery()));
            log_message('debug', '[LaporanPendapatan-Single] Result: ' . json_encode($resultSingle));

            $totalPendapatan = ($resultSingle && isset($resultSingle->total)) ? (float)$resultSingle->total : 0;
            
            $data['filter_type'] = 'single';
            $data['tanggal'] = $tanggal;
        }
        
        $data['total_pendapatan'] = $totalPendapatan;
        log_message('debug', '[LaporanPendapatan] Data dikirim ke view: ' . json_encode($data));
        return view('Backend/Admin/Laporan/pendapatan_index', $data);
    }

    // --- Analisis Lanjutan ---
    public function produkTerlaris()
    {
        $data['title'] = 'Laporan Produk Terlaris';

        $limit = $this->request->getGet('limit') ?: 10; // Default 10 produk
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $builder = $this->db->table('detail_transaksi dt');
        $builder->select('pr.nama as nama_produk, SUM(dt.jumlah) as total_terjual, pr.produk_id, pr.kode_barcode');
        $builder->join('produk pr', 'pr.produk_id = dt.produk_id');
        $builder->join('transaksi t', 't.transaksi_id = dt.transaksi_id'); 
        
        $builder->where('dt.is_deleted', 0);
        $builder->where('t.is_deleted', 0); 

        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('DATE(t.created_at) >=', $tanggal_awal);
            $builder->where('DATE(t.created_at) <=', $tanggal_akhir);
            $data['info_periode'] = "Periode " . date('d M Y', strtotime($tanggal_awal)) . " s/d " . date('d M Y', strtotime($tanggal_akhir));
        } elseif ($tanggal_awal) {
            $builder->where('DATE(t.created_at)', $tanggal_awal);
            $data['info_periode'] = "Tanggal " . date('d M Y', strtotime($tanggal_awal));
        } else {
            $data['info_periode'] = "Semua Waktu";
        }

        $builder->groupBy('pr.produk_id, pr.nama, pr.kode_barcode');
        $builder->orderBy('total_terjual', 'DESC');
        $builder->limit((int)$limit);

        $data['produk_terlaris'] = $builder->get()->getResultArray();
        $data['limit'] = $limit;
        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;

        log_message('debug', '[LaporanController::produkTerlaris] Data: ' . json_encode($data));
        return view('Backend/Admin/Laporan/produk_terlaris_index', $data);
    }

    public function rataRataBelanjaPelanggan()
    {
        $data['title'] = 'Laporan Rata-Rata Belanja per Member';

        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        
        $builder = $this->db->table('transaksi t');
        $builder->select("COALESCE(p.nama, 'Pelanggan Umum') AS nama_pelanggan, AVG(t.total_harga) AS rata_rata_belanja, COUNT(t.transaksi_id) as jumlah_transaksi, SUM(t.total_harga) as total_belanja_pelanggan, p.pelanggan_id", false);
        $builder->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left');
        $builder->where('t.is_deleted', 0);

        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('DATE(t.created_at) >=', $tanggal_awal);
            $builder->where('DATE(t.created_at) <=', $tanggal_akhir);
            $data['info_periode'] = "Periode " . date('d M Y', strtotime($tanggal_awal)) . " s/d " . date('d M Y', strtotime($tanggal_akhir));
        } elseif ($tanggal_awal) {
            $builder->where('DATE(t.created_at)', $tanggal_awal);
            $data['info_periode'] = "Tanggal " . date('d M Y', strtotime($tanggal_awal));
        } else {
            $data['info_periode'] = "Semua Waktu";
        }

        $builder->groupBy('p.pelanggan_id, p.nama');
        $builder->orderBy('rata_rata_belanja', 'DESC');

        $data['analisis_pelanggan'] = $builder->get()->getResultArray();
        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;

        return view('Backend/Admin/Laporan/analisis_pelanggan_index', $data);
    }

    public function metodePembayaranPopuler()
    {
        $data['title'] = 'Laporan Metode Pembayaran Populer';

        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $builder = $this->db->table('transaksi t');
        $builder->select("t.metode_pembayaran, COUNT(t.transaksi_id) AS total_transaksi");
        $builder->where('t.is_deleted', 0);
        $builder->where("t.metode_pembayaran IS NOT NULL");
        $builder->where("t.metode_pembayaran !=", '');


        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('DATE(t.created_at) >=', $tanggal_awal);
            $builder->where('DATE(t.created_at) <=', $tanggal_akhir);
            $data['info_periode'] = "Periode " . format_indo($tanggal_awal) . " s/d " . format_indo($tanggal_akhir);
        } elseif ($tanggal_awal) {
            $builder->where('DATE(t.created_at)', $tanggal_awal);
            $data['info_periode'] = "Tanggal " . format_indo($tanggal_awal);
        } else {
            $data['info_periode'] = "Semua Waktu";
        }

        $builder->groupBy('t.metode_pembayaran');
        $builder->orderBy('total_transaksi', 'DESC');

        $queryResult = $builder->get()->getResultArray();
        log_message('debug', '[LaporanController::metodePembayaranPopuler] Query: ' . str_replace(["\r", "\n"], ' ', $this->db->getLastQuery()->getQuery()));
        log_message('debug', '[LaporanController::metodePembayaranPopuler] Result: ' . json_encode($queryResult));

        $data['metode_populer'] = $queryResult;
        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;

        log_message('debug', '[LaporanController::metodePembayaranPopuler] Data to view: ' . json_encode($data));
        return view('Backend/Admin/Laporan/metode_pembayaran_populer_index', $data);
    }

}