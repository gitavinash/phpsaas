<footer class="vartical-padding side-padding">
    <div class="container">
        <div class="row align-items-start footer-section">
            <div class="col-12 col-lg-4">
                <img src="<?php echo USER_IMAGES ?>footer.png " alt="" />

                <p class="py-2 text-white lh-lg">
                    Diagnostic Services capabilities are top-notch in assessing its
                    patients with the care and expertise of reliable doctors and staff
                    members to deliver a serene solution for your health conditions.
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <h5 class="text-white">Quick Links</h5>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/about-us/">About Us</a></li>
                    <li><a href="/populaar-tests/">Populaar Tests</a></li>
                    <li><a href="/contact-us/">Contact Us</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h5 class="text-white">Our Policies</h5>
                <ul>
                    <li><a href="/terms-of-use/">Term of use</a></li>
                    <li><a href="/privacy-policy/">Privacy Policy</a></li>
                    <li><a href="/refund-policy/">Refund Policy</a></li>
                </ul>
            </div>

            <div class="col-12 col-lg-4 my-3 my-lg-0">
                <h5 class="text-white">Connect With us</h5>
                <ul>
                    <li class=" d-flex align-items-start ">
                        <span><img src="<?php echo USER_IMAGES ?>location.png" alt="" class="mx-2 mt-3" /></span>
                        <p class="lh-lg p-0"> Diagnostic Services <br> 149/1, Malviya Nagar Opp. Max Hosiptal, New
                            Delhi - 110017</p>
                    </li>
                    <li>
                        <img src="<?php echo USER_IMAGES ?>gmail.png" alt="" class="mx-2" />diagnosticcare.in@gmail.com
                    </li>
                    <li>
                        <img src="<?php echo USER_IMAGES ?>phone.png" alt="" class="mx-2" />+91
                        9212535996
                    </li>
                </ul>
            </div>
        </div>

        <div class="row pt-4 justify-content-between">
            <div class="col-6">
                <h6 class="text-white text-start">
                    Copyright &copy; 2023 All rights reserved
                </h6>
            </div>

            <div class="col-6">
                <h6 class="text-white text-end">
                    Design & Developed By
                    <a href="http://blumox.com/" class="text-white">Blumox Technologies</a>
                </h6>
            </div>
        </div>
    </div>
</footer>
<script src="https://kit.fontawesome.com/da7bbb41f1.js" crossorigin="anonymous"></script>
<script src="<?php echo USER_JS ?>app.js"></script>
<script>
    (function(w, d, s, u) {
        w.gbwawc = {
            url: u,
            options: {
                waId: "+91-9212535996",
                siteName: "Diagnosticcare",
                siteTag: "Usually Response in 5 Minute",
                siteLogo: "https://waw.gallabox.com/chatbotavatars/2.png",
                widgetPosition: "RIGHT",
                triggerMessage: "",
                welcomeMessage: "Welcome to DiagnosticCare, how can I assist you today?",
                brandColor: "#25D366",
                messageText: "Are you looking for Diagnostic Service?",
                replyOptions: ["", "", ""],
            },
        };
        var h = d.getElementsByTagName(s)[0],
            j = d.createElement(s);
        j.async = true;
        j.src = u + "/whatsapp-widget.min.js?_=" + Math.random();
        h.parentNode.insertBefore(j, h);
    })(window, document, "script", "https://waw.gallabox.com");
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(".pt-slider").slick({
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
                    slidesToShow: 2,
                    slidesToScroll: 2,
                },
            },
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ],
    });

    $(".packge-slider").slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 3,
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

<script>
    const prescription = document.querySelector('.Prescription');
    const prescriptionForm = document.querySelector('.prescription-form');
    const opacity = document.querySelector('.opacity');
    prescription.addEventListener('click', () => {
        prescriptionForm.classList.add('active');
        opacity.classList.add('active')
    })

    const closeForm = document.querySelector('.close-form');
    closeForm.addEventListener('click', () => {
        prescriptionForm.classList.remove('active')
        opacity.classList.remove('active')
    })

    opacity.addEventListener('click', () => {
        prescriptionForm.classList.remove('active')
        opacity.classList.remove('active')
    })
</script>
</body>

</html>