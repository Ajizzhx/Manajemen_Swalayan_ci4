<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url('admin/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/produk') ?>">Kelola Produk</a></li>
            <li class="active">Tambah Produk</li>
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
                <div class="panel-heading">Form Tambah Produk</div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('error') || (isset($validation) && $validation->getErrors())): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') // General error from controller ?>
                            <?php if(isset($validation) && $validation->getErrors()): ?>
                                <p>Harap perbaiki kesalahan berikut:</p>
                                <?= $validation->listErrors() // Validation specific errors ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form role="form" action="<?= site_url('admin/produk/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Produk" value="<?= old('nama') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori_id" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                <?php if (!empty($kategori)): ?>
                                    <?php foreach ($kategori as $kat): ?>
                                        <option value="<?= $kat->kategori_id ?>" <?= (old('kategori_id') == $kat->kategori_id) ? 'selected' : '' ?>>
                                            <?= esc($kat->nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Supplier</label>
                            <select name="supplier_id" class="form-control" required>
                                <option value="">Pilih Supplier</option>
                                 <?php if (!empty($supplier)): ?>
                                    <?php foreach ($supplier as $sup): ?>
                                        <option value="<?= $sup->supplier_id ?>" <?= (old('supplier_id') == $sup->supplier_id) ? 'selected' : '' ?>>
                                            <?= esc($sup->nama) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" placeholder="Masukkan Harga" value="<?= old('harga') ?>" required min="0" step="any">
                        </div>

                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" placeholder="Masukkan Jumlah Stok" value="<?= old('stok') ?>" required min="0" step="1">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= site_url('admin/produk') ?>" class="btn btn-default">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div><!--/.row-->
</div><!--/.main-->
