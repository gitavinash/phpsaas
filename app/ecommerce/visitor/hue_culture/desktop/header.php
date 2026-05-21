<!doctype html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" />
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php require_once DEFAULT_META ?>
</head>

<body>
    <!-- Header Section -->
    <header id="main-header">
        <a href="/">
            <div class="logo">
                <img src="<?php echo USER_IMAGES ?>logo.png" alt="<?php echo SITE_NAME ?>" />
            </div>
        </a>
        <nav>
            <ul>
                <li><a href="hue-color-system/">Hue Color System</a></li>
                <li><a href="color-clinic/">Color Clinic</a></li>
                <li><a href="blogs/">Blogs</a></li>
                <li><a href="training/">Training</a></li>
                <li><a href="community/">Community</a></li>
                <li><a href="/#portfolio">Portfolio</a></li>
            </ul>
        </nav>
        <div class="header-cta">
            <a href="contact/" class="btn green-bg">Contact Us</a>
        </div>
        <div class="menu-toggle" id="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>