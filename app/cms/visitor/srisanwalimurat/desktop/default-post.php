<?php

    include_once 'header.php';

?>

<section>
    <div class="about-us">
        <h1><?php echo $post_title ?></h1>
        <?php
        
            if($post_type=='post') { ?>

                <div class="feature-image">
                    <img src="<?php echo $post_image ?>" width="100%" alt="">
                </div>

            <?php
            }

        ?>
        <p><?php echo $post_description ?></p>
    </div>
</section>

<?php

    include_once 'footer.php';

?>