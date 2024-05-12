<?php

require_once 'header.php';

?>
<!--Main Slider Start-->
<section class="main-slider clearfix">
    <div class="swiper-container thm-swiper__slider" data-swiper-options='{"slidesPerView": 1, "loop": true,
                "effect": "fade",
                "pagination": {
                "el": "#main-slider-pagination",
                "type": "bullets",
                "clickable": true
                },
                "navigation": {
                "nextEl": "#main-slider__swiper-button-next",
                "prevEl": "#main-slider__swiper-button-prev"
                },
                "autoplay": {
                "delay": 5000
                }}'>
        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <div class="image-layer"
                    style="background-image: url(<?php echo USER_IMAGES ?>img-1/IMG-0253-scaled.jpg);">
                </div>
                <!-- /.image-layer -->

                <div class="main-slider-shape-1"
                    style="background-image: url(<?php echo USER_IMAGES ?>img-1/IMG-0253-scaled.jpg);">
                </div>
                <div class="main-slider-shape-2 float-bob-x">
                    <img src="<?php echo USER_IMAGES ?>shapes/main-slider-shape-2.png" alt="">
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col-xl-6 col-lg-8">
                            <div class="main-slider__content">
                                <p class="main-slider__sub-title">Mission</p>
                                <h2 class="main-slider__title">Let’s Reach and support Who need it Most.</h2>
                                <div class="main-slider__btn-box">
                                    <a href="about-us.php" class="thm-btn main-slider__btn"> Discover more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="image-layer"
                    style="background-image: url(<?php echo USER_IMAGES ?>img-1/IMG-3240-scaled.jpg);">
                </div>
                <!-- /.image-layer -->

                <div class="main-slider-shape-1"
                    style="background-image: url(<?php echo USER_IMAGES ?>img-1/IMG-3240-scaled.jpg);"></div>
                <div class="main-slider-shape-2 float-bob-x">
                    <img src="<?php echo USER_IMAGES ?>shapes/main-slider-shape-2.png" alt="">
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col-xl-6 col-lg-8">
                            <div class="main-slider__content">
                                <p class="main-slider__sub-title">Mission</p>
                                <h2 class="main-slider__title">Let’s Reach and support Who need it Most.</h2>
                                <div class="main-slider__btn-box">
                                    <a href="about-us.php" class="thm-btn main-slider__btn"> Discover more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="image-layer"
                    style="background-image: url(<?php echo USER_IMAGES ?>img-1/IMG-0303-scaled.jpg);">
                </div>
                <!-- /.image-layer -->

                <div class="main-slider-shape-1"
                    style="background-image: url(<?php echo USER_IMAGES ?>img-1/IMG-0303-scaled.jpg);"></div>
                <div class="main-slider-shape-2 float-bob-x">
                    <img src="<?php echo USER_IMAGES ?>shapes/main-slider-shape-2.png" alt="">
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col-xl-6 col-lg-8">
                            <div class="main-slider__content">
                                <p class="main-slider__sub-title">Mission</p>
                                <h2 class="main-slider__title">Let’s Reach and support Who need it Most.</h2>
                                <div class="main-slider__btn-box">
                                    <a href="about-us.php" class="thm-btn main-slider__btn"> Discover more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- If we need navigation buttons -->
        <div class="main-slider__nav">
            <div class="swiper-button-prev" id="main-slider__swiper-button-next">
                <i class="icon-left-arrow"></i>
            </div>
            <div class="swiper-button-next" id="main-slider__swiper-button-prev">
                <i class="icon-right-arrow"></i>
            </div>
        </div>

    </div>
</section>
<!--Main Slider End-->

<!--About One Start-->
<section class="about-one">
    <div class="about-one__shape-box-1">
        <div class="about-one__shape-1"
            style="background-image: url(<?php echo USER_IMAGES ?>shapes/about-one-shape-1.png);">
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="about-one__left">
                    <div class="about-one__img-box wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                        <div class="about-one__img">
                            <img src="<?php echo USER_IMAGES ?>img-1/child.jpg" alt="">
                        </div>
                        <div class="about-one__img-border"></div>
                        <div class="about-one__curved-circle-box">
                            <div class="curved-circle">
                                <span class="curved-circle--item">
                                    5 Years Of Undefeated Success
                                </span>
                            </div><!-- /.curved-circle -->
                            <div class="about-one__curved-circle-icon">
                                <img src="<?php echo USER_IMAGES ?>logo-icon.png" alt="">
                            </div>
                        </div>
                        <div class="about-one__shape-2 zoom-fade">
                            <img src="<?php echo USER_IMAGES ?>shapes/about-one-shape-2.png" alt="">
                        </div>
                        <div class="about-one__shape-3 float-bob-y">
                            <img src="<?php echo USER_IMAGES ?>shapes/about-one-shape-3.png" alt="">
                        </div>
                        <div class="about-one__shape-4 zoominout">
                            <img src="<?php echo USER_IMAGES ?>shapes/about-one-shape-4.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="about-one__right">
                    <div class="section-title text-left">
                        <span class="section-title__tagline">Welcome to Samaarambh</span>
                        <h2 class="section-title__title">5 Years Of Undefeated Success</h2>
                    </div>
                    <p class="about-one__text">SAMAARAMBH FOUNDATION is a NON-GOVERNMENT ORGANISATION (NGO) founded and
                        run by
                        a group of like-minded individuals from diverse fields, coming together to reach out and help
                        the deprived
                        strata of our society residing in remote areas of our country. The foundation is a
                        not-for-profit organisation
                        (NFPO) that works on Private-Public-Participation (PPP) Model.</p>
                    <div class="about-one__fund">
                        <p class="about-one__fund-text"><span>45+ </span> Projects Successfully Finished
                            <br><span>Area </span>UK, UT of Ladakh, J&K and HP <br>
                            <span>7 </span> States,organisation’s intervention has expanded in 7
                            states.<br><span>Majority </span>majorly out of which 29 were focused on Uttarakhand
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--About One End-->

<!--Causes One Start-->
<section class="causes-one">
    <div class="container">
        <div class="section-title text-center">
            <span class="section-title__tagline">Help & donate us now</span>
            <h2 class="section-title__title">Find the popular cause <br> and donate them</h2>
        </div>
        <div class="row">
            <!--Causes One Single Start-->
            <div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="100ms">
                <div class="causes-one__single">
                    <div class="causes-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/child-ed.png" alt="">
                        <div class="causes-one__cat">
                            <p>Education</p>
                        </div>
                    </div>
                    <div class="causes-one__content">
                        <h3 class="causes-one__title"><a href="contact-us.php">Let’s education for
                                children get good life</a>
                        </h3>
                        <p class="causes-one__text">Join Hands with Us: Together, Let's Create a Brighter Future for
                            Those in Need.</p>
                    </div>
                </div>
            </div>
            <!--Causes One Single End-->
            <!--Causes One Single Start-->
            <div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="200ms">
                <div class="causes-one__single">
                    <div class="causes-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/child-ed2.png" alt="">
                        <div class="causes-one__cat">
                            <p>Medical</p>
                        </div>
                    </div>
                    <div class="causes-one__content">
                        <h3 class="causes-one__title"><a href="contact-us.php">It is a long established
                                fact that a reader</a>
                        </h3>
                        <p class="causes-one__text">Your Donations Are the Building Blocks of a Better, More Inclusive
                            Society.</p>
                    </div>
                </div>
            </div>
            <!--Causes One Single End-->
            <!--Causes One Single Start-->
            <div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="300ms">
                <div class="causes-one__single">
                    <div class="causes-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/child-ed3.png" alt="">
                        <div class="causes-one__cat">
                            <p>Food</p>
                        </div>
                    </div>
                    <div class="causes-one__content">
                        <h3 class="causes-one__title"><a href="contact-us.php">There are many variations
                                of passages</a>
                        </h3>
                        <p class="causes-one__text">Your Investment in Our Mission Is an Investment in a Better World
                            for All.</p>
                    </div>
                </div>
            </div>
            <!--Causes One Single End-->
        </div>
    </div>
</section>
<!--Causes One End-->

<!--Become Volunteer One Start-->
<section class="become-volunteer-one">
    <div class="become-volunteer-one__bg-box">
        <div class="become-volunteer-one__bg jarallax" data-jarallax data-speed="0.2" data-imgPosition="50% 0%"
            style="background-image: url(<?php echo USER_IMAGES ?>img-1/46-1-scaled.jpg);"></div>
    </div>
    <div class="become-volunteer-one__shape-1"
        style="background-image: url(<?php echo USER_IMAGES ?>shapes/become-volunteer-shape-1.png);"></div>
    <div class="container">
        <div class="become-volunteer-one__inner">
            <p class="become-volunteer-one__sub-title">Become a Volunteers</p>
            <h3 class="become-volunteer-one__title">Join your hand with us for a <br> better life and future
            </h3>
            <div class="become-volunteer-one__btn-box">
                <a href="contact-us.php" class="thm-btn become-volunteer-one__btn">Discover More</a>
            </div>
        </div>
    </div>
</section>
<!--Become Volunteer One End-->

<!--Feature One Start-->
<section class="feature-one">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6  wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                <div class="feature-one__single">
                    <div class="feature-one__single-bg"
                        style="background-image: url(<?php echo USER_IMAGES ?>shapes/feature-one-shape-1.png);"></div>
                    <div class="feature-one__top">
                        <div class="feature-one__top-inner">
                            <div class="feature-one__top-icon">
                                <span class="icon-help"></span>
                            </div>
                            <div class="feature-one__top-title-box">
                                <h3 class="feature-one__top-title"><a href="contact-us.php">Vision
                                    </a></h3>
                            </div>
                        </div>
                    </div>
                    <p class="feature-one__text">लोकः समस्तः सुखिनो भवन्तु</p>
                    <ul class="list-unstyled feature-one__point">
                        <li>
                            <!-- <div class="icon">
                                        <span class="fa fa-check"></span>
                                    </div> -->
                            <div class="text">
                                <p>Providing quality education to every child, <br>creating livelihood opportunities to
                                    break <br> the vicious cycle of poverty and social isolation <br> to restore hope
                                    for a better and sustainable future.</p>
                            </div>
                        </li>

                    </ul>
                    <a href="contact-us.php" class="thm-btn feature-one__btn">Donate now</a>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6  wow slideInRight" data-wow-delay="100ms" data-wow-duration="2500ms">
                <div class="feature-one__single feature-one__single--two">
                    <div class="feature-one__single-bg"
                        style="background-image: url(<?php echo USER_IMAGES ?>shapes/feature-one-shape-1.png);"></div>
                    <div class="feature-one__top">
                        <div class="feature-one__top-inner">
                            <div class="feature-one__top-icon feature-one__top-icon--two">
                                <span class="icon-gift-box"></span>
                            </div>
                            <div class="feature-one__top-title-box">
                                <h3 class="feature-one__top-title"><a href="contact-us.php">Mission
                                        <br>
                                    </a></h3>
                            </div>
                        </div>
                    </div>
                    <p class="feature-one__text">Let’s Reach and support Who need it Most.
                    </p>
                    <ul class="list-unstyled feature-one__point">
                        <li>
                            <div class="text">
                                <p>Our mission is to Provide Educational & Financial Aid,<br>Technical Assistance &
                                    Equipment, <br>Conducting Awareness session for Organize Health check-up Camps
                                </p>
                            </div>
                        </li>
                    </ul>
                    <a href="contact-us.php" class="thm-btn feature-one__btn">Donate now</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Feature One End-->

<!--Brand One Start-->
<!-- <section class="brand-one">
            <div class="container">
                <div class="thm-swiper__slider swiper-container" data-swiper-options='{"spaceBetween": 100, "slidesPerView": 5, "autoplay": { "delay": 5000 }, "breakpoints": {
                            "0": {
                                "spaceBetween": 30,
                                "slidesPerView": 2
                            },
                            "375": {
                                "spaceBetween": 30,
                                "slidesPerView": 2
                            },
                            "575": {
                                "spaceBetween": 30,
                                "slidesPerView": 3
                            },
                            "767": {
                                "spaceBetween": 50,
                                "slidesPerView": 4
                            },
                            "991": {
                                "spaceBetween": 50,
                                "slidesPerView": 5
                            },
                            "1199": {
                                "spaceBetween": 100,
                                "slidesPerView": 6
                            }
                        }}'> -->
<!-- <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-1.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-2.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-3.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-4.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-5.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-6.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-1.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-2.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-3.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-4.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-5.png" alt="">
                        </div>/.swiper-slide -->
<!-- <div class="swiper-slide">
                            <img src="<?php echo USER_IMAGES ?>brand/brand-1-6.png" alt="">
                        </div>/.swiper-slide -->
<!-- </div>
                </div>
            </div>
        </section> -->
<!--Brand One End-->

<!--Testimonial One Start-->
<section class="testimonial-one">
    <div class="testimonial-one-bg jarallax" data-jarallax data-speed="0.2" data-imgPosition="50% 0%"
        style="background-image: url(<?php echo USER_IMAGES ?>img-1/IMG-3240-scaled.jpg);"></div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="testimonial-one__left">
                    <div class="section-title text-left">
                        <span class="section-title__tagline">Our Testimonials</span>
                        <h2 class="section-title__title">What they are talking about Samaarambh</h2>
                    </div>
                    <!-- <a href="#" class="thm-btn testimonial-one__btn">all testimonials</a> -->
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="testimonial-one__right">
                    <div class="testimonial-one__img-1 zoom-fade">
                        <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-img-1.jpg" alt="">
                    </div>
                    <div class="testimonial-one__img-1 testimonial-one__img-2 zoom-fade">
                        <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-img-2.jpg" alt="">
                    </div>
                    <div class="testimonial-one__img-1 testimonial-one__img-3 zoom-fade">
                        <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-img-3.jpg" alt="">
                    </div>
                    <div class="testimonial-one__img-1 testimonial-one__img-4 zoom-fade">
                        <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-img-4.jpg" alt="">
                    </div>
                    <div class="testimonial-one__carousel owl-carousel owl-theme thm-owl__carousel" data-owl-options='{
                                "loop": true,
                                "autoplay": true,
                                "margin": 50,
                                "nav": true,
                                "dots": false,
                                "smartSpeed": 500,
                                "autoplayTimeout": 10000,
                                "navText": ["<span class=\"icon-left-arrow\"></span>","<span class=\"icon-right-arrow\"></span>"],
                                "responsive": {
                                    "0": {
                                        "items": 1
                                    },
                                    "768": {
                                        "items": 1
                                    },
                                    "992": {
                                        "items": 1
                                    },
                                    "1200": {
                                        "items": 1
                                    }
                                }
                            }'>
                        <div class="item">
                            <!--Testimonial One Single Start-->
                            <div class="testimonial-one__single">
                                <div class="testimonial-one__shape-1"
                                    style="background-image: url(<?php echo USER_IMAGES ?>shapes/testimonial-one-shape-1.png);">
                                </div>
                                <div class="testimonial-one__client-img">
                                    <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-1-2.jpg" alt="">
                                </div>
                                <div class="testimonial-one__client-info">
                                    <h3 class="testimonial-one__client-name">Amandeep Rana</h3>
                                    <p class="testimonial-one__client-sub-title">Project Head, HCL Technologies</p>
                                </div>
                                <div class="testimonial-one__quote">
                                    <span class="icon-double-quotes"></span>
                                </div>
                                <p class="testimonial-one__text-2">One Thing from Many things that makes Sammarambh
                                    different from
                                    other NGO’s is their wonderful connect with Children-Women – Communities and
                                    villagers.</p>
                            </div>
                            <!--Testimonial One Single End-->
                        </div>
                        <div class="item">
                            <!--Testimonial One Single Start-->
                            <div class="testimonial-one__single">
                                <div class="testimonial-one__shape-1"
                                    style="background-image: url(<?php echo USER_IMAGES ?>shapes/testimonial-one-shape-1.png);">
                                </div>
                                <div class="testimonial-one__client-img">
                                    <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-1-2.jpg" alt="">
                                </div>
                                <div class="testimonial-one__client-info">
                                    <h3 class="testimonial-one__client-name">Arindam Mukherjee</h3>
                                    <p class="testimonial-one__client-sub-title">Sr. Manager, TV Today Network</p>
                                </div>
                                <div class="testimonial-one__quote">
                                    <span class="icon-double-quotes"></span>
                                </div>
                                <p class="testimonial-one__text-2">I want to thank SAMAARMBH for giving me the
                                    opportunity to help
                                    people from the vast parts of our country. I have been donating to SAMAARAMBH for
                                    the past
                                    few years whenever I can I will Contribute and Participate.</p>
                            </div>
                            <!--Testimonial One Single End-->
                        </div>
                        <div class="item">
                            <!--Testimonial One Single Start-->
                            <div class="testimonial-one__single">
                                <div class="testimonial-one__shape-1"
                                    style="background-image: url(<?php echo USER_IMAGES ?>shapes/testimonial-one-shape-1.png);">
                                </div>
                                <div class="testimonial-one__client-img">
                                    <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-1-2.jpg" alt="">
                                </div>
                                <div class="testimonial-one__client-info">
                                    <h3 class="testimonial-one__client-name">Ashutosh Maindola</h3>
                                    <p class="testimonial-one__client-sub-title">Branch Manager, STATE BANK OF INDIA
                                        Dehradoon</p>
                                </div>
                                <div class="testimonial-one__quote">
                                    <span class="icon-double-quotes"></span>
                                </div>
                                <p class="testimonial-one__text-2">I’m fortunate to have been part of Samaarambh
                                    Foundation and
                                    really happy to be part of social reforms and Awareness sessions organized for needy
                                    ones.</p>
                            </div>
                            <!--Testimonial One Single End-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Testimonial One End-->

<!--Gallery One Start-->
<section class="gallery-one">
    <div class="gallery-one__top">
        <h3 class="gallery-one__top-title">Our photo gallery</h3>
    </div>
    <div class="gallery-one__bottom">
        <div class="gallery-one__container">
            <ul class="list-unstyled gallery-one__list">
                <li class="gallery-one__single wow fadeInUp" data-wow-delay="100ms">
                    <div class="gallery-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/photog-1.jpg" alt="">
                        <a href="<?php echo USER_IMAGES ?>img-1/photog-1.jpg" class="img-popup"></a>
                        <div class="gallery-one__content">
                            <!-- /.img-popup -->
                            <p class="gallery-one__sub-title">Charity</p>
                            <h5 class="gallery-one__title"><a href="#">Education</a></h5>
                        </div>
                    </div>
                </li>
                <li class="gallery-one__single wow fadeInUp" data-wow-delay="200ms">
                    <div class="gallery-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/photog-2.jpg" alt="">
                        <a href="<?php echo USER_IMAGES ?>img-1/photog-2.jpg" class="img-popup"></a>
                        <div class="gallery-one__content">
                            <!-- /.img-popup -->
                            <p class="gallery-one__sub-title">Charity</p>
                            <h5 class="gallery-one__title"><a href="#">Education</a></h5>
                        </div>
                    </div>
                </li>
                <li class="gallery-one__single wow fadeInUp" data-wow-delay="300ms">
                    <div class="gallery-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/photog-3.jpg" alt="">
                        <a href="<?php echo USER_IMAGES ?>img-1/photog-3.jpg" class="img-popup"></a>
                        <div class="gallery-one__content">
                            <!-- /.img-popup -->
                            <p class="gallery-one__sub-title">Charity</p>
                            <h5 class="gallery-one__title"><a href="#">Education</a></h5>
                        </div>
                    </div>
                </li>
                <li class="gallery-one__single wow fadeInUp" data-wow-delay="400ms">
                    <div class="gallery-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/photog-4.jpg" alt="">
                        <a href="<?php echo USER_IMAGES ?>img-1/photog-4.jpg" class="img-popup"></a>
                        <div class="gallery-one__content">
                            <!-- /.img-popup -->
                            <p class="gallery-one__sub-title">Charity</p>
                            <h5 class="gallery-one__title"><a href="#">Education</a></h5>
                        </div>
                    </div>
                </li>
                <li class="gallery-one__single wow fadeInUp" data-wow-delay="500ms">
                    <div class="gallery-one__img">
                        <img src="<?php echo USER_IMAGES ?>img-1/photog-5.jpg" alt="">
                        <a href="<?php echo USER_IMAGES ?>img-1/photog-5.jpg" class="img-popup"></a>
                        <div class="gallery-one__content">
                            <!-- /.img-popup -->
                            <p class="gallery-one__sub-title">Charity</p>
                            <h5 class="gallery-one__title"><a href="#">Education</a></h5>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>
<!--Gallery One End-->

<!--FAQ One Start-->
<section class="faq-one">
    <!-- <div class="faq-one-shape-1" style="background-image: url(<?php echo USER_IMAGES ?>shapes/faq-one-shape-1.png);"></div>
    <div class="faq-one-bg" style="background-image: url(<?php echo USER_IMAGES ?>backgrounds/faq-one-bg.png);"></div> -->
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="faq-one__left">
                    <div class="section-title text-left">
                        <span class="section-title__tagline">Learn More From</span>
                        <h2 class="section-title__title">People are frequently asking some questions from us
                        </h2>
                    </div>
                    <p class="faq-one__text-1">When You Give, You Not Only Offer Resources but Also Open
                        Doors to Opportunity, Education,
                        and Empowerment for Those Less Fortunate</p>
                    <a href="contact-us.php" class="thm-btn faq-one__btn">Learn how to get help</a>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="faq-one__right">
                    <div class="accrodion-grp" data-grp-name="faq-one-accrodion">
                        <div class="accrodion active">
                            <div class="accrodion-title">
                                <h4>1. Module of Samaarambh!</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p>Step 1- Study REQUEST LETTERS Received from schools/ Administration and
                                        Villagers<br>

                                        Step 2- Prioritize requests based on URGENCY ,IMPACT & Number of
                                        Beneficiaries<br>

                                        Step 3- Site Visit and data collection of beneficiary<br>

                                        Step 4- Cost analysis of aid and project management<br>

                                        Step 5- Sharing project details with partner organisations<br>

                                        Step 6- Getting required approvals and Grants from participating partners.<br>

                                        Step 7- Organising and Managing entire Event.<br>

                                        Step 8- Sharing completion report along with the Impact derived.</p>
                                </div><!-- /.inner -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--FAQ One End-->

<?php

require_once 'footer.php';

?>