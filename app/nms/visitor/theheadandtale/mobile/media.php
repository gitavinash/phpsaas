<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="featureArticle">
            </div>
            <div class="left-container">
                <div class="mediaFile">
                    <h1><?php echo $media_title ?></h1>
                    <img style="float: left; width: 100%; height: auto" src="<?php echo SITE_URL . $media_url; ?>"
                        alt="">
                </div>
            </div>

            <?php

            require_once 'sidebar.php';

            ?>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>