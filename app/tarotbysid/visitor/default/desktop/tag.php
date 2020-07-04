<?php

    require_once 'header.php';

?>

    <section>
        <div class="homepage-slider-box">
            <div class="homepage-slider">
                        <?php
                        for($i=0; $i < 5 ; $i++) {

                            if(isset($tagPost[$i])) {

                                echo "<div class='slider'>
                                        <div class='img-box'>
                                            <a href='{$tagPost[$i]['link_url']}' data-href='{$tagPost[$i]['news_image']}' class='progressive replace'>
                                                <img src='/upload/preview.jpg' class='preview' alt='{$tagPost[$i]['news_title']}'>
                                            </a>
                                            <img src='{$tagPost[$i]['news_image']}' alt=''>
                                        </div>
                                        <a href='{$tagPost[$i]['link_url']}'>
                                            <div class='text-box'>
                                                <p class='skew'><span>".ucwords($tag_title)."</span></p>
                                                <h1>{$tagPost[$i]['news_title']}</h1>
                                            </div>
                                            <div class='summery-box'>
                                                <p>{$tagPost[$i]['meta_description']}</p>
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
                                
                                if(isset($tagPost[$i])) {

                                    echo "<a href='{$tagPost[$i]['link_url']}' title='{$tagPost[$i]['news_title']}'><h2>{$tagPost[$i]['news_title']}</h2></a>";

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

                    for($i=8; $i<32; $i++) {

                        if(isset($tagPost[$i])) {

                            echo "<div class='category-post'>
                                    <a href='{$tagPost[$i]['link_url']}' data-href='{$tagPost[$i]['news_image']}' class='progressive replace'>
                                        <img src='/upload/preview.jpg' class='preview' alt='{$tagPost[$i]['news_title']}'>
                                    </a>
                                    <a href='{$tagPost[$i]['link_url']}'><h3>{$tagPost[$i]['news_title']}</h3></a>
                                </div>";
                        }

                    }

                ?>
            </section>

<?php

    require_once 'footer.php';

?>