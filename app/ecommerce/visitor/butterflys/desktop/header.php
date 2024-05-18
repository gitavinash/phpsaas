<?php

if ($productCategory = mysqli_query($connection, "SELECT category_title, category_image, link_url FROM " . TABLE_PREFIX . "category INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=category_id WHERE link_type='category' AND link_status=200 AND link_site_id=" . SITE_ID)) {

    if (mysqli_num_rows($productCategory) != 0) {
        $productCategoryData = array();
        while ($data = mysqli_fetch_array($productCategory)) {
            $productCategoryData[] = $data;
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <base href="<?php echo SITE_URL ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php require_once DEFAULT_META; ?>
    <style>
    :root {
        --theme-color: <?php echo SITE_THEME_COLOUR ?>;
    }
    </style>
    <!-- css -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>zoom-gallery/zoom.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>

<body>

    <!-- header start -->
    <header>
        <!-- <div class="header-top">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="web-detail dn-s">
                            <p>Latest Update: Delivery available in Delhi NCR & Kolkata only</p>
                        </div>

                    </div>
                </div>
            </div>
        </div> -->
        <div class="header-mid dn-s">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-2">
                        <div class="logo">
                            <a href="/"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></a>
                        </div>
                    </div>
                    <div class="col-md-3">

                    </div>
                    <div class="col-md-7">
                        <div class="search-box">
                            <form action="/search/" class="form-inline">
                                <input class="form-control" name="q" type="search"
                                    value="<?php echo ((isset($_GET['q'])) ? $_GET['q'] : ''); ?>"
                                    placeholder="Search...">
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
                                        }

                                        ?>
                                        <li><a href="/checkout/"><img
                                                    src="<?php echo USER_IMAGES ?>cart.svg"></a></a><span
                                                class="crt-crcl"
                                                totalCartItems="<?php $cartItems ?>"><?php echo $cartItems ?>

                                            </span><span class="span-item">Cart</span></li>
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
                            <a href="" class="dn-l"><img src="<?php echo SITE_LOGO ?>" class="logo-mb"></a>
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
                                    <li class="nav-item">
                                        <a class="nav-link" href="/about-us/">About us</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbardrop"
                                            data-toggle="dropdown">Our Collections
                                        </a>
                                        <div class="dropdown-menu mega-menu">
                                            <div class="mega1">
                                                <?php

                                                for ($i = 0; $i < sizeof($productCategoryData); $i++) {
                                                    extract($productCategoryData[$i]);
                                                    echo "<a class='dropdown-item' href='{$link_url}'>{$category_title}</a>";
                                                }

                                                ?>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/contact-us/">Contact us</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header end -->