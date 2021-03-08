        </main>
        <footer>
        <div class="foter-top">
                <div class="box-1">
                    <h2>Quick LInks</h2>
                    <ul>
                        <li><a href="">Home</a></li>
                        <li><a href="">Gallery</a></li>
                        <li><a href="">Discourses</a></li>
                        <li><a href="">About</a></li>
                        <li><a href=""></a></li>
                    </ul>
                </div>
                <div class="box-1">
                    <h2>Quick LInks</h2>
                    <ul>
                        <li><a href="">Bhajan</a></li>
                        <li><a href="">Arti</a></li>
                        <li><a href="">Terms</a></li>
                        <li><a href="">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="box-1">
                    <h2>Quick LInks</h2>
                    <ul>
                        <li><a href="">Home</a></li>
                        <li><a href="">Gallery</a></li>
                        <li><a href="">Discourses</a></li>
                        <li><a href="">About</a></li>
                        <li><a href=""></a></li>
                    </ul>
                </div>
                <div class="box-1">
                    <h2>Quick LInks</h2>
                    <ul>
                        <li>Address : 36, Gali Number 2, Block 69B, Dori Walan, Karol Bagh, New Delhi, Delhi 110005, India</li>
                        <li>info@srisunwalimurat@gmail.com</li>
                    </ul>
                </div>

            </div>

        </footer>
        <div class="whatsapp">
			<a href="https://wa.me/919810115828?text=Hello"><img src="<?php echo SITE_URL ?>/lib/resource/ui/img/whatsapp.png" alt=""></a>
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
            });
        </script>
    </body>
</html>