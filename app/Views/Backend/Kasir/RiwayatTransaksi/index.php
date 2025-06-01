<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Riwayat Transaksi</li>
        </ol>
    
    <div class="row">
    <!-- <div class="row"> -->
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
                    <form action="<?= site_url('kasir/riwayat-transaksi') ?>" method="get" class="form-horizontal">
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
                                    <label for="search_id_transaksi" class="col-sm-4 control-label">ID Trans:</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="search_id_transaksi" id="search_id_transaksi" class="form-control" value="<?= esc($selected_id_transaksi ?? '') ?>" placeholder="Cari ID/Kode Transaksi">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="<?= site_url('kasir/riwayat-transaksi') ?>" class="btn btn-default">Reset</a>
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
                    Daftar Transaksi
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-pagination="true" data-sort-name="kode_transaksi" data-sort-order="desc">
                        <thead>
                        <tr>
                            <th data-field="kode_transaksi" data-sortable="true">ID/Kode Transaksi</th>
                            <th data-field="tanggal" data-sortable="true">Tanggal & Waktu</th>
                            <th data-field="pelanggan" data-sortable="true">Member</th>
                            <th data-field="total" data-sortable="true" data-align="right">Total Harga</th>
                            <th data-field="metode" data-sortable="true">Metode Bayar</th>
                            <th data-field="alasan_tolak" data-sortable="false">Alasan Penolakan</th>
                            <th data-field="actions">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($riwayat_transaksi)): ?>
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
                                <td><?= esc(number_to_currency($transaksi['total_harga'], 'IDR', 'id_ID', 0)) ?></td>
                                <td><?= esc(ucwords(str_replace('_', ' ', $transaksi['metode_pembayaran'] ?? 'N/A'))) ?></td>
                                <td style="min-width: 150px;"> <!-- Kolom Alasan Penolakan -->
                                    <?php if (isset($transaksi['status_penghapusan']) && $transaksi['status_penghapusan'] === 'rejected' && isset($transaksi['alasan_penolakan_owner']) && !empty(trim((string)$transaksi['alasan_penolakan_owner']))): ?>
                                        <span class="text-danger" data-toggle="tooltip" title="<?= esc($transaksi['alasan_penolakan_owner']) ?>"><?= esc(substr($transaksi['alasan_penolakan_owner'], 0, 30)) . (strlen($transaksi['alasan_penolakan_owner']) > 30 ? '...' : '') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="min-width: 180px;">
                                    <a href="<?= site_url('kasir/transaksi/detail/' . $transaksi['transaksi_id']) ?>" class="btn btn-info btn-xs">
                                        <span class="glyphicon glyphicon-eye-open"></span> Detail
                                    </a>

                                    <?php if (isset($transaksi['status_penghapusan']) && $transaksi['status_penghapusan'] === 'pending_approval'): ?>
                                        <span class="label label-warning" style="margin-left: 5px;">Menunggu Persetujuan</span> <!-- Status: Menunggu -->
                                    <?php elseif (isset($transaksi['status_penghapusan']) && $transaksi['status_penghapusan'] === 'rejected'): ?>
                                        <span class="label label-danger" style="margin-left: 5px; margin-right: 5px;">Ditolak</span>
                                        <?php // Tidak ada tombol request hapus jika sudah ditolak ?>
                                    <?php elseif (isset($transaksi['status_penghapusan']) && $transaksi['status_penghapusan'] === 'approved_for_deletion'): ?>
                                        <span class="label label-info" style="margin-left: 5px; margin-right: 5px;">Disetujui (Proses)</span>
                                    <?php elseif (isset($transaksi['status_penghapusan']) && $transaksi['status_penghapusan'] === 'deleted'): ?>
                                        <span class="label label-success" style="margin-left: 5px; margin-right: 5px;">Telah Dihapus</span>
                                    <?php elseif (!isset($transaksi['status_penghapusan']) || empty($transaksi['status_penghapusan'])): // Tombol Request Hapus hanya muncul jika status belum ada/kosong ?>
                                        <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#requestDeleteModal_<?= esc($transaksi['transaksi_id']) ?>" style="margin-left: 5px;">

                                            Request Hapus
                                        </button>
                                    <?php endif; ?>

                                    <?php
                                    // Modal hanya ditampilkan jika ada tombol yang bisa memicunya
                                    if (!isset($transaksi['status_penghapusan']) || empty($transaksi['status_penghapusan'])):
                                    ?>
                                        <!-- Modal Request Delete -->
                                        <div class="modal fade" id="requestDeleteModal_<?= esc($transaksi['transaksi_id']) ?>" tabindex="-1" role="dialog" aria-labelledby="requestDeleteModalLabel_<?= esc($transaksi['transaksi_id']) ?>">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="<?= site_url('kasir/transaksi/request-delete/' . esc($transaksi['transaksi_id'])) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="requestDeleteModalLabel_<?= esc($transaksi['transaksi_id']) ?>">
                                                                Request Hapus Transaksi #<?= esc($transaksi['transaksi_id']) ?>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Anda akan meminta penghapusan untuk transaksi ini. Stok produk yang terkait akan dikembalikan. Penghapusan permanen memerlukan persetujuan Pemilik.</p>
                                                            
                                                            <div class="form-group">
                                                                <label for="alasan_penghapusan_<?= esc($transaksi['transaksi_id']) ?>">
                                                                    Alasan Penghapusan (Wajib) Dan Poin Tidak Akan Dikembalikan Jika Alasan Ditolak
                                                                </label>
                                                                <textarea name="alasan_penghapusan" id="alasan_penghapusan_<?= esc($transaksi['transaksi_id']) ?>" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Kirim Permintaan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">Belum ada riwayat transaksi.</td></tr>
                        <?php endif; ?>
                       </tbody>
                    </table>
                </div>
            </div>
        </div>

<?= $this->include('Backend/Template/footer') ?>

<script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
    });
</script>
