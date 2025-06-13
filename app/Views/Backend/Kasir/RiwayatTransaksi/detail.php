<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('kasir/riwayat-transaksi') ?>">Riwayat Transaksi</a></li>
            <li class="active">Detail Transaksi</li>
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
                    Informasi Transaksi
                    <button type="button" class="btn btn-info btn-xs pull-right" id="btnCetakStrukDetail" style="margin-left: 10px;">
                        <span class="glyphicon glyphicon-print"></span> Cetak Struk
                    </button>
                    <a href="<?= site_url('kasir/riwayat-transaksi') ?>" class="btn btn-primary btn-xs pull-right"><i class="fa fa-arrow-left"></i> Kembali ke Riwayat</a>
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <?php if (!empty($transaksi)): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th style="width: 30%;">ID Transaksi</th>
                                        <td>: <?= esc($transaksi['transaksi_id']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>: <?php
                                                // Daftar nama bulan dalam bahasa Indonesia
                                                $bulan_indo = array(
                                                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                                );
                                                $timestamp = strtotime($transaksi['created_at']);
                                                $tanggal_formatted = date('d', $timestamp) . ' ' . $bulan_indo[(int)date('n', $timestamp)] . ' ' . date('Y, H:i', $timestamp);
                                                echo esc($tanggal_formatted);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <th>Member</th>
                                        <td>: <?= esc($transaksi['nama_pelanggan'] ?? 'Umum') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Kasir</th>
                                        <td>: <?= esc($transaksi['nama_kasir'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php if (isset($transaksi['status_penghapusan']) && $transaksi['status_penghapusan'] === 'rejected' && !empty($transaksi['alasan_penolakan_owner'])): ?>
                                <tr>
                                    <th class="text-danger" style="vertical-align: top;">Alasan Ditolak</th>
                                    <td style="vertical-align: top;">: <span class="text-danger"><em><?= nl2br(esc($transaksi['alasan_penolakan_owner'])) ?></em></span></td>
                                </tr>
                                <?php endif; ?>


                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th style="width: 30%;">Subtotal</th>
                                        <td style="text-align: right;">: Rp <?= esc(number_format( (float)($transaksi['total_harga'] ?? 0) + (float)($transaksi['total_diskon'] ?? 0) , 0, ',', '.')) ?></td>
                                    </tr>
                                    <?php if (!empty($transaksi['total_diskon']) && (float)$transaksi['total_diskon'] > 0): ?>
                                    <tr>
                                        <th style="width: 30%;">Diskon Member</th>
                                        <td style="text-align: right;">: - Rp <?= esc(number_format($transaksi['total_diskon'], 0, ',', '.')) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th style="width: 30%;">Total Belanja</th>
                                        <td style="text-align: right;">: Rp <?= esc(number_format($transaksi['total_harga'], 0, ',', '.')) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Uang Bayar</th>
                                        <td style="text-align: right;">: Rp <?= esc(number_format($transaksi['uang_bayar'], 0, ',', '.')) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Kembalian</th>
                                        <td style="text-align: right;">: Rp <?= esc(number_format($transaksi['total_harga'], 0, ',', '.')) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Uang Bayar</th>
                                        <td style="text-align: right;">: Rp <?= esc(number_format($transaksi['uang_bayar'], 0, ',', '.')) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Kembalian</th>
                                        <td style="text-align: right;">: Rp <?= esc(number_format($transaksi['kembalian'], 0, ',', '.')) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Metode Pembayaran</th>
                                        <td style="text-align: right;">: <?= esc(ucwords(str_replace('_', ' ', $transaksi['metode_pembayaran'] ?? 'N/A'))) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h4>Item Transaksi:</h4>
                        <?php if (!empty($detail_items)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Kode Barcode</th>
                                            <th>Nama Produk</th>
                                            <th style="text-align: right;">Harga Satuan (Rp)</th>
                                            <th style="text-align: center;">Jumlah</th>
                                            <th style="text-align: right;">Subtotal (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no_item = 1; foreach ($detail_items as $item): ?>
                                        <tr>
                                            <td><?= $no_item++ ?></td>
                                            <td><?= esc($item['kode_barcode']) ?></td>
                                            <td><?= esc($item['nama_produk']) ?></td>
                                            <td style="text-align: right;"><?= esc(number_format($item['harga_saat_itu'], 0, ',', '.')) ?></td>
                                            <td style="text-align: center;"><?= esc($item['jumlah']) ?></td>
                                            <td style="text-align: right;"><?= esc(number_format($item['sub_total'], 0, ',', '.')) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>Tidak ada item detail untuk transaksi ini.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Detail transaksi tidak ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div><!--/.row-->
</div>  <!--/.main-->

<?= $this->include('Backend/Template/footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    function formatCurrencySimple(amount) { 
        return 'Rp ' + Number(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    const btnCetakStruk = document.getElementById('btnCetakStrukDetail');
    if (btnCetakStruk) {
        btnCetakStruk.addEventListener('click', function() {
            <?php
            
            $struk_data = [
                'kode_transaksi' => $transaksi['transaksi_id'], // Gunakan transaksi_id
               
                'tanggal_transaksi' => isset($tanggal_formatted) ? $tanggal_formatted : date('d M Y, H:i', strtotime($transaksi['created_at'])),
                'nama_pelanggan' => $transaksi['nama_pelanggan'] ?? 'Umum',
                'nama_kasir' => $transaksi['nama_kasir'] ?? 'N/A',
                'metode_pembayaran' => ucwords(str_replace('_', ' ', $transaksi['metode_pembayaran'] ?? 'N/A')),
                // Data untuk perhitungan di struk
                'sub_total_numeric' => (float)($transaksi['total_harga'] ?? 0) + (float)($transaksi['total_diskon'] ?? 0), 
                'total_diskon_numeric' => (float)($transaksi['total_diskon'] ?? 0),
                'total_harga_neto_numeric' => (float)($transaksi['total_harga'] ?? 0), 
                'uang_bayar_numeric' => (float)($transaksi['uang_bayar'] ?? 0),
                'diskon_persen_pelanggan' => (float)($transaksi['diskon_pelanggan_saat_transaksi'] ?? 0),
                'kembalian_numeric' => (float)$transaksi['kembalian'],
                'items' => array_map(function($item) {
                    return [
                        'nama' => $item['nama_produk'],
                        'qty' => (int)$item['jumlah'],
                        'harga' => (float)$item['harga_saat_itu'],
                    ];
                }, $detail_items ?? [])
            ];
            ?>
            const strukData = <?= json_encode($struk_data) ?>;

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

            let calculatedTotal = 0;
            if (strukData.items && strukData.items.length > 0) {
                strukData.items.forEach(item => {
                    const itemSubtotal = item.qty * item.harga;
                    calculatedTotal += itemSubtotal;                    
                    const namaProduk = item.nama || 'Produk Tidak Dikenal'; 
                    const namaProdukDisplay = namaProduk.length > 20 ? namaProduk.substring(0, 17) + '...' : namaProduk;
                    
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
    }
});
</script>