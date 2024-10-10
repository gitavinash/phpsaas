<?php

require_once 'header.php';

?>

<section>
    <div class="introBox">
        <h1>Our Products</h1>
    </div>
</section>

<section>
    <div class="projectBox">
        <a href="https://oxteal.com" target="_blank">
            <div class="image">
                <img src="<?php echo USER_IMAGES ?>websites/oxteal-website.png" alt="">
            </div>
            <div class="text oxtealBgColor">
                <h2 class="blumox">Oxteal</h2>
                <p>Oxteal is an online platform offering web hosting and website AMC services.</p>
                <p><a href="https://oxteal.com" class="tryNowBtn" target="_blank">Try now</a></p>
            </div>
        </a>
    </div>
    <div class="projectBox">
        <a href="https://aleya.in" target="_blank">
            <div class="image leftImageBox">
                <img src="<?php echo USER_IMAGES ?>websites/aleya-dashboard.png" alt="">
            </div>
            <div class="text leftBox aleyaBgColor">
                <h2 class="blumox">Aleya</h2>
                <p>Aleya is a CRM and ERP software built for travel agencies.</p>
                <p><a href="https://aleya.in" class="tryNowBtn" target="_blank">Try now</a></p>
            </div>
        </a>
    </div>
</section>

<?php

require_once 'footer.php';

?>