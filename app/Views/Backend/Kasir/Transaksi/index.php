<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Transaksi Penjualan</li>
        </ol>
    </div><!--/.row-->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-7">
            <!-- Product Search and Cart -->
            <div class="panel panel-default">
            <div class="panel-heading">Pilih Produk</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="search_produk" class="sr-only">Cari Produk (Nama/Kode) atau Scan Barcode</label>
                        <div class="input-group">
                            <input type="text" id="search_produk" class="form-control input-lg" placeholder="Ketik Nama Produk / Kode Barcode atau Scan Barcode...">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-lg" type="button" id="start-scan-btn" title="Scan Barcode Produk">
                                    <span class="glyphicon glyphicon-qrcode"></span>
                                </button>
                            </span>
                        </div>
                        <div id="qr-reader" style="width: 100%; max-width:350px; margin-top:10px; display:none; border: 1px solid #ccc; padding:5px; background-color:#f9f9f9;"></div> <!-- ID bisa tetap qr-reader -->
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Keranjang Belanja</div>
                <div class="panel-body" style="min-height: 300px; max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped" id="cart_table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th style="width: 120px;" class="text-right">Harga</th>
                                <th style="width: 90px;" class="text-center">Qty</th>
                                <th style="width: 130px;" class="text-right">Subtotal</th>
                                <th style="width: 50px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div id="cart_empty_message" class="text-center" style="padding-top: 50px;">
                        <p class="lead">Keranjang belanja masih kosong.</p>
                        <span class="glyphicon glyphicon-shopping-cart" style="font-size: 48px; color: #ddd;"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <!-- Customer and Payment -->
            <div class="panel panel-default">
                <div class="panel-heading">Membership</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="search_pelanggan" class="sr-only">Cari Member (Nama/Telepon)</label>
                        <input type="text" id="search_pelanggan" class="form-control" placeholder="Ketik nama atau telepon member...">
                        <input type="hidden" id="selected_pelanggan_id" name="pelanggan_id">
                    </div>
                    <div id="selected_pelanggan_info" style="margin-bottom: 10px; display:none;" class="well well-sm">
                        <strong>Member:</strong> <span id="pelanggan_nama_display"></span>
                        <strong>Poin Saat Ini:</strong> <span id="pelanggan_poin_display">0</span>
                        <button type="button" class="btn btn-xs btn-warning pull-right" id="clear_pelanggan_btn">Ganti</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalTambahPelanggan">
                        <span class="glyphicon glyphicon-plus"></span> Tambah Member Baru
                    </button>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">Ringkasan & Pembayaran</div>
                <div class="panel-body">
                    <div class="summary-item" id="summary_subtotal_row">
                        <span class="summary-label">Subtotal:</span>
                        <span class="summary-value"><strong id="subtotal_display">Rp 0</strong></span>
                    </div>
                    <div class="summary-item text-danger" id="summary_diskon_row" style="display:none;">
                        <span class="summary-label">Diskon Member:</span>
                        <span class="summary-value"><strong id="diskon_display">Rp 0 (0%)</strong></span>
                    </div>

                    <hr id="summary_hr_after_diskon" style="margin-top: 8px; margin-bottom: 8px; display:none;">

                    <div class="summary-item summary-total">
                        <span class="summary-label">Total Bayar:</span>
                        <span class="summary-value"><strong id="total_neto_display">Rp 0</strong></span>
                    </div>

                    <hr style="margin-top: 12px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="uang_bayar">Uang Bayar (Rp)</label>
                        <input type="text" id="uang_bayar_display" class="form-control input-lg text-right" placeholder="0">
                        <input type="hidden" id="uang_bayar" name="uang_bayar">
                    </div>
                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select id="metode_pembayaran" class="form-control">
                            <option value="tunai">Tunai</option>
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <!-- Baris Diskon tambahan (di atas Kembalian) -->
                    <div class="summary-item text-danger" id="summary_diskon_final_row" style="display:none;">
                        <span class="summary-label">Diskon Diterapkan:</span>
                        <span class="summary-value"><strong id="diskon_final_display"></strong></span>
                    </div>

                    <div class="summary-item summary-kembalian">
                        <span class="summary-label">Kembalian:</span>
                        <span class="summary-value"><strong id="kembalian_display">Rp 0</strong></span>
                    </div>
                    <hr style="margin-top: 12px; margin-bottom: 15px;">
                    <button type="button" id="btn_proses_pembayaran" class="btn btn-success btn-lg btn-block" disabled>
                        <span class="glyphicon glyphicon-ok-circle"></span> Proses Pembayaran
                    </button>
                     <button type="button" id="btn_reset_transaksi" class="btn btn-danger btn-block" style="margin-top:10px;">
                        <span class="glyphicon glyphicon-refresh"></span> Reset Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div><!--/.row-->

    <!-- Modal Tambah Pelanggan -->
    <div class="modal fade" id="modalTambahPelanggan" tabindex="-1" role="dialog" aria-labelledby="modalTambahPelangganLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formTambahPelanggan">
                    <?= csrf_field() ?> <!-- CSRF field for the form -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalTambahPelangganLabel">Tambah Member Baru</h4>
                    </div>
                    <div class="modal-body">
                        <div id="pelanggan_form_error" class="alert alert-danger" style="display:none;"></div>
                        <div class="form-group">
                            <label for="no_ktp_pelanggan">No KTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="no_ktp_pelanggan" name="no_ktp_pelanggan" required pattern="[0-9]{8,32}" maxlength="32" minlength="8" title="Masukkan No KTP (8-32 digit angka, unik)">
                        </div>
                        <div class="form-group">
                            <label for="nama_pelanggan">Nama Member <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
                        </div>
                        <div class="form-group">
                            <label for="email_pelanggan">Email</label>
                            <input type="email" class="form-control" id="email_pelanggan" name="email_pelanggan">
                        </div>
                        <div class="form-group">
                            <label for="telepon_pelanggan">Telepon</label>
                            <input type="text" class="form-control" id="telepon_pelanggan" name="telepon_pelanggan">
                        </div>
                        <div class="form-group">
                            <label for="alamat_pelanggan">Alamat</label>
                            <textarea class="form-control" id="alamat_pelanggan" name="alamat_pelanggan" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran Berhasil -->
    <div class="modal fade" id="modalPembayaranBerhasil" tabindex="-1" role="dialog" aria-labelledby="modalPembayaranBerhasilLabel" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalPembayaranBerhasilLabel"><span class="glyphicon glyphicon-ok-sign text-success"></span> Pembayaran Berhasil!</h4>
                </div>
                <div class="modal-body">
                    <p class="text-center lead" style="margin-bottom: 20px;">Transaksi telah berhasil diproses.</p>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td style="width:40%;" class="text-right"><strong>Kode Transaksi:</strong></td>
                                    <td><span id="detail_kode_transaksi_modal"></span></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><strong>Tanggal:</strong></td>
                                    <td><span id="detail_tanggal_transaksi_modal"></span></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><strong>Member:</strong></td>
                                    <td><span id="detail_nama_pelanggan_modal"></span></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><strong>Kasir:</strong></td>
                                    <td><span id="detail_nama_kasir_modal"></span></td>
                                </tr>
                                <tr id="detail_poin_diperoleh_modal_row" style="display:none;">
                                    <td class="text-right"><strong>Poin Diperoleh:</strong></td>
                                    <td><strong id="detail_poin_diperoleh_modal" class="text-success"></strong></td>
                                </tr>
                                <tr id="detail_subtotal_modal_row" style="display:none;">
                                    <td class="text-right"><strong>Subtotal:</strong></td>
                                    <td><span id="detail_subtotal_modal"></span></td>
                                </tr>
                                <tr id="detail_diskon_modal_row" style="display:none;">
                                    <td class="text-right text-danger"><strong>Diskon Member:</strong></td>
                                    <td class="text-danger"><span id="detail_diskon_modal"></span></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><strong>Metode Pembayaran:</strong></td>
                                    <td><span id="detail_metode_pembayaran_modal"></span></td>
                                </tr>
                                <tr style="font-size: 1.1em;">
                                    <td class="text-right"><strong>Total Belanja:</strong></td>
                                    <td><strong id="detail_total_belanja_modal"></strong></td>
                                </tr>
                                <tr style="font-size: 1.1em;">
                                    <td class="text-right"><strong>Uang Bayar:</strong></td>
                                    <td><strong id="detail_uang_bayar_modal"></strong></td>
                                </tr>
                                <tr style="font-size: 1.2em; color: green;">
                                    <td class="text-right"><strong>Kembalian:</strong></td>
                                    <td><strong id="detail_kembalian_modal"></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-center small" style="margin-top:15px;"><em>Terima kasih telah berbelanja!</em></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal" id="btnTutupModalSukses">Tutup</button>
                    <button type="button" class="btn btn-info" id="btnCetakStrukModal" title="Cetak Struk Transaksi">
                        <span class="glyphicon glyphicon-print"></span> Cetak Struk
                    </button>
                    <button type="button" class="btn btn-success" id="btnTransaksiBaruDariModal">
                        <span class="glyphicon glyphicon-plus"></span> Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

</div> <!--/.main-->

<style>
    .panel-body .summary-item { 
        overflow: hidden; 
        margin-bottom: 10px;
        line-height: 1.6;
    }
    .summary-item .summary-label {
        float: left;
        font-size: 1.1em; 
    }
    .summary-item .summary-value {
        float: right;
        font-size: 1.1em;
        font-weight: bold;
    }

    .summary-item.summary-total .summary-label,
    .summary-item.summary-total .summary-value {
        font-size: 1.7em; 
        font-weight: bold;
    }
     .summary-item.summary-total .summary-label { 
        font-weight: bold;
    }

    .summary-item.summary-kembalian .summary-label,
    .summary-item.summary-kembalian .summary-value {
        font-size: 1.4em; 
        font-weight: bold;
    }
    .summary-item.summary-kembalian .summary-label { 
        font-weight: bold;
    }
    .summary-item.text-danger .summary-label { 
        color: #a94442; 
    }
</style>
<?= $this->include('Backend/Template/footer') ?>

<!-- jQuery UI for Autocomplete -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script> <!-- Library QR Scanner -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
$(document).ready(function() {
    let cart = [];
    let csrfName = '<?= csrf_token() ?>'; // CSRF Token Name
    let lastSuccessfulTransactionDetails = null; 
    let csrfHash = '<?= csrf_hash() ?>'; // CSRF Hash
    let currentPelangganPoin = 0; 
    let selectedPelangganNameForStruk = null; 
    let selectedPelangganDiskonPersen = 0; 
    
    // Fungsi untuk mengupdate CSRF token global dan di form
    function updateGlobalCsrfToken(newHash) {
        if (newHash && newHash !== csrfHash) {
            console.log("Updating CSRF token from", csrfHash, "to", newHash);
            csrfHash = newHash;
            $('input[name="' + csrfName + '"]').val(csrfHash); 
        }
    }


    $.ajaxSetup({
        beforeSend: function(xhr, settings) {
            if (settings.type === 'POST' || settings.type === 'PUT' || settings.type === 'DELETE') {
                if (typeof settings.data === 'object' && settings.data !== null && !(settings.data instanceof FormData)) {
             
                    if (!settings.data[csrfName]) {
                        settings.data[csrfName] = csrfHash;
                    }
                } else if (typeof settings.data === 'string' && settings.data.indexOf(csrfName + '=') === -1) {
                   
                    settings.data += (settings.data ? '&' : '') + csrfName + '=' + csrfHash;
                } else if (settings.data instanceof FormData) {
                    if (!settings.data.has(csrfName)) {
                        settings.data.append(csrfName, csrfHash);
                    }
                }
            }
        },
        complete: function(xhr) {
            const newCsrfHeader = xhr.getResponseHeader('X-CSRF-TOKEN');
            if (newCsrfHeader) {
                updateGlobalCsrfToken(newCsrfHeader);
            }
        }
    });
  
    function esc_html(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        if (text === null || typeof text === 'undefined') return '';
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    $("#search_produk").on('keydown', function(event) {
        if (event.key === "Enter" || event.keyCode === 13) { // Tombol Enter
            const $autocompleteInstance = $(this).data('ui-autocomplete');
            const $menuElement = $autocompleteInstance ? $autocompleteInstance.menu.element : null;
            const enteredValue = $(this).val().trim();

            if ($autocompleteInstance && $menuElement && $menuElement.is(":visible")) {
                if ($autocompleteInstance.menu.active) {
                   
                    console.log("Enter pressed on #search_produk: Autocomplete item is active, letting select event handle.");
                    return; 
                } else {
                    
                    event.preventDefault(); 

                    const items = $menuElement.find("li").map(function() {
                        return $(this).data("ui-autocomplete-item"); 
                    }).get();

                    if (items.length > 0) {
                        
                        console.log("Enter pressed on #search_produk: Autocomplete menu visible, no specific keyboard-active item. Selecting the (usually first) focused item programmatically.");
                        $autocompleteInstance.menu.select();
                        return; 
                    }
                    
                    console.log("Enter pressed on #search_produk: Autocomplete menu visible but no items. Attempting to fetch as barcode: '" + enteredValue + "'.");
                    if (enteredValue) {
                        fetchProductByBarcodeAndAddToCart(enteredValue);
                    }
                }
            } else {
                
                event.preventDefault(); 
                console.log("Enter pressed on #search_produk: Autocomplete menu not visible. Attempting to fetch as barcode: '" + enteredValue + "'.");
                if (enteredValue) {
                    fetchProductByBarcodeAndAddToCart(enteredValue);
                }
            }
        }
    });

    $("#search_pelanggan").on('keydown', function(event) {
        if (event.key === "Enter" || event.keyCode === 13) {
            const $autocompleteInstance = $(this).data('ui-autocomplete'); 
            const $menuElement = $autocompleteInstance ? $autocompleteInstance.menu.element : null;
            const enteredValue = $(this).val().trim();

            if ($autocompleteInstance && $menuElement && $menuElement.is(":visible")) {
                if ($autocompleteInstance.menu.active) {
                   
                    console.log("Enter pressed on #search_pelanggan: Autocomplete item is active, letting select event handle.");
                    return; 
                } else {
                   
                    event.preventDefault(); 

                    const items = $menuElement.find("li").map(function() {
                        return $(this).data("ui-autocomplete-item");
                    }).get();

                    if (items.length > 0) {
                       
                        console.log("Enter pressed on #search_pelanggan: Autocomplete menu visible, no specific keyboard-active item. Selecting the (usually first) focused item programmatically.");
                        $autocompleteInstance.menu.select(); 
                        return;
                    }
                   
                    console.log("Enter pressed on #search_pelanggan: Autocomplete menu visible but no items. Doing nothing.");
                }
            } else {
               
                event.preventDefault(); 
                console.log("Enter pressed on #search_pelanggan: Autocomplete menu not visible. Doing nothing.");
            }
        }
    });

    // Pelanggan Search Autocomplete
    $("#search_pelanggan").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "<?= site_url('/kasir/transaksi/search-pelanggan') ?>",
                dataType: "json",
                data: {
                    term: request.term,
                    [csrfName]: csrfHash
                },
                beforeSend: function() {
                    console.log("Searching pelanggan. Term: " + request.term + ", CSRF: " + csrfHash);
                },
                success: function(data) {
                    console.log("Pelanggan search success. Raw Response:", data); // Log the raw data
                    if (data && data.csrf_hash) {
                        updateGlobalCsrfToken(data.csrf_hash);
                        console.log("CSRF token updated from pelanggan search response.");
                    }
                   
                    response(data && data.results ? data.results : []);
                },
                error: function(xhr, status, error) {
                    console.error("Pelanggan Autocomplete AJAX Error. Status: " + status + ", Error: " + error, "XHR:", xhr);
                    if (xhr.responseJSON && xhr.responseJSON.csrf_hash) {
                        updateGlobalCsrfToken(xhr.responseJSON.csrf_hash);
                    }
                    let errMsg = "Error saat mencari member.";
                    if (xhr.responseJSON && xhr.responseJSON.message) errMsg = xhr.responseJSON.message;
                    else if (xhr.statusText) errMsg += " Status: " + xhr.statusText;
                    alert(errMsg);
                    console.error("Pelanggan Autocomplete AJAX Error:", xhr.status, xhr.statusText, xhr.responseText);
                    response([]);
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            console.log("Pelanggan selected:", ui.item); 
            if (typeof ui.item.diskon_persen === 'undefined') {
                console.warn("Peringatan: 'diskon_persen' tidak ditemukan pada data pelanggan yang dipilih dari autocomplete. Default ke 0.", ui.item);
            }
            $("#selected_pelanggan_id").val(ui.item.id);
            selectedPelangganNameForStruk = ui.item.nama;
            $("#pelanggan_nama_display").text(ui.item.nama); // Hanya tampilkan nama
            selectedPelangganDiskonPersen = parseFloat(ui.item.diskon_persen) || 0; 
            currentPelangganPoin = parseInt(ui.item.poin) || 0; 
            $("#pelanggan_poin_display").text(currentPelangganPoin);
            $("#selected_pelanggan_info").show();
            $("#search_pelanggan").hide().val('');
            return false;
        }
    });

    $('#clear_pelanggan_btn').on('click', function() {
        $("#selected_pelanggan_id").val('');
        $("#selected_pelanggan_info").hide();
        selectedPelangganDiskonPersen = 0; 
        renderCart(); 
        currentPelangganPoin = 0; 
        $("#pelanggan_poin_display").text(currentPelangganPoin);
        selectedPelangganNameForStruk = null; 
        $("#search_pelanggan").show().focus();
    });

    // Tambah Pelanggan Form Submission
    $('#formTambahPelanggan').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize(); 
        console.log("Tambah Pelanggan - Form Data (serialized):", formData);
        $.ajax({
            url: "<?= site_url('/kasir/transaksi/add-pelanggan') ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.csrf_hash) updateGlobalCsrfToken(response.csrf_hash);
                if (response.success) {
                    alert(response.message);
                    $('#modalTambahPelanggan').modal('hide');
                    $('#formTambahPelanggan')[0].reset();
                    $("#pelanggan_form_error").hide();
                    if(response.pelanggan) { // Auto-select new customer
                        if (typeof response.pelanggan.diskon_persen === 'undefined') {
                            console.warn("Peringatan: 'diskon_persen' tidak ditemukan pada data pelanggan baru dari modal. Default ke 0.", response.pelanggan);
                        }
                        $("#selected_pelanggan_id").val(response.pelanggan.pelanggan_id); 
                        selectedPelangganDiskonPersen = parseFloat(response.pelanggan.diskon_persen) || 0; 
                        selectedPelangganNameForStruk = response.pelanggan.nama;
                        $("#pelanggan_nama_display").text(response.pelanggan.nama); // Hanya tampilkan nama
                        currentPelangganPoin = parseInt(response.pelanggan.poin) || 0; 
                        $("#pelanggan_poin_display").text(currentPelangganPoin);
                        $("#selected_pelanggan_info").show();
                        renderCart(); 
                        $("#search_pelanggan").hide().val('');
                    }
                } else {
                    let errorMessages = '<ul>';
                    if (response.errors && typeof response.errors === 'object') {
                        $.each(response.errors, function(key, value) {
                            errorMessages += '<li>' + esc_html(value) + '</li>';
                        });
                    } else if (response.message) {
                        errorMessages += '<li>' + esc_html(response.message) + '</li>';
                    } else {
                        errorMessages += '<li>Terjadi kesalahan yang tidak diketahui saat menambahkan member.</li>';
                    }
                    errorMessages += '</ul>';
                    $("#pelanggan_form_error").html(errorMessages).show();
                    console.warn("Gagal menambahkan member:", response);
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.csrf_hash) updateGlobalCsrfToken(xhr.responseJSON.csrf_hash);
                $("#pelanggan_form_error").html('<li>Terjadi kesalahan saat menghubungi server. Silakan coba lagi. Status: ' + xhr.status + '</li>').show();
                alert("Gagal menambahkan member. Periksa konsol untuk detail.");
                console.error(xhr.responseText);
            }
        });
    });

    // QR Code Scanner Logic
    let html5QrCode = null;
    const qrConfig = { fps: 10, qrbox: { width: 250, height: 250 }, rememberedStates: true };

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`QR Code detected: ${decodedText}`);
        if (decodedText && decodedText.trim() !== "") {
            fetchProductByBarcodeAndAddToCart(decodedText);
            
            try {
                const audio = new Audio("<?= base_url('assets/sounds/success-scan.mp3') ?>"); // Pastikan file ini ada
                audio.play().catch(e => console.error("Error playing sound:", e));
            } catch (e) {
                console.error("Error initializing audio:", e);
            }
        } else {
            console.warn("Scan resulted in empty or null decodedText.");
            alert("Hasil scan kosong atau tidak valid.");
        }
        stopScanner(); 
    }

    function onScanFailure(error) {
       
    }

    function fetchProductByBarcodeAndAddToCart(scannedBarcode) { 
        const cleanBarcode = scannedBarcode.trim(); 
        if (!cleanBarcode || cleanBarcode.length < 3) { 
            console.warn("Barcode yang di-scan kosong setelah dibersihkan.");
            $('#search_produk').val(''); 
            return; 
        }

        $('#search_produk').val(cleanBarcode); 
        console.log("Mencoba mengambil produk untuk barcode:", cleanBarcode); 
        const ajaxUrl = "<?= site_url('/kasir/transaksi/get-produk-by-barcode/') ?>" + encodeURIComponent(cleanBarcode);
        console.log("AJAX URL for barcode scan:", ajaxUrl);

        $.ajax({
            url: ajaxUrl,
            method: "GET",
            dataType: "json",
            success: function(response) {
                console.log("AJAX success response untuk barcode " + cleanBarcode + ":", JSON.stringify(response)); // Log full response
                if (response.csrf_hash) updateGlobalCsrfToken(response.csrf_hash);
                
                if (response.success && response.product && typeof response.product.nama !== 'undefined' && response.product.nama !== null && response.product.nama.trim() !== '') {
                    console.log("Produk ditemukan:", response.product);
                    addProductToCart(response.product);
                    $('#search_produk').val(response.product.nama); 
                } else {
                    let failMessage = response.message || "Produk dengan barcode tersebut tidak ditemukan atau data tidak lengkap.";
                    if (response.success && response.product && (typeof response.product.nama === 'undefined' || response.product.nama === null || response.product.nama.trim() === '')) {
                        failMessage = "Produk ditemukan, tetapi data nama produk tidak valid atau kosong.";
                        console.warn("Produk ditemukan ("+ cleanBarcode +") tetapi nama produk tidak ada, null, atau kosong:", response.product);
                    } else if (!response.success) {
                        console.warn("Produk dengan barcode " + cleanBarcode + " tidak ditemukan atau error dari server:", response.message);
                    }
                    console.warn("Info Barcode (" + cleanBarcode + "): " + failMessage, "Full response:", response);
                    alert("Info Barcode (" + cleanBarcode + "):\n" + failMessage);
                    $('#search_produk').val(cleanBarcode); 
                }
            },
            error: function(xhr) {
                console.error("AJAX error untuk barcode " + cleanBarcode + ". Status:", xhr.status, "Response Text:", xhr.responseText); // Log detail error
                if (xhr.responseJSON && xhr.responseJSON.csrf_hash) updateGlobalCsrfToken(xhr.responseJSON.csrf_hash);
                
                let userErrorMessage = "Terjadi kesalahan saat mengambil data produk.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    userErrorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText && xhr.statusText.toLowerCase() !== 'error' && xhr.statusText.toLowerCase() !== 'timeout') {
                    userErrorMessage += " Detail Teknis: " + xhr.statusText;
                } else if (xhr.status) {
                    userErrorMessage += " Kode Status HTTP: " + xhr.status;
                }
                alert("Error Barcode (" + cleanBarcode + "):\n" + userErrorMessage);

                $('#search_produk').val(''); 
            }
        });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) { 
            html5QrCode.stop().then(() => {
                $("#qr-reader").hide();
                $('#start-scan-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false);
                console.log("Barcode scanning stopped.");
            }).catch(err => {
                console.error("Failed to stop barcode scanner", err);
                $("#qr-reader").hide(); 
                $('#start-scan-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false);
            });
        } else {
             $("#qr-reader").hide();
             $('#start-scan-btn').html('<span class="glyphicon glyphicon-qrcode"></span>').prop('disabled', false);
        }
    }

    html5QrCode = new Html5Qrcode("qr-reader");

    $('#start-scan-btn').on('click', function() {
        if (html5QrCode && html5QrCode.isScanning) {
            stopScanner();
        } else {

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }
            $("#qr-reader").show();
            $(this).html('<span class="glyphicon glyphicon-stop"></span> Stop Scan').prop('disabled', false);
            
            html5QrCode.start(
                { facingMode: "environment" }, 
                qrConfig,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Tidak dapat memulai pemindaian barcode.", err);
                alert("Tidak dapat memulai kamera. Pastikan izin kamera telah diberikan dan halaman diakses melalui HTTPS jika tidak di localhost. Pemindai ini mendukung barcode 1D dan QR Code.");
                stopScanner(); 
            });
        }
    });
    // Produk Search Autocomplete
    $("#search_produk").autocomplete({
        
        source: function(request, response) { 
            $.ajax({
                url: "<?= site_url('/kasir/transaksi/search-produk') ?>",
                dataType: "json",
                data: {
                    term: request.term, 
                    [csrfName]: csrfHash 
                },
                success: function(data) {
                    console.log("Produk search success. Raw Response:", data);
                   
                    if (data && data.csrf_hash) {
                        updateGlobalCsrfToken(data.csrf_hash);
                        console.log("CSRF token updated from produk search response.");
                    }
                    response(data && data.results ? data.results : []); 
                },
                error: function(xhr, status, error) {
                    
                    if (xhr.responseJSON && xhr.responseJSON.csrf_hash) { 
                        updateGlobalCsrfToken(xhr.responseJSON.csrf_hash);
                    }
                    let errMsg = "Error saat mencari produk.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    } else if (xhr.statusText) {
                        errMsg += " Status: " + xhr.statusText;
                    }
                    alert(errMsg);
                    console.error("Autocomplete AJAX Error:", xhr.status, xhr.statusText, xhr.responseText);
                    response([]); 
                }
            });
        },
        minLength: 1,
        select: function(event, ui) {
            addProductToCart(ui.item);
            $(this).val('');
            return false;
        }
    });

    function addProductToCart(product) {
        console.log("Mencoba menambahkan produk ke keranjang:", product);
        const existingItem = cart.find(item => item.id === product.id);
        if (existingItem) {
            console.log("Produk sudah ada di keranjang, mencoba menambah jumlah.");
            if (existingItem.qty < product.stok) {
                existingItem.qty++;
                console.log("Jumlah produk ID " + product.id + " ditambah menjadi: " + existingItem.qty);
            } else {
                console.warn("Stok produk " + product.nama + " tidak mencukupi untuk ditambah.");
                alert('Stok produk ' + product.nama + ' tidak mencukupi.');
            }
        } else {
            console.log("Produk " + product.nama + " (ID: " + product.id + ", Barcode: " + product.kode_barcode + ") belum ada di keranjang, mencoba menambahkan baru.");
            if (product.stok > 0) {
                cart.push({ id: product.id, nama: product.nama, harga: parseFloat(product.harga), qty: 1, stok: parseInt(product.stok) });
                console.log("Produk " + product.nama + " berhasil ditambahkan ke keranjang.");
            } else {
                console.warn("Stok produk " + product.nama + " habis.");
                alert('Stok produk ' + product.nama + ' habis.');
            }
        }
        renderCart();
    }


    function renderCart() {
        const cartTableBody = $('#cart_table tbody');
        cartTableBody.empty();
        let subtotalKotor = 0; 
        
        // Reset and reapply uang bayar based on payment method when cart changes
        const metodePembayaran = $('#metode_pembayaran').val();
        if (metodePembayaran !== 'tunai') {
            const event = new Event('change', { bubbles: true });
            document.getElementById('metode_pembayaran').dispatchEvent(event);
        }

        if (cart.length === 0) {
            $('#cart_empty_message').show();
            $('#subtotal_display').text(formatCurrency(0));
            $('#summary_diskon_row').hide();
            $('#summary_hr_after_diskon').hide();
            $('#total_neto_display').text(formatCurrency(0));
        } else {
            $('#cart_empty_message').hide();
            cart.forEach((item, index) => {
                const subtotal = item.harga * item.qty;
                subtotalKotor += subtotal;
                cartTableBody.append(`
                    <tr>
                        <td>${item.nama}</td>
                        <td class="text-right">${formatCurrency(item.harga)}</td>
                        <td class="text-center"><input type="number" class="form-control input-sm cart-item-qty" value="${item.qty}" min="1" max="${item.stok}" data-index="${index}" style="width: 70px; margin:auto;"></td>
                        <td class="text-right">${formatCurrency(subtotal)}</td>
                        <td class="text-center"><button class="btn btn-danger btn-xs remove-item-btn" data-index="${index}"><span class="glyphicon glyphicon-trash"></span></button></td>
                    </tr>
                `);
            });
        }
        // Hitung diskon dan total neto
        $('#subtotal_display').text(formatCurrency(subtotalKotor));

        let totalDiskonNominal = 0;
        
        let totalHargaNeto = subtotalKotor;

       
        if ($("#selected_pelanggan_id").val() && subtotalKotor > 0) {
           
            totalDiskonNominal = Math.round((subtotalKotor * selectedPelangganDiskonPersen) / 100);
            totalHargaNeto = subtotalKotor - totalDiskonNominal; 

            $('#diskon_display').text(`- ${formatCurrency(totalDiskonNominal)} (${selectedPelangganDiskonPersen.toFixed(2)}%)`);
            $('#summary_diskon_row').show();
            $('#summary_hr_after_diskon').show();
        } else {
           
            $('#summary_diskon_row').hide();
            $('#summary_hr_after_diskon').hide();
        }
      
        if ($("#selected_pelanggan_id").val() && subtotalKotor > 0 && totalDiskonNominal > 0) {
            $('#diskon_final_display').text(`- ${formatCurrency(totalDiskonNominal)} (${selectedPelangganDiskonPersen.toFixed(2)}%)`);
            $('#summary_diskon_final_row').show();
        } else {
            $('#summary_diskon_final_row').hide();
        }
        $('#total_neto_display').text(formatCurrency(totalHargaNeto));

        const uangBayarInput = parseFloat($('#uang_bayar').val() || 0);
        $('#btn_proses_pembayaran').prop('disabled', cart.length === 0 || uangBayarInput < totalHargaNeto);
        calculateKembalian();
    }

    $('#cart_table').on('change input', '.cart-item-qty', function() {
        const index = $(this).data('index');
        let newQty = parseInt($(this).val());
        if (isNaN(newQty) || newQty < 1) newQty = 1;
        if (newQty > cart[index].stok) {
            newQty = cart[index].stok;
            alert('Jumlah melebihi stok yang tersedia (' + cart[index].stok + ')');
        }
        $(this).val(newQty); 
        cart[index].qty = newQty;
        renderCart();
    });

    $('#cart_table').on('click', '.remove-item-btn', function() {
        cart.splice($(this).data('index'), 1);
        renderCart();
    });

    // Formatting untuk Uang Bayar
    const uangBayarDisplay = document.getElementById('uang_bayar_display');
    const uangBayarHidden = document.getElementById('uang_bayar');

    if (uangBayarDisplay && uangBayarHidden) {
        uangBayarDisplay.addEventListener('input', function(e) {
            let value = e.target.value;
            let numericValue = value.replace(/[^0-9]/g, '');

            uangBayarHidden.value = numericValue; // Simpan nilai numerik ke input hidden

            if (numericValue.length > 0) {
                e.target.value = parseInt(numericValue, 10).toLocaleString('id-ID');
            } else {
                e.target.value = '';
            }
            // Trigger event input pada input hidden agar listener lain tetap berfungsi
            var event = new Event('input', { bubbles: true, cancelable: true });
            uangBayarHidden.dispatchEvent(event);
        });
    }

    $('#uang_bayar').on('input', function() {
        // Listener ini sekarang ada di input hidden #uang_bayar
        const subtotalKotor = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
        let totalHargaNeto = subtotalKotor;
        
        if ($("#selected_pelanggan_id").val() && subtotalKotor > 0) {
            const diskonAmount = Math.round((subtotalKotor * selectedPelangganDiskonPersen) / 100);
            totalHargaNeto = subtotalKotor - diskonAmount;
        }

        calculateKembalian();
        // Ambil nilai dari input hidden untuk perbandingan
        $('#btn_proses_pembayaran').prop('disabled', cart.length === 0 || parseFloat(uangBayarHidden.value || 0) < totalHargaNeto);
    });

    function calculateKembalian() {
        const subtotalKotor = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
        let totalHargaNeto = subtotalKotor;
        let currentTotalDiskon = 0; 

       
        if ($("#selected_pelanggan_id").val() && subtotalKotor > 0) { 
            currentTotalDiskon = Math.round((subtotalKotor * selectedPelangganDiskonPersen) / 100);
            totalHargaNeto = subtotalKotor - currentTotalDiskon;
        }

        // Ambil nilai dari input hidden untuk kalkulasi
        const uangBayar = parseFloat(uangBayarHidden.value) || 0;
        const kembalian = uangBayar - totalHargaNeto;
        $('#kembalian_display').text(formatCurrency(kembalian >= 0 ? kembalian : 0));
    }

    function formatCurrency(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    $('#btn_proses_pembayaran').on('click', function() {
        const pelangganId = $('#selected_pelanggan_id').val();
        // Ambil nilai dari input hidden untuk proses pembayaran
        const uangBayar = parseFloat(uangBayarHidden.value);
        const metodePembayaran = $('#metode_pembayaran').val(); 

        const subtotalKotor = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
        let totalDiskonNominal = 0;
        let totalHargaNeto = subtotalKotor;

     
        if ($("#selected_pelanggan_id").val() && subtotalKotor > 0) {
            totalDiskonNominal = Math.round((subtotalKotor * selectedPelangganDiskonPersen) / 100);
            totalHargaNeto = subtotalKotor - totalDiskonNominal;
        }
        
        console.log("Proses Pembayaran - Data Awal: PelangganID=" + pelangganId + ", TotalNeto=" + totalHargaNeto + ", Diskon=" + totalDiskonNominal + ", UangBayar=" + uangBayar + ", Metode=" + metodePembayaran);

        if (cart.length === 0) { alert('Keranjang belanja kosong.'); return; }
        // Fokus ke input yang terlihat jika error
        if (isNaN(uangBayar) || uangBayar < totalHargaNeto) { alert('Uang bayar tidak cukup atau tidak valid.'); $('#uang_bayar_display').focus(); return; }
        if (!metodePembayaran) { alert('Silakan pilih metode pembayaran.'); $('#metode_pembayaran').focus(); return; }

        const transactionData = {
            metode_pembayaran: metodePembayaran, 
            pelanggan_id: pelangganId || null, 
            total_harga: totalHargaNeto, 
            total_diskon: totalDiskonNominal, 
            uang_bayar: uangBayar,
            cart: cart.map(item => ({ id: item.id, nama: item.nama, qty: item.qty, harga: item.harga, kode_barcode: item.kode_barcode })), // Tambahkan 'nama' dan 'kode_barcode'
            [csrfName]: csrfHash 
        };
        console.log("Proses Pembayaran - Data yang akan dikirim (transactionData):", JSON.stringify(transactionData));
        
        $(this).prop('disabled', true).html('<span class="glyphicon glyphicon-refresh spinning"></span> Memproses...');

        $.ajax({
            url: "<?= site_url('/kasir/transaksi/proses-pembayaran') ?>",
            method: "POST",
            data: transactionData, 
            dataType: "json",
            success: function(response) {
                console.log("Proses Pembayaran - AJAX Success Response:", JSON.stringify(response));
                if (response.csrf_hash) updateGlobalCsrfToken(response.csrf_hash);
                if (response.success) {
                    
                    $('#detail_kode_transaksi_modal').text(response.transaksi_id);
                    $('#detail_tanggal_transaksi_modal').text(new Date().toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })); 
                    
                    const pelangganNama = $("#pelanggan_nama_display").text() || 'Umum';
                    $('#detail_nama_pelanggan_modal').text(selectedPelangganNameForStruk || 'Umum'); 
                    $('#detail_nama_kasir_modal').text('<?= esc(session()->get('nama_karyawan') ?? session()->get('nama') ?? 'Kasir') ?>');
                    
                    const metodePembayaranValue = transactionData.metode_pembayaran; // "tunai", "debit", etc.
                    let metodePembayaranFormatted = 'N/A';
                    if (metodePembayaranValue) {
                        metodePembayaranFormatted = metodePembayaranValue.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    }
                    $('#detail_metode_pembayaran_modal').text(metodePembayaranFormatted);
                    
                    // Menampilkan subtotal, diskon, dan total belanja (neto) di modal
                    const subtotalKotorModal = transactionData.total_harga + transactionData.total_diskon;
                    if (transactionData.total_diskon > 0) {
                        $('#detail_subtotal_modal_row').show();
                        $('#detail_subtotal_modal').text(formatCurrency(subtotalKotorModal));
                        $('#detail_diskon_modal_row').show();
                        $('#detail_diskon_modal').text(`- ${formatCurrency(transactionData.total_diskon)} (${selectedPelangganDiskonPersen.toFixed(2)}%)`);
                    } else {
                        $('#detail_subtotal_modal_row').hide();
                        $('#detail_diskon_modal_row').hide();
                    }
                    $('#detail_total_belanja_modal').text(formatCurrency(transactionData.total_harga));

                    $('#detail_uang_bayar_modal').text(formatCurrency(transactionData.uang_bayar));
                    // Tampilkan poin diperoleh di modal
                    if (response.poin_diperoleh && response.poin_diperoleh > 0) {
                        $('#detail_poin_diperoleh_modal').text(response.poin_diperoleh + ' Poin');
                        $('#detail_poin_diperoleh_modal_row').show();
                    } else {
                        $('#detail_poin_diperoleh_modal_row').hide();
                    }
                    $('#detail_kembalian_modal').text(formatCurrency(response.kembalian));

                    // Simpan semua detail transaksi yang berhasil untuk struk
                    lastSuccessfulTransactionDetails = {
                        kode_transaksi: response.transaksi_id,
                        tanggal_transaksi: new Date().toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }),
                        nama_pelanggan: selectedPelangganNameForStruk || 'Umum',
                        nama_kasir: '<?= esc(session()->get('nama_karyawan') ?? session()->get('nama') ?? 'Kasir') ?>',
                        metode_pembayaran: metodePembayaranFormatted, 
                        items: JSON.parse(JSON.stringify(transactionData.cart)), 
                        sub_total_numeric: transactionData.total_harga + transactionData.total_diskon, 
                        total_diskon_numeric: transactionData.total_diskon,
                        diskon_persen_pelanggan: selectedPelangganDiskonPersen, 
                        total_harga_neto_numeric: transactionData.total_harga, 
                        uang_bayar_numeric: transactionData.uang_bayar,
                        kembalian_numeric: response.kembalian,
                        poin_diperoleh_transaksi: response.poin_diperoleh || 0, 
                        total_poin_pelanggan_setelah_transaksi: (currentPelangganPoin + (response.poin_diperoleh || 0)) 
                    };
                    console.log("Data untuk struk disimpan:", lastSuccessfulTransactionDetails);

                    $('#modalPembayaranBerhasil').modal('show'); 
                   
                } else {
                    let errMsg = 'Gagal memproses pembayaran: ';
                    if (response.message) {
                        errMsg += response.message;
                    } else {
                        errMsg += "Tidak ada pesan error spesifik dari server.";
                    }
                   
                    if (response.errors && typeof response.errors === 'object' && Object.keys(response.errors).length > 0) {
                        errMsg += "\n\nDetail Kesalahan Validasi:";
                        for (const field in response.errors) {
                            errMsg += `\n- ${field}: ${response.errors[field]}`;
                        }
                    } else if (response.errors && typeof response.errors === 'string') {
                        errMsg += `\n\nInfo Tambahan: ${response.errors}`;
                    }
                    alert(errMsg);
                    console.warn("Pembayaran Gagal (Server Response):", response);
                }
            },
            error: function(xhr, status, error) { 
                console.error("Proses Pembayaran - AJAX Error. Status: " + status + ", Error: " + error, "XHR Object:", xhr);
                if (xhr.responseJSON && xhr.responseJSON.csrf_hash) { 
                    updateGlobalCsrfToken(xhr.responseJSON.csrf_hash);
                }

                let ajaxErrMsg = "Terjadi kesalahan fatal saat menghubungi server untuk memproses pembayaran.";
                console.error("AJAX Error (Proses Pembayaran):", {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    responseJSON: xhr.responseJSON,
                    errorThrown: error
                });

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    ajaxErrMsg += "\n\nPesan dari Server: " + xhr.responseJSON.message;
                     if (xhr.responseJSON.errors && typeof xhr.responseJSON.errors === 'object' && Object.keys(xhr.responseJSON.errors).length > 0) {
                        ajaxErrMsg += "\n\nDetail Kesalahan Validasi:";
                        for (const field in xhr.responseJSON.errors) {
                            ajaxErrMsg += `\n- ${field}: ${xhr.responseJSON.errors[field]}`;
                        }
                    } else if (xhr.responseJSON.errors && typeof xhr.responseJSON.errors === 'string') {
                        ajaxErrMsg += `\n\nInfo Tambahan: ${xhr.responseJSON.errors}`;
                    }
                } else if (xhr.responseText) {
                    if (xhr.responseText.trim().toLowerCase().startsWith("<!doctype html") || xhr.responseText.trim().toLowerCase().startsWith("<html")) {
                        ajaxErrMsg += "\n\nServer mengembalikan halaman HTML, yang mengindikasikan adanya error di backend (misalnya error PHP, halaman tidak ditemukan, atau error server). Silakan periksa tab 'Network' di Developer Tools browser Anda untuk melihat respons lengkap, dan periksa log error server.";
                    } else {
                        ajaxErrMsg += "\n\nRespons mentah dari server (mungkin tidak lengkap atau bukan JSON):\n" + xhr.responseText.substring(0, 300) + (xhr.responseText.length > 300 ? "..." : "");
                    }
                } else if (status === 'timeout') {
                    ajaxErrMsg += "\n\nPermintaan ke server memakan waktu terlalu lama (timeout). Periksa koneksi internet Anda dan status server.";
                } else if (status === 'parsererror') {
                    ajaxErrMsg += "\n\nGagal mem-parsing respons dari server. Server mungkin tidak mengirimkan JSON yang valid.";
                } else if (xhr.status === 0) {
                    ajaxErrMsg += "\n\nTidak dapat terhubung ke server. Periksa koneksi internet Anda atau pastikan server berjalan.";
                } else if (error) {
                    ajaxErrMsg += "\n\nError: " + error;
                }
                
                if (xhr.status && xhr.status !== 0) {
                     ajaxErrMsg += `\n\nKode Status HTTP: ${xhr.status} (${xhr.statusText})`;
                }

                alert(ajaxErrMsg);
            },
            complete: function() {
                 $('#btn_proses_pembayaran').html('<span class="glyphicon glyphicon-ok-circle"></span> Proses Pembayaran');
                
                 const currentTotalNeto = parseFloat($('#total_neto_display').text().replace(/[Rp. ]/g, '').replace(/,/g, '.')) || 0;
                 const currentUangBayar = parseFloat($('#uang_bayar').val()) || 0;
                 $('#btn_proses_pembayaran').prop('disabled', cart.length === 0 || currentUangBayar < currentTotalNeto);
            }
        });
    });

    $('#btn_reset_transaksi').on('click', function(){
        if(confirm('Apakah Anda yakin ingin mereset transaksi ini? Semua item di keranjang akan dihapus.')){
            resetTransaksiForm();
        }
    });

    function resetTransaksiForm() {
        cart = [];
        renderCart();
        $('#search_produk').val('').focus();
        $('#search_pelanggan').val('').show();
        $('#selected_pelanggan_id').val('');
        $('#selected_pelanggan_info').hide();
        $('#uang_bayar_display').val(''); // Kosongkan juga field display
        $('#uang_bayar').val('');
        $('#metode_pembayaran').val('tunai'); 
        currentPelangganPoin = 0; 
        $("#pelanggan_poin_display").text(currentPelangganPoin);
        selectedPelangganDiskonPersen = 0; 
        renderCart(); 
        $('#btn_proses_pembayaran').prop('disabled', true);
    }

    // Handler untuk tombol di modal sukses
    $('#btnTransaksiBaruDariModal').on('click', function() {
        $('#modalPembayaranBerhasil').modal('hide');
        resetTransaksiForm();
    });

    $('#btnCetakStrukModal').on('click', function() {
        const strukData = lastSuccessfulTransactionDetails;

        if (!strukData) {
            alert('Data transaksi terakhir tidak ditemukan untuk dicetak.');
            return;
        }

        let strukContent = `<pre style="font-family: 'Courier New', Courier, monospace; font-size: 12px; line-height: 1.4;">`;
        strukContent += `============================================\n`;
        strukContent += `            Toko Dolog Sihite 3            \n`; 
        strukContent += `   Jl. Danau Pakis 1 No.C2, RW.1, Kebalen  \n`; 
        strukContent += `      Kec. Babelan, Kabupaten Bekasi      \n`; 
        strukContent += `            Jawa Barat 17610             \n`;  
        strukContent += `============================================\n`;
        strukContent += `No. Transaksi : ${strukData.kode_transaksi.padEnd(25)}\n`;
        strukContent += `Tanggal       : ${strukData.tanggal_transaksi}\n`;
        strukContent += `Member        : ${strukData.nama_pelanggan.padEnd(25)}\n`;
        strukContent += `Kasir         : ${strukData.nama_kasir.padEnd(25)}\n`;
        strukContent += `Metode Bayar  : ${strukData.metode_pembayaran.padEnd(25)}\n`;
        strukContent += `--------------------------------------------\n`;
        strukContent += `Detail Pembelian:\n`;


        if (strukData.items && strukData.items.length > 0) {
            strukData.items.forEach(item => {
                const itemSubtotal = item.qty * item.harga;
                const namaProdukDisplay = item.nama.length > 20 ? item.nama.substring(0, 17) + '...' : item.nama;
                
                strukContent += `- ${namaProdukDisplay.padEnd(20)} \n`;
                strukContent += `  (${item.qty} x ${formatCurrencySimple(item.harga).padStart(10)}) = ${formatCurrencySimple(itemSubtotal).padStart(10)}\n`;
            });
        } else {
            strukContent += `  (Tidak ada item transaksi)\n`;
        }

        strukContent += `--------------------------------------------\n`;
        strukContent += `Subtotal      : ${formatCurrencySimple(strukData.sub_total_numeric).padStart(25)}\n`;
        if (strukData.total_diskon_numeric > 0) {
            let diskonPersenText = "";
            if (strukData.diskon_persen_pelanggan > 0) {
                diskonPersenText = ` (${strukData.diskon_persen_pelanggan.toFixed(2)}%)`;
            }
            strukContent += `Diskon${diskonPersenText}: -${formatCurrencySimple(strukData.total_diskon_numeric).padStart(29 - diskonPersenText.length)}\n`;
        }
        strukContent += `Total Bayar   : ${formatCurrencySimple(strukData.total_harga_neto_numeric).padStart(25)}\n`;
        strukContent += `Uang Bayar    : ${formatCurrencySimple(strukData.uang_bayar_numeric).padStart(25)}\n`;
        strukContent += `Kembalian     : ${formatCurrencySimple(strukData.kembalian_numeric).padStart(25)}\n`;
        if (strukData.poin_diperoleh_transaksi > 0) {
            strukContent += `Poin Diperoleh: ${strukData.poin_diperoleh_transaksi.toString().padStart(25)}\n`;
            strukContent += `Total Poin    : ${strukData.total_poin_pelanggan_setelah_transaksi.toString().padStart(25)}\n`;
        }
        strukContent += `============================================\n`;
        strukContent += `         TERIMA KASIH TELAH BERBELANJA        \n`;
        strukContent += `            www.dolog-sihite-3.com           \n`; 
        strukContent += `============================================\n`;
        strukContent += `</pre>`;

        const strukWindow = window.open('', 'Cetak Struk', 'height=600,width=400');
        strukWindow.document.write('<html><head><title>Struk Pembayaran</title>');
        strukWindow.document.write('<style> body { font-family: "Courier New", Courier, monospace; margin: 0; padding: 10px; } pre { white-space: pre-wrap; word-wrap: break-word; font-size: 12px; line-height: 1.4;} @media print { body { margin: 0; } .no-print { display: none; } } </style>');
        strukWindow.document.write('</head><body>');
        strukWindow.document.write(strukContent);
        strukWindow.document.write('<button class="no-print" onclick="window.print()" style="margin-top:15px; padding:8px 15px; background-color:#4CAF50; color:white; border:none; border-radius:4px; cursor:pointer;">Cetak</button>');
        strukWindow.document.write('<button class="no-print" onclick="window.close()" style="margin-left:10px; padding:8px 15px; background-color:#f44336; color:white; border:none; border-radius:4px; cursor:pointer;">Tutup</button>');
        strukWindow.document.write('</body></html>');
        strukWindow.document.close(); 
        strukWindow.focus(); 
        
    });

    
    $('#modalPembayaranBerhasil').on('hidden.bs.modal', function () {
        
    });

    renderCart(); 
    $('#search_produk').focus(); 

   
    function formatCurrencySimple(amount) { 
        return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    $('#metode_pembayaran').on('change', function() {
        const metodePembayaran = $(this).val();
        const totalHargaNeto = parseFloat($('#total_neto_display').text().replace(/[^0-9]/g, ''));
        
        if (metodePembayaran !== 'tunai') {
            // Untuk pembayaran non-tunai, set uang bayar sama dengan total
            uangBayarDisplay.value = totalHargaNeto.toLocaleString('id-ID');
            uangBayarHidden.value = totalHargaNeto;
            $('#uang_bayar_display').prop('readonly', true);
            var event = new Event('input', { bubbles: true, cancelable: true });
            uangBayarHidden.dispatchEvent(event);
        } else {
            // Untuk pembayaran tunai, biarkan input manual dan reset nilai
            $('#uang_bayar_display').prop('readonly', false);
            uangBayarDisplay.value = '';
            uangBayarHidden.value = '';
            var event = new Event('input', { bubbles: true, cancelable: true });
            uangBayarHidden.dispatchEvent(event);
        }
    });
});
</script>