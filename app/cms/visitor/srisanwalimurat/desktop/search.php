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

            } else { ?>

        <!-- No Result found -->

        <section class='continue-shopping'>
            <h1><span>No result found</span></h1>
            <a href="/" class="btn btn-success">Go to homepage</a>
        </section>

        <?php
            }

        ?>

    </div>
</section>

<?php

    require_once 'footer.php';

?>