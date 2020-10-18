<?php

    include_once 'header.php';

?>

<section class="main" >
    <div class="container">
        <h1 class="pageHeading" ><?php echo $post_title ?></h1>
        <div class="page-content">
            <img src="<?php echo $post_image ?>" alt="">
            <?php echo $post_description ?>
        </div>
    </div>
</section>

<?php

    include_once 'footer.php';

?>