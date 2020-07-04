<?php

    include_once 'header.php';

?>

<!-- Default page view -->

<section class='body'>
    <div class="main">
        <h1><?php echo $page_title; ?></h1>
        <div class="feature-img">
            <img src="//<?php echo SITE_DOMAIN.$page_image ?>" alt="">
        </div>
        <div class="content">
            <?php echo $page_description ?>
        </div>
    </div>
</section>

<?php

    include_once 'footer.php';