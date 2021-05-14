<?php

    include_once 'header.php';

?>

<br />

<div class="container">
    <div class="categories__grid__post">
        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="breadcrumb__text">
                    <h2>Categories: <span><?php echo $category_title ?></span></h2>
                    <div class="breadcrumb__option">
                        <a href="/">Home</a>
                        <span><?php echo $category_title ?></span>
                    </div>
                </div>
                <div class="row">
                            
                            <?php

                                if($postList=mysqli_query($connection, "SELECT link_url, post_title, post_image, meta_description, profile_display_name, link_published FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN isw_category ON category_id=post_category INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id INNER JOIN ".TABLE_PREFIX."profile ON post_owner_id=profile_user_id WHERE link_site_id=".SITE_ID." AND link_type='post' AND link_status=200 AND post_category={$category_id} ORDER BY link_published DESC LIMIT 0, 100")) {

                                    if(mysqli_num_rows($postList)!=0) {

                                        while($postData=mysqli_fetch_assoc($postList)) {

                                            extract($postData);

                                            echo "<div class='categories__post__item'>
                                            <div class='categories__post__item__pic small__item set-bg' data-setbg='{$post_image}' style='background-image: url(&quot;{$post_image}&quot;);'>
                                                <div class='post__meta'>
                                                    <h4>".date('d', strtotime($link_published))."</h4>
                                                    <span>".date('M', strtotime($link_published))."</span>
                                                </div>
                                            </div>
                                            <div class='categories__post__item__text'>
                                                <span class='post__label'>{$category_title}</span>
                                                <h3><a href='{$link_url}'>{$post_title}</a></h3>
                                                <ul class='post__widget'>
                                                    <li>by <span>{$profile_display_name}</span></li>
                                                </ul>
                                                <p>{$meta_description}</p>
                                            </div>
                                        </div>";

                                        }

                                    }

                                } else {

                                    echo mysqli_error($connection);
                                    exit();

                                }

                                ?>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <?php require_once 'sidebar.php'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <br />

<?php

    include_once 'footer.php';

?>