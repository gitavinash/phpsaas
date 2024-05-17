<!-- stay conect -->
<section class="section-content bg-gr dn-s">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="h2-socl">Spread The Love</h2>

                <div class="social-1">
                    <a href="https://www.facebook.com/andblooms"><img src="<?php echo USER_IMAGES ?>facebook.png"></a>
                    <a href="https://www.instagram.com/andblooms/"><img
                            src="<?php echo USER_IMAGES ?>instagram.png"></a>
                    <!-- <a href=""><img src="<?php echo USER_IMAGES ?>tweet.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>linkedin.svg"></a>
							<a href=""><img src="<?php echo USER_IMAGES ?>pinterest.svg"></a> -->
                    <!-- <a href=""><img src="<?php echo USER_IMAGES ?>insta.svg"></a> -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- stay conect end -->

<footer>
    <div class="top-section-footer bg-gr">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="fot-box">
                        <ul class="dn-s">
                            <li><a href="/about-us/">About Us</a></li>
                            <li><a href="/cancellation-refund/">Cancellation & Refund</a></li>
                            <li><a href="/terms-and-conditions/">Terms &amp; conditions</a></li>
                            <li><a href="/privacy-policy/">Privacy policy</a></li>
                            <li><a href="/about-us/">Contact Us</a></li>
                        </ul>
                        <ul class="dn-l">
                            <li><a href="/about-us/">About Us</a></li>
                            <li><a href="/cancellation-refund/">Cancellation & Refund</a></li>
                            <li><a href="/terms-and-conditions/">Terms &amp; conditions</a></li>
                            <li><a href="/privacy-policy/">Privacy policy</a></li>
                            <li><a href="/about-us/">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="acept-py">
                        <span class="payout dn-s">We Accept <img
                                src="<?php echo USER_IMAGES ?>desktop-payment-option.png"></span>
                        <span class="payout dn-l">We Accept <img
                                src="<?php echo USER_IMAGES ?>paymentFooter.png"></span>
                    </div>
                </div>
            </div>
            <div class="row center">
                <div class="col-md-12 col-12">
                    <div class="footer-btm">
                        <p>© <?php echo date('Y') ?> <?php echo SITE_NAME; ?>. All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="footer-btm">
                        <p>Design &amp; Developed by <a href="https://blumox.com"
                                title="Ecommerce development in delhi">Blumox Technologies</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="whatsapp">
        <a
            href="https://wa.me/919910744119?text=<?php echo ((isset($link_type)) ? (($link_type == 'product') ? urlencode('i want to know about ' . $product_title . ' ' . SITE_URL . ACTIVE_URL) : 'Hello') : 'Hello') ?>"><img
                src="<?php echo IMG_LIBRARY ?>whatsapp.png" alt=""></a>
    </div>
</footer>

<!-- sections end -->
<?php include_once NOTIFICATION . 'index.php' ?>

<script type="text/javascript" src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
<script type="text/javascript" src="<?php echo JS_LIBRARY ?>flickity.min.js"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>zoom-gallery/zoom.min.js"></script>

<script type="text/javascript" src="<?php echo USER_JS ?>app.min.js"></script>

<!-- city search popup end -->

<script type="text/javascript">
$(document).ready(function() {

    $('.notes').select2();

    $('.card-ban').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        dots: false,
        arrows: false,
    });

    $('.testimonial-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        dots: false,
        arrows: false,
    });

    $('.product-slider').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        dots: false,
        arrows: false,
        adaptiveHeight: true,
        responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.product-main-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        fade: true,
        asNavFor: '.product-main-slider-nav'
    });

    <?php

        if (SITE_VERSION == 'desktop') { ?>

    $('.product-main-slider-nav').slick({
        slidesToShow: 5,
        slidesToScroll: 2,
        asNavFor: '.product-main-slider',
        dots: false,
        arrows: false,
        focusOnSelect: true
    });

    <?php
        } else { ?>

    $('.product-main-slider-nav').slick({
        slidesToShow: 3,
        slidesToScroll: 2,
        asNavFor: '.product-main-slider',
        dots: false,
        arrows: false,
        focusOnSelect: true
    });

    <?php
        }

        ?>

});
</script>

<!-- search in mobile div show -->

<script type="text/javascript">
function divSearchPop(e) {

    e.preventDefault();

    var x = document.getElementById("search-mob-div");

    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }

}

$('#searchBtn').on('click', function(e) {

    e.preventDefault();

});

$("#close").click(function() {
    $("searchInput").hide();
});
</script>

<?php

if (SITE_VERSION == 'desktop') { ?>

<script>
// Height of product details dev

var productDetailsHeight = document.getElementById('productDetails').offsetHeight - 150;

function sticky_relocate() {

    var window_top = $(window).scrollTop();

    //var div_top = $('#sticky-anchor').offset().top;
    if (window_top >= 300) {
        // alert('i m ready');
        $('#sticky').addClass('stick');
    } else {

        $('#sticky').removeClass('stick');

    }

}

// Product Zoom

$('.picZoomer').picZoomer();


$(function() {
    $(window).scroll(sticky_relocate);
    sticky_relocate();
});

$(function() {
    $(window).scroll(function(e) {
        if ($(this).scrollTop() > productDetailsHeight) {
            $('#sticky').removeClass('stick');
        }
    });
});
</script>

<?php
}

?>

</body>

</html>