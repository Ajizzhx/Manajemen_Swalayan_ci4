<?= $this->extend('Backend/Template/admin_template'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Profil Owner</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Profil Owner</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Update Informasi Owner & Email OTP
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <form action="<?= site_url('admin/owner-profile/update') ?>" method="post">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= $owner['nama'] ?? '' ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email (Untuk OTP Login)</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $owner['email'] ?? '' ?>" required>
                            <div class="form-text text-info">
                                Email ini akan digunakan untuk menerima kode OTP saat login sebagai Owner.
                                Pastikan email yang dimasukkan adalah email aktif dan dapat diakses.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-info-circle me-1"></i>
                            Informasi OTP Login
                        </div>
                        <div class="card-body">
                            <p>Sistem login owner menggunakan two-factor authentication (2FA) dengan kode OTP yang dikirimkan ke email.</p>
                            
                            <h5 class="mt-3">Cara Kerja:</h5>
                            <ol>
                                <li>Saat Anda login sebagai owner, sistem akan mengirimkan kode OTP ke email Anda</li>
                                <li>Kode OTP hanya berlaku selama 5 menit</li>
                                <li>Masukkan kode OTP untuk menyelesaikan proses login</li>
                            </ol>
                            
                            <div class="alert alert-warning mt-3">
                                <strong>Penting!</strong> Pastikan email yang Anda masukkan adalah email yang aktif dan dapat diakses 
                                agar Anda selalu dapat menerima kode OTP untuk login.
                            </div>
                            
                            <p class="mt-3">
                                Untuk informasi lebih lanjut tentang pengaturan email OTP, silakan baca panduan 
                                <a href="<?= base_url('PANDUAN_SETUP_EMAIL_OTP.md') ?>" target="_blank">Setup Email OTP</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
