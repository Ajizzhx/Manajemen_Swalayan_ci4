<?php

namespace App\Controllers\Common;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

class ProfileController extends BaseController
{
    protected $karyawanModel;
    protected $session;
    protected $validation;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $karyawan_id = $this->session->get('karyawan_id');
        if (!$karyawan_id) {
            return redirect()->to(site_url('login'))->with('error', 'Sesi tidak valid.');
        }

        $karyawan = $this->karyawanModel->find($karyawan_id);

        if (!$karyawan) {
            session()->setFlashdata('error', 'Data profil tidak ditemukan.');
            return redirect()->to(site_url(session()->get('role') . '/dashboard'));
        }

        $data = [
            'title' => 'Profil Saya',
            'karyawan' => $karyawan,
            'validation' => $this->validation
        ];

        return view('Backend/Common/Profile/index', $data);
    }

    public function update()
    {
        $karyawan_id = $this->session->get('karyawan_id');
        if (!$karyawan_id) {
            return redirect()->to(site_url('login'))->with('error', 'Sesi tidak valid.');
        }

        $karyawanLama = $this->karyawanModel->find($karyawan_id);
        if (!$karyawanLama) {
            session()->setFlashdata('error', 'Data profil tidak ditemukan untuk diupdate.');
            return redirect()->to(site_url(session()->get('role') . '/profile'));
        }

        $emailRule = 'required|valid_email|is_unique[karyawan.email,karyawan_id,' . $karyawan_id . ']';
        if ($this->request->getPost('email') == $karyawanLama['email']) {
            $emailRule = 'required|valid_email';
        }

        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'email' => $emailRule,
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['confirm_password'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->to(site_url(session()->get('role') . '/profile'))->withInput()->with('validation', $this->validator);
        }

        $dataUpdate = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
        ];

        if ($this->request->getPost('password')) {
            
            $dataUpdate['password'] = $this->request->getPost('password');
        }
        
        if ($this->karyawanModel->update($karyawan_id, $dataUpdate)) {
            
            $this->session->set('nama_karyawan', $dataUpdate['nama']);
            $this->session->set('email', $dataUpdate['email']); 

            session()->setFlashdata('message', 'Profil berhasil diperbarui.');
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui profil.');
        }

        return redirect()->to(site_url(session()->get('role') . '/profile'));
    }
}
