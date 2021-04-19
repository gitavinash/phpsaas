<!DOCTYPE html>
<html>
	<head>
		<base href="<?php echo SITE_URL ?>">
		<meta charset="utf-8">
      	<link rel="shortcut icon" href="<?php echo SITE_ICON ?>">
		<meta name="" content="">
		<meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
		<title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_NAME.': '.SITE_DESC ) ?></title>
		<!-- css -->
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>flickity.min.css">
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
		<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600&display=swap" rel="stylesheet">
		        
        <!-- SEO -->

        <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
        <meta name='robots' content='<?php echo ((isset($meta_robots)) ? $meta_robots : 'index, follow' ) ?>' />
		<link rel="manifest" href="mainfest.json">
		<link rel="apple-touch-icon" href="<?php echo SITE_ICON ?>">
        <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
        <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
        <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">

	</head>
	<body>

		<!-- header start -->
		<header>
			<div class="header-top">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="web-detail">
								<p>Free Shipping on all prepaid orders</p>
								<!-- <p><span class="material-icons">email</span></p> -->
							</div>
							
						</div>
					</div>
				</div>
			</div>
			<div class="header-mid">
				<div class="container-fluid">
					<div class="row">
					
						<div class="col-md-12">
							<nav class="navbar navbar-expand-lg menu-top ">
								<div class="logo">
									<a href="/" ><img src="<?php echo SITE_LOGO ?>" class="logo-mb"></a>
								</div>
								<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-icon"></span>
								<span class="navbar-icon"></span>
								<span class="navbar-icon"></span>
								</button>
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav justify-content-center menu-ul">
									  	<li class="nav-item"><a class="nav-link" href="/">Home</a></li>
									  	<li class="nav-item dropdown">
									      	<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Our Products</a>
									      	<div class="dropdown-menu">
												<a class="dropdown-item" href="/product/gems/gemes-stone/">Gemes stone</a>
												<a class="dropdown-item" href="/product/ring/meru-ring/">Meru ring</a>
												<a class="dropdown-item" href="/product/yantra/yantra/">Yantra</a>
												<a class="dropdown-item" href="/product/puja/puja/">Yantra</a>
									      	</div>
									  	</li>
									  	<li class="nav-item"><a class="nav-link" href="/our-services/">Our Services</a></li>
									  	<!-- <li class="nav-item"><a class="nav-link" target="_blank" href="https://vedicvitals.shiprocket.co">Track Order</a></li> -->
									  	<li class="nav-item"><a class="nav-link" href="/contact-us/">Contact Us</a></li>
									</ul>

								<div class="search-pro">
									<nav class="navbar navbar-expand-lg login-section justify-content-right ">
										<div class="collapse navbar-collapse" id="navbarNavDropdown">
											<ul class="navbar-nav justify-content-center menu-login">
												<li>
											  		<a href="/checkout/"><!-- <i class="fa fa-shopping-bag" aria-hidden="true"></i> -->
											  			<svg aria-hidden="true" focusable="false" role="presentation" class="icon-a icon-cart" viewBox="0 0 37 40"><path d="M36.5 34.8L33.3 8h-5.9C26.7 3.9 23 .8 18.5.8S10.3 3.9 9.6 8H3.7L.5 34.8c-.2 1.5.4 2.4.9 3 .5.5 1.4 1.2 3.1 1.2h28c1.3 0 2.4-.4 3.1-1.3.7-.7 1-1.8.9-2.9zm-18-30c2.2 0 4.1 1.4 4.7 3.2h-9.5c.7-1.9 2.6-3.2 4.8-3.2zM4.5 35l2.8-23h2.2v3c0 1.1.9 2 2 2s2-.9 2-2v-3h10v3c0 1.1.9 2 2 2s2-.9 2-2v-3h2.2l2.8 23h-28z"></path></svg>
													</a>
											  		<span id="cartNumber" >0</span>
												</li>											  
											</ul>

										</div>
									</nav>
								</div>

								</div>

							</nav>
						</div>

					</div>
				</div>
			</div>
		</header>
		<!-- header end -->