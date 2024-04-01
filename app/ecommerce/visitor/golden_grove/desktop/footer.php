</main>

<footer>
    <section class="footer_wrapper">
        <div class="container">
            <div class="footer_input">
                <h6>Get Exclusive Offers & Updates</h6>
                <div class="input_box">
                    <input type="text" placeholder="Sign up to our newslatter">
                    <button class="input_icon">
                        <i class='bx bx-right-arrow-alt'></i>
                    </button>
                </div>
            </div>
            <div class="footer_faq">
                <div class="footer_heading">
                    <h3>About <?php echo SITE_NAME ?></h3>
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="footer_content">
                    <a href="/about-us/">About Us</a>
                </div>
            </div>
            <div class="footer_faq">
                <div class="footer_heading">
                    <h3>Category</h3>
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="footer_content">

                    <?php
            
                        if($checkCategory=mysqli_query($connection, "SELECT category_title, link_url FROM ".TABLE_PREFIX."category INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=category_id WHERE link_site_id=".SITE_ID." AND link_type='category' AND link_status=200 ORDER BY link_is_featured, link_modified DESC LIMIT 0, 5")) {
                            if(mysqli_num_rows($checkCategory)!=0) {
                                while($categoryData=mysqli_fetch_assoc($checkCategory)) {
                                    extract($categoryData);
                                    echo "<a href='{$link_url}' title='{$category_title}' >{$category_title}</a>";
                                }
                            }
                        }
                    ?>

                </div>
            </div>
            <div class="footer_faq">
                <div class="footer_heading">
                    <h3>Customer support</h3>
                    <i class='bx bx-chevron-down'></i>
                </div>
                <div class="footer_content">
                    <a href="/contact-us/">Contact Us</a>
                    <a href="/privacy-policy/">Privacy policy</a>
                    <a href="/refund-policy/">Refund policy</a>
                    <a href="/terms-and-conditions/">Terms & conditions</a>
                </div>
            </div>

            <div class="footer_social">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-instagram'></i></a>
                <a href="#"><i class='bx bxl-linkedin'></i></a>
                <a href="#"><i class='bx bxl-youtube'></i></a>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y') ?> <?php echo SITE_NAME ?>.</p>
                <p class="credit">Powered by <a href="https://cartt.in" target="_blank"
                        title="Ecommerce website creator">Cartt - Craft
                        Your Online Success with
                        Cartt.
                    </a></p>
            </div>
        </div>
        </div>
    </section>
</footer>






<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
$('.banner_slider').slick({
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 2000,
    arrows: false,
    dots: true,
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
$('.category_card__slider').slick({
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 2000,
    arrows: false,
    dots: true,
    responsive: [{
            breakpoint: 1200,
            settings: {
                slidesToShow: 4,
            }
        },
        {
            breakpoint: 992,
            settings: {
                slidesToShow: 3,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 3,
            }
        },
        {
            breakpoint: 576,
            settings: {
                slidesToShow: 2,
            }
        },
    ]
});
</script>
<script>
$('.special_slider').slick({
    infinite: false,
    slidesToShow: 2.5,
    slidesToScroll: 1,
    autoplay: false,
    autoplaySpeed: 2000,
    arrows: false,
    dots: true,
    responsive: [{
            breakpoint: 1200,
            settings: {
                slidesToShow: 2.5,
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
                slidesToShow: 1.5,
            }
        },
    ]
});
</script>



<script src="<?php echo USER_JS ?>navbar.js"></script>
<script src="<?php echo USER_JS ?>counter.js"></script>
<script src="<?php echo USER_JS ?>cart.js"></script>
<script src="<?php echo USER_JS ?>footer_faq.js"></script>
</body>

</html>