<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.css" />
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.css" />
    <link href="<?php echo CSS_LIBRARY ?>bootstrap_5/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <?php require_once DEFAULT_META ?>
</head>

<body>

    <header>
        <nav>
            <!-- <div class="top_nav">
                <p><strong>40-45% off</strong> everything online & in stores</p>
            </div> -->
            <div class="myNav">
                <div class="container">
                    <div class="fixed_nav">
                        <div class="menu_icon">
                            <i class='bx bx-menu'></i>
                        </div>
                        <div class="logo">
                            <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt=""></a>
                        </div>
                        <div class="cart">
                            <a href="#"><i class='bx bxs-cart-alt'></i></a>
                            <div class="cart_no">
                                <p>0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php

            $showBottomBar = ((isset($link_type)) ? $link_type : 'normal');

            if ($showBottomBar == 'product') { ?>
                <div class="bottom_nav singel_product__nav">
                    <div class="bottom_nav__flex singel_product__flex">
                        <div class="bottom_nav__item our_cart">
                            <button>
                                <i class="bx bxs-cart-alt"></i>add to cart
                            </button>
                        </div>
                        <div class="bottom_nav__item buy_now">
                            <button>
                                <i class="bx bxs-cart-download"></i>buy now
                            </button>
                        </div>
                    </div>
                </div>
            <?php
            } else { ?>
                <div class="bottom_nav">
                    <div class="container">
                        <div class="bottom_nav__flex">
                            <div class="bottom_nav__item">
                                <a href="/" class="active_menu"><i class='bx bxs-home'></i>Home</a>
                            </div>
                            <div class="bottom_nav__item">
                                <a href="/categories/"><i class='bx bx-category'></i>category</a>
                            </div>
                            <div class="bottom_nav__item search">
                                <a href="#"><i class='bx bx-search'></i>Search</a>
                            </div>


                            <div class="bottom_nav__item menu">
                                <a href="#"><i class='bx bx-dots-vertical-rounded'></i>Menu</a>
                                <div class="menu_container">
                                    <div class="menu_container__box">
                                        <a href="/">Home</a>
                                        <a href="/about-us/">About</a>
                                        <a href="/categories/">Category</a>
                                        <a href="/contact-us/">Contact us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }

            ?>

            <div class="menu_wrapper">
                <div class="menu_box">
                    <div class="menu_header">
                        <div class="menu_logo">
                            <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt=""></a>
                        </div>
                        <div class="menu_cancel">
                            <i class='bx bx-x'></i>
                        </div>
                    </div>
                    <div class="menu_list">
                        <a href="/">Home</a>
                        <a href="/about-us/">About</a>
                        <a href="/contact-us/">Contact</a>
                        <a href="/categories/">Category</a>
                        <a href="/contact-us/">Contact us</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main>