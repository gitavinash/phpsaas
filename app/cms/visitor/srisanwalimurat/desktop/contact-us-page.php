<?php

include_once 'header.php';

?>

<section>
    <div class="contact-us">

        <div class="map">
            <?php echo $page_description ?>
        </div>

        <div class="box-cont-1">
            <h1>Need a Solution</h1>
            <form action="" method="POST">
                <input type="text" name="name" placeholder="Name">
                <input type="email" name="email" placeholder="Email" class="lft">
                <input type="tel" name="mobile" placeholder="Mobile" class="lft">
                <textarea name="message" placeholder="Enter Message"></textarea>
                <input type="hidden" name="type" value="enquiry">
                <input type="hidden" name="action" value="new">
                <input type="hidden" name="subject" value="Contact us">
                <input type="hidden" name="sender" value="c2hyaXNhbndhbGltdXJhdEBnbWFpbC5jb20=">
                <input type="hidden" name="redirect_url"
                    value="/thank-you/?msg=Thank+you+for+your+query%2Ffeedback.+It+is+important+for+us.">
                <input type="submit" value="Send Your Message">
            </form>
        </div>
        <div class="box-cont-2">
            <h2>Contact Us</h2>
            <ul>
                <li>
                    <img src="<?php echo USER_IMAGES ?>ping.png"> 36, Gali Number 2, Block 69B, Dori Walan, Karol Bagh,
                    New Delhi, Delhi 110005
                </li>
                <li>
                    <img src="<?php echo USER_IMAGES ?>cal.png"> <a href="tel:+91-9821005828">+91-9821005828</a>, <a
                        href="tel:+91-9560849164">+91-9560849164</a>,<br /> <a style="margin-left: 38px"
                        href="tel:011-23670506">011-23670506</a>,<a href="tel:011-47200965">011-47200965</a>
                </li>
                <li>
                    <img src="<?php echo USER_IMAGES ?>mail.png"> info@srisanwalimurat.com
                </li>
            </ul>


        </div>
    </div>
</section>

<?php

include_once 'footer.php';

?>