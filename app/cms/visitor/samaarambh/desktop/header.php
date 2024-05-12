<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Samaarambh Foundation</title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo USER_IMAGES ?>logo.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo USER_IMAGES ?>logo.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo USER_IMAGES ?>logo.png" />
    <!-- <link rel="manifest" href="<?php echo USER_IMAGES ?>favicons/site.webmanifest" /> -->
    <meta name="description" content="Samaarambh Foundation" />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap" rel="stylesheet">


    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', str_replace('css/', 'js/', USER_STYLESHEET)) ?>vendors/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/animate/animate.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/animate/custom-animate.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/fontawesome/css/all.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/jarallax/jarallax.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/jquery-magnific-popup/jquery.magnific-popup.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/nouislider/nouislider.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/nouislider/nouislider.pips.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/odometer/odometer.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/swiper/swiper.min.css" />
    <link rel="stylesheet" href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/icons/style.css">
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/tiny-slider/tiny-slider.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/reey-font/stylesheet.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/owl-carousel/owl.carousel.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/owl-carousel/owl.theme.default.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/bxslider/jquery.bxslider.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/bootstrap-select/css/bootstrap-select.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/vegas/vegas.min.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet"
        href="<?php echo str_replace('css/', 'js/', USER_STYLESHEET) ?>vendors/timepicker/timePicker.css" />

    <!-- template styles -->
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global-responsive.css" />
</head>

<body class="custom-cursor">

    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>





    <div class="preloader">
        <div class="preloader__image"></div>
    </div>
    <!-- /.preloader -->


    <div class="page-wrapper">
        <header class="main-header">
            <nav class="main-menu">
                <div class="main-menu__wrapper">
                    <div class="main-menu__wrapper-inner">
                        <div class="main-menu__left">
                            <div class="main-menu__logo">
                                <a href="/"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
                            </div>
                            <div class="main-menu__shape-1 float-bob-x">
                                <img src="<?php echo USER_IMAGES ?>shapes/main-menu-shape-1.png" alt="">
                            </div>
                        </div>
                        <div class="main-menu__right">
                            <div class="main-menu__right-top">
                                <div class="main-menu__right-top-left">
                                    <div class="main-menu__volunteers">
                                        <div class="main-menu__volunteers-icon">
                                            <img src="<?php echo USER_IMAGES ?>icon/main-menu-heart-icon.png" alt="">
                                        </div>
                                        <div class="main-menu__volunteers-text-box">
                                            <p class="main-menu__volunteers-text"><a href="contact-us/">Become
                                                    a
                                                    <span>volunteer</span></a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="main-menu__right-top-right">
                                    <div class="main-menu__right-top-address">
                                        <ul class="list-unstyled main-menu__right-top-address-list">
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-phone-call"></span>
                                                </div>
                                                <div class="content">
                                                    <p>Helpline</p>
                                                    <h5><a href="tel:9999419333">+91-9999419333</a></h5>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-message"></span>
                                                </div>
                                                <div class="content">
                                                    <p>Send email</p>
                                                    <h5><a
                                                            href="mailto:mail.samaarambh@gmail.com">mail.samaarambh@gmail.com</a>
                                                    </h5>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="icon">
                                                    <span class="icon-location"></span>
                                                </div>
                                                <div class="content">
                                                    <p>Registered office</p>
                                                    <h5>B4/320 C, Keshav Puram, New Delhi
                                                    </h5>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="main-menu__right-top-social">
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                        <a href="#"><i class="fab fa-facebook"></i></a>
                                        <a href="#"><i class="fab fa-pinterest-p"></i></a>
                                        <a href="#"><i class="fab fa-instagram"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="main-menu__right-bottom">
                                <div class="main-menu__main-menu-box">
                                    <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                                    <ul class="main-menu__list">
                                        <li
                                            class="dropdown megamenu <?php echo ((ACTIVE_URL == '/') ? 'current' : '') ?>">
                                            <a href="/">Home </a>
                                            <ul>
                                                <li>
                                                    <section class="home-showcase megamenu-left">
                                                        <div class="container"></div>
                                                    </section>
                                                </li>
                                            </ul>
                                        </li>
                                        <li
                                            class="dropdown <?php echo ((ACTIVE_URL == '/about-us/') ? 'current' : '') ?>">
                                            <a href=" about-us/">About us</a>
                                        </li>
                                        <li <?php echo ((ACTIVE_URL == '/contact-us/') ? 'class="current"' : '') ?>">
                                            <a href="contact-us/">Contact</a>
                                        </li>
                                        <!-- add new menu using
                                    
                                            <li>
                                                <a href="{{path_of_html_file}}">{{Menu title}}</a>
                                            </li>

                                        -->
                                    </ul>
                                </div>
                                <div class="main-menu__main-menu-content-box">
                                    <div class="main-menu__search-cat-btn-box">
                                        <div class="main-menu__btn-box">
                                            <a href="contact-us/" class="main-menu__btn"> <span
                                                    class="fa fa-heart"></span> Donate
                                                now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <div class="stricky-header stricked-menu main-menu">
            <div class="sticky-header__content"></div><!-- /.sticky-header__content -->
        </div><!-- /.stricky-header -->