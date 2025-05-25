<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <?php
            $user_role_for_breadcrumb_lpr_pendapatan = session()->get('role');
            $dashboard_link_for_breadcrumb_lpr_pendapatan = '';
            if ($user_role_for_breadcrumb_lpr_pendapatan === 'admin' || $user_role_for_breadcrumb_lpr_pendapatan === 'pemilik') {
                $dashboard_link_for_breadcrumb_lpr_pendapatan = site_url('admin/dashboard');
            } elseif ($user_role_for_breadcrumb_lpr_pendapatan === 'kasir') {
                $dashboard_link_for_breadcrumb_lpr_pendapatan = site_url('kasir/dashboard');
            } else {
                $dashboard_link_for_breadcrumb_lpr_pendapatan = site_url('/');
            }
            ?>
            <li><a href="<?= $dashboard_link_for_breadcrumb_lpr_pendapatan ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/laporan/transaksi') ?>">Laporan & Analisis</a></li>
            <li class="active">Pendapatan</li>
        </ol>
    </div><!--/.row-->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Filter Pendapatan Harian</div>
                <div class="panel-body">
                    <form action="<?= site_url('admin/laporan/pendapatan') ?>" method="get" class="form-inline">
                        <div class="form-group">
                            <label for="tanggal">Pilih Tanggal:</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= esc($tanggal ?? date('Y-m-d')) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Lihat</button>
                         <a href="<?= site_url('admin/laporan/pendapatan') ?>" class="btn btn-default" style="margin-left: 5px;">Hari Ini</a>
                    </form>                    
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Filter Pendapatan Rentang Tanggal</div>
                <div class="panel-body">
                    <form action="<?= site_url('admin/laporan/pendapatan') ?>" method="get" class="form-inline">
                        <div class="form-group">
                            <label for="tanggal_awal_range">Dari:</label>
                            <input type="date" name="tanggal_awal_range" id="tanggal_awal_range" class="form-control" value="<?= esc($tanggal_awal_range ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-left: 10px;">
                            <label for="tanggal_akhir_range">Sampai:</label>
                            <input type="date" name="tanggal_akhir_range" id="tanggal_akhir_range" class="form-control" value="<?= esc($tanggal_akhir_range ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Lihat</button>
                    </form>                    
                </div>
            </div>
        </div>
    </div><!--/.row-->

    <!-- Grafik Pendapatan -->
    <div class="row">
        <div class="col-lg-12">
             <div class="panel panel-default">
                <div class="panel-heading">Grafik Pendapatan</div>
                <div class="panel-body">
                    <?php if (isset($filter_type) && $filter_type == 'range' && !empty($pendapatan_per_hari)): ?>
                        <div class="canvas-wrapper">
                            <canvas class="main-chart" id="pendapatanChart" style="width:100%; height:300px;"></canvas>
                        </div>
                    <?php elseif (isset($filter_type) && $filter_type == 'single' && isset($total_pendapatan) && $total_pendapatan > 0): ?>
                         <p class="text-info">Grafik hanya tersedia untuk laporan rentang tanggal. Untuk tanggal tunggal, total pendapatan adalah <strong><?= esc(number_to_currency($total_pendapatan, 'IDR', 'id_ID', 0)) ?></strong>.</p>
                    <?php else: ?>
                        <p class="text-muted"><em>Grafik akan ditampilkan di sini jika Anda memilih filter rentang tanggal dan terdapat data pendapatan.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div><!--/.row-->

    <!-- Hasil Laporan Pendapatan -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Hasil Laporan Pendapatan <?php if(isset($tanggal) && $filter_type == 'single') echo "untuk Tanggal " . date('d M Y', strtotime($tanggal)); elseif(isset($tanggal_awal_range) && isset($tanggal_akhir_range) && $filter_type == 'range') echo "Periode " . date('d M Y', strtotime($tanggal_awal_range)) . " s/d " . date('d M Y', strtotime($tanggal_akhir_range)); ?></div>
                <div class="panel-body">
                    <?php if (isset($filter_type) && $filter_type == 'single' && isset($total_pendapatan)): ?>
                        <h3>Laporan Pendapatan Tanggal: <?php
                                                        $bulan_indo = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                                                        $timestamp_single = strtotime($tanggal);
                                                        echo esc(date('d', $timestamp_single) . ' ' . $bulan_indo[(int)date('n', $timestamp_single)] . ' ' . date('Y', $timestamp_single));
                                                    ?></h3>
                        <p>Total Pendapatan: <strong><?= esc(number_to_currency($total_pendapatan, 'IDR', 'id_ID', 0)) ?></strong></p>
                    <?php elseif (isset($filter_type) && $filter_type == 'range' && !empty($pendapatan_per_hari)): ?>
                        <h3>Laporan Pendapatan Periode: <?php
                                                        $bulan_indo = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                                                        $timestamp_awal = strtotime($tanggal_awal_range);
                                                        $timestamp_akhir = strtotime($tanggal_akhir_range);
                                                        $tgl_awal_formatted = date('d', $timestamp_awal) . ' ' . $bulan_indo[(int)date('n', $timestamp_awal)] . ' ' . date('Y', $timestamp_awal);
                                                        $tgl_akhir_formatted = date('d', $timestamp_akhir) . ' ' . $bulan_indo[(int)date('n', $timestamp_akhir)] . ' ' . date('Y', $timestamp_akhir);
                                                        echo esc($tgl_awal_formatted);
                                                    ?> s/d <?php
                                                        echo esc($tgl_akhir_formatted);
                                                    ?></h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:70%;">Tanggal</th>
                                    <th class="text-right">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendapatan_per_hari as $harian): ?>
                                    <tr>
                                        <td><?php
                                                $timestamp_harian = strtotime($harian['tanggal']);
                                                echo esc(date('d', $timestamp_harian) . ' ' . $bulan_indo[(int)date('n', $timestamp_harian)] . ' ' . date('Y', $timestamp_harian));
                                            ?>
                                        </td>
                                        <td class="text-right"><?= esc(number_to_currency($harian['total'], 'IDR', 'id_ID', 0)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right"><strong>Total Keseluruhan:</strong></td>
                                    <td class="text-right"><strong><?= esc(number_to_currency($total_pendapatan, 'IDR', 'id_ID', 0)) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php elseif (isset($filter_type) && $filter_type == 'single' && $total_pendapatan == 0 && !empty($tanggal)): ?>
                        <p>Tidak ada data pendapatan untuk tanggal yang dipilih.</p>
                    <?php elseif (isset($filter_type) && $filter_type == 'range' && empty($pendapatan_per_hari)): ?>
                        <p>Tidak ada data pendapatan untuk periode/tanggal yang dipilih.</p>
                    <?php else: ?>
                        <p>Silakan pilih filter tanggal untuk melihat laporan pendapatan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div><!--/.row-->

</div>  <!--/.main-->

<?= $this->include('Backend/Template/footer') ?>
<!-- Jika ingin menggunakan Chart.js, tambahkan di sini atau di footer template -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pendapatanDataPHP = <?= json_encode($pendapatan_per_hari ?? []) ?>;
        const canvasElement = document.getElementById('pendapatanChart');
        const filterType = '<?= esc($filter_type ?? "") ?>';

        if (filterType === 'range' && typeof Chart !== 'undefined' && pendapatanDataPHP && pendapatanDataPHP.length > 0 && canvasElement) {
            const labels = pendapatanDataPHP.map(item => {
                const date = new Date(item.tanggal);
                
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }); 
            });
            const dataValues = pendapatanDataPHP.map(item => parseFloat(item.total));

            const data = {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: dataValues,
                    backgroundColor: 'rgba(48, 164, 255, 0.2)', 
                    borderColor: 'rgba(48, 164, 255, 1)',     
                    borderWidth: 2,
                    tension: 0.3, 
                    fill: true, 
                    pointBackgroundColor: 'rgba(48, 164, 255, 1)', 
                    pointBorderColor: '#fff', 
                    pointHoverBackgroundColor: '#fff', 
                    pointHoverBorderColor: 'rgba(48, 164, 255, 1)' 
                }]
            };

            const config = {
                type: 'line', // Tipe grafik garis
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return 'Rp ' + Number(value).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            };

            new Chart(canvasElement, config);
        } else if (canvasElement) {
            
        }
    });
</script>