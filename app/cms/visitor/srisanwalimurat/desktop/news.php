<?php

    include_once 'header.php';

?>

<section>
    <div class="about-us">
        <h1><?php echo $news_title ?></h1>
        <div class="feature-image">
            <img src="<?php echo $news_image ?>" width="100%" alt="">
        </div>
        <p><?php echo $news_description ?></p>
    </div>
</section>

<?php

    include_once 'footer.php';

?>