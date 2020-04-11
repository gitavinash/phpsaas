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
        <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>style.css">
        <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>all.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>sweetalert/sweetalert2.min.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
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
						<a href="">Welcome, <strong><?php echo PROFILE_DISPLAY_NAME ?></strong> <div class="profile-img"><img src="<?php echo PROFILE_IMAGE ?>" alt=""></div></a>
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
					<li <?php echo ((ADMIN_BASE_URL=='/result/') ? 'class="active"' : '' ) ?> >
						<a href="result.php?type=list&status=all"><i class="fas fa-poll"></i> Result</a>
					</li>
				</ul>
			</nav>
		</aside>

		<main>

			<div class="container">
					