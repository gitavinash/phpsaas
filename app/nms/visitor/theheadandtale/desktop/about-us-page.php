<?php

    require_once 'header.php';

?>
<!-- about -->
<section class="about_wrapper">
    <div class="container">
        <div class="about_content">
            <h1><?php echo $page_title ?></h1>
        </div>
    </div>
</section>

<!-- about banner -->
<section class="about_container">
    <div class="container">
        <div class="row about_flex">
            <div class="col-md-9">
                <div class="about_parra">
                    <?php echo $page_description ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="client_img">
                    <img src="<?php echo $page_image ?>" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

<?php

    require_once 'footer.php';

?>