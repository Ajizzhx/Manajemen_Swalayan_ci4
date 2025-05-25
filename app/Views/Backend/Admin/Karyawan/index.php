<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') === 'pemilik' ? 'admin/owner-area/dashboard' : session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Kelola Karyawan</li>
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
                Daftar Karyawan
                <a href="<?= site_url('admin/owner-area/karyawan/create') ?>" class="btn btn-primary btn-sm pull-right">Tambah Karyawan</a>
            </div>
			<div class="panel-body">
                <?php if (session()->getFlashdata('message')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

				<table data-toggle="table" data-url="#"  data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="no" data-sort-order="asc">
				    <thead>
				    <tr>
				        <th data-field="no" data-sortable="true">No</th>
				        <th data-field="nama" data-sortable="true">Nama</th>
				        <th data-field="email" data-sortable="true">Email</th>
				        <th data-field="role" data-sortable="true">Role</th>
                        <th data-field="actions">Aksi</th>
				    </tr>
				    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($karyawan as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['nama']) ?></td>
                            <td><?= esc($item['email']) ?></td>
                            <td><?= esc(ucfirst($item['role'])) ?></td>
                            <td>
                                <a href="<?= site_url('admin/owner-area/karyawan/edit/' . $item['karyawan_id']) ?>" class="btn btn-warning btn-xs">
                                    <span class="glyphicon glyphicon-edit"></span> Edit
                                </a>
                                <form action="<?= site_url('admin/owner-area/karyawan/delete/' . $item['karyawan_id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-xs">
                                        <span class="glyphicon glyphicon-trash"></span> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
				</table>
                <?php /* if (isset($pager)): ?>
                <div class="panel-footer">
                    <?= $pager->links('group1', 'bootstrap_lumino_pagination') // 'group1' harus sama dengan yang di controller ?>
                </div>
                <?php endif; */ ?>
			</div>
		</div>
	</div>

<?= $this->include('Backend/Template/footer') ?>
