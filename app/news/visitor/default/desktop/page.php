<?php

    include_once 'header.php';

?>

<!-- Default post view -->

<section class='body'>
    <div class="main">
        <h1><?php echo $post_title; ?></h1>
        <div class="feature-img">
            <img src="//<?php echo SITE_DOMAIN.$post_image ?>" alt="">
        </div>
        <div class="content">
            <?php echo $post_content ?>
        </div>
    </div>
</section>

<?php

    include_once 'footer.php';