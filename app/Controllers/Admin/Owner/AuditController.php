<?php

namespace App\Controllers\Admin\Owner;

use App\Controllers\BaseController;
use App\Models\AuditLogModel; 
use App\Models\KaryawanModel; 

class AuditController extends BaseController
{
    protected $auditLogModel;
    protected $karyawanModel; 
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
        $itemsPerPage = 25; 

        $data['title'] = 'Log Audit Sistem';

        $builder = $this->auditLogModel
            ->select('audit_logs.*, karyawan.nama as nama_pengguna') 
            ->join('karyawan', 'karyawan.karyawan_id = audit_logs.user_id', 'left'); 

        // Filter keyword
        $keyword = $this->request->getGet('keyword');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        if ($keyword) {
            $builder->groupStart()
                    ->like('audit_logs.action', $keyword)
                    ->orLike('audit_logs.description', $keyword)
                    ->orLike('karyawan.nama', $keyword)
                    ->groupEnd();
        }

        // Filter tanggal
        if ($start_date && $end_date) {
            $builder->where('DATE(audit_logs.created_at) >=', $start_date)
                   ->where('DATE(audit_logs.created_at) <=', $end_date);
        } elseif ($start_date) {
            $builder->where('DATE(audit_logs.created_at) >=', $start_date);
        } elseif ($end_date) {
            $builder->where('DATE(audit_logs.created_at) <=', $end_date);
        }

        $data['audit_logs'] = $builder->orderBy('audit_logs.created_at', 'DESC')->paginate($itemsPerPage);
        $data['pager'] = $this->auditLogModel->pager;
        $data['keyword'] = $keyword;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        return view('Backend/Admin/Owner/Audit/index', $data); 
    }
}