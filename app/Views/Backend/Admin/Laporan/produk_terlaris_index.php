<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <?php
            $user_role_for_breadcrumb_lpr_produk = session()->get('role');
            $dashboard_link_for_breadcrumb_lpr_produk = '';
            if ($user_role_for_breadcrumb_lpr_produk === 'admin' || $user_role_for_breadcrumb_lpr_produk === 'pemilik') {
                $dashboard_link_for_breadcrumb_lpr_produk = site_url('admin/dashboard');
            } elseif ($user_role_for_breadcrumb_lpr_produk === 'kasir') {
                $dashboard_link_for_breadcrumb_lpr_produk = site_url('kasir/dashboard');
            } else {
                $dashboard_link_for_breadcrumb_lpr_produk = site_url('/');
            }
            ?>
            <li><a href="<?= $dashboard_link_for_breadcrumb_lpr_produk ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/laporan/transaksi') ?>">Laporan & Analisis</a></li>
            <li class="active">Produk Terlaris</li>
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
                <div class="panel-heading">Filter Produk Terlaris</div>
                <div class="panel-body">
                    <form action="<?= site_url('admin/laporan/produk-terlaris') ?>" method="get" class="form-inline">
                        <div class="form-group">
                            <label for="tanggal_awal">Dari Tanggal:</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="<?= esc($tanggal_awal ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-left: 10px;">
                            <label for="tanggal_akhir">Sampai Tanggal:</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="<?= esc($tanggal_akhir ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-left: 10px;">
                            <label for="limit">Tampilkan Top:</label>
                            <select name="limit" id="limit" class="form-control">
                                <option value="5" <?= ($limit ?? 10) == 5 ? 'selected' : '' ?>>5</option>
                                <option value="10" <?= ($limit ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                                <option value="20" <?= ($limit ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= ($limit ?? 10) == 50 ? 'selected' : '' ?>>50</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Filter</button>
                        <a href="<?= site_url('admin/laporan/produk-terlaris') ?>" class="btn btn-default" style="margin-left: 5px;">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Hasil Laporan Produk Terlaris
                    <?php if(isset($info_periode)): ?>
                        <span class="pull-right"><em><?= esc($info_periode) ?></em></span>
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <?php if (!empty($produk_terlaris)): ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:5%;">No.</th>
                                    <th>Kode Barcode</th>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($produk_terlaris as $produk): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($produk['kode_barcode']) ?></td>
                                    <td><?= esc($produk['nama_produk']) ?></td>
                                    <td class="text-center"><?= esc($produk['total_terjual']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif (isset($tanggal_awal) || isset($tanggal_akhir)): ?>
                        <p>Tidak ada data produk terlaris untuk periode/filter yang dipilih.</p>
                    <?php else: ?>
                        <p>Silakan pilih filter untuk melihat laporan produk terlaris.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Grafik Produk Terlaris</div>
                <div class="panel-body">
                    <?php if (!empty($produk_terlaris)): ?>
                        <div class="canvas-wrapper">
                            <canvas class="main-chart" id="produkTerlarisChart" style="width:100%; height:300px;"></canvas>
                        </div>
                    <?php else: ?>
                        <p class="text-muted"><em>Grafik akan ditampilkan di sini jika terdapat data produk terlaris.</em></p>
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
    const produkDataPHP = <?= json_encode($produk_terlaris ?? []) ?>;
    const canvasElement = document.getElementById('produkTerlarisChart');

    if (typeof Chart !== 'undefined' && produkDataPHP && produkDataPHP.length > 0 && canvasElement) {
        const labels = produkDataPHP.map(item => {
            
            const nama = item.nama_produk || 'Produk Tidak Dikenal';
            return nama.length > 20 ? nama.substring(0, 17) + '...' : nama;
        });
        const dataValues = produkDataPHP.map(item => parseInt(item.total_terjual));

        const data = {
            labels: labels,
            datasets: [{
                label: 'Total Terjual',
                data: dataValues,
                
                backgroundColor: [
                    'rgba(48, 164, 255, 0.6)',  
                    'rgba(255, 180, 0, 0.6)',   
                    'rgba(77, 189, 116, 0.6)',  
                    'rgba(240, 80, 80, 0.6)',   
                    'rgba(153, 102, 255, 0.6)', 
                    'rgba(255, 159, 64, 0.6)',  
                    'rgba(75, 192, 192, 0.6)', 
                    'rgba(201, 203, 207, 0.6)', 
                    'rgba(255, 99, 132, 0.6)',  
                    'rgba(17, 42, 207, 0.6)'   
                ],
                borderColor: [ 
                    'rgba(48, 164, 255, 1)',
                    'rgba(255, 180, 0, 1)',
                    'rgba(77, 189, 116, 1)',
                    'rgba(240, 80, 80, 1)',
                    'rgba(153, 102, 255, 1)', 
                    'rgba(255, 159, 64, 1)', 'rgba(75, 192, 192, 1)', 'rgba(201, 203, 207, 1)', 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        };

        const config = {
            type: 'pie',
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
                                // Mengambil nama produk asli sebelum dipotong untuk label
                                const originalLabel = produkDataPHP[tooltipItem.dataIndex].nama_produk || 'Produk Tidak Dikenal';
                                return originalLabel + ': ' + tooltipItem.raw.toLocaleString() + ' terjual';
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