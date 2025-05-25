<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\KategoriModel;

use App\Models\AuditLogModel;
use Picqer\Barcode\BarcodeGeneratorPNG; // barcode
use App\Models\SupplierModel;

class ProdukController extends BaseController
{
    protected $produkModel;
    protected $auditLogModel;
    protected $kategoriModel;
    protected $supplierModel;
    protected $helpers = ['form', 'url', 'custom']; 
    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->supplierModel = new SupplierModel();
        $this->session = \Config\Services::session();
        $this->auditLogModel = new AuditLogModel();
    }

    public function index()
    {
        
        $produkData = $this->produkModel
            ->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
            ->join('kategori', 'kategori.kategori_id = produk.kategori_id', 'left')
            ->withDeleted() 
            ->join('supplier', 'supplier.supplier_id = produk.supplier_id', 'left')
            ->where('produk.is_deleted', 0)
            ->findAll();

        $data = [
            'title' => 'Kelola Produk',
            'produks' => $produkData
        ];
        return view('Backend/Admin/Produk/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Produk Baru',
            'kategori' => $this->kategoriModel->withDeleted()->where('is_deleted', 0)->findAll(), // Ambil semua kategori aktif
            'supplier' => $this->supplierModel->withDeleted()->where('is_deleted', 0)->findAll(), // Ambil semua supplier aktif
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Produk/create', $data);
    }

    public function store()
    {
        $rules = [
            'nama'          => 'required|min_length[3]|max_length[100]',
            'kode_barcode'  => 'required|max_length[100]|is_unique[produk.kode_barcode]',
            'harga'         => 'required|numeric|greater_than[0]',
            'stok'          => 'required|integer|greater_than_equal_to[0]',
            'kategori_id'   => 'required|is_not_unique[kategori.kategori_id]',
            'supplier_id'   => 'permit_empty|is_not_unique[supplier.supplier_id]' // Supplier bisa opsional
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/produk/create')->withInput()->with('validation', $this->validator);
        }

        $kodeBarcode = $this->request->getPost('kode_barcode');
        $produk_id = generate_sequential_id('PRD', $this->produkModel, 'produk_id', 5); // PRD00001

        $this->produkModel->save([
            'produk_id'     => $produk_id, // Simpan ID yang di-generate
            'nama'          => $this->request->getPost('nama'),
            'kode_barcode'  => $kodeBarcode,
            'harga'         => $this->request->getPost('harga'),
            'stok'          => $this->request->getPost('stok'),
            'kategori_id'   => $this->request->getPost('kategori_id'),
            'supplier_id'   => $this->request->getPost('supplier_id') ?: null, // Simpan null jika kosong
            'is_deleted'    => 0
        ]);

        // Log Audit
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'CREATE_PRODUK',
            'description' => 'Menambah produk baru: ' . $this->request->getPost('nama') . ' (ID: ' . $produk_id . ', Barcode: ' . $kodeBarcode . ')',
        ]);


        session()->setFlashdata('message', 'Data produk berhasil ditambahkan.');

        // Generate Barcode setelah produk disimpan
        $barcodePath = $this->generateBarcode($produk_id, $kodeBarcode); 
        if ($barcodePath) { 
            $this->produkModel->update($produk_id, ['barcode_path' => $barcodePath]);
        }
        return redirect()->to('/admin/produk');
    }

    public function edit($id)
    {
       
        $produk = $this->produkModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$produk) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan: ' . $id);
        }

        $data = [
            'title' => 'Edit Produk',
            'produk' => $produk, 
            'kategori' => $this->kategoriModel->withDeleted()->where('is_deleted', 0)->findAll(),
            'supplier' => $this->supplierModel->withDeleted()->where('is_deleted', 0)->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('Backend/Admin/Produk/edit', $data);
    }

    public function update($id)
    {
       
        $produkLama = $this->produkModel->withDeleted()->where('is_deleted', 0)->find($id);
        if (!$produkLama) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produk tidak ditemukan untuk diupdate: ' . $id);
        }

        $rules = [
            'nama'          => 'required|min_length[3]|max_length[100]',
            'kode_barcode'  => 'required|max_length[100]|is_unique[produk.kode_barcode,produk_id,'.$id.']',
            'harga'         => 'required|numeric|greater_than[0]',
            'stok'          => 'required|integer|greater_than_equal_to[0]',
            'kategori_id'   => 'required|is_not_unique[kategori.kategori_id]',
            'supplier_id'   => 'permit_empty|is_not_unique[supplier.supplier_id]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/produk/edit/' . $id)->withInput()->with('validation', $this->validator);
        }

        $kodeBarcode = $this->request->getPost('kode_barcode');

        $this->produkModel->update($id, [
            'nama'          => $this->request->getPost('nama'),
            'kode_barcode'  => $kodeBarcode,
            'harga'         => $this->request->getPost('harga'),
            'stok'          => $this->request->getPost('stok'),
            'kategori_id'   => $this->request->getPost('kategori_id'),
            'supplier_id'   => $this->request->getPost('supplier_id') ?: null
        ]);

        // Log Audit
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'UPDATE_PRODUK',
            'description' => 'Memperbarui produk ID: ' . $id . ' (Nama: ' . $this->request->getPost('nama') . ')',
        ]);

        session()->setFlashdata('message', 'Data produk berhasil diperbarui.');

       
        $barcodePath = $this->generateBarcode($id, $kodeBarcode);
        if ($barcodePath) {
            $this->produkModel->update($id, ['barcode_path' => $barcodePath]);
        }
        return redirect()->to('/admin/produk');
    }

    public function delete($id)
    {
        // Soft delete
        $this->produkModel->update($id, ['is_deleted' => 1]);

        // Log Audit
        $this->auditLogModel = new AuditLogModel(); // Re-instantiate if needed, or move to constructor
        $this->auditLogModel->insert([
            'user_id' => session()->get('karyawan_id'),
            'action' => 'DELETE_PRODUK',
            'description' => 'Menghapus (soft delete) produk ID: ' . $id,
        ]);
        session()->setFlashdata('message', 'Data produk berhasil dihapus.');
        return redirect()->to('/admin/produk');
    }
    private function generateBarcode($produkId, $dataToEncode)
    {
        log_message('debug', "[ProdukController] generateBarcode dipanggil. produkId: {$produkId}, dataToEncode: {$dataToEncode}");
        
        $barcodeDir = FCPATH . 'uploads/barcodes/';
        log_message('debug', "[ProdukController] Barcode directory: {$barcodeDir}");

        if (!is_dir($barcodeDir)) {
            log_message('debug', "[ProdukController] Direktori barcode belum ada, mencoba membuat: {$barcodeDir}");
            if (!mkdir($barcodeDir, 0775, true)) {
                log_message('error', "[ProdukController] GAGAL membuat direktori: {$barcodeDir}. Periksa izin parent directory.");
                return null; 
            } else {
                log_message('info', "[ProdukController] Direktori barcode berhasil dibuat: {$barcodeDir}");
            }
        }
        
        if (!is_writable($barcodeDir)) {
            log_message('error', "[ProdukController] Direktori barcode TIDAK DAPAT DITULIS: {$barcodeDir}. Periksa izin direktori.");
            return null;
        } else {
            log_message('debug', "[ProdukController] Direktori barcode DAPAT DITULIS: {$barcodeDir}");
        }
        $barcodeFileName = 'produk_' . $produkId . '.png';
        $fullBarcodePath = $barcodeDir . $barcodeFileName;
        log_message('debug', "[ProdukController] Full barcode path: {$fullBarcodePath}");

        try {
            $generator = new BarcodeGeneratorPNG();
            log_message('debug', "[ProdukController] BarcodeGeneratorPNG berhasil diinisialisasi.");
            
            $barcodeImage = $generator->getBarcode($dataToEncode, $generator::TYPE_CODE_128, 2, 50); 
            log_message('debug', "[ProdukController] Gambar barcode berhasil di-generate oleh library.");

            if (file_put_contents($fullBarcodePath, $barcodeImage)) {
                log_message('info', "[ProdukController] Barcode berhasil disimpan ke: {$fullBarcodePath}");
                return 'uploads/barcodes/' . $barcodeFileName; 
            } else {
                log_message('error', "[ProdukController] GAGAL menyimpan file barcode ke: {$fullBarcodePath}. Cek izin tulis atau error file system lainnya.");
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', '[ProdukController] Barcode generation failed for produk_id ' . $produkId . ': ' . $e->getMessage());
            return null;
        }
    }
}