        </main>
        <footer>
        <div class="foter-top">
            <div class="box-1">
                <h2>Quick links</h2>
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/about-us/">About Us</a></li>
                        <li><a href="/contact-us/">Contact Us</a></li>
                    </ul>
                </div>
                <div class="box-1">
                    <h2>Quick links</h2>
                    <ul>
						<li><a href="/publication/">Publication</a></li>
						<li><a href="/gallery/">Gallery</a></li>
						<li><a href="/events/">Events</a></li>
						<li><a href="/discourses/">Discourses</a></li>
						<li><a href="/bhajan/">Bhajan/Aarti</a></li>
						<li><a href="https://pangat.srisanwalimurat.com/" style="color: #fff" >Request For Pangat</a></li>
                    </ul>
                </div>
                <div class="box-1">
                    <h2>Information</h2>
                    <ul>
						<li><a href="/privacy-policy/">Privacy Policy</a></li>
						<li><a href="/disclaimer/">Disclaimer</a></li>
						<li><a href="/refund-policy/">Refund Policy</a></li>
						<li><a href="/terms-of-use/">Terms of Use</a></li>
                    </ul>
                </div>
                <div class="box-1">
                    <h2>Contact Us</h2>
                    <ul>
                        <li>Address : 36, Gali Number 2, Block 69B, Dori Walan, Karol Bagh, New Delhi, Delhi 110005, India</li>
                        <li>info@srisanwalimurat.com</li>
                    </ul>
                </div>    
        </div>

        </footer>
        <div class="whatsapp">
			<a href="https://wa.me/919810115828?text=Hello" target="_blank" ><img src="<?php echo SITE_URL ?>/lib/resource/ui/img/whatsapp.png" alt=""></a>
		</div>
        <?php require_once NOTIFICATION.'index.php'; ?>
        <script src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js" type="text/javascript" ></script>
        <script type="text/javascript" >
            $(document).ready(function(e) {
                $('.news-slider').slick({
                    arrows: false,
                    autoplay: true,
                    autoplaySpeed: 2000,
                    infinite: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                });
                $('.show').click(function() {
                    $('#target').show(200);
                    $('.show').hide(0);
                    $('.hide').show(0);
                });
                $('.hide').click(function() {
                    $('#target').hide(500);
                    $('.show').show(0);
                    $('.hide').hide(0);
                });
                $('.toggle').click(function() {
                    $('#target').toggle('slow');
                });
                $('.pop-up-close').on('click', function() {
                    $('.live-pop-up').hide();
                });
            });
        </script>
    </body>
</html>