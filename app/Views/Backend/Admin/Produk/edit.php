<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/produk') ?>">Kelola Data Master</a></li>
            <li><a href="<?= site_url('admin/produk') ?>">Produk</a></li>
            <li class="active">Edit Produk</li>
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
                Form Edit Produk
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

                <?= form_open('/admin/produk/update/' . $produk->produk_id) ?>
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="nama">Nama Produk</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $produk->nama) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_barcode">Kode Barcode</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="kode_barcode" name="kode_barcode" value="<?= old('kode_barcode', $produk->kode_barcode) ?>" required placeholder="Ketik atau scan kode barcode">
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
                                <option value="<?= esc($kat->kategori_id) ?>" <?= old('kategori_id', $produk->kategori_id) == $kat->kategori_id ? 'selected' : '' ?>>
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
                                <option value="<?= esc($sup->supplier_id) ?>" <?= old('supplier_id', $produk->supplier_id) == $sup->supplier_id ? 'selected' : '' ?>>
                                    <?= esc($sup->nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" value="<?= old('harga', $produk->harga) ?>" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" value="<?= old('stok', $produk->stok) ?>" required min="0">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="<?= site_url('/admin/produk') ?>" class="btn btn-default">Batal</a>
                <?= form_close() ?>
			</div>
		</div>
	</div>

<!-- Library QR Scanner -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<?= $this->include('Backend/Template/footer') ?> 


<script>
$(document).ready(function() {
    let html5QrCodeAdmin = null;
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
        if (!html5QrCodeAdmin) {
            html5QrCodeAdmin = new Html5Qrcode("admin-barcode-reader");
        }

        $("#admin-barcode-reader").show();
        $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-stop"></span> Stop Scan').prop('disabled', false);

        html5QrCodeAdmin.start(
            { facingMode: "environment" }, 
            qrConfigAdmin,
            onScanSuccessAdmin,
            onScanFailureAdmin
        ).catch(err => {
            console.error("Tidak dapat memulai pemindaian barcode (Admin).", err);
            alert("Tidak dapat memulai kamera. Pastikan izin kamera telah diberikan dan halaman diakses melalui HTTPS jika tidak di localhost. Pemindai ini mendukung barcode 1D dan QR Code.");
            stopScannerAdmin(); 
        });
    }

    function stopScannerAdmin() {
        if (html5QrCodeAdmin && html5QrCodeAdmin.isScanning) {
            html5QrCodeAdmin.stop().then(() => {
                $("#admin-barcode-reader").hide();
                $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false);
                console.log("Admin Barcode scanning stopped.");
            }).catch(err => {
                console.error("Failed to stop admin barcode scanner", err);
                $("#admin-barcode-reader").hide(); 
                $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false);
            });
        } else {
             $("#admin-barcode-reader").hide();
             $('#start-scan-barcode-admin-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false);
        }
    }

    $('#start-scan-barcode-admin-btn').on('click', function() {
        if (html5QrCodeAdmin && html5QrCodeAdmin.isScanning) {
            stopScannerAdmin();
        } else {
            startScannerAdmin();
        }
    });


    $(window).on('beforeunload', function(){
        stopScannerAdmin();
    });
    $('form[action="<?= site_url('/admin/produk/update/' . $produk->produk_id) ?>"]').on('submit', function(){
        stopScannerAdmin();
    });
});
</script>