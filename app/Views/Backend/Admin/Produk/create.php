<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/produk') ?>">Kelola Data Master</a></li>
            <li><a href="<?= site_url('admin/produk') ?>">Produk</a></li>
            <li class="active">Tambah Produk</li>
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
                Form Tambah Produk
                <a href="<?= site_url('/admin/produk') ?>" class="btn btn-default btn-xs pull-right">Kembali</a>
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

                <?= form_open('/admin/produk/store') ?>
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="nama">Nama Produk</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_barcode">Kode Barcode</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="kode_barcode" name="kode_barcode" value="<?= old('kode_barcode') ?>" required placeholder="Ketik atau scan kode barcode">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="button" id="start-scan-barcode-admin-btn" title="Scan Barcode dengan Kamera">
                                    <span class="glyphicon glyphicon-qrcode"></span>
                                </button>
                            </span>
                        </div>
                        <div id="admin-barcode-reader" style="width: 100%; max-width:350px; margin-top:10px; display:none; border: 1px solid #ccc; padding:5px; background-color:#f9f9f9;"></div>
                    </div>
                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select class="form-control" id="kategori_id" name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= esc($kat->kategori_id) ?>" <?= old('kategori_id') == $kat->kategori_id ? 'selected' : '' ?>>
                                    <?= esc($kat->nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="supplier_id">Supplier (Opsional)</label>
                        <select class="form-control" id="supplier_id" name="supplier_id">
                            <option value="">Pilih Supplier (Jika Ada)</option>
                            <?php foreach ($supplier as $sup): ?>
                                <option value="<?= esc($sup->supplier_id) ?>" <?= old('supplier_id') == $sup->supplier_id ? 'selected' : '' ?>>
                                    <?= esc($sup->nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" value="<?= old('harga') ?>" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" value="<?= old('stok') ?>" required min="0">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= site_url('/admin/produk') ?>" class="btn btn-default">Batal</a>
                <?= form_close() ?>
			</div>
		</div>
	</div>

<!-- Library QR Scanner - muat pustaka terlebih dahulu -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<?= $this->include('Backend/Template/footer') ?> <!-- Muat jQuery dan JS global lainnya -->

<!-- Skrip kustom yang menggunakan jQuery dan html5-qrcode -->
<script>
$(document).ready(function() {
    const adminBarcodeReaderElementId = "admin-barcode-reader";
    let html5QrCodeAdmin = new Html5Qrcode(adminBarcodeReaderElementId); 
    const qrConfigAdmin = { fps: 10, qrbox: { width: 250, height: 250 }, rememberedStates: true };

    function onScanSuccessAdmin(decodedText, decodedResult) {
        console.log(`Admin Barcode detected: ${decodedText}`);
        $('#kode_barcode').val(decodedText).focus(); 
        
        
        try {
            const audio = new Audio("<?= base_url('assets/sounds/success-scan.mp3') ?>"); // Pastikan file ini ada
            audio.play().catch(e => console.error("Error playing sound:", e));
        } catch (e) {
            console.error("Error initializing audio:", e);
        }
        
        stopScannerAdmin();
    }

    function onScanFailureAdmin(error) {
        
    }

    function startScannerAdmin() {
        console.log("Fungsi startScannerAdmin dipanggil."); 
        

        $("#" + adminBarcodeReaderElementId).show();
        $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-stop"></span> Stop Scan').prop('disabled', false); // Tombol berubah menjadi stop

        console.log("Mencoba memulai html5QrCodeAdmin.start()..."); 
        html5QrCodeAdmin.start(
            { facingMode: "environment" }, 
            qrConfigAdmin,
            onScanSuccessAdmin,
            onScanFailureAdmin
        ).catch(err => {
            console.error("Gagal memulai pemindaian barcode (Admin). Error object:", err);
            alert("Tidak dapat memulai kamera. Detail: " + (err.name ? err.name + " - " : "") + (err.message ? err.message : "Error tidak diketahui") + ". Pastikan izin kamera telah diberikan dan halaman diakses melalui HTTPS jika tidak di localhost. Pemindai ini mendukung barcode 1D dan QR Code.");
            stopScannerAdmin(); 
        });
    }

    function stopScannerAdmin() {
        console.log("Fungsi stopScannerAdmin dipanggil."); 
        if (html5QrCodeAdmin && html5QrCodeAdmin.isScanning) {
            html5QrCodeAdmin.stop().then((ignore) => {
                $("#" + adminBarcodeReaderElementId).hide();
                $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false); 
                console.log("Admin Barcode scanning stopped.");
            }).catch(err => {
                console.error("Failed to stop admin barcode scanner", err);
                $("#" + adminBarcodeReaderElementId).hide(); 
                $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false); 
            });
        } else {
             $("#" + adminBarcodeReaderElementId).hide();
             $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false); 
        }
    }

    $('#start-scan-barcode-admin-btn').on('click', function() {
        console.log("Tombol Scan Barcode Admin diklik."); 
        if (html5QrCodeAdmin && html5QrCodeAdmin.isScanning) { 
            stopScannerAdmin();
        } else { 
            startScannerAdmin();
        }
    });

    
    $(window).on('beforeunload', function(){
        stopScannerAdmin();
    });
    $('form[action="<?= site_url('/admin/produk/store') ?>"]').on('submit', function(){
        stopScannerAdmin();
    });
});
</script>