<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\AuditLogModel;

class Auth extends BaseController
{
    protected $session;
    protected $karyawanModel;
    protected $auditLogModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        helper(['form', 'url']);
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        
        if ($this->session->get('isLoggedIn')) {
            if ($this->session->get('role') == 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($this->session->get('role') == 'kasir') {
                return redirect()->to('/kasir/dashboard');
                } elseif ($this->session->get('role') == 'pemilik') {
                return redirect()->to('/admin/dashboard'); 
            }
        }
        return view('Backend/Login/login'); 
    }
    public function loginKasir()
    {
        
        if ($this->session->get('isLoggedIn')) {
            if ($this->session->get('role') == 'admin') {
                
                return redirect()->to('/admin/dashboard');
            } elseif ($this->session->get('role') == 'kasir') {
                return redirect()->to('/kasir/dashboard'); 
                } elseif ($this->session->get('role') == 'pemilik') { 
                return redirect()->to('/admin/dashboard'); 

            }
        }
        return view('Backend/Login/kasir_login'); 
    }
    public function loginAdmin()
    {
        
        if ($this->session->get('isLoggedIn')) {
            if ($this->session->get('role') == 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($this->session->get('role') == 'kasir') {
                
                return redirect()->to('/kasir/dashboard');
            } elseif ($this->session->get('role') == 'pemilik') { 
                return redirect()->to('/admin/dashboard');
            }
        }
        return view('Backend/Login/admin_login'); 
    }
    public function loginProcess()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            
            $referrerPath = previous_url(true)->getPath();
            $loginRedirectUrl = site_url('login'); // Default
            if (strpos($referrerPath, 'kasir-login') !== false) {
                $loginRedirectUrl = site_url('kasir-login');
            } elseif (strpos($referrerPath, 'admin-login') !== false) {
                $loginRedirectUrl = site_url('admin-login');
            }
            return redirect()->to($loginRedirectUrl)->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        
        $karyawan = $this->karyawanModel->withDeleted()
                                     ->where('email', $email)->where('is_deleted', 0)->first();

        if ($karyawan) {
            if (hash('sha256', $password) === $karyawan['password']) {
                $sessData = [
                    'karyawan_id' => $karyawan['karyawan_id'],
                    'nama'        => $karyawan['nama'],
                    'email'       => $karyawan['email'],
                    'role'        => $karyawan['role'],
                    'isLoggedIn'  => TRUE
                ];
                $this->session->set($sessData);
                 $auditLogModel = new AuditLogModel(); 
                 $auditLogModel->insert([
                     'user_id' => $karyawan['karyawan_id'],
                     'action' => 'LOGIN_SUCCESS',
                     'description' => 'User ' . $karyawan['email'] . ' logged in successfully.',
                     'ip_address' => $this->request->getIPAddress(),
                     'user_agent' => $this->request->getUserAgent()->getAgentString(),
                 ]);

                if ($karyawan['role'] == 'admin') {
                    return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang, Admin.');
                } elseif ($karyawan['role'] == 'kasir') {
                    return redirect()->to('/kasir/dashboard')->with('success', 'Login berhasil! Selamat datang, Kasir.');
                } elseif ($karyawan['role'] == 'pemilik') { 
                    return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang, Pemilik.');
                } else {
                     
                     $auditLogModel->insert([
                         'user_id' => $karyawan['karyawan_id'],
                         'action' => 'LOGIN_INVALID_ROLE',
                         'description' => 'User ' . $karyawan['email'] . ' attempted login with an invalid role: ' . $karyawan['role'],
                         'ip_address' => $this->request->getIPAddress(),
                         'user_agent' => $this->request->getUserAgent()->getAgentString(),
                     ]);
                    $this->session->destroy();
                    return redirect()->to('/login')->with('error', 'Role pengguna tidak valid atau tidak diizinkan.');
                }
            } else {
                
                $auditLogModel = new AuditLogModel(); 
                $auditLogModel->insert([
                    'user_id' => $karyawan['karyawan_id'],
                    'action' => 'LOGIN_FAILED',
                    'description' => 'Failed login attempt for user ' . $email . ' (wrong password).',
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getAgentString(),
                ]);
                $referrerPath = previous_url(true)->getPath();
                $loginRedirectUrl = site_url('login'); // Default
                if (strpos($referrerPath, 'kasir-login') !== false) {
                    $loginRedirectUrl = site_url('kasir-login');
                } elseif (strpos($referrerPath, 'admin-login') !== false) {
                    $loginRedirectUrl = site_url('admin-login');
                }
                return redirect()->to($loginRedirectUrl)->withInput()->with('error', 'Password salah.');
            }
        } else {
            
            $auditLogModel = new AuditLogModel();
            $auditLogModel->insert([
                'action' => 'LOGIN_FAILED',
                'description' => 'Failed login attempt: Email not found or account inactive - ' . $email,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
            ]);
            $referrerPath = previous_url(true)->getPath();
            $loginRedirectUrl = site_url('login'); // Default
            if (strpos($referrerPath, 'kasir-login') !== false) {
                $loginRedirectUrl = site_url('kasir-login');
            } elseif (strpos($referrerPath, 'admin-login') !== false) {
                $loginRedirectUrl = site_url('admin-login');
            }
            return redirect()->to($loginRedirectUrl)->withInput()->with('error', 'Email tidak ditemukan atau akun tidak aktif.');
        }
    }

    public function logout()
    {
        // Record logout
        $auditLogModel = new AuditLogModel(); 
        $auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'), 
            'action' => 'LOGOUT',
            'description' => 'User logged out.',
        ]);
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');

    }
}