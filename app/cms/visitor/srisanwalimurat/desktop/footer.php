        </main>
        <footer>
            
        </footer>
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
            });
        </script>
    </body>
</html>