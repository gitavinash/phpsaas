<?php

	include_once 'header.php';

?>

    <section class="bgD01F29 no-padding slider">
        <div class="homepageSlider">
            <?php

                if($topNews=mysqli_query($connection, "SELECT link_url, news_image, news_title, category_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."news ON link_relation_id=news_id INNER JOIN ".TABLE_PREFIX."category ON news_category=category_id WHERE link_status=200 AND link_is_featured=1 AND link_type='news' AND link_site_id=1 AND news_status=1 ORDER BY link_modified DESC LIMIT 0, 5")) {
                    if(mysqli_num_rows($topNews)!=0) {
                        while($topNewsData=mysqli_fetch_assoc($topNews)) {

                            $topNewsPost[]=$topNewsData;

                        }
                    }
                } else {
                    echo mysqli_error($connection);
                    exit();

                }

                for($i=0; $i < 5 ; $i++) {
                    echo "
                        <a href='{$topNewsPost[$i]['link_url']}' title='{$topNewsPost[$i]['news_title']}' >
                            <div class='slider'>
                                <div class='img-box'>
                                    <img src='{$topNewsPost[$i]['news_image']}' alt='{$topNewsPost[$i]['news_title']}'>
                                </div>
                                <div class='text-box'>
                                    <h1>{$topNewsPost[$i]['news_title']}</h1>
                                </div>
                            </div>
                        </a>";
                }

            ?>
        </div>
    </section>

    <!-- Advertisment Box -->
    
    <section class="ad-box" ></section>

    <!-- Latest News -->

    <section>
        <div class="categoryBox">
            <?php

                if($recentNews=mysqli_query($connection, "SELECT link_url, news_thumbnail, news_title, category_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."news ON link_relation_id=news_id INNER JOIN ".TABLE_PREFIX."category ON news_category=category_id WHERE link_status=200 AND link_type='news' AND link_site_id=1 AND news_status=1 ORDER BY link_modified DESC LIMIT 0, 15")) {
                    
                    if(mysqli_num_rows($recentNews)!=0) {

                        $i=1;
                    
                        while($recentNewsData=mysqli_fetch_assoc($recentNews)) {
                            
                            extract($recentNewsData);

                            echo "
                                <div class='post-box'>
                                    <a href='{$link_url}' title='{$category_title}' >
                                        <div class='text-box'>
                                            <span>{$category_title}</span>
                                            <h2>{$news_title}</h2>
                                        </div>
                                        <div class='img-box'>";
                                            if($i==2) {
                                                echo "<span><img src='".USER_IMAGES."live-icon.gif' alt='live icon' class='live' /></span>";
                                            }
                                            echo "<img src='{$news_thumbnail}' class='preview' alt='{$news_title}'>
                                        </div>
                                    </a>
                                </div>";

                            if($i==5) { ?>

                                <!-- Advertisment Box -->
                                
                                <section class="in-article-ad-box" ></section>
                            
                            <?php

                                $i=0;

                            }

                            $i++;

                            
                        }
                        
                    }

                } else {
                    echo mysqli_error($connection);
                    exit();

                }

            ?>
        </div>
    </section>

<?php

	include_once 'footer.php';

?>