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
            <input type="text" name="" placeholder="Name">
            <input type="email" name="" placeholder="Email" class="lft">
            <input type="tel" name="" placeholder="Mobile" class="lft">
            <textarea placeholder="Enter Msg"></textarea>
            <input type="hidden" name="type" value="enquiry" >
            <input type="hidden" name="action" value="new" >
            <input type="submit" value="Send Your Message">
        </div>
        <div class="box-cont-2">
            <h2>Contact Us</h2>
            <ul>
                <li>
                    <img src="<?php echo USER_IMAGES ?>ping.png"> 36, Gali Number 2, Block 69B, Dori Walan, Karol Bagh, New Delhi, Delhi 110005
                </li>
                <li>
                    <img src="<?php echo USER_IMAGES ?>cal.png"> 011 2367 0506
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