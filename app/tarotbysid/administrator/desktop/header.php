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
        <link rel="stylesheet" type="text/css" href="<?php echo ADMIN_STYLESHEET ?>admin.min.css">
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
					<li <?php echo ((ADMIN_BASE_URL=='/news/') ? 'class="active"' : '' ) ?> >
						<a href="news.php?type=list&status=all"><i class="fas fa-newspaper"></i> News</a>
						<div class="side-menu">
							<ul>
								<li><a href="news.php?type=list&status=all"><i class="fas fa-list"></i> All News</a></li>
								<li><a href="news.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
								<li><a href="news.php?type=category"><i class="fas fa-folder-plus"></i> Categories</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="news.php?type=list&status=all"><i class="fas fa-list"></i> All News</a></li>
								<li><a href="news.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
								<li><a href="news.php?type=category"><i class="fas fa-folder-plus"></i> Categories</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/live/') ? 'class="active"' : '' ) ?> >
						<a href="news.php?type=list&status=all"><i class="far fa-dot-circle"></i> Live</a>
						<div class="side-menu">
							<ul>
								<li><a href="live.php?type=list&status=all"><i class="fas fa-list"></i> All Lives</a></li>
								<li><a href="live.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="news.php?type=list&status=all"><i class="fas fa-list"></i> All News</a></li>
								<li><a href="news.php?type=add&editor=true"><i class="fas fa-plus"></i> Add New</a></li>
								<li><a href="news.php?type=category"><i class="fas fa-folder-plus"></i> Categories</a></li>
								<li><a href="news.php?type=tag"><i class="fas fa-tags"></i> Tags</a></li>
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
					<li <?php echo ((ADMIN_BASE_URL=='/comments/') ? 'class="active"' : '' ) ?> ><a href="comments.php"><i class="fas fa-comments"></i> Comments</a></li>
					<li <?php echo ((ADMIN_BASE_URL=='/data/') ? 'class="active"' : '' ) ?> >
						<a href="data.php?type=import"><i class="fas fa-database"></i> Transfer Data</a>
						<div class="side-menu">
							<ul>
								<li><a href="data.php?type=import"><i class="fas fa-upload"></i> Import Data</a></li>
								<li><a href="data.php?type=export"><i class="fas fa-download"></i> Export Data</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="data.php?type=import"><i class="fas fa-upload"></i> Import Data</a></li>
								<li><a href="data.php?type=export"><i class="fas fa-download"></i> Export Data</a></li>
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
					<!-- <li <?php echo ((ADMIN_BASE_URL=='/analytics/') ? 'class="active"' : '' ) ?> >
						<a href="analytics.php?type=report"><i class="fas fa-chart-line"></i> Analytics</a>
						<div class="side-menu">
							<ul>
								<li><a href="analytics.php?type=realtime"><i class="fas fa-clock"></i>  Real Time</a></li>
								<li><a href="analytics.php?type=source"><i class="fas fa-filter"></i>  Traffic Source</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="analytics.php?type=realtime"><i class="fas fa-clock"></i>  Real Time</a></li>
								<li><a href="analytics.php?type=source"><i class="fas fa-filter"></i>  Traffic Source</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/webmaster/') ? 'class="active"' : '' ) ?> >
						<a href="webmaster.php?type=configure"><i class="fas fa-tools"></i> Webmaster</a>
						<div class="side-menu">
							<ul>
								<li><a href="webmaster.php?type=configure"><i class="fas fa-wrench"></i>  Configure</a></li>
								<li><a href="webmaster.php?type=sitemap"><i class="fas fa-sitemap"></i>  Submit Sitemap</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="webmaster.php?type=configure"><i class="fas fa-wrench"></i>  Configure</a></li>
								<li><a href="webmaster.php?type=sitemap"><i class="fas fa-sitemap"></i>  Submit Sitemap</a></li>
							</ul>
						</div>
					</li> -->
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
					<!-- <li <?php echo ((ADMIN_BASE_URL=='/customize/') ? 'class="active"' : '' ) ?> >
						<a href="customize.php?type=theme"><i class="fas fa-paint-roller"></i> Customize</a>
						<div class="side-menu">
							<ul>
								<li><a href="customize.php?type=theme"><i class="fas fa-palette"></i>  Theme</a></li>
								<li><a href="customize.php?type=menu"><i class="fas fa-bars"></i>  Menu</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="customize.php?type=theme"><i class="fas fa-palette"></i>  Theme</a></li>
								<li><a href="customize.php?type=menu"><i class="fas fa-bars"></i>  Menu</a></li>
							</ul>
						</div>
					</li>
					<li <?php echo ((ADMIN_BASE_URL=='/help/') ? 'class="active"' : '' ) ?> ><a href="help.php"><i class="fas fa-question-circle"></i> Help</a></li>
					<li <?php echo ((ADMIN_BASE_URL=='/support/') ? 'class="active"' : '' ) ?> >
						<a href="support.php?type=create"><i class="fas fa-life-ring"></i> Support</a>
						<div class="side-menu">
							<ul>
								<li><a href="support.php.php?type=create"><i class="fas fa-ticket-alt"></i> Create Ticket</a></li>
								<li><a href="support.php?type=status"><i class="fas fa-search"></i> Check Ticket Status</a></li>
								<li><a href="support.php?type=history"><i class="fas fa-history"></i> Ticket History</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="support.php.php?type=create"><i class="fas fa-ticket-alt"></i> Create Ticket</a></li>
								<li><a href="support.php?type=status"><i class="fas fa-search"></i> Check Ticket Status</a></li>
								<li><a href="support.php?type=history"><i class="fas fa-history"></i> Ticket History</a></li>
							</ul>
						</div>
					</li> -->
				</ul>
			</nav>
		</aside>

		<main>

			<div class="container">
					