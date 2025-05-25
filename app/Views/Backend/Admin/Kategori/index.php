<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/kategori') ?>">Kelola Data Master</a></li>
            <li class="active">Kategori Produk</li>
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
                Daftar Kategori
                <a href="<?= site_url('/admin/kategori/create') ?>" class="btn btn-primary btn-sm pull-right">Tambah Kategori</a>
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
				        <th data-field="nama" data-sortable="true">Nama Kategori</th>
                        <th data-field="actions">Aksi</th>
				    </tr>
				    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($kategori as $kat): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($kat->nama) ?></td>
                            <td>
                                <a href="<?= site_url('/admin/kategori/edit/' . $kat->kategori_id) ?>" class="btn btn-warning btn-xs">
                                    <span class="glyphicon glyphicon-edit"></span> Edit
                                </a>
                                <form action="<?= site_url('/admin/kategori/delete/' . $kat->kategori_id) ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Menghapus kategori dapat mempengaruhi produk terkait.');">
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