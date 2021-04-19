<!DOCTYPE html>
<html>
	<head>
	
		<base href="<?php echo SITE_URL ?>">
		<meta charset="utf-8">
      	<link rel="shortcut icon" href=" ">
		<meta name="" content="">
		<meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
		<title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'}.' | '.SITE_NAME ) : SITE_DESC.' | '.SITE_NAME ) ?></title>
		
		<!-- css -->
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
		<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.css">
		        
        <!-- SEO --><!-- SEO -->

        <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
        <meta name='robots' content='<?php echo ((isset($meta_robots)) ? $meta_robots : 'index, follow' ) ?>' />
		<link rel="manifest" href="mainfest.json">
		<link rel="apple-touch-icon" href="<?php echo SITE_ICON ?>">
        <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
        <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : ((isset($tagName)) ? $tagName : '' ) ) : SITE_TAGS ) ?>" >
        <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">

		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-69G2SPCWHX"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'G-69G2SPCWHX');
		</script>

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
								<!-- <p><span class="material-icons">email</span></p> -->
							</div>
							
						</div>
					</div>
				</div>
			</div>
			<div class="header-mid dn-s">
				<div class="container-fluid">
					<div class="row">
						
						<div class="col-md-2">
							<div class="logo">
								<a href="/"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>" ></a>
							</div>
						</div>
						<div class="col-md-3">

							<!-- <div class="select-city">
								<form>
								  <div class="form-group city-popup">
								  	<a href="#popup1">
								    <input class="form-control" id="user-selected-city-input" placeholder="Choose Delivery City" ></a>
								  </div>
								</form>
							</div> -->
						</div>
						<div class="col-md-7">
							<div class="search-box">
								<form action="/search/" class="form-inline">
							      <input class="form-control" name="q" type="search" value="<?php echo ((isset($_GET['q'])) ? $_GET['q'] : '' ); ?>" placeholder="Search for gifts, flowers..." >
							    </form>
							</div>
							<div class="search-pro">
								<nav class="navbar navbar-expand-lg login-section justify-content-right ">
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav justify-content-center menu-login">
									  <!-- <li class="searc-div">
									  	<form action="" autocomplete="on">
										  <input id="search" name="search" type="text" placeholder="What're we looking for ?"><input id="search_submit" value="Rechercher" type="submit">
										 </form>
									  </li> -->
									   <!-- <li class="wal-icn"><a href=""><img src="<?php echo USER_IMAGES ?>wallet.svg"></a><span class="span-item">Wallet</span></li>
									  <li><a href=""><img src="<?php echo USER_IMAGES ?>track.svg"></a><span class="span-item">Track Order</span></li> -->
									  <li><a href="/checkout/"><img src="<?php echo USER_IMAGES ?>cart.svg"></a></a><span class="crt-crcl">

									  <?php
											
											if($cartNumber=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart INNER JOIN ".TABLE_PREFIX."product ON cart_relation_id=product_id WHERE cart_session_id='".SESSION_ID."' AND cart_status=0 AND product_status=1 AND cart_site_id=".SITE_ID)) {

												echo mysqli_num_rows($cartNumber);

											}

										?>

										</span><span class="span-item">Cart</span></li>
									  <!-- user acount -->
									  <!-- <li class="nav-item dropdown us-icn">
									      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown"><img src="<?php echo USER_IMAGES ?>signin.svg"><span class="span-item">My Account</span>
									      </a>
									      <div class="dropdown-menu">
									      	<div class="user-data">
									      		<img src="<?php echo USER_IMAGES ?>signin.svg">
									      		<h2>Puneet Jalindra</h2>
									      		<span>puneet.jalindra90@gmail.com</span>
									      		<a href="" class="edit-btn">Edit</a>
									      	</div>
									        <a class="dropdown-item" href="#"><img src="<?php echo USER_IMAGES ?>order.png"> Orders</a>
									        <a class="dropdown-item" href="#"><img src="<?php echo USER_IMAGES ?>feedback-fa.svg">  My Reviews</a>
									        <a class="dropdown-item" href="#"><img src="<?php echo USER_IMAGES ?>address-fa.png"> Manage Address</a>
									        <a class="dropdown-item" href="#"><img src="<?php echo USER_IMAGES ?>logout-fa.png"> Logout</a>
									        
									      </div>
									  </li> -->
									  
									  <!-- <li class="nav-item dropdown">
									      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown"><img src="<?php echo USER_IMAGES ?>menu.svg"><span class="span-item">More</span>
									      </a>
									      <div class="dropdown-menu barMenu">
									        <a class="dropdown-item bm1" href="#"><img src="<?php echo USER_IMAGES ?>gifts-fa.svg"> Corporate Gifts</a>
									        <a class="dropdown-item bm2" href="#"><img src="<?php echo USER_IMAGES ?>franchise-fa.svg"> Franchise</a>
									        <a class="dropdown-item " href="#"><img src="<?php echo USER_IMAGES ?>need-fa.svg"> Need Help</a>
									        <a class="dropdown-item " href="#"><img src="<?php echo USER_IMAGES ?>feedback-fa.svg"> About Us</a>
									      </div>
									  </li> -->
									  
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
								<a href="" class="dn-l"><img src="<?php echo USER_IMAGES ?>logo1.png" class="logo-mb"></a>
								<button class="navbar-toggler bar-mnu" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-icon bar-1"></span>
								<span class="navbar-icon bar-2"></span>
								<span class="navbar-icon bar-1"></span>
								<!-- <img src="<?php echo USER_IMAGES ?>menu.svg"> -->
								</button>
								<div class="mob-menu dn-l">
									<div class="cart-mob">
										<a href="" class="mob-bag sr-mob" id="searchBtn" ><img src="<?php echo USER_IMAGES ?>search-fa.svg"></a></span>
										<a href="/checkout/" class="mob-bag"><img src="<?php echo USER_IMAGES ?>cart.svg"></a><span class="crt-crcl">
											
										<?php
											
											if($cartNumber=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."cart INNER JOIN ".TABLE_PREFIX."product ON cart_relation_id=product_id WHERE cart_session_id='".SESSION_ID."' AND cart_status=0 AND product_status=1")) {

												echo mysqli_num_rows($cartNumber);

											}

										?>
										
										</span></span>
									</div>

									<!-- more dots menu mobile -->
									<!-- <button class="navbar-toggler more-dots" type="button" data-toggle="collapse" data-target="#navbarMore" aria-controls="navbarMore" aria-expanded="false" aria-label="Toggle navigation">
										<img src="<?php echo USER_IMAGES ?>more.svg">		
									</button>
									<div class="collapse navbar-collapse" id="navbarMore">
										<ul class="navbar-nav mob-more">
											<li class="nav-item">
											    <a class="dropdown-item" href="#">Login</a>
											</li>
											<li class="nav-item">
											    <a class="dropdown-item" href="#">Register</a>
											</li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Track Order</a>
										    </li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Same Day</a>
										    </li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Midnight</a>
										    </li>
											<li class="nav-item">
										        <a class="dropdown-item" href="#">Contact Us</a>
										    </li>
										</ul>
									</div> -->
									<!-- more dots menu mobile end-->
								</div>
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav menu-ul">
									 
									  <li class="nav-item active">
									    <a class="nav-link" href="/">Home</a>
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="/tag/luxury/" id="navbardrop" data-toggle="dropdown">Luxury Collections
									      </a>
									      <div class="dropdown-menu mega-menu">
									      	<div class="mega1">
										      	<div class="menu-titl"><a href="">New Arivals<span class="cart-bodr"></span></a></div>
										        <a class="dropdown-item" href="/category/flower/">Fresh Flowers</a>
										        <a class="dropdown-item" href="/category/artistic-artificial/">Artistic Artificial</a>
									        </div>
									  	  </div>
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="/tag/occasions/" id="navbardrop" data-toggle="dropdown">Occasions
									      </a>
									      <div class="dropdown-menu mega-menu">
									      	<div class="mega2">
									      	    <div class="mega-gry flot-menu">
									      	    	<div class="menu-titl"><a href="#">By Theme<span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="/tag/anniversary">Anniversary</a>
											        <a class="dropdown-item" href="/tag/birthday/">Birthday</a>
											        <a class="dropdown-item" href="/tag/corporate-gift/">Corporate Gifting</a>
											        <a class="dropdown-item" href="/tag/congratulation/">Congratulation</a>
											        <a class="dropdown-item" href="/tag/get-well/">Get Well</a>
											        <a class="dropdown-item" href="/tag/house-warming/">House Warming</a>
									    		</div>
									    		<div class="mega-wht flot-menu">
									    			<div class="menu-titl"><a href="#">By Type<span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="/tag/i-m-sorry/">I'M Sorry</a>
											        <a class="dropdown-item" href="/tag/just-because/">Just Because</a>
											        <a class="dropdown-item" href="/tag/new-baby/">New Baby</a>
											        <a class="dropdown-item" href="/tag/love/">Love & Romance</a>
											        <a class="dropdown-item" href="/tag/retirement/">Retirement</a>
											        <a class="dropdown-item" href="/tag/sympathy/">Sympathy</a>
									    		</div>
									    		<div class="mega-gry flot-menu">
									      	    	<div class="menu-titl"><a href="/tag/bumper-sale/">Offer <span class="cart-bodr"></span></a></div>
											        <a class="dropdown-item" href="#">
											        	<img class="mega-img" src="">
											        </a>
									    		</div>
									    	</div>
									      </div>
									  </li>
									  <li class="nav-item">
									    <a class="nav-link" href="/tag/everlasting/">Infinity Everlasting Flowers</a>
									  </li>
									  <li class="nav-item">
									    <a class="nav-link" href="/category/vases-and-ceramics/">Vases & Ceramics</a>
									  </li>
									  <li class="nav-item">
									    <a class="nav-link" href="/category/planters/">Planters</a>
									  </li>
									  <li class="nav-item ">
									    <a class="nav-link " href="/subscription/">Subscription</a>
									  </li>

									  <div class="dn-l log-in">
									  	<li class="nav-item">
									    	<a class="nav-link" href="/login/">Login</a>
									  	</li>
									  </div>
									  <!-- <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Birthday
									      </a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									      </div> 
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Occasions
									      </a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									      </div> 
									  </li>
									  <li class="nav-item dropdown">
									    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Corporate Gifts
									      </a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									        <a class="dropdown-item" href="#">Link 1</a>
									      </div> 
									  </li> -->
									</ul>

									<a href="/contact/" class="cont-menu">Contact Us</a>
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
							<div class="map-tag">
								<a href=""><img src="<?php echo USER_IMAGES ?>ping-map.svg">Select the city</a>
							</div>
							<div id="search-mob-div" class="searchInput">
									<input title="Enter the terms you wish to search for." type="text" id="search_block" name="search_block_form" size="15" maxlength="128" class="form-text ui-autocomplete-input" placeholder="Search for gifts, flowers..." autocomplete="off" value=""><div id="close">x</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<!-- header end -->
