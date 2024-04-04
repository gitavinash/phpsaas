<!DOCTYPE html>
<html lang="en">

<head>
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

    <!-- banner section -->
    <section class="banner_wrapper" id="home">
        <div class="container">
            <div class="row banner_flex">
                <div class="col-lg-6">
                    <div class="banner_wrapper__left">
                        <h6>Aleya is now available</h6>
                        <h1>Elevate your <span>Travel Business</span> with: Aleya</h1>
                        <p>Simplify your travel business operations by successfully managing leads, efficiently
                            following up with customers, smoothly handling bookings, easily collecting payments, sending
                            booking vouchers etc.</p>
                        <div class="get_in">
                            <a href="/register/" class="global_btn">Try now <img
                                    src="<?php echo USER_IMAGES ?>right-arrow.png" class="img-fluid white-arrow" alt="">
                                <img src="<?php echo USER_IMAGES ?>black-right-arrow.png" class="img-fluid black-arrow"
                                    alt=""></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner_wrapper__right">
                        <div class="banner_img">
                            <img src="<?php echo USER_IMAGES ?>banner_img.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- feature section -->
    <section class="feature_wrapper" id="feature">
        <!-- manage section -->
        <div class="container manage--container">
            <section class="manage_wrapper">
                <div class="container manage_wrapper__box">
                    <div class="row manage_flex">
                        <div class="col-lg-6">
                            <div class="manage_wrapper__left">
                                <img src="<?php echo USER_IMAGES ?>features/enquiry_source.png" class="img-fluid"
                                    alt="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="manage_wrapper__content">
                                <div class="manage_content">
                                    <h3>Manage all your query in one place</h3>
                                </div>
                                <div class="manage_box">
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>Never miss any lead</h3>
                                            <p>With timely follow-ups, Aleya can manage queries and leads effectively,
                                                ensuring no client is ever missed.</p>
                                        </div>
                                    </div>
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>Advanced lead collection</h3>
                                            <p>Centralize lead collection from Facebook, Instagram, Google, and more
                                                with Aleya. Simplify management in one place</p>
                                        </div>
                                    </div>
                                    <div class="get_started">
                                        <a href="/register/">Try now <img
                                                src="<?php echo USER_IMAGES ?>black-right-arrow.png"
                                                class="img-fluid white-arrow" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="container manage--container">
            <section class="manage_wrapper">
                <div class="container manage_wrapper__box red-bg">
                    <div class="row manage_flex">
                        <div class="col-lg-6" style="padding-left: 5%">
                            <div class="manage_wrapper__content">
                                <div class="manage_content">
                                    <h3>Manage all your report's in one place</h3>
                                </div>
                                <div class="manage_box">
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>Employees performance</h3>
                                            <p>Track and monitor employee performance effectively on Aleya, ensuring
                                                optimal productivity and team success.</p>
                                        </div>
                                    </div>
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>Leads performance</h3>
                                            <p>Analyze lead performance seamlessly with Aleya's comprehensive tracking
                                                tools, optimizing your sales strategy for success.</p>
                                        </div>
                                    </div>
                                    <div class="get_started">
                                        <a href="/register/">Try now <img
                                                src="<?php echo USER_IMAGES ?>black-right-arrow.png"
                                                class="img-fluid white-arrow" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-6">
                            <div class="manage_wrapper__left">
                                <img src="<?php echo USER_IMAGES ?>features/enquiry_performace.png" class="img-fluid"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="container manage--container">
            <section class="manage_wrapper">
                <div class="container manage_wrapper__box">
                    <div class="row manage_flex">
                        <div class="col-lg-6">
                            <div class="manage_wrapper__left">
                                <img src="<?php echo USER_IMAGES ?>features/coming_soon.png" class="img-fluid" alt="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="manage_wrapper__content">
                                <div class="manage_content">
                                    <h3>Upcoming Features That Will Wow You</h3>
                                </div>
                                <div class="manage_box">
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3><a href="https://oxpay.in" target="_blank">Ox Pay</a> - Payment
                                                collector</h3>
                                        </div>
                                    </div>
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>Itinerary Management</h3>
                                        </div>
                                    </div>
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>Vendor Management</h3>
                                        </div>
                                    </div>
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>Voucher Management</h3>
                                        </div>
                                    </div>
                                    <div class="manage_box__flex">
                                        <div class="manage_box__content">
                                            <h3>and many more...</h3>
                                        </div>
                                    </div>
                                    <div class="get_started">
                                        <a href="/register/">Try now <img
                                                src="<?php echo USER_IMAGES ?>black-right-arrow.png"
                                                class="img-fluid white-arrow" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <!-- pricing -->
    <section class="pricing_wrapper" id="price">
        <div class="container">
            <div class="main_heading">
                <h6><img src="<?php echo USER_IMAGES ?>Star.png" class="img-fluid" alt="">PRICING</h6>
                <div class="main_heading__content">
                    <h3>This offer is exclusive to the first 100 customers.</h3>
                    <div class="toggle_input">
                        <h5>Monthly</h5>

                        <div class="price-toggler">

                        </div>
                        <h5>Annually<span> (save up to 70%)</span></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="pricing_wrapper__content">
            <div class="container">
                <div class="row price_slider">
                    <div class="col-lg-6 col-md-6">
                        <div class="pricing_box first_box">
                            <div class="pricing_box__content">
                                <h3>CRM for All</h3>
                                <p>Streamline customer interactions, boost sales efficiency.</p>
                            </div>
                            <div class="price">
                                <p class="myPrice1">₹ 999</p>
                                <del class="del1 del">
                                    ₹ 2,499
                                </del>
                                <span class="duration"> /month upto 10 users</span>
                            </div>
                            <div class="points">
                                <ul>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Lead Acquisition from Google & Facebook</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Customer Engagement</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Performance Tracking</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> User Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Employee Management</li>
                                </ul>
                            </div>
                            <div class="buy_now">
                                <a href="/register/">Start now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="pricing_box">
                            <div class="pricing_box__content">
                                <h3>Travel ERP Software</h3>
                                <p>Optimize operations, drive business growth seamlessly.</p>
                            </div>
                            <div class="price">
                                <p class="myPrice2">₹ 2,499</p>
                                <del class="del2 del">₹ 4,999</del>
                                <span class="duration"> /month upto 10 users</span>
                            </div>
                            <div class="points">
                                <ul>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Everything in CRM</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Supplier Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Itinerary Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Invoice Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Voucher Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Booking Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Vouchers Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> Client Tour Management</li>
                                    <li><img src="<?php echo USER_IMAGES ?>comment-check 1.png"
                                            class="img-fluid black-check" alt=""> <img
                                            src="<?php echo USER_IMAGES ?>white-check.png" class="img-fluid white-check"
                                            alt=""> New feature will announcement soon</li>
                                </ul>
                            </div>
                            <div class="buy_now">
                                <a href="/register/">Start now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- contact us -->
    <section class="contact_wrapper" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact_wrapper__left">
                        <form method="POST">
                            <div class="contaact_form">
                                <div class="contact_flex">
                                    <div class="contact_input">
                                        <label for="">Full name</label>
                                        <input type="text" name="name" required>
                                    </div>
                                    <div class="contact_input">
                                        <label for="">Email address</label>
                                        <input type="email" name="email" required>
                                    </div>
                                </div>
                                <div class="contact_flex">
                                    <div class="contact_input">
                                        <label for="">Phone number</label>
                                        <input type="number" name="mobile" required>
                                    </div>
                                    <div class="contact_input">
                                        <label for="">Subject</label>
                                        <input type="text" name="subject">
                                    </div>
                                </div>
                                <div class="contact_input">
                                    <label for="">Message</label>
                                    <textarea name="" id="" cols="30" name="message" rows="4"></textarea>
                                </div>
                                <div class="send_btn">
                                    <input type="hidden" name="type" value="enquiry">
                                    <input type="hidden" name="action" value="new">
                                    <input type="hidden" name="subject" value="Contact us">
                                    <input type="hidden" name="sender"
                                        value="<?php echo base64_encode("avinash.kumar@aleya.in") ?>">
                                    <input type="hidden" name="redirect_url" value="https://aleya.in/">
                                    <input type="submit" value="send message" style="color: #fff !important"
                                        class="global_btn">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact_wrapper__right">
                        <h3>Experience it live - Request your demo now</h3>
                        <p><strong>Phone: </strong><a href="tel:+91-9355239595">+91-9355239595</a>, <a
                                href="tel:+91-9355239494">+91-9355239494</a></p>
                        <p><strong>Email: </strong><a href="mailto:sales@aleya.in">sales@aleya.in</a>, <a
                                href="mailto:contact@aleya.in">contact@aleya.in</a></p>
                        <p><strong>Corporate Office: </strong><a href="https://maps.app.goo.gl/JoFYSsRLWAwjAg5q8"
                                target="_blank">B-38,
                                Upper Ground Floor, Vishal Enclave,
                                Rajouri Garden, New Delhi
                                - 110027</a>
                        </p>
                        <div class="social_links">
                            <a href="https://wa.me/919355239595?text=Hi" target="_blank"><img
                                    src="<?php echo USER_IMAGES ?>whatsapp-icon.png" class="img-fluid" alt=""></a>
                            <a href="https://www.instagram.com/aleyaerp" target="_blank"><img
                                    src="<?php echo USER_IMAGES ?>black-instagram-icon.png" class="img-fluid"
                                    alt=""></a>
                            <a href="https://www.facebook.com/aleyaerp" target="_blank"><img
                                    src="<?php echo USER_IMAGES ?>facebook-app-round-white-icon 1.png" class="img-fluid"
                                    alt=""></a>
                            <a href="https://twitter.com/aleyaerp" target="_blank"><img
                                    src="<?php echo USER_IMAGES ?>x.png" class="img-fluid" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CRM section -->
    <section class="crm_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="crm_wrapper__left">
                        <!-- <h3>Start using the CRM platform of the future, today.</h3> -->
                        <h3>Begin transforming your business operations today with Aleya.</h3>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="get_started">
                        <a href="#">Get started <img src="<?php echo USER_IMAGES ?>right-green-arrow.png"
                                class="img-fluid" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <footer>
        <div class="footer_wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="footer_box">
                            <h3>Pages</h3>
                            <div class="footer_links">
                                <a href="#home">Home</a>
                                <a href="#feature">Features</a>
                                <a href="#price">Pricing</a>
                                <a href="#contact">Contact Us</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="footer_box">
                            <h3>Quick links</h3>
                            <div class="footer_links">
                                <a href="https://blumox.com/privacy-policy/" target="_blank">Privacy policy</a>
                                <a href="https://blumox.com/terms-and-conditions/" target="_blank">Terms of service</a>
                                <a href="https://blumox.com/refund-policy/" target="_blank">Refund policy</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="footer_box">
                            <h3>Office Address</h3>
                            <div class="footer_links">
                                <a href="https://maps.app.goo.gl/THLd4FMV3x8WChe76" target="_blank"><strong>Registered
                                        Office Address:</strong><br />A2/5A, First Floor, WHS Block 2, Near Richi Rich
                                    Banquet Hall, Kirti Nagar, New Delhi - 110015</a>
                                <a href="https://maps.app.goo.gl/JoFYSsRLWAwjAg5q8" target="_blank"><strong>Corporate
                                        Office Address:</strong><br />
                                    B-38, Upper Ground Floor, Vishal Enclave, Rajouri Garden, New Delhi - 110027</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="footer_right">
                            <div class="footer_logo">
                                <img src="<?php echo USER_IMAGES ?>aleya_logo.png" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row credit">
                    <p>&copy; <?php echo date('Y') ?> Aleya | A unit of <a href="https://blumox.com">Blumox
                            Technologies</a></p>
                </div>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="<?php echo USER_JS ?>stickynav.js"></script>
    <script src="<?php echo USER_JS ?>pricetoggle.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
    $('.manage_slider').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 2000,
        arrows: true,
        dots: false,
        responsive: [{
                breakpoint: 1200,
                settings: {
                    slidesToShow: 1,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 1,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                }
            },
        ]
    });
    </script>

    <script>
    jQuery(document).ready(function() {
        if (jQuery(window).width() < 768) {
            jQuery('.price_slider').slick({
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                // autoplay: true,
                arrows: false,
                dots: true,
                mobileFirst: true,
                responsive: [{
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 1,
                        mobileFirst: true,
                    }
                }, ]
            });
        }
    });
    </script>

    <script>
    $('#homeBtn').click(function() {
        $('.mobile_nav').removeClass('active');
    })
    $('#featureBtn').click(function() {
        $('.mobile_nav').removeClass('active');
    })
    $('#priceBtn').click(function() {
        $('.mobile_nav').removeClass('active');
    })
    $('#contactBtn').click(function() {
        $('.mobile_nav').removeClass('active');
    })
    $('#demoBtn').click(function() {
        $('.mobile_nav').removeClass('active');
    })
    </script>

</body>

</html>