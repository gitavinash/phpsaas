<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4HB2C27FJJ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-4HB2C27FJJ');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <?php require_once DEFAULT_META ?>
    <script type="text/javascript">
    (function(c, l, a, r, i, t, y) {
        c[a] = c[a] || function() {
            (c[a].q = c[a].q || []).push(arguments)
        };
        t = l.createElement(r);
        t.async = 1;
        t.src = "https://www.clarity.ms/tag/" + i;
        y = l.getElementsByTagName(r)[0];
        y.parentNode.insertBefore(t, y);
    })(window, document, "clarity", "script", "lr85q84qkh");
    </script>
</head>

<body>
    <header>
        <nav>
            <div class="myNav">
                <div class="container">
                    <div class="myNav_wrapper">
                        <div class="logo">
                            <a href="#home"><img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt=""></a>
                        </div>
                        <div class="menu">
                            <ul>
                                <li><a href="#home">Home</a></li>
                                <li><a href="#feature">Features</a></li>
                                <li><a href="#price">Pricing</a></li>
                                <li><a href="#contact">Contact Us</a></li>
                            </ul>
                        </div>
                        <div class="contact">
                            <a href="#contact" class="global_btn">Request a demo</a>
                        </div>
                        <div class="menu_icon">
                            <i class='bx bx-menu'></i>
                        </div>
                    </div>
                </div>

                <!-- mobile nav -->
                <div class="mobile_nav">
                    <div class="mobile_nav__container">
                        <div class="mobile_nav__header">
                            <div class="mobile_logo">
                                <a href="#home"><img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt=""></a>
                            </div>
                            <div class="cancel_icon">
                                <i class='bx bx-x'></i>
                            </div>
                        </div>
                        <div class="mobile_menu">
                            <ul>
                                <li><a href="#home" id="homeBtn">Home</a></li>
                                <li><a href="#feature" id="featureBtn">Features</a></li>
                                <li><a href="#price" id="priceBtn">Pricing</a></li>
                                <li><a href="#contact" id="contactBtn">Contact Us</a></li>
                            </ul>
                            <div class="mobile_contact">
                                <a href="#contact" id="demoBtn" class="global_btn">Request a demo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>