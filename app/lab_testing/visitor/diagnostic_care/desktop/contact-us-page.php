<?php

require_once 'header.php';

?>
<section class="hero side-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <h2 class="isfont-40 text-white my-3 is-font600 text-center text-lg-start">
                    <?php echo $page_title ?>
                </h2>
            </div>
            <div class="col-12 col-lg-5 my-4 my-lg-0">
                <img src="<?php echo USER_IMAGES ?>contact.png" alt=""
                    class="img-fluid d-none d-lg-block m-auto ms-lg-auto" />
            </div>
        </div>
    </div>
</section>

<section class="vartical-padding side-padding checkout">
    <div class="container">
        <div>
            <h3 class="isfont-40 is-font600 is-color-blue">Get in touch</h3>
            <div class="row vartical-padding align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="contact-form">
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="name" placeholder="Full name"
                                            required />
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="mobile" id="exampleInputEmail1"
                                            placeholder="Phone" required />
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="email" id="exampleInputEmail1"
                                            placeholder="Email" />
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="test_details"
                                            id="exampleInputEmail1" placeholder="Test Details" />
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="address" placeholder="Address..."
                                            id="floatingTextarea" style="height: 100px"></textarea>
                                    </div>

                                    <input type="hidden" name="type" value="enquiry">
                                    <input type="hidden" name="action" value="new">
                                    <input type="hidden" name="subject" value="Contact us form">
                                    <input type="hidden" name="sender"
                                        value="<?php echo base64_encode('diagnosticcare.in@gmail.com') ?>">
                                    <input type="hidden" name="redirect_url"
                                        value="/thank-you/?form_type=true&msg=Thank+you+for+your+query%2Ffeedback.+It+is+important+for+us.">
                                    <input type="submit" class="btn w-100 btn1" value="Submit">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="contact-details">
                        <h3 class="isfont-40 is-font600">Contact Details</h3>


                        <div class="my-5">
                            <div class="d-flex my-3">
                                <img src="<?php echo USER_IMAGES ?>loc.png" alt="" class="mx-3 c-icon" />
                                <div>
                                    <h2 class="isfont-28">Address</h2>
                                    <p class="p-0"> Diagnostic Services <br>149/1, Malviya Nagar Opp. Max
                                        Hosiptal, New
                                        Delhi - 110017</p>
                                </div>
                            </div>

                            <div class="d-flex my-3">
                                <img src="<?php echo USER_IMAGES ?>call.png" alt="" class="mx-3 c-icon" />
                                <div>
                                    <h2 class="isfont-28">Phone</h2>
                                    <p>Phone: +91 9212535996</p>
                                </div>
                            </div>

                            <div class="d-flex my-3">
                                <img src="<?php echo USER_IMAGES ?>msg.png" alt="" class="mx-3 c-icon" />
                                <div>
                                    <h2 class="isfont-28">Email</h2>
                                    <p>diagnosticcare.in@gmail.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d28043.00056474799!2d77.211469!3d28.528444!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce1f67f45f229%3A0x4f3624b2fc617bc!2sDiagnostic%20Services!5e0!3m2!1sen!2sin!4v1696501058347!5m2!1sen!2sin"
        width="100%" height="450" style="border: 0" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>

<?php

require_once 'footer.php';

?>