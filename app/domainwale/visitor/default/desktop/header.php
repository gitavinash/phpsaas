<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Poppins:wght@100;300;400;500;600;700;800;900&display=swap"
        media="screen">
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_LIBRARY ?>uicons-regular-rounded.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.min.css">

    <?php require_once DEFAULT_META ?>

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
        })(window, document, 'script', 'dataLayer', 'GTM-5WND545');
    </script>
    <!-- End Google Tag Manager -->


</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5WND545" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <header class="l-w1">
        <!-- <div class="notification-bar center">Welcome to <?php echo SITE_NAME ?>.</div> -->
        <section class="desktop">
            <div class="main-bar l-w1">
                <div class="container">
                    <div class="left-box fl">
                        <ul class="list-menu">
                            <li><a href="tel:+91-8082026069" target='_blank'><i class="fi-rr-headset"></i>
                                    +91-8802026069</a></li>
                            <li><a href="mailto:support@oxteal.com" target='_blank'><i class="fi-rr-envelope"></i>
                                    support@oxteal.com</a></li>
                            <!-- <li><a href="https://wa.me/918802026069?text=Hello%0aI%20need%20a%20help." target='_blank' ><i class="fab fa-whatsapp"></i> +91-8802026069</a></li> -->
                        </ul>
                    </div>
                    <div class="right-box fr">
                        <ul class="list-menu">
                            <li><a href="https://dashboard.oxteal.com/"><i class="fi-rr-user"></i> Login / Signup</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="logo fl">
                    <a href="/"><img src="<?php echo USER_IMAGES ?>oxteal-logo-dark.png"
                            alt="<?php echo SITE_NAME ?> logo"></a>
                </div>
                <div class="menu fr">
                    <ul class="list-menu">
                        <li><a href="/domain-name-registration/">Domain</a></li>
                        <li><a href="/linux-web-hosting/">Web Hosting</a></li>
                        <li><a href="/cloud-web-hosting/">Cloud Hosting</a></li>
                        <li><a href="/wordpress-hosting/">WordPress Hosting</a></li>
                        <li><a href="/reseller-hosting/">Reseller Hosting</a></li>
                        <li><a href="/website-amc/">Website AMC</a></li>
                        <!-- <li><a href="https://blog.oxteal.com/"><i class="fas fa-blog"></i> Blog</a></li> -->
                        <li><a href="/checkout/">Cart <i class="fi-rr-shopping-cart"></i></a></li>
                    </ul>
                </div>
            </div>
        </section>
        <section class="mobile">
            <div class="container">
                <div class="logo l-w3 fl">
                    <a href="/"><img src="<?php echo USER_IMAGES ?>oxteal-logo-dark.png"
                            alt="<?php echo SITE_NAME ?> Logo"></a>
                </div>
                <div class="menu fr">
                    <ul class="list-menu">
                        <li><a href="/support/"><i class="fi-rr-interrogation"></i></a></li>
                        <li><a href="https://dashboard.oxteal.com/"><i class="fi-rr-user"></i></a></li>
                        <li><a href="/checkout/"><i class="fi-rr-shopping-cart"></i></a></li>
                        <li><a href="#" id="openNav"><i class="fi-rr-menu-burger"></i></a></li>
                    </ul>
                </div>

                <!-- full screen mobile -->

                <div class="mobile-menu" id="mobileNav">
                    <a href="#" class="closebtn" id="closeNav"><i class="fi-rr-cross-small"></i></a>
                    <div class="menu fl">
                        <ul class="list-menu">
                            <li><a href="/linux-web-hosting/"><i class="fi-rr-database"></i> Web Hosting <i
                                        class="fi-rr-arrow-small-right fr"></i></a></li>
                            <li><a href="/cloud-web-hosting/"><i class="fi-rr-network-cloud"></i> Cloud Hosting <i
                                        class="fi-rr-arrow-small-right fr"></i></a></li>
                            <li><a href="/wordpress-hosting/"><i class="fi-rr-database"></i> WordPress Hosting
                                    <i class="fi-rr-arrow-small-right fr"></i></a></li>
                            <li><a href="/reseller-hosting/"><i class="fi-rr-database"></i> Reseller Hosting
                                    <i class="fi-rr-arrow-small-right fr"></i></a></li>
                            <li><a href="/website-amc/"><i class="fi-rr-pulse"></i> Website AMC <i
                                        class="fi-rr-arrow-small-right fr"></i></a></li>
                            <li><a href="/whois-lookup/"><i class="fi-rr-world"></i> WHOIS <i
                                        class="fi-rr-arrow-small-right fr"></i></a></li>
                            <!-- <li><a href="https://blog.oxteal.com/"><i class="fi-rr-book-alt"></i> Blog <i
                                        class="fi-rr-arrow-small-right fr"></i></a></li> -->
                        </ul>
                    </div>
                </div>

            </div>
        </section>
    </header>