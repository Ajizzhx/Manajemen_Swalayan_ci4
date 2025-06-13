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
        $this->auditLogModel = new AuditLogModel(); // Inisialisasi AuditLogModel di constructor
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
            'validation' => session()->getFlashdata('validation') ?? $this->validation, 
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
            'no_ktp'        => $this->request->getPost('no_ktp'), 
            'email'         => $this->request->getPost('email'),
            'telepon'       => $this->request->getPost('telepon'),
            'alamat'        => $this->request->getPost('alamat'),
            'diskon_persen' => (float)($this->request->getPost('diskon_persen') ?? 1.00),
            'poin'          => (int)($this->request->getPost('poin') ?? 0), 
            'is_deleted'    => 0
        ];

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
            'validation' => session()->getFlashdata('validation') ?? $this->validation, 
        ];
        return view('Backend/Admin/Pelanggan/edit', $data);
    }

    public function update($id)
    {
        $pelanggan = $this->pelangganModel->find($id);
        if (!$pelanggan) {
            session()->setFlashdata('error', 'Member tidak ditemukan.');
            return redirect()->to('/admin/pelanggan');
        }

        // Debug log untuk tracking perubahan no_ktp
        log_message('debug', '[PelangganController::update] ID: ' . $id . ', Old KTP: ' . $pelanggan->no_ktp . ', New KTP: ' . $this->request->getPost('no_ktp'));        // Cek perubahan data sebelum validasi unik
        $no_ktp = trim($this->request->getPost('no_ktp'));
          // Validate KTP format
        if (strlen($no_ktp) !== 16) {
            session()->setFlashdata('error', 'No KTP harus terdiri dari 16 digit (saat ini: ' . strlen($no_ktp) . ' digit)');
            return redirect()->back()->withInput();
        }
        if (!ctype_digit($no_ktp)) {
            session()->setFlashdata('error', 'No KTP hanya boleh berisi angka');
            return redirect()->back()->withInput();
        }
        $updateData = [
            'nama'          => trim($this->request->getPost('nama')),
            'alamat'        => trim($this->request->getPost('alamat')),
            'diskon_persen' => (float)($this->request->getPost('diskon_persen') ?? 0.00), 
            'poin'          => (int)($this->request->getPost('poin') ?? $pelanggan->poin ?? 0),
            'no_ktp'        => $no_ktp,
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
        log_message('debug', '[PelangganController::update] Comparing KTP - Current: ' . $pelanggan->no_ktp . ', New: ' . $updateData['no_ktp']);
        
        $isDataChanged = ($updateData['nama'] !== $pelanggan->nama) ||
                         ($updateData['alamat'] !== $pelanggan->alamat) ||
                         ($updateData['diskon_persen'] != ($pelanggan->diskon_persen ?? 0.00)) || 
                         ($updateData['poin'] != ($pelanggan->poin ?? 0)) ||
                         (isset($updateData['email']) && $updateData['email'] !== $pelanggan->email) ||
                         (isset($updateData['telepon']) && $updateData['telepon'] !== $pelanggan->telepon) ||
                         (strcmp($updateData['no_ktp'], $pelanggan->no_ktp) !== 0); // Use strcmp for string comparison

        
        $isPotentiallyUniqueFieldChanged = (isset($updateData['email']) && $updateData['email'] !== $pelanggan->email) ||
                                         (isset($updateData['telepon']) && $updateData['telepon'] !== $pelanggan->telepon) ||
                                         (strcmp($updateData['no_ktp'], $pelanggan->no_ktp) !== 0);

        if (!$isDataChanged && !$isPotentiallyUniqueFieldChanged) {
            session()->setFlashdata('message', 'Tidak ada perubahan data.');
            return redirect()->to('/admin/pelanggan');
        }        $rules = $this->pelangganModel->getValidationRules();
        
        $rules['email'] = [
            'rules' => 'required|valid_email|is_unique[pelanggan.email,pelanggan_id,'.$id.']',
            'errors' => [
                'required' => 'Email harus diisi.',
                'valid_email' => 'Format email tidak valid.',
                'is_unique' => 'Email sudah digunakan member lain.'
            ]
        ];
        $rules['telepon'] = [
            'rules' => 'permit_empty|numeric|max_length[20]|is_unique[pelanggan.telepon,pelanggan_id,'.$id.']',
            'errors' => [
                'numeric' => 'Nomor telepon hanya boleh berisi angka.',
                'is_unique' => 'Nomor telepon sudah digunakan member lain.'
            ]
        ];        $rules['no_ktp'] = [
            'rules' => 'required|numeric|exact_length[16]|is_unique[pelanggan.no_ktp,pelanggan_id,'.$id.']',
            'errors' => [
                'required' => 'No KTP wajib diisi.',
                'numeric' => 'No KTP hanya boleh berisi angka.',
                'exact_length' => 'No KTP harus terdiri dari 16 digit (masukkan semua 16 angka tanpa spasi atau karakter lain)',
                'is_unique' => 'No KTP sudah digunakan member lain.'
            ]
        ];
        

        if (!$this->validate($rules)) {
            $validation = $this->validator;
            $errorMsgKtp = $validation->getError('no_ktp');
            $errorMsgEmail = $validation->getError('email');
            $errorMsgTelepon = $validation->getError('telepon');
            if ($errorMsgKtp && (strpos($errorMsgKtp, 'unique') !== false || strpos($errorMsgKtp, 'sudah terdaftar') !== false || strpos($errorMsgKtp, 'No KTP sudah digunakan') !== false)) {
                session()->setFlashdata('error', 'No KTP sudah digunakan oleh member lain.');
            } else if ($errorMsgEmail && (strpos($errorMsgEmail, 'unique') !== false || strpos($errorMsgEmail, 'sudah terdaftar') !== false)) {
                session()->setFlashdata('error', 'Email sudah digunakan oleh member lain.');
            } else if ($errorMsgTelepon && (strpos($errorMsgTelepon, 'unique') !== false || strpos($errorMsgTelepon, 'sudah terdaftar') !== false)) {
                session()->setFlashdata('error', 'Nomor telepon sudah digunakan oleh member lain.');
            } else if ($errorMsgKtp) {
                session()->setFlashdata('error', $errorMsgKtp);
            } else if ($errorMsgEmail) {
                session()->setFlashdata('error', $errorMsgEmail);
            } else if ($errorMsgTelepon) {
                session()->setFlashdata('error', $errorMsgTelepon);
            }
            return redirect()->back()->withInput()->with('validation', $validation);
        }        
        log_message('debug', '[PelangganController::update] Attempting to update with data: ' . json_encode($updateData));
        
        if ($this->pelangganModel->update($id, $updateData)) {
            log_message('debug', '[PelangganController::update] Update successful');
            // Log Audit
            $this->auditLogModel = new AuditLogModel();
            $this->auditLogModel->insert([
                'user_id' => session()->get('karyawan_id'),
                'action' => 'UPDATE_PELANGGAN',
                'description' => 'Memperbarui data member ID: ' . $id . ' (Nama: ' . $updateData['nama'] . ')',
            ]);

            session()->setFlashdata('message', 'Data member berhasil diperbarui.');
            return redirect()->to('/admin/pelanggan');        } else {
            $modelErrors = $this->pelangganModel->errors();
            log_message('error', '[PelangganController::update] Update failed. Model errors: ' . json_encode($modelErrors));
            log_message('error', '[PelangganController::update] Last query: ' . $this->pelangganModel->db->getLastQuery());
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