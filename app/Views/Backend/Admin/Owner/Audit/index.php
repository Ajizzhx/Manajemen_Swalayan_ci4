<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('admin/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/owner-area/audit-log') ?>">Area Pemilik</a></li>
            <li class="active"><?= esc($title) ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Daftar Log Audit</div>
                <div class="panel-body">
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-12">
                            <form action="<?= site_url('admin/owner-area/audit-log') ?>" method="get" class="form-inline">
                                <div class="form-group" style="margin-right: 10px;">
                                    <input type="text" name="keyword" class="form-control" placeholder="Cari aksi/deskripsi/pengguna..." value="<?= esc($keyword ?? '') ?>">
                                </div>
                                <div class="form-group" style="margin-right: 10px;">
                                    <input type="date" name="start_date" class="form-control" placeholder="Tanggal Awal" value="<?= esc($start_date ?? '') ?>">
                                </div>
                                <div class="form-group" style="margin-right: 10px;">
                                    <input type="date" name="end_date" class="form-control" placeholder="Tanggal Akhir" value="<?= esc($end_date ?? '') ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Cari</button>
                                <?php if (!empty($keyword) || !empty($start_date) || !empty($end_date)): ?>
                                    <a href="<?= site_url('admin/owner-area/audit-log') ?>" class="btn btn-default">Reset</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    <?php if (isset($audit_logs) && !empty($audit_logs)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Timestamp</th>
                                        <th>ID Pengguna</th>
                                        <th>Nama Pengguna</th>
                                        <th>Aksi</th>
                                        <th>Deskripsi/Detail</th>
                                        <th>Alamat IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1; 
                                    if (isset($pager) && $pager->getCurrentPage() > 1) {
                                        $no = (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1;
                                    }
                                    foreach ($audit_logs as $log): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc(format_indo($log->created_at, true)) // Asumsi kolom timestamp adalah created_at dan Anda punya helper format_indo ?></td>
                                        <td><?= esc($log->user_id ?? 'N/A') ?></td>
                                        <td><?= esc($log->nama_pengguna ?? ($log->user_id ? 'Pengguna Tidak Ditemukan' : 'Sistem')) ?></td>
                                        <td><?= esc($log->action) // Asumsi ada kolom action ?></td>
                                        <td><?= esc($log->description ?? '') // Asumsi ada kolom description ?></td>
                                        <td><?= esc($log->ip_address ?? 'N/A') // Asumsi ada kolom ip_address ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (isset($pager)): ?>
                            <?= $pager->links() ?>
                        <?php endif; ?>
                    <?php elseif (isset($audit_logs) && empty($audit_logs)): ?>
                        <div class="alert alert-info">Tidak ada data log audit yang ditemukan.</div>
                    <?php else: ?>
                        <p>Data log audit belum dimuat. Silakan periksa konfigurasi controller.</p>
                        
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('Backend/Template/footer') ?>