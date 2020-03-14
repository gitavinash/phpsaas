<?php

    include_once 'header.php';

?>
        <section class="bgD01F29 no-padding slider">
            <amp-carousel
                width="320"
                height="200"
                layout="responsive"
                type="slides">
                
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

                    for($i=0; $i < 5 ; $i++) { ?>

                        <div>
                            <div class="slide">
                                <a href="/amp<?php echo $topNewsPost[$i]['link_url']; ?>">
                                    <div class="img-box">
                                        <amp-img src="<?php echo $topNewsPost[$i]['news_image']; ?>"
                                            layout="responsive"
                                            width="320"
                                            height="200"
                                            alt="<?php echo $topNewsPost[$i]['news_title'] ?>">
                                        </amp-img>
                                    </div>
                                    <div class="text-box">
                                        <amp-fit-text layout="responsive"
                                            width="500"
                                            height="150">
                                            <h1 style="position: relative; float:left; font-size: 18px; line-height: 28px; padding: 5px 10px;" ><?php echo $topNewsPost[$i]['news_title']; ?></h1>
                                        </amp-fit-text>
                                    </div>
                                </a>
                            </div>
                        </div>

                    <?php

                    }

                ?>

            </amp-carousel>
        </section>

        <!-- Advertisment Box -->
        
        <section class="ad-box" ></section>

        <!-- Latest News -->

        <section>
            <div class="categoryBox">
                <?php

                    if($recentNews=mysqli_query($connection, "SELECT link_url, news_image, news_title, category_title FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."news ON link_relation_id=news_id INNER JOIN ".TABLE_PREFIX."category ON news_category=category_id WHERE link_status=200 AND link_type='news' AND link_site_id=1 AND news_status=1 ORDER BY link_modified DESC LIMIT 0, 15")) {
                        
                        if(mysqli_num_rows($recentNews)!=0) {

                            $i=1;
                        
                            while($recentNewsData=mysqli_fetch_assoc($recentNews)) {
                                
                                extract($recentNewsData);

                                echo "
                                    <div class='post-box'>
                                        <a href='/amp{$link_url}'>
                                            <div class='text-box'>
                                                <span>{$category_title}</span>
                                                <h2>{$news_title}</h2>
                                            </div>
                                            <div class='img-box'>";
                                                if($i==2) {
                                                    echo "<span><amp-img src='".USER_IMAGES."live-icon.gif' class='live'  layout='fixed' width='40' height='13' alt='LIVE : {$news_title}' ></amp-img></span>";
                                                }
                                                echo "<amp-img src='{$news_image}' class='preview' layout='responsive' width='500' height='300' alt='{$news_title}'></amp-img>
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

    </main>

<?php

    include_once 'footer.php';

?>