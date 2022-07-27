<?php

    require_once 'header.php';

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner" style="background-image: url(<?php echo $service_image ?>)">
                    <div class="inner_content">
                        <p>
                            <?php echo ucwords($service_title) ?></p>
                        <div class="text-start"><a href="index.html">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span>Service</span>
                            <span><i
                                    class="fa fa-chevron-right"></i></span><span><?php echo ucwords($service_title) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section_spacing_top ">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-12">
                        <div class="w-100 h100">
                            <div class="section_heading">
                                <h3 class="text-start mb-md-4"><?php echo ucwords($service_title) ?></h3>
                            </div>

                            <div class="description">
                                <?php echo ucwords($service_description) ?>
                            </div>

                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="w-100 h100">
                            <img src="<?php echo USER_IMAGES ?>doctor-1.jpg" alt="" class="img-fluid w-100 h100 cover">
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section_spacing_top section_spacing_bottom">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section_heading mb-md-4">
                            <p class="text-center">Our Process</p>
                            <h3 class="text-center">How to do the Service?</h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 mb-lg-0 mb-md-0 mb-4">
                        <div class="w-100 relative service_box">
                            <figure class="m-0">
                                <img src="<?php echo USER_IMAGES ?>service-steps/1.png" alt="" class="img-fluid w-100">
                            </figure>
                            <div class="service-title">
                                <h3>Medication</h3>
                            </div>
                            <div class="written_text p-3">
                                <p class="m-0">
                                    Assessing your condition, our dermatologist will recommend you a course of
                                    medications
                                </p>
                                <!-- <a href="" class="btn mt-3 px-4 bg-pink text-white rounded-0">
                                    Learn More
                                </a> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 mb-lg-0 mb-md-0 mb-4">
                        <div class="w-100 relative service_box">
                            <figure class="m-0">
                                <img src="<?php echo USER_IMAGES ?>service-steps/2.png" alt="" class="img-fluid w-100">
                            </figure>
                            <div class="service-title">
                                <h3>Chemical Peels</h3>
                            </div>
                            <div class="written_text p-3">
                                <p class="m-0">
                                    We have specialised chemical peels for treating acne conditions. They are used as
                                    per dermatologist’s recommendation.
                                </p>
                                <!-- <a href="" class="btn mt-3 px-4 bg-pink text-white rounded-0">
                                    Learn More
                                </a> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 mb-lg-0 mb-md-0 mb-4">
                        <div class="w-100 relative service_box">
                            <figure class="m-0">
                                <img src="<?php echo USER_IMAGES ?>service-steps/3.png" alt="" class="img-fluid w-100">
                            </figure>
                            <div class="service-title">
                                <h3>Lasers</h3>
                            </div>
                            <div class="written_text p-3">
                                <p class="m-0">
                                    SkinQure being the pioneer in Lasers technology, we have machines available that
                                    help you get rid of Acne problems.
                                </p>
                                <!-- <a href="" class="btn mt-3 px-4 bg-pink text-white rounded-0">
                                    Learn More
                                </a> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section_spacing_top">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-lg-0 p-md-0">
                        <div class="w-100 second_img service_first relative">
                            <img src="<?php echo USER_IMAGES ?>img-04.jpg" alt="" class="img-fluid ">
                            <div class="top_img">
                                <img src="<?php echo USER_IMAGES ?>ser-5.jpg" alt="" class="img-fluid w-100">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-lg-0 p-md-0">
                        <div class="w-100 secondaboutpage ">
                            <div class="section_heading">
                                <p class="text-start">Committed to patient satisfaction</p>
                                <h3 class="text-start mb-md-5">Why Chose SkinQure for Acne Treatments:</h3>
                            </div>
                            <div class="d-flex w-100 align-items-center mb-5">
                                <div class="icon me-2">
                                    <img src="<?php echo USER_IMAGES ?>calendar.png" alt="" class="img-fluid">
                                </div>
                                <div class="text text-start">
                                    <h5>
                                        More than 13 Years of Experience
                                    </h5>
                                    <!-- <p class="m-0">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit ut elit tellus,
                                        ullamcorper.
                                    </p> -->
                                </div>
                            </div>
                            <div class="d-flex w-100 align-items-center mb-5">
                                <div class="icon me-2">
                                    <img src="<?php echo USER_IMAGES ?>consultation.png" alt="" class="img-fluid">
                                </div>
                                <div class="text text-start">
                                    <h5>
                                        World-Class technology
                                    </h5>
                                    <!-- <p class="m-0">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit ut elit tellus,
                                        ullamcorper.
                                    </p> -->
                                </div>
                            </div>
                            <div class="d-flex w-100 align-items-center mb-4">
                                <div class="icon me-2">
                                    <img src="<?php echo USER_IMAGES ?>customer-satisfaction.png" alt=""
                                        class="img-fluid">
                                </div>
                                <div class="text text-start">
                                    <h5>
                                        Treatments & Procudures done by Expert Dermatologist</h5>
                                    <!-- <p class="m-0 text-start">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit ut elit tellus,
                                        ullamcorper.
                                    </p> -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<section class="greenbg  section_spacing_top sectionpadding">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="row">

                    <div class="col-md-4 position-relative">
                        <div class="section_heading">
                            <p class="text-start">See our Results</p>
                            <h3 class="text-start">Ready to Transform Yourself?</h3>
                        </div>

                        <p class="separate_quote">
                            <img src="<?php echo USER_IMAGES ?>left-quote.png">
                            One of our patient, Ms. Harshita sharma finally got rid of acne and she is very happy with
                            her smooth skin and the way she looks. She has shared her overall experience in a short
                            video, click to hear her SkinQure experience.
                        </p>
                        <a href="https://www.youtube.com/watch?v=VUA4SVORS50" target="_blank" class="btn btn_quote"><i
                                class="fa fa-play-circle"></i> Watch Full Video</a>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="bimg">
                            <a class="lightbox" href="#before">
                                <img src="<?php echo USER_IMAGES ?>before-after/1.jpg" class="w-100">
                                <h4>Before</h4>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="bimg">
                            <a class="lightbox" href="#after">
                                <img src="<?php echo USER_IMAGES ?>before-after/2.jpg" class="w-100">
                                <h4>After</h4>
                            </a>
                        </div>
                    </div>

                    <div class="lightbox-target" id="before">
                        <img src="<?php echo USER_IMAGES ?>before-after/1.jpg" />
                        <a class="lightbox-close" href="#"></a>
                    </div>
                    <div class="lightbox-target" id="after">
                        <img src="<?php echo USER_IMAGES ?>before-after/2.jpg" />
                        <a class="lightbox-close" href="#"></a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<section class="sectionpadding youtube_testimonial">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="w-100 video-box relative">
                    <div class="section_heading">
                        <p class="text-center text-white">Watch Video</p>
                        <h3 class="text-center mb-md-4 text-white">Dr Jangid explains about Acne, skincare and treatment
                            options</h3>
                    </div>
                    <a class="btn youtube youtubbtn" target="_blank"
                        data-youtube="https://youtube.com/embed/76oQSWR6b-M"
                        href="https://www.youtube.com/watch?v=76oQSWR6b-M">
                        <i class="md hydrated fa fa-play"> </i>
                    </a>

                </div>
            </div>
        </div>
    </div>
</section>
<div class="video-modal d-flex justify-content-center align-items-center d-none v-none" id="video-modal">
    <div class="video-body relative p-md-4 p-1">
        <div class="close-video">X</div>
        <div class="youtub-link w-100 h100">
            <iframe src="https://youtube.com/embed/-_xPGQnljT4" width="100%" height="100%"></iframe>
        </div>
    </div>
</div>
<section class="">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 h-100 formfield relative">
                            <img src="<?php echo USER_IMAGES ?>08.jpg" alt="" class="img-fluid w-100 h100 cover">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 p-0 p-lg-0 p-md-0">
                        <div class="w-100 bg-primary formfield text-white form-box ">
                            <h4>Reveal The NU in You:</h4>
                            <p class="mb-md-4">Book your appointment today with our expert dermatologist for a perfect
                                diagnosis & medical advice at SkinQure Clinic. Consultation is available both online and
                                offline.</p>
                            <form action="" class="w-100">
                                <div class="row ">
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="First Name"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="Last Name"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="Contact Number"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="w-100 form-group">
                                            <input type="text" name="" id="name" placeholder="Email Address"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <div class="w-100 form-group">
                                            <textarea name="" id="" placeholder="Your Message"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="w-100 d-flex justify-content-center">
                                            <input type="submit" value="Book Consultation" class="btn text-white">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<section class="section_spacing_top ">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-md-6">
                        <div class="w-100 h100 p-md-3">
                            <div class="section_heading">
                                <p class="text-start">Our commitment</p>
                                <h3 class="text-start mb-md-4">Our Promise to our Clients in transforming their skin
                                </h3>
                            </div>
                            <p class="text-start servicep">SkinQure clinic is the torch bearer of the latest technology
                                and state of the art equipment for all your requirements for skin care and hair
                                treatments. Our team of experienced and qualified doctors are able to provide the best
                                in class services to our patients. Our list of satisfied customers is the testimony to
                                the care and attention to detail that every member of our staff provide to our patients
                                in our quest to make them more beautiful.</p>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="w-100 h100">
                            <img src="<?php echo USER_IMAGES ?>our-commitment.jpg" alt=""
                                class="img-fluid w-100 h100 cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- <section class="section_spacing_top section_spacing_bottom">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="w-100 bg-white shadow subscription_box">
                            <div class="sub_tag">
                                <p class="m-0">
                                    $ 0
                                </p>
                                <p class="m-0">
                                    Month
                                </p>
                            </div>
                            <div class="sub_tag_shadow"></div>
                            <div class="sub-header w-100">
                                <img src="<?php echo USER_IMAGES ?>makeup.png" alt="" class="img-fluid">
                            </div>
                            <div class="subscription_body">
                                <h5>
                                    FREE PLAN
                                </h5>
                                <h2>
                                    Basic
                                </h2>
                                <ul>
                                    <li>
                                        3 user report
                                    </li>
                                    <li>
                                        10 downloads per day
                                    </li>
                                    <li>
                                        Daily content Update
                                    </li>
                                    <li>
                                        Full support
                                    </li>
                                </ul>
                            </div>
                            <div class="sub_footer  ">
                                <a href="" class="btn  w-100">
                                    Choose the Plan
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="w-100 bg-white shadow subscription_box">
                            <div class="sub_tag">
                                <p class="m-0">
                                    $ 0
                                </p>
                                <p class="m-0">
                                    Month
                                </p>
                            </div>
                            <div class="sub_tag_shadow"></div>
                            <div class="sub-header w-100">
                                <img src="<?php echo USER_IMAGES ?>makeup.png" alt="" class="img-fluid">
                            </div>
                            <div class="subscription_body">
                                <h5>
                                    FREE PLAN
                                </h5>
                                <h2>
                                    Basic
                                </h2>
                                <ul>
                                    <li>
                                        3 user report
                                    </li>
                                    <li>
                                        10 downloads per day
                                    </li>
                                    <li>
                                        Daily content Update
                                    </li>
                                    <li>
                                        Full support
                                    </li>
                                    <li>
                                        Daily content Update
                                    </li>
                                    <li>
                                        Full support
                                    </li>
                                </ul>
                            </div>
                            <div class="sub_footer ">
                                <a href="" class="btn  w-100">
                                    Choose the Plan
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="w-100 bg-white shadow subscription_box">
                            <div class="sub_tag">
                                <p class="m-0">
                                    $ 0
                                </p>
                                <p class="m-0">
                                    Month
                                </p>
                            </div>
                            <div class="sub_tag_shadow"></div>
                            <div class="sub-header w-100">
                                <img src="<?php echo USER_IMAGES ?>makeup.png" alt="" class="img-fluid">
                            </div>
                            <div class="subscription_body">
                                <h5>
                                    FREE PLAN
                                </h5>
                                <h2>
                                    Basic
                                </h2>
                                <ul>
                                    <li>
                                        3 user report
                                    </li>
                                    <li>
                                        10 downloads per day
                                    </li>
                                    <li>
                                        Daily content Update
                                    </li>
                                    <li>
                                        Full support
                                    </li>
                                    <li>
                                        3 user report
                                    </li>
                                    <li>
                                        10 downloads per day
                                    </li>
                                    <li>
                                        Daily content Update
                                    </li>
                                    <li>
                                        Full support
                                    </li>
                                </ul>
                            </div>
                            <div class="sub_footer  ">
                                <a href="" class="btn  w-100">
                                    Choose the Plan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section> -->
<?php

require_once 'footer.php';

?>