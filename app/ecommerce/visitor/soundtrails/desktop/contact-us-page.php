<?php require_once('header.php'); ?>
<!-- navbar -->
<div class="container-fluid">
    <div class="row product_banner_section sidepadding hero align-items-center py-5">
        <div class="col-12 text-center my-5">
            <h5>Home / <span>Contact Us</span></h5>
        </div>
    </div>
</div>
<!-- hero -->

<!-- end -->
</section>

<section class="vertical-padding sidepadding">
    <div class="container-fluid">
        <div class="row py-3">
            <h1 class="text-white text-center">Need help?<br /><br /></h1>
            <h2 class="text-white text-center">
                Have a question about an order, or about getting in
                touch?<br />
                We’re always happy to hear from you.
            </h2>

            <div class="row vertical-padding my-5">
                <div class="col-12 col-lg-4 col-md-4 col-sm-12 side-boder d-flex justify-content-center text-center">
                    <div>
                        <a href="https://api.whatsapp.com/send?phone=919560030646&text=Hi." target="_blank">
                            <img src="<?php echo USER_IMAGES ?>3.png" alt="" />
                            <h4 class="font-size-57 fw-bold mt-3 text-white text-center my-3">
                                <span class="font-size-40">WhatsApp</span>
                            </h4>
                            <h4 class="text-white text-center">+91-9560030646</h4>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-lg-4 col-md-4 side-boder d-flex justify-content-center text-center">
                    <div>
                        <a href="tel:+91-9560030646">
                            <img src="<?php echo USER_IMAGES ?>1.png" alt="" style="padding: 6px 0px" />
                            <h4 class="font-size-57 fw-bold mt-3 text-white text-center my-3">
                                <span class="font-size-40">Call Us</span>
                            </h4>
                            <h4 class="text-white text-center">+91-9560030646</h4>
                        </a>
                    </div>
                </div>

                <div
                    class="col-12 col-lg-4 col-md-4 col-sm-12 side-boder d-flex justify-content-center text-center border-none">
                    <div>
                        <a href="mailto:support@soundtrails.in">
                            <img src="<?php echo USER_IMAGES ?>2.png" alt="" style="padding: 9px 0px" />
                            <h4 class="font-size-57 fw-bold mt-3 text-white text-center my-3">
                                <span class="font-size-40">Write To us</span>
                            </h4>
                            <h4 class="text-white text-center">support@soundtrails.in</h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="form-data sidepadding mb-5">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8">
                <form method="post" action="">
                    <h3 class="text-white text-center">Fill this form to contact us.<br /><br /></h3>
                    <div class="d-flex flex-column">
                        <input type="text" class="forms" name="name" placeholder="Enter Your name" />

                        <input type="text" class="forms" name="email" placeholder="Enter Your Email" />
                        <input type="text" class="forms" name="mobile" placeholder="Enter Your phone" />
                        <textarea name="message" id="" cols="30" rows="10" placeholder="Message"></textarea>

                        <input type="hidden" name="type" value="enquiry">
                        <input type="hidden" name="action" value="new">
                        <input type="hidden" name="subject" value="Contact Us">
                        <input type="hidden" name="sender" value="<?php echo base64_encode('sahil@soundtrails.in') ?>">
                        <input type="hidden" name="redirect_url"
                            value="/thank-you/?form_type=true&msg=Thank+you+for+your+query%2Ffeedback.+It+is+important+for+us.">
                        <input type="submit" value="Submit" class="submit  text-center" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="vertical-padding sidepadding d-flex align-content-center">
    <div class="container-fluid">
        <div class="row d-flex justify-content-center text-center">
            <div class="col-12 col-lg-6">
                <h5 class="text-white font-size-25">
                    Stay Connected With Our Email Updates
                </h5>
            </div>
            <div class="col-12 col-lg-6">
                <div class="newsleeter d-flex m-0 justify-content-center mt-3 mt-md-0">
                    <input type="search" class="newsleeter-bar m-0" placeholder="Enter Your Email" />
                    <button class="btn1 m-0">Submit</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- testimonail -->

<!-- /testimonial -->

<!-- treding -end -->
<?php require_once('footer.php') ?>