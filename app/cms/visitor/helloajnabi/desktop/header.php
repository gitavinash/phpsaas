<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'}.' | '.SITE_NAME ) : SITE_DESC.' | '.SITE_NAME ) ?></title>
		<meta name="google-site-verification" content="HeLXduc1QTuVs_ZJRPnqJqm0gn-Ztp973cQC95nNjgM" />
        <link href="https://fonts.googleapis.com/css2?family=Mukta&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>owlcarousel2/owlcarousel.min.css" />
		<link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css"/>
		<?php

		if(isset($link_type)) {

			echo (($link_type=='page' || $link_type=='category' || $link_type=='tag') ? "<link rel='stylesheet' href='".USER_STYLESHEET."inner-page.min.css'/>" : '');

		}

		?>

        <?php require_once DEFAULT_META; ?>

		<link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
		<!-- <link type="text/css" rel="stylesheet" href="<?php echo USER_STYLESHEET ?>inner-page.css"/> -->
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-91858004-1"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-91858004-1');
		</script>
		<script data-ad-client="ca-pub-7243832611250872" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<script async type="text/javascript" src="//asset.fwcdn2.com/js/embed-feed.js"></script>
		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-MVDFRXZ');</script>

		<!-- facebook pixel -->

		<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '111545552801423');
		fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=111545552801423&ev=PageView&noscript=1"
		/></noscript>

    </head>
	<body>
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MVDFRXZ" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	
		<!-- Header -->
		<header id="header">
			<!-- <div id="top-header">
				<div class="container">
					<div class="header-links">
						<ul>
							<li><a href="/about-us/">About us</a></li>
							<li><a href="/contact-us/">Contact us</a></li>
							<li><a href="/advertisement/">Advertisement</a></li>
							<li><a href="/privacy-policy/">Privacy policy</a></li>
						</ul>
					</div>
					<div class="header-social">
						<ul>
							<li><a href="https://www.facebook.com/helloajnabi/" target="black"><i class="fab fa-facebook"></i></a></li>
							<li><a href="https://twitter.com/Hello_Ajnabi" target="black"><i class="fab fa-twitter"></i></a></li>
							<li><a href="https://www.instagram.com/helloajnabi/" target="black"><i class="fab fa-instagram"></i></a></li>
							<li><a href="https://www.youtube.com/channel/UC507HwLRcMaAqUNFTzSEK3w?sub_confirmation=1"><i class="fab fa-youtube"></i></a></li>
						</ul>
					</div>
				</div>
			</div> -->
			<div id="center-header">
				<div class="container">
					<div class="header-logo">
						<a href="/" class="logo"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
					</div>
				</div>
			</div>
			<!-- Nav Header -->
			<div id="nav-header">
				<div class="container">
					<nav id="main-nav">
						<div class="nav-logo">
							<a href="/" class="logo"><img src="<?php echo USER_IMAGES ?>logo-alt.png" alt=""></a>
						</div>
						<ul class="main-nav nav navbar-nav">
							<li class="active"><a href="/">Home</a></li>
							<li><a href="/category/bollywood-and-tv/">Bollywood & TV</a></li>
							<li><a href="/category/sports/">Sports</a></li>
							<li><a href="/category/breaking-news/">Breaking news</a></li>
							<li><a href="/category/interesting/">Intersting</a></li>
							<li><a href="/category/food/">Food</a></li>
							<li><a href="/category/astrology/">Astrology</a></li>
							<li><a href="/category/politics/">Politics</a></li>
							<li><a href="/category/spiritual/">Spiritual</a></li>
							<li><a href="/category/viral/">Viral</a></li>
							<li><a href="/category/stories/">Stories</a></li>
						</ul>
					</nav>
					<div class="button-nav">
						<button class="search-collapse-btn"><i class="fa fa-search"></i></button>
						<button class="nav-collapse-btn"><i class="fa fa-bars"></i></button>
						<div class="search-form">
							<form action="/search/" class="form-inline">
								<input class="form-control" name="q" type="search" value="" placeholder="Search...">
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- /Nav Header -->
		</header>
		<!-- /Header --> 