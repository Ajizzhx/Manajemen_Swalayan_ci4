<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <?php
            $user_role_for_breadcrumb_lpr_metode = session()->get('role');
            $dashboard_link_for_breadcrumb_lpr_metode = '';
            if ($user_role_for_breadcrumb_lpr_metode === 'admin' || $user_role_for_breadcrumb_lpr_metode === 'pemilik') {
                $dashboard_link_for_breadcrumb_lpr_metode = site_url('admin/dashboard');
            } elseif ($user_role_for_breadcrumb_lpr_metode === 'kasir') {
                $dashboard_link_for_breadcrumb_lpr_metode = site_url('kasir/dashboard');
            } else {
                $dashboard_link_for_breadcrumb_lpr_metode = site_url('/');
            }
            ?>
            <li><a href="<?= $dashboard_link_for_breadcrumb_lpr_metode ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/laporan/transaksi') ?>">Laporan & Analisis</a></li>
            <li class="active">Metode Pembayaran Populer</li>
        </ol>
    </div><!--/.row-->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Filter Metode Pembayaran</div>
                <div class="panel-body">
                    <form action="<?= site_url('admin/laporan/metode-pembayaran-populer') ?>" method="get" class="form-inline">
                        <div class="form-group">
                            <label for="tanggal_awal">Dari Tanggal:</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="<?= esc($tanggal_awal ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-left: 10px;">
                            <label for="tanggal_akhir">Sampai Tanggal:</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="<?= esc($tanggal_akhir ?? '') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Filter</button>
                        <a href="<?= site_url('admin/laporan/metode-pembayaran-populer') ?>" class="btn btn-default" style="margin-left: 5px;">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Hasil Laporan Metode Pembayaran
                    <?php if(isset($info_periode)): ?>
                        <span class="pull-right"><em><?= esc($info_periode) ?></em></span>
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <?php if (!empty($metode_populer)): ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:5%;">No.</th>
                                    <th>Metode Pembayaran</th>
                                    <th class="text-center">Total Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($metode_populer as $metode): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc(ucwords(str_replace('_', ' ', $metode['metode_pembayaran']))) ?></td>
                                    <td class="text-center"><?= esc($metode['total_transaksi']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif (isset($tanggal_awal) || isset($tanggal_akhir)): ?>
                        <p>Tidak ada data metode pembayaran untuk periode/filter yang dipilih.</p>
                    <?php else: ?>
                        <p>Silakan pilih filter untuk melihat laporan metode pembayaran.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Grafik Metode Pembayaran</div>
                <div class="panel-body">
                    <?php if (!empty($metode_populer)): ?>
                        <div class="canvas-wrapper">
                            <canvas class="main-chart" id="metodePembayaranChart" style="width:100%; height:300px;"></canvas>
                        </div>
                    <?php else: ?>
                        <p class="text-muted"><em>Grafik akan ditampilkan di sini jika terdapat data.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div><!--/.row-->

</div>  <!--/.main-->

<?= $this->include('Backend/Template/footer') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const metodeDataPHP = <?= json_encode($metode_populer ?? []) ?>;
    const canvasElement = document.getElementById('metodePembayaranChart');

    if (typeof Chart !== 'undefined' && metodeDataPHP && metodeDataPHP.length > 0 && canvasElement) {
        const labels = metodeDataPHP.map(item => {
            let name = item.metode_pembayaran.replace(/_/g, ' ');
            return name.charAt(0).toUpperCase() + name.slice(1); // Capitalize
        });
        const dataValues = metodeDataPHP.map(item => parseInt(item.total_transaksi));

        const data = {
            labels: labels,
            datasets: [{
                label: 'Total Transaksi',
                data: dataValues,
                backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
                borderWidth: 1
            }]
        };
        const config = { 
            type: 'pie', // Mengubah tipe chart menjadi 'pie'
            data: data, 
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top', // Posisi legenda
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw.toLocaleString() + ' transaksi';
                            }
                        }
                    }
                }
            } 
        };
        new Chart(canvasElement, config);
    }
});
</script>