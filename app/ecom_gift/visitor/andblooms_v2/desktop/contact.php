<?php

require_once 'header.php';

?>
<main>
    <section class="contact_wrapper">
        <div class="container">
            <div class="row border_bottom">
                <div class="col-lg-6">
                    <div class="contact_wrapper__left">
                        <h3 class="is-font-45 dark-brown main_font_family text-capitalize">Get in touch</h3>
                        <p class="is-font-18 dark-brown ">We are here for you! how can we help you?</p>
                        <div class="contact_form">
                            <form action="">
                                <div class="contact_input">
                                    <input type="text" placeholder="Name" required>
                                </div>
                                <div class="contact_input">
                                    <input type="email" placeholder="Email" required>
                                </div>
                                <div class="contact_input">
                                    <input type="text" placeholder="Subject" required>
                                </div>
                                <div class="contact_input">
                                    <textarea name="" id="" rows="4" placeholder="Message" required></textarea>
                                </div>
                                <div class="submit--btn">
                                    <button class="global_btn dark-brown">submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact_wrapper__right">
                        <img src="assets/images/svg/contact_img.svg" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="map_wrapper">
            <div class="container">
                <div class="row map_flex">
                    <div class="col-lg-7">
                        <div class="map_wrapper__left">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14009.163673253448!2d77.08022521910576!3d28.62104179651956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d04ba6b064d0f%3A0xf609cdf712fe603e!2sJanakpuri%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1705920468860!5m2!1sen!2sin"
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="map_wrapper__right">
                            <h3 class="is-font-45 dark-brown text-capitalize">Contact detail</h3>
                            <div class="contact_details">
                                <p><img src="assets/images/svg/map.svg" class="img-fluid">A179/2 Andheria More, MG
                                    Road,
                                    New Delhi
                                    - 110074</p>
                                <a href="#"><img src="assets/images/svg/call.svg" class="img-fluid"
                                        alt="">+91-9910744119</a>
                                <a href="#"><img src="assets/images/svg/email.svg" class="img-fluid"
                                        alt="">sales@andblooms.com</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>
</main>

<?php

require_once 'footer.php';

?>