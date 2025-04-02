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
                                    <input type="number" onwheel="this.blur()" name="mobile" required>
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
                                    value="<?php echo base64_encode("sales@aleya.in") ?>">
                                <input type="hidden" name="cc"
                                    value="<?php echo base64_encode("avinashsharma114@gmail.com") ?>">
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
                            href="mailto:support@aleya.in">support@aleya.in</a></p>
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
                                src="<?php echo USER_IMAGES ?>black-instagram-icon.png" class="img-fluid" alt=""></a>
                        <a href="https://www.facebook.com/aleyaerp" target="_blank"><img
                                src="<?php echo USER_IMAGES ?>facebook-app-round-white-icon 1.png" class="img-fluid"
                                alt=""></a>
                        <a href="https://twitter.com/aleyaerp" target="_blank"><img src="<?php echo USER_IMAGES ?>x.png"
                                class="img-fluid" alt=""></a>
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
                    <a href="#">Get started <img src="<?php echo USER_IMAGES ?>right-green-arrow.png" class="img-fluid"
                            alt=""></a>
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
                            <a href="/#feature">Features</a>
                            <a href="/#price">Pricing</a>
                            <a href="/#contact">Contact Us</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="footer_box">
                        <h3>Quick links</h3>
                        <div class="footer_links">
                            <a href="/privacy-policy/">Privacy policy</a>
                            <a href="/terms-and-conditions/">Terms of service</a>
                            <a href="/refund-policy/">Refund policy</a>
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