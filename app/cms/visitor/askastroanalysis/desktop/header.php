<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo SITE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <link rel="stylesheet" href="<?php echo CSS_LIBRARY ?>all.min.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.css">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>slick/slick-theme.css">
</head>

<body>
    <header>
        <div class="sticky-bar">
            <section>
                <div class="left-box">
                    <p>Welcome to <?php echo SITE_NAME ?></p>
                </div>
                <div class="right-box">
                    <ul>
                        <li><a href="">Sign In</a></li>
                        <li><a href="">Sign Up</a></li>
                    </ul>
                </div>
            </section>
        </div>
        <div class="main-header">
            <section>
                <div class="logo">
                    <a href="/"><img src="<?php echo USER_IMAGES ?>logo.png" alt=""></a>
                </div>
                <div class="search-box">
                    <form action="/search/">
                        <input type="text" name="q" class="form-control inside-shadow search-bar" placeholder="Search for astrology services...">
                    </form>
                </div>
            </section>
            <div class="main-menu">
                <section>
                    <ul>
                        <li><a href="">Home</a></li>
                        <li><a href="/pooja/">Pooja</a></li>
                        <li><a href="">Horoscope</a></li>
                        <li><a href="">Astrology</a></li>
                        <li><a href="">Astrologer</a></li>
                        <li><a href="">Online Pandit</a></li>
                        <li><a href="">Blogs</a></li>
                        <li><a href="">Contact Us</a></li>
                    </ul>
                </section>
            </div>
        </div>
    </header>
    <main>