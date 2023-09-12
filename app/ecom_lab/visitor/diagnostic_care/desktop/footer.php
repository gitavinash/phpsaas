<footer class="container-fluid pt-3  pb-2 pb-sm-3">
    <div class="container-fluid container-xl white">
        <div class="row justify-content-between align-items-center common_padding">
            <div class="col-12 flex_prop justify-content-sm-center">
                <div class="footer_logo mb-2">
                    <img src="<?php echo USER_IMAGES ?>logo.png">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <h3 class="footer_t">Diagnostic Services- A brief introduction</h3>
                <p class="mt-2 font_14">Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur vero, rem
                    odio nobis libero dolore quasi eligendi placeat aliquid fuga inventore, tenetur illum odit soluta
                    quibusdam repellendus suscipit. At, nostrum quidem deserunt obcaecati fugit consectetur molestias?
                    Et voluptates molestiae dolor?</p>
            </div>
            <div class="col-12 col-sm-5 mt-2 mt-sm-0">
                <h3 class="footer_t">Disclaimer:-</h3>
                <p class="mt-1 font_14">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Consequuntur, rem?
                    Obcaecati eos cumque harum labore itaque illum officia aliquid, et ipsum veniam. Voluptatem repellat
                    placeat nulla in sint! Incidunt, asperiores tempore. Dolores accusantium, similique quas perferendis
                    ratione eveniet ipsa aperiam?</p>
            </div>
        </div>
        <hr class="footer_hr">
        <div class="row common_padding">
            <div class="col-6 col-lg-4">
                <div>
                    <p class="footer_title">Quick Links</p>
                    <div class="footer_line">
                        <span></span>
                    </div>
                </div>
                <ul class="footer_ul mt-2 mt-sm-3">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Terms Condition</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-4">
                <div>
                    <p class="footer_title">Important Links</p>
                    <div class="footer_line">
                        <span></span>
                    </div>
                </div>
                <ul class="footer_ul mt-2 mt-sm-3">
                    <li><a href="#">Bookings</a></li>
                    <li><a href="#">Reports</a></li>
                    <li><a href="#">Discussion Board</a></li>
                    <li><a href="#">Why Diagnostic</a></li>
                </ul>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 mt-4 mt-lg-0">
                <div>
                    <p class="footer_title">Contact Us</p>
                    <div class="footer_line">
                        <span></span>
                    </div>
                </div>
                <ul class="footer_ul mt-1 mt-sm-3">
                    <li><a href="#"><b>Email : </b>diagnosticcare.in@gmail.com</a></li>
                    <li><a href="#"><b>Phone : </b>+91-9212535996</a></li>
                    <li><a href="#"><b>Address : </b>149/1, Opp. Max Hosiptal, Malviya Nagar, New Delhi</a></li>
                </ul>
            </div>
        </div>
        <hr class="footer_hr">
        <div class="row common_padding m_bottom">
            <div class="col-12 text-center">
                <p>&copy <?php echo date('Y') ?> <?php echo SITE_NAME ?> All Rights Reserved. | Design and Developed by
                    <a href="https://blumox.com?utm_source=<?php echo strtolower(SITE_NAME) ?>">Blumox</a>
                </p>
            </div>

        </div>
    </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
</script>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="https://kit.fontawesome.com/da7bbb41f1.js" crossorigin="anonymous"></script>
<script src="<?php echo USER_JS ?>app.js"></script>
<!-- Initialize Swiper -->
<script>
var swiper = new Swiper(".mySwiper", {
    spaceBetween: 30,

    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});
var swiper = new Swiper(".partners_slider", {
    pagination: {
        el: ".swiper-pagination",
    },
    infinity: true,
    autoplay: {
        delay: 1500,
    },
    slidesPerView: 3,
    breakpoints: {
        768: {
            slidesPerView: 4
        },
        992: {
            slidesPerView: 6
        }
    },
});
var swiper = new Swiper(".package_slider", {
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    infinity: true,
    autoplay: {
        delay: 2000,
    },
    slidesPerView: 1,
    spaceBetween: 25,
    breakpoints: {
        768: {
            slidesPerView: 2,
            spaceBetween: 25,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 25,
        }
    },
});
</script>
</body>

</html>