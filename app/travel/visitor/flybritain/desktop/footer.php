<footer>
    <div class="container">
        <div class="row">
            <div class="logoCol">
                <div class="footerLogo">
                    <img src="<?php echo USER_IMAGES ?>logo-white.png" alt="<?php echo SITE_NAME ?>">
                </div>
                <div class="creditSite">
                    <p>&copy; <?php echo date('Y'); ?> All Rights Reserved by Fly Britain</p>
                </div>
            </div>
            <div class="menuCol">
                <div class="footerMenu">
                    <ul>
                        <li><a href="">About Us</a></li>
                        <li><a href="">Contact Us</a></li>
                        <li><a href="">Privacy Policy</a></li>
                        <li><a href="">Cookies Policy</a></li>
                    </ul>
                    <ul>
                        <li><a href="">Terms and Conditions</a></li>
                        <li><a href="">Data Protection Policy</a></li>
                        <li><a href="">Disclaimer</a></li>
                        <li><a href="">Sitemap</a></li>
                    </ul>
                </div>
            </div>
            <div class="contactCol">
                <div class="phone">
                    <a href="tel:02071838045">02071838045</a>
                </div>
                <p>All of the card transactions we process are securely protected and encrypted by Comodo, we also offer
                    the option to pay with PayPal. Our registered office address is 20-22 Wenlock Road, London,
                    United Kingdom</p>
            </div>
            <div class="newsletterCol">
                <div class="form">
                    <form>
                        <div class="form-group">
                            <label>Sign up for our mailing list to get latest updates and offers.</label>
                            <div class="inlineBtn">
                                <input type="text" class="input">
                                <button class="submitBtn">&#10003;</button>
                            </div>
                        </div>
                        <p>We respect your privacy <a href="/privacy-policy/">View Policy</a></p>
                    </form>
                </div>
            </div>
            <div class="imagesCol">
                <div class="socialMedia">
                    <a href=""><img src="<?php echo USER_IMAGES ?>icons/facebook.png" alt=""></a>
                    <a href=""><img src="<?php echo USER_IMAGES ?>icons/instagram.png" alt=""></a>
                    <a href=""><img src="<?php echo USER_IMAGES ?>icons/twitter.png" alt=""></a>
                </div>
                <div class="banner">
                    <img src="https://tup-cdn.azureedge.net/tup-web/images/fastrack/fasttrack-footer.jpg" alt="">
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- sections end -->
<?php include_once NOTIFICATION . 'index.php' ?>
<script src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js"></script>
<script>
    $(document).ready(function() {
        $(document).mouseup(function(e) {
            var passangerPopUp = $(".passangerPopUp");
            var tripPopUp = $(".tripPopUp");
            var classPopUp = $(".classPopUp");
            if (!passangerPopUp.is(e.target) && passangerPopUp.has(e.target).length === 0) {
                passangerPopUp.hide();
            }
            if (!tripPopUp.is(e.target) && tripPopUp.has(e.target).length === 0) {
                tripPopUp.hide();
            }
            if (!classPopUp.is(e.target) && classPopUp.has(e.target).length === 0) {
                classPopUp.hide();
            }
        });
        $('#departureDate').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });
        $('.slider').slick({
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            arrows: false,
        });
        $('#passangerPopUp').on('click', function() {
            $('.passangerPopUp').show();
        });
        $('#tripPopUp').on('click', function() {
            $('.tripPopUp').show();
        });
        $('#classPopUp').on('click', function() {
            $('.classPopUp').show();
        });
    });
</script>
</body>

</html>