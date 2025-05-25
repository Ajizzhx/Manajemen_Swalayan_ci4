<?php

namespace App\Controllers\Admin\Owner;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;
use App\Models\KaryawanModel;

class TransaksiManagementController extends BaseController
{
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $produkModel;
    protected $karyawanModel;
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->produkModel = new ProdukModel();
        $this->karyawanModel = new KaryawanModel();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'number', 'date', 'custom']); 
    }

    public function approvalList()
    {
        $data['title'] = 'Persetujuan Penghapusan Transaksi';

        $builder = $this->db->table('transaksi as t');
        $builder->select("t.*, COALESCE(p.nama, 'Pelanggan Umum') as nama_pelanggan, k_kasir.nama as nama_kasir, k_pembatal.nama as nama_pembatal", false);
        $builder->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left');
        $builder->join('karyawan k_kasir', 'k_kasir.karyawan_id = t.karyawan_id', 'left'); // Kasir yang melakukan transaksi
        $builder->join('karyawan k_pembatal', 'k_pembatal.karyawan_id = t.dibatalkan_oleh_karyawan_id', 'left'); // Karyawan yang membatalkan/request hapus
        $builder->where('t.is_deleted', 0);
        $builder->whereIn('t.status_penghapusan', ['pending_approval', 'approved_for_deletion']); // Tampilkan yang pending atau sudah diapprove
        $builder->orderBy('t.tanggal_dibatalkan', 'DESC'); // Menggunakan tanggal_dibatalkan untuk sorting

        $data['transaksi_pending'] = $builder->get()->getResultArray();

        return view('Backend/Admin/Owner/Transaksi/approval_list', $data);
    }

    public function approveDeletion($transaksi_id = null)
    {
        if (!$this->request->is('post') || !$transaksi_id) {
            return redirect()->to(site_url('admin/owner-area/transaksi-approval'))->with('error', 'Aksi tidak valid.');
        }

        $pemilik_id = $this->session->get('karyawan_id'); // Pastikan ini ID pemilik

        $transaksi = $this->transaksiModel->where('transaksi_id', $transaksi_id)
                                          ->where('status_penghapusan', 'pending_approval')
                                          ->first();

        if (!$transaksi) {
            session()->setFlashdata('error', 'Transaksi tidak ditemukan atau status tidak valid untuk persetujuan.');
            return redirect()->to(site_url('admin/owner-area/transaksi-approval'));
        }

        $updateData = [
            'status_penghapusan'         => 'approved_for_deletion',
            'penghapusan_disetujui_oleh' => $pemilik_id,
            'tanggal_approval_hapus'     => date('Y-m-d H:i:s'),
        ];

        if ($this->transaksiModel->update($transaksi_id, $updateData)) {
            session()->setFlashdata('message', 'Penghapusan transaksi #' . esc($transaksi_id) . ' telah disetujui.');
        } else {
            session()->setFlashdata('error', 'Gagal menyetujui penghapusan transaksi.');
        }
        return redirect()->to(site_url('admin/owner-area/transaksi-approval'));
    }

    public function rejectDeletion($transaksi_id = null)
    {
        if (!$this->request->is('post') || !$transaksi_id) {
            return redirect()->to(site_url('admin/owner-area/transaksi-approval'))->with('error', 'Aksi tidak valid.');
        }

        $pemilik_id = $this->session->get('karyawan_id');

        $transaksi = $this->transaksiModel->where('transaksi_id', $transaksi_id)
                                          ->where('status_penghapusan', 'pending_approval') // Hanya bisa reject yang masih pending
                                          ->first();

        if (!$transaksi) {
            session()->setFlashdata('error', 'Transaksi tidak ditemukan atau status tidak valid untuk penolakan.');
            return redirect()->to(site_url('admin/owner-area/transaksi-approval'));
        }

        $this->db->transBegin();
        try {
            // 1. Kembalikan (kurangi) stok produk yang sebelumnya ditambah saat request hapus
            $detailItems = $this->detailTransaksiModel
                ->where('transaksi_id', $transaksi_id)
                ->findAll();

            foreach ($detailItems as $item) {
                // Validasi stok sebelum mengurangi, untuk mencegah stok minus jika ada perubahan manual
                $produk = $this->produkModel->find($item->produk_id); // Menggunakan sintaks objek
                if ($produk && $produk->stok >= (int)$item->jumlah) { // Menggunakan sintaks objek
                    $this->produkModel->where('produk_id', $item->produk_id) // Menggunakan sintaks objek
                                      ->set('stok', 'stok - ' . (int)$item->jumlah, false) // Menggunakan sintaks objek
                                      ->update();
                    log_message('info', '[RejectDeletionOwner] Stok produk ID ' . $item->produk_id . ' dikurangi kembali sebanyak ' . $item->jumlah);
                } else {
                    log_message('warning', '[RejectDeletionOwner] Stok produk ID ' . $item->produk_id . ' tidak mencukupi untuk dikurangi saat reject. Stok saat ini: ' . ($produk->stok ?? 'N/A'));
                    // Handle error ini, mungkin dengan pesan atau biarkan (tergantung kebijakan bisnis)
                }
            }

            // 2. Update status transaksi
            $updateData = [
                'status_penghapusan'         => null, // Kembalikan ke status normal
                'alasan_pembatalan'          => null, // Hapus alasan pembatalan sebelumnya
                'dibatalkan_oleh_karyawan_id'=> null, // Hapus ID karyawan yang request
                'tanggal_dibatalkan'         => null, // Hapus tanggal request
                // Jika Anda punya kolom terpisah untuk approval, set juga ke null
                // 'penghapusan_disetujui_oleh' => null, 
                // 'tanggal_approval_hapus'     => null, 
            ];
            $this->transaksiModel->update($transaksi_id, $updateData);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                session()->setFlashdata('error', 'Gagal menolak penghapusan. Kesalahan database.');
            } else {
                $this->db->transCommit();
                session()->setFlashdata('message', 'Permintaan penghapusan transaksi #' . esc($transaksi_id) . ' telah ditolak. Stok produk telah disesuaikan kembali.');
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[RejectDeletionOwner] Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan internal saat menolak: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/owner-area/transaksi-approval'));
    }

    public function permanentDelete($transaksi_id = null)
    {
        if (!$this->request->is('post') || !$transaksi_id) {
            return redirect()->to(site_url('admin/owner-area/transaksi-approval'))->with('error', 'Aksi tidak valid.');
        }

        $pemilik_id = $this->session->get('karyawan_id');

        $transaksi = $this->transaksiModel->where('transaksi_id', $transaksi_id)
                                          ->where('status_penghapusan', 'approved_for_deletion') // Hanya bisa hapus yang sudah diapprove
                                          ->first();

        if (!$transaksi) {
            session()->setFlashdata('error', 'Transaksi tidak ditemukan atau belum disetujui untuk penghapusan permanen.');
            return redirect()->to(site_url('admin/owner-area/transaksi-approval'));
        }

        // Soft delete transaksi utama
        // Stok sudah dikembalikan saat kasir request, jadi tidak perlu diubah lagi di sini.
        // Detail transaksi juga bisa di-soft delete jika diperlukan.
        if ($this->transaksiModel->update($transaksi_id, ['is_deleted' => 1, 'status_penghapusan' => 'deleted_by_owner'])) {
            // Opsional: soft delete juga detail_transaksi jika diperlukan
            // $this->detailTransaksiModel->where('transaksi_id', $transaksi_id)->set(['is_deleted' => 1])->update();
            session()->setFlashdata('message', 'Transaksi #' . esc($transaksi_id) . ' berhasil dihapus secara permanen (soft delete).');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus transaksi secara permanen.');
        }
        return redirect()->to(site_url('admin/owner-area/transaksi-approval'));
    }
    public function getDetailTransaksiItems($transaksi_id = null)
    {
        if (!$this->request->isAJAX() || !$transaksi_id) {
            log_message('error', '[AJAX Detail] Permintaan tidak valid. Is AJAX: ' . ($this->request->isAJAX() ? 'true':'false') . ', Transaksi ID: ' . $transaksi_id);
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Permintaan tidak valid.']);
        }

        try {
            // 1. Ambil data header transaksi
            $transaksi = $this->db->table('transaksi AS t')
                ->select('t.*, p.nama as nama_pelanggan, k.nama as nama_kasir')
                ->join('pelanggan p', 'p.pelanggan_id = t.pelanggan_id', 'left')
                ->join('karyawan k', 'k.karyawan_id = t.karyawan_id', 'left')
                ->where('t.transaksi_id', $transaksi_id)
                ->where('t.is_deleted', 0)
                ->get()->getRowObject(); // Ambil sebagai objek

            if (!$transaksi) {
                log_message('info', '[AJAX Detail] Header transaksi tidak ditemukan untuk ID: ' . $transaksi_id);
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data transaksi tidak ditemukan.']);
            }

            // 2. Ambil detail item transaksi
            $detailItems = $this->detailTransaksiModel
                ->select('detail_transaksi.*, produk.nama as nama_produk, produk.kode_barcode as kode_produk')
                ->join('produk', 'produk.produk_id = detail_transaksi.produk_id', 'left')
                ->where('detail_transaksi.transaksi_id', $transaksi_id)
                ->where('detail_transaksi.is_deleted', 0)
                ->findAll();

            // Format data untuk ditampilkan di modal
            $html = '<div class="row">';
            $html .= '  <div class="col-md-6">';
            $html .= '    <table class="table table-condensed">';
            $html .= '      <tr><th style="width:30%;">ID Transaksi</th><td>: ' . esc($transaksi->transaksi_id) . '</td></tr>';
            $html .= '      <tr><th>Tanggal</th><td>: ' . esc(format_indo($transaksi->created_at, true)) . '</td></tr>';
            $html .= '      <tr><th>Member</th><td>: ' . esc($transaksi->nama_pelanggan ?? 'Umum') . '</td></tr>';
            $html .= '      <tr><th>Kasir</th><td>: ' . esc($transaksi->nama_kasir ?? 'N/A') . '</td></tr>';
            $html .= '    </table>';
            $html .= '  </div>';
            $html .= '  <div class="col-md-6">';
            $html .= '    <table class="table table-condensed">';
            $subtotal_kotor = (float)($transaksi->total_harga ?? 0) + (float)($transaksi->total_diskon ?? 0);
            $html .= '      <tr><th style="width:30%;">Subtotal</th><td style="text-align:right;">: ' . esc(number_to_currency($subtotal_kotor, 'IDR', 'id_ID', 0)) . '</td></tr>';
            if (!empty($transaksi->total_diskon) && (float)$transaksi->total_diskon > 0) {
                $html .= '  <tr><th>Diskon</th><td style="text-align:right;">: - ' . esc(number_to_currency($transaksi->total_diskon, 'IDR', 'id_ID', 0)) . '</td></tr>';
            }
            $html .= '      <tr><th>Total Belanja</th><td style="text-align:right;">: ' . esc(number_to_currency($transaksi->total_harga, 'IDR', 'id_ID', 0)) . '</td></tr>';
            $html .= '      <tr><th>Uang Bayar</th><td style="text-align:right;">: ' . esc(number_to_currency($transaksi->uang_bayar, 'IDR', 'id_ID', 0)) . '</td></tr>';
            $html .= '      <tr><th>Kembalian</th><td style="text-align:right;">: ' . esc(number_to_currency($transaksi->kembalian, 'IDR', 'id_ID', 0)) . '</td></tr>';
            $html .= '      <tr><th>Metode Bayar</th><td style="text-align:right;">: ' . esc(ucwords(str_replace('_', ' ', $transaksi->metode_pembayaran ?? 'N/A'))) . '</td></tr>';
            $html .= '    </table>';
            $html .= '  </div>';
            $html .= '</div>';

            $html .= '<h4>Item Transaksi:</h4>';
            if (!empty($detailItems)) {
                $html .= '<div class="table-responsive"><table class="table table-striped table-bordered table-condensed">';
                $html .= '<thead><tr><th>No.</th><th>Kode Produk</th><th>Nama Produk</th><th style="text-align:right;">Harga Satuan</th><th style="text-align:center;">Jumlah</th><th style="text-align:right;">Subtotal</th></tr></thead>';
                $html .= '<tbody>';
                $no_item = 1;
                foreach ($detailItems as $item) {
                    $html .= '<tr>';
                    $html .= '<td>' . $no_item++ . '</td>';
                    $html .= '<td>' . esc($item->kode_produk ?? 'N/A') . '</td>';
                    $html .= '<td>' . esc($item->nama_produk ?? 'N/A') . '</td>';
                    $html .= '<td style="text-align:right;">' . esc(number_to_currency($item->harga_saat_itu, 'IDR', 'id_ID', 0)) . '</td>';
                    $html .= '<td style="text-align:center;">' . esc($item->jumlah) . '</td>';
                    $html .= '<td style="text-align:right;">' . esc(number_to_currency($item->sub_total, 'IDR', 'id_ID', 0)) . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</tbody></table></div>';
            } else {
                $html .= '<p>Tidak ada item detail untuk transaksi ini.</p>';
            }

            return $this->response->setJSON(['success' => true, 'html' => $html]);

        } catch (\Exception $e) {
            log_message('error', '[AJAX Detail] Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Terjadi kesalahan internal di server.']);
        }
    }
}