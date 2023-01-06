<footer class="vertical-padding sidepadding ">
    <div class="container-fluid">
        <div class="row align-items-top">
            <div class=" col-12  col-sm-6 col-md-6 col-lg-4   fotter-margin  px=5">
                <div class="">
                    <img src="<?php echo USER_IMAGES ?>logo.png" alt="" class="img-fluid">
                    <p class="text-white py-3 ">At SoundTrails, we love sound. We really do! Whether it is for a hom.
                        theater system or for acoustic solutions or even if it is to create an alluring sensory
                        environment, we believe, we are the right people for the job. Because we help create a
                        customized immersive experience that includes the best of audio and video. All this, at a cost
                        that’s truly affordable!</p>
                    <div class="social text-white d-flex">
                        <a href="https://www.facebook.com/soundtrailsindia/"><i class="fa-brands fa-facebook"></i></a>
                        <a href="https://www.instagram.com/soundtrailsindia/"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <div class=" col-6 col-sm-6 col-md-6 col-lg-2 px-2  fotter-margin">
                <h3 class="text-white font-size-25">Quick Links</h3>
                <ul>
                    <li><a href="/about-us/">About Us</a> </li>
                    <li><a href="/shop/">Our Products</a> </li>
                    <li><a href="/brands/">Brands</a> </li>
                    <li><a href="/contact-us/">Contact Us</a> </li>
                    <li><a href="/checkout/">Checkout</a> </li>
                </ul>
            </div>

            <div class="col-6  col-sm-6 col-md-6 col-lg-2 px-4  fotter-margin">
                <h3 class="text-white font-size-25" style="height: 30px"></h3>
                <ul>
                    <li><a href="#">Terms & Conditions</a> </li>
                    <li><a href="/privacy-policy/">Privacy Policy</a> </li>
                    <li><a href="/cancellation-and-refund-policy/">Cancellation Policy</a> </li>
                    <li><a href="/shipping-policies/">Shipping Policies</a> </li>
                    <li><a href="/sitemap/">Site Map</a> </li>
                </ul>
            </div>
            <div class=" col-12  col-sm-6 col-md-6 col-lg-4 px-2  .fotter-margin">
                <h3 class="text-white font-size-25 ms-3 ms-sm-0">Contact Us</h3>
                <ul>
                    <li><a href="#" class="d-flex align-items-center"><i class="fa-solid fa-location-dot"></i> Ground
                            Floor
                            A-92,Sushant Lok III Extension, Sector 57, Gurugram- 122003</a> </li>
                    <li><a href="mailto:contact@soundtrails.in" class="d-flex align-items-center"><i
                                class="fa-solid fa-envelope"></i>
                            contact@soundtrails.in</a> </li>
                    <li><a href="tel:+91-9560030646" class="d-flex align-items-center"><i
                                class="fa-solid fa-phone"></i>+91-9560030646</a> </li>


                </ul>


            </div>

            <!-- <div class="row">
                <div class="col-6"></div>
                <div class="col-6 text-end">
                    <img src="<?php echo USER_IMAGES ?>payment.png" alt="" class="img-fluid">
                </div>
            </div> -->


        </div>
    </div>
</footer>
<!-- Optional JavaScript; choose one of the two! -->

<!-- sections end -->
<?php include_once NOTIFICATION . 'index.php' ?>

<!-- Option 1: Bootstrap Bundle with Popper -->

<script src="<?php echo USER_JS ?>app.js"></script>
<script src="<?php echo USER_JS ?>popup.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
    integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<script>
$('.multiple-items').slick({

    cssEase: 'ease-in-out',
    touchThreshold: 100,
    slidesToShow: 1,
    autoplay: true,

});


$('.testi').slick({
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 3,
    autoplay: true,
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
                slidesToScroll: 2
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
    ]
});
</script>


<script>
$(".slider").slick({
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
    autoplay: true,
    autoplaySpeed: 2000,
    responsive: [{
            breakpoint: 1024,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: true,
                dots: true,
            },
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
            },
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
            },
        },
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
    ],
});
</script>

<!-- Option 2: Separate Popper and Bootstrap JS -->
<!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>