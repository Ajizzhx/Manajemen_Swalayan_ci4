<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Authentication Routes
$routes->get('/', 'Auth::index', ['as' => 'login.form']);
$routes->get('login', 'Auth::index'); // Alias untuk login form
$routes->post('auth/loginProcess', 'Auth::loginProcess', ['as' => 'login.process']);

$routes->get('logout', 'Auth::logout', ['as' => 'logout']);

// Route for 2FA
$routes->get('auth/verify-otp', 'Auth::verifyOtp');
$routes->post('auth/process-otp', 'Auth::processOtp');
$routes->get('auth/resend-otp', 'Auth::resendOtp');

// --- ADMIN & PEMILIK GROUP ---
$routes->group('admin', ['filter' => 'roleGuard:admin,pemilik', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'DashboardController::index', ['as' => 'admin.dashboard.index']); // Mengarahkan /admin ke dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin.dashboard']);

    // Rute CRUD Produk
    $routes->get('produk', 'ProdukController::index', ['as' => 'admin.produk.index']);
    $routes->get('produk/create', 'ProdukController::create', ['as' => 'admin.produk.create']);
    $routes->post('produk/store', 'ProdukController::store', ['as' => 'admin.produk.store']);
    $routes->get('produk/edit/(:segment)', 'ProdukController::edit/$1', ['as' => 'admin.produk.edit']);
    $routes->post('produk/update/(:segment)', 'ProdukController::update/$1', ['as' => 'admin.produk.update']);
    $routes->post('produk/delete/(:segment)', 'ProdukController::delete/$1', ['as' => 'admin.produk.delete']);

    // Rute CRUD Kategori
    $routes->get('kategori', 'KategoriController::index', ['as' => 'admin.kategori.index']);
    $routes->get('kategori/create', 'KategoriController::create', ['as' => 'admin.kategori.create']);
    $routes->post('kategori/store', 'KategoriController::store', ['as' => 'admin.kategori.store']);
    $routes->get('kategori/edit/(:segment)', 'KategoriController::edit/$1', ['as' => 'admin.kategori.edit']);
    $routes->post('kategori/update/(:segment)', 'KategoriController::update/$1', ['as' => 'admin.kategori.update']);
    $routes->post('kategori/delete/(:segment)', 'KategoriController::delete/$1', ['as' => 'admin.kategori.delete']);

    // Rute CRUD Supplier
    $routes->get('supplier', 'SupplierController::index', ['as' => 'admin.supplier.index']);
    $routes->get('supplier/create', 'SupplierController::create', ['as' => 'admin.supplier.create']);
    $routes->post('supplier/store', 'SupplierController::store', ['as' => 'admin.supplier.store']);
    $routes->get('supplier/edit/(:segment)', 'SupplierController::edit/$1', ['as' => 'admin.supplier.edit']);
    $routes->post('supplier/update/(:segment)', 'SupplierController::update/$1', ['as' => 'admin.supplier.update']);
    $routes->post('supplier/delete/(:segment)', 'SupplierController::delete/$1', ['as' => 'admin.supplier.delete']);

    // Rute CRUD Pelanggan
    $routes->get('pelanggan', 'PelangganController::index', ['as' => 'admin.pelanggan.index']);
    $routes->get('pelanggan/create', 'PelangganController::create', ['as' => 'admin.pelanggan.create']);
    $routes->post('pelanggan/store', 'PelangganController::store', ['as' => 'admin.pelanggan.store']);
    $routes->get('pelanggan/edit/(:segment)', 'PelangganController::edit/$1', ['as' => 'admin.pelanggan.edit']);
    $routes->post('pelanggan/update/(:segment)', 'PelangganController::update/$1', ['as' => 'admin.pelanggan.update']);
    $routes->post('pelanggan/delete/(:segment)', 'PelangganController::delete/$1', ['as' => 'admin.pelanggan.delete']);

    // Rute Laporan Umum (Admin & Pemilik)
    $routes->get('laporan/transaksi', 'LaporanController::transaksi', ['as' => 'admin.laporan.transaksi']);
    $routes->get('laporan/transaksi/detail/(:segment)', 'LaporanController::detailTransaksi/$1', ['as' => 'admin.laporan.transaksi.detail']);
    $routes->get('laporan/transaksi/export', 'LaporanController::exportTransaksiExcel', ['as' => 'admin.laporan.transaksi.export']);
    $routes->get('laporan/pendapatan', 'LaporanController::pendapatan', ['as' => 'admin.laporan.pendapatan']);
    $routes->get('laporan/produk-terlaris', 'LaporanController::produkTerlaris', ['as' => 'admin.laporan.produkterlaris']);
    $routes->get('laporan/metode-pembayaran-populer', 'LaporanController::metodePembayaranPopuler', ['as' => 'admin.laporan.metodepembayaran']);
    

    // Pengaturan & Profil Umum (Admin & Pemilik) - Menggunakan namespace App\Controllers\Common
    $routes->get('settings', '\App\Controllers\Common\SettingsController::index', ['as' => 'admin.settings']);
    $routes->get('profile', '\App\Controllers\Common\ProfileController::index', ['as' => 'admin.profile']);
    $routes->post('profile/update', '\App\Controllers\Common\ProfileController::update', ['as' => 'admin.profile.update']);

    // --- PEMILIK SPECIFIC SUB-GROUP ---
    $routes->group('owner-area', ['filter' => 'roleGuard:pemilik'], static function ($routes) { // Filter 'owneronly' juga bisa digunakan jika secara spesifik memeriksa 'pemilik'
        $routes->get('dashboard', 'DashboardController::index', ['as' => 'owner.dashboard']); // Dashboard pemilik, bisa sama dengan admin atau berbeda

        // Audit Log
        $routes->get('audit-log', 'Owner\AuditController::index', ['as' => 'owner.auditlog']);

        // Financial Reports
        $routes->get('financial-reports', 'Owner\FinancialReportController::index', ['as' => 'owner.financialreports']);
        $routes->post('financial-reports/save-expense', 'Owner\FinancialReportController::saveExpense', ['as' => 'owner.financialreports.saveexpense']);
        $routes->delete('financial-reports/delete-expense/(:num)', 'Owner\FinancialReportController::deleteExpense/$1', ['as' => 'owner.financialreports.deleteexpense']);
        $routes->get('financial-reports/download-excel-report', 'Owner\FinancialReportController::downloadExcelReport', ['as' => 'owner.financialreports.downloadexcel']);

        // Rute CRUD Karyawan (Hanya untuk Pemilik)
        $routes->get('karyawan', 'KaryawanController::index', ['as' => 'owner.karyawan.index']);
        $routes->get('karyawan/create', 'KaryawanController::create', ['as' => 'owner.karyawan.create']);
        $routes->post('karyawan/store', 'KaryawanController::store', ['as' => 'owner.karyawan.store']);
        $routes->get('karyawan/edit/(:segment)', 'KaryawanController::edit/$1', ['as' => 'owner.karyawan.edit']);
        $routes->put('karyawan/update/(:segment)', 'KaryawanController::update/$1', ['as' => 'owner.karyawan.update']);
        $routes->delete('karyawan/delete/(:segment)', 'KaryawanController::delete/$1', ['as' => 'owner.karyawan.delete']);

        // Rute untuk manajemen approval penghapusan transaksi oleh pemilik
        $routes->get('transaksi-approval', 'Owner\TransaksiManagementController::approvalList', ['as' => 'owner.transaksi.approval.list']);
        $routes->post('transaksi-approval/approve/(:segment)', 'Owner\TransaksiManagementController::approveDeletion/$1', ['as' => 'owner.transaksi.approval.approve']);
        $routes->post('transaksi-approval/reject/(:segment)', 'Owner\TransaksiManagementController::rejectDeletion/$1', ['as' => 'owner.transaksi.approval.reject']);
        $routes->post('transaksi-approval/delete-permanent/(:segment)', 'Owner\TransaksiManagementController::permanentDelete/$1', ['as' => 'owner.transaksi.approval.deletepermanent']);
        $routes->get('transaksi-approval/detail-items/(:segment)', 'Owner\TransaksiManagementController::getDetailTransaksiItems/$1', ['as' => 'owner.transaksi.approval.detailitems']);
    });
});

// --- KASIR GROUP ---
$routes->group('kasir', ['filter' => 'roleGuard:kasir', 'namespace' => 'App\Controllers\Kasir'], static function ($routes) {
    $routes->get('/', 'DashboardController::index', ['as' => 'kasir.dashboard.index']); // Mengarahkan /kasir ke dashboard
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'kasir.dashboard']);

    // Transaksi Penjualan
    $routes->get('transaksi', 'TransaksiController::index', ['as' => 'kasir.transaksi.index']);
    $routes->get('transaksi/search-produk', 'TransaksiController::searchProduk', ['as' => 'kasir.transaksi.searchproduk']);
    $routes->get('transaksi/search-pelanggan', 'TransaksiController::searchPelanggan', ['as' => 'kasir.transaksi.searchpelanggan']);
    $routes->post('transaksi/add-pelanggan', 'TransaksiController::addPelanggan', ['as' => 'kasir.transaksi.addpelanggan']);
    $routes->post('transaksi/tambah-by-barcode', 'TransaksiController::tambahProdukByBarcodeKeKeranjang', ['as' => 'kasir.transaksi.addbybarcode']);
    $routes->get('transaksi/get-produk-by-barcode/(:any)', 'TransaksiController::getProdukByBarcode/$1', ['as' => 'kasir.transaksi.getprodbybarcode']);
    $routes->post('transaksi/proses-pembayaran', 'TransaksiController::prosesPembayaran', ['as' => 'kasir.transaksi.prosespembayaran']);

    // Riwayat Transaksi Kasir
    $routes->get('riwayat-transaksi', 'TransaksiController::riwayatTransaksi', ['as' => 'kasir.riwayat.index']);
    $routes->get('transaksi/detail/(:segment)', 'TransaksiController::detailTransaksi/$1', ['as' => 'kasir.riwayat.detail']);
    $routes->post('transaksi/request-delete/(:segment)', 'TransaksiController::requestDeleteTransaksi/$1', ['as' => 'kasir.riwayat.requestdelete']);

    // Cek Produk
    $routes->get('cek-produk', 'CekProdukController::index', ['as' => 'kasir.cekproduk.index']);
    

    // Pengaturan & Profil Kasir - Menggunakan namespace App\Controllers\Common
    $routes->get('settings', '\App\Controllers\Common\SettingsController::index', ['as' => 'kasir.settings']);
    $routes->get('profile', '\App\Controllers\Common\ProfileController::index', ['as' => 'kasir.profile']);
    $routes->post('profile/update', '\App\Controllers\Common\ProfileController::update', ['as' => 'kasir.profile.update']);
});
