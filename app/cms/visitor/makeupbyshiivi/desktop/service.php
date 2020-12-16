<?php

    include_once 'header.php';

?>

<!-- Portfolio -->
<section class="mbs-portfolio-page-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="mbs-blog-items mbs-single-post">
                    <div class="entry-thumb"> <img src="<?php echo $service_image ?>" alt="blog post"></div>
                    <div class="mbs-page-content mb-20">
                        <h2><?php echo $service_title ?></h2>
                    </div>
                    <?php echo $service_description ?>
                </div>
            </div>
        </div>
        <div class="row mbs-portfolio-gallery-section">
            <div class="col-md-12 text-center">
                <h2>Portfolio Photos</h2>
            </div>
        </div>
            <div class="row gallery-items">
                <?php
                
                    $service_gallery=json_decode($service_gallery, TRUE);

                    for($i=0; $i<sizeof($service_gallery); $i++) { ?>
                        <div class="col-md-6 single-item hair">
                            <div class="gallery-wrap">
                                <img src="<?php echo $service_gallery[$i]['path'] ?>" alt="<?php echo $service_gallery[$i]['name'] ?>" >
                            </div>
                        </div>
                    <?php
                    }

                ?>
            </div>
            
        </div>
</section>
<?php

    include_once 'footer.php';

?>