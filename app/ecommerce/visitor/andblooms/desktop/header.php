<!DOCTYPE html>
<html>
	<head>
		<base href="<?php echo SITE_URL ?>">
		<meta charset="utf-8">
      	<link rel="shortcut icon" href=" ">
		<meta name="" content="">
		<meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
		<title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_DESC.' | '.SITE_NAME ) ?></title>
		<!-- css -->
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>flickity.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>all.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;600;800&display=swap" rel="stylesheet">
		        
        <!-- SEO -->

        <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
        <meta name='robots' content='noindex, nofollow' />
        <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
        <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
		<meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">
	
	</head>
	<body >
		<!-- header start -->
		<header>
			<div class="header-top">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="web-detail dn-s">
								<p>Latest Update: Delivery Available in selected Cities<span></p>
							</div>
							
						</div>
					</div>
				</div>
			</div>
			<div class="header-mid dn-s">
				<div class="container-fluid">
					<div class="row">
						
						<div class="col-md-2">
							<a href="/">
								<div class="logo">
									<!-- <img src="<?php echo USER_IMAGES ?>logo.png"> -->
									<img src="https://andblooms.com/wp-content/uploads/2019/07/andblooms_logo_512x512.png">
								</div>
							</a>
						</div>
						<div class="col-md-3">
						</div>
						<div class="col-md-7">
							<div class="search-box">
								<form method="get" action="/search/" class="form-inline">
							      <input class="form-control" type="text" name="q" placeholder="Search for gifts, flowers..." >
							    </form>
							</div>
							<div class="search-pro">
								<nav class="navbar navbar-expand-lg login-section justify-content-right ">
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav justify-content-center menu-login">
										<li><a href="/checkout/"><img src="<?php echo USER_IMAGES ?>cart.svg"></a></a><span class="crt-crcl">
											<?php
											
												if($cartNumber=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart INNER JOIN ".TABLE_PREFIX."product ON cart_relation_id=product_id WHERE cart_session_id='".SESSION_ID."' AND cart_status=0 AND product_status=1")) {

													echo mysqli_num_rows($cartNumber);

												}

											?>
										</span><span class="span-item">Cart</span></li>
									  	<li class="nav-item dropdown">
											<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown"><img src="<?php echo USER_IMAGES ?>menu.svg"><span class="span-item">More</span>
											</a>
											<div class="dropdown-menu barMenu">
												<a class="dropdown-item bm1" href="#"><img src="<?php echo USER_IMAGES ?>gifts-fa.svg"> Corporate Gifts</a>
												<a class="dropdown-item bm2" href="#"><img src="<?php echo USER_IMAGES ?>franchise-fa.svg"> Franchise</a>
												<a class="dropdown-item " href="#"><img src="<?php echo USER_IMAGES ?>need-fa.svg"> Need Help</a>
												<a class="dropdown-item " href="#"><img src="<?php echo USER_IMAGES ?>feedback-fa.svg"> About Us</a>
											</div>
									  	</li>
									  
									</ul>
								</div>
							</nav>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="header-box">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<nav class="navbar navbar-expand-lg menu-top fixed">
								<a href="" class="dn-l">
									<!-- <img src="<?php echo USER_IMAGES ?>logo1.png" class="logo-mb"> -->
									<img src="https://andblooms.com/wp-content/uploads/2019/07/andblooms_logo_512x512.png" class="logo-mb">
								</a>
								<button class="navbar-toggler bar-mnu" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-icon bar-1"></span>
								<span class="navbar-icon bar-2"></span>
								<span class="navbar-icon bar-1"></span>
								</button>
								<div class="mob-menu dn-l">
									<div class="cart-mob">
										<a href="" class="mob-bag sr-mob" onclick="divSearchPop()"><img src="<?php echo USER_IMAGES ?>search-fa.svg"></a></span>
										<a href="/checkout/" class="mob-bag"><img src="<?php echo USER_IMAGES ?>cart.svg"></a><span class="crt-crcl">0</span></span>
									</div>
								</div>
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav menu-ul">
									 
									  <li class="nav-item">
									    <a class="nav-link" href="/">Home</a>
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="category/" id="navbardrop" data-toggle="dropdown">Luxury Collections
									      </a>
									      <div class="dropdown-menu mega-menu">
									      	<div class="mega1">
										      	<div class="menu-titl"><a href="#">New Arivals<span class="cart-bodr"></span></a></div>
										        <a class="dropdown-item" href="category/luxury-collections/fresh-flowers/">Fresh Flowers</a>
										        <a class="dropdown-item" href="category/artistic-artificial/">Artistic Artificial</a>
									        </div>
									  	  </div>
									  </li>
									  <li class="nav-item">
									    <a class="nav-link" href="category/infinity-everlasting-flowers/">Infinity Everlasting Flowers</a>
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Occasins
									      </a>
									      <div class="dropdown-menu mega-menu">
									      	<div class="mega2">
									      	    <div class="mega-gry flot-menu">
									      	    	<div class="menu-titl"><a href="#">Menu Name <span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="category/anniversary/">Anniversary</a>
											        <a class="dropdown-item" href="category/birthday/">Birthday</a>
											        <a class="dropdown-item" href="category/corporate-gifting/">Corporate Gifting</a>
											        <a class="dropdown-item" href="category/congratulation/">Congratulation</a>
											        <a class="dropdown-item" href="category/get-well/">Get Well</a>
											        <a class="dropdown-item" href="category/category/occasions/house-warming/">House Warming</a>
									    		</div>
									    		<div class="mega-wht flot-menu">
									    			<div class="menu-titl"><a href="#">Menu Name <span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="category/i-m-sorry/">I'M Sorry</a>
											        <a class="dropdown-item" href="category/just-because/">Just Because</a>
											        <a class="dropdown-item" href="category/new-baby/">New Baby</a>
											        <a class="dropdown-item" href="category/love---romance/">Love & Romance</a>
											        <a class="dropdown-item" href="category/retirement/">Retirement</a>
											        <a class="dropdown-item" href="category/sympathy/">Sympathy</a>
									    		</div>
									    		<div class="mega-gry flot-menu">
									      	    	<div class="menu-titl"><a href="">Bumper Sale <span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="#">
											        	<img class="mega-img" src="https://img.& Blooms.com/sites/default/files/alloccasion/menucard/Plants.png">
											        </a>
									    		</div>
									    	</div>
									      </div>
									  </li>
									  <li class="nav-item ">
									    <a class="nav-link " href="#">Subscription</a>
									  </li>
									  <li class="nav-item ">
									    <a class="nav-link " href="#">Contact</a>
									  </li>

									  <div class="dn-l log-in">
									  	<li class="nav-item">
									    	<a class="nav-link" href="/">Login</a>
									  	</li>
									  </div>
									</ul>

									<a href=" " class="cont-menu">Contact Us</a>
								</div>
							</nav>
						</div>
					</div>
				</div>
			</div>
			<div class="dn-l location-map">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div id="search-mob-div" class="searchInput">
								<form method="get" action="/search/">
									<input type="text" id="search_block" name="q" class="form-text ui-autocomplete-input" placeholder="Search for gifts, flowers..." autocomplete="off" ><div id="close">x</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<!-- header end -->