<footer class="container-fluid margin_top">
    <div class="row common_padding footer_sub justify-content-between pt-3 pt-lg-5 pb-3">
        <div class="col-12 col-lg-3 mb-3 mb-lg-0">
            <a href="/"><img src="<?php echo USER_IMAGES ?>logo_astro.png" class="logo_astro" /></a>
            <p>
                We have the World Famous Astroaayu on the Best Astrology Website in India, practicing both Indian
                Astrology
                and Western Astrology for astrology today.
            </p>
        </div>
        <div class="col-12 col-sm-4 col-lg-2">
            <h3 class="color_blue footer_title">Useful Links</h3>
            <ul class="footer_list">
                <li><a href="/about/">About Us</a></li>
                <li><a href="/privacy-policy/">Privacy Policy</a></li>
                <li><a href="/terms-of-use/">Terms Of Use</a></li>
                <li><a href="/shop/">Shop</a></li>
                <li><a href="/contact/">Contact Us</a></li>
            </ul>
        </div>
        <div class="col-12 col-sm-4 col-lg-2">
            <h3 class="color_blue footer_title">Astroaayu</h3>
            <ul class="footer_list">
                <li><a href="/category/yantra/">Yantra</a></li>
                <li><a href="/category/gem-stones/">Gem Stones</a></li>
                <li><a href="/category/hawan/">Hawan</a></li>
                <li><a href="/category/upay/">Upay</a></li>
            </ul>
        </div>
        <div class="col-12 col-sm-4 col-xl-3">
            <h3 class="color_blue footer_title">Address</h3>
            <div>
                <p>Astroaayu Pvt. Ltd.</p>
                <a href="https://goo.gl/maps/RLKBsXvwD2YsLsdc8" class="aa" target="_blank">
                    A-62,Freedom fighter enclav,e<br />
                    Neb sarai Hill top apartment,<br /> New Delhi - 110068
                </a>
                <div class="mt-2 mt-sm-3 footer_btn_b">
                    <a class="footer_btn" href="/contact/">Ask Your Question</a>
                    <a class="footer_btn" href="/appointment/">Book An Appointment</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row common_padding footer_dark white py-3 align-items-center">
        <div class="col-12 col-sm-6 text-center text-sm-start">
            <p>&copy 2021-<?php echo date('Y') ?> <?php echo SITE_NAME ?> Rights Reserved. | Design and Developed by <a
                    href="//blumox.com?utm_source=astroaayu.com">Blumox Technologies</a></p>
        </div>
        <div class="col-12 col-sm-6 d-flex justify-content-center justify-content-sm-end mt-2 mt-sm-0">
            <div class="footer_icon">
                <a href="https://www.facebook.com/astroaayu4u">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
            </div>
            <div class="footer_icon mx-3">
                <a href="https://twitter.com/astroaayu">
                    <i class="fa-brands fa-twitter"></i>
                </a>
            </div>
            <div class="footer_icon">
                <a href="https://www.instagram.com/astroaayu">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- sections end -->
<?php include_once NOTIFICATION . 'index.php' ?>

<!--Jquery Cdn-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="<?php echo USER_JS ?>app.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous">
</script>
<!--Slick Slider Cdn-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
    integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="<?php echo USER_JS ?>read.js"></script>
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
<!-- font-awesome-kit -->

<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>
<!-- jquery -->
<!-- slider main -->
<script>
var recaptchaSiteKey = "<?php echo GOOGLE_RECAPTCHA_SITE_KEY ?>";
$(".testimonial_slider").slick({
    dots: false,
    infinite: false,
    speed: 300,
    slidesToShow: 2,
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 3000,
    arrows: true,
    prevArrow: '<span class="prev_arrow"><i class="fas fa-arrow-left"></i></span>',
    nextArrow: '<span class="next-arrow"><i class="fas fa-arrow-right"></i></span>',
    responsive: [{
            breakpoint: 1024,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
            },
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            },
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            },
        },
    ],
});
</script>
<script>
$(".testi_slider").slick({
    dots: true,
    arrows: true,
    infinite: true,
    speed: 300,
    slidesToShow: 2,
    slidesToScroll: 1,
    autplay: true,
    autplaySpeed: 1500,
    responsive: [{
            breakpoint: 1024,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: true,
                dots: true,
            },
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 1,
            },
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            },
        },
    ],
});
</script>
</body>

</html>