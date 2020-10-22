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
		<link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>flickity.min.css">
		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
		<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600&display=swap" rel="stylesheet">
		        
        <!-- SEO -->

        <meta name="theme-color" content="<?php echo SITE_THEME_COLOUR ?>">
        <meta name='robots' content='<?php echo $meta_robots ?>' />
        <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
        <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
        <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
        <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">

		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-55MD564');</script>
		<!-- End Google Tag Manager -->

		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-176806223-1"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-176806223-1');
		</script>
		
		<!-- Pixel Code for https://socialproof.zaperp.com/ -->

		<script async src="https://socialproof.zaperp.com/pixel/b8d4b3a5e4abe7b606dac596510cfc02"></script>

		<script src="//cdn.letsetcom.io/js/main/0d979598f98a68caf8697f0f1cdf8d83.js"></script>

		<!-- END Pixel Code -->

	</head>
	<body>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-55MD564"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
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
									      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Our Products
									      </a>
									      <div class="dropdown-menu">
									        <a class="dropdown-item" href="/immunity-vitals/">Immunity Vitals</a>
									        <a class="dropdown-item" href="/tar-free/">Tar Free</a>
									        <a class="dropdown-item" href="/gyno-vitals/">Gyno Vitals</a>
									        <a class="dropdown-item" href="/v-liver-vitals/">V-Liver Vitals</a>
									      </div>
									  </li>
									  <li class="nav-item"><a class="nav-link" href="/our-story/">Our Story</a></li>
									  <li class="nav-item"><a class="nav-link" target="_blank" href="https://vedicvitals.ordr.live/">Track Order</a></li>
									  <li class="nav-item"><a class="nav-link" href="/contact-us/">Contact Us</a></li>
									</ul>

								<div class="search-pro">
									<nav class="navbar navbar-expand-lg login-section justify-content-right ">
										<div class="collapse navbar-collapse" id="navbarNavDropdown">
											<ul class="navbar-nav justify-content-center menu-login">
											  <!-- <li class="searc-div ">
											  	<form action="" autocomplete="on">
												  <input id="search" name="search" type="text" placeholder="What're we looking for ?"><input id="search_submit" value="Rechercher" type="submit">
												 </form>
											  </li> -->
											  <li class="nav-item">
											      <a href="/login/" id="navbardrop" ><!-- data-toggle="dropdown"<i class="fa fa-user" aria-hidden="true"></i> --><svg aria-hidden="true" focusable="false" role="presentation" class="icon-a icon-login" viewBox="0 0 28.33 37.68"><path d="M14.17 14.9a7.45 7.45 0 1 0-7.5-7.45 7.46 7.46 0 0 0 7.5 7.45zm0-10.91a3.45 3.45 0 1 1-3.5 3.46A3.46 3.46 0 0 1 14.17 4zM14.17 16.47A14.18 14.18 0 0 0 0 30.68c0 1.41.66 4 5.11 5.66a27.17 27.17 0 0 0 9.06 1.34c6.54 0 14.17-1.84 14.17-7a14.18 14.18 0 0 0-14.17-14.21zm0 17.21c-6.3 0-10.17-1.77-10.17-3a10.17 10.17 0 1 1 20.33 0c.01 1.23-3.86 3-10.16 3z"></path></svg>
											      </a>
											      <!-- <div class="dropdown-menu">
											        <a class="dropdown-item" href="#">My Account</a>
											        <a class="dropdown-item" href="#">My Whislist</a>
											        <a class="dropdown-item" href="#">Register</a>
											        <a class="dropdown-item" href="#">Login</a>
											      </div> -->
											  </li>
											  <li> <a href="/checkout/"><!-- <i class="fa fa-shopping-bag" aria-hidden="true"></i> -->
											  	<svg aria-hidden="true" focusable="false" role="presentation" class="icon-a icon-cart" viewBox="0 0 37 40"><path d="M36.5 34.8L33.3 8h-5.9C26.7 3.9 23 .8 18.5.8S10.3 3.9 9.6 8H3.7L.5 34.8c-.2 1.5.4 2.4.9 3 .5.5 1.4 1.2 3.1 1.2h28c1.3 0 2.4-.4 3.1-1.3.7-.7 1-1.8.9-2.9zm-18-30c2.2 0 4.1 1.4 4.7 3.2h-9.5c.7-1.9 2.6-3.2 4.8-3.2zM4.5 35l2.8-23h2.2v3c0 1.1.9 2 2 2s2-.9 2-2v-3h10v3c0 1.1.9 2 2 2s2-.9 2-2v-3h2.2l2.8 23h-28z"></path></svg></a>
											  	<span id="cartNumber" >0</span></li>
											  
											  
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