<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/kategori') ?>">Kelola Data Master</a></li>
            <li><a href="<?= site_url('admin/kategori') ?>">Kategori Produk</a></li>
            <li class="active">Edit Kategori</li>
        </ol>
    </div><!--/.row-->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?= esc($title) ?></h1>
		</div>
	</div><!--/.row-->
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
                Form Edit Kategori
                <a href="<?= site_url('/admin/kategori') ?>" class="btn btn-default btn-xs pull-right">Kembali</a>
            </div>
			<div class="panel-body">
                <?php if ($validation->getErrors()): ?>
                    <div class="alert alert-danger">
                        <strong>Gagal menyimpan data:</strong>
                        <ul>
                        <?php foreach ($validation->getErrors() as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= form_open('/admin/kategori/update/' . $kategori->kategori_id) ?>
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="nama">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $kategori->nama) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?= site_url('/admin/kategori') ?>" class="btn btn-default">Batal</a>
                <?= form_close() ?>
			</div>
		</div>
	</div>

<?= $this->include('Backend/Template/footer') ?>