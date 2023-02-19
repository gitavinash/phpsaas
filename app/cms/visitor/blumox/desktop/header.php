<!DOCTYPE html>
<html>

<head>
    <base href="<?php echo SITE_URL ?>">
    <meta charset="utf-8">
    <link rel="shortcut icon" href=" ">
    <meta name="" content="">
    <meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1,initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo USER_IMAGES ?>favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo USER_IMAGES ?>favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo USER_IMAGES ?>favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo USER_IMAGES ?>favicon/site.webmanifest">
    <link rel="mask-icon" href="<?php echo USER_IMAGES ?>favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#3ab4f1">
    <meta name="theme-color" content="#3ab4f1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php require_once DEFAULT_META; ?>

    <!-- css -->
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STYLESHEET ?>style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.min.css">
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Righteous&display=swap" rel="stylesheet">

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
        })(window, document, "clarity", "script", "fvi9wdlvm3");
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZBK4Z8XT71"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-ZBK4Z8XT71');
    </script>

</head>

<body>

    <!-- header start -->
    <header>
        <?php
        if (ACTIVE_URL != '/') {
            echo "<div class='logo' ><a href='/'><img src='" . USER_IMAGES . "logo.png' alt='" . SITE_NAME . "' ></a></div>";
        }
        ?>
        <div class="menu">
            <span></span>
        </div>
        <div class="menuBox">
            <section>
                <div class="menuList">
                    <ul>
                        <li><a href="/about-us/">About</a></li>
                        <li><a href="/our-work/">Work</a></li>
                        <li><a href="/products/">Products</a></li>
                        <li><a href="/services/">Services</a></li>
                        <li><a href="/contact-us/">Contact Us</a></li>
                    </ul>
                </div>
                <div class="legalLinks">
                    <ul>
                        <li><a href="/privacy-policy/">Privacy Policy</a></li>
                        <li><a href="/refund-policy/">Refund Policy</a></li>
                        <li><a href="/terms-and-conditions/">Terms and Conditions</a></li>
                        <li><a href="/sitemap/" target="_blank">Sitemap</a></li>
                        <li>&copy; 2022 - <?php echo date('Y'); ?> Blumox | a Unit of ITS Soft World</li>
                    </ul>
                </div>
            </section>
            <div class="menu closeMenu">
                <span></span>
            </div>
        </div>
    </header>
    <!-- header end -->