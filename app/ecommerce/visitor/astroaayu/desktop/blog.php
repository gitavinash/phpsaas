<?php require_once 'header.php' ?>
<div class="common_banner">
    <h1 class="banner_title">Blogs</h1>
</div>
<!-- This is astro consultant section -->
<section class="astro_consultant padding-top padding-bottom">
    <div class="container">
        <div class="row">

            <?php

            // Recent post

            if ($recentPost = mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM " . TABLE_PREFIX . "post INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=post_id INNER JOIN " . TABLE_PREFIX . "category ON category_id=post_category WHERE link_type='post' AND link_status=200 AND link_site_id=" . SITE_ID . " ORDER BY link_published DESC LIMIT 0, 4")) {

                if (mysqli_num_rows($recentPost) != 0) {

                    while ($recentPostData = mysqli_fetch_assoc($recentPost)) {

                        extract($recentPostData); ?>

            <div class="col-md-12 col-sm-12">
                <article class="article">
                    <div class="article-img">
                        <a href="<?php echo $link_url ?>">
                            <img src="<?php echo $post_image ?>" alt="">
                        </a>
                    </div>
                    <div class="article-body">
                        <h4 class="article-title"><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h4>
                        <ul class="article-meta">
                            <li><i class="fa fa-clock-o"></i> <?php echo date('F d, Y', strtotime($link_published)) ?>
                            </li>
                        </ul>
                    </div>
                </article>
            </div>

            <?php

                    }
                } else {
                    echo '<div class="col-md-12"><p class="text-center">No recent posts available.</p></div>';
                }
            }

            ?>
        </div>
    </div>
</section>

<?php require_once 'footer.php' ?>