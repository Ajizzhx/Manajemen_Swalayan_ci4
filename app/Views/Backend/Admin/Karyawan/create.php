<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<!-- Bagian Konten Utama -->
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="<?= site_url(session()->get('role') === 'pemilik' ? 'admin/owner-area/dashboard' : session()->get('role') . '/dashboard') ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?= site_url('admin/owner-area/karyawan') ?>">Kelola Karyawan</a></li>
            <li class="active">Tambah Karyawan</li>
        </ol>
    </div><!--/.row-->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?= esc($title) ?></h1>
		</div>
	</div><!--/.row-->
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
                Form Tambah Karyawan
                <a href="<?= site_url('admin/owner-area/karyawan') ?>" class="btn btn-default btn-xs pull-right">Kembali</a>
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

                <?= form_open('admin/owner-area/karyawan/store') ?>
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="nama">Nama Karyawan</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="input-group-addon" style="cursor: pointer;" onclick="togglePasswordVisibility('password', this)">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </span>
                        </div>
                        <small class="form-text text-muted">Minimal 6 karakter.</small>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="kasir" <?= old('role') == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= site_url('admin/owner-area/karyawan') ?>" class="btn btn-default">Batal</a>
                <?= form_close() ?>
			</div>
		</div>
	</div>

<script>
function togglePasswordVisibility(inputId, toggleIconElement) {
    const passwordInput = document.getElementById(inputId);
    const icon = toggleIconElement.querySelector('i'); // Assuming the icon is an <i> tag

    if (passwordInput && icon) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('glyphicon-eye-open');
            icon.classList.add('glyphicon-eye-close');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('glyphicon-eye-close');
            icon.classList.add('glyphicon-eye-open');
        }
    }
}
</script>
<?= $this->include('Backend/Template/footer') ?>