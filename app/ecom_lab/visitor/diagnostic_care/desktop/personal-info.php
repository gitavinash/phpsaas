<?php

require_once 'header.php';

?>
<main>
    <section class="container-fluid container-xl margin_top bg_contact">

        <div class="row justify-content-between">
            <div class="col-12 col-md-7 mt-3 pe-0 pe-md-3 order-2 order-md-1 mt-4 mt-md-0">
                <div class="row">
                    <div class="col-12 mb-3">
                        <h1 class="common_title">Personal Information</h1>
                    </div>
                    <div class="col-6 pe-2 mb-3 mb-sm-4">
                        <input type="text" placeholder="Enter Your Name" class="form-control form_control per_form_in">
                    </div>
                    <div class="col-6 ps-2 mb-3 mb-sm-4">
                        <input type="number" placeholder="Enter Your number"
                            class="form-control form_control per_form_in">
                    </div>
                    <div class="col-6 pe-2 mb-3 mb-sm-4">
                        <label for="Date" class="form_label">Appointment Date</label>
                        <input type="date" class="form-control form_control per_form_in" id="Date">
                    </div>
                    <div class="col-6 ps-2 mb-3 mb-sm-4">
                        <label for="time" class="form_label">Appointment Time</label>
                        <input type="time" class="form-control form_control per_form_in" id="time">
                    </div>
                    <div class="col-12 mb-3 mb-sm-4">
                        <input type="email" placeholder="Enter Your Email"
                            class="form-control form_control per_form_in">
                    </div>
                    <div class="col-12 mb-3 mb-sm-4">
                        <textarea rows="5" class="form-control form_control per_form_in"
                            placeholder="Your Address"></textarea>
                    </div>
                    <div class="col-12">
                        <a class="sub_btn sub_btn2 cont_btn" href="#">Continue Payment</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5 col-lg-4 ps-0 ps-md-3 order-1 order-md-2">
                <h4 class="common_title">Booking Summary</h4>
                <div class="cont_booking mt-2">
                    <div class="cont_booking1">
                        <div class="cont_book1">
                            <p class="title_book">MediBuddy Basic Extended Health Check-up</p>
                        </div>
                        <div class="cont_book2">
                            <p>₹799</p>
                            <del>₹899</del>
                        </div>
                    </div>
                    <hr class="hr_book">
                    <div class="cont_booking1">
                        <div class="cont_book1">
                            <p class="title_book">Total M.R.P.</p>
                        </div>
                        <div class="cont_book2">
                            <p>₹2048</p>
                        </div>
                    </div>
                    <hr class="hr_book">
                    <div class="cont_booking1">
                        <div class="cont_book1">
                            <p class="title_book">Price Discount</p>
                        </div>
                        <div class="cont_book2">
                            <p>₹1000
                            </p>
                        </div>
                    </div>
                    <div class="cont_booking1 total_amount">
                        <div class="cont_book1">
                            <p class="title_book">TOTAL AMOUNT</p>
                        </div>
                        <div class="cont_book2">
                            <p>₹1048
                            </p>
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