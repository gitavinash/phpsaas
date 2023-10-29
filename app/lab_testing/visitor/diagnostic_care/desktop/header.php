<!DOCTYPE html>
<html lang="en">

<head>

    <?php

    if (BASE_URL == '/booking-process/') {
        echo "<meta http-equiv='refresh' content='60;url=/thank-you/' />";
    }

    ?>
    <base href="<?php echo SITE_URL ?>">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.min.css" />
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>global.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>home.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>checkout.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>contact.css" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>list.css" />
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.css">
    <link rel="mask-icon" href="<?php echo USER_IMAGES ?>favicon.png" color="#0077be">
    <meta name="msapplication-TileColor" content="#0077be">
    <meta name="theme-color" content="#0077be">
    <?php require_once DEFAULT_META; ?>
</head>

<body>
    <header class="side-padding">
        <nav class="navbar navbar-expand-lg navigation">
            <div class="container">
                <a class="navbar-brand" href="/"><img src="<?php echo SITE_LOGO ?>" alt="" class="img-fluid" /></a>
                <div class="humburger d-block d-lg-none">
                    <i class='bx bx-menu isfont-28'></i>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/about-us/">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/popular-tests/">Popular Tests</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contact-us/">Contact Us</a>
                        </li>
                    </ul>
                    <div>
                        <a href="tel:+91 9212535996" class="ms-3 btn1">Call Now</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="mobile-nav">
            <div class="row my-5 px-3 align-items-center">
                <div class="col-6">
                    <img src="<?php echo SITE_LOGO ?>" alt="" class="img-fluid mobile-logo">
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <div class="close-icon">
                        <i class='bx bxs-x-circle isfont-28 text-white'></i>
                    </div>
                </div>
                <div class="col-12">
                    <ul class="p-0">
                        <li><a href="/" class="border_none">Home</a></li>
                        <li><a href="/about-us/">About Us</a></li>
                        <li><a href="/popular-tests/">Popular Tests</a></li>
                        <li><a href="/contact-us/">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>