<?php

    include_once 'header.php';

?>

    <!-- Default post view -->

    <section class='article'>

        <!-- Advertisment Box -->

        <section class="ad-box-small-rectangular"></section>

        <h1><?php echo $news_title; ?></h1>

        <div class="feature-img">
            <img src="//<?php echo SITE_DOMAIN.$news_image ?>" alt="<?php $imagePath=explode('/', $news_image); $imaegFileName=strstr(strstr(end($imagePath), '.', true), '_'); echo str_replace('_', ' ', $imaegFileName); ?>">
        </div>

        <!-- Author Box
            <div class='post-info'>
                <div class="author-img"><img src="<?php echo (($profile_image=='') ? USER_IMAGES.'default-profile.png' : $profile_image ); ?>" alt="<?php echo $profile_display_name ?>"></div>
                <div class="author-name"><p><?php echo $profile_display_name; ?></p></div>
                <div class="post-update"><p><strong>Updated </strong> <?php echo date('D, dS M Y h:i A T', strtotime($link_modified)) ?></p></div>
                <div class="post-share">
                    <div class="social-link">
                        <a href="https://www.facebook.com/sharer.php?u=<?php echo SITE_URL.BASE_URL ?>" target='_blank' ><i class="fab fa-facebook-square"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo SITE_URL.BASE_URL ?>&text=<?php echo $news_title ?>&hashtags=<?php echo str_replace(' ', '', $tagName) ?>" target='_blank' ><i class="fab fa-twitter-square"></i></a>
                        <a href="https://reddit.com/submit?url=<?php echo SITE_URL.BASE_URL ?>&title=<?php echo $news_title ?>" target='_blank' ><i class="fab fa-reddit-square"></i></a>
                        <a href="http://pinterest.com/pin/create/link/?url=<?php echo SITE_URL.BASE_URL ?>&media=<?php echo urlencode(HTTP_PROTOCOL.SITE_DOMAIN.$news_image); ?>&description=<?php echo ((isset($meta_description)) ? (($meta_description!='') ? $meta_description : SITE_DESC ) : SITE_DESC ) ?>" target='_blank' ><i class="fab fa-pinterest-square"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo SITE_URL.BASE_URL ?>" target='_blank' ><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div> -->

            <div class="content">
                <?php
                
                    echo $bodyContent;

                ?>
            </div>

            <div class="tags">

                <?php
                    $tags=explode(', ', $tagName);
                    for($i=0; $i<sizeof($tags); $i++) {
                        if($tags[$i]!='') {
                            echo "<a href='tag/".str_replace(' ', '-', strtolower($tags[$i]))."/' class='english' >#".str_replace(' ', '_', $tags[$i])."</a>";
                        }
                    }
                ?>
            </div>

        </div>

    </section>

<?php

    include_once 'footer.php';