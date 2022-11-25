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
                        <h3 class="text-start mb-md-4">Patient Gallery</h3>
                    </div>

                    <?php

                    if ($imageGallery = mysqli_query($connection, "SELECT gallery_title, gallery_image, link_url FROM " . TABLE_PREFIX . "gallery INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = gallery_id WHERE link_type='gallery' AND link_status=200 AND gallery_category='{$category_id}' AND link_site_id=" . SITE_ID)) {

                        if (mysqli_num_rows($imageGallery) != 0) {

                            while ($galleryData = mysqli_fetch_assoc($imageGallery)) {

                                extract($galleryData); ?>

                    <div class="col-12 col-md-4">
                        <a href="<?php echo $link_url ?>">
                            <div class='galleryBox'>
                                <img src="<?php echo $gallery_image ?>" alt="<?php echo $gallery_title ?>" />
                                <h2 class='gallerTitle'><?php echo $category_title ?></h2>
                            </div>
                        </a>
                    </div>

                    <?php

                            }
                        }
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