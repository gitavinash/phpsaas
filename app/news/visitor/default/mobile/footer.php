
        
        </main>

        <footer>
            
        

        </footer>


        <?php require_once NOTIFICATION.'index.php'; ?>
        <script type="text/javascript" src="<?php echo USER_PLUGINS ?>slick/slick.min.js"></script>
        <script type="text/javascript" src="<?php echo USER_JS ?>script.min.js"></script>
        <script>

            /* Homepage slider */

            $('.homepageSlider').slick({
                infinite: true,
                autoplay: true,
                speed: 1500,
                slidesToShow: 1,
                slidesToScroll: 1,
                nextArrow: false,
                prevArrow: false
            });

        </script>
        
    </body>

</html>