<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MonitoringModel;

class MonitoringController extends BaseController
{
    protected $monitoringModel;

    public function __construct()
    {
        $this->monitoringModel = new MonitoringModel();
    }

    public function stok()
    {
        $data = [
            'title' => 'Monitoring Stok',
            'products' => $this->monitoringModel->getStockStatus(),
            'lowStockThreshold' => 10,
            'lowStockProducts' => $this->monitoringModel->getLowStockProducts()
        ];
        
        return view('Backend/monitoring/stok', $data);
    }

    public function penjualan()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        $data = [
            'title' => 'Monitoring Penjualan',
            'salesData' => $this->monitoringModel->getSalesDataByDateRange($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('Backend/monitoring/penjualan', $data);
    }

    public function kasir()
    {
        $data = [
            'title' => 'Monitoring Kasir',
            'dailyKasirData' => $this->monitoringModel->getDailyKasirPerformance(),
            'monthlyKasirData' => $this->monitoringModel->getMonthlyKasirPerformance()
        ];

        return view('Backend/monitoring/kasir', $data);
    }

    // API endpoints untuk data realtime
    public function getRealtimeKasirData()
    {
        return $this->response->setJSON([
            'data' => $this->monitoringModel->getDailyKasirPerformance()
        ]);
    }

    public function getRealtimeSalesData()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        return $this->response->setJSON([
            'data' => $this->monitoringModel->getSalesDataByDateRange($startDate, $endDate)
        ]);
    }
}
