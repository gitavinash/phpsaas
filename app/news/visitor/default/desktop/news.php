<?php

    include_once 'header.php';

?>

<!-- Default post view -->

<section class='body'>

    <div class="main">
        <ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList" >

            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a itemprop="item" href="<?php echo SITE_URL ?>"><span itemprop="name">Home</span></a>
                <meta itemprop="position" content="1" />
            </li>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a itemprop="item" href="<?php echo SITE_URL ?>/category/<?php echo $urlStructure[0]; ?>"><span itemprop="name"><?php echo $category_title ?></span></a>
                <meta itemprop="position" content="2" />
            </li>
            <li class='last-nav' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a itemprop="item" href="<?php echo $link_url ?>"><span itemprop="name"><?php echo $news_title ?></span></a>
                <meta itemprop="position" content="3" />
            </li>

        </ul>

        <h1><?php echo $news_title; ?></h1>

        <!-- Author Box -->
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
        </div>
        
        <div class="feature-img">
            <a href='//<?php echo SITE_DOMAIN.$news_image ?>' class='progressive replace'>
                <img src='<?php $thumbImage=explode('/', $news_image); echo str_replace(end($thumbImage), '', $news_image).'thumbnail/'.end($thumbImage); ?>' class='preview' alt='{$news_title}'>
            </a>
        </div>
        <div class="content">
            <?php
            
                echo $bodyContent;

            ?>
        </div>

        <div class="comment" style="margin-top: 20px" >
            <a href="/download-app.html" target="_blank" ><img src="<?php echo USER_IMAGES ?>app-download-banner.jpg" alt=""></a>
        </div>

        <div class="tags">
            <?php
                $tags=explode(', ', $tagName);
                for($i=0; $i<sizeof($tags); $i++) {
                    if($tags[$i]!='') {
                        echo "<a href='tag/".str_replace(' ', '-', strtolower($tags[$i]))."/' class='english' >{$tags[$i]}</a>";
                    }
                }
            ?>
        </div>

        <!-- Composite Start -->
<div id="M531126ScriptRootC812430">
</div>
<script src="https://jsc.mgid.com/p/u/punjabkesari.com.812430.js" async>
</script>
<!-- Composite End -->

        <div class="comment">
            <div class="fb-comments" data-href="<?php echo SITE_URL.ACTIVE_URL ?>" data-width="100%" data-numposts="5"></div>
        </div>

        <!-- <div class="taboola">
            <div id="taboola-below-article-thumbnails"></div>
            <script type="text/javascript">
            window._taboola = window._taboola || [];
            _taboola.push({
                mode: 'thumbnails-a',
                container: 'taboola-below-article-thumbnails',
                placement: 'Below Article Thumbnails',
                target_type: 'mix'
            });
            </script>
        </div> -->

    </div>

    <div class="sidebar">
        <?php
        
            require_once 'sidebar.php';

        ?>
    </div>

</section>

<?php

    include_once 'footer.php';