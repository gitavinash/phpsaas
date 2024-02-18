<?php

    require_once 'header.php';

?>
<main>
    <section class="contact_wrapper">
        <div class="container">
            <div class="contact_wrapper__heading">
                <h3>Contact us</h3>
                <p>get in touch</p>
            </div>
        </div>
    </section>
    <section class="touch_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="touch_wrapper__left">
                        <h3>Get in Touch Today!</h3>
                        <div class="find_us">
                            <div class="our_address">
                                <div class="icon">
                                    <img src="assest/icon/Location.svg" class="img-fluid" alt="">
                                </div>
                                <div class="details">
                                    <h3>Find us</h3>
                                    <p>114 , 1st Floor , Kirti Shikhar Tower, Distt Center , Janakpuri , New Delhi
                                        110058</p>
                                </div>
                            </div>
                            <div class="our_address">
                                <div class="icon">
                                    <img src="assest/icon/message_nav.svg" class="img-fluid" alt="">
                                </div>
                                <div class="details">
                                    <h3>Write to us</h3>
                                    <a href="#">reservations@delightstay.co.in</a>
                                </div>
                            </div>
                            <div class="our_address">
                                <div class="icon">
                                    <img src="assest/icon/Call.svg" class="img-fluid" alt="">
                                </div>
                                <div class="details">
                                    <h3>call us</h3>
                                    <a href="tel:+91-8929604524">+91-8929604524</a><br />
                                    <a href="tel:+91-9773889527">+91-9773889527</a><br />
                                    <a href="tel:+91-9728872206">+91-9728872206</a><br />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="touch_wrapper__right">
                        <h3>Have Queries?</h3>
                        <p>Our team will be in touch with you soon</p>
                        <div class="form_wrapper">
                            <form action="">
                                <div class="form_wrapper__input">
                                    <input type="text" placeholder="First Name *">
                                    <input type="text" placeholder="Last Name *">
                                </div>
                                <div class="form_wrapper__input">
                                    <input type="number" placeholder="Mobile Number *">
                                    <input type="email" placeholder="Email *">
                                </div>
                                <div class="form_wrapper__input">
                                    <textarea name="" id="" cols="30" rows="4" placeholder="Message *"></textarea>
                                </div>
                                <div class="submit_form">
                                    <button class="global_btn text-uppercase">submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="map_wrapper">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14009.166186427328!2d77.0802252160038!3d28.621022960733747!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d04ba6b064d0f%3A0xf609cdf712fe603e!2sJanakpuri%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1707471444282!5m2!1sen!2sin"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
</main>

<?php

    require_once 'footer.php';

?>