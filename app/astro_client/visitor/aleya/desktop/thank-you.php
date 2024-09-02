<!DOCTYPE html>
<html lang="en">

<head>
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
</head>

<body>
    <header>
        <nav>
            <div class="myNav">
                <div class="container">
                    <div class="myNav_wrapper">
                        <div class="logo">
                            <a href="/"><img src="<?php echo USER_IMAGES ?>aleya_logo.png" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- contact us -->
    <section class="contact_wrapper" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact_wrapper__left">
                        <form action="">
                            <div class="contaact_form">
                                <div class="contact_flex">
                                    <div class="contact_input">
                                        <label for="">Full name</label>
                                        <input type="text">
                                    </div>
                                    <div class="contact_input">
                                        <label for="">Email address</label>
                                        <input type="email">
                                    </div>
                                </div>
                                <div class="contact_flex">
                                    <div class="contact_input">
                                        <label for="">Phone number</label>
                                        <input type="number">
                                    </div>
                                    <div class="contact_input">
                                        <label for="">Subject</label>
                                        <input type="text">
                                    </div>
                                </div>
                                <div class="contact_input">
                                    <label for="">Message</label>
                                    <textarea name="" id="" cols="30" rows="4"></textarea>
                                </div>
                                <div class="send_btn">
                                    <button class="global_btn">send message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact_wrapper__right">
                        <h3>Experience it live -<br />Request your demo now</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore.</p>
                        <div class="social_links">
                            <a href="#" target="_blank"><img src="<?php echo USER_IMAGES ?>whatsapp-icon.png"
                                    class="img-fluid" alt=""></a>
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
                        <h3>Start using the CRM platform of the future, today.</h3>
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
                    <div class="col-lg-3 col-4">
                        <div class="footer_box">
                            <h3>Pages</h3>
                            <div class="footer_links">
                                <a href="#">Home</a>
                                <a href="#">About</a>
                                <a href="#">Contact</a>
                                <a href="#">Blog</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-4">
                        <div class="footer_box">
                            <h3>Pages</h3>
                            <div class="footer_links">
                                <a href="#">Home</a>
                                <a href="#">About</a>
                                <a href="#">Contact</a>
                                <a href="#">Blog</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-4">
                        <div class="footer_box">
                            <h3>Pages</h3>
                            <div class="footer_links">
                                <a href="#">Home</a>
                                <a href="#">About</a>
                                <a href="#">Contact</a>
                                <a href="#">Blog</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12">
                        <div class="footer_right">
                            <div class="footer_logo">
                                <img src="<?php echo USER_IMAGES ?>aleya_logo.png" class="img-fluid" alt="">
                            </div>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="<?php echo USER_JS ?>stickynav.js"></script>
    <script src="<?php echo USER_JS ?>pricetoggle.js"></script>
    <!-- <script src="<?php echo USER_JS ?>slider.js"></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
    $('.testimonial_slider').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 2000,
        arrows: true,
        dots: false,
        nextArrow: '<button type="button" class="custom-next slick-next">Next</button>',
        prevArrow: '<button type="button" class="custom-prev slick-prev">Prev</button>',
        responsive: [{
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2,
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
    $(".testimonial_slider .slick-prev").hover(
        function() {
            $(".testimonial_slider").addClass("prev-hovered");
        },
        function() {
            $(".testimonial_slider").removeClass("prev-hovered");
        }
    );
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

</body>

</html>