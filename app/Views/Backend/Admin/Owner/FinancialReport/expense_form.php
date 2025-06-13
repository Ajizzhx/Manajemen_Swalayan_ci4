<!-- c:\xampp\htdocs\swalayan_ci4\app\Views\Backend\Admin\Owner\FinancialReport\expense_form.php -->
<div class="panel panel-default">
    <div class="panel-heading" style="margin-bottom: 10px;">Pengeluaran Baru untuk Laporan</div>
    <div class="panel-body">
        <?php if (session()->getFlashdata('error_expense')): ?>
            <div class="alert alert-danger">
                <strong>Gagal menyimpan pengeluaran:</strong>
                <ul>
                    <?php foreach (session()->getFlashdata('error_expense') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?= site_url('admin/owner-area/financial-reports/save-expense') ?>" method="post">
            <?= csrf_field() ?>
            <!-- Hidden fields untuk membawa filter tanggal saat redirect -->
            <input type="hidden" name="report_start_date" value="<?= esc($filter['start_date'] ?? date('Y-m-01')) ?>">
            <input type="hidden" name="report_end_date" value="<?= esc($filter['end_date'] ?? date('Y-m-t')) ?>">

            <div class="form-group" style="margin-top: 5px;">
                <label for="expense_tanggal">Tanggal Pengeluaran:</label>
                <input type="date" class="form-control input-sm" id="expense_tanggal" name="tanggal" value="<?= old('tanggal', date('Y-m-d')) ?>" required>
                <small>Pastikan tanggal ini masuk dalam periode laporan yang sedang dilihat.</small>
            </div>
            <div class="form-group">
                <label for="expense_kategori">Kategori Pengeluaran:</label>
                <select class="form-control input-sm" id="expense_kategori" name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Gaji Karyawan" <?= old('kategori') == 'Gaji Karyawan' ? 'selected' : '' ?>>Gaji Karyawan</option>
                    <option value="Modal Produk" <?= old('kategori') == 'Modal Produk' ? 'selected' : '' ?>>Modal Pembelian Produk</option>
                    <option value="Biaya Operasional" <?= old('kategori') == 'Biaya Operasional' ? 'selected' : '' ?>>Biaya Operasional (Listrik, Air, dll)</option>
                    <option value="Sewa Tempat" <?= old('kategori') == 'Sewa Tempat' ? 'selected' : '' ?>>Sewa Tempat</option>
                    <option value="Pemasaran" <?= old('kategori') == 'Pemasaran' ? 'selected' : '' ?>>Pemasaran</option>
                    <option value="Lain-lain" <?= old('kategori') == 'Lain-lain' ? 'selected' : '' ?>>Pengeluaran Lain-lain</option>
                </select>
            </div>
            <div class="form-group">
                <label for="expense_deskripsi">Deskripsi (Opsional):</label>
                <textarea class="form-control input-sm" id="expense_deskripsi" name="deskripsi" rows="2"><?= old('deskripsi') ?></textarea>
            </div>
            <div class="form-group">
                <label for="expense_jumlah">Jumlah (IDR):</label>
                <input type="text" class="form-control input-sm" id="expense_jumlah_display" placeholder="Masukkan jumlah pengeluaran" value="<?= old('jumlah') ? number_format(old('jumlah'), 0, ',', '.') : '' ?>" required>
                <input type="hidden" id="expense_jumlah" name="jumlah" value="<?= old('jumlah') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Simpan Pengeluaran</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahDisplay = document.getElementById('expense_jumlah_display');
    const jumlahHidden = document.getElementById('expense_jumlah');

    if (jumlahDisplay && jumlahHidden) {
        jumlahDisplay.addEventListener('input', function(e) {
            let value = e.target.value;
            
            let numericValue = value.replace(/[^0-9]/g, '');

           
            jumlahHidden.value = numericValue;

            
            if (numericValue.length > 0) {
                e.target.value = parseInt(numericValue, 10).toLocaleString('id-ID');
            } else {
                e.target.value = '';
            }
        });
    }
});
</script>