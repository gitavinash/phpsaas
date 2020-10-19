<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : ${$link_type.'_title'} ) : SITE_DESC.' | '.SITE_NAME ) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">

    <!-- SEO -->

    <meta name="theme-color" content="#D01F28">
    <meta name='robots' content='index, follow' />
    <meta name='title' content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_title)) ? (($meta_title!='') ? $meta_title : SITE_NAME ) : SITE_NAME ))))) ?>" />
    <meta name="description" content="<?php echo str_replace("''", "", str_replace('\"', '', str_replace("\'", "", str_replace('"', "'", ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ))))) ?>" >
    <meta name="keywords" content="<?php echo ((isset($meta_keyword)) ? (($meta_keyword!='') ? $meta_keyword : $tagName ) : SITE_TAGS ) ?>" >
    <meta name="author" content="<?php echo ((isset($profile_display_name)) ? $profile_display_name : SITE_NAME ); ?>">


</head>
<body id="home" >
    <header>
        <div class="container">
            <div class="desktop">
                <div class="logo">
                    <a href="#"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
                </div>
                <div class="menu">
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#ourstory">Our Story</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#portfolio">Portfolio</a></li>
                        <li><a href="/blog/">Blog</a></li>
                        <li><a href="#contactus">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="mobile">
                <div class="logo">
                    <a href="#"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
                </div>
                <div class="menu">
                    <img src="<?php echo USER_IMAGES ?>icon/menu.png" id="mobileMenu" alt="">
                </div>
            </div>
        </div>
    </header>
    <main>