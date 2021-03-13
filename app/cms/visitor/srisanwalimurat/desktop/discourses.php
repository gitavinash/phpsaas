<?php

    include_once 'header.php';

?>


<section>
    <div class="gallery">
        <h1>Discourses</h1>

        <?php
        
        if($galleryList=mysqli_query($connection, "SELECT link_url, post_title, post_image FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id WHERE link_type='post' AND post_type='discourses' AND link_status=200 AND link_site_id=".SITE_ID)) {

            if(mysqli_num_rows($galleryList)!=0) {

                while($galleryListData=mysqli_fetch_assoc($galleryList)) {

                    extract($galleryListData);

                    echo "<div class='img-gallery-magnific'>
                        <div class='magnific-img cur-pointer'>
                            <a class='image-popup-vertical-fit' href='{$link_url}' title='{$post_title}'>
                                <img src='{$post_image}' alt='{$post_title}' />
                            </a>
                            <a href='{$link_url}' class='gallery-title' >{$post_title}</a>
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