<?php

    include_once 'header.php';

?>

<section class="mbs-about-section mbs-about-inner-page">
        <div class="container">
            <div class="row mbs-about-inner">
                <div class="col-md-6"> <img class="mbs-about-img" src="<?php echo $page_image ?>" alt=""> </div>
                <div class="col-md-6">
                    <div class="mbs-about-info">
                        <h2><?php echo $page_title ?></h2>
                        <?php echo $page_description ?>
                    </div>
                </div>
            </div>
        </div>
</section>

<?php

    include_once 'footer.php';

?>