<?php

require_once 'header.php';

?>

<section>
    <div class="welcomeNote">
        <h1>Hello, we’re <img src="<?php echo USER_IMAGES ?>logo.png" class="welcomeLogo" alt="<?php echo SITE_NAME ?>" />
        </h1>
        <p><strong>We <span class="heart"></span> developing websites, mobile apps and applications for small, medium
                and large
                enterprises.</strong></p>
        <p>Keen to know more? <a href="mailto:avinash@isw.one" class="getInTouchBtn">Get in Touch</a></p>
    </div>
</section>
<section>
    <div class="testimonialBox">
        <a href="#">
            <div class="image">
                <img src="<?php echo USER_IMAGES ?>la-skinnovita-testimonial.jpg" alt="">
            </div>
            <div class="text">
                <h2 class="blumox">La Skinnovita</h2>
                <p>Welcome to La Skinnovita, one of its own kind, for the first time in Delhi & NCR, a dedicated
                    dermatology, lasers & hair center</p>
            </div>
        </a>
    </div>
    <div class="testimonialBox">
        <div class="image leftImageBox">
            <img src="<?php echo USER_IMAGES ?>punjab-kesari-testimonial.jpg" alt="">
        </div>
        <div class="text leftBox">
            <h2 class="blumox">Punjab Kesari</h2>
            <p>Welcome to La Skinnovita, one of its own kind, for the first time in Delhi & NCR, a dedicated
                dermatology, lasers & hair center</p>
        </div>
    </div>
    <div class="testimonialBox">
        <a href="#">
            <div class="image">
                <img src="<?php echo USER_IMAGES ?>and-blooms-testimonial.jpg" alt="">
            </div>
            <div class="text">
                <h2 class="blumox">And Blooms</h2>
                <p>Welcome to La Skinnovita, one of its own kind, for the first time in Delhi & NCR, a dedicated
                    dermatology, lasers & hair center</p>
            </div>
        </a>
    </div>
</section>

<section>
    <div class="linkBox">
        <p>See our <a href="/portfolio/" class="getInTouchBtn">more work</a></p>
    </div>
</section>
<?php

require_once 'footer.php';

?>