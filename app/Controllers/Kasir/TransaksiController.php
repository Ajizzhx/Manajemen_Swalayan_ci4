<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\PelangganModel;
use App\Models\TransaksiModel;
use App\Models\AuditLogModel;
use App\Models\DetailTransaksiModel;

class TransaksiController extends BaseController
{
    protected $produkModel;
    protected $pelangganModel;
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $db;
    protected $auditLogModel;
    protected $session;

    protected $helpers = ['form', 'url', 'number', 'date', 'custom']; 

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->pelangganModel = new PelangganModel();
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->session = \Config\Services::session();
        $this->auditLogModel = new AuditLogModel();
        $this->db = \Config\Database::connect();

        
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'kasir') {
            
        }
    }

    public function index()
    {
        $kasir_id = $this->session->get('karyawan_id');
        if (!$kasir_id) {
            // Handle jika kasir_id tidak ada di session, mungkin redirect ke login
            session()->setFlashdata('error', 'Sesi Anda tidak valid. Silakan login kembali.');
            return redirect()->to(site_url('login'));
        }

        $data['title'] = 'Transaksi Penjualan';
       
        $data['cart_items_view'] = $this->session->get('kasir_cart_items') ?? [];
        $data['selected_pelanggan'] = $this->session->get('kasir_selected_pelanggan') ?? null;
        $data['kasir_rejected_request_count'] = $this->transaksiModel
            ->where('karyawan_id', $kasir_id)
            ->where('status_penghapusan', 'rejected')->where('is_deleted', 0)->countAllResults();
        
        
        return view('Backend/Kasir/Transaksi/index', $data);
    }

    public function searchProduk()
    {
        try {
            $keyword = $this->request->getGet('term'); 
            log_message('debug', '[TransaksiController] searchProduk: Keyword received: "' . trim((string)$keyword) . '"');

            if (empty(trim((string)$keyword))) {
                log_message('debug', '[TransaksiController] searchProduk: No keyword provided or keyword is empty, returning empty array.');
                return $this->response->setJSON([]);
            }

            $builder = $this->produkModel->builder(); 
            $builder->where('is_deleted', 0);
            

            $builder->groupStart();
            $builder->like('nama', $keyword, 'both', null, true);
            $builder->orLike('kode_barcode', $keyword, 'both', null, true);
            
            if (is_numeric($keyword)) {
                $builder->orWhere('produk_id', (int)$keyword);
            }
            $builder->groupEnd();

            $produkData = $builder->limit(10)->get()->getResultObject(); 

            
            log_message('debug', '[TransaksiController] searchProduk: Last Query: ' . $this->db->getLastQuery());

            $results = [];
            if (!empty($produkData)) {
                foreach ($produkData as $p) {
                    $results[] = [
                        'id'    => $p->produk_id,
                        'label' => $p->nama . " (Kode: " . esc($p->kode_barcode) . ", Stok: " . $p->stok . ", Harga: " . number_to_currency($p->harga, 'IDR', 'id_ID', 0) . ")",
                        'value' => $p->nama,
                        'stok'  => (int)$p->stok,
                        'harga' => (float)$p->harga,
                        'nama'  => $p->nama,
                        'kode_barcode' => $p->kode_barcode,
                    ];
                }
            }
            log_message('debug', '[TransaksiController] searchProduk: Results count: ' . count($results));
            return $this->response->setJSON(['results' => $results, 'csrf_hash' => csrf_hash()]); 

        } catch (\Exception $e) {
            log_message('error', '[TransaksiController] searchProduk: Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
           
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server saat mencari produk.', 'csrf_hash' => csrf_hash()]);
        }
    }

    public function searchPelanggan()
    {
        $keyword = $this->request->getGet('term');
        $currentCsrfHash = csrf_hash(); 
        log_message('debug', '[TransaksiController::searchPelanggan] Keyword: "' . $keyword . '", Current CSRF: ' . $currentCsrfHash);

        $response_data = []; 
        $results = [];

        if ($keyword) {
            $query = $this->pelangganModel
                ->where('is_deleted', 0) 
                ->groupStart()
                    ->like('nama', $keyword, 'both', null, true)    
                    ->orLike('telepon', $keyword, 'both', null, true) 
                    ->orLike('email', $keyword, 'both', null, true)   
                ->groupEnd()
                ->findAll(10);
            log_message('debug', '[TransaksiController] searchPelanggan Last Query: ' . $this->pelangganModel->getLastQuery()->getQuery());
            log_message('debug', '[TransaksiController::searchPelanggan] Query Result Count: ' . count($query));

            if (!empty($query)) { 
                foreach ($query as $p) {
                    $results[] = [
                        'id' => $p->pelanggan_id,
                        'label' => $p->nama . ($p->telepon ? " (" . $p->telepon . ")" : ""),
                        'value' => $p->nama,
                        'nama' => $p->nama,
                        'email' => $p->email,
                        'telepon' => $p->telepon,
                        'alamat' => $p->alamat,
                        'diskon_persen' => $p->diskon_persen ?? 0.00, 
                        'poin' => $p->poin ?? 0, // Tambahkan poin pelanggan
                    ];
                }
            }
        } else {
            log_message('debug', '[TransaksiController::searchPelanggan] No keyword provided.');
        }

        
        $response_data['results'] = $results; 
        $response_data['csrf_hash'] = $currentCsrfHash;
        log_message('debug', '[TransaksiController::searchPelanggan] Response Data: ' . json_encode($response_data));
        return $this->response->setJSON($response_data);
    }

    public function addPelanggan()
    {
        $currentCsrfHash = csrf_hash();
        $postData = $this->request->getPost(); 
        log_message('debug', '[TransaksiController::addPelanggan] Attempting to add customer. Current CSRF: ' . $currentCsrfHash . '. POST Data: ' . json_encode($postData));

        // Validasi metode request
        if (!($this->request->isAJAX() && $this->request->getMethod(true) === 'POST')) {
            log_message('error', '[TransaksiController::addPelanggan] Invalid request: Not AJAX or not POST. Actual method: ' . $this->request->getMethod(true));
            return $this->response->setStatusCode(405)->setJSON([ 
                'success' => false,
                'message' => 'Metode permintaan tidak diizinkan.',
                'csrf_hash' => csrf_hash()
            ]);
        }

       
        $rules = [
            'nama_pelanggan'    => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Nama pelanggan wajib diisi.',
                    'min_length' => 'Nama pelanggan minimal 3 karakter.',
                    'max_length' => 'Nama pelanggan maksimal 100 karakter.'
                ]
            ],
            'email_pelanggan'   => [
                'rules'  => 'permit_empty|valid_email|max_length[100]|is_unique[pelanggan.email]',
                'errors' => ['valid_email' => 'Format email pelanggan tidak valid.']
            ],
            'telepon_pelanggan' => [
                'rules'  => 'permit_empty|numeric|max_length[20]|is_unique[pelanggan.telepon]',
                'errors' => ['numeric' => 'Nomor telepon pelanggan hanya boleh berisi angka.']
            ],
            'alamat_pelanggan'  => 'permit_empty|max_length[255]',
        ];

        if ($this->validate($rules)) { 
            $pelanggan_id_baru = generate_sequential_id('PLG', $this->pelangganModel, 'pelanggan_id', 5); // PLG00001
            $data = [
                'pelanggan_id'  => $pelanggan_id_baru,
                'nama'          => trim((string)($postData['nama_pelanggan'] ?? '')),
                'email'         => trim((string)($postData['email_pelanggan'] ?? '')),
                'telepon'       => trim((string)($postData['telepon_pelanggan'] ?? '')),
                'alamat'        => trim((string)($postData['alamat_pelanggan'] ?? '')),
                'poin'          => 0, // Poin awal untuk member baru dari POS
                'diskon_persen' => 1.00, // Default diskon 1% untuk member baru dari POS
                'is_deleted'    => 0,
            ];
            log_message('debug', '[TransaksiController::addPelanggan] Data prepared for model: ' . json_encode($data));
            try {
                log_message('debug', '[TransaksiController::addPelanggan] Attempting $this->pelangganModel->save($data)');
                if ($this->pelangganModel->save($data)) {
                    $pelanggan_id = $this->pelangganModel->getInsertID();
                    log_message('info', '[TransaksiController::addPelanggan] PelangganModel->save() returned true. Insert ID: ' . $pelanggan_id);

                    // Log Audit
                    $this->auditLogModel->insert([
                        'user_id' => session()->get('karyawan_id'),
                        'action' => 'CREATE_PELANGGAN_FROM_POS',
                        'description' => 'Menambah member baru dari POS: ' . $data['nama'] . ' (ID: ' . $pelanggan_id_baru . ')',
                    ]);

                    
                    $newPelanggan = $this->pelangganModel->find($pelanggan_id_baru);
                    $pelangganDataForJs = null;
                    if ($newPelanggan) {
                        $pelangganDataForJs = [
                            'pelanggan_id' => $newPelanggan->pelanggan_id, 
                            'nama' => $newPelanggan->nama,
                            'telepon' => $newPelanggan->telepon,
                            'diskon_persen' => $newPelanggan->diskon_persen ?? 0.00, 
                            'poin' => $newPelanggan->poin ?? 0, // Sertakan poin
                        ];
                    }
                    return $this->response->setJSON([
                        'success'   => true,
                        'message'   => 'Pelanggan berhasil ditambahkan.',
                        'pelanggan' => $pelangganDataForJs,
                        'csrf_hash' => csrf_hash(),
                    ]);
                } else {
                    $modelErrors = $this->pelangganModel->errors();
                    log_message('warning', '[TransaksiController::addPelanggan] PelangganModel->save() returned false. Validation errors: ' . json_encode($modelErrors));
                    return $this->response->setJSON([
                        'success' => false, 
                        'errors' => $modelErrors, 
                        'message' => 'Gagal menambahkan pelanggan. Periksa input Anda.', 
                        'csrf_hash' => csrf_hash()
                    ]);
                }
            } catch (\Throwable $e) { 
                log_message('error', '[TransaksiController::addPelanggan] Exception during save: ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\nTrace: " . $e->getTraceAsString());
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false, 
                    'message' => 'Terjadi kesalahan internal saat menyimpan pelanggan.', 
                    
                    'csrf_hash' => csrf_hash()
                ]);
            }
        } else { // Validasi gagal
            $validationErrors = $this->validator->getErrors();
            log_message('warning', '[TransaksiController::addPelanggan] Validation failed: ' . json_encode($validationErrors));
            return $this->response->setJSON(['success' => false, 'errors' => $validationErrors, 'message' => 'Data yang Anda masukkan tidak valid.', 'csrf_hash' => csrf_hash()]);
        }
    }

    public function prosesPembayaran()
    {
        // Logging debugging
        log_message('critical', '[TransaksiController::prosesPembayaran] Raw REQUEST_METHOD from server: "' . $this->request->getServer('REQUEST_METHOD') . '"');
        log_message('critical', '[TransaksiController::prosesPembayaran] $this->request->getMethod(): "' . $this->request->getMethod() . '"');
        log_message('critical', '[TransaksiController::prosesPembayaran] $this->request->is(\'post\'): ' . ($this->request->is('post') ? 'true' : 'false'));
        log_message('critical', '[TransaksiController::prosesPembayaran] Request URI: ' . (string) $this->request->getUri());
        log_message('critical', '[TransaksiController::prosesPembayaran] Request POST Data: ' . json_encode($this->request->getPost()));
        log_message('critical', '[TransaksiController::prosesPembayaran] Request Headers: ' . json_encode($this->request->headers()));

        
        if (!$this->request->is('post')) {
            log_message('error', '[TransaksiController::prosesPembayaran] Condition "!$this->request->is(\'post\')" was TRUE.');
            log_message('error', '[TransaksiController::prosesPembayaran] Actual method from $this->request->getMethod(): "' . $this->request->getMethod() . '"');
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed', 'csrf_hash' => csrf_hash()]);
        }

        $cartItems = $this->request->getPost('cart');
        $pelangganId = $this->request->getPost('pelanggan_id');
        $uangBayar = (float)$this->request->getPost('uang_bayar');
        $metodePembayaran = $this->request->getPost('metode_pembayaran'); 
        $kasirId = $this->session->get('karyawan_id'); 

        
        if (empty($cartItems) || !is_array($cartItems)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Keranjang belanja kosong.', 'csrf_hash' => csrf_hash()]);
        }

        // Hitung ulang subtotal kotor di server untuk keamanan
        $serverCalculatedSubtotalKotor = 0;
        foreach ($cartItems as $item) {
            
            $produkDb = $this->produkModel->find($item['id']);
            if (!$produkDb) {
                log_message('error', "[ProsesPembayaran] Produk dengan ID " . esc($item['id']) . " tidak ditemukan saat kalkulasi server.");
                return $this->response->setJSON(['success' => false, 'message' => 'Produk ' . esc($item['nama'] ?? ('ID ' . $item['id'])) . ' tidak ditemukan.', 'csrf_hash' => csrf_hash()]);
            }
            $serverCalculatedSubtotalKotor += (float)$produkDb->harga * (int)$item['qty'];
        }

        // Hitung diskon dan total neto di server
        $serverDiskonPersen = 0;
        $serverTotalDiskon = 0;
        if ($pelangganId) {
            $pelanggan = $this->pelangganModel->find($pelangganId);
            if ($pelanggan && isset($pelanggan->diskon_persen) && (float)$pelanggan->diskon_persen > 0) {
                $serverDiskonPersen = (float)$pelanggan->diskon_persen;
                $serverTotalDiskon = round(($serverCalculatedSubtotalKotor * $serverDiskonPersen) / 100);
            }
        }
        $serverTotalHargaNeto = $serverCalculatedSubtotalKotor - $serverTotalDiskon;

        if ($uangBayar < $serverTotalHargaNeto) {
            return $this->response->setJSON(['success' => false, 'message' => 'Uang bayar tidak mencukupi (Server calc: ' . number_to_currency($serverTotalHargaNeto, 'IDR', 'id_ID', 0) . ').', 'csrf_hash' => csrf_hash()]);
        }

        // Pengecekan stok
        foreach ($cartItems as $item) {
            $produkCek = $this->produkModel->find($item['id']);
            if (!$produkCek) {
                return $this->response->setJSON(['success' => false, 'message' => 'Produk dengan ID ' . esc($item['id']) . ' tidak ditemukan.', 'csrf_hash' => csrf_hash()]);
            }
            if ($produkCek->stok < $item['qty']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Stok produk ' . esc($produkCek->nama) . ' tidak mencukupi (tersisa ' . $produkCek->stok . ', diminta ' . $item['qty'] . ').', 'csrf_hash' => csrf_hash()]);
            }
        }

        $this->db->transBegin();
        log_message('info', '[TransaksiController::prosesPembayaran] Database transaction started.');
        try {
            
            $tanggalKode = date('Ymd');
            $prefixKode = 'TRX-' . $tanggalKode . '-';
            log_message('debug', "[ProsesPembayaran] Mencari transaksi_id terakhir. Prefix: " . $prefixKode);

            // Debug: 
            $allTransactionsTodayBuilder = $this->transaksiModel
                                        ->select('transaksi_id') 
                                        ->like('transaksi_id', $prefixKode, 'after'); 
            $allTransactionsTodayDebug = $allTransactionsTodayBuilder->findAll();
            log_message('debug', "[ProsesPembayaran] DEBUG: All transactions for today with prefix (findAll): " . json_encode($allTransactionsTodayDebug));
            if ($this->db->getLastQuery()) {
                 log_message('debug', "[ProsesPembayaran] DEBUG: Query for all transactions today: " . str_replace(["\r", "\n"], " ", $this->db->getLastQuery()->getQuery()));
            }

            
            $builder = $this->transaksiModel->builder();
            $builder->select('transaksi_id') 
                                        ->like('transaksi_id', $prefixKode, 'after') 
                                        ->orderBy('transaksi_id', 'DESC'); 
            
            
            $sqlQueryForLast = $builder->getCompiledSelect(false); 
            log_message('debug', "[ProsesPembayaran] SQL Query for lastTransactionToday (before first()): " . str_replace(["\r", "\n"], " ", $sqlQueryForLast));
            
            $lastTransactionToday = $builder->limit(1)->get()->getFirstRow(); 
            log_message('debug', "[ProsesPembayaran] Result of lastTransactionToday (raw object/array from first()): " . json_encode($lastTransactionToday));

            $nextSequence = 1;
            if ($lastTransactionToday && !empty($lastTransactionToday->transaksi_id)) { 
                log_message('debug', "[ProsesPembayaran] lastTransactionToday->transaksi_id found: " . $lastTransactionToday->transaksi_id);
                
                if (strpos($lastTransactionToday->transaksi_id, $prefixKode) === 0) { 
                    $lastSequencePart = substr($lastTransactionToday->transaksi_id, strlen($prefixKode)); 
                    log_message('debug', "[ProsesPembayaran] lastSequencePart: " . $lastSequencePart);
                    if (is_numeric($lastSequencePart)) {
                        $lastSequence = (int) $lastSequencePart;
                        $nextSequence = $lastSequence + 1;
                        log_message('debug', "[ProsesPembayaran] Calculated nextSequence: " . $nextSequence);
                    } else {
                       
                        log_message('error', "[ProsesPembayaran] Format sequence tidak valid pada transaksi_id terakhir hari ini: " . $lastTransactionToday->transaksi_id . ". Menggunakan sequence 1.");
                    }
                } else {
                    
                    log_message('warning', "[ProsesPembayaran] Transaksi ID terakhir yang ditemukan (" . $lastTransactionToday->transaksi_id . ") tidak sesuai prefix hari ini (" . $prefixKode . "). Menggunakan sequence 1.");
                }
            }
            $kodeTransaksiGenerated = $prefixKode . str_pad($nextSequence, 4, '0', STR_PAD_LEFT); // TRX-YYYYMMDD-0001 (4 digit sequence)
            log_message('info', "[ProsesPembayaran] Kode Transaksi Dihasilkan: " . $kodeTransaksiGenerated);

            $transaksiData = [
                'transaksi_id' => $kodeTransaksiGenerated, 
                'pelanggan_id' => !empty($pelangganId) ? $pelangganId : null, // Boleh NULL jika tidak ada pelanggan dipilih
                'karyawan_id'  => $kasirId, 
                'total_harga'  => $serverTotalHargaNeto, 
                'total_diskon' => $serverTotalDiskon,    
                'uang_bayar'   => $uangBayar,
                'kembalian'    => $uangBayar - $serverTotalHargaNeto,
                'created_at'   => date('Y-m-d H:i:s'), 
                
                'metode_pembayaran' => $metodePembayaran, 
                'is_deleted' => 0, 
            ];
            log_message('debug', '[ProsesPembayaran] Data Transaksi Siap Insert: ' . json_encode($transaksiData));
            if (!$this->transaksiModel->save($transaksiData)) { 
                $modelErrors = $this->transaksiModel->errors();
                $dbError = $this->db->error();
                log_message('error', '[ProsesPembayaran] GAGAL insert ke transaksiModel. Model Errors: ' . json_encode($modelErrors) . '. DB Error: Code ' . ($dbError['code'] ?? 'N/A') . ' - ' . ($dbError['message'] ?? 'N/A'));
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data transaksi utama. ' . implode(', ', $modelErrors ?: ['Error tidak diketahui.']), 'csrf_hash' => csrf_hash()]);
            }
            $transaksiId = $kodeTransaksiGenerated; 
            log_message('info', '[ProsesPembayaran] Transaksi ID Dihasilkan: ' . $transaksiId);
            if (!$transaksiId) { 
                log_message('error', '[ProsesPembayaran] GAGAL mendapatkan insert ID untuk transaksi.');
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal mendapatkan ID transaksi setelah insert.', 'csrf_hash' => csrf_hash()]);
            }


            foreach ($cartItems as $item) {
                
                $produk = $this->produkModel->where('is_deleted', 0)->find($item['id']);
                if (!$produk) {
                    log_message('error', "[ProsesPembayaran] Produk ID " . esc($item['id']) . " tidak ditemukan saat iterasi detail.");
                    $this->db->transRollback();
                    return $this->response->setJSON(['success' => false, 'message' => 'Stok produk ' . ($produk ? esc($produk->nama) : 'ID ' . esc($item['id'])) . ' tidak mencukupi.', 'csrf_hash' => csrf_hash()]);
                }
                if ($produk->stok < $item['qty']) {
                    log_message('warning', '[ProsesPembayaran] Stok tidak cukup. Produk ID: ' . $item['id'] . ', Diminta: ' . $item['qty'] . ', Stok Ada: ' . ($produk->stok ?? 'Tidak ditemukan'));
                    $this->db->transRollback();
                    return $this->response->setJSON(['success' => false, 'message' => 'Stok produk ' . esc($produk->nama) . ' tidak mencukupi.', 'csrf_hash' => csrf_hash()]);
                }

                $detail_id_baru = generate_daily_sequential_id('DTL', $this->detailTransaksiModel, 'detail_id', 5); // DTL-YYYYMMDD-00001
                $detailData = [
                    'detail_id'      => $detail_id_baru,
                    'transaksi_id'   => $transaksiId, 
                    'produk_id'      => $item['id'], 
                    'jumlah'         => (int)$item['qty'],
                    'harga_saat_itu' => (float)$produk->harga, 
                    'sub_total'      => (float)($item['qty'] * $produk->harga), 
                ];
                log_message('debug', '[ProsesPembayaran] Data Detail Transaksi Siap Insert: ' . json_encode($detailData));
                if (!$this->detailTransaksiModel->insert($detailData)) {
                    $modelErrorsDetail = $this->detailTransaksiModel->errors();
                    $dbErrorDetail = $this->db->error();
                    log_message('error', '[ProsesPembayaran] GAGAL insert ke detailTransaksiModel. Model Errors: ' . json_encode($modelErrorsDetail) . '. DB Error: Code ' . ($dbErrorDetail['code'] ?? 'N/A') . ' - ' . ($dbErrorDetail['message'] ?? 'N/A'));
                    $this->db->transRollback();
                    return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan detail item transaksi. ' . implode(', ', $modelErrorsDetail ?: ['Error tidak diketahui.']), 'csrf_hash' => csrf_hash()]);
                }
                log_message('info', '[ProsesPembayaran] Detail item transaksi berhasil disimpan untuk produk ID: ' . $item['id']);


                $newStok = $produk->stok - $item['qty'];
                log_message('debug', '[ProsesPembayaran] Update stok produk ID ' . $item['id'] . ' dari ' . $produk->stok . ' ke ' . $newStok);
                if (!$this->produkModel->update($item['id'], ['stok' => $newStok])) {
                    $modelErrorsProduk = $this->produkModel->errors();
                    $dbErrorProduk = $this->db->error();
                    log_message('error', '[ProsesPembayaran] GAGAL update stok produk ID ' . $item['id'] . '. Model Errors: ' . json_encode($modelErrorsProduk) . '. DB Error: Code ' . ($dbErrorProduk['code'] ?? 'N/A') . ' - ' . ($dbErrorProduk['message'] ?? 'N/A'));
                    $this->db->transRollback();
                    return $this->response->setJSON(['success' => false, 'message' => 'Gagal update stok produk. ' . implode(', ', $modelErrorsProduk ?: ['Error tidak diketahui.']), 'csrf_hash' => csrf_hash()]);
                }
                log_message('info', '[ProsesPembayaran] Stok produk ID ' . $item['id'] . ' berhasil diupdate.');
            }

            if ($this->db->transStatus() === false) {
                $dbError = $this->db->error(); 
                log_message('error', '[TransaksiController::prosesPembayaran] Transaksi GAGAL (transStatus false). DB Error Code: ' . ($dbError['code'] ?? 'N/A') . ' - Message: ' . ($dbError['message'] ?? 'Unknown DB Error after all operations.'));
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses transaksi. Terjadi kesalahan pada database (status).', 'csrf_hash' => csrf_hash()]);
            }
            $this->db->transCommit();
            log_message('info', '[TransaksiController::prosesPembayaran] Transaksi BERHASIL di-commit.');

            // Logika Penambahan Poin Member setelah transaksi berhasil
            $poinDiperolehTransaksiIni = 0;
            if (!empty($pelangganId)) {
                $pelangganUntukPoin = $this->pelangganModel->find($pelangganId);
                if ($pelangganUntukPoin) {
                    // Aturan: 1 poin untuk setiap Rp 10.000 belanja (dari total harga neto)
                    $poinDiperolehTransaksiIni = floor($serverTotalHargaNeto / 10000); 

                    if ($poinDiperolehTransaksiIni > 0) {
                        $poinSaatIni = $pelangganUntukPoin->poin ?? 0;
                        $poinBaru = $poinSaatIni + $poinDiperolehTransaksiIni;

                        if ($this->pelangganModel->update($pelangganId, ['poin' => $poinBaru])) {
                            log_message('info', "[ProsesPembayaran] Poin berhasil ditambahkan untuk pelanggan ID: " . $pelangganId . ". Poin diperoleh: " . $poinDiperolehTransaksiIni . ". Total poin baru: " . $poinBaru);
                           
                        } else {
                            log_message('error', "[ProsesPembayaran] Gagal update poin untuk pelanggan ID: " . $pelangganId);
                           
                        }
                    }
                }
            }

            // Log Audit
            $this->auditLogModel->insert([
                'user_id' => $kasirId,
                'action' => 'CREATE_TRANSAKSI',
                'description' => 'Memproses transaksi baru: ' . $kodeTransaksiGenerated . 
                                 ' dengan total ' . number_to_currency($serverTotalHargaNeto, 'IDR', 'id_ID', 0) .
                                 ($poinDiperolehTransaksiIni > 0 ? '. Poin diperoleh: ' . $poinDiperolehTransaksiIni : ''),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transaksi berhasil diproses.',
                'poin_diperoleh' => $poinDiperolehTransaksiIni, // Kirim poin yang diperoleh
                'transaksi_id' => $kodeTransaksiGenerated, 
                'kembalian' => $uangBayar - $serverTotalHargaNeto, 
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[TransaksiController::prosesPembayaran] Exception caught: ' . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan internal: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()]);
        }
    }

   
    public function tambahProdukByBarcodeKeKeranjang()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('kasir/transaksi')->with('error_form', 'Metode tidak diizinkan.');
        }

        $kodeBarcode = trim((string)$this->request->getPost('barcode_input'));

        if (empty($kodeBarcode)) {
            $this->session->setFlashdata('error_barcode', 'Input barcode tidak boleh kosong.');
            return redirect()->to('kasir/transaksi');
        }

        $produk = $this->produkModel
            ->where('kode_barcode', $kodeBarcode)
            ->where('is_deleted', 0)
            ->first();

        $cart = $this->session->get('kasir_cart_items') ?? [];

        if ($produk) {
            if ($produk->stok < 1) {
                $this->session->setFlashdata('error_barcode', 'Stok produk ' . esc($produk->nama) . ' habis.');
                return redirect()->to('kasir/transaksi');
            }

            $itemExists = false;
            $itemIndex = -1;
            foreach ($cart as $index => $itemInCart) {
                if ($itemInCart['id'] == $produk->produk_id) {
                    $itemExists = true;
                    $itemIndex = $index;
                    break;
                }
            }

            if ($itemExists) {
                if ($produk->stok > $cart[$itemIndex]['qty']) {
                    $cart[$itemIndex]['qty']++;
                } else {
                    $this->session->setFlashdata('error_barcode', 'Stok produk ' . esc($produk->nama) . ' tidak mencukupi untuk menambah kuantitas.');
                    return redirect()->to('kasir/transaksi');
                }
            } else {
                $cart[] = [
                    'id'    => $produk->produk_id,
                    'nama'  => $produk->nama,
                    'harga' => (float)$produk->harga,
                    'qty'   => 1,
                    'stok_awal' => (int)$produk->stok, 
                    'kode_barcode' => $produk->kode_barcode,
                ];
            }
            $this->session->set('kasir_cart_items', $cart);
            // $this->session->setFlashdata('success_barcode', 'Produk ' . esc($produk->nama) . ' ditambahkan/diperbarui.');
        } else {
            $this->session->setFlashdata('error_barcode', 'Produk dengan barcode "' . esc($kodeBarcode) . '" tidak ditemukan.');
        }
        return redirect()->to('kasir/transaksi');
    }

    public function getProdukByBarcode($kodeBarcode = null)
    {
        try {
            log_message('debug', '[TransaksiController] getProdukByBarcode: RAW Kode Barcode diterima dari URL: "' . $kodeBarcode . '"'); 
            $kodeBarcode = trim((string)$kodeBarcode); 
            
            log_message('debug', '[TransaksiController] getProdukByBarcode: Trimmed Kode Barcode for query: "' . $kodeBarcode . '"');
            log_message('debug', '[TransaksiController] getProdukByBarcode: Length of Trimmed Kode Barcode: ' . strlen($kodeBarcode));
            log_message('debug', '[TransaksiController] getProdukByBarcode: Hex of Trimmed Kode Barcode: ' . bin2hex($kodeBarcode));

            if (empty($kodeBarcode)) {
                log_message('warning', '[TransaksiController] getProdukByBarcode: Kode Barcode kosong.');
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Kode Barcode tidak valid.', 'csrf_hash' => csrf_hash()]);
            }
            
            $produk = $this->produkModel
                ->where('kode_barcode', $kodeBarcode) // Cari berdasarkan kode_barcode
                ->where('is_deleted', 0)
                
                ->first();
            
            
            log_message('debug', '[TransaksiController] getProdukByBarcode: Last Query: ' . $this->db->getLastQuery()->getQuery());
            if ($produk) {
                $result = [
                    'id'    => $produk->produk_id,
                    'stok'  => (int) $produk->stok,
                    'harga' => (float) $produk->harga,
                    'nama'  => $produk->nama,
                    'kode_barcode' => $produk->kode_barcode, 
                ];
                return $this->response->setJSON(['success' => true, 'product' => $result, 'csrf_hash' => csrf_hash()]);
            } else {
                log_message('info', '[TransaksiController] getProdukByBarcode: Produk dengan Kode Barcode "' . esc($kodeBarcode) . '" (Hex: ' . bin2hex($kodeBarcode) . ') tidak ditemukan. Query sudah di log sebelumnya.');
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Produk dengan barcode tersebut tidak ditemukan.', 'csrf_hash' => csrf_hash()]);
            }
        } catch (\Exception $e) {
            log_message('error', '[TransaksiController] Error in getProdukByBarcode for barcode ' . $kodeBarcode . ': ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server saat mencari produk.', 'csrf_hash' => csrf_hash()]);
        }
    }

    public function riwayatTransaksi()
    {
        $kasir_id = $this->session->get('karyawan_id');
        if (!$kasir_id) {
            session()->setFlashdata('error', 'Sesi kasir tidak valid atau Anda belum login.');
            return redirect()->to(site_url('login')); 
        }

        $data['title'] = 'Riwayat Transaksi'; 
        
        $builder = $this->db->table('transaksi AS t');
        $builder->select("t.*, COALESCE(p.nama, 'Pelanggan Umum') as nama_pelanggan, t.alasan_penolakan_owner", false); 
        $builder->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left'); 
        $builder->where('t.is_deleted', 0); 
        $builder->where('t.karyawan_id', $kasir_id); 

       
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $metode_pembayaran = $this->request->getGet('metode_pembayaran');
        $search_id_transaksi = $this->request->getGet('search_id_transaksi');

        // Filter berdasarkan ID Transaksi
        if ($search_id_transaksi) {
            
            $builder->like('t.transaksi_id', $search_id_transaksi, 'both'); 
        }

        // Filter berdasarkan tanggal
        if ($tanggal_awal && $tanggal_akhir) {
            $builder->where('DATE(t.created_at) >=', $tanggal_awal);
            $builder->where('DATE(t.created_at) <=', $tanggal_akhir);
        } elseif ($tanggal_awal) {
            $builder->where('DATE(t.created_at)', $tanggal_awal);
        }

        // Filter berdasarkan metode pembayaran
        if ($metode_pembayaran) {
            $builder->where('t.metode_pembayaran', $metode_pembayaran);
        }

        $builder->orderBy('t.created_at', 'DESC');
        $query = $builder->get();
        $data['riwayat_transaksi'] = $query->getResultArray();

        // Data untuk filter dropdowns/inputs
        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;
        $data['selected_metode_pembayaran'] = $metode_pembayaran;
        $data['selected_id_transaksi'] = $search_id_transaksi;
       
        $data['metode_pembayaran_list'] = $this->transaksiModel->select('metode_pembayaran')->distinct()->where('is_deleted', 0)->where('karyawan_id', $kasir_id)->where('metode_pembayaran IS NOT NULL')->where("metode_pembayaran != ''")->findAll();
        
        $data['kasir_rejected_request_count'] = $this->transaksiModel
            ->where('karyawan_id', $kasir_id)
            ->where('status_penghapusan', 'rejected')->where('is_deleted', 0)->countAllResults();

        
        $this->session->set('kasir_rejected_notification_seen', true);


        return view('Backend/Kasir/RiwayatTransaksi/index', $data);
    }
    public function detailTransaksi($transaksi_id = null) 
    {
        if (!$transaksi_id) {
            session()->setFlashdata('error', 'ID Transaksi tidak valid.');
            return redirect()->back();
        }

        $kasir_id_session = $this->session->get('karyawan_id');
        if (!$kasir_id_session) {
            session()->setFlashdata('error', 'Sesi kasir tidak valid atau Anda belum login.');
            return redirect()->to(site_url('login'));
        }

        // Fetch main transaction data
        $transaksi = $this->db->table('transaksi AS t') 
            ->select('t.*, p.nama as nama_pelanggan, p.diskon_persen as diskon_pelanggan_saat_transaksi, k.nama as nama_kasir, t.alasan_penolakan_owner') // Tambahkan t.alasan_penolakan_owner
            ->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left') 
            ->join('karyawan k', 'k.karyawan_id = t.karyawan_id', 'left')  
            ->where('t.transaksi_id', $transaksi_id) 
            ->where('t.is_deleted', 0) 
            ->get()->getRowArray();

        if (!$transaksi) {
            session()->setFlashdata('error', 'Detail transaksi tidak ditemukan.');
            return redirect()->to(site_url('kasir/riwayat-transaksi'));
        }
        
        // Security check: Pastikan kasir hanya bisa melihat transaksinya sendiri
        // Menggunakan $txransaksi['karyawan_id'] karena itu yang disimpan di tabel transaksi
        if ($transaksi['karyawan_id'] != $kasir_id_session) { 
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk melihat detail transaksi ini.');
            return redirect()->to(site_url('kasir/riwayat-transaksi'));
        }
        
        
        $detailItems = $this->db->table('detail_transaksi AS dt')
            ->select('dt.*, pr.nama as nama_produk, pr.kode_barcode')
            ->join('produk pr', 'pr.produk_id = dt.produk_id')
            ->where('dt.transaksi_id', $transaksi_id)
            ->where('dt.is_deleted', 0) 
            ->get()->getResultArray();
        
        
        $data['title'] = 'Detail Transaksi #' . esc($transaksi['transaksi_id']); 
        $data['transaksi'] = $transaksi;
        $data['detail_items'] = $detailItems;
        $data['kasir_rejected_request_count'] = $this->transaksiModel
            ->where('karyawan_id', $kasir_id_session) // Menggunakan kasir_id_session yang sudah divalidasi
            ->where('status_penghapusan', 'rejected')->where('is_deleted', 0)->countAllResults();


        return view('Backend/Kasir/RiwayatTransaksi/detail', $data);
    }
public function requestDeleteTransaksi($transaksi_id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url('kasir/riwayat-transaksi'))->with('error', 'Metode tidak diizinkan.');
        }

        if (!$transaksi_id) {
            session()->setFlashdata('error', 'ID Transaksi tidak valid.');
            return redirect()->back();
        }

        $kasir_id_session = $this->session->get('karyawan_id');
        if (!$kasir_id_session) {
            session()->setFlashdata('error', 'Sesi kasir tidak valid.');
            return redirect()->to(site_url('login'));
        }

        $transaksi = $this->transaksiModel->where('transaksi_id', $transaksi_id)
                                          ->where('karyawan_id', $kasir_id_session) // Pastikan kasir hanya bisa request hapus transaksinya sendiri
                                          ->where('is_deleted', 0)
                                          ->first();

        if (!$transaksi) {
            session()->setFlashdata('error', 'Transaksi tidak ditemukan atau Anda tidak berhak melakukan aksi ini.');
            return redirect()->to(site_url('kasir/riwayat-transaksi'));
        }

        // Cek apakah sudah pernah di-request atau sudah diproses
        if (!empty($transaksi->status_penghapusan) && $transaksi->status_penghapusan !== 'rejected') {
            session()->setFlashdata('error', 'Transaksi ini sudah dalam proses penghapusan atau sudah dihapus.');
            return redirect()->to(site_url('kasir/riwayat-transaksi'));
        }

        $alasan = $this->request->getPost('alasan_penghapusan');
        if (empty(trim((string)$alasan))) {
            session()->setFlashdata('error', 'Alasan penghapusan wajib diisi.');
            return redirect()->back()->withInput();
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // 1. Kembalikan stok produk
            $detailItems = $this->detailTransaksiModel
                ->where('transaksi_id', $transaksi_id)
                ->where('is_deleted', 0)
                ->findAll();

            if (empty($detailItems)) {
                $this->db->transRollback();
                session()->setFlashdata('error', 'Tidak ada detail item ditemukan untuk transaksi ini.');
                return redirect()->to(site_url('kasir/riwayat-transaksi'));
             }

            foreach ($detailItems as $item) {
                $this->produkModel->where('produk_id', $item->produk_id) // Menggunakan sintaks objek
                                  ->set('stok', 'stok + ' . (int)$item->jumlah, false) // Menggunakan sintaks objek
                                  ->update();
                log_message('info', '[RequestDeleteKasir] Stok produk ID ' . $item->produk_id . ' dikembalikan sebanyak ' . $item->jumlah);
            }

            // Pengurangan Poin jika transaksi melibatkan member dan ada poin yang terkait
            if (!empty($transaksi->pelanggan_id)) {
                $pelangganUntukPoin = $this->pelangganModel->find($transaksi->pelanggan_id);
                if ($pelangganUntukPoin) {
                    // Hitung ulang poin yang didapat dari transaksi ini
                    $poinDariTransaksiIni = floor($transaksi->total_harga / 10000); // total_harga adalah harga neto

                    if ($poinDariTransaksiIni > 0) {
                        $poinSaatIni = $pelangganUntukPoin->poin ?? 0;
                        $poinBaru = max(0, $poinSaatIni - $poinDariTransaksiIni); // Pastikan poin tidak negatif

                        if ($this->pelangganModel->update($transaksi->pelanggan_id, ['poin' => $poinBaru])) {
                            log_message('info', '[RequestDeleteKasir] Poin berhasil dikurangi untuk pelanggan ID: ' . $transaksi->pelanggan_id . ". Poin dikurangi: " . $poinDariTransaksiIni . ". Total poin baru: " . $poinBaru);
                        } else {
                            log_message('error', "[RequestDeleteKasir] Gagal update (kurangi) poin untuk pelanggan ID: " . $transaksi->pelanggan_id);
                           
                        }
                    }
                }
            }

            // 2. Update status transaksi
            $updateData = [
                'status_penghapusan'       => 'pending_approval',
                'alasan_pembatalan'        => $alasan, 
                'dibatalkan_oleh_karyawan_id' => $kasir_id_session, 
                'tanggal_dibatalkan'       => date('Y-m-d H:i:s'), 

            ];
            $this->transaksiModel->update($transaksi_id, $updateData);
            log_message('info', '[RequestDeleteKasir] Transaksi ID ' . $transaksi_id . ' status diubah ke pending_approval oleh kasir ID ' . $kasir_id_session);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                session()->setFlashdata('error', 'Gagal memproses permintaan penghapusan. Terjadi kesalahan database.');
                 // Log Audit Gagal
                 $this->auditLogModel->insert([
                     'user_id' => $kasir_id_session,
                     'action' => 'REQUEST_DELETE_TRANSAKSI_FAILED',
                     'description' => 'Gagal memproses permintaan penghapusan transaksi ID: ' . $transaksi_id . '. DB Error.',
                     'ip_address' => $this->request->getIPAddress(),
                     'user_agent' => $this->request->getUserAgent()->getAgentString(),
                 ]);
            } else {
                $this->db->transCommit();
                session()->setFlashdata('message', 'Permintaan penghapusan transaksi #' . esc($transaksi_id) . ' telah dikirim ke pemilik untuk persetujuan. Stok produk telah dikembalikan.');
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[RequestDeleteKasir] Exception: ' . $e->getMessage());
             // Log Audit Exception
             $this->auditLogModel->insert([
                 'user_id' => $kasir_id_session,
                 'action' => 'REQUEST_DELETE_TRANSAKSI_EXCEPTION',
                 'description' => 'Exception saat memproses permintaan penghapusan transaksi ID: ' . $transaksi_id . '. Error: ' . $e->getMessage(),
                 'ip_address' => $this->request->getIPAddress(),
                 'user_agent' => $this->request->getUserAgent()->getAgentString(),
             ]);
            session()->setFlashdata('error', 'Terjadi kesalahan internal: ' . $e->getMessage());
        }

        return redirect()->to(site_url('kasir/riwayat-transaksi'));
    }

   
    public function handleDeleteRequest($transaksi_id = null)
    {
        if (!$this->request->is('post')) {
            log_message('error', '[handleDeleteRequest] Metode tidak diizinkan. Seharusnya POST.');
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Metode tidak diizinkan.']);
        }

        if (!$transaksi_id) {
            log_message('error', '[handleDeleteRequest] ID Transaksi tidak disediakan.');
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'ID Transaksi tidak valid.']);
        }

        $status = $this->request->getPost('status'); // 'approved' atau 'rejected'
        if (!in_array($status, ['approved', 'rejected'])) {
            log_message('error', '[handleDeleteRequest] Status permintaan tidak valid: ' . $status);
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Status permintaan tidak valid.']);
        }
        
        $alasan_penolakan = ($status === 'rejected') ? trim((string)$this->request->getPost('alasan_penolakan')) : null;

        $transaksi = $this->transaksiModel->find($transaksi_id);
        if (!$transaksi) {
            log_message('error', '[handleDeleteRequest] Transaksi ID ' . $transaksi_id . ' tidak ditemukan.');
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Transaksi tidak ditemukan.']);
        }

       
        if ($transaksi->status_penghapusan !== 'pending_approval') {
            log_message('warning', '[handleDeleteRequest] Transaksi ID ' . $transaksi_id . ' tidak dalam status pending_approval. Status saat ini: ' . $transaksi->status_penghapusan);
           
        }

        $this->db->transBegin();
        log_message('info', '[handleDeleteRequest] Memulai transaksi database untuk ID: ' . $transaksi_id . ', Status: ' . $status);
        try {
            if ($status === 'approved') {
              
                $this->transaksiModel->update($transaksi_id, ['status_penghapusan' => 'approved']);
                log_message('info', '[handleDeleteRequest APPROVED] Transaksi ' . $transaksi_id . ' disetujui untuk dihapus.');
              
            } elseif ($status === 'rejected') {
      
                $this->transaksiModel->update($transaksi_id, [
                    'status_penghapusan' => 'rejected',
                    'alasan_penolakan_owner' => $alasan_penolakan,
                ]);
                log_message('info', "[handleDeleteRequest REJECTED] Transaksi ID {$transaksi_id} status diupdate ke 'rejected'. Alasan: '{$alasan_penolakan}'");

                // 2. Sesuaikan stok: Karena transaksi TIDAK JADI DIBATALKAN,
         
                $detailItems = $this->detailTransaksiModel->where('transaksi_id', $transaksi_id)->findAll();
                foreach ($detailItems as $item) {
                    $this->produkModel->where('produk_id', $item->produk_id)
                                      ->set('stok', 'stok - ' . (int)$item->jumlah, false)
                                      ->update();
                    log_message('debug', "[handleDeleteRequest REJECTED] Stok produk ID: {$item->produk_id} dikurangi sebanyak {$item->jumlah} untuk transaksi ID: {$transaksi_id}. Penjualan tetap valid.");
                }
                log_message('info', "[handleDeleteRequest REJECTED] Penyesuaian stok selesai untuk transaksi ID: {$transaksi_id}.");

              
                if ($transaksi->pelanggan_id) {
                    log_message('debug', "[handleDeleteRequest REJECTED] Transaksi ID: {$transaksi_id} memiliki pelanggan_id: {$transaksi->pelanggan_id}. Memproses pengembalian poin.");
                    $pelangganUntukPoin = $this->pelangganModel->find($transaksi->pelanggan_id);
                    if ($pelangganUntukPoin) {
                        log_message('debug', '[handleDeleteRequest REJECTED] Pelanggan ditemukan: ' . $pelangganUntukPoin->nama . '. Poin saat ini (setelah dikurangi saat request): ' . ($pelangganUntukPoin->poin ?? 0));

                        $poinDariTransaksiIni = floor((float)$transaksi->total_harga / 10000);
                        log_message('debug', '[handleDeleteRequest REJECTED] Poin yang dihitung dari transaksi ini (total_harga ' . $transaksi->total_harga . '): ' . $poinDariTransaksiIni);

                        if ($poinDariTransaksiIni > 0) {
                            $poinSaatIniDatabase = (int)($pelangganUntukPoin->poin ?? 0);
                            $poinBaru = $poinSaatIniDatabase + $poinDariTransaksiIni; 
                            log_message('debug', '[handleDeleteRequest REJECTED] Poin pelanggan akan diupdate menjadi: ' . $poinBaru);

                            if ($this->pelangganModel->update($transaksi->pelanggan_id, ['poin' => $poinBaru])) {
                                log_message('info', "[handleDeleteRequest REJECTED] Poin berhasil dikembalikan ke pelanggan ID: {$transaksi->pelanggan_id}. Poin ditambah: +{$poinDariTransaksiIni}. Total poin baru: {$poinBaru}.");
                            } else {
                                $modelErrors = $this->pelangganModel->errors();
                                log_message('error', "[handleDeleteRequest REJECTED] Gagal mengembalikan poin ke pelanggan ID: {$transaksi->pelanggan_id}. Model errors: " . json_encode($modelErrors));
                               
                            }
                        } else {
                            log_message('info', '[handleDeleteRequest REJECTED] Tidak ada poin (' . $poinDariTransaksiIni . ') yang perlu dikembalikan dari transaksi ini (total_harga mungkin < 10000 atau 0).');
                        }
                    } else {
                        log_message('warning', '[handleDeleteRequest REJECTED] Pelanggan dengan ID ' . $transaksi->pelanggan_id . ' tidak ditemukan untuk pengembalian poin.');
                    }
                } else {
                    log_message('info', "[handleDeleteRequest REJECTED] Transaksi ID: {$transaksi_id} tidak memiliki pelanggan_id, tidak ada poin untuk dikembalikan.");
                }
                log_message('info', "[handleDeleteRequest REJECTED] Proses penolakan untuk transaksi ID: {$transaksi_id} selesai. Stok dan poin telah disesuaikan.");
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_message('error', '[handleDeleteRequest] Transaksi database gagal (rollback) untuk ID: ' . $transaksi_id);
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal memproses permintaan hapus. Terjadi kesalahan database.']);
            } else {
                $this->db->transCommit();
                log_message('info', '[handleDeleteRequest] Transaksi database berhasil (commit) untuk ID: ' . $transaksi_id);
                $pesanSukses = ($status === 'approved') ? 'Transaksi berhasil disetujui untuk dihapus.' : 'Permintaan hapus ditolak. Poin dan stok telah disesuaikan.';
               
                
             
                session()->setFlashdata('message', $pesanSukses);
               
                return redirect()->to(site_url('admin/owner-area/transaksi-approval')); // Contoh URL
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[handleDeleteRequest] Exception untuk ID ' . $transaksi_id . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Untuk AJAX response
            // return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Terjadi kesalahan internal: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()]);
            
            session()->setFlashdata('error', 'Terjadi kesalahan internal: ' . $e->getMessage());
            return redirect()->to(site_url('admin/owner-area/transaksi-approval')); // Contoh URL
        }
    }
    
}
