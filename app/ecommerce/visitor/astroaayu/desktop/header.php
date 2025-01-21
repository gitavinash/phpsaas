<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous" />

    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" />

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <!--Slick Slider-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"
        integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"
        integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php require_once DEFAULT_META; ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CBE6LK3DEF"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-CBE6LK3DEF');
    </script>

</head>

<body>
    <header>
        <div class="sub_header d-flex justify-content-center align-items-center flex_wrap">
            <div class="icon_container">
                <div class="sub_icon_box">
                    <span class="material-symbols-sharp"> call </span>
                </div>
                <p>
                    <a href='tel:91-8505850919'><span class="font_weight">Talk to our astro</span> - 8505850919</a>
                </p>
            </div>
            <div class="icon_container ms-3 ms-md-5">
                <div class="sub_icon_box">
                    <span class="material-symbols-sharp"> mail </span>
                </div>
                <p>
                    <a hrfe='mailto:support@astroaayu.com'><span class="font_weight">Talk to our astro</span> -
                        support@astroaayu.com</a>
                </p>
            </div>
            <div class="sub_buttons ms-3 ms-md-5 d-flex align-items-center d-none">
                <button class="sub_button1 relative">
                    <div class="sub_icon_box2"><i class="fa-solid fa-user"></i></div>
                    Sign Up
                </button>
                <button class="sub_button2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>Log In
                </button>
            </div>
        </div>
        <nav class="common_padding py-1 nav nav_bg">
            <a href="/">
                <img src="<?php echo USER_IMAGES ?>logo_astro.png" class="logo_astro" />
            </a>
            <div class="d-flex align-items-center">
                <div class="nav_container nav_res">
                    <ul class="d-flex align-items-center relative">
                        <li><a href="/" <?php echo ((ACTIVE_URL == '/') ? 'class="active_link"' : '') ?>>Home</a></li>
                        <li><a href="/about/"
                                <?php echo ((ACTIVE_URL == '/about/') ? 'class="active_link"' : '') ?>>About
                                Us</a></li>
                        <li><a href="/numerology/"
                                <?php echo ((ACTIVE_URL == '/numerology/') ? 'class="active_link"' : '') ?>>Numerology</a>
                        </li>
                        <li><a href="/shop/"
                                <?php echo ((ACTIVE_URL == '/shop/') ? 'class="active_link"' : '') ?>>Shop</a>
                        </li>
                        <li><a href="/contact/"
                                <?php echo ((ACTIVE_URL == '/contact/') ? 'class="active_link"' : '') ?>>Contact
                                Us</a></li>
                        <li>
                            <a href="/appointment/" class="display_prop">Book An Appointment</a>
                        </li>
                        <span class="material-symbols-sharp close_icon_res"> close </span>
                    </ul>
                </div>
                <div class="nav_container2">
                    <!-- <img src="<?php echo USER_IMAGES ?>search_icon.png" class="search_icon" /> -->
                    <a class="common_btn d-inline-block ms-4 btn_margin" href="/appointment/">Book Appointment</a>

                    <span class="material-symbols-sharp toggler"> menu </span>
                </div>
            </div>
        </nav>
    </header>