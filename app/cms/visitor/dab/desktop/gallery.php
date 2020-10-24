<?php

    include_once 'header.php';

?>

<section class="main" >
    <h1 class="pageHeading" ><?php echo $gallery_title ?></h1>
    <div class="container">
        <div class="row">
            <div class="galleria">
                <?php
                
                    $galleryImageList=json_decode($gallery_image_list, TRUE);

                    for($i=0; $i<sizeof($galleryImageList); $i++) {

                        extract($galleryImageList[$i]);

                        echo "
                                    <img src='{$path}' alt='{$name}' >
                        ";

                    }

                ?>
            </div>
        </div>
    </div>
</section>

<?php

    include_once 'footer.php';

?>