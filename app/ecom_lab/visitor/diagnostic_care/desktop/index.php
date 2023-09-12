<?php

require_once 'header.php';

?>
<!-- Swiper -->
<div class="swiper mySwiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide swiper_img">

            <?php

            if ($sliderList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "slide WHERE slide_status=1 AND slide_site_id=" . APP_SITE_ID . " LIMIT 0, 1")) {

                if (mysqli_num_rows($sliderList) != 0) {

                    while ($sliderData = mysqli_fetch_assoc($sliderList)) {
                        extract($sliderData); ?>

            <img src="<?php echo $slide_image ?>">
            <div class="swiper_content flex_prop justify-content-between common_padding">
                <div class="swiper_content2">
                    <h1 class="hero_title"><?php echo $slide_title ?></h1>

                    <p class="mt-1 mt-sm-2 d_none"><?php echo $slide_description ?></p>
                    <a href="<?php echo $slide_link ?>" class="book_btn book_btn_hero mt-3">Book Now</a>
                </div>

                <?php
                    }
                }
            }

                ?>
            </div>
        </div>

    </div>

</div>

<main>
    <!--Pres-->
    <section class="container-fluid">
        <div class="container-fluid container-xl">
            <div class="row common_padding common_padding_zero">
                <div class="col-12 col-lg-8 col-xl-7 margin_minus ms-auto ">
                    <div class="mb-3 d_none2">
                        <h3 class="common_title2">Quality Consult With Doctors</h3>
                        <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloremque beatae
                            obcaecati assumenda cumque alias, nobis incidunt? Voluptatum voluptate rerum ipsam.</p>
                    </div>
                    <div class="pres_grid">

                        <div class="pres_container flex_prop">
                            <div class="pres_img">
                                <img src="<?php echo USER_IMAGES ?>emergency-call.png">
                            </div>
                            <div class="pres_content">
                                <p class="pres_title">Call our health advisor</p>
                                <p>Our team of experts will guide you</p>
                            </div>
                        </div>
                        <div class="pres_container flex_prop">
                            <div class="pres_img">
                                <img src="<?php echo USER_IMAGES ?>whatsapp.png">
                            </div>
                            <div class="pres_content">
                                <p class="pres_title">Book through WhatsApp</p>
                                <p>To book a lab test, say 'Hi'</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Choose-->
    <section class="container-fluid margin_top">
        <div class="container-fluid container-xl">
            <div class="row common_padding">

                <div class="col-12">
                    <div class="grid_more_info">
                        <div class="more_cont">
                            <div class="flex_prop justify-content-between more_cont_semi">
                                <figure>
                                    <img src='<?php echo USER_IMAGES ?>tab-icon.png'>
                                </figure>
                                <div class="num">
                                    <p>01</p>
                                </div>
                            </div>
                            <div class="more_content">
                                <p class="title_1">
                                    Quick Pickup</p>
                                <p class="font_small">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sit
                                    cupiditate voluptatum nisi sequi sed labore!</p>
                            </div>
                        </div>
                        <div class="more_cont">
                            <div class="flex_prop justify-content-between more_cont_semi">
                                <figure>
                                    <img src='<?php echo USER_IMAGES ?>tab-icon.png'>
                                </figure>
                                <div class="num">
                                    <p>02</p>
                                </div>
                            </div>
                            <div class="more_content">
                                <p class="title_1">Fast Report Delivery</p>
                                <p class="font_small">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sit
                                    cupiditate voluptatum nisi sequi sed labore!</p>
                            </div>
                        </div>
                        <div class="more_cont">
                            <div class="flex_prop justify-content-between more_cont_semi">
                                <figure>
                                    <img src='<?php echo USER_IMAGES ?>tab-icon.png'>
                                </figure>
                                <div class="num">
                                    <p>03</p>
                                </div>
                            </div>
                            <div class="more_content">
                                <p class="title_1">
                                    Affordable Price</p>
                                <p class="font_small">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sit
                                    cupiditate voluptatum nisi sequi sed labore!</p>
                            </div>
                        </div>
                        <div class="more_cont">
                            <div class="flex_prop justify-content-between more_cont_semi">
                                <figure>
                                    <img src='<?php echo USER_IMAGES ?>tab-icon.png'>
                                </figure>
                                <div class="num">
                                    <p>04</p>
                                </div>
                            </div>
                            <div class="more_content">
                                <p class="title_1">
                                    100% Report Accuracy</p>
                                <p class="font_small">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sit
                                    cupiditate voluptatum nisi sequi sed labore!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--About-->
    <section class="container-fluid margin_top bg_light_b">
        <div class="container-fluid container-xl">
            <div class="row align-items-center common_padding">
                <div class="col-12 col-lg-6 pe-0 pe-lg-3">
                    <p class="semi_title">DIAGNOSTIC SERVICES HISTORY</p>
                    <h1 class="common_title">Why Choose Diagnostic Services?</h1>
                    <p class="mt-2 color_blue font_big"><b>Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                            Similique, unde!</b></p>
                    <p class="mt-2">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Autem, quia, mollitia
                        tempore, sed cupiditate odio fugiat quis fuga quibusdam laboriosam debitis non dolorem excepturi
                        ullam ex dolor culpa placeat fugit officiis? Mollitia exercitationem accusamus deleniti quas
                        vero saepe nihil unde.</p>
                    <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam ipsum velit deleniti
                        iste deserunt suscipit at dignissimos molestiae animi nostrum.</p>
                    <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam ipsum velit deleniti
                        iste deserunt suscipit at dignissimos molestiae animi nostrum.</p>
                    <a href="personal-info.html" class="book_btn2 mt-3">Book Now</a>
                </div>
                <div class="col-12 col-lg-6 ps-0 ps-lg-3 mt-3 mt-lg-0">
                    <img src="<?php echo USER_IMAGES ?>ab-img.png">
                </div>
            </div>
        </div>
    </section>
    <!--Contact Us-->
    <div class="container-fluid container-xl">
        <div class="row form_holder3">


            <div class="col-12 col-lg-6 pe-4 py-3">
                <img src="<?php echo USER_IMAGES ?>im1.jpg" alt="" class="img-fluid">
            </div>

            <div class="col-12 col-lg-6 py-3">
                <div class="col-12 col-sm-12 col-lg-12  relative">
                    <h3 class="common_title2">Schedule Your Appointment: Unlocking Your Preferred Time Slot</h3>
                    <p class="mt-2 d_none">Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloremque beatae
                        obcaecati assumenda cumque alias, nobis incidunt? Voluptatum voluptate rerum ipsam.</p>

                    <div class="row mt-3">
                        <div class="col-12 col-sm-6 mb-3">
                            <input type="text" placeholder="Enter Name" class="form-control form_control form_control2">
                        </div>
                        <div class="col-12 col-sm-6 ps-0 ps-sm-3 mb-3">
                            <input type="number" placeholder="Enter Phone"
                                class="form-control form_control form_control2">
                        </div>
                        <div class="col-12 col-sm-12 ps-0  mb-3">
                            <!-- <input type = "number" placeholder="Enter PinCode" class="form-control form_control form_control2"> -->
                            <textarea class="form-control form_control2" placeholder="Address"
                                id="floatingTextarea"></textarea>
                        </div>
                        <div class="col-12 my-3 d-flex ">
                            <button class="sub_btn4">Submit Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Popular Package-->
    <section class="container-fluid margin_top relative">
        <div class="container-fluid container-xl">
            <div class="row common_padding">
                <div class="col-12 text-center">
                    <h1 class="common_title">Popular Packages</h1>
                </div>
                <div class="col-12 mt-2 mt-sm-3 swiper package_slider">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="des_c_p flex_prop">
                                <div class="des_c_p_img relative">
                                    <img src="<?php echo USER_IMAGES ?>test1.png">
                                    <div class="off_cont">
                                        <p>- 10% Off</p>
                                    </div>
                                </div>
                                <div class="des_c_content">
                                    <p class="des_c_content_title">Senior Citizen Health Package (Male)</p>
                                    <p class="font_14 mt-1">
                                        Alanine amino Transferase (ALT/SGPT), Alkaline Pho...
                                    </p>
                                    <p class="mt-1">Starting From: <b>₹80.00</b></p>
                                    <a href="personal-info.html" class="read_btn read_btn2 mt-2">Book Now</a>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="des_c_p flex_prop">
                                <div class="des_c_p_img relative">
                                    <img src="<?php echo USER_IMAGES ?>test2.png">
                                    <div class="off_cont">
                                        <p>- 10% Off</p>
                                    </div>
                                </div>
                                <div class="des_c_content">
                                    <p class="des_c_content_title">Senior Citizen Health Package (Male)</p>
                                    <p class="font_14 mt-1">
                                        Alanine amino Transferase (ALT/SGPT), Alkaline Pho...
                                    </p>
                                    <p class="mt-1">Starting From: <b>₹80.00</b></p>
                                    <a href="personal-info.html" class="read_btn read_btn2 mt-2">Book Now</a>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="des_c_p flex_prop">
                                <div class="des_c_p_img relative">
                                    <img src="<?php echo USER_IMAGES ?>test3.png">
                                    <div class="off_cont">
                                        <p>- 10% Off</p>
                                    </div>
                                </div>
                                <div class="des_c_content">
                                    <p class="des_c_content_title">Senior Citizen Health Package (Male)</p>
                                    <p class="font_14 mt-1">
                                        Alanine amino Transferase (ALT/SGPT), Alkaline Pho...
                                    </p>
                                    <p class="mt-1">Starting From: <b>₹80.00</b></p>
                                    <a href="personal-info.html" class="read_btn read_btn2 mt-2">Book Now</a>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="des_c_p flex_prop">
                                <div class="des_c_p_img relative">
                                    <img src="<?php echo USER_IMAGES ?>test1.png">
                                    <div class="off_cont">
                                        <p>- 10% Off</p>
                                    </div>
                                </div>
                                <div class="des_c_content">
                                    <p class="des_c_content_title">Senior Citizen Health Package (Male)</p>
                                    <p class="font_14 mt-1">
                                        Alanine amino Transferase (ALT/SGPT), Alkaline Pho...
                                    </p>
                                    <p class="mt-1">Starting From: <b> ₹80.00</b></p>
                                    <a href="personal-info.html" class="read_btn read_btn2 mt-2">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <!--Tests Available-->
    <section class="container-fluid margin_top bg_light_b">
        <div class="container-fluid container-xl">
            <div class="row common_padding">
                <div class="col-12 text-center text-sm-start">

                    <div class="mb-1">
                        <p class="semi_title">Range Of Blood Test</p>
                        <div class="line">
                            <span class="line2"></span>
                        </div>
                    </div>
                    <h1 class="common_title">350+ Blood Test Available</h1>
                </div>
                <div class="col-12 mt-3 mt-sm-4">
                    <div class="grid_destinations">
                        <a class="des_cont_p" href="#">
                            <img src="<?php echo USER_IMAGES ?>test1.png" />
                            <div class="des_content_p">
                                <div class="flex_prop justify-content-between">
                                    <p class="des_title">Basic test</p>
                                    <div class="ratings flex_prop">
                                        <p class="font_14">(Reviews 4.5)</p>
                                        <div class="ratings_star ratings_star2 flex_prop">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list_test">
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        VITAMIN D PLUS
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        25 - HYDROXYVITAMIN D, SERUM
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        OSTEOSCREEN PANEL - I
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        LIVER SCREEN
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        LIVER FUNCTION TEST (WITHOUT GGT)
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        KIDNEY PANEL - II
                                    </li>
                                </ul>

                            </div>
                        </a>
                        <a class="des_cont_p" href="#">
                            <img src="<?php echo USER_IMAGES ?>test2.png" />
                            <div class="des_content_p">
                                <div class="flex_prop justify-content-between">
                                    <p class="des_title">Advance test</p>
                                    <div class="ratings flex_prop">
                                        <p class="font_14">(Reviews 4.5)</p>
                                        <div class="ratings_star ratings_star2 flex_prop">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list_test">
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        VITAMIN D PLUS
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        25 - HYDROXYVITAMIN D, SERUM
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        OSTEOSCREEN PANEL - I
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        LIVER SCREEN
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        LIVER FUNCTION TEST (WITHOUT GGT)
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        KIDNEY PANEL - II
                                    </li>
                                </ul>

                            </div>
                        </a>
                        <a class="des_cont_p" href="#">
                            <img src="<?php echo USER_IMAGES ?>test3.png" />
                            <div class="des_content_p">
                                <div class="flex_prop justify-content-between">
                                    <p class="des_title">Featured test</p>
                                    <div class="ratings flex_prop">
                                        <p class="font_14">(Reviews 4.5)</p>
                                        <div class="ratings_star ratings_star2 flex_prop">
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                                <ul class="list_test">
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        VITAMIN D PLUS
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        25 - HYDROXYVITAMIN D, SERUM
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        OSTEOSCREEN PANEL - I
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        LIVER SCREEN
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        LIVER FUNCTION TEST (WITHOUT GGT)
                                    </li>
                                    <li>
                                        <div class="circle_1">
                                            <div class="circle_2"></div>
                                        </div>
                                        KIDNEY PANEL - II
                                    </li>
                                </ul>

                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container-fluid margin_top">
        <div class="container-fluid container-xl">
            <div class="row common_padding">
                <div class="col-12">
                    <div class="mb-1">
                        <p class="semi_title">100+ Partner Labs</p>
                        <div class="line">
                            <span class="line2"></span>
                        </div>
                    </div>
                    <h1 class="common_title">A Few Of #OurBloodRelations</h1>
                </div>
                <div class="col-12 mt-0 mt-sm-2">
                    <div class="swiper partners_slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide partner_img">
                                <img src="<?php echo USER_IMAGES ?>p1.png">
                            </div>
                            <div class="swiper-slide partner_img">
                                <img src="<?php echo USER_IMAGES ?>p2.png">
                            </div>
                            <div class="swiper-slide partner_img">
                                <img src="<?php echo USER_IMAGES ?>p3.png">
                            </div>
                            <div class="swiper-slide partner_img">
                                <img src="<?php echo USER_IMAGES ?>p4.jpg">
                            </div>
                            <div class="swiper-slide partner_img">
                                <img src="<?php echo USER_IMAGES ?>p5.png">
                            </div>
                            <div class="swiper-slide partner_img">
                                <img src="<?php echo USER_IMAGES ?>p6.jpg">
                            </div>
                            <div class="swiper-slide partner_img">
                                <img src="<?php echo USER_IMAGES ?>p7.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<div class="margin_top map_minus">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14021.445494587508!2d77.21200903426306!3d28.528855842013467!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce1f67f45f229%3A0x4f3624b2fc617bc!2sDiagnostic%20Services!5e0!3m2!1sen!2sin!4v1691778509093!5m2!1sen!2sin"
        width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
<?php

require_once 'footer.php';

?>