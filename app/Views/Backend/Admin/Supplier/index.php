<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/supplier') ?>">Kelola Data Master</a></li>
            <li class="active">Supplier</li>
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
                Daftar Supplier
                <a href="<?= site_url('/admin/supplier/create') ?>" class="btn btn-primary btn-sm pull-right">Tambah Supplier</a>
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
				        <th data-field="no" data-sortable="true">No</th>
				        <th data-field="nama" data-sortable="true">Nama Supplier</th>
				        <th data-field="alamat" data-sortable="true">Alamat</th>
				        <th data-field="telepon" data-sortable="true">Telepon</th>
                        <th data-field="actions">Aksi</th>
				    </tr>
				    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($supplier->nama) ?></td>
                            <td><?= esc($supplier->alamat) ?></td>
                            <td><?= esc($supplier->telepon) ?></td>
                            <td>
                                <a href="<?= site_url('/admin/supplier/edit/' . $supplier->supplier_id) ?>" class="btn btn-warning btn-xs">
                                    <span class="glyphicon glyphicon-edit"></span> Edit
                                </a>
                                <form action="<?= site_url('/admin/supplier/delete/' . $supplier->supplier_id) ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
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

<?= $this->include('Backend/Template/footer') ?>