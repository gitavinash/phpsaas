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
		<link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>sweetalert/sweetalert.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY?>ui/jquery-ui.css">
    	<link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.css">
        <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>datatable/css/datatables.min.css">
    	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
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
					<li <?php echo ((ADMIN_BASE_URL=='/product/') ? 'class="active"' : '' ) ?> >
						<a href="product.php?type=list"><i class="fas fa-box-open"></i> Product</a>
						<div class="side-menu">
							<ul>
								<li><a href="product.php?type=list&status=all"><i class="fas fa-list"></i> All product</a></li>
								<li><a href="product.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new product</a></li>
								<li><a href="product.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="product.php?type=list&status=all"><i class="fas fa-list"></i> All product</a></li>
								<li><a href="product.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new product</a></li>
								<li><a href="product.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/page/') ? 'class="active"' : '' ) ?> >
						<a href="page.php?type=list"><i class="fas fa-file"></i> Page</a>
						<div class="side-menu">
							<ul>
								<li><a href="page.php?type=list&status=all"><i class="fas fa-list"></i> All pages</a></li>
								<li><a href="page.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new page</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="page.php?type=list&status=all"><i class="fas fa-list"></i> All pages</a></li>
								<li><a href="page.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new page</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/media/') ? 'class="active"' : '' ) ?> >
						<a href="media.php?type=list"><i class="fas fa-folder-open"></i> Media</a>
						<div class="side-menu">
							<ul>
								<li><a href="media.php?type=list&status=all"><i class="fas fa-list"></i> All media</a></li>
								<li><a href="media.php?type=add"><i class="fas fa-plus"></i> Add new media</a></li>
								<li><a href="media.php?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
								<li><a href="media.php?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
								<li><a href="media.php?type=list&media=docs"><i class="fas fa-copy"></i> Documents</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="media.php?type=list&status=all"><i class="fas fa-list"></i> All media</a></li>
								<li><a href="media.php?type=add"><i class="fas fa-plus"></i> Add new media</a></li>
								<li><a href="media.php?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
								<li><a href="media.php?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
								<li><a href="media.php?type=list&media=docs"><i class="fas fa-copy"></i> Documents</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/coupon/') ? 'class="active"' : '' ) ?> >
						<a href="coupon.php?type=list"><i class="fas fa-tags"></i> Coupon</a>
						<div class="side-menu">
							<ul>
								<li><a href="coupon.php?type=list&status=all"><i class="fas fa-list"></i> All coupons</a></li>
								<li><a href="coupon.php?type=add"><i class="fas fa-ticket-alt"></i> Add new coupon</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="coupon.php?type=list&status=all"><i class="fas fa-list"></i> All coupons</a></li>
								<li><a href="coupon.php?type=add"><i class="fas fa-ticket-alt"></i> Add new coupon</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/import/') ? 'class="active"' : '' ) ?> >
						<a href="import.php?type=product"><i class="fas fa-upload"></i> Import</a>
						<div class="side-menu">
							<ul>
								<li><a href="import.php?type=product"><i class="fas fa-cubes"></i> Products</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="import.php?type=product"><i class="fas fa-cubes"></i> Products</a></li>
							</ul>
						</div>
					</li>
					<!-- <li <?php echo ((ADMIN_BASE_URL=='/export/') ? 'class="active"' : '' ) ?> >
						<a href="export.php?type=product"><i class="fas fa-download"></i> Export</a>
						<div class="side-menu">
							<ul>
								<li><a href="export.php?type=product"><i class="fas fa-cubes"></i> Products</a></li>
								<li><a href="export.php?type=users"><i class="fas fa-users"></i> Users</a></li>
								<li><a href="export.php?type=orders"><i class="fas fa-funnel-dollar"></i> Orders</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="export.php?type=product"><i class="fas fa-cubes"></i> Products</a></li>
								<li><a href="export.php?type=users"><i class="fas fa-users"></i> Users</a></li>
								<li><a href="export.php?type=orders"><i class="fas fa-funnel-dollar"></i> Orders</a></li>
							</ul>
						</div>
					</li> -->
					<!-- <li <?php echo ((ADMIN_BASE_URL=='/import/') ? 'class="active"' : '' ) ?> >
						<a href="setting.php?type=website"><i class="fas fa-cog"></i> Import</a>
						<div class="side-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
							</ul>
						</div>
					</li> -->
					<li <?php echo ((ADMIN_BASE_URL=='/setting/') ? 'class="active"' : '' ) ?> >
						<a href="setting.php?type=website"><i class="fas fa-cog"></i> Settings</a>
						<div class="side-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
								<li><a href="setting.php?type=configuration"><i class="fas fa-tools"></i> Configuration</a></li>
								<li><a href="setting/?type=password"><i class="fas fa-key"></i> Change password</a></li>
								<li><a href="setting.php?type=profile"><i class="fas fa-user"></i> Profile</a></li>
								<li><a href="setting.php?type=redirection&status=all"><i class="fas fa-external-link-alt"></i> URL redirection</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
								<li><a href="setting.php?type=configuration"><i class="fas fa-tools"></i> Configuration</a></li>
								<li><a href="setting/?type=password"><i class="fas fa-key"></i> Change Password</a></li>
								<li><a href="setting.php?type=profile"><i class="fas fa-user"></i> Profile</a></li>
								<li><a href="setting.php?type=redirection&status=all"><i class="fas fa-external-link-alt"></i> URL redirection</a></li>
							</ul>
						</div>
					</li>
				</ul>
			</nav>
		</aside>

		<main>

			<div class="container">
					