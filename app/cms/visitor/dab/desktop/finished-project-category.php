<?php

    include_once 'header.php';

?>

<section id="blog" class="blog main" >
    <div class="container">
        <h2><?php echo $category_title ?></h2>
        <div class="blogSlider">

            <?php
            
            if($checkBlogs=mysqli_query($connection, "SELECT gallery_title, gallery_image, link_url FROM ".TABLE_PREFIX."gallery, ".TABLE_PREFIX."link, ".TABLE_PREFIX."category WHERE link_type='gallery' AND link_relation_id=gallery_id AND link_site_id='".SITE_ID."' AND category_id=gallery_category AND link_status=200 ORDER BY link_published DESC")) {

                    if(mysqli_num_rows($checkBlogs)!=0) {

                        while($blogData=mysqli_fetch_assoc($checkBlogs)) {

                            extract($blogData);

                            echo "
                            <a href='{$link_url}'>
                                <div class='slide'>
                                    <div class='img'>
                                        <img src='{$gallery_image}' alt='{$gallery_title}' >
                                    </div>
                                    <div class='text'>
                                        <h3 style='float: left; width: 100%; text-align: center' >{$gallery_title}</h3>
                                    </div>
                                </div>
                            </a>";

                        }

                    }

                }
            
            ?>

        </div>
    </div>
</section>

<?php

    include_once 'footer.php';

?>