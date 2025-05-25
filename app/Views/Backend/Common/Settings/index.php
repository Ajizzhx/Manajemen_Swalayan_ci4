<?= $this->include('Backend/Template/header') ?>
<?= $this->include('Backend/Template/sidebar') ?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <?php
            $user_role_for_breadcrumb_settings = session()->get('role');
            $dashboard_link_for_breadcrumb_settings = '';
            if ($user_role_for_breadcrumb_settings === 'admin' || $user_role_for_breadcrumb_settings === 'pemilik') {
                $dashboard_link_for_breadcrumb_settings = site_url('admin/dashboard');
            } elseif ($user_role_for_breadcrumb_settings === 'kasir') {
                $dashboard_link_for_breadcrumb_settings = site_url('kasir/dashboard');
            } else {
                $dashboard_link_for_breadcrumb_settings = site_url('/'); // Fallback
            }
            ?>
            <li><a href="<?= $dashboard_link_for_breadcrumb_settings ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Pengaturan</li>
        </ol>
    </div><!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><?= esc($title ?? 'Pengaturan') ?></h1>
        </div>
    </div><!--/.row-->

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Pengaturan Akun & Aplikasi
                </div>
                <div class="panel-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <p>Halaman ini adalah tempat untuk mengelola pengaturan aplikasi dan akun Anda.</p>
                    <p>Saat ini, halaman ini masih dalam pengembangan. Beberapa pengaturan yang mungkin tersedia di masa mendatang meliputi:</p>
                    <ul>
                        <li>Pengaturan Akun (seperti mengubah password - saat ini tersedia di halaman <a href="<?= site_url(session()->get('role') . '/profile') ?>">Profil</a>)</li>
                        <?php if (session()->get('role') === 'admin' || session()->get('role') === 'pemilik'): ?>
                            <li>Pengaturan Umum Aplikasi (nama toko, alamat, dll.)</li>
                            <li>Pengaturan Printer Struk</li>
                            <li>Pengaturan Pajak atau Diskon Global</li>
                        <?php endif; ?>
                        <li>Preferensi Tampilan</li>
                        <!-- <li>Notifikasi</li> -->
                    </ul>

                    <p>Silakan kunjungi halaman <a href="<?= site_url(session()->get('role') . '/profile') ?>">Profil</a> Anda untuk memperbarui informasi pribadi dan password.</p>

                    <a href="<?= site_url(session()->get('role') . '/dashboard') ?>" class="btn btn-default" style="margin-top: 15px;">Kembali ke Dashboard</a>
                </div>
            </div>
        </div> <!-- /.col-md-8 -->
    </div><!--/.row-->
</div>  <!--/.main-->

<?= $this->include('Backend/Template/footer') ?>
