<?php

    require_once 'header.php';

?>
<section class="blog_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="blog_banner_img">
                    <img src="<?php echo $page_image ?>" class="img-fluid" alt="">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="blog_banner__left">
                    <h3><?php echo $page_title ?></h3>
                    <p><?php echo $meta_description ?></p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- blog container -->
<section class="blog_container">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-12"></div>
            <div class="col-lg-8 col-12">
                <div class="blog_parra">
                    <?php echo $page_description ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>