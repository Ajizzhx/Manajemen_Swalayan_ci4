<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?= esc($title ?? 'Dashboard') ?></h1>
			</div>
	</div><!--/.row-->

	<!-- Notifikasi untuk Pemilik -->
	<?php if (isset($user_role) && $user_role === 'pemilik' && isset($transaksi_pending_approval_count) && $transaksi_pending_approval_count > 0): ?>
	<div class="row">
		<div class="col-lg-12">
			<div class="alert alert-warning">
				<span class="glyphicon glyphicon-exclamation-sign"></span>
				<strong>Perhatian Pemilik!</strong> Ada <strong><?= esc($transaksi_pending_approval_count) ?></strong> transaksi yang menunggu persetujuan penghapusan Anda.
				<a href="<?= site_url('admin/owner-area/transaksi-approval') ?>" class="alert-link">Lihat Detail & Proses Sekarang</a>
				<?php // Pastikan URL 'admin/owner-area/approval-transaksi-hapus' sesuai dengan rute halaman persetujuan Anda ?>
			</div>
		</div>
	</div><!--/.row-->
	<?php endif; ?> <!-- This was missing -->

	<div class="row">
		<div class="col-xs-12 col-md-6 col-lg-3">
			<div class="panel panel-blue panel-widget ">
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-shopping-cart glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc($total_produk ?? 0) ?></div>
						<div class="text-muted">Total Produk</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-3">
			<div class="panel panel-orange panel-widget">
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-tags glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc($total_kategori ?? 0) ?></div>
						<div class="text-muted">Total Kategori</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-3">
			<div class="panel panel-teal panel-widget">
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-user glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc($total_karyawan ?? 0) ?></div>
						<div class="text-muted">Total Karyawan</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-3">
			<div class="panel panel-red panel-widget">
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-briefcase glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc($total_supplier ?? 0) ?></div>
						<div class="text-muted">Total Supplier</div>
					</div>
				</div>
			</div>
		</div>
</div><!--/.row-->

	<div class="row">
		<div class="col-xs-12 col-md-6 col-lg-4"> 
			<div class="panel panel-green panel-widget"> 
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-stats glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc(number_to_currency($pendapatan_harian ?? 0, 'IDR', 'id_ID', 0)) ?></div>
						<div class="text-muted">Penjualan Hari Ini</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-4"> 
			<div class="panel panel-yellow panel-widget"> 
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-exclamation-sign glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc($produk_stok_rendah ?? 0) ?></div>
						<div class="text-muted">Produk Stok Rendah (&le;<?= esc($batas_stok_rendah ?? 5) ?>)</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-4"> 
			<div class="panel panel-purple panel-widget"> 
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-heart glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc($pelanggan_aktif ?? 0) ?></div>
						<div class="text-muted">Membership Aktif</div>
					</div>
				</div>
			</div>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Selamat Datang, <?= esc(session()->get('nama') ?? 'Pengguna') ?>!
					(<?= esc(ucfirst(session()->get('role'))) ?>)
				</div>
				<div class="panel-body">
					<?php if (session()->get('role') === 'pemilik'): ?>
						<p>Ini adalah halaman dashboard utama untuk Anda sebagai <strong>Pemilik</strong>.</p>
						<p>Anda memiliki akses penuh ke semua fitur administrasi dan laporan keuangan detail.</p>
						<div class="alert alert-info">
							<p><strong>Area Khusus Pemilik:</strong></p>
							<ul>
								<li><a href="<?= site_url('admin/owner-area/financial-reports') ?>">Lihat Laporan Keuangan Detail</a></li>
								<li><a href="<?= site_url('admin/owner-area/audit-log') ?>">Lihat Log Audit Sistem</a></li>
							<li>
									<a href="<?= site_url('admin/owner-area/transaksi-approval') ?>" style="position: relative; display: inline-block; padding-right: 15px;">
										<span style="position: relative; display: inline-block;">
											Persetujuan Hapus Transaksi
										<?php if (isset($transaksi_pending_approval_count) && $transaksi_pending_approval_count > 0): ?>
											<span style="position: absolute; top: -3px; right: -10px; width: 8px; height: 8px; background-color: red; border-radius: 50%;" title="Ada <?= esc($transaksi_pending_approval_count) ?> transaksi menunggu persetujuan"></span>
										<?php endif; ?>
										</span>
									</a>
								</li>
							</ul>
						</div>
					<?php elseif (session()->get('role') === 'admin'): ?>
						<p>Ini adalah halaman dashboard utama untuk Administrator.</p>
					<?php endif; ?>
					<p>Anda dapat mengelola data master, karyawan, dan melihat laporan melalui menu di samping.</p>
				</div>
			</div>
		</div>
	</div>

<?= $this->include('Backend/Template/footer') ?>