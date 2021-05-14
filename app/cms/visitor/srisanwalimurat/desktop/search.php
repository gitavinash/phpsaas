<?php

    require_once 'header.php';

    print_r($queryPost);

?>

<section>
    <div class="gallery">
        <h1>Search : <?php echo $searchQuery ?></h1>


        <?php

            if(is_array($queryPost)) {

                if(!isset($productListType)) {

                    for($i=0; $i<sizeof($queryPost); $i++) {

                        $tagId[]=$queryPost[$i]['tag_relation_id'];
                        $tagType = (($queryPost[$i]['tag_type']=='event') ? 'event' : 'video' );

                    }

                    $resultTags=implode(' OR link_relation_id=', $tagId);

                    if($queryResults=mysqli_query($connection, "SELECT link_url, {$tagType}_title, {$tagType}_image, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."{$tagType} ON link_relation_id={$tagType}_id WHERE link_site_id=".SITE_ID." AND link_type='{$tagType}' AND link_status=200 AND (link_relation_id={$resultTags}) ORDER BY link_id DESC LIMIT 0, 10")) {

                        if(mysqli_num_rows($queryResults)!=0) {

                            while($queryResultsData=mysqli_fetch_assoc($queryResults)) {

                                extract($queryResultsData);

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

                            // echo 'No result found';
                            echo "SELECT link_url, {$tagType}_title, {$tagType}_image, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."{$tagType} ON link_relation_id={$tagType}_id WHERE link_site_id=".SITE_ID." AND link_type='{$tagType}' AND link_status=200 AND (link_relation_id={$resultTags}) ORDER BY link_id DESC LIMIT 0, 10";

                        }

                    } else {

                        echo mysqli_error($connection);

                    }

                } else {

                    echo 'I m here';

                }

            } else { ?>

                <!-- No Result found -->

                <section class='continue-shopping' >
                    <h1><span>No product found</span></h1>
                    <a href="/" class="btn btn-success" >Go to homepage</a>
                </section>

            <?php
            }

        ?>

    </div>
</section>

<?php

    require_once 'footer.php';

?>