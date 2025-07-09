<!DOCTYPE html>
<html>

<head>

    <base href="<?php echo SITE_URL ?>">
    <meta charset="utf-8">
    <link rel="shortcut icon" href=" ">
    <meta name="" content="">
    <meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
    <title>
        <?php echo ((isset($meta_title)) ? (($meta_title != '') ? $meta_title : ${$link_type . '_title'} . ' | ' . SITE_NAME) : SITE_DESC . ' | ' . SITE_NAME) ?>
    </title>

    <!-- css -->
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@200;400;600&family=Lato&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>zoom-gallery/zoom.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <?php require_once DEFAULT_META; ?>

    <meta name="google-site-verification" content="HT7OlsigSJmSLCG9PCz7JmCbyRGmzUeJapljsV4KRIg" />

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-69G2SPCWHX"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-69G2SPCWHX');
    </script>

    <!-- Global site tag (gtag.js) - Google Ads: 628904231 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-628904231"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'AW-628904231');
    </script>

    <script type="text/javascript">
    (function(c, l, a, r, i, t, y) {
        c[a] = c[a] || function() {
            (c[a].q = c[a].q || []).push(arguments)
        };
        t = l.createElement(r);
        t.async = 1;
        t.src = "https://www.clarity.ms/tag/" + i;
        y = l.getElementsByTagName(r)[0];
        y.parentNode.insertBefore(t, y);
    })(window, document, "clarity", "script", "dw3u1ggui2");
    </script>

    <?php

    if (BASE_URL == '/thank-you/') {

        if (isset($id)) {

            if ($id != '') {

    ?>

    <script>
    gtag('event', 'conversion', {
        'send_to': 'AW-628904231/OgMhCLfdtdIBEKei8asC',
        'value': 1,
        'currency': 'INR',
        'transaction_id': ''
    });
    </script>
    <?php

            }
        }
    }

    ?>

    <!-- Meta Pixel Code -->
    <script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '577779820444463');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=577779820444463&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->

    <!-- Google Tag Manager -->
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-K46P78T');
    </script>
    <!-- End Google Tag Manager -->
    <?php require_once GOOGLE_RECAPTCHA_GENERATOR; ?>
</head>

<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K46P78T" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- header start -->
    <header>
        <div class="header-top">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="web-detail dn-s">
                            <!-- <p><strong>Women's Day </strong>is on March 8, 2025! Explore our special collection & order in advance for timely delivery. 💐</p> -->
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
                            <a href="/"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></a>
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
                                <input class="form-control" name="q" type="search"
                                    value="<?php echo ((isset($_GET['q'])) ? $_GET['q'] : ''); ?>"
                                    placeholder="Search for gifts, flowers...">
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

                                        <?php

                                        if ($cartNumber = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "product ON cart_relation_id=product_id WHERE cart_session_id='" . SESSION_ID . "' AND cart_status=0 AND product_status=1 AND cart_site_id=" . SITE_ID)) {

                                            $cartItems = mysqli_num_rows($cartNumber);
                                        } else {
                                            $cartItems = 0;
                                        }

                                        ?>
                                        <li><a href="/checkout/"><img
                                                    src="<?php echo USER_IMAGES ?>cart.svg"></a></a><span
                                                class="crt-crcl" id="cartItems"
                                                totalCartItems="<?php $cartItems ?>"><?php echo $cartItems ?>

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
                    <div class="col-md-12 mobile-bg">
                        <nav class="navbar navbar-expand-lg menu-top fixed">
                            <a href="" class="dn-l"><img src="<?php echo USER_IMAGES ?>logo1.png" class="logo-mb"></a>
                            <button class="navbar-toggler bar-mnu" type="button" data-toggle="collapse"
                                data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <span class="navbar-icon bar-1"></span>
                                <span class="navbar-icon bar-2"></span>
                                <span class="navbar-icon bar-1"></span>
                                <!-- <img src="<?php echo USER_IMAGES ?>menu.svg"> -->
                            </button>
                            <div class="mob-menu dn-l">
                                <div class="cart-mob">
                                    <!-- <a href="" class="mob-bag sr-mob" id="searchBtn"><img
                                            src="<?php echo USER_IMAGES ?>search-fa.svg"></a></span> -->
                                    <a href="/checkout/" class="mob-bag"><img
                                            src="<?php echo USER_IMAGES ?>cart.svg"></a><span class="crt-crcl">

                                        <?php

                                        if ($cartNumber = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "product ON cart_relation_id=product_id WHERE cart_session_id='" . SESSION_ID . "' AND cart_status=0 AND product_status=1")) {

                                            echo mysqli_num_rows($cartNumber);
                                        }

                                        ?>

                                    </span>
                                </div>

                            </div>
                            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                                <ul class="navbar-nav menu-ul">

                                    <li class="nav-item">
                                        <a class="nav-link" href="/">Home</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="/tag/luxury/" id="navbardrop"
                                            data-toggle="dropdown">Luxury Collections
                                        </a>
                                        <div class="dropdown-menu mega-menu">
                                            <div class="mega1">
                                                <div class="menu-titl"><a href="">New Arivals<span
                                                            class="cart-bodr"></span></a></div>
                                                <a class="dropdown-item" href="/category/flower/">Fresh Flowers</a>
                                                <a class="dropdown-item" href="/category/artistic-artificial/">Artistic
                                                    Artificial</a>
                                                <a class="dropdown-item" href="/tag/everlasting/">Infinity Everlasting
                                                    Flowers</a>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="/tag/occasions/" id="navbardrop"
                                            data-toggle="dropdown">Occasions
                                        </a>
                                        <div class="dropdown-menu mega-menu">
                                            <div class="mega2">
                                                <div class="mega-gry flot-menu">
                                                    <div class="menu-titl"><a href="#">By Theme<span
                                                                class="cart-bodr"></span></a></div>
                                                    <a class="dropdown-item" href="/tag/anniversary">Anniversary</a>
                                                    <a class="dropdown-item" href="/tag/birthday/">Birthday</a>
                                                    <a class="dropdown-item" href="/tag/corporate-gift/">Corporate
                                                        Gifting</a>
                                                    <a class="dropdown-item"
                                                        href="/tag/congratulation/">Congratulation</a>
                                                    <a class="dropdown-item" href="/tag/get-well/">Get Well</a>
                                                    <a class="dropdown-item" href="/tag/house-warming/">House
                                                        Warming</a>
                                                </div>
                                                <div class="mega-wht flot-menu">
                                                    <div class="menu-titl"><a href="#">By Type<span
                                                                class="cart-bodr"></span></a></div>
                                                    <a class="dropdown-item" href="/tag/i-m-sorry/">I'M Sorry</a>
                                                    <a class="dropdown-item" href="/tag/just-because/">Just Because</a>
                                                    <a class="dropdown-item" href="/tag/new-baby/">New Baby</a>
                                                    <a class="dropdown-item" href="/tag/love/">Love & Romance</a>
                                                    <a class="dropdown-item" href="/tag/retirement/">Retirement</a>
                                                    <a class="dropdown-item" href="/tag/sympathy/">Sympathy</a>
                                                </div>
                                                <div class="mega-gry flot-menu">
                                                    <div class="menu-titl"><a href="/tag/bumper-sale/">Offer <span
                                                                class="cart-bodr"></span></a></div>
                                                    <a class="dropdown-item" href="#">
                                                        <img class="mega-img" src="">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/category/planters/">Planters</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " href="/category/cake/">Cakes &amp; Hampers</a>
                                        <!-- <a class="nav-link " href="/tag/everlasting/"
                                            style="font-weight: 600; color: #D4946E">New Collection</a> -->
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link"
                                            href="/tag/blooms-of-<?php echo strtolower(date('F')) ?>/">Blooms of
                                            <?php echo date('F') ?></a>
                                    </li>
                                    <!-- <li class="nav-item " style="background: #F19EB5;">
                                        <a class="nav-link" style="color: #fff" href="/tag/christmas/"><b>Christmas</b></a>
                                    </li> -->
                                    <!-- <li class="nav-item ">
                                        <a class="nav-link " href="/category/subscription/">Subscription</a>
                                    </li> -->
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbardrop"
                                            data-toggle="dropdown">Quick selections
                                        </a>
                                        <div class="dropdown-menu mega-menu">
                                            <div class="mega1">
                                                <div class="menu-titl"><a href="#">Under price<span
                                                            class="cart-bodr"></span></a></div>
                                                <a class="dropdown-item"
                                                    href="/filter/product/?filterType=price&min=0&max=1999">Under ₹
                                                    1999</a>
                                                <a class="dropdown-item"
                                                    href="/filter/product/?filterType=price&min=0&max=2999">Under ₹
                                                    2999</a>
                                            </div>
                                        </div>
                                    </li>
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
                            <input title="Enter the terms you wish to search for." type="text" id="search_block"
                                name="search_block_form" size="15" maxlength="128"
                                class="form-text ui-autocomplete-input" placeholder="Search for gifts, flowers..."
                                autocomplete="off" value="">
                            <div id="close">x</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header end -->