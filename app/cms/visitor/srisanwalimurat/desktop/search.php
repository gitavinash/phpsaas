<?php

    require_once 'header.php';

?>

<section>
    <div class="gallery">
        <h1>Search : <?php echo $searchKeyword ?></h1>


        <?php

            if(is_array($queryPost)) {

                if(isset($queryPost[0]['tag_type'])) {

                    for($i=0; $i<sizeof($queryPost); $i++) {

                        $tagType=((isset($queryPost[$i]['tag_type'])) ? $queryPost[$i]['tag_type'] : 'post');

                        $mysqlSearchQuery[]="SELECT link_url, {$tagType}_title as search_title, {$tagType}_image as search_image, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."{$tagType} ON link_relation_id={$tagType}_id WHERE link_site_id=".SITE_ID." AND link_type='{$tagType}' AND link_status=200 AND link_relation_id={$queryPost[$i]['tag_relation_id']} ORDER BY link_id DESC LIMIT 0, 10";

                    }

                    $mysqlSearchQuery=implode("; ", $mysqlSearchQuery);

                    if($queryResults=mysqli_multi_query($connection, $mysqlSearchQuery)) {

                        do {

                            if($queryPost=mysqli_store_result($connection)) {
                                
                                while($queryResult=mysqli_fetch_assoc($queryPost)) {

                                    extract($queryResult);
                                    
                                    echo "<div class='img-gallery-magnific'>
                                        <div class='magnific-img cur-pointer'>
                                            <a class='image-popup-vertical-fit' href='{$link_url}' title='{$search_title}'>
                                                <img src='{$search_image}' alt='{$search_title}' />
                                            </a>
                                            <a href='{$link_url}' class='gallery-title' >{$search_title}</a>
                                        </div>
                                    </div>";

                                    
                                }
                                
                                mysqli_free_result($queryPost);

                            }

                        } while(

                            mysqli_next_result($connection)
                            
                        );

                    } else {

                        echo mysqli_error($connection);

                    }

                } else {

                    for($i=0; $i<sizeof($queryPost); $i++) {

                        extract($queryPost[$i]);

                        echo "<div class='img-gallery-magnific'>
                                <div class='magnific-img cur-pointer'>
                                    <a class='image-popup-vertical-fit' href='{$link_url}' title='{$post_title}'>
                                        <img src='{$post_image}' alt='{$post_title}' />
                                    </a>
                                    <a href='{$link_url}' class='gallery-title' >{$post_title}</a>
                                </div>
                            </div>";

                    }

                }

            } else {
                
                if($searchInEvent=mysqli_query($connection,  "SELECT event_title, event_image, link_url FROM ".TABLE_PREFIX."event INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=event_id WHERE event_title LIKE '%{$searchKeyword}%' AND event_site_id=".SITE_ID." AND link_status=200 AND link_type='event' ORDER BY event_time DESC")) {

                    if(mysqli_num_rows($searchInEvent)!=0) {

                        while($searchInEventData=mysqli_fetch_assoc($searchInEvent)) {
                            
                            extract($searchInEventData);

                            echo "<div class='img-gallery-magnific'>
                                <div class='magnific-img cur-pointer'>
                                    <a class='image-popup-vertical-fit' href='{$link_url}' title='{$event_title}'>
                                        <img src='{$event_image}' alt='{$event_title}' />
                                    </a>
                                    <a href='{$link_url}' class='gallery-title' >{$event_title}</a>
                                </div>
                            </div>";

                        }

                    } else {

                        if($searchInGallery=mysqli_query($connection, "SELECT gallery_title, gallery_image, link_url FROM ".TABLE_PREFIX."gallery INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=gallery_id WHERE gallery_title LIKE '%{$searchKeyword}%' AND gallery_site_id=".SITE_ID." AND link_status=200 AND link_type='gallery' ORDER BY gallery_time DESC")) {

                        if(mysqli_num_rows($searchInGallery)!=0) {
                            
                            while($searchInGalleryData=mysqli_fetch_assoc($searchInGallery)) {
                                
                                extract($searchInGalleryData);

                                echo "<div class='img-gallery-magnific'>
                                    <div class='magnific-img cur-pointer'>
                                        <a class='image-popup-vertical-fit' href='{$link_url}' title='{$gallery_title}'>
                                            <img src='{$gallery_image}' alt='{$gallery_title}' />
                                        </a>
                                        <a href='{$link_url}' class='gallery-title' >{$gallery_title}</a>
                                    </div>
                                </div>";

                            }

                        } else { ?>

        <!-- No Result found -->

        <section class='continue-shopping'>
            <h1><span>No result found</span></h1>
            <a href="/" class="btn btn-success">Go to homepage</a>
        </section>

        <?php
                        }

                    }

                    }

                }

            }

        ?>

    </div>
</section>

<?php

    require_once 'footer.php';

?>