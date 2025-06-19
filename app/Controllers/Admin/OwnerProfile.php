<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

class OwnerProfile extends BaseController
{
    protected $karyawanModel;
    protected $session;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->session = \Config\Services::session();
    }    public function index()
    {
        // Hanya owner yang bisa mengakses halaman ini
        if ($this->session->get('role') != 'pemilik') {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $karyawanId = $this->session->get('karyawan_id');
        $data['owner'] = $this->karyawanModel->find($karyawanId);
        
        return view('Backend/Owner/profile', $data);
    }    public function update()
    {
        // Hanya owner yang bisa mengakses halaman ini
        if ($this->session->get('role') != 'pemilik') {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $karyawanId = $this->session->get('karyawan_id');
        
        // Validasi
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[karyawan.email,karyawan_id,' . $karyawanId . ']',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
        ];
        
        // Jika ada password baru
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $data['password'] = $newPassword; // KaryawanModel akan meng-hash password
        }
        
        if ($this->karyawanModel->update($karyawanId, $data)) {
            // Update juga data session
            $this->session->set('nama', $data['nama']);
            $this->session->set('email', $data['email']);
            
            return redirect()->to(base_url('admin/owner-profile'))->with('success', 'Profil berhasil diperbarui. Email OTP juga telah diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }
}
