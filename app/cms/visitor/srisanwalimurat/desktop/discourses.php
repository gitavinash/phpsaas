<?php

    include_once 'header.php';

?>


<section>
    <div class="gallery">
        <h1>Discourses</h1>

        <?php
        
        if($galleryList=mysqli_query($connection, "SELECT link_url, gallery_title, gallery_image FROM ".TABLE_PREFIX."gallery INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=gallery_id WHERE link_type='gallery' AND link_status=200 AND link_site_id=".SITE_ID)) {

            if(mysqli_num_rows($galleryList)!=0) {

                while($galleryListData=mysqli_fetch_assoc($galleryList)) {

                    extract($galleryListData);

                    echo "<div class='img-gallery-magnific'>
                        <div class='magnific-img cur-pointer'>
                            <a class='image-popup-vertical-fit' href='{$link_url}' title='{$gallery_title}'>
                                <img src='{$gallery_image}' alt='{$gallery_title}' />
                            </a>
                            <a href='{$link_url}' class='gallery-title' >{$gallery_title}</a>
                        </div>
                    </div>";

                }

            } else {

                echo 'No discourses found';

            }

        }

        ?>

    </div>
</section>

<?php

    include_once 'footer.php';

?>