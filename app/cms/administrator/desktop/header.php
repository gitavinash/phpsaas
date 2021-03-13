<?php
	$postType=mysqli_real_escape_string($connection, rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') );
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
		<link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>image-uploader/css/image-uploader.min.css">
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
					<li <?php echo (($postType=='') ? 'class="active"' : '' ) ?>><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
					<li <?php echo (($postType=='gallery') ? 'class="active"' : '' ) ?> >
						<a href="gallery.php?type=list&status=all"><i class="fas fa-images"></i> Gallery</a>
						<div class="side-menu">
							<ul>
								<li><a href="gallery.php?type=list&status=all"><i class="fas fa-list"></i> All galleries</a></li>
								<li><a href="gallery.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new gallery</a></li>
								<li><a href="gallery.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="gallery.php?type=list&status=all"><i class="fas fa-list"></i> All galleries</a></li>
								<li><a href="gallery.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new gallery</a></li>
								<li><a href="gallery.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
							</ul>
						</div>
					</li>

					<li <?php echo (($postType=='page') ? 'class="active"' : '' ) ?> >
								<a href="page.php?type=list"><i class="fas fa-file"></i> Page</a>
								<div class="side-menu">
									<ul>
										<li><a href="page.php?type=list&status=all"><i class="fas fa-list"></i> All Pages</a></li>
										<li><a href="page.php?type=add&editor=true"><i class="fas fa-plus"></i>Add New Page</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="page.php?type=list&status=all"><i class="fas fa-list"></i> All Pages</a></li>
										<li><a href="page.php?type=add&editor=true"><i class="fas fa-plus"></i>Add New Page</a></li>
									</ul>
								</div>
							</li>
					<?php
					
						if(SITE_ID==9) { ?>

							<li <?php echo (($postType=='event') ? 'class="active"' : '' ) ?> >
								<a href="event.php?type=list&status=all"><i class="fas fa-gopuram"></i> Events</a>
								<div class="side-menu">
									<ul>
										<li><a href="event.php?type=list&status=all"><i class="fas fa-list"></i> All events</a></li>
										<li><a href="event.php?type=add"><i class="fas fa-plus"></i> Add New event</a></li>
										<li><a href="event.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="event.php?type=list&status=all"><i class="fas fa-list"></i> All events</a></li>
										<li><a href="event.php?type=add"><i class="fas fa-plus"></i> Add New event</a></li>
										<li><a href="event.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
							</li>
							<li <?php echo (($postType=='bhajan' ) ? 'class="active"' : '' ) ?> >
								<a href="bhajan.php?type=list&status=all"><i class="fas fa-om"></i> Bhajan / Aarti</a>
								<div class="side-menu">
									<ul>
										<li><a href="bhajan.php?type=list&status=all"><i class="fas fa-list"></i> All bhajan / aarti</a></li>
										<li><a href="bhajan.php?type=add"><i class="fas fa-plus"></i> Add New bhajan / aarti</a></li>
										<li><a href="bhajan.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="bhajan.php?type=list&status=all"><i class="fas fa-list"></i> All bhajan / aarti</a></li>
										<li><a href="bhajan.php?type=add"><i class="fas fa-plus"></i> Add New bhajan / aarti</a></li>
										<li><a href="bhajan.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
							</li>
							<li <?php echo (($postType=='book') ? 'class="active"' : '' ) ?> >
								<a href="book.php?type=list&status=all"><i class="fas fa-book-open"></i> Books</a>
								<div class="side-menu">
									<ul>
										<li><a href="book.php?type=list&status=all"><i class="fas fa-list"></i> All books</a></li>
										<li><a href="book.php?type=add"><i class="fas fa-plus"></i> Add New book</a></li>
										<li><a href="book.php?type=category"><i class="fas fa-tags"></i> Publications</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="book.php?type=list&status=all"><i class="fas fa-list"></i> All books</a></li>
										<li><a href="book.php?type=add"><i class="fas fa-plus"></i> Add New book</a></li>
										<li><a href="book.php?type=category"><i class="fas fa-tags"></i> Publications</a></li>
									</ul>
								</div>
							</li>
							<li <?php echo (($postType=='discourse') ? 'class="active"' : '' ) ?> >
								<a href="discourse.php?type=list&status=all"><i class="fas fa-podcast"></i> Discourses</a>
								<div class="side-menu">
									<ul>
										<li><a href="discourse.php?type=list&status=all"><i class="fas fa-list"></i> All discourses</a></li>
										<li><a href="discourse.php?type=add"><i class="fas fa-plus"></i> Add New discourse</a></li>
										<li><a href="discourse.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="discourse.php?type=list&status=all"><i class="fas fa-list"></i> All discourses</a></li>
										<li><a href="discourse.php?type=add"><i class="fas fa-plus"></i> Add New discourse</a></li>
										<li><a href="discourse.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
							</li>
							<li <?php echo (($postType=='news') ? 'class="active"' : '' ) ?> >
								<a href="news.php?type=list&status=all"><i class="fas fa-newspaper"></i> News</a>
								<div class="side-menu">
									<ul>
										<li><a href="news.php?type=list&status=all"><i class="fas fa-list"></i> All news</a></li>
										<li><a href="news.php?type=add"><i class="fas fa-plus"></i> Add New news</a></li>
										<li><a href="news.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="news.php?type=list&status=all"><i class="fas fa-list"></i> All news</a></li>
										<li><a href="news.php?type=add"><i class="fas fa-plus"></i> Add New news</a></li>
										<li><a href="news.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
							</li>
							<li <?php echo (($postType=='thought') ? 'class="active"' : '' ) ?> >
								<a href="thought.php?type=list&status=all"><i class="fas fa-lightbulb"></i> Thought of The Day</a>
								<div class="side-menu">
									<ul>
										<li><a href="thought.php?type=list&status=all"><i class="fas fa-list"></i> All Thoughts</a></li>
										<li><a href="thought.php?type=add"><i class="fas fa-plus"></i> Add New thought</a></li>
										<li><a href="thought.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="thought.php?type=list&status=all"><i class="fas fa-list"></i> All Thoughts</a></li>
										<li><a href="thought.php?type=add"><i class="fas fa-plus"></i> Add New thought</a></li>
										<li><a href="thought.php?type=category"><i class="fas fa-tags"></i> Categories</a></li>
									</ul>
								</div>
							</li>
							<li <?php echo (($postType=='embed') ? 'class="active"' : '' ) ?> >
								<a href="embed.php?type=list&status=all"><i class="fab fa-youtube"></i> Embed video</a>
								<div class="side-menu">
									<ul>
										<li><a href="embed.php?type=list&status=all"><i class="fas fa-list"></i> All video</a></li>
										<li><a href="embed.php?type=add"><i class="fas fa-plus"></i> Add New video</a></li>
										<li><a href="embed.php?type=live"><i class="fas fa-video"></i> Add New live video</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="embed.php?type=list&status=all"><i class="fas fa-list"></i> All video</a></li>
										<li><a href="embed.php?type=add"><i class="fas fa-plus"></i> Add New video</a></li>
										<li><a href="embed.php?type=live"><i class="fas fa-video"></i> Add New live video</a></li>
									</ul>
								</div>
							</li>

						<?php
						} else { ?>

							<li <?php echo (($postType=='post') ? 'class="active"' : '' ) ?> >
								<a href="post.php?type=list&status=all"><i class="fas fa-file-alt"></i> Post</a>
								<div class="side-menu">
									<ul>
										<li><a href="post.php?type=list&status=all"><i class="fas fa-list"></i> All post</a></li>
										<li><a href="post.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new post</a></li>
										<li><a href="post.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="post.php?type=list&status=all"><i class="fas fa-list"></i> All post</a></li>
										<li><a href="post.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new post</a></li>
										<li><a href="post.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
									</ul>
								</div>
							</li>

							<li <?php echo (($postType=='service') ? 'class="active"' : '' ) ?> >
								<a href="service.php?type=list&status=all"><i class="fas fa-hand-holding-heart"></i> Services</a>
								<div class="side-menu">
									<ul>
										<li><a href="service.php?type=list&status=all"><i class="fas fa-list"></i> All services</a></li>
										<li><a href="service.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new service</a></li>
										<li><a href="service.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="service.php?type=list&status=all"><i class="fas fa-list"></i> All services</a></li>
										<li><a href="service.php?type=add&editor=true"><i class="fas fa-plus"></i>Add new service</a></li>
										<li><a href="service.php?type=category"><i class="fas fa-tags"></i>Categories</a></li>
									</ul>
								</div>
							</li>
							<li <?php echo ((ADMIN_BASE_URL=='/import/') ? 'class="active"' : '' ) ?> >
								<a href="import.php?type=product"><i class="fas fa-upload"></i> Import</a>
								<div class="side-menu">
									<ul>
										<li><a href="import.php?type=post"><i class="fas fa-file-alt"></i> Post</a></li>
										<li><a href="import.php?type=page"><i class="fas fa-file"></i> Page</a></li>
									</ul>
								</div>
								<div class="bottom-menu">
									<ul>
										<li><a href="import.php?type=post"><i class="fas fa-file-alt"></i> Post</a></li>
										<li><a href="import.php?type=page"><i class="fas fa-file"></i> Page</a></li>
									</ul>
								</div>
							</li>

						<?php
						}

					?>
					<!-- <li <?php echo (($postType=='media') ? 'class="active"' : '' ) ?> >
						<a href="media.php?type=list"><i class="fas fa-folder-open"></i> Media</a>
						<div class="side-menu">
							<ul>
								<li><a href="media.php?type=list"><i class="fas fa-list"></i> All Media</a></li>
								<li><a href="media.php?type=add"><i class="fas fa-plus"></i> Add New</a></li>
								<li><a href="media.php?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
								<li><a href="media.php?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
								<li><a href="media.php?type=list&media=docs"><i class="fas fa-copy"></i> Documents</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="media.php?type=list"><i class="fas fa-list"></i> All Media</a></li>
								<li><a href="media.php?type=add"><i class="fas fa-plus"></i> Add New</a></li>
								<li><a href="media.php?type=list&media=image"><i class="fas fa-images"></i> Images</a></li>
								<li><a href="media.php?type=list&media=video"><i class="fas fa-video"></i> Videos</a></li>
								<li><a href="media.php?type=list&media=docs"><i class="fas fa-copy"></i> Documents</a></li>
							</ul>
						</div>
					</li> -->
					<li <?php echo (($postType=='setting') ? 'class="active"' : '' ) ?> >
						<a href="setting.php?type=configuration"><i class="fas fa-cog"></i> Settings</a>
						<div class="side-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
								<li><a href="setting.php?type=configuration"><i class="fas fa-tools"></i> Configuration</a></li>
								<li><a href="setting/?type=password"><i class="fas fa-key"></i> Change Password</a></li>
								<li><a href="setting.php?type=profile"><i class="fas fa-user"></i> Profile</a></li>
							</ul>
						</div>
						<div class="bottom-menu">
							<ul>
								<li><a href="setting.php?type=menu"><i class="fas fa-ellipsis-v"></i> Menu</a></li>
								<li><a href="setting.php?type=configuration"><i class="fas fa-tools"></i> Configuration</a></li>
								<li><a href="setting/?type=password"><i class="fas fa-key"></i> Change Password</a></li>
								<li><a href="setting.php?type=profile"><i class="fas fa-user"></i> Profile</a></li>
							</ul>
						</div>
					</li>
				</ul>
			</nav>
		</aside>

		<main>

			<div class="container">
					