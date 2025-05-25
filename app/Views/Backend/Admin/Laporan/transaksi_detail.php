<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <?php
            $user_role_for_breadcrumb_lpr_detail = session()->get('role');
            $dashboard_link_for_breadcrumb_lpr_detail = '';
            if ($user_role_for_breadcrumb_lpr_detail === 'admin' || $user_role_for_breadcrumb_lpr_detail === 'pemilik') {
                $dashboard_link_for_breadcrumb_lpr_detail = site_url('admin/dashboard');
            } elseif ($user_role_for_breadcrumb_lpr_detail === 'kasir') {
                $dashboard_link_for_breadcrumb_lpr_detail = site_url('kasir/dashboard');
            } else {
                $dashboard_link_for_breadcrumb_lpr_detail = site_url('/');
            }
            ?>
            <li><a href="<?= $dashboard_link_for_breadcrumb_lpr_detail ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/laporan/transaksi') ?>">Laporan & Analisis</a></li>
            <li><a href="<?= site_url('admin/laporan/transaksi') ?>">Riwayat Transaksi</a></li>
            <li class="active">Detail Transaksi</li>
        </ol>
    </div><!--/.row-->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title) ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Informasi Transaksi
                    <button type="button" class="btn btn-info btn-xs pull-right" id="btnCetakStrukDetailAdmin" style="margin-left: 10px;">
                        <span class="glyphicon glyphicon-print"></span> Cetak Struk
                    </button>
                    <a href="<?= site_url('admin/laporan/transaksi') ?>" class="btn btn-default btn-xs pull-right">Kembali ke Riwayat</a>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th style="width:30%;">ID/Kode Transaksi</th>
                                    <td>: <?= esc($transaksi['transaksi_id']) ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                <td>: <?php
                                       
                                        $bulan_indo = array(
                                            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                        );
                                        $timestamp = strtotime($transaksi['created_at']);
                                        $tanggal_formatted = date('d', $timestamp) . ' ' . $bulan_indo[(int)date('n', $timestamp)] . ' ' . date('Y, H:i:s', $timestamp);
                                        echo esc($tanggal_formatted);
                                    ?></td>
                                </tr>
                                <tr>
                                    <th>Member</th>
                                    <td>: <?= esc($transaksi['nama_pelanggan'] ?: 'Umum') ?></td>
                                </tr>
                                <tr>
                                    <th>Kasir</th>
                                    <td>: <?= esc($transaksi['nama_kasir'] ?: 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                             <table class="table">
                                 <tr>
                                    <th style="width:30%;">Subtotal</th>
                                    <td>: <?= esc(number_to_currency( (float)($transaksi['total_harga'] ?? 0) + (float)($transaksi['total_diskon'] ?? 0) , 'IDR', 'id_ID', 0)) ?></td>
                                </tr>
                                <?php if (!empty($transaksi['total_diskon']) && (float)$transaksi['total_diskon'] > 0): ?>
                                <tr>
                                    <th style="width:30%;">Diskon Member</th>
                                    <td>: - <?= esc(number_to_currency($transaksi['total_diskon'], 'IDR', 'id_ID', 0)) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th style="width:30%;">Total Belanja</th>
                                    <td>: <?= esc(number_to_currency($transaksi['total_harga'], 'IDR', 'id_ID', 0)) ?></td>
                                </tr>
                                <tr>
                                    <th>Uang Bayar</th>
                                    <td>: <?= esc(number_to_currency($transaksi['uang_bayar'], 'IDR', 'id_ID', 0)) ?></td>
                                </tr>
                                <tr>
                                    <th>Kembalian</th>
                                    <td>: <?= esc(number_to_currency($transaksi['kembalian'], 'IDR', 'id_ID', 0)) ?></td>
                                </tr>
                                <tr>
                                    <th>Metode Pembayaran</th>
                                    <td>: <?= esc(ucwords(str_replace('_', ' ', $transaksi['metode_pembayaran'] ?? 'N/A'))) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>
                    <h4>Item Transaksi:</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barcode</th>
                                <th>Nama Produk</th>
                                <th class="text-right">Harga Saat Itu</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($detail_items as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($item['kode_barcode']) ?></td>
                                <td><?= esc($item['nama_produk']) ?></td>
                                <td class="text-right"><?= esc(number_to_currency($item['harga_saat_itu'], 'IDR', 'id_ID', 0)) ?></td>
                                <td class="text-center"><?= esc($item['jumlah']) ?></td>
                                <td class="text-right"><?= esc(number_to_currency($item['sub_total'], 'IDR', 'id_ID', 0)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

    const btnCetakStruk = document.getElementById('btnCetakStrukDetailAdmin');
    if (btnCetakStruk) {
        btnCetakStruk.addEventListener('click', function() {
            <?php
           
            $struk_data_admin = [
                'kode_transaksi' => $transaksi['transaksi_id'], 
                
                'tanggal_transaksi' => isset($tanggal_formatted) ? $tanggal_formatted : date('d M Y, H:i:s', strtotime($transaksi['created_at'])),
                'nama_pelanggan' => $transaksi['nama_pelanggan'] ?? 'Umum',
                'nama_kasir' => $transaksi['nama_kasir'] ?? 'N/A',
                'metode_pembayaran' => ucwords(str_replace('_', ' ', $transaksi['metode_pembayaran'] ?? 'N/A')),
                // Data untuk perhitungan di struk
                'sub_total_numeric' => (float)($transaksi['total_harga'] ?? 0) + (float)($transaksi['total_diskon'] ?? 0), 
                'total_diskon_numeric' => (float)($transaksi['total_diskon'] ?? 0),
                'total_harga_neto_numeric' => (float)($transaksi['total_harga'] ?? 0), 
                'uang_bayar_numeric' => (float)$transaksi['uang_bayar'],
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
            const strukData = <?= json_encode($struk_data_admin) ?>;

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