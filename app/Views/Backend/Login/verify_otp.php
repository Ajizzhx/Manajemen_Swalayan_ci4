<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Verifikasi OTP - Swalayan</title>
	<link href="<?= base_url('Assets/css/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?= base_url('Assets/css/datepicker3.css') ?>" rel="stylesheet">
	<link href="<?= base_url('Assets/css/styles.css') ?>" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="<?= base_url('Assets/js/html5shiv.js') ?>"></script>
	<script src="<?= base_url('Assets/js/respond.min.js') ?>"></script>
	<![endif]-->
</head>
<body>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
			<div class="login-panel panel panel-default">
				<div class="panel-heading">
                    <span class="glyphicon glyphicon-lock"></span> Verifikasi OTP
                </div>
				<div class="panel-body">
                    <p class="text-center text-muted">Masukkan kode OTP yang dikirim ke email Anda.</p>
                    <p class="text-center">
                        <span class="glyphicon glyphicon-envelope"></span> Email: <strong><?= esc(session()->get('2fa_karyawan_data')['email'] ?? 'email Anda') ?></strong>
                    </p>
                    <hr/>
                        <?php if (session()->getFlashdata('message')): ?>
                            <div class="alert alert-info alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <?= session()->getFlashdata('message') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?= form_open('auth/process-otp') ?>
                            <?= csrf_field() ?>
                            <fieldset>
                            <div class="form-group">
                                <input id="otp_code" class="form-control text-center" style="font-size: 1.5em; letter-spacing: 0.5em;" placeholder="______" name="otp_code" type="text" pattern="\d{6}" title="Masukkan 6 digit kode OTP" maxlength="6" value="<?= old('otp_code') ?>" required autofocus autocomplete="one-time-code">
                            </div>
                            <p class="help-block text-center">Kode berlaku 5 menit. <a href="<?= site_url('auth/resend-otp') ?>">Kirim Ulang OTP</a></p>
                            
                            <button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-ok"></span> Verifikasi</button>
                            <a href="<?= site_url('login') ?>" class="btn btn-default btn-block" style="margin-top: 10px;"><span class="glyphicon glyphicon-log-out"></span> Batal Login</a>
                            </fieldset>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

+<script src="<?= base_url('Assets/js/jquery-1.11.1.min.js') ?>"></script>
+<script src="<?= base_url('Assets/js/bootstrap.min.js') ?>"></script>
 </body>
 </html>