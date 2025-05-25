<?php

namespace App\Controllers\Admin\Owner;

use App\Controllers\BaseController;
use App\Models\AuditLogModel; // Menggunakan model AuditLogModel
use App\Models\KaryawanModel; // Untuk join nama pengguna

class AuditController extends BaseController
{
    protected $auditLogModel;
    protected $karyawanModel; // Tambahkan properti untuk KaryawanModel
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel(); // Inisialisasi AuditLogModel
        $this->karyawanModel = new KaryawanModel(); // Inisialisasi KaryawanModel
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'date', 'custom']);
    }

    public function index()
    {   
        $itemsPerPage = 25; // Jumlah item per halaman

        $data['title'] = 'Log Audit Sistem';

        // Mengambil data log audit dengan join ke tabel karyawan untuk mendapatkan nama pengguna
        // dan menggunakan paginasi
        $builder = $this->auditLogModel
            ->select('audit_logs.*, karyawan.nama as nama_pengguna') // Ambil semua kolom dari audit_logs dan nama dari karyawan
            ->join('karyawan', 'karyawan.karyawan_id = audit_logs.user_id', 'left'); // LEFT JOIN untuk kasus user_id bisa NULL atau tidak cocok

        // Filter (Contoh, bisa Anda kembangkan)
        $keyword = $this->request->getGet('keyword');
        if ($keyword) {
            $builder->groupStart()
                    ->like('audit_logs.action', $keyword)
                    ->orLike('audit_logs.description', $keyword)
                    ->orLike('karyawan.nama', $keyword)
                    ->groupEnd();
        }

        $data['audit_logs'] = $builder->orderBy('audit_logs.created_at', 'DESC')->paginate($itemsPerPage);
        $data['pager'] = $this->auditLogModel->pager;
        $data['keyword'] = $keyword; // Kirim keyword ke view untuk ditampilkan di form search

        return view('Backend/Admin/Owner/Audit/index', $data); // Sesuaikan path view jika perlu
    }
}