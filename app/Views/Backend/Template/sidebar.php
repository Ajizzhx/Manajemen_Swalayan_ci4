<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
		<form role="search">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search" id="sidebarSearchInput">
			</div>
		</form>
		<ul class="nav menu">
        <?php $userRole = session()->get('role'); ?>
        <?php if (in_array($userRole, ['admin', 'pemilik', 'kepala_toko'])): ?>
				<li class="<?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>"><a href="<?= site_url('admin/dashboard') ?>"><span class="glyphicon glyphicon-dashboard"></span> Dashboard</a></li>
				
				<?php if ($userRole === 'admin'): ?>
				<li class="parent <?= (strpos(uri_string(), 'admin/produk') !== false || strpos(uri_string(), 'admin/kategori') !== false || strpos(uri_string(), 'admin/supplier') !== false) ? 'active' : '' ?>">
					<a data-toggle="collapse" href="#sub-item-master">
						<span class="glyphicon glyphicon-cog"></span> Kelola Data Master <span class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
					</a>
					<ul class="children collapse <?= (strpos(uri_string(), 'admin/produk') !== false || strpos(uri_string(), 'admin/kategori') !== false || strpos(uri_string(), 'admin/supplier') !== false || strpos(uri_string(), 'admin/pelanggan') !== false) ? 'in' : '' ?>" id="sub-item-master">
						<li class="<?= (strpos(uri_string(), 'admin/produk') !== false) ? 'active' : '' ?>"><a class="" href="<?= site_url('admin/produk') ?>"><span class="glyphicon glyphicon-shopping-cart"></span> Produk</a></li>
						<li class="<?= (strpos(uri_string(), 'admin/kategori') !== false) ? 'active' : '' ?>"><a class="" href="<?= site_url('admin/kategori') ?>"><span class="glyphicon glyphicon-list"></span> Kategori</a></li>
						<li class="<?= (strpos(uri_string(), 'admin/supplier') !== false) ? 'active' : '' ?>"><a class="" href="<?= site_url('admin/supplier') ?>"><span class="glyphicon glyphicon-briefcase"></span> Supplier</a></li>
						<li class="<?= (strpos(uri_string(), 'admin/pelanggan') !== false) ? 'active' : '' ?>"><a class="" href="<?= site_url('admin/pelanggan') ?>"><span class="glyphicon glyphicon-star-empty"></span> Membership</a></li>
					</ul>
				</li>
				<?php endif; ?>

				<?php if (in_array($userRole, ['kepala_toko', 'admin'])): ?>
				<li class="parent <?= (strpos(uri_string(), 'admin/monitoring') !== false) ? 'active' : '' ?>">
					<a data-toggle="collapse" href="#sub-item-monitoring">
						<span class="glyphicon glyphicon-eye-open"></span> Monitoring <span class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
					</a>
					<ul class="children collapse <?= (strpos(uri_string(), 'admin/monitoring') !== false) ? 'in' : '' ?>" id="sub-item-monitoring">
						<li class="<?= (strpos(uri_string(), 'admin/monitoring/stok') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/monitoring/stok') ?>"><span class="glyphicon glyphicon-stats"></span> Monitoring Stok</a>
						</li>
						<li class="<?= (strpos(uri_string(), 'admin/monitoring/penjualan') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/monitoring/penjualan') ?>"><span class="glyphicon glyphicon-signal"></span> Monitoring Penjualan</a>
						</li>
						<li class="<?= (strpos(uri_string(), 'admin/monitoring/kasir') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/monitoring/kasir') ?>"><span class="glyphicon glyphicon-user"></span> Monitoring Kasir</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>

				<li class="parent <?= (strpos(uri_string(), 'admin/laporan') !== false) ? 'active' : '' ?>">
					<a data-toggle="collapse" href="#sub-item-laporan">
						<span class="glyphicon glyphicon-stats"></span> Laporan & Analisis <span class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
					</a>
					<ul class="children collapse <?= (strpos(uri_string(), 'admin/laporan') !== false || (strpos(uri_string(), 'admin/audit-log') !== false && $userRole === 'admin')) ? 'in' : '' ?>" id="sub-item-laporan">
						<li class="<?= (strpos(uri_string(), 'admin/laporan/transaksi') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/laporan/transaksi') ?>"><span class="glyphicon glyphicon-transfer"></span> Riwayat Transaksi</a>
						</li>
						<?php if (in_array($userRole, ['pemilik', 'kepala_toko'])): ?>
						<li class="<?= (strpos(uri_string(), 'admin/laporan/pendapatan') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/laporan/pendapatan') ?>"><span class="glyphicon glyphicon-usd"></span> Pendapatan</a>
						</li>
						<?php endif; ?>
						<li class="<?= (strpos(uri_string(), 'admin/laporan/produk-terlaris') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/laporan/produk-terlaris') ?>"><span class="glyphicon glyphicon-star"></span> Produk Terlaris</a>
						</li>
						<?php if ($userRole === 'admin'): ?>
						<li class="<?= (strpos(uri_string(), 'admin/audit-log') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/audit-log') ?>"><span class="glyphicon glyphicon-eye-open"></span> Log Audit</a>
						</li>
						<?php endif; ?>
					</ul>
				</li>

				<?php if ($userRole === 'pemilik'): ?>
				<li class="parent <?= (strpos(uri_string(), 'admin/owner-area') !== false) ? 'active' : '' ?>">
					<a data-toggle="collapse" href="#sub-item-owner">
						<span class="glyphicon glyphicon-lock"></span> Area Pemilik <span class="icon pull-right"><em class="glyphicon glyphicon-s glyphicon-plus"></em></span>
					</a>
					<ul class="children collapse <?= (strpos(uri_string(), 'admin/owner-area') !== false) ? 'in' : '' ?>" id="sub-item-owner">
						<li class="<?= (strpos(uri_string(), 'admin/owner-area/financial-reports') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/owner-area/financial-reports') ?>"><span class="glyphicon glyphicon-list-alt"></span> Laporan Keuangan</a>
						</li>
						<li class="<?= (strpos(uri_string(), 'admin/owner-area/audit-log') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/owner-area/audit-log') ?>"><span class="glyphicon glyphicon-eye-open"></span> Log Audit</a>
						</li>
						<li class="<?= (strpos(uri_string(), 'admin/owner-area/karyawan') !== false) ? 'active' : '' ?>">
							<a href="<?= site_url('admin/owner-area/karyawan') ?>"><span class="glyphicon glyphicon-briefcase"></span> Kelola Karyawan</a>
						</li>
					</ul>
				</li>
				<?php endif; ?>

			<?php elseif ($userRole === 'kasir'): ?>
				<li class="<?= (uri_string() == 'kasir/dashboard') ? 'active' : '' ?>"><a href="<?= site_url('kasir/dashboard') ?>"><span class="glyphicon glyphicon-dashboard"></span> Dashboard Kasir</a></li>
				<li class="<?= (uri_string() == 'kasir/transaksi' && strpos(uri_string(), 'kasir/transaksi/detail/') === false) ? 'active' : '' ?>"><a href="<?= site_url('kasir/transaksi') ?>"><span class="glyphicon glyphicon-shopping-cart"></span> Transaksi Penjualan</a></li>
				<li class="<?= (uri_string() == 'kasir/riwayat-transaksi' || strpos(uri_string(), 'kasir/transaksi/detail/') !== false) ? 'active' : '' ?>">
					<a href="<?= site_url('kasir/riwayat-transaksi') ?>"><span class="glyphicon glyphicon-list-alt"></span> Riwayat Transaksi</a>
				</li>
				<li class="<?= (uri_string() == 'kasir/cek-produk') ? 'active' : '' ?>">
					<a href="<?= site_url('kasir/cek-produk') ?>"><span class="glyphicon glyphicon-search"></span> Cek Harga & Stok</a>
				</li>
			<?php endif; ?>
			<li role="presentation" class="divider"></li>
			<li><a href="#" id="sidebarLogoutLink"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
		</ul>
	</div><!--/.sidebar-->