<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PelangganModel;

use App\Models\AuditLogModel;

class PelangganController extends BaseController
{
    protected $pelangganModel;
    protected $session; 
    protected $helpers = ['form', 'url', 'custom']; 
    protected $auditLogModel;
    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->session = \Config\Services::session(); 
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'custom']); 
    }

    public function index()
    {
        $data = [
            'title'     => 'Manajemen Membership',
            'pelanggan' => $this->pelangganModel->where('is_deleted', 0)->orderBy('nama', 'ASC')->findAll(),
        ];
        return view('Backend/Admin/Pelanggan/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Member Baru',
            'validation' => $this->validation,
        ];
        return view('Backend/Admin/Pelanggan/create', $data);
    }

    public function store()
    {
        $rules = $this->pelangganModel->getValidationRules();
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $pelanggan_id = generate_sequential_id('PLG', $this->pelangganModel, 'pelanggan_id', 5); 
        $data = [
            'pelanggan_id' => $pelanggan_id, // Simpan ID yang di-generate
            'nama'          => $this->request->getPost('nama'),
            'email'         => $this->request->getPost('email'),
            'telepon'       => $this->request->getPost('telepon'),
            'alamat'        => $this->request->getPost('alamat'),
            // Ambil diskon_persen dari form, jika tidak ada, default ke 1.00
            'diskon_persen' => (float)($this->request->getPost('diskon_persen') ?? 1.00),
            'is_deleted'    => 0, 
        ];

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'CREATE_PELANGGAN',
            'description' => 'Menambah member baru: ' . $data['nama'] . ' (ID: ' . $pelanggan_id . ')',
        ]);

        if ($this->pelangganModel->save($data)) {
            session()->setFlashdata('message', 'Member berhasil ditambahkan.');
            return redirect()->to('/admin/pelanggan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan member.');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $pelanggan = $this->pelangganModel->where('is_deleted', 0)->find($id);
        if (!$pelanggan) {
            session()->setFlashdata('error', 'Member tidak ditemukan.');
            return redirect()->to('/admin/pelanggan');
        }

        $data = [
            'title'      => 'Edit Member',
            'pelanggan'  => $pelanggan,
            'validation' => $this->validation,
        ];
        return view('Backend/Admin/Pelanggan/edit', $data);
    }

    public function update($id)
    {
        $pelanggan = $this->pelangganModel->where('is_deleted', 0)->find($id);
        if (!$pelanggan) {
            session()->setFlashdata('error', 'Member tidak ditemukan.');
            return redirect()->to('/admin/pelanggan');
        }

        
        $rules = $this->pelangganModel->getValidationRules();
        
        $rules['email']   = str_replace('{id}', $id, $rules['email'] ?? 'permit_empty|valid_email|max_length[100]');
        $rules['telepon'] = str_replace('{id}', $id, $rules['telepon'] ?? 'permit_empty|max_length[20]');


        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        
        $updateData = [
            'nama'          => $this->request->getPost('nama'),
            'alamat'        => $this->request->getPost('alamat'),
            'diskon_persen' => (float)($this->request->getPost('diskon_persen') ?? 0.00), 
        ];

        
        $submitted_email = trim((string)$this->request->getPost('email'));
        $original_email = trim((string)($pelanggan->email ?? '')); 
        if ($submitted_email !== $original_email) {
            $updateData['email'] = $submitted_email;
        }

        
        $submitted_telepon = trim((string)$this->request->getPost('telepon'));
        $original_telepon = trim((string)($pelanggan->telepon ?? '')); 
        if ($submitted_telepon !== $original_telepon) {
            $updateData['telepon'] = $submitted_telepon;
        }

        
        $isDataChanged = ($updateData['nama'] !== $pelanggan->nama) ||
                         ($updateData['alamat'] !== $pelanggan->alamat) ||
                         ($updateData['diskon_persen'] != ($pelanggan->diskon_persen ?? 0.00)) || 
                         (isset($updateData['email'])) ||
                         (isset($updateData['telepon']));

        if (!$isDataChanged) {
            session()->setFlashdata('message', 'Tidak ada perubahan data.');
            return redirect()->to('/admin/pelanggan');
        }

        if ($this->pelangganModel->update($id, $updateData)) {
            // Log Audit
            $this->auditLogModel = new AuditLogModel();
            $this->auditLogModel->insert([
                'user_id' => session()->get('karyawan_id'),
                'action' => 'UPDATE_PELANGGAN',
                'description' => 'Memperbarui data member ID: ' . $id . ' (Nama: ' . $updateData['nama'] . ')',
            ]);

            session()->setFlashdata('message', 'Data member berhasil diperbarui.');
            return redirect()->to('/admin/pelanggan');
        } else {
            $modelErrors = $this->pelangganModel->errors();
            session()->setFlashdata('error', 'Gagal memperbarui data member. ' . (!empty($modelErrors) ? implode(' ', array_values($modelErrors)) : 'Periksa kembali data yang Anda masukkan.'));
            return redirect()->back()->withInput()->with('validation', $this->pelangganModel->validator); 
        }
    }

    public function delete($id)
    {
        $pelanggan = $this->pelangganModel->find($id);
        if (!$pelanggan) {
            session()->setFlashdata('error', 'Member tidak ditemukan.');
            return redirect()->to('/admin/pelanggan');
        }

        
        if ($this->pelangganModel->update($id, ['is_deleted' => 1])) {
        
            // Log Audit
            $this->auditLogModel = new AuditLogModel();
            $this->auditLogModel->insert([
                'user_id' => session()->get('karyawan_id'),
                'action' => 'DELETE_PELANGGAN',
                'description' => 'Menghapus (soft delete) member ID: ' . $id . ' (Nama: ' . $pelanggan->nama . ')',
            ]);

            session()->setFlashdata('message', 'Member berhasil dihapus (soft delete).');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus member.');
        }
        return redirect()->to('/admin/pelanggan');
    }
}