<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?= esc($title ?? 'Dashboard Kasir') ?></h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-xs-12 col-md-6 col-lg-4">
			<div class="panel panel-blue panel-widget ">
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-transfer glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc($transaksi_hari_ini ?? 0) ?></div>
						<div class="text-muted">Transaksi Hari Ini</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-4">
			<div class="panel panel-orange panel-widget">
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-usd glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large"><?= esc(number_to_currency($pendapatan_hari_ini ?? 0, 'IDR', 'id_ID', 0)) ?></div>
						<div class="text-muted">Pendapatan Hari Ini</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6 col-lg-4">
			<div class="panel panel-teal panel-widget">
				<div class="row no-padding">
					<div class="col-sm-3 col-lg-5 widget-left">
						<em class="glyphicon glyphicon-time glyphicon-l"></em>
					</div>
					<div class="col-sm-9 col-lg-7 widget-right">
						<div class="large" id="waktu-sekarang"><?= date('H:i:s') ?></div>
						<div class="text-muted">Waktu Sekarang</div>
					</div>
				</div>
			</div>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">					Selamat Datang, <?= esc(session()->get('nama') ?? 'Kasir') ?>!
				</div>
				<div class="panel-body">
					<p>Ini adalah halaman dashboard utama untuk Kasir.</p>
					<p>Silakan akses menu Transaksi Penjualan untuk memulai.</p>
					<a href="<?= site_url('kasir/transaksi') ?>" class="btn btn-primary"><span class="glyphicon glyphicon-shopping-cart"></span> Mulai Transaksi Baru</a>
				</div>
			</div>
		</div>
	</div>

<?= $this->include('Backend/Template/footer') ?>

<script>
	
	function updateWaktu() {
		const now = new Date();
		const hours = String(now.getHours()).padStart(2, '0');
		const minutes = String(now.getMinutes()).padStart(2, '0');
		const seconds = String(now.getSeconds()).padStart(2, '0');
		document.getElementById('waktu-sekarang').textContent = `${hours}:${minutes}:${seconds}`;
	}
	setInterval(updateWaktu, 1000);
	updateWaktu(); 
</script>