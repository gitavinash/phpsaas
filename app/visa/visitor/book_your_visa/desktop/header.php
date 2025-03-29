<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Commissioner:wght@100..900&family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>select2/css/select2.min.css">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.4.2/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css">
    <?php
        require_once DEFAULT_META;
    ?>
</head>

<body>
    <header>
        <div class="container-fluid d-flex">
            <div class="col-4">
                <img src="<?php echo USER_IMAGES ?>logo.png" class="logo" alt="<?php echo SITE_NAME ?> logo"
                    title="<?php echo SITE_NAME ?> logo">
            </div>
            <div class="col-8">
                <div class="d-flex">
                    <ul class="menu">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li><a href="/about-us/" title="About us">About us</a></li>
                        <li><a href="/blogs/" title="Blogs">Blogs</a></li>
                        <li><a href="/contact-us/" title="">Contact Us</a></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <a class="login btn ms-auto"><strong>Login</strong></a>
                </div>
            </div>
        </div>
    </header>