<?php

require_once 'header.php'

?>
<section>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center p-0">
                <div class="inner_page_banner about_banner_bg">
                    <div class="inner_content">
                        <p>Gallery</p>
                        <div class="text-start"><a href="/">Home</a> <span><i
                                    class="fa fa-chevron-right"></i></span><span>Gallery</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container-fluid">
        <div class="row justify-content-center section_spacing_top section_spacing_bottom">
            <div class="col-lg-10">
                <div class="row">
                    <div class="section_heading">
                        <h3 class="text-start mb-md-4">Patient Results</h3>
                    </div>

                    <?php

                    $galleryList = json_decode($gallery_image_list, TRUE);

                    for ($i = 0; $i < sizeof($galleryList); $i++) {

                        extract($galleryList[$i]);

                        echo "<div class='col-12 col-md-4'>
                        <div class='galleryBox'>
                            <a href='' class='mediaGallery'
                                data-rbox-image='{$path}'
                                data-rbox-caption='{$name}' data-rbox-series='gallery'>
                                <img src='{$path}'
                                    alt='{$name}' />
                                <h2 class='gallerTitle' >{$name}</h2>
                            </a>
                        </div>
                    </div>";
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php

require_once 'footer.php';

?>