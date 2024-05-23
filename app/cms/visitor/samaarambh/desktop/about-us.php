<?php

require_once 'header.php';

?>

<!--Page Header Start-->
<section class="page-header">
    <div class="page-header-bg" style="background-image: url(<?php echo $page_image ?>)">
    </div>
    <div class="container">
        <div class="page-header__inner">
            <ul class="thm-breadcrumb list-unstyled">
                <li><a href="/">Home</a></li>
                <li><span>/</span></li>
                <li class="active"><?php echo $page_title ?></li>
            </ul>
            <h2><?php echo $page_title ?></h2>
        </div>
    </div>
</section>
<!--Page Header End-->

<!--About Four Start-->
<section class="about-four">
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="about-four__left">
                    <div class="about-four__img-box">
                        <div class="about-four__img">
                            <img src="<?php echo USER_IMAGES ?>img-1/about-2.jpg" alt="">
                        </div>
                        <div class="about-four__img-two">
                            <img src="<?php echo USER_IMAGES ?>img-1/about-1.jpg" alt="">
                        </div>
                        <div class="about-four__border"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="about-four__right">
                    <div class="section-title text-left">
                        <span class="section-title__tagline">About Samaarambh</span>
                        <h2 class="section-title__title">Welcome to the journey of Samaarambh foundation</h2>
                    </div>
                    <p class="about-four__text">Samaarambh Foundation was born out of a collective desire to make a
                        difference in the lives of those living on the frontiers of our nation, where the challenges are
                        as formidable as the landscapes are breathtaking. We believe that every individual, regardless
                        of their geographical location, deserves access to quality education, healthcare, and employment
                        opportunities. Our commitment is not just to address immediate needs but to create sustainable,
                        long-term solutions that will uplift communities and foster growth. </p>
                    <!-- <div class="about-four__btn-box">
                        <a href="about.html" class="thm-btn about-four__btn">Discover More</a>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</section>
<!--About Four End-->

<!--Testimonial Two Start-->
<section class="testimonial-two">
    <div class="container">
        <div class="section-title text-center">
            <span class="section-title__tagline">Our testimonials</span>
            <h2 class="section-title__title">What they’re talking <br> about Samaarambh</h2>
        </div>
        <div class="testimonial-two__carousel owl-carousel owl-theme thm-owl__carousel" data-owl-options='{
                    "loop": true,
                    "autoplay": true,
                    "margin": 30,
                    "nav": false,
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
                            "items": 2
                        },
                        "1200": {
                            "items": 3
                        }
                    }
                }'>
            <div class="item">
                <!--Testimonial Two Single Start-->
                <div class="testimonial-two__single">
                    <div class="testimonial-two__img">
                        <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-2-1.jpg" alt="">
                    </div>
                    <div class="testimonial-two__content">
                        <div class="testimonial-two__shape-1"
                            style="background-image: url(<?php echo USER_IMAGES ?>shapes/testimonial-two-shape-1.png);">
                        </div>
                        <p class="testimonial-two__text">I want to thank SAMAARMBH for giving me the opportunity to help
                            people from the vast parts of our country. I have been donating to Samaarambh for the past
                            few years whenever I can I will Contribute and Participate.</p>
                        <div class="testimonial-two__client-info">
                            <h4 class="testimonial-two__client-name">Arindam Mukherjee</h4>
                            <p class="testimonial-two__client-sub-title">Sr. Manager, TV Today Network</p>
                        </div>
                        <div class="testimonial-two__rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                    </div>
                </div>
                <!--Testimonial Two Single End-->
            </div>
            <div class="item">
                <!--Testimonial Two Single Start-->
                <div class="testimonial-two__single">
                    <div class="testimonial-two__img">
                        <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-2-1.jpg" alt="">
                    </div>
                    <div class="testimonial-two__content">
                        <div class="testimonial-two__shape-1"
                            style="background-image: url(<?php echo USER_IMAGES ?>shapes/testimonial-two-shape-1.png);">
                        </div>
                        <p class="testimonial-two__text">I’m fortunate to have been part of Samaarambh Foundation and
                            really happy to be part of social reforms and Awareness sessions organized for needy ones.
                        </p>
                        <div class="testimonial-two__client-info">
                            <h4 class="testimonial-two__client-name">Ashutosh Maindola</h4>
                            <p class="testimonial-two__client-sub-title">Branch Manager, STATE BANK OF INDIA Dehradoon
                            </p>
                        </div>
                        <div class="testimonial-two__rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                    </div>
                </div>
                <!--Testimonial Two Single End-->
            </div>
            <div class="item">
                <!--Testimonial Two Single Start-->
                <div class="testimonial-two__single">
                    <div class="testimonial-two__img">
                        <img src="<?php echo USER_IMAGES ?>testimonial/testimonial-2-1.jpg" alt="">
                    </div>
                    <div class="testimonial-two__content">
                        <div class="testimonial-two__shape-1"
                            style="background-image: url(<?php echo USER_IMAGES ?>shapes/testimonial-two-shape-1.png);">
                        </div>
                        <p class="testimonial-two__text">One Thing from Many things that makes Sammarambh different from
                            other NGO’s is their wonderful connect with Children-Women – Communities and villagers.</p>
                        <div class="testimonial-two__client-info">
                            <h4 class="testimonial-two__client-name">Amandeep Rana</h4>
                            <p class="testimonial-two__client-sub-title">Project Head, HCL Technologies</p>
                        </div>
                        <div class="testimonial-two__rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                    </div>
                </div>
                <!--Testimonial Two Single End-->
            </div>
        </div>
    </div>
</section>
<!--Testimonial Two End-->

<!--Become Volunteer One Start-->
<section class="become-volunteer-one">
    <div class="become-volunteer-one__bg-box">
        <div class="become-volunteer-one__bg jarallax" data-jarallax data-speed="0.2" data-imgPosition="50% 0%"
            style="background-image: url(<?php echo USER_IMAGES ?>img-1/ab-2.jpg);"></div>
    </div>
    <div class="become-volunteer-one__shape-1"
        style="background-image: url(<?php echo USER_IMAGES ?>shapes/become-volunteer-shape-1.png);"></div>
    <div class="container">
        <div class="become-volunteer-one__inner">
            <p class="become-volunteer-one__sub-title">Become a Volunteers</p>
            <h3 class="become-volunteer-one__title">Join your hand with us for a <br> better life and future
            </h3>
            <div class="become-volunteer-one__btn-box">
                <a href="become-volunteer.html" class="thm-btn become-volunteer-one__btn">Discover More</a>
            </div>
        </div>
    </div>
</section>
<!--Become Volunteer One End-->


<?php

require_once 'footer.php';

?>