<?php

    require_once 'header.php';

?>
<section class="blog_banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="blog_banner_img">
                    <img src="assets/images/laravel-vs-express.avif" class="img-fluid" alt="">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="blog_banner__left">
                    <span><?php echo date('d F Y', strtotime($link_published)) ?></span>
                    <h3><?php echo $news_title ?></h3>
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
                    <?php echo $news_description ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>