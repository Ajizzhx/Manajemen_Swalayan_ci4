<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/pelanggan') ?>">Kelola Data Master</a></li>
            <li><a href="<?= site_url('admin/pelanggan') ?>">Membership</a></li>
            <li class="active">Tambah Member</li>
        </ol>
    </div><!--/.row-->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div><!--/.row-->
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                Form Tambah Member
                <a href="<?= site_url('/admin/pelanggan') ?>" class="btn btn-default btn-xs pull-right">Kembali</a>
            </div>
            <div class="panel-body">
                <?php if ($validation && $validation->getErrors()): ?>
                    <div class="alert alert-danger">
                        <strong>Gagal menyimpan data:</strong>
                        <ul>
                        <?php foreach ($validation->getErrors() as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>
                 <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>


                <?= form_open('/admin/pelanggan/store') ?>
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="nama">Nama Member <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email (Opsional)</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>">
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon (Opsional)</label>
                        <input type="text" class="form-control" id="telepon" name="telepon" value="<?= old('telepon') ?>">
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat (Opsional)</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= old('alamat') ?></textarea>
                    </div>
                     <div class="form-group">
                        <label for="diskon_persen">Diskon (%)</label>
                        <input type="number" class="form-control" id="diskon_persen" name="diskon_persen" value="<?= old('diskon_persen', '1.00') ?>" step="0.01" min="0" max="100">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= site_url('/admin/pelanggan') ?>" class="btn btn-default">Batal</a>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>  <!--/.main-->

<?= $this->include('Backend/Template/footer') ?>
