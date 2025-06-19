<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\AuditLogModel;

class Auth extends BaseController
{
    protected $session;
    protected $karyawanModel;
    protected $auditLogModel;
      protected function sendOTPEmail($email, $otp, $karyawanId) {
        $emailService = \Config\Services::email();
        log_message('info', 'Attempting to send OTP email to owner: ' . $email);
        
        try {
            // Initialize email service with explicit configuration from config file
            $emailConfig = config('Email');
            
            // Tambahkan log informasi konfigurasi (tanpa password untuk keamanan)
            log_message('info', 'Using SMTP configuration: Host=' . $emailConfig->SMTPHost . ', User=' . $emailConfig->SMTPUser . ', Port=' . $emailConfig->SMTPPort);
            
            $emailService->initialize([
                'protocol' => 'smtp',
                'SMTPHost' => 'smtp.gmail.com',
                'SMTPUser' => $emailConfig->SMTPUser, 
                'SMTPPass' => $emailConfig->SMTPPass, 
                'SMTPPort' => 465,
                'SMTPCrypto' => 'ssl',
                'mailType' => 'html',
                'charset' => 'utf-8',
                'validate' => true,
                'SMTPTimeout' => 60,
                'SMTPKeepAlive' => true,
                'newline' => "\r\n",
                'SMTPDebug' => 0 // Ubah ke 2 untuk debugging detail
            ]);
            
            log_message('info', 'Email configuration initialized');
            
            $emailService->setFrom($emailConfig->fromEmail, 'Swalayan 2FA')
                ->setTo($email)
                ->setSubject('Kode OTP Login Owner Swalayan')
                ->setMessage('
                    <html>
                        <body>
                            <h2>Kode OTP Login Owner Swalayan</h2>
                            <p>Kode OTP Anda: <strong style="font-size: 24px;">' . $otp . '</strong></p>
                            <p>Kode berlaku selama 5 menit.</p>
                            <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.</p>
                        </body>
                    </html>
                ');            // Clear any previous errors
            error_clear_last();
            
            // Enable error reporting for this section
            $old_error_reporting = error_reporting(E_ALL);
            
            $result = $emailService->send(false);
            
            // Restore error reporting
            error_reporting($old_error_reporting);
            
            if (!$result) {
                $error = error_get_last();
                $debug = $emailService->printDebugger(['headers', 'subject', 'body']);
                $errorMsg = $error ? $error['message'] : 'Unknown error';
                
                log_message('error', 'Failed to send OTP email. Error: ' . $errorMsg);
                log_message('error', 'SMTP Debug: ' . $debug);
                
                $this->logEmailError($email, $errorMsg . "\nDebug: " . $debug);
                return false;
            }
            
            log_message('info', 'Successfully sent OTP email to: ' . $email);
            
            // Log success
            $auditLogModel = new AuditLogModel();
            $auditLogModel->insert([
                'user_id' => $karyawanId,
                'action' => 'LOGIN_2FA_OTP_SENT',
                'description' => 'OTP dikirim ke email owner: ' . $email,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
            ]);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Exception while sending OTP email: ' . $e->getMessage());
            $this->logEmailError($email, $e->getMessage());
            return false;
        }
    }

    protected function logEmailError($email, $error) {
        log_message('error', 'Failed to send OTP email to ' . $email . '. Error: ' . $error);
        $auditLogModel = new AuditLogModel();
        $auditLogModel->insert([
            'action' => 'EMAIL_SEND_FAILED',
            'description' => 'Failed to send OTP email to ' . $email . '. Error: ' . $error,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
        ]);
    }

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
            } elseif ($this->session->get('role') == 'kepala_toko') {
                return redirect()->to('/admin/dashboard');
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
            } elseif ($this->session->get('role') == 'kepala_toko') {
                return redirect()->to('/admin/dashboard');
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
            } elseif ($this->session->get('role') == 'kepala_toko') {
                return redirect()->to('/admin/dashboard');
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
                                     ->where('email', $email)->where('is_deleted', 0)->first();        if ($karyawan) {
            if (hash('sha256', $password) === $karyawan['password']) {
                $sessData = [
                    'karyawan_id' => $karyawan['karyawan_id'],
                    'nama'        => $karyawan['nama'],
                    'email'       => $karyawan['email'],
                    'role'        => $karyawan['role'],
                    'isLoggedIn'  => TRUE
                ];                // Update last_login
                $this->karyawanModel->update($karyawan['karyawan_id'], ['last_login' => date('Y-m-d H:i:s')]);                // Jika role pemilik, lakukan 2FA OTP
                if ($karyawan['role'] == 'pemilik' || $karyawan['role'] == 'owner') {
                    // Generate OTP 6 digit
                    $otp = random_int(100000, 999999);
                    
                    // Pastikan menggunakan email owner yang sebenarnya
                    $ownerEmail = $karyawan['email'];
                    
                    $otpData = [
                        'otp_code' => $otp,
                        'otp_expires' => time() + 300, // 5 menit
                        'email' => $ownerEmail,
                        'karyawan_id' => $karyawan['karyawan_id'],
                    ];
                    $this->session->set('2fa_karyawan_data', $otpData);
                    
                    // Log untuk debugging
                    log_message('info', 'Sending OTP to owner: ' . $ownerEmail);
                    
                    // Kirim OTP ke email owner
                    if (!$this->sendOTPEmail($ownerEmail, $otp, $karyawan['karyawan_id'])) {
                        return redirect()->back()->with('error', 'Gagal mengirim kode OTP ke ' . $ownerEmail . '. Silakan coba lagi atau hubungi administrator untuk memastikan email terkonfigurasi dengan benar.');
                    }
                    
                    // Redirect ke halaman verifikasi OTP
                    return redirect()->to(site_url('auth/verify-otp'))->with('info', 'Kode OTP telah dikirimkan ke email ' . $ownerEmail);
                }
                $this->session->set($sessData);
                 $auditLogModel = new AuditLogModel(); 
                 $auditLogModel->insert([
                     'user_id' => $karyawan['karyawan_id'],
                     'action' => 'LOGIN_SUCCESS',
                     'description' => 'User ' . $karyawan['email'] . ' logged in successfully.',
                     'ip_address' => $this->request->getIPAddress(),
                     'user_agent' => $this->request->getUserAgent()->getAgentString(),
                 ]);                if ($karyawan['role'] == 'admin') {
                    return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang, Admin.');
                } elseif ($karyawan['role'] == 'kasir') {
                    return redirect()->to('/kasir/dashboard')->with('success', 'Login berhasil! Selamat datang, Kasir.');
                } elseif ($karyawan['role'] == 'kepala_toko') {
                    return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil! Selamat datang, Kepala Toko.');                } elseif ($karyawan['role'] == 'owner') { 
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
                
                $auditLogModel = new AuditLogModel();                // Use safer method to log failed login
                try {
                    $auditLogModel->logActivity(
                        $karyawan['karyawan_id'],
                        'LOGIN_FAILED',
                        'Failed login attempt for user ' . $email . ' (wrong password).'
                    );
                } catch (\Exception $e) {
                    log_message('error', 'Failed to log login attempt: ' . $e->getMessage());
                }
                
                $referrerPath = previous_url(true)->getPath();
                $loginRedirectUrl = site_url('login'); // Default
                if (strpos($referrerPath, 'kasir-login') !== false) {
                    $loginRedirectUrl = site_url('kasir-login');
                } elseif (strpos($referrerPath, 'admin-login') !== false) {
                    $loginRedirectUrl = site_url('admin-login');
                }
                return redirect()->to($loginRedirectUrl)
                    ->withInput()
                    ->with('error', 'Password yang Anda masukkan salah. Mohon periksa kembali email dan password Anda.');
            }
        } else {
              // Use safer method to log failed login
            try {
                $auditLogModel = new AuditLogModel();
                $auditLogModel->logActivity(
                    null,
                    'LOGIN_FAILED',
                    'Failed login attempt: Email not found or account inactive - ' . $email
                );
            } catch (\Exception $e) {
                log_message('error', 'Failed to log login attempt: ' . $e->getMessage());
            }
            
            $referrerPath = previous_url(true)->getPath();
            $loginRedirectUrl = site_url('login'); // Default
            if (strpos($referrerPath, 'kasir-login') !== false) {
                $loginRedirectUrl = site_url('kasir-login');
            } elseif (strpos($referrerPath, 'admin-login') !== false) {
                $loginRedirectUrl = site_url('admin-login');
            }
            return redirect()->to($loginRedirectUrl)
                ->withInput()
                ->with('error', 'Akun dengan email tersebut tidak ditemukan atau tidak aktif. Mohon periksa kembali email Anda atau hubungi administrator.');
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
        if (!$this->sendOTPEmail($otpData['email'], $otp, $otpData['karyawan_id'])) {
            return redirect()->back()->with('error', 'Gagal mengirim ulang kode OTP ke email. Silakan coba lagi.');
        }
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

    // ...existing code...
}