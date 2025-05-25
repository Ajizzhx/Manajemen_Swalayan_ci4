<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('kasir/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Cek Harga & Stok Produk</li>
        </ol>
    </div><!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title ?? 'Cek Harga & Stok Produk') ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Daftar Produk
                    <span class="pull-right clickable panel-toggle panel-button-tab-left"><em class="fa fa-toggle-up"></em></span>
                </div>
                <div class="panel-body">
                    <div id="searchResults" class="table-responsive">
                        <?php if (!empty($produk_list) && is_array($produk_list)): ?>
                            <table 
                                class="table table-hover table-striped table-bordered" 
                                id="produkTable"
                                data-toggle="table"
                                data-search="true"
                                data-show-refresh="true"
                                data-show-toggle="true"
                                data-show-columns="true"
                                data-pagination="true"
                                data-sort-name="produk_id" 
                                data-sort-order="asc">
                                <thead>
                                    <tr>
                                        <th data-field="produk_id" data-sortable="true">ID Produk</th>
                                        <th data-field="nama" data-sortable="true">Nama Produk</th>
                                        <th data-field="harga" data-sortable="true" data-align="right">Harga</th>
                                        <th data-field="stok" data-sortable="true" data-align="right">Stok</th>
                                        <th data-field="kode_barcode" data-sortable="true">Kode Barcode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produk_list as $produk): ?>
                                        <tr>
                                            <td><?= esc($produk->produk_id ?? 'N/A') ?></td>
                                            <td><?= esc($produk->nama ?? 'N/A') ?></td>
                                            <td class="text-right">Rp <?= number_format($produk->harga ?? 0, 0, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($produk->stok ?? 0, 0, ',', '.') ?></td>
                                            <td><?= esc($produk->kode_barcode ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-info"><em>Tidak ada data produk yang dapat ditampilkan.</em></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div><!--/.row-->

</div>  <!--/.main-->

<script>

$(document).ready(function() {

});
</script>
<?= $this->include('Backend/Template/footer') ?>