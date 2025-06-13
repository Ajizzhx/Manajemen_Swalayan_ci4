<?= $this->extend('Backend/Template/index') ?>

<?= $this->section('styles') ?>
<style>
    #kasirTable th, #kasirTable td {
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
            <li class="active">Monitoring / Kasir</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Monitoring Kasir</h1>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-blue panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-user glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="totalActiveKasir">0</div>
                        <div class="text-muted text-center">Kasir Aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-orange panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-transfer glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="totalTransaksi">0</div>
                        <div class="text-muted text-center">Total Transaksi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-teal panel-widget">
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
            <div class="panel panel-red panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-stats glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="avgTransaksi">0</div>
                        <div class="text-muted text-center">Rata-rata/Kasir</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-user"></span> Performa Kasir Hari Ini (<?= date('d/m/Y') ?>)
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="kasirTable">
                            <thead>
                                <tr>
                                    <th>Nama Kasir</th>
                                    <th>Total Transaksi</th>
                                    <th>Total Penjualan</th>
                                    <th>Last Login</th>
                                    <th>Last Activity</th>
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

    <!-- Grafik Performa -->
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-stats"></span> Jumlah Transaksi per Kasir
                </div>
                <div class="panel-body">
                    <canvas id="transactionChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-usd"></span> Total Penjualan per Kasir  
                </div>
                <div class="panel-body">
                    <canvas id="salesChart" style="height: 300px;"></canvas>
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
    let transactionChart = null;
    let salesChart = null;

    function formatDateTime(dateTimeString) {
        if (!dateTimeString || dateTimeString === '0000-00-00 00:00:00') return '-';
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        try {
            const date = new Date(dateTimeString);
            return date.toLocaleString('id-ID', options).replace(/\./g, ':'); 
        } catch (e) {
            return dateTimeString;
        }
    }

    function initCharts() {
        // Initialize Transaction Chart
        const transCtx = document.getElementById('transactionChart').getContext('2d');
        transactionChart = new Chart(transCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Initialize Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: [],
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function updateCharts(data) {
        const names = data.map(k => k.nama);
        const transactions = data.map(k => k.total_transactions || 0);
        const sales = data.map(k => k.total_sales || 0);

        transactionChart.data.labels = names;
        transactionChart.data.datasets[0].data = transactions;
        transactionChart.update();

        salesChart.data.labels = names;
        salesChart.data.datasets[0].data = sales;
        salesChart.update();
    }

    function updateKasirData() {
        $.ajax({
            url: '<?= site_url('admin/monitoring/api/kasir-data') ?>',
            method: 'GET',
            success: function(response) {
                const data = response.data;
                
                // Update table
                let tableHtml = '';
                let totalTransaksi = 0;
                let totalPendapatan = 0;

                data.forEach(kasir => {
                    const transactions = parseInt(kasir.total_transactions) || 0;
                    const sales = parseFloat(kasir.total_sales) || 0;
                    
                    totalTransaksi += transactions;
                    totalPendapatan += sales;

                    tableHtml += `
                        <tr>
                            <td>${kasir.nama}</td>
                            <td>${transactions}</td>
                            <td>Rp ${sales.toLocaleString('id-ID')}</td>
                            <td>${formatDateTime(kasir.last_login)}</td>
                            <td>${formatDateTime(kasir.last_activity)}</td>
                        </tr>
                    `;
                });

                $('#kasirTable tbody').html(tableHtml);
                $('#totalActiveKasir').text(data.length); 
                $('#totalTransaksi').text(totalTransaksi);
                $('#totalPendapatan').text('Rp ' + totalPendapatan.toLocaleString('id-ID'));
                $('#avgTransaksi').text(data.length > 0 ? (totalTransaksi / data.length).toFixed(1) : 0);

                // Update charts
                updateCharts(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching kasir data:', error);
            }
        });
    }

    // Initialize charts and start real-time updates
    initCharts();
    updateKasirData();
    setInterval(updateKasirData, 30000); 
});
</script>
<?= $this->endSection() ?>
