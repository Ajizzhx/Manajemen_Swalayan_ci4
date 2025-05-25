<?php

namespace App\Controllers\Admin\Owner;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\ExpenseModel; // Tambahkan ini
use PhpOffice\PhpSpreadsheet\Spreadsheet; // Untuk Excel
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; // Untuk Excel

class FinancialReportController extends BaseController
{
    protected $transaksiModel;
    protected $db;
    protected $session;
    protected $expenseModel; // Tambahkan ini

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->expenseModel = new ExpenseModel(); // Inisialisasi di sini
        helper(['form', 'url', 'number', 'date', 'custom']);
    }

    public function index()
    {
        // Mengambil tanggal dari GET request atau set default
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        // Set default jika tidak ada filter atau filter tidak valid
        // Default ke bulan berjalan
        if (!$this->isValidDate($startDate)) $startDate = date('Y-m-01');
        if (!$this->isValidDate($endDate)) $endDate = date('Y-m-t');
        // Pastikan end_date tidak lebih kecil dari start_date
        if (strtotime($endDate) < strtotime($startDate)) {
            $endDate = $startDate; // Atau set ke akhir bulan dari $startDate
        }

        $data['title'] = 'Laporan Keuangan';
        $data['filter'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        // 1. Ambil Detail Transaksi Pendapatan
        $transaksiPendapatan = $this->transaksiModel
            ->select('transaksi_id, created_at, total_harga, total_diskon, metode_pembayaran, pelanggan_id, karyawan_id') // Pilih kolom yang relevan
            ->where('is_deleted', 0)
            ->where('(status_penghapusan IS NULL OR status_penghapusan NOT IN ("pending_approval", "approved_for_deletion", "deleted_by_owner", "rejected"))')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        // Hitung total dari detail transaksi pendapatan
        $totalPendapatanKotor = 0;
        $totalDiskonDiberikan = 0;
        foreach ($transaksiPendapatan as $trx) {
            $totalPendapatanKotor += ((float)$trx->total_harga + (float)$trx->total_diskon); // Ini adalah total SEBELUM diskon
            $totalDiskonDiberikan += (float)$trx->total_diskon;
        }

        // 2. Ambil Detail Pengeluaran dari ExpenseModel
        $daftarPengeluaran = $this->expenseModel->getExpensesByPeriod($startDate, $endDate);
        $totalPengeluaran = 0;
        foreach ($daftarPengeluaran as $pengeluaran) {
            $totalPengeluaran += (float)$pengeluaran->jumlah;
        }

        $data['detail_pendapatan'] = $transaksiPendapatan;
        $data['detail_pengeluaran'] = $daftarPengeluaran;
        $data['summary'] = [
            'total_pendapatan_kotor' => $totalPendapatanKotor,
            'total_diskon_diberikan' => $totalDiskonDiberikan,
            'total_pengeluaran' => $totalPengeluaran,
        ];

        // Untuk form tambah pengeluaran, kita akan passing data filter ke partial view
        $data['expense_form_view'] = view('Backend/Admin/Owner/FinancialReport/expense_form', ['filter' => $data['filter'], 'validation' => \Config\Services::validation()] );

        return view('Backend/Admin/Owner/FinancialReport/index', $data);
    }

    public function saveExpense()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url('admin/owner-area/financial-reports'))->with('error', 'Aksi tidak valid.');
        }

        $validation = \Config\Services::validation();
        // Ambil rules dari model, atau definisikan di sini jika perlu
        $rules = $this->expenseModel->getValidationRules();
        // Tambahkan validasi untuk report_start_date dan report_end_date jika diperlukan,
        // tapi biasanya ini hanya untuk redirect, bukan validasi data expense.

        if (!$validation->setRules($rules)->withRequest($this->request)->run()) {
            // Ambil tanggal filter dari form (hidden input) untuk redirect
            $reportStartDate = $this->request->getPost('report_start_date') ?? date('Y-m-01');
            $reportEndDate = $this->request->getPost('report_end_date') ?? date('Y-m-t');
            $queryParams = http_build_query(['start_date' => $reportStartDate, 'end_date' => $reportEndDate]);

            return redirect()->to(site_url('admin/owner-area/financial-reports?' . $queryParams))
                ->withInput()
                ->with('error_expense', $validation->getErrors()) // Kirim error validasi khusus expense
                ->with('show_expense_form_error', true); // Flag untuk menampilkan form dengan error
        }

        $dataToSave = [
            'tanggal'   => $this->request->getPost('tanggal'),
            'kategori'  => $this->request->getPost('kategori'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'jumlah'    => $this->request->getPost('jumlah'),
        ];

        if ($this->expenseModel->save($dataToSave)) {
            session()->setFlashdata('message', 'Pengeluaran berhasil ditambahkan.');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan pengeluaran. Kesalahan database.');
        }

        // Redirect kembali ke halaman laporan dengan filter tanggal yang sama
        $reportStartDate = $this->request->getPost('report_start_date') ?? date('Y-m-01');
        $reportEndDate = $this->request->getPost('report_end_date') ?? date('Y-m-t');
        $queryParams = http_build_query([
            'start_date' => $reportStartDate,
            'end_date' => $reportEndDate
        ]);
        return redirect()->to(site_url('admin/owner-area/financial-reports?' . $queryParams));
    }

    public function deleteExpense($id = null)
    {
        if (!$this->request->is('delete')) { // Memastikan metode adalah DELETE (setelah spoofing)
            return redirect()->to(site_url('admin/owner-area/financial-reports'))->with('error', 'Aksi tidak valid.');
        }

        $expense = $this->expenseModel->find($id);

        if (!$expense) {
            session()->setFlashdata('error', 'Data pengeluaran tidak ditemukan.');
            return redirect()->to(site_url('admin/owner-area/financial-reports'));
        }

        if ($this->expenseModel->delete($id)) {
            session()->setFlashdata('message', 'Pengeluaran berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus pengeluaran.');
        }

        // Redirect kembali ke halaman laporan dengan filter tanggal yang sama jika ada
        // Ambil dari session atau parameter sebelumnya jika memungkinkan, atau default
        $startDate = $this->request->getGet('start_date') ?? session()->getFlashdata('report_start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? session()->getFlashdata('report_end_date') ?? date('Y-m-t');

        $queryParams = http_build_query([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        return redirect()->to(site_url('admin/owner-area/financial-reports?' . $queryParams));
    }

    // Helper function untuk validasi format tanggal YYYY-MM-DD
    private function isValidDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function downloadExcelReport()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        if (!$this->isValidDate($startDate)) $startDate = date('Y-m-01');
        if (!$this->isValidDate($endDate)) $endDate = date('Y-m-t');
        if (strtotime($endDate) < strtotime($startDate)) $endDate = $startDate;

        // 1. Data Pendapatan
        $transaksiPendapatan = $this->transaksiModel
            ->select('transaksi_id, created_at, total_harga, total_diskon, metode_pembayaran')
            ->where('is_deleted', 0)
            ->where('(status_penghapusan IS NULL OR status_penghapusan NOT IN ("pending_approval", "approved_for_deletion", "deleted_by_owner", "rejected"))')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $totalPendapatanKotor = 0;
        $totalDiskonDiberikan = 0;
        foreach ($transaksiPendapatan as $trx) {
            $totalPendapatanKotor += ((float)$trx->total_harga + (float)$trx->total_diskon);
            $totalDiskonDiberikan += (float)$trx->total_diskon;
        }
        $pendapatanBersih = $totalPendapatanKotor - $totalDiskonDiberikan;

        // 2. Data Pengeluaran
        $daftarPengeluaran = $this->expenseModel->getExpensesByPeriod($startDate, $endDate);
        $totalPengeluaran = 0;
        foreach ($daftarPengeluaran as $pengeluaran) {
            $totalPengeluaran += (float)$pengeluaran->jumlah;
        }

        // 3. Laba/Rugi
        $labaRugi = $pendapatanBersih - $totalPengeluaran;

        // Membuat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Keuangan');

        // Judul Laporan
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Laporan Keuangan Toko Dolog Sihite 3');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Periode: ' . format_indo($startDate) . ' s/d ' . format_indo($endDate));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('A3', ''); // Baris kosong

        // Ringkasan
        $row = 4;
        $sheet->setCellValue('A'.$row, 'Ringkasan Keuangan')->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A'.$row, 'Total Pendapatan Kotor');
        $sheet->setCellValue('B'.$row, $totalPendapatanKotor)->getStyle('B'.$row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
        $row++;
        $sheet->setCellValue('A'.$row, 'Total Diskon Diberikan');
        $sheet->setCellValue('B'.$row, $totalDiskonDiberikan)->getStyle('B'.$row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
        $row++;
        $sheet->setCellValue('A'.$row, 'Pendapatan Bersih');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $pendapatanBersih);
        $stylePendapatanBersih = $sheet->getStyle('B'.$row);
        $stylePendapatanBersih->getFont()->setBold(true);
        $stylePendapatanBersih->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
        $row++;
        $sheet->setCellValue('A'.$row, 'Total Pengeluaran');
        $sheet->setCellValue('B'.$row, $totalPengeluaran)->getStyle('B'.$row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
        $row++;
        $sheet->setCellValue('A'.$row, 'Laba / Rugi Bersih');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$row, $labaRugi);
        $styleLabaRugi = $sheet->getStyle('B'.$row);
        $styleLabaRugi->getFont()->setBold(true);
        $styleLabaRugi->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
        $row+=2; // Spasi

        // Detail Pendapatan
        $sheet->setCellValue('A'.$row, 'Detail Pendapatan (Transaksi)')->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        $headerPendapatan = ['ID Transaksi', 'Tanggal', 'Subtotal Kotor', 'Diskon', 'Total Bersih', 'Metode Bayar'];
        $sheet->fromArray($headerPendapatan, NULL, 'A'.$row);
        $sheet->getStyle('A'.$row.':F'.$row)->getFont()->setBold(true);
        $row++;
        foreach ($transaksiPendapatan as $trx) {
            $sheet->setCellValue('A'.$row, $trx->transaksi_id);
            $sheet->setCellValue('B'.$row, format_indo($trx->created_at, true));
            $sheet->setCellValue('C'.$row, (float)$trx->total_harga + (float)$trx->total_diskon)->getStyle('C'.$row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
            $sheet->setCellValue('D'.$row, (float)$trx->total_diskon)->getStyle('D'.$row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
            $sheet->setCellValue('E'.$row, (float)$trx->total_harga)->getStyle('E'.$row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
            $sheet->setCellValue('F'.$row, ucwords(str_replace('_', ' ', $trx->metode_pembayaran)));
            $row++;
        }
        $row++; // Spasi

        // Detail Pengeluaran
        $sheet->setCellValue('A'.$row, 'Detail Pengeluaran')->getStyle('A'.$row)->getFont()->setBold(true);
        $row++;
        $headerPengeluaran = ['Tanggal', 'Kategori', 'Deskripsi', 'Jumlah'];
        $sheet->fromArray($headerPengeluaran, NULL, 'A'.$row);
        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true);
        $row++;
        foreach ($daftarPengeluaran as $pengeluaran) {
            $sheet->setCellValue('A'.$row, format_indo($pengeluaran->tanggal));
            $sheet->setCellValue('B'.$row, $pengeluaran->kategori);
            $sheet->setCellValue('C'.$row, $pengeluaran->deskripsi);
            $sheet->setCellValue('D'.$row, (float)$pengeluaran->jumlah)->getStyle('D'.$row)->getNumberFormat()->setFormatCode('_("Rp"* #,##0.00_);_("Rp"* (#,##0.00);_("Rp"* "-"??_);_(@_)');
            $row++;
        }

        // Auto size kolom
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output ke browser
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Keuangan_' . str_replace('-', '', $startDate) . '_' . str_replace('-', '', $endDate) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}