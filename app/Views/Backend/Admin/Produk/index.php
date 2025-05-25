<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/produk') ?>">Kelola Data Master</a></li>
            <li class="active">Produk</li>
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
                Daftar Produk
                <a href="<?= site_url('/admin/produk/create') ?>" class="btn btn-primary btn-sm pull-right">Tambah Produk</a>
            </div>
			<div class="panel-body">
                <?php if (session()->getFlashdata('message')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

				<table data-toggle="table" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-pagination="true" data-sort-order="asc">
				    <thead>
				    <tr>
				        <th data-field="no" data-sortable="false">No</th>
                        <th data-field="kode_barcode_text" data-sortable="true">Kode Barcode</th>
				        <th data-field="nama" data-sortable="true">Nama Produk</th> 
                        <th data-field="barcode" data-sortable="false">Barcode</th>
				        <th data-field="kategori" data-sortable="true">Kategori</th>
				        <th data-field="supplier" data-sortable="true">Supplier</th>
				        <th data-field="harga" data-sortable="true" data-align="right">Harga</th>
				        <th data-field="stok" data-sortable="true" data-align="center">Stok</th>
                        <th data-field="actions">Aksi</th>
				    </tr>
				    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($produks as $produk): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($produk->kode_barcode) ?></td>
                            <td>
                                <?= esc($produk->nama) ?>
                            </td>
                            <td>
                                <?php if (!empty($produk->barcode_path) && file_exists(FCPATH . $produk->barcode_path)): ?>
                                    <img src="<?= base_url($produk->barcode_path) ?>" alt="Barcode <?= esc($produk->kode_barcode) ?>" style="height: 30px; max-width: 150px; object-fit: contain;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= esc($produk->nama_kategori ?? 'N/A') ?></td>
                            <td><?= esc($produk->nama_supplier ?? 'N/A') ?></td>
                            <td><?= number_format($produk->harga, 0, ',', '.') ?></td>
                            <td><?= esc($produk->stok) ?></td>
                            <td>
                                <a href="<?= site_url('/admin/produk/edit/' . $produk->produk_id) ?>" class="btn btn-warning btn-xs">
                                    <span class="glyphicon glyphicon-edit"></span> Edit
                                </a>
                                <form action="<?= site_url('/admin/produk/delete/' . $produk->produk_id) ?>" method="post" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
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