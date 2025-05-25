<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <?php
            $user_role_for_breadcrumb_profile = session()->get('role');
            $dashboard_link_for_breadcrumb_profile = '';
            if ($user_role_for_breadcrumb_profile === 'admin' || $user_role_for_breadcrumb_profile === 'pemilik') {
                $dashboard_link_for_breadcrumb_profile = site_url('admin/dashboard');
            } elseif ($user_role_for_breadcrumb_profile === 'kasir') {
                $dashboard_link_for_breadcrumb_profile = site_url('kasir/dashboard');
            } else {
                $dashboard_link_for_breadcrumb_profile = site_url('/'); // Fallback
            }
            ?>
            <li><a href="<?= $dashboard_link_for_breadcrumb_profile ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Profil Saya</li>
        </ol>
    </div><!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title ?? 'Profil Saya') ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Informasi Profil
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <?php if ($validation && $validation->getErrors()): ?>
                        <div class="alert alert-danger">
                            <strong>Gagal menyimpan data:</strong>
                            <ul>
                            <?php foreach ($validation->getErrors() as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($karyawan) && is_array($karyawan)): ?>
                        <?= form_open(site_url(session()->get('role') . '/profile/update')) ?>
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= esc(old('nama', $karyawan['nama'] ?? '')) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email', $karyawan['email'] ?? '')) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <p class="form-control-static"><?= esc(ucfirst($karyawan['role'] ?? 'N/A')) ?></p>
                            </div>
                            <hr>
                            <p class="text-muted"><em>Kosongkan field password jika tidak ingin mengubahnya.</em></p>
                            <div class="form-group">
                                <label for="password-profile">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password-profile" name="password" placeholder="Minimal 6 karakter">
                                    <span class="input-group-addon" style="cursor: pointer;" onclick="togglePasswordVisibility('password-profile', this)">
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password-profile">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password-profile" name="confirm_password" placeholder="Ulangi password baru">
                                    <span class="input-group-addon" style="cursor: pointer;" onclick="togglePasswordVisibility('confirm_password-profile', this)">
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Profil</button>
                            <a href="<?= $dashboard_link_for_breadcrumb_profile ?>" class="btn btn-default">Kembali ke Dashboard</a>
                        <?= form_close() ?>
                    <?php else: ?>
                        <div class="alert alert-warning">Data profil tidak dapat dimuat.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div><!--/.row-->
</div>  <!--/.main-->

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
