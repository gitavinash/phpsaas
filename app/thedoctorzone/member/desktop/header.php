<?php
	$postType=rtrim(ltrim(ADMIN_BASE_URL, '/'), '/');
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<base href="/dashboard/">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="<?php echo SITE_ICON ?>">
        <title><?php echo ucwords(((ADMIN_BASE_URL=='/') ? 'Dashboard' : str_replace('/', ' ', ADMIN_BASE_URL.' '.@$_GET['type']) ).' | '.SITE_NAME ); ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo DEFAULT_ADMIN_STYLESHEET ?>admin.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>all.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>sweetalert/sweetalert2.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>ui/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
	</head>
	<body>
		
		<header>
			<div class="left-side">
				<ul>
					<li><div class="logo"><a href="index.php"><img src="<?php echo SITE_LOGO ?>" alt=""></a></div></li>
				</ul>
			</div>
			<div class="right-side">
				<ul>
					<li>
						<a href="">Welcome, <strong><?php echo PROFILE_DISPLAY_NAME ?></strong> <div class="profile-img"><img src="<?php echo ((PROFILE_IMAGE!='') ? PROFILE_IMAGE : IMG_LIBRARY.'default-profile.png' ); ?>" alt=""></div></a>
						<ul>
							<li><a href="setting.php?type=profile"><i class="fas fa-user-cog"></i> Edit Profile</a></li>
							<li><a href="setting.php?type=password"><i class="fas fa-key"></i> Change Password</a></li>
							<li><a href="/logout/"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</header>
		
		<aside>
			<nav class="side-nav">
				<ul>
					<li <?php echo ((ADMIN_BASE_URL=='/') ? 'class="active"' : '' ) ?>><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
					<li <?php echo ((ADMIN_BASE_URL=='/setting/') ? 'class="active"' : '' ) ?> >
						<a href="setting.php?type=website"><i class="fas fa-cog"></i> Settings</a>
						<div class="side-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i>  Menu</a></li>
								<li><a href="setting.php?type=configuration"><i class="fas fa-tools"></i>  Configuration</a></li>
								<li><a href="setting/?type=password"><i class="fas fa-key"></i>  Change Password</a></li>
								<li><a href="setting.php?type=profile"><i class="fas fa-user"></i>  Profile</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i>  Menu</a></li>
								<li><a href="setting.php?type=configuration"><i class="fas fa-tools"></i>  Configuration</a></li>
								<li><a href="setting/?type=password"><i class="fas fa-key"></i>  Change Password</a></li>
								<li><a href="setting.php?type=profile"><i class="fas fa-user"></i>  Profile</a></li>
							</ul>
						</div>
					</li>
				</ul>
			</nav>
		</aside>

		<main>

			<div class="container">
					