    </main>
    <footer>
        <section>
            <div class="contact-us">
                <img src="images/logo.png" alt="">
                <p><span><i class="fas fa-map-marker-alt"></i></span></p>
            </div>
        </section>
    </footer>
    <script src="js/jquery.js" ></script>
    <script src="plugins/slick/slick.min.js" ></script>
    <script type="text/javascript" >
        $(document).ready(function(e) {
            $('.homepage-slider').slick({
                autoplay: true,
                autoplaySpeed: 3000,
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1
            });
        });
    </script>
</body>
</html>