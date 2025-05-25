<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <?php
            $user_role_for_breadcrumb_lpr_trx = session()->get('role');
            $dashboard_link_for_breadcrumb_lpr_trx = '';
            if ($user_role_for_breadcrumb_lpr_trx === 'admin' || $user_role_for_breadcrumb_lpr_trx === 'pemilik') {
                $dashboard_link_for_breadcrumb_lpr_trx = site_url('admin/dashboard');
            } elseif ($user_role_for_breadcrumb_lpr_trx === 'kasir') {
                $dashboard_link_for_breadcrumb_lpr_trx = site_url('kasir/dashboard');
            } else {
                $dashboard_link_for_breadcrumb_lpr_trx = site_url('/');
            }
            ?>
            <li><a href="<?= $dashboard_link_for_breadcrumb_lpr_trx ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/laporan/transaksi') ?>">Laporan & Analisis</a></li>
            <li class="active">Riwayat Transaksi</li>
        </ol>
    </div><!--/.row-->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Filter Transaksi
                </div>
                <div class="panel-body">
                    <form action="<?= site_url('admin/laporan/transaksi') ?>" method="get" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_awal" class="col-sm-4 control-label">Tgl Awal:</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="<?= esc($tanggal_awal ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_akhir" class="col-sm-4 control-label">Tgl Akhir:</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="<?= esc($tanggal_akhir ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="metode_pembayaran" class="col-sm-4 control-label">Metode:</label>
                                    <div class="col-sm-8">
                                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control">
                                            <option value="">Semua</option>
                                            <?php foreach ($metode_pembayaran_list as $metode): ?>
                                                <option value="<?= esc($metode->metode_pembayaran) ?>" <?= ($selected_metode_pembayaran == $metode->metode_pembayaran) ? 'selected' : '' ?>>
                                                    <?= esc(ucwords(str_replace('_', ' ', $metode->metode_pembayaran))) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search_id_transaksi" class="col-sm-4 control-label">ID/Kode:</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="search_id_transaksi" id="search_id_transaksi" class="form-control" value="<?= esc($selected_id_transaksi ?? '') ?>" placeholder="Cari ID/Kode Transaksi">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kasir_id" class="col-sm-4 control-label">Kasir:</label>
                                    <div class="col-sm-8">
                                        <select name="kasir_id" id="kasir_id" class="form-control">
                                            <option value="">Semua Kasir</option>
                                            <?php foreach ($kasir_list as $kasir): ?>
                                                <option value="<?= esc($kasir['karyawan_id']) ?>" <?= ($selected_kasir_id == $kasir['karyawan_id']) ? 'selected' : '' ?>>
                                                    <?= esc($kasir['nama']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 text-right">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="<?= site_url('admin/laporan/transaksi') ?>" class="btn btn-default">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Daftar Semua Transaksi
                    <a href="<?= site_url('admin/laporan/transaksi/export?' . esc($current_query_string ?? '')) ?>" class="btn btn-success btn-sm pull-right" style="margin-left: 10px;">
                        <span class="glyphicon glyphicon-download-alt"></span> Unduh Excel
                    </a>
                    <span class="pull-right clickable panel-toggle panel-button-tab-left"><em class="fa fa-toggle-up"></em></span>
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-pagination="true" data-sort-name="tanggal" data-sort-order="desc">
                        <thead>
                        <tr>
                            <th data-field="kode_transaksi" data-sortable="true">ID/Kode Transaksi</th>
                            <th data-field="tanggal" data-sortable="true">Tanggal & Waktu</th>
                            <th data-field="pelanggan" data-sortable="true">Member</th>
                            <th data-field="kasir" data-sortable="true">Kasir</th>
                            <th data-field="total" data-sortable="true" data-align="right">Total Harga</th>
                            <th data-field="metode" data-sortable="true">Metode Bayar</th>
                            <th data-field="actions">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat_transaksi as $transaksi): ?>
                            <tr>
                                <td><?= esc($transaksi['transaksi_id']) ?></td>
                                <td><?php
                                        // Daftar nama bulan dalam bahasa Indonesia
                                        $bulan_indo = array(
                                            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                        );
                                        $timestamp = strtotime($transaksi['created_at']);
                                        $tanggal_formatted = date('d', $timestamp) . ' ' . $bulan_indo[(int)date('n', $timestamp)] . ' ' . date('Y, H:i', $timestamp);
                                        echo esc($tanggal_formatted);
                                    ?></td>
                                <td><?= esc($transaksi['nama_pelanggan'] ?: 'Umum') ?></td>
                                <td><?= esc($transaksi['nama_kasir'] ?: 'N/A') ?></td>
                                <td><?= esc(number_to_currency($transaksi['total_harga'], 'IDR', 'id_ID', 0)) ?></td>
                                <td><?= esc(ucwords(str_replace('_', ' ', $transaksi['metode_pembayaran'] ?? 'N/A'))) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/laporan/transaksi/detail/' . $transaksi['transaksi_id']) ?>" class="btn btn-info btn-xs">
                                        <span class="glyphicon glyphicon-eye-open"></span> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><!--/.row-->
</div>  <!--/.main-->

<?= $this->include('Backend/Template/footer') ?>