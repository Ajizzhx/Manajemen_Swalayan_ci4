<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SupplierModel;

use App\Models\AuditLogModel;

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $session; 
    protected $auditLogModel;
    protected $helpers = ['form', 'url', 'custom'];
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->session = \Config\Services::session();

        
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Supplier',
           
            'suppliers' => $this->supplierModel->withDeleted()->where('is_deleted', 0)->findAll()

        ];
        return view('Backend/Admin/Supplier/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Supplier Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Supplier/create', $data);
    }

    public function store()
    {
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'alamat' => 'permit_empty|max_length[255]',
            'telepon' => 'permit_empty|min_length[7]|max_length[20]|alpha_numeric_punct'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/supplier/create')->withInput()->with('validation', $this->validator);
        }

        $supplier_id = generate_sequential_id('SUP', $this->supplierModel, 'supplier_id', 5); // SUP00001
        $this->supplierModel->save([
            'supplier_id' => $supplier_id, // Simpan ID yang di-generate
            'nama'        => $this->request->getPost('nama'),
            'alamat'      => $this->request->getPost('alamat'),
            'telepon'     => $this->request->getPost('telepon'),
            'is_deleted'  => 0 
        ]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'CREATE_SUPPLIER',
            'description' => 'Menambah supplier baru: ' . $this->request->getPost('nama') . ' (ID: ' . $supplier_id . ')',
        ]);

        session()->setFlashdata('message', 'Data supplier berhasil ditambahkan.');
        return redirect()->to('/admin/supplier');
    }

    public function edit($id)
    {
        
        $supplier = $this->supplierModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$supplier) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Supplier tidak ditemukan: ' . $id);
        }

        $data = [
            'title' => 'Edit Supplier',
            'supplier' => $supplier, 
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Supplier/edit', $data);
    }

    public function update($id)
    {
        
        $supplierLama = $this->supplierModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$supplierLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Supplier tidak ditemukan untuk diupdate: ' . $id);
        }

        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'alamat' => 'permit_empty|max_length[255]',
            'telepon' => 'permit_empty|min_length[7]|max_length[20]|alpha_numeric_punct'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/supplier/edit/' . $id)->withInput()->with('validation', $this->validator);
        }

        $this->supplierModel->update($id, [
            'nama' => $this->request->getPost('nama'),
            'alamat' => $this->request->getPost('alamat'),
            'telepon' => $this->request->getPost('telepon')
        ]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'UPDATE_SUPPLIER',
            'description' => 'Memperbarui supplier ID: ' . $id . ' (Nama: ' . $this->request->getPost('nama') . ')',
        ]);

        session()->setFlashdata('message', 'Data supplier berhasil diperbarui.');
        return redirect()->to('/admin/supplier');
    }

    public function delete($id)
    {
        
        $this->supplierModel->update($id, ['is_deleted' => 1]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel();
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'DELETE_SUPPLIER',
            'description' => 'Menghapus (soft delete) supplier ID: ' . $id,
        ]);
        session()->setFlashdata('message', 'Data supplier berhasil dihapus.');
        return redirect()->to('/admin/supplier');
    }
}