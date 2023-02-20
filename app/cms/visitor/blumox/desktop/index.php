<?php

require_once 'header.php';

?>

<section>
    <div class="welcomeNote">
        <h1>Hello, we’re <img src="<?php echo USER_IMAGES ?>logo.png" class="welcomeLogo"
                alt="<?php echo SITE_NAME ?>" />
        </h1>
        <p><strong>We <span class="heart"></span> developing websites, mobile apps and applications for small, medium
                and large
                enterprises.</strong></p>
        <p>Keen to know more? <a href="/contact-us/" class="getInTouchBtn">Get in Touch</a></p>
    </div>
</section>
<section>
    <div class="projectBox">
        <a href="https://laskinnovita.com/download-app.php" target="_blank">
            <div class="image">
                <img src="<?php echo USER_IMAGES ?>la-skinnovita-testimonial.jpg" alt="">
            </div>
            <div class="text lsBgColor">
                <h2 class="blumox">La Skinnovita</h2>
                <p>Developed an innovative clinic management system to automate clinic operations.</p>
            </div>
        </a>
    </div>
    <div class="projectBox">
        <a href="https://punjabkesari.com" target="_blank">
            <div class="image leftImageBox">
                <img src="<?php echo USER_IMAGES ?>punjab-kesari-testimonial.jpg" alt="">
            </div>
            <div class="text leftBox pkBgColor">
                <h2 class="blumox">Punjab Kesari</h2>
                <p>Developed a news portal to digitize a legacy newspaper of India.</p>
            </div>
        </a>
    </div>
    <div class="projectBox">
        <a href="https://andblooms.com" target="_blank">
            <div class="image">
                <img src="<?php echo USER_IMAGES ?>and-blooms-testimonial.jpg" alt="">
            </div>
            <div class="text abBgColor">
                <h2 class="blumox">And Blooms</h2>
                <p>E-commerce solution to give a better experience in finding better gifts for loved ones.</p>
            </div>
        </a>
    </div>
</section>

<section>
    <div class="linkBox">
        <p>See our <a href="/our-work/" class="getInTouchBtn">more work</a></p>
    </div>
</section>

<?php

require_once 'footer.php';

?>