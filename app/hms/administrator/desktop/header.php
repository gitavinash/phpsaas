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
        <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_PLUGINS ?>sweetalert/sweetalert2.min.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
	</head>
	
	<body>
		
		<header>
			<div class="left-side">
				<ul>
					<li><div class="logo"><a href="index.php"><img src="<?php echo ADMIN_IMAGES ?>logo.png" alt=""></a></div></li>
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
					<li <?php echo ((ADMIN_BASE_URL=='/app/') ? 'class="active"' : '' ) ?> >
						<a href="app.php?type=list"><i class="fas fa-box-open"></i> Apps</a>
						<div class="side-menu">
							<ul>
								<li><a href="app.php?type=list"><i class="fas fa-list"></i>  All Apps</a></li>
								<li><a href="app.php?type=install"><i class="fas fa-cloud-download-alt"></i>Install New Apps</a></li>
								<li><a href="app.php?type=installed"><i class="fab fa-dropbox"></i>  My Apps</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="app.php?type=list"><i class="fas fa-list"></i>  All Apps</a></li>
								<li><a href="app.php?type=install"><i class="fas fa-cloud-download-alt"></i>Install New Apps</a></li>
								<li><a href="app.php?type=installed"><i class="fab fa-dropbox"></i>  My Apps</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/hotel/') ? 'class="active"' : '' ) ?> >
						<a href="hotel.php?type=list&status=all"><i class="fas fa-hotel"></i> Hotel</a>
						<div class="side-menu">
							<ul>
								<li><a href="hotel.php?type=list&status=all"><i class="fas fa-list"></i> All Hotel</a></li>
								<li><a href="hotel.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
								<li><a href="hotel.php?type=category"><i class="fas fa-map-marker-alt"></i> Location</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="hotel.php?type=list&status=all"><i class="fas fa-list"></i> All Hotel</a></li>
								<li><a href="hotel.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
								<li><a href="hotel.php?type=category"><i class="fas fa-map-marker-alt"></i> Location</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/page/') ? 'class="active"' : '' ) ?> >
						<a href="page.php?type=list"><i class="fas fa-file"></i> Page</a>
						<div class="side-menu">
							<ul>
								<li><a href="page.php?type=list&status=all"><i class="fas fa-list"></i> All Pages</a></li>
								<li><a href="page.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="page.php?type=list&status=all"><i class="fas fa-list"></i> All Pages</a></li>
								<li><a href="page.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/media/') ? 'class="active"' : '' ) ?> >
						<a href="media.php?type=list"><i class="fas fa-folder-open"></i> Media</a>
						<div class="side-menu">
							<ul>
								<li><a href="media.php?type=list"><i class="fas fa-list"></i>  All Media</a></li>
								<li><a href="media.php?type=add"><i class="fas fa-plus"></i>  Add New</a></li>
								<li><a href="media.php?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
								<li><a href="media.php?type=list&media=video"><i class="fas fa-video"></i>  Videos</a></li>
								<li><a href="media.php?type=list&media=docs"><i class="fas fa-copy"></i>  Documents</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="media.php?type=list"><i class="fas fa-list"></i>  All Media</a></li>
								<li><a href="media.php?type=add"><i class="fas fa-plus"></i>  Add New</a></li>
								<li><a href="media.php?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
								<li><a href="media.php?type=list&media=video"><i class="fas fa-video"></i>  Videos</a></li>
								<li><a href="media.php?type=list&media=docs"><i class="fas fa-copy"></i>  Documents</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/user/') ? 'class="active"' : '' ) ?> >
						<a href="user.php?type=list"><i class="fas fa-users"></i> Users</a>
						<div class="side-menu">
							<ul>
								<li><a href="user.php?type=add"><i class="fas fa-plus"></i>  Add New</a></li>
								<li><a href="user.php?type=list"><i class="fas fa-list"></i>  All Users</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="user.php?type=add"><i class="fas fa-plus"></i>  Add New</a></li>
								<li><a href="user.php?type=list"><i class="fas fa-list"></i>  All Users</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/setting/') ? 'class="active"' : '' ) ?> >
						<a href="setting.php?type=website"><i class="fas fa-cog"></i> Settings</a>
						<div class="side-menu">
							<ul>
								<li><a href="setting.php?type=website"><i class="fas fa-globe"></i>  Website</a></li>
								<li><a href="setting.php?type=social"><i class="fas fa-hashtag"></i>  Social</a></li>
								<li><a href="setting/?type=password"><i class="fas fa-key"></i>  Change Password</a></li>
								<li><a href="setting.php?type=profile"><i class="fas fa-user"></i>  Profile</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="setting.php?type=website"><i class="fas fa-globe"></i>  Website</a></li>
								<li><a href="setting.php?type=social"><i class="fas fa-hashtag"></i>  Social</a></li>
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
					