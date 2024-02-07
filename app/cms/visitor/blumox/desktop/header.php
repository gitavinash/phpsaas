<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>home.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>about.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>work.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>contact.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css"> -->
    <!-- or -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik&family=Sora:wght@200;300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>/slick/slick-theme.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <?php require_once DEFAULT_META; ?>

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
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-00T4DDHZXS"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-00T4DDHZXS');
    </script>

</head>

<body>
    <header>
        <!-- navbar start -->
        <div class="container">
            <div class="custom_nav">
                <div class="mylogo">
                    <a href="/"><img src="<?php echo USER_IMAGES ?>logo.svg" class="img-fluid" alt=""></a>
                </div>
                <div class="myul">
                    <!-- <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/about-us/">About Blumox</a></li>
                        <li><a href="/our-works/">Our works</a></li>
                        <li><a href="/service/">What we do</a></li>
                    </ul> -->
                </div>
                <div class="contact">
                    <a href="/contact-us/">contact us</a>
                </div>
            </div>
        </div>

        <!-- 1200px down navbar -->

        <nav class="navbar navbar-expand-xl navbar-light mynav">
            <div class="container">
                <a class="navbar-brand" href="/"><img src="<?php echo USER_IMAGES ?>logo.svg" class="img-fluid"
                        alt=""></a>

                <button class="navbar-toggler slide" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <!-- <i class='bx bx-menu-alt-right'></i> -->
                    <img src="<?php echo USER_IMAGES ?>svg/menu_icon.svg" class="img-fluid" alt="">
                </button>
                <div class="collapse navbar-collapse home_menu" id="navbarNav">
                    <ul class="navbar-nav">
                        <li><a href="/">home</a></li>
                        <li><a href="/about/">about us</a></li>
                        <li><a href="">products</a></li>
                        <li><a href="/work/">work</a></li>
                        <li><a href="/service/">services</a></li>
                        <li><a href="#">pay now</a></li>
                    </ul>
                    <div class="contact">
                        <a href="/contact/">contact us</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="mobile-nav ">
            <div class="company_logo">
                <a href="/"><img src="<?php echo USER_IMAGES ?>logo.svg" class="img-fluid" alt=""></a>
            </div>
            <div class="row p-2 ">

                <div class="col-12 d-flex align-items-center justify-content-end ">

                    <!-- <div class="crose"> <i class='bx bx-x icon-manage' id="box-change"></i> </div> -->
                    <div class="crose"> <img src="<?php echo USER_IMAGES ?>png/close.png" id="box-change"
                            class="img-fluid" alt=""></i> </div>
                </div>
                <div class="col-12 home-nav is_font_18 font_f_libre">
                    <ul>
                        <li><a href="/">home</a></li>
                        <li><a href="/about/">about us</a></li>
                        <li><a href="">products</a></li>
                        <li><a href="/work/">work</a></li>
                        <li><a href="/service/">services</a></li>
                        <li><a href="#">pay now</a></li>
                    </ul>
                    <div class="contact">
                        <a href="/contact/">contact us</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 1200px down navbar end-->
    </header>