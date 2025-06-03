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
                // Jika role pemilik, lakukan 2FA OTP
                if ($karyawan['role'] == 'pemilik') {
                    // Generate OTP 6 digit
                    $otp = random_int(100000, 999999);
                    $otpData = [
                        'otp_code' => $otp,
                        'otp_expires' => time() + 300, // 5 menit
                        'email' => $karyawan['email'],
                        'karyawan_id' => $karyawan['karyawan_id'],
                    ];
                    $this->session->set('2fa_karyawan_data', $otpData);
                    // Kirim OTP ke email
                    $emailService = \Config\Services::email();
                    $emailService->setFrom('no-reply@swalayan.com', 'Swalayan 2FA');
                    $emailService->setTo($karyawan['email']);
                    $emailService->setSubject('Kode OTP Login Owner Swalayan');
                    $emailService->setMessage('Kode OTP Anda: <b>' . $otp . '</b>\nKode berlaku 5 menit.');
                    $emailService->send();
                    // Simpan audit log
                    $auditLogModel = new AuditLogModel();
                    $auditLogModel->insert([
                        'user_id' => $karyawan['karyawan_id'],
                        'action' => 'LOGIN_2FA_OTP_SENT',
                        'description' => 'OTP dikirim ke email owner: ' . $karyawan['email'],
                        'ip_address' => $this->request->getIPAddress(),
                        'user_agent' => $this->request->getUserAgent()->getAgentString(),
                    ]);
                    // Redirect ke halaman verifikasi OTP
                    return redirect()->to(site_url('auth/verify-otp'));
                }
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
    public function verifyOtp()
    {
        // Cek jika data OTP ada di session
        $otpData = $this->session->get('2fa_karyawan_data');
        if (!$otpData) {
            return redirect()->to('/login')->with('error', 'Akses tidak valid.');
        }
        return view('Backend/Login/verify_otp');
    }

    public function processOtp()
    {
        $otpData = $this->session->get('2fa_karyawan_data');
        if (!$otpData) {
            return redirect()->to('/login')->with('error', 'Akses tidak valid.');
        }
        $inputOtp = $this->request->getPost('otp_code');
        if (!$inputOtp || !preg_match('/^\d{6}$/', $inputOtp)) {
            return redirect()->back()->withInput()->with('error', 'Kode OTP tidak valid.');
        }
        if (time() > $otpData['otp_expires']) {
            return redirect()->back()->with('error', 'Kode OTP sudah kadaluarsa.');
        }
        if ($inputOtp != $otpData['otp_code']) {
            return redirect()->back()->with('error', 'Kode OTP salah.');
        }
        // OTP benar, set session login owner
        $sessData = [
            'karyawan_id' => $otpData['karyawan_id'],
            'email' => $otpData['email'],
            'role' => 'pemilik',
            'isLoggedIn' => TRUE
        ];
        $this->session->set($sessData);
        $this->session->remove('2fa_karyawan_data');
        // Audit log
        $auditLogModel = new AuditLogModel();
        $auditLogModel->insert([
            'user_id' => $otpData['karyawan_id'],
            'action' => 'LOGIN_2FA_SUCCESS',
            'description' => 'Owner login sukses dengan OTP.',
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
        ]);
        return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang, Pemilik.');
    }

    public function resendOtp()
    {
        $otpData = $this->session->get('2fa_karyawan_data');
        if (!$otpData) {
            return redirect()->to('/login')->with('error', 'Akses tidak valid.');
        }
        $otp = random_int(100000, 999999);
        $otpData['otp_code'] = $otp;
        $otpData['otp_expires'] = time() + 300;
        $this->session->set('2fa_karyawan_data', $otpData);
        // Kirim ulang OTP ke email
        $emailService = \Config\Services::email();
        $emailService->setFrom('no-reply@swalayan.com', 'Swalayan 2FA');
        $emailService->setTo($otpData['email']);
        $emailService->setSubject('Kode OTP Login Owner Swalayan');
        $emailService->setMessage('Kode OTP Anda: <b>' . $otp . '</b>\nKode berlaku 5 menit.');
        $emailService->send();
        // Audit log
        $auditLogModel = new AuditLogModel();
        $auditLogModel->insert([
            'user_id' => $otpData['karyawan_id'],
            'action' => 'LOGIN_2FA_OTP_RESEND',
            'description' => 'OTP dikirim ulang ke email owner: ' . $otpData['email'],
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
        ]);
        return redirect()->back()->with('message', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}