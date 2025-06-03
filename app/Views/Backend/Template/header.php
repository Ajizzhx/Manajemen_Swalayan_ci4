<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Aplikasi Swalayan Berbasis Web</title>

<link href="/Assets/css/bootstrap.min.css" rel="stylesheet">
<link href="/Assets/css/datepicker3.css" rel="stylesheet">
<link href="/Assets/css/bootstrap-table.css" rel="stylesheet">
<link href="/Assets/css/styles.css" rel="stylesheet">

<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<script src="js/respond.min.js"></script>
<![endif]-->

</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<?php
					$dashboard_url = site_url('/'); // Default fallback
					if (session()->get('isLoggedIn')) {
						if (session()->get('role') === 'admin' || session()->get('role') === 'pemilik') {
							$dashboard_url = site_url('admin/dashboard');
						} elseif (session()->get('role') === 'kasir') {
							$dashboard_url = site_url('kasir/dashboard');
						} // Pemilik juga diarahkan ke admin/dashboard
					}
				?>
				<a class="navbar-brand" href="<?= $dashboard_url ?>">
					<span class="brand-highlight">Toko Dolog Sihite</span> <span class="brand-sub">3</span>
				</a>
				<ul class="user-menu">
					<li class="dropdown pull-right">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?= esc(session()->get('nama_karyawan') ?? session()->get('nama') ?? 'User') ?> <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<?php
								$profile_url = '#';
								$settings_url = '#';
								$current_role = session()->get('role');
								if (session()->get('isLoggedIn') && $current_role) {
									if ($current_role === 'admin' || $current_role === 'pemilik') {
										$profile_url = site_url('admin/profile');
										$settings_url = site_url('admin/settings');
									} elseif ($current_role === 'kasir') {
										$profile_url = site_url('kasir/profile');
										$settings_url = site_url('kasir/settings');
									}
								}
							?>
							<li><a href="<?= $profile_url ?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
							<?php
							?>
							<li><a href="<?= $settings_url ?>"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
							<li><a href="#" id="headerLogoutLink"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
							
		</div><!-- /.container-fluid -->
	</nav>