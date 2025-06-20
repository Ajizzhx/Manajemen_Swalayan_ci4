<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

use App\Models\AuditLogModel;

class KaryawanController extends BaseController
{
    protected $session; 
    protected $karyawanModel;
    protected $auditLogModel;
    protected $helpers = ['form', 'url', 'custom'];
    
    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->session = \Config\Services::session();

        
    }

    public function index()
    {
        
        $excludedKaryawanId = 'KRY-OWNER-001';

        $data = [
            'title' => 'Kelola Karyawan',
            'karyawan' => $this->karyawanModel->withDeleted()->where('is_deleted', 0)
                                            ->where('karyawan_id !=', $excludedKaryawanId)->findAll(),
        ];
        return view('Backend/Admin/Karyawan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Karyawan Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Karyawan/create', $data);
    }

    public function store()
    {
    log_message('critical', '[[ KARYAWAN CONTROLLER :: STORE METHOD CALLED ]]');        $rules = [
            'nama' => [
                'rules'  => 'required|alpha_space|min_length[3]|max_length[100]',
                'errors' => [
                    'alpha_space' => 'Nama karyawan hanya boleh berisi karakter alfabet dan spasi.',
                    'required'    => 'Nama karyawan wajib diisi.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[karyawan.email]',
                'errors' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email ini sudah digunakan.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi.',
                    'min_length' => 'Password minimal harus terdiri dari {param} karakter.'
                ]
            ],
            'role' => [
                'rules' => 'required|in_list[admin,kasir,kepala_toko]',
                'errors' => [
                    'required' => 'Role wajib dipilih.',
                    'in_list' => 'Role yang dipilih tidak valid.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('admin/owner-area/karyawan/create')->withInput()->with('validation', $this->validator);
        }

        $karyawan_id = generate_sequential_id('KAR', $this->karyawanModel, 'karyawan_id', 4); // KAR0001
        $saveData = [
            'karyawan_id' => $karyawan_id, // Simpan ID yang di-generate
            'nama'        => $this->request->getPost('nama'),
            'email'       => $this->request->getPost('email'),
            'password'    => $this->request->getPost('password'),
            'role'        => $this->request->getPost('role'),
            'is_deleted'  => 0
        ];
        log_message('debug', '[KaryawanController::store] Data to save: ' . json_encode($saveData));
        $this->karyawanModel->save($saveData);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'CREATE_KARYAWAN',
            'description' => 'Menambah karyawan baru: ' . $saveData['nama'] . ' (ID: ' . $karyawan_id . ')',
        ]);

        session()->setFlashdata('message', 'Data karyawan berhasil ditambahkan.');
        return redirect()->to('admin/owner-area/karyawan');
    }

    public function edit($id)
    {
        
        
        $karyawan = $this->karyawanModel->withDeleted()->where('is_deleted', 0)->find($id); 
        if (!$karyawan) {
            session()->setFlashdata('error', 'Karyawan tidak ditemukan atau tidak aktif.');
            return redirect()->to('admin/owner-area/karyawan');
        }
        $data = [
            'title' => 'Edit Karyawan',
            'karyawan' => $karyawan,
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Karyawan/edit', $data);
    }

    public function update($id)
    {
    log_message('critical', '[[ KARYAWAN CONTROLLER :: UPDATE METHOD CALLED for ID: ' . $id . ' via METHOD: ' . $this->request->getMethod() . ' ]]');
        
        $karyawanLama = $this->karyawanModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$karyawanLama) {
             session()->setFlashdata('error', 'Karyawan tidak ditemukan untuk diupdate atau tidak aktif.');
             return redirect()->to('admin/owner-area/karyawan');
        }

        $emailRule = 'required|valid_email|is_unique[karyawan.email,karyawan_id,'.$id.']';
        if ($this->request->getPost('email') == $karyawanLama['email']) {
            $emailRule = 'required|valid_email';
        }
        $rules = [
            'nama' => [
                'rules'  => 'required|alpha_space|min_length[3]|max_length[100]',
                'errors' => [
                    'alpha_space' => 'Nama karyawan hanya boleh berisi karakter alfabet dan spasi.',
                    'required'    => 'Nama karyawan wajib diisi.'
                ]
            ],
            'email' => $emailRule,
            'role' => 'required|in_list[admin,kasir,kepala_toko]'
        ];        // Password hanya diupdate jika diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = [
                'rules' => 'min_length[6]',
                'errors' => [
                    'min_length' => 'Password minimal harus terdiri dari {param} karakter.'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->to('admin/owner-area/karyawan/edit/' . $id)->withInput()->with('validation', $this->validator);
        }

        $dataUpdate = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role')
        ];

        if ($this->request->getPost('password')) {
            $dataUpdate['password'] = $this->request->getPost('password');
        }
        
        log_message('debug', '[KaryawanController::update] Data to update for ID ' . $id . ': ' . json_encode($dataUpdate));
        if ($this->karyawanModel->update($id, $dataUpdate)) {
            // Log Audit
            $this->auditLogModel = new AuditLogModel();
            $this->auditLogModel->insert([
                'user_id' => session()->get('karyawan_id'),
                'action' => 'UPDATE_KARYAWAN',
                'description' => 'Memperbarui data karyawan ID: ' . $id . ' (Nama: ' . $dataUpdate['nama'] . ')',
            ]);

            session()->setFlashdata('message', 'Data karyawan berhasil diperbarui.');
        } else {
            
            $modelErrors = $this->karyawanModel->errors();
            $errorMessage = 'Gagal memperbarui data karyawan.';
            if (!empty($modelErrors)) {
                $errorMessage .= ' ' . implode(' ', array_values($modelErrors));
            }
            session()->setFlashdata('error', $errorMessage);
            return redirect()->to('admin/owner-area/karyawan/edit/' . $id)->withInput()->with('validation', $this->karyawanModel->validator ?? $this->validator);
        }
        return redirect()->to('admin/owner-area/karyawan');
    }

    public function delete($id)
    {
        
        $karyawan = $this->karyawanModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$karyawan) {
             session()->setFlashdata('error', 'Karyawan tidak ditemukan atau sudah tidak aktif.');
             return redirect()->to('admin/owner-area/karyawan');
        }

        
        $this->karyawanModel->update($id, ['is_deleted' => 1]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'DELETE_KARYAWAN',
            'description' => 'Menghapus (soft delete) karyawan ID: ' . $id . ' (Nama: ' . $karyawan['nama'] . ')',
        ]);

        session()->setFlashdata('message', 'Data karyawan berhasil dihapus (soft delete).');
        return redirect()->to('admin/owner-area/karyawan');
    }

    // public function forceDelete($id)
    // {
    //     $this->karyawanModel->delete($id, true); // Parameter kedua true untuk force delete
    //     session()->setFlashdata('message', 'Data karyawan berhasil dihapus permanen.');
    //     return redirect()->to('/admin/karyawan');
    // }
}