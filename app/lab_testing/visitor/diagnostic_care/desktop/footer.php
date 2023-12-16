<footer class="vartical-padding side-padding">
    <div class="container">
        <div class="row align-items-start footer-section">
            <div class="col-12 col-lg-4">
                <img src="<?php echo USER_IMAGES ?>footer.png " alt="" />

                <p class="py-2 text-white">
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
                    <li><a href="/popular-tests/">Populaar Tests</a></li>
                    <li><a href="/contact-us/">Contact Us</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h5 class="text-white">Our Policies</h5>
                <ul>
                    <li><a href="/terms-and-conditions/">Term of use</a></li>
                    <li><a href="/privacy-policy/">Privacy Policy</a></li>
                    <li><a href="/refund-policy/">Refund Policy</a></li>
                </ul>
            </div>

            <div class="col-12 col-lg-4 my-3 my-lg-0">
                <h5 class="text-white">Connect With us</h5>
                <ul>
                    <li class=" d-flex align-items-start ">
                        <span><img src="<?php echo USER_IMAGES ?>location.png" alt="" class="mx-2 mt-3" /></span>
                        <p class="p-0"> Diagnostic Services <br>149/1, Malviya Nagar Opp. Max Hosiptal,<br>New
                            Delhi - 110017</p>
                    </li>
                    <li>
                        <img src="<?php echo USER_IMAGES ?>gmail.png" alt="" class="mx-2" />diagnosticcare.in@gmail.com
                    </li>
                    <li>
                        <a href="tel:+91-9212535996"><img src="<?php echo USER_IMAGES ?>phone.png" alt=""
                                class="mx-2" />+91-9212535996</a>
                    </li>
                    <li>
                        <a href="tel:+91-7053430002"><img src="<?php echo USER_IMAGES ?>phone.png" alt=""
                                class="mx-2" />+91-7053430002</a>
                    </li>
                    <li>
                        <a href="tel:+91-9211443544"><img src="<?php echo USER_IMAGES ?>phone.png" alt=""
                                class="mx-2" />+91-9211443544</a>
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
                <!-- <h6 class="text-white text-end">
                    Design & Developed By
                    <a href="http://blumox.com/" class="text-white">Blumox Technologies</a>
                </h6> -->
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- sections end -->
<?php include_once NOTIFICATION . 'index.php' ?>

<script src="https://kit.fontawesome.com/da7bbb41f1.js" crossorigin="anonymous"></script>
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
<?php

if (BASE_URL == '/') {
    echo '<script>
        const prescription = document.querySelector(".prescription");
        const prescriptionForm = document.querySelector(".prescription-form");
        const opacity = document.querySelector(".opacity");
        prescription.addEventListener("click", () => {
          prescriptionForm.classList.add("active");
          opacity.classList.add("active");
        });
      
        const closeForm = document.querySelector(".close-form");
        closeForm.addEventListener("click", () => {
          prescriptionForm.classList.remove("active");
          opacity.classList.remove("active");
        });
      
        opacity.addEventListener("click", () => {
          prescriptionForm.classList.remove("active");
          opacity.classList.remove("active");
        });</script>';
}

?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
</script>
<script src="<?php echo PLUGIN_LIBRARY ?>slick/slick.min.js"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js"></script>
<script type="text/javascript" src="<?php echo PLUGIN_LIBRARY ?>datepicker/datepicker.min.js"></script>
<script src="<?php echo USER_JS ?>app.js"></script>
</body>

</html>