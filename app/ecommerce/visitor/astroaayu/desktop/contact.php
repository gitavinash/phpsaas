<?php require_once 'header.php' ?>
<div class="common_banner">
    <h1 class="banner_title">Contact Us</h1>
</div>
<main class="container-fluid">
    <div class="row common_padding justify-content-center contact_bg py-3 py-sm-5">
        <div class="col-12 col-xl-6 px-3">
            <div class="col-12 text-center text-sm-start">
                <div class="row">
                    <h1 class="font_weight p-0 m-0 mb-sm-2">Contact Us</h1>
                    <h4 class="font_res_contact p-0 mt-1">
                        Any question or Suggestions? Write us a message and we will
                        contact you!
                    </h4>
                </div>
            </div>
            <div class="col-12 mt-0 mt-sm-3">
                <div class="form row">
                    <form method="post" action="">
                        <div class="col-12 mb-3 mb-sm-4 p-0">
                            <input type="text" class="form-control form_control3" id="exampleFormControlInput1"
                                placeholder="Your First Name" name="name" />
                        </div>
                        <div class="col-12 mb-3 mb-sm-4 p-0">
                            <input type="Phone" class="form-control form_control3" id="exampleFormControlInput1"
                                placeholder="Your Number" name="mobile" required />
                        </div>
                        <div class="col-12 mb-3 mb-sm-4 p-0">
                            <input type="email" class="form-control form_control3" id="exampleFormControlInput1"
                                placeholder="Your Email" name="email" />
                        </div>
                        <div class="col-12 mb-3 mb-sm-4 p-0">
                            <textarea class="form-control form_control3" name="message" rows="6"
                                placeholder="Your Message"></textarea>
                        </div>

                        <input type="hidden" name="type" value="enquiry">
                        <input type="hidden" name="action" value="new">
                        <input type="hidden" name="subject" value="Contact Us">
                        <input type="hidden" name="sender" value="bW9oaXRhZ2dhcndhbDExMTk0QGdtYWlsLmNvbQ==">
                        <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                        <input type="hidden" name="redirect_url"
                            value="/thank-you/?form_type=true&msg=Thank+you+for+your+query%2Ffeedback.+It+is+important+for+us.">
                        <input type="submit" class="submit_btn_contact verifyRecaptcha" value="Submit">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6 mt-3 mt-sm-4 ps-0 ps-xl-5">
            <div class="contact_bg_box">
                <h2 class="inside relative">Get In Touch</h2>
                <div class="mt-2 mt-sm-3 grid_cust">
                    <a href="mailto:Support@website.com" class="contact_icon_container">
                        <div class="contact_icon_box_page">
                            <span class="material-symbols-sharp"> mail </span>
                        </div>
                        <p>support@astroaayu.com</p>
                    </a>
                    <a href="tel:8505850919" class="contact_icon_container mt-3 mt-sm-0">
                        <div class="contact_icon_box_page">
                            <span class="material-symbols-sharp"> call </span>
                        </div>
                        <p>8505850919</p>
                    </a>
                    <a href="https://goo.gl/maps/RLKBsXvwD2YsLsdc8" class="contact_icon_container mt-3" target="_blank">
                        <div class="contact_icon_box_page">
                            <span class="material-symbols-sharp">
                                location_on
                            </span>
                        </div>
                        <p class="width_co">
                            A-62,Freedom fighter enclav,e<br />
                            Neb sarai Hill top apartment,<br /> New Delhi - 110068
                        </p>
                    </a>
                    <a href="#" class="contact_icon_container mt-3">
                        <div class="contact_icon_box_page">
                            <span class="material-symbols-sharp"> schedule </span>
                        </div>
                        <div>
                            <p>Mon-Sun: 10:00AM till 7:00PM</p>

                        </div>
                    </a>
                </div>
                <h2 class="inside relative mt-4">Social Media...</h2>
                <div class="mt-2 mt-sm-3 grid_cust2">
                    <a href="https://www.facebook.com/Astroaayu4u " class="contact_icon_container" target="_blank">
                        <div class="contact_icon_box_page color_change_social fb_bg">
                            <i class="fa-brands fa-facebook-f"></i>
                        </div>
                    </a>
                    <!-- <a href="https://twitter.com/astroaayu " class="contact_icon_container" target="_blank">
                        <div class="contact_icon_box_page color_change_social twitter_bg">
                            <i class="fa-brands fa-twitter"></i>
                        </div>
                    </a> -->
                    <a href="https://www.instagram.com/astroaayu" class="contact_icon_container" target="_blank">
                        <div class="contact_icon_box_page color_change_social instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </div>
                    </a>
                    <a href="https://www.youtube.com/channel/UCywB8KPcdGYwhTwHGPkuU2Q/" class="contact_icon_container"
                        target="_blank">
                        <div class="contact_icon_box_page color_change_social youtube">
                            <i class="fa-brands fa-youtube"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
<div class="">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7011.332849140068!2d77.23719599999998!3d28.517867!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0:0x1f219d508e940498!2sMohit%20Aggarwal%20Red%20Book%20Expert!5e0!3m2!1sen!2sin!4v1663996066166!5m2!1sen!2sin"
        width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<?php require_once 'footer.php' ?>