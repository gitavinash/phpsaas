<?php

require_once 'header.php';

?>
<main>
    <section class="hero_common flex_prop justify-content-center container-fluid bg_prop contact_bg height_control">
        <div class="container text-center">
            <!-- <h1 class="banner_title2">Contact Us</h1> -->
        </div>
    </section>
    <section class="container-fluid container-xl margin_top">
        <div class="row justify-content-center common_padding">
            <div class="col-12 col-sm-5 col-lg-4">
                <h2 class="common_title">Contact Us</h2>
                <p class="mt-2">
                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Est, facere odit nesciunt dolore esse
                    corrupti officia! Enim placeat delectus modi.
                </p>
                <ul class="mt-3 location_contact relative ms-3 ps-3 ps-sm-2 flex_prop">
                    <li>
                        <div>
                            <p class="des_title">1. Office Address</p>
                            <p>10153 W California Ave, Beach Park, IL 60099.</p>
                        </div>
                    </li>
                    <li>
                        <div>
                            <p class="des_title">2. Email Address</p>
                            <p>demo@gmail.com.</p>
                        </div>
                    </li>
                    <li>
                        <div>
                            <p class="des_title">3. Phone Number</p>
                            <p>+91-XXX-XXX-XXXX.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-sm-7 col-lg-6 bg_contact_form">
                <div class="row">
                    <div class="col-12">
                        <div class="flex_prop contact_cont1">
                            <div class="contact_icon2">
                                <img src="imgs/envelope.png" />
                            </div>
                            <p class="ev_p">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorum beatae, facilis quam
                                debitis dignissimos incidunt?
                            </p>
                        </div>
                        <div class="row mt-2 p-2 p-sm-4">
                            <div class="col-6 pe-2 mb-3 mb-sm-4">
                                <input type="text" placeholder="Enter Your Name" class="form-control form_control" />
                            </div>
                            <div class="col-6 ps-2 mb-3 mb-sm-4">
                                <input type="text" placeholder="Enter Your Number" class="form-control form_control" />
                            </div>
                            <div class="col-6 pe-2 mb-3 mb-sm-4">
                                <input type="text" placeholder="Enter Your Email" class="form-control form_control" />
                            </div>
                            <div class="col-6 ps-2 mb-3 mb-sm-4">
                                <select name="" id="" class="form-select form_control">
                                    <option value="">Select Your Test</option>

                                </select>
                            </div>
                            <div class="col-12 mb-3 mb-sm-4">
                                <textarea name="" id="" rows="7" class="form-control form_control"
                                    placeholder="Your Message"></textarea>
                            </div>
                            <div class="col-12">
                                <button class="sub_btn">Submit Now</button>
                            </div>
                            <p class="col-12 mt-3 text-center">
                                Our customer service executives are available 24*7 to help
                                you.. Call Us At <a href="#">999-99-9999</a>
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