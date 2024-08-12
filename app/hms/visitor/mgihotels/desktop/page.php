<?php

    require_once 'header.php';

?>

<main>
    <section class="contact_wrapper background_img"
        style="url(<?php echo (($page_image!='') ? $page_image : '../img/hotel.jpeg' ) ?>)">
        <div class="container">
            <div class="contact_wrapper__heading">
                <h3><?php echo $page_title ?></h3>
            </div>
        </div>
    </section>

    <section class="term_content">
        <div class="container">
            <div class="row vertical-center-row">
                <div class="col-2"></div>
                <div class="col-8 page_description">
                    <?php echo $page_description ?>
                </div>
                <div class="col-2"></div>
            </div>
        </div>
    </section>
</main>
<?php

    require_once 'footer.php';

?>