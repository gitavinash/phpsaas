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
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>flickity.min.css">
		<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@500;700&family=Mrs+Saint+Delafield&family=Poppins:wght@200;300;400;600;800&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">
		
		<!-- SEO -->

        <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
        <meta name='robots' content='<?php echo $meta_robots ?>' />
		<link rel="manifest" href="mainfest.json">
		<link rel="apple-touch-icon" href="<?php echo SITE_ICON ?>">
        <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
        <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
        <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">

	</head>
	<body>
		<!-- header start -->
		<header id="header">
			<div class="header-mid dn-s">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-5">
							<div class="lang-section">
								<form action="" autocomplete="on">
									<!-- <div class="input-group slct-lang">
	  									<select class="custom-select" id="inputGroupSelect02">
		    								<option selected>English</option>
		    								<option value="1">Hindi</option>
		    								<option value="2">Urban</option>
		    								<option value="3">Urdu</option>
	  									</select>
								    </div>
								    <div class="input-group slct-lang1">
	  									<select class="custom-select" id="inputGroupSelect02">
		    								<option selected>USD</option>
		    								<option value="1">INR</option>
	  									</select>
								    </div> -->
									<div class="searc-div">
										<!-- <div id="google_translate_element"></div> -->
										<input id="search" name="search" type="text" placeholder="What're we looking for ?"><input id="search_submit" value="Rechercher" type="submit">
									</div>
								</form>
							</div>
						</div>
						<div class="col-md-2">
							<div class="logo">
								<img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>">
							</div>
						</div>
						<div class="col-md-5">
							<div class="search-pro">
								<nav class="navbar navbar-expand-lg login-section justify-content-right ">
								<div class="collapse navbar-collapse" id="navbarNavDropdown">
									<ul class="navbar-nav justify-content-center menu-login">
									  <!-- <li><a href=""><img src="<?php echo USER_IMAGES ?>heart.jpg"><b>Wishlist</b></a></li> -->
									  <!-- <li class="nav-item dropdown">
									      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown"><img src="<?php echo USER_IMAGES ?>account.png"><b>My Account</b></a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="#">My Account</a>
									        <a class="dropdown-item" href="#">My Whislist</a>
									        <a class="dropdown-item" href="#">Register</a>
									        <a class="dropdown-item" href="#">Login</a>
									      </div>
									  </li> -->
									  <li><a href="/checkout/"><img src="<?php echo USER_IMAGES ?>bag.jpg"><span>0</span><b>Cart</b></a></li>
									  
									</ul>
								</div>
							</nav>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="header-box dn-l">
				<nav class="navbar navbar-expand-lg menu-top ">
					<a href="" class="logo-mob"><img src="<?php echo USER_IMAGES ?>logo.jpg" class="logo-mb"></a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-icon"></span>
					<span class="navbar-icon"></span>
					<span class="navbar-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarNavDropdown">
						<ul class="navbar-nav menu-ul">
						  
							<li class="nav-item ">
								<a class="nav-link" href="/about-us/" >The Brand</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/faq/" >Jewery Faq</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="/how-to-wear/" >How to wear</a>
							</li>

							<li class="nav-item ">
								<a class="nav-link" href="#" >Gemstone Pop Sockets</a>
							</li>
							<li class="nav-item ">
								<a class="nav-link" href="#" >Studs & Climber</a>
							</li>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Ladies
								</a>
								<div class="dropdown-menu">
									<a class="dropdown-item" href="#">Best Seller</a>
									<a class="dropdown-item" href="#">Gemstone</a>
									<a class="dropdown-item" href="#">Essential</a>
								</div>
							</li>
							<!-- <li class="nav-item">
								<a class="nav-link" href="#">Login</a>
							</li> -->
							<li class="nav-item ">
								<a class="nav-link" href="#" >Studs & Climber</a>
							</li>

						</ul>
					</div>
				</nav>
			</div>
		</header>
		<!-- header end -->
