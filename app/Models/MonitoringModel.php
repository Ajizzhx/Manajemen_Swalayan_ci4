<?php

namespace App\Models;

use CodeIgniter\Model;

class MonitoringModel extends Model
{    public function getDailyKasirPerformance()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        
        return $db->table('karyawan')
            ->select('karyawan.*, COUNT(transaksi.transaksi_id) as total_transactions, 
                     SUM(transaksi.total_harga) as total_sales,
                     karyawan.last_login,
                     karyawan.last_activity') 
            ->join('transaksi', 
                   'transaksi.karyawan_id = karyawan.karyawan_id AND transaksi.is_deleted = 0 AND DATE(transaksi.created_at) = \'' . $db->escapeString($today) . '\'', 
                   'left') 
            ->where('karyawan.role', 'kasir')
            ->where('karyawan.is_deleted', 0)
            ->groupBy('karyawan.karyawan_id, karyawan.last_login, karyawan.last_activity') 
            ->get()
            ->getResultArray();
    }    public function getStockStatus()
    {
        $db = \Config\Database::connect();
          // Get basic product data first
        $products = $db->table('produk')
            ->select('
                produk.*,
                produk.kode_barcode, 
                produk.nama,
                produk.stok,
                kategori.nama as nama_kategori
            ')
            ->join('kategori', 'kategori.kategori_id = produk.kategori_id')
            ->where('produk.is_deleted', 0)
            ->get()
            ->getResultArray();

        // Calculate stock statistics
        $lowStockThreshold = 10; // Threshold for low stock
        $stats = [
            'products' => $products,
            'totalProducts' => count($products),
            'lowStockCount' => 0,
            'outOfStockCount' => 0,
            'wellStockedCount' => 0
        ];

        // Count products in each category
        foreach ($products as $product) {
            if ($product['stok'] <= 0) {
                $stats['outOfStockCount']++;
            } elseif ($product['stok'] <= $lowStockThreshold) {
                $stats['lowStockCount']++;
            } else {
                $stats['wellStockedCount']++;
            }
        }

        return $stats;
    }

    public function getSalesDataByDateRange($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        return $db->table('transaksi')            ->select('DATE(created_at) as date, COUNT(*) as total_transactions, SUM(total_harga) as total_sales')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->where('is_deleted', 0)
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();
    }    public function getMonthlyKasirPerformance()
    {
        $db = \Config\Database::connect();
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        
        return $db->table('karyawan')
            ->select('karyawan.*, COUNT(transaksi.transaksi_id) as total_transactions, SUM(transaksi.total_harga) as total_sales')
            ->join('transaksi', 'transaksi.karyawan_id = karyawan.karyawan_id', 'left')            ->where('karyawan.role', 'kasir')
            ->where('karyawan.is_deleted', 0)
            ->where('transaksi.is_deleted', 0)
            ->where('DATE(transaksi.created_at) >=', $firstDayOfMonth)
            ->where('DATE(transaksi.created_at) <=', $lastDayOfMonth)
            ->groupBy('karyawan.karyawan_id')
            ->get()
            ->getResultArray();
    }    public function getLowStockProducts($threshold = 10)
    {
        $db = \Config\Database::connect();
        return $db->table('produk')
            ->select('produk.*, produk.kode_barcode as kode_produk, kategori.nama as nama_kategori')
            ->join('kategori', 'kategori.kategori_id = produk.kategori_id')
            ->where('produk.stok <=', $threshold)
            ->where('produk.is_deleted', 0)
            ->get()
            ->getResultArray();
    }    public function getTopSellingProducts($limit = 10)
    {
        $db = \Config\Database::connect();
        return $db->table('detail_transaksi')
            ->select('produk.kode_barcode as kode_produk, produk.nama as nama_produk, SUM(detail_transaksi.jumlah) as total_sold')
            ->join('produk', 'produk.produk_id = detail_transaksi.produk_id')
            ->join('transaksi', 'transaksi.transaksi_id = detail_transaksi.transaksi_id')
            ->where('transaksi.is_deleted', 0)
            ->where('produk.is_deleted', 0)
            ->where('MONTH(transaksi.created_at)', date('m'))
            ->where('YEAR(transaksi.created_at)', date('Y'))
            ->groupBy('produk.produk_id')
            ->orderBy('total_sold', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
