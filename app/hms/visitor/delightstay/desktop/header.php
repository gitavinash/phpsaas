<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>index.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>about.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>hotel_list.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>details.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>contact.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.min.css">
    <?php require_once DEFAULT_META ?>

</head>

<body>
    <header>
        <div class="container ">
            <div class="d-flex align-items-end justify-content-between py-3 nav_img ">
                <div class="font_size_16 d-flex align-items-center ">
                    <img src="<?php echo USER_IMAGES ?>icon/nav_calling.svg" alt="" class="img-fluid ">
                    <a href="" class="ps-2"> +91 7827515902</a>
                    <img src="<?php echo USER_IMAGES ?>icon/message_nav.svg" alt=""
                        class="img-fluid ms-3 ms-md-5 ms-lg- 5">
                    <a href="" class="ps-2">reservations@mgihotels.com</a>
                </div>
                <div class="font_size_18">
                    <a href="/login/">Member login</a>
                </div>
            </div>
        </div>
        <hr class=" m-0 horizontal_line">
        <div class="container">
            <div class="row py-2">
                <nav class="navbar navbar-expand-lg ">
                    <div class="container-fluid">
                        <a class="navbar-brand main_logo" href="/">
                            <img src="<?php echo USER_IMAGES ?>mgi_logo.jpeg" alt="" class="img-fluid">
                        </a>
                        <button class="menu header_menu">
                            <i class='bx bx-menu'></i>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="/">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/about-us/" class="nav-link" aria-current="page">About
                                        Us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" aria-current="page" href="#">Our Hotels</a>
                                </li>
                            </ul>
                            <form class="d-flex" role="search">
                                <a href="/contact-us/"
                                    class="contact-btn bg_orange text-white px-5 px-md-3 px-lg-3 px-xl-5 py-3">Contact
                                    Us
                                </a>
                            </form>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <div class="menu_container">
            <div class="menu_container__box">
                <div class="menu_logo">
                    <a href="/"><img src="<?php echo USER_IMAGES ?>mgi_logo.jpeg" class="img-fluid" alt=""></a>
                    <div class="cut">
                        <i class='bx bx-x'></i>
                    </div>
                </div>
                <div class="menu_wrapper">
                    <a href="/">Home</a>
                    <a href="/about-us/">About us</a>
                    <a href="/hotel_list/">Our hotel</a>
                    <a href="/promotions/">Promotions</a>

                </div>
                <div class="contact_us">
                    <a href="/contact-us/" class="global_btn">contact us</a>
                </div>
            </div>
        </div>
    </header>