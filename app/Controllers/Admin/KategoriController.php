<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

use App\Models\AuditLogModel;

class KategoriController extends BaseController
{
    protected $kategoriModel;
    protected $session;
    protected $auditLogModel;
    protected $helpers = ['form', 'url', 'custom'];

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Kategori Produk',
            'kategori' => $this->kategoriModel->withDeleted()->where('is_deleted', 0)->findAll()
        ];
        return view('Backend/Admin/Kategori/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kategori Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Kategori/create', $data);
    }

    public function store()
    {
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama,kategori_id,{id}]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/kategori/create')->withInput()->with('validation', $this->validator);
        }

        $kategori_id = generate_sequential_id('KAT', $this->kategoriModel, 'kategori_id', 5); // KAT00001
        $this->kategoriModel->save([
            'kategori_id' => $kategori_id, // Simpan ID yang di-generate
            'nama'        => $this->request->getPost('nama'),
            'is_deleted'  => 0
        ]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'CREATE_KATEGORI',
            'description' => 'Menambah kategori baru: ' . $this->request->getPost('nama') . ' (ID: ' . $kategori_id . ')',
        ]);

        session()->setFlashdata('message', 'Data kategori berhasil ditambahkan.');
        return redirect()->to('/admin/kategori');
    }

    public function edit($id)
    {
        
        $kategori = $this->kategoriModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan: ' . $id);
        }

        $data = [
            'title' => 'Edit Kategori',
            'kategori' => $kategori, 
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Kategori/edit', $data);
    }

    public function update($id)
    {
        
        $kategoriLama = $this->kategoriModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$kategoriLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan untuk diupdate: ' . $id);
        }

        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama,kategori_id,'.$id.']'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/kategori/edit/' . $id)->withInput()->with('validation', $this->validator);
        }

        $this->kategoriModel->update($id, [
            'nama' => $this->request->getPost('nama')
        ]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'UPDATE_KATEGORI',
            'description' => 'Memperbarui kategori ID: ' . $id . ' (Nama: ' . $this->request->getPost('nama') . ')',
        ]);

        session()->setFlashdata('message', 'Data kategori berhasil diperbarui.');
        return redirect()->to('/admin/kategori');
    }

    public function delete($id)
    {
        // Soft delete
        $this->kategoriModel->update($id, ['is_deleted' => 1]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'DELETE_KATEGORI',
            'description' => 'Menghapus (soft delete) kategori ID: ' . $id,
        ]);
        session()->setFlashdata('message', 'Data kategori berhasil dihapus.');
        return redirect()->to('/admin/kategori');
    }
}