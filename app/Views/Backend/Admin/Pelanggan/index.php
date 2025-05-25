<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/pelanggan') ?>">Kelola Data Master</a></li>
            <li class="active">Membership</li>
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
                    Daftar Member
                    <a href="<?= site_url('/admin/pelanggan/create') ?>" class="btn btn-primary btn-sm pull-right">Tambah Pelanggan</a>
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-pagination="true" data-sort-name="no" data-sort-order="asc">
                        <thead>
                        <tr>
                            <th data-field="no" data-sortable="false">No</th>
                            <th data-field="nama" data-sortable="true">Nama Member</th>
                            <th data-field="email" data-sortable="true">Email</th>
                            <th data-field="telepon" data-sortable="true">Telepon</th>
                            <th data-field="alamat" data-sortable="true">Alamat</th>
                            <th data-field="actions">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($pelanggan as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($p->nama) ?></td>
                                <td><?= esc($p->email ?: '-') ?></td>
                                <td><?= esc($p->telepon ?: '-') ?></td>
                                <td><?= esc($p->alamat ?: '-') ?></td>
                                <td>
                                    <a href="<?= site_url('/admin/pelanggan/edit/' . $p->pelanggan_id) ?>" class="btn btn-warning btn-xs">
                                        <span class="glyphicon glyphicon-edit"></span> Edit
                                    </a>
                                    <form action="<?= site_url('/admin/pelanggan/delete/' . $p->pelanggan_id) ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <span class="glyphicon glyphicon-trash"></span> Hapus
                                        </button>
                                    </form>
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