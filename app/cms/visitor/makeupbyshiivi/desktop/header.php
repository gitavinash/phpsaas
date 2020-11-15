
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    <base href="/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Makeup by Shiivi</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo USER_IMAGES ?>favicon.png">
    <link href="<?php echo CSS_LIBRARY ?>all.min.css" rel="stylesheet">
    <!-- <link href="<?php echo USER_STYLESHEET ?>themify-icons.min.css" rel="stylesheet"> -->
    <link href="<?php echo CSS_LIBRARY ?>bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo PLUGIN_LIBRARY ?>owlcarousel2/owlcarousel.min.css">
    <link href="<?php echo USER_STYLESHEET ?>style.min.css" rel="stylesheet"> </head>

<body>
    <!-- Menu -->
    <nav class="mbs-site-menu" id="mbs-navbar-spy">
        <div class="mbs-site-menu-inner" id="mbs-navbar">
            <ul class="list-unstyled">
                <li><a href="/">Home</a></li>
                <li><a href="about/">About Me</a></li>
                <li><a href="services/">Services</a></li>
                <li><a href="portfolio/">Portfolio</a></li>
                <li><a href="blog/">Blog</a></li>
                <li><a href="contact/">Contact</a></li>
            </ul>
        </div>
    </nav>
    <!-- Header -->
    <header class="mbs-site-header">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-6 col-md-4 mbs-site-logo-wrap">
                    <a href="/" class="mbs-site-logo"><img src="<?php echo USER_IMAGES ?>favicon.png" alt="Makeup by Shiivi"></a>
                </div>
                <!-- Menu Burger -->
                <div class="col-6 col-md-8 text-right mbs-menu-burger-wrap"> <a href="#" class="mbs-site-nav-toggle mbs-js-mbs-site-nav-toggle"><i></i></a> </div>
            </div>
        </div>
    </header>
    <!-- Slider -->
    <div class="mbs-slider-section">
        <div class="mbs-profile-info">
            <div class="d-flex align-items-center w-100">
                <div class="mx-auto mbs-profile-inner">
                    <h2>Makeup by Shiivi<span>Makeup Artist</span></h2> </div>
            </div>
        </div>
        <div class="owl-carousel mbs-main-slider">
            <div class="slide-item" style="background-image: url(<?php echo USER_IMAGES ?>shiivi-bg-2.jpg);"></div>
        </div>
    </div>
    <!-- About Me -->
    <section class="mbs-about-section-home mbs-about-inner-page bg-black">
        <div class="container">
            <div class="row mbs-about-inner">
                <div class="col-md-8 offset-md-2 text-center">
                    <div class="mbs-about-info">
                        <h1>About Me</h1>
                        <h3>Hi, I'm Shivani Gupta a Professional Makeup Artist</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lobortis congue odio nec tempus. Donec non sodales metus. Suspendisse iaculis, turpis id ornare congue, ante arcu imperdiet odio, nec sollicitudin sapien dolor eu sapien. Ut commodo massa sed ultricies pellentesque. Aenean ac sapien tristique, ullamcorper felis vel, convallis nunc. Pellentesque auctor, est ut lacinia luctus, purus ante accumsan sem, quis iaculis odio tortor in nisi. </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Services -->
    <!-- <section class="mbs-homepage-services-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center mb-40">
                    <h2>Services</h2>
                    <p>Here are a few selections from my portfolio. Please view my expanded portfolio further below. Nam rhoncus euismo.</p>
                </div>
            </div>
            <div class="row mbs-portfolio-items">
                <div class="col-md-6 single-item">
                    <div class="mbs-portfolio-wrap">
                        <a href="portfolio/"><img src="<?php echo USER_IMAGES ?>services/eye-makeup.jpg" alt=""></a>
                        <div class="mbs-portfolio-content">
                            <p>Vestibulume</p>
                            <h3><a href="portfolio/">Eye Makeup</a></h3>
                            <a href="portfolio/" class="mbs-portfolio-link"></a>
                        </div>
                    </div>
                    <div class="mbs-portfolio-wrap">
                        <a href="portfolio/"><img src="<?php echo USER_IMAGES ?>services/children-makeup.jpg" alt=""></a>
                        <div class="mbs-portfolio-content">
                            <p>Vestibulume</p>
                            <h3><a href="portfolio/">Children Makeup</a></h3>
                            <a href="portfolio/" class="mbs-portfolio-link"></a>
                        </div>
                    </div>
                    <div class="mbs-portfolio-wrap">
                        <a href="portfolio/"><img src="<?php echo USER_IMAGES ?>services/effect-makeup.jpg" alt=""></a>
                        <div class="mbs-portfolio-content">
                            <p>Vestibulume</p>
                            <h3><a href="portfolio/">Effect Makeup</a></h3>
                            <a href="portfolio/" class="mbs-portfolio-link"></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 single-item">
                    <div class="mbs-portfolio-wrap">
                        <a href="portfolio/"><img src="<?php echo USER_IMAGES ?>services/face-makeup.jpg" alt=""></a>
                        <div class="mbs-portfolio-content">
                            <p>Vestibulume</p>
                            <h3><a href="portfolio/">Face Makeup</a></h3>
                            <a href="portfolio/" class="mbs-portfolio-link"></a>
                        </div>
                    </div>
                    <div class="mbs-portfolio-wrap">
                        <a href="portfolio/"><img src="<?php echo USER_IMAGES ?>services/hair-makeup.jpg" alt=""></a>
                        <div class="mbs-portfolio-content">
                            <p>Vestibulume</p>
                            <h3><a href="portfolio/">Hair Makeup</a></h3>
                            <a href="portfolio/" class="mbs-portfolio-link"></a>
                        </div>
                    </div>
                    <div class="mbs-portfolio-wrap">
                        <a href="portfolio/"><img src="<?php echo USER_IMAGES ?>services/wedding-makeup.jpg" alt=""></a>
                        <div class="mbs-portfolio-content">
                            <p>Vestibulume</p>
                            <h3><a href="portfolio/">Wedding Makeup</a></h3>
                            <a href="portfolio/" class="mbs-portfolio-link"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- Footer -->
    <footer class="mbs-footer-section text-center">
        <div class="container">
            <!-- Social Link -->
            <div class="row">
                <div class="col-md-12 text-center mb-20">
                    <div class="text-center">
                        <ul class="mbs-social-link">
                            <li><a href="/#"><i class="fab fa-facebook"></i></a></li>
                            <li><a href="/#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="/#"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Logo -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="brand"><img src="<?php echo USER_IMAGES ?>favicon.png" alt="Makeup by Shiivi"></div>
                </div>
            </div>
            <!-- Copyright -->
            <div class="row">
                <div class="col-md-12">
                    <p class="copright"><?php echo date('Y') ?> &copy; <?php echo SITE_NAME ?>. All rights reserved.</p>
                    <p class="copright">Made with <i class="fa fa-heart"></i> by <a href="http://www.itssoftworld.com" title="Software development company in delhi" style="color: #fff" >ITS Soft World</a> in India </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- JS -->
    <?php require_once NOTIFICATION.'index.php'; ?>
    <script src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>owlcarousel2/owlcarousel.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>imagesloaded/imagesloaded.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>isotope/isotope.min.js"></script>
    <script src="<?php echo USER_JS ?>script.min.js"></script>
</body>

</html>