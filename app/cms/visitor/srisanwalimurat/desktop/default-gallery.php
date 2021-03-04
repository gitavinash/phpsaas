<?php

    include_once 'header.php';

?>

<section>
    <div class="gallery">
        <h1><?php echo $gallery_title ?></h1>

        <?php
        
            $galleryList=json_decode($gallery_image_list, TRUE);

            for($i=0; $i<sizeof($galleryList); $i++) {

                extract($galleryList[$i]);

                echo "<div class='img-gallery-magnific'>
                    <div class='magnific-img'>
                        <a class='image-popup-vertical-fit' href='{$path}' title='{$name}'>
                            <img src='{$path}' alt='{$name}' />
                        </a>
                    </div>
                </div>";

            }

        ?>

    </div>
</section>

<?php
    include_once 'footer.php';

?>