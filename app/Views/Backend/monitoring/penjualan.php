<?= $this->extend('Backend/Template/index') ?>

<?= $this->section('styles') ?>
<style>
    #salesTable th, #salesTable td {
        text-align: center;
        vertical-align: middle;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('admin/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Monitoring / Penjualan</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Monitoring Penjualan</h1>
        </div>
    </div>

    <!-- Filter Date Range -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-calendar"></span> Filter Periode
                </div>
                <div class="panel-body">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="startDate">Tanggal Mulai</label>
                            <input type="date" id="startDate" class="form-control" value="<?= $startDate ?>">
                        </div>
                        <div class="form-group">
                            <label for="endDate">Tanggal Akhir</label>
                            <input type="date" id="endDate" class="form-control" value="<?= $endDate ?>">
                        </div>
                        <button type="button" id="filterBtn" class="btn btn-primary">Terapkan Filter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-blue panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-shopping-cart glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="totalTransaksi">0</div>
                        <div class="text-muted text-center">Total Transaksi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-orange panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-usd glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="totalPendapatan">Rp 0</div>
                        <div class="text-muted text-center">Total Pendapatan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-teal panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-stats glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="avgTransaksiHarian">0</div>
                        <div class="text-muted text-center">Rata-rata Transaksi/Hari</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-red panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-calendar glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="avgPendapatanHarian">Rp 0</div>
                        <div class="text-muted text-center">Rata-rata Pendapatan/Hari</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Penjualan -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-stats"></span> Grafik Penjualan
                </div>
                <div class="panel-body">
                    <div class="canvas-wrapper">
                        <canvas id="salesChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Penjualan -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-list-alt"></span> Detail Penjualan Harian
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="salesTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Total Penjualan</th>
                                    <th>Rata-rata per Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let salesChart = null;

    function initChart() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Penjualan',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function updateSalesData() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        $.ajax({
            url: '<?= site_url('admin/monitoring/api/sales-data') ?>',
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                const data = response.data;
                
                // Update summary cards
                let totalTransaksi = 0;
                let totalPendapatan = 0;

                // Update table and calculate totals
                let tableHtml = '';
                data.forEach(sale => {
                    const transactions = parseInt(sale.total_transactions) || 0;
                    const sales = parseFloat(sale.total_sales) || 0;
                    const avgPerTransaction = transactions ? (sales / transactions) : 0;

                    totalTransaksi += transactions;
                    totalPendapatan += sales;

                    tableHtml += `
                        <tr>
                            <td>${sale.date}</td>
                            <td>${transactions}</td>
                            <td>Rp ${sales.toLocaleString('id-ID')}</td>
                            <td>Rp ${avgPerTransaction.toLocaleString('id-ID', {maximumFractionDigits: 0})}</td>
                        </tr>
                    `;
                });

                $('#salesTable tbody').html(tableHtml);

                // Update summary cards
                const dayCount = data.length || 1;
                $('#totalTransaksi').text(totalTransaksi);
                $('#totalPendapatan').text(totalPendapatan.toLocaleString('id-ID'));
                $('#avgTransaksiHarian').text((totalTransaksi / dayCount).toFixed(1));
                $('#avgPendapatanHarian').text((totalPendapatan / dayCount).toLocaleString('id-ID', {maximumFractionDigits: 0}));

                // Update chart
                salesChart.data.labels = data.map(sale => sale.date);
                salesChart.data.datasets[0].data = data.map(sale => sale.total_sales);
                salesChart.update();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching sales data:', error);
            }
        });
    }

    // Initialize chart and data
    initChart();
    updateSalesData();

    // Handle filter button click
    $('#filterBtn').click(function() {
        updateSalesData();
    });

   
    setInterval(updateSalesData, 300000);
});
</script>
<?= $this->endSection() ?>
