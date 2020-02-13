<?php

    require_once 'header.php';

?>

    <section>
        <div class="homepage-slider-box">
            <div class="homepage-slider">
                        <?php
                        for($i=0; $i < 5 ; $i++) {

                            if(isset($categorySinglePost[$i])) {

                                echo "<div class='slider'>                                
                                        <div class='img-box'>
                                            <a href='{$categorySinglePost[$i]['link_url']}' data-href='{$categorySinglePost[$i]['news_image']}' class='progressive replace'>
                                                <img src='/upload/preview.jpg' class='preview' alt='{$categorySinglePost[$i]['news_title']}'>
                                            </a>
                                        </div>
                                        <a href='{$categorySinglePost[$i]['link_url']}'>
                                            <div class='text-box'>
                                                <p class='skew'><span>{$category_title}</span></p>
                                                <h1>{$categorySinglePost[$i]['news_title']}</h1>
                                            </div>
                                            <div class='summery-box'>
                                                <p>{$categorySinglePost[$i]['meta_description']}</p>
                                            </div>
                                        </a>
                                </div>";

                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="trending-box">
                    <div class="big-news">
                        <?php
                            for($i=5; $i<10; $i++) {
                                
                                if(isset($categorySinglePost[$i])) {
                                
                                    echo "<a href='{$categorySinglePost[$i]['link_url']}' title='{$categorySinglePost[$i]['news_title']}'><h2>{$categorySinglePost[$i]['news_title']}</h2></a>";

                                }
                            }
                        ?>
                    </div>
                    </div>
                </div>
            </section>

            <section class="header-ad-box">
            
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- Banner-728 x 90 -->
            <ins class="adsbygoogle"
                style="display:inline-block;width:728px;height:90px"
                data-ad-client="ca-pub-3305465668438254"
                data-ad-slot="6646472831"></ins>
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            
            </section>
            
            <section>
                <?php

                    for($i=7; $i<32; $i++) {
                        if(isset($categorySinglePost[$i])) {
                            
                            if(isset($categorySinglePost[$i])) {

                                echo "<div class='category-post'>
                                        <a href='{$categorySinglePost[$i]['link_url']}' data-href='{$categorySinglePost[$i]['news_image']}' class='progressive replace'>
                                            <img src='/upload/preview.jpg' class='preview' alt='{$categorySinglePost[$i]['news_title']}'>
                                        </a>
                                        <a href='{$categorySinglePost[$i]['link_url']}'><h3>{$categorySinglePost[$i]['news_title']}</h3></a>
                                </div>";

                            }
                        }
                    }

                ?>
            </section>

            <!-- Pagination -->

            <section>
                <div class="pagination">
                    <?php

                        $totalPost=mysqli_query($connection, "SELECT count(link_id) as totalPost FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."news ON news_id=link_relation_id WHERE news_category='{$category_id}' AND link_type='news' AND link_status=200");
                        
                        extract(mysqli_fetch_assoc($totalPost));

                        if($totalPost>PER_PAGE_LIMIT) {

                            /** If total post is greater than page limit */

                            $maxLimit=round($totalPost/PER_PAGE_LIMIT);

                            echo '<ul>';

                            if($currentPage!=1) {

                                echo "<li class='first-page'><a href='{$categoryLink}'><i class='fas fa-arrow-left'></i> First Page</a></li>";

                            }

                            if($currentPage==1) {

                                /** Show only next page */

                                for($i=1; $i<=10 && $i<$maxLimit; $i++) {

                                    echo "<li ".(($currentPage==$i) ? "class='active'" : '' )." ><a href='{$categoryLink}{$i}/'>{$i}</a></li>";
    
                                }

                            } else if($currentPage==$maxLimit) {

                                /** Show only back page */

                                for($i=($maxLimit-10); $i<=$maxLimit; $i++) {

                                    echo "<li ".(($currentPage==$i) ? "class='active'" : '' )." ><a href='{$categoryLink}{$i}/'>{$i}</a></li>";
    
                                }

                            } else {

                                /** Inner page pagination current page is less than 10 */

                                if($currentPage<10) {

                                    for($i=1; $i<=10; $i++) {

                                        echo "<li ".(($currentPage==$i) ? "class='active'" : '' )." ><a href='{$categoryLink}{$i}/'>{$i}</a></li>";
        
                                    }

                                } else {

                                    for($i=$currentPage-4; $i<=$currentPage; $i++) {

                                        echo "<li ".(($currentPage==$i) ? "class='active'" : '' )." ><a href='{$categoryLink}{$i}/'>{$i}</a></li>";

                                    }

                                    for($i=$currentPage+1; $i<=$currentPage+5; $i++) {

                                        echo "<li ".(($currentPage==$i) ? "class='active'" : '' )." ><a href='{$categoryLink}{$i}/'>{$i}</a></li>";

                                    }

                                }

                            }

                            if($maxLimit!=$currentPage) {

                                echo "<li class='last-page'><a href='{$categoryLink}{$maxLimit}/'>Last Page <i class='fas fa-arrow-right'></i></a></li>";
                            }

                            echo '</ul>';

                        }

                    ?>
                </div>
            </section>

            <!-- You plus -->

            <div id="yp_widget"></div>

<?php

    require_once 'footer.php';

?>