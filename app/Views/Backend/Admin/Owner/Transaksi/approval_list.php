<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('admin/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active"><?= esc($title) ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Daftar Permintaan Penghapusan Transaksi</div>
                <div class="panel-body">
                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="tanggal_req" data-sort-order="desc">
                        <thead>
                            <tr>
                                <th data-field="id_transaksi" data-sortable="true">ID Transaksi</th>
                                <th data-field="tanggal_transaksi" data-sortable="true">Tgl Transaksi</th>
                                <th data-field="kasir_transaksi" data-sortable="true">Kasir Transaksi</th>
                                <th data-field="dibatalkan_oleh" data-sortable="true">Diminta/Dibatalkan Oleh</th>
                                <th data-field="tanggal_req" data-sortable="true">Tgl Request Hapus</th>
                                <th data-field="alasan" data-sortable="false">Alasan</th>
                                <th data-field="total" data-sortable="true">Total</th>
                                <th data-field="status_hapus" data-sortable="true">Status Hapus</th>
                                <th data-field="aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transaksi_pending)): ?>
                                <?php foreach ($transaksi_pending as $trx): ?>
                                    <tr>
                                        <td><a href="<?= site_url('admin/laporan/transaksi/detail/' . esc($trx['transaksi_id'])) ?>" target="_blank"><?= esc($trx['transaksi_id']) ?></a></td>
                                        <td><?= esc(format_indo($trx['created_at'], true)) ?></td>
                                        <td><?= esc($trx['nama_kasir'] ?? 'N/A') ?></td> <!-- Dari join k_kasir -->
                                        <td><?= esc($trx['nama_pembatal'] ?? 'N/A') ?></td> <!-- Dari join k_pembatal -->
                                        <td><?= esc(format_indo($trx['tanggal_dibatalkan'], true)) ?></td>
                                        <td data-toggle="tooltip" title="<?= esc($trx['alasan_pembatalan']) ?>"><?= esc(substr($trx['alasan_pembatalan'], 0, 50)) . (strlen($trx['alasan_pembatalan']) > 50 ? '...' : '') ?></td>
                                        <td><?= esc(number_to_currency($trx['total_harga'], 'IDR', 'id_ID', 0)) ?></td>
                                        <td>
                                            <?php
                                            $status_label = 'default';
                                            if ($trx['status_penghapusan'] === 'pending_approval') $status_label = 'warning';
                                            if ($trx['status_penghapusan'] === 'approved_for_deletion') $status_label = 'info';
                                            if ($trx['status_penghapusan'] === 'rejected') $status_label = 'danger';
                                            if ($trx['status_penghapusan'] === 'deleted_by_owner') $status_label = 'success';
                                            ?>
                                            <span class="label label-<?= $status_label ?>"><?= esc(ucwords(str_replace('_', ' ', $trx['status_penghapusan']))) ?></span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-xs btn-lihat-detail" data-transaksiid="<?= esc($trx['transaksi_id']) ?>" data-toggle="modal" data-target="#detailTransaksiModal">
                                                <span class="glyphicon glyphicon-eye-open"></span> Detail
                                            </button>
                                            <?php if ($trx['status_penghapusan'] === 'pending_approval'): ?>
                                                <form action="<?= site_url('admin/owner-area/transaksi-approval/approve/' . esc($trx['transaksi_id'])) ?>" method="post" style="display: inline-block;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Anda yakin ingin menyetujui penghapusan transaksi ini?')">Setujui</button>
                                                </form>
                                                <!-- Tombol Tolak yang memicu modal -->
                                                <button type="button" class="btn btn-warning btn-xs btn-reject-request" data-transaksi-id="<?= esc($trx['transaksi_id']) ?>" data-action-url="<?= site_url('admin/owner-area/transaksi-approval/reject/' . esc($trx['transaksi_id'])) ?>">
                                                    Tolak
                                                </button>
                                            <?php elseif ($trx['status_penghapusan'] === 'approved_for_deletion'): ?>
                                                <form action="<?= site_url('admin/owner-area/transaksi-approval/delete-permanent/' . esc($trx['transaksi_id'])) ?>" method="post" style="display: inline-block;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('PERHATIAN: Transaksi ini akan dihapus (soft delete). Aksi ini tidak dapat diurungkan. Lanjutkan?')">Hapus Permanen</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada permintaan penghapusan transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Detail Transaksi -->
<div class="modal fade" id="detailTransaksiModal" tabindex="-1" role="dialog" aria-labelledby="detailTransaksiModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="detailTransaksiModalLabel">Detail Item Transaksi</h4>
            </div>
            <div class="modal-body">
                <div id="detailTransaksiContent">
                    <p class="text-center">Memuat detail transaksi...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alasan Penolakan -->
<div class="modal fade" id="modalAlasanPenolakan" tabindex="-1" role="dialog" aria-labelledby="modalAlasanPenolakanLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formRejectRequest" action="" method="POST"> <!-- Action akan diisi oleh JS -->
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="rejected"> <!-- Meskipun tidak digunakan di KasirController, ini bisa jadi penanda -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalAlasanPenolakanLabel">Alasan Penolakan Transaksi #<span id="modalTransaksiIdDisplayReject"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="alasan_penolakan_owner">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan_owner" name="alasan_penolakan_owner_input" rows="3" required></textarea>
                        <div class="invalid-feedback" style="color: red; display: none;">
                            Alasan penolakan wajib diisi, dan poin member tidak akan dikembalikan.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->include('Backend/Template/footer') ?>
<script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()

    // Script untuk Modal Penolakan
    $('.btn-reject-request').on('click', function() {
        var transaksiId = $(this).data('transaksi-id');
        var actionUrl = $(this).data('action-url');
        
        $('#formRejectRequest').attr('action', actionUrl);
        $('#modalTransaksiIdDisplayReject').text(transaksiId);
        $('#alasan_penolakan_owner').val(''); // Kosongkan textarea
        $('.invalid-feedback').hide(); // Sembunyikan pesan error
        $('#modalAlasanPenolakan').modal('show');
    });

    $('#formRejectRequest').on('submit', function(e) {
        var alasan = $('#alasan_penolakan_owner').val().trim();
        if (alasan === '') {
            e.preventDefault(); // Hentikan submit form
            $('#alasan_penolakan_owner').addClass('is-invalid'); // Jika Anda menggunakan Bootstrap untuk styling error
            $('.invalid-feedback').show();
        } else {
            $('#alasan_penolakan_owner').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            // Tambahkan konfirmasi sebelum submit jika diinginkan
            if(!confirm('Anda yakin ingin menolak permintaan penghapusan transaksi ini? Stok akan disesuaikan kembali.')) {
                e.preventDefault();
            }
        }
    });

    $('.btn-lihat-detail').on('click', function() {
        var transaksiId = $(this).data('transaksiid');
        $('#detailTransaksiModalLabel').text('Detail Item Transaksi #' + transaksiId);
        $('#detailTransaksiContent').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Memuat detail transaksi...</p>');

        $.ajax({
            url: "<?= site_url('admin/owner-area/transaksi-approval/detail-items/') ?>" + transaksiId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.html) {
                    $('#detailTransaksiContent').html(response.html);
                } else {
                    $('#detailTransaksiContent').html('<p class="text-danger">Gagal memuat detail: ' + (response.error || 'Data tidak ditemukan.') + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
                $('#detailTransaksiContent').html('<p class="text-danger">Terjadi kesalahan saat mengambil data detail transaksi. Silakan coba lagi.</p>');
            }
        });
      });
    });

    // Inisialisasi Bootstrap Table jika belum otomatis
    // $(document).ready(function () {
    //     $('table').bootstrapTable();
    // });
</script>
