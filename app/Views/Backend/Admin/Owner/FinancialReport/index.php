<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('admin/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/owner-area/financial-reports') ?>">Area Pemilik</a></li>
            <li class="active"><?= esc($title) ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Detail Laporan Keuangan</div>
                <div class="panel-body">
                    <!-- Filter Tanggal -->
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-12">
                            <form class="form-inline" method="get" action="<?= site_url('admin/owner-area/financial-reports') ?>">
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <label for="start_date" style="margin-right: 5px;">Dari:</label>
                                    <input type="date" class="form-control input-sm" id="start_date" name="start_date" value="<?= esc($filter['start_date'] ?? date('Y-m-01')) ?>">
                                </div>
                                <div class="form-group" style="margin-left: 10px; margin-bottom: 5px;">
                                    <label for="end_date" style="margin-right: 5px;">Sampai:</label>
                                    <input type="date" class="form-control input-sm" id="end_date" name="end_date" value="<?= esc($filter['end_date'] ?? date('Y-m-t')) ?>">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm" style="margin-left: 10px; margin-bottom: 5px;">
                                    <span class="glyphicon glyphicon-filter"></span> Filter
                                </button>
                                <a href="<?= site_url('admin/owner-area/financial-reports') ?>" class="btn btn-default btn-sm" style="margin-left: 5px; margin-bottom: 5px;">
                                    <span class="glyphicon glyphicon-refresh"></span> Reset
                                </a>
                                <!-- Tombol Download Excel -->
                                <?php
                                    $downloadParams = ['start_date' => $filter['start_date'] ?? date('Y-m-01'), 'end_date' => $filter['end_date'] ?? date('Y-m-t')];
                                ?>
                                <a href="<?= site_url('admin/owner-area/financial-reports/download-excel-report?' . http_build_query($downloadParams)) ?>" class="btn btn-success btn-sm" style="margin-left: 5px; margin-bottom: 5px;" target="_blank">
                                    <span class="glyphicon glyphicon-download-alt"></span> Download Excel
                                </a>
                            </form>
                        </div>
                    </div>
                    <hr>

                    <?php if (isset($summary) && (isset($detail_pendapatan) || isset($detail_pengeluaran))): ?>
                        <h4>Ringkasan Laporan Keuangan</h4>
                        <p>Periode: <strong><?= esc(format_indo($filter['start_date'])) ?></strong> s/d <strong><?= esc(format_indo($filter['end_date'])) ?></strong></p>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr class="active">
                                        <th style="width:40%;">Total Pendapatan (Penjualan)</th>
                                        <td style="text-align:right;"><?= esc(number_to_currency($summary['total_pendapatan_kotor'] ?? 0, 'IDR', 'id_ID', 2)) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total Diskon Diberikan</th>
                                        <td style="text-align:right;"><?= esc(number_to_currency($summary['total_diskon_diberikan'] ?? 0, 'IDR', 'id_ID', 2)) ?></td>
                                    </tr>
                                    <tr class="info">
                                        <th>Pendapatan Bersih (Setelah Diskon)</th>
                                        <td style="text-align:right; font-weight:bold;"><?= esc(number_to_currency(($summary['total_pendapatan_kotor'] ?? 0) - ($summary['total_diskon_diberikan'] ?? 0), 'IDR', 'id_ID', 2)) ?></td>
                                    </tr>
                                    <tr class="danger">
                                        <th>Total Pengeluaran</th>
                                        <td style="text-align:right; color:red;">- <?= esc(number_to_currency($summary['total_pengeluaran'] ?? 0, 'IDR', 'id_ID', 2)) ?></td>
                                    </tr>
                                    <tr style="font-size: 1.1em;">
                                        <th style="background-color: #f9f9f9;">Laba / Rugi Bersih</th>
                                        <td style="text-align:right; font-weight:bold; background-color: #f9f9f9;">
                                            <?php 
                                                $laba_rugi = ($summary['total_pendapatan_kotor'] ?? 0) - ($summary['total_diskon_diberikan'] ?? 0) - ($summary['total_pengeluaran'] ?? 0);
                                                echo esc(number_to_currency($laba_rugi, 'IDR', 'id_ID', 2));
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>

                        <h4>Detail Pengeluaran</h4>
                        <?php if (!empty($detail_pengeluaran)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No.</th>
                                            <th style="width: 25%;">Tanggal</th>
                                            <th style="width: 50%;">Kategori & Deskripsi</th>
                                            <th style="width: 15%; text-align:right;">Jumlah (IDR)</th>
                                            <th style="width: 5%; text-align:center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no_pengeluaran = 1; foreach ($detail_pengeluaran as $trx_e): ?>
                                        <tr>
                                            <td><?= $no_pengeluaran++ ?></td>
                                            <td><?= esc(format_indo($trx_e->tanggal)) ?></td>
                                            <td><strong><?= esc($trx_e->kategori) ?></strong><br><small><?= esc($trx_e->deskripsi) ?></small></td>
                                            <td style="text-align:right;"><?= esc(number_to_currency($trx_e->jumlah, 'IDR', 'id_ID', 2)) ?></td>
                                            <td style="text-align:center;">
                                                <form action="<?= site_url('admin/owner-area/financial-reports/delete-expense/' . $trx_e->id) ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?');" style="display:inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>Tidak ada data pengeluaran untuk periode ini.</p>
                        <?php endif; ?>
                        <hr>

                        <h4>Detail Pendapatan (Transaksi Penjualan)</h4>
                        <?php if (!empty($detail_pendapatan)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; text-align:left;">No.</th>
                                            <th style="width: 20%; text-align:left;">ID Transaksi</th>
                                            <th style="width: 25%; text-align:left;">Tanggal</th>
                                            <th style="width: 15%; text-align:left;">Subtotal Kotor</th>
                                            <th style="width: 10%; text-align:left;">Diskon</th>
                                            <th style="width: 15%; text-align:left;">Total Bersih</th>
                                            <th style="width: 10%; text-align:left;">Metode Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no_pendapatan = 1; foreach ($detail_pendapatan as $trx_p): ?>
                                        <tr>
                                            <td><?= $no_pendapatan++ ?></td>
                                            <td><a href="<?= site_url('admin/laporan/transaksi/detail/' . esc($trx_p->transaksi_id)) ?>" target="_blank"><?= esc($trx_p->transaksi_id) ?></a></td>
                                            <td><?= esc(format_indo($trx_p->created_at, true)) ?></td>
                                            <td style="text-align:right;"><?= esc(number_to_currency($trx_p->total_harga + $trx_p->total_diskon, 'IDR', 'id_ID', 2)) ?></td> <!-- Total sebelum diskon -->
                                            <td style="text-align:right;"><?= esc(number_to_currency($trx_p->total_diskon, 'IDR', 'id_ID', 2)) ?></td>
                                            <td style="text-align:right; font-weight:bold;"><?= esc(number_to_currency($trx_p->total_harga, 'IDR', 'id_ID', 2)) ?></td> <!-- Total setelah diskon -->
                                            <td><?= esc(ucwords(str_replace('_', ' ', $trx_p->metode_pembayaran))) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>Tidak ada data transaksi pendapatan untuk periode ini.</p>
                        <?php endif; ?>
                        <hr>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Include form tambah pengeluaran -->
            <?= $expense_form_view ?? '' ?>
        </div>
    </div>
</div>
<?= $this->include('Backend/Template/footer') ?>