<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/css/fontawesome.min.css" integrity="sha384-zIaWifL2YFF1qaDiAo0JFgsmasocJ/rqu7LKYH8CoBEXqGbb9eO+Xi3s6fQhgFWM" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css" integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>checkout.css">
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
        })(window, document, 'script', 'dataLayer', 'GTM-NMRSL3N');
    </script>
    <!-- End Google Tag Manager -->
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NMRSL3N" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <section class="home-hero">
        <!-- navbar -->
        <header>
            <nav>
                <div class="wrapper">
                    <div class="logo"><a href="/"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a></div>
                    <input type="radio" name="slider" id="menu-btn">
                    <input type="radio" name="slider" id="close-btn">
                    <ul class="nav-links">
                        <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
                        <li><a href="/">Home</a></li>
                        <li>
                            <a href="/shop/" class="desktop-item">Shop</a>
                            <input type="checkbox" id="showMega">
                            <label for="showMega" class="mobile-item">Shop</label>
                            <div class="mega-box">
                                <div class="content">
                                    <!-- <div class="row">
                                        <img src="https://tinypic.host/images/2022/12/19/img_avatar.png" alt="">
                                    </div> -->
                                    <div class="row">
                                        <header>Home Theaters Packages</header>
                                        <ul class="mega-links">
                                            <li><a href="#">5.1 Solutions</a></li>
                                            <li><a href="#">7.1 Solutions</a></li>
                                        </ul>
                                        <header>Projector</header>
                                        <ul class="mega-links">
                                            <li><a href="">Full HD</a></li>
                                            <li><a href="">4k Projectors</a></li>
                                            <li><a href="">Projector Screens</a></li>
                                            <li><a href="">Projector Accessories</a></li>
                                        </ul>
                                    </div>
                                    <div class="row">
                                        <header>Hi-Fi Speakers</header>
                                        <ul class="mega-links">
                                            <li><a href="#">Tower Speakers</a></li>
                                            <li><a href="#">Floor Speakers</a></li>
                                            <li><a href="#">Ceiling Speakers</a></li>
                                        </ul>
                                    </div>
                                    <div class="row">
                                        <header>Audio Componenets</header>
                                        <ul class="mega-links">
                                            <li>
                                                <a href="">AV Receivers</a>
                                            </li>
                                            <li>
                                                <a href="">Power
                                                    Amplifiers</a>
                                            </li>
                                            <li>
                                                <a href="">Integrated Amplifiers</a>
                                            </li>
                                            <li>
                                                <a href="">Pre-Amplifiers</a>
                                            </li>
                                            <li>
                                                <a href="">CD Players</a>
                                            </li>
                                            <li>
                                                <a href="">Streamers</a>
                                            </li>
                                            <li>
                                                <a href="">Digital to Analogue
                                                    Convertors (DACs)</a>
                                            </li>
                                            <li>
                                                <a href="">Power
                                                    Conditioners</a>
                                            </li>
                                            <li>
                                                <a href="">Network Switches</a>
                                            </li>
                                            <li>
                                                <a href="">Electronics
                                                    Accessories</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="row">
                                        <header>Turntables</header>
                                        <ul class="mega-links">
                                            <li><a href="#">Turntables</a></li>
                                            <li><a href="#">Cartridges</a></li>
                                        </ul>
                                        <header>Vinyl Records</header>
                                        <ul class="mega-links">
                                            <li><a href="">All Vinyl</a></li>
                                            <li><a href="">Limited Edition</a></li>
                                        </ul>
                                        <header>Digital Audio</header>
                                        <ul class="mega-links">
                                            <li><a href="">Soundbars</a></li>
                                            <li><a href="">Powered Bookshelf Speakers</a></li>
                                            <li><a href="">Bluetooth Speakers</a></li>
                                            <li><a href="">Wireless Multiroom Speakers</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="desktop-item">Brands</a>
                            <input type="checkbox" id="showDrop">
                            <label for="showDrop" class="mobile-item">Brands</label>
                            <ul class="drop-menu">
                                <li><a href="#">Premium Brands</a></li>
                                <li><a href="#">Featured Brands</a></li>
                            </ul>
                        </li>
                        <li><a href="https://lp.altimaglobal.com/hometheatre?gclid=EAIaIQobChMI-viVypmU_QIVrpNmAh1DQw83EAAYASAAEgJTH_D_BwE">Turnkey
                                Solutions</a></li>
                        <li><a href="/soundtrails-experience/" target="_blank">SoundTrails Experience</a></li>
                        <li><a href="/about-us/">Why Us</a></li>
                        <li><a href="/contact-us/">Reach Us</a></li>
                    </ul>
                    <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
                </div>
            </nav>

            <!-- <div class="bg-white serach-box d-none d-lg-block">
                <div>

                    <nav class="navbar ">
                        <div class="container-fluid">
                            <a class="navbar-brand d-none d-lg-block" href="/"><img
                                    src="<?php echo USER_IMAGES ?>logo.png" alt="" class="img-fluid" /></a>
                            <div class="row align-items-center d-flex w-75">
                                <div class="col-8 col-lg-8">
                                    <input type="text" class="forms text-white " placeholder=" Search your product" />
                                </div>
                                <div class="col-4 col-lg-4">
                                    <button class="btn1 text-white rounded-pill w-100">Serach</button>
                                </div>


                            </div>
                            <div class="serach-cross  text-white d-none d-sm-block">
                                <i class="fa-solid fa-xmark "></i>
                            </div>


                        </div>
                    </nav>
                </div>
            </div> -->

            </div>
            </div>
        </header>