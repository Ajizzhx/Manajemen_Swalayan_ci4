<?php

namespace App\Controllers\Common;

use App\Controllers\BaseController;

class SettingsController extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        $data = [
            'title' => 'Pengaturan',
            
            'user_role' => $this->session->get('role'),
            'user_name' => $this->session->get('nama_karyawan') ?? $this->session->get('nama') ?? 'User',
        ];

        return view('Backend/Common/Settings/index', $data);
    }

    
}
