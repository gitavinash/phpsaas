<?php

require_once 'header.php';

?>

<!-- Category Hero -->
<section class="category-hero-section">
    <div class="container reveal-on-load">
        <span class="editorial-tag">Tag</span>

        <h1 class="hero-premium-headline">
            <?php echo ucwords($tag_title) ?>
        </h1>

        <p class="category-hero-subtitle reveal-on-load">
            <?php echo $tag_description ?>
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="category-main-content">
    <div class="container">

        <div class="category-feed">

            <div class="related-posts-list" style="margin: 0;">

                <?php

                if (is_array($tagPost) && !empty($tagPost)) {

                    $tagId = [];

                    for ($i = 0; $i < sizeof($tagPost); $i++) {

                        $tagId[] = (int) $tagPost[$i]['tag_relation_id'];
                    }

                    $resultTags = implode(',', $tagId);

                    $query = "
                        SELECT 
                            link_url,
                            posttitle,
                            postimage,
                            postpaid,
                            meta_description,
                            profile_display_name,
                            link_published AS publishedTime,
                            postpublished
                        FROM " . TABLE_PREFIX . "link
                        
                        INNER JOIN " . TABLE_PREFIX . "post 
                            ON link_relation_id = postid
                            
                        INNER JOIN " . TABLE_PREFIX . "meta 
                            ON meta_relation_id = link_id
                            
                        INNER JOIN " . TABLE_PREFIX . "profile 
                            ON postowner_id = profile_user_id
                            
                        WHERE 
                            link_site_id = " . SITE_ID . "
                            AND link_type = 'post'
                            AND link_status = 200
                            AND link_relation_id IN ({$resultTags})
                            
                        ORDER BY postpublished DESC
                        
                        LIMIT 0, 20
                    ";

                    if ($postList = mysqli_query($connection, $query)) {

                        if (mysqli_num_rows($postList) != 0) {

                            while ($postData = mysqli_fetch_assoc($postList)) {

                                extract($postData);

                ?>

                <!-- Blog Card -->
                <div class="blog-card related-blog-card">

                    <div class="featured-img-col">

                        <a href="<?php echo $link_url ?>" class="featured-img-link related-img-link">

                            <img src="<?php echo $postimage ?>" alt="<?php echo htmlspecialchars($posttitle) ?>" />

                        </a>

                    </div>

                    <div class="featured-text-col related-text-col">

                        <a href="#" class="category-tag">

                            <?php echo ucwords($tag_title) ?>

                        </a>

                        <a href="<?php echo $link_url ?>" class="featured-title-link related-title-link">

                            <h2 class="serif-normal">

                                <?php echo $posttitle ?>

                                <?php

                                                if ($postpaid == 1) {

                                                    echo " <img src='" . USER_IMAGES . "premium.png' 
                                                            alt='Premium'
                                                            style='width:18px; margin-left:6px; display:inline-block;'>";
                                                }

                                                ?>

                            </h2>

                        </a>

                        <p class="blog-excerpt related-blog-excerpt">

                            <?php echo $meta_description ?>

                        </p>

                        <div class="post-meta-info" style="margin-top:15px; font-size:14px; opacity:0.7;">

                            <span>

                                <?php echo date('d M Y, h:i A', strtotime($postpublished)) ?>

                            </span>

                            <?php

                                            if (!empty($profile_display_name)) {

                                                echo " • <span>{$profile_display_name}</span>";
                                            }

                                            ?>

                        </div>

                        <a href="<?php echo $link_url ?>" class="read-more-link">

                            Read More <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

                <?php
                            }
                        } else {

                            ?>

                <div class="no-post-found">

                    <h3>No Posts Found</h3>

                </div>

                <?php
                        }
                    } else {

                        echo mysqli_error($connection);
                    }
                } else {

                    ?>

                <div class="no-post-found">

                    <h3>No Posts Available</h3>

                </div>

                <?php

                }

                ?>

            </div>

            <!-- Pagination -->
            <div class="number-pagination-container">

                <?php

                require_once 'pagination.php';

                ?>

            </div>

        </div>

    </div>
</section>

<?php

require_once 'footer.php';

?>