<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Swalayan CI4 - Login Admin</title>
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
				<div class="panel-heading">Log in Admin</div>
				<div class="panel-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('errors')): // Untuk validation errors ?>
                        <div class="alert alert-danger">
                            <ul>
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

					<?= form_open('auth/loginProcess') ?> {/* Tetap mengarah ke loginProcess umum */}
						<fieldset>
							<div class="form-group">
								<input class="form-control" placeholder="E-mail Admin" name="email" type="email" autofocus="" value="<?= old('email') ?>">
							</div>
							<div class="form-group">
								<input class="form-control" placeholder="Password" name="password" type="password" value="">
							</div>
							<button type="submit" class="btn btn-primary">Login Admin</button>
                        </fieldset>
					<?= form_close() ?>
				</div>
			</div>
		</div><!-- /.col-->
	</div><!-- /.row -->	
	

<script src="<?= base_url('Assets/js/jquery-1.11.1.min.js') ?>"></script>
<script src="<?= base_url('Assets/js/bootstrap.min.js') ?>"></script>
</body>
</html>