<?= $this->extend('Backend/Template/index') ?>

<?= $this->section('styles') ?>
<style>
    #lowStockTable th, #lowStockTable td,
    #allProductsTable th, #allProductsTable td {
        text-align: center;
        vertical-align: middle; /* Opsional: untuk alignment vertikal yang lebih baik */
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('admin/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Monitoring / Stok</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Monitoring Stok</h1>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-red panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-warning-sign glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="lowStockCount">0</div>
                        <div class="text-muted text-center">Stok Menipis</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-blue panel-widget ">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-shopping-cart glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="totalProducts">0</div>
                        <div class="text-muted text-center">Total Produk</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-orange panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-remove-circle glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="outOfStockCount">0</div>
                        <div class="text-muted text-center">Stok Habis</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-green panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <i class="glyphicon glyphicon-ok-circle glyphicon-l"></i>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large text-center" id="wellStockedCount">0</div>
                        <div class="text-muted text-center">Stok Aman</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk dengan Stok Menipis -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-alert"></span> Produk dengan Stok Menipis
                    <span class="label label-danger">Stok ≤ <?= $lowStockThreshold ?></span>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="lowStockTable">
                            <thead>
                                <tr>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockProducts as $product): ?>
                                <tr class="<?= ($product['stok'] <= $lowStockThreshold/2) ? 'danger' : 'warning' ?>">
                                    <td><?= esc($product['kode_produk']) ?></td>
                                    <td><?= esc($product['nama_produk']) ?></td>
                                    <td><?= esc($product['nama_kategori']) ?></td>
                                    <td><?= esc($product['stok']) ?></td>
                                    <td>
                                        <?php if ($product['stok'] <= $lowStockThreshold/2): ?>
                                            <span class="label label-danger">Sangat Rendah</span>
                                        <?php else: ?>
                                            <span class="label label-warning">Rendah</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Semua Stok Produk -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-list"></span> Daftar Stok Semua Produk
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered" id="allProductsTable">
                            <thead>
                                <tr>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr class="<?= ($product['stok'] <= $lowStockThreshold) ? 'danger' : '' ?>">
                                    <td><?= esc($product['kode_produk']) ?></td>
                                    <td><?= esc($product['nama_produk']) ?></td>
                                    <td><?= esc($product['nama_kategori']) ?></td>
                                    <td><?= esc($product['stok']) ?></td>
                                    <td>
                                        <?php if ($product['stok'] <= $lowStockThreshold): ?>
                                            <span class="label label-danger">Stok Rendah</span>
                                        <?php else: ?>
                                            <span class="label label-success">Stok Aman</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
    // Initialize DataTables
    $('#lowStockTable').DataTable({
        responsive: true,
        order: [[3, 'asc']] // Sort by stock amount ascending
    });

    $('#allProductsTable').DataTable({
        responsive: true
    });

});
</script>
<?= $this->endSection() ?>
