<?php

    include_once 'header.php';

?>

<br />

<div class="container">
    <div class="categories__grid__post">
        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="breadcrumb__text">
                    <h2>Search: <span><?php echo $searchQuery ?></span></h2>
                    <div class="breadcrumb__option">
                        <a href="/">Home</a>
                        <a href="#" class="search-collapse-btn" >Search</a>
                        <span><?php echo $searchQuery ?></span>
                    </div>
                </div>
                <div class="row">

                    <?php
                    
                        if(is_array($queryPost)) {

                            for($i=0; $i<sizeof($queryPost); $i++) {

                                $tagId[]=$queryPost[$i]['tag_relation_id'];

                            }

                            $resultTags=implode(' OR link_relation_id=', $tagId);

                            if($queryResults=mysqli_query($connection, "SELECT link_url, link_published, post_title, post_image, profile_display_name, meta_description FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."post ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."profile ON profile_user_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id=link_id WHERE link_site_id=".SITE_ID." AND link_type='post' AND link_status=200 AND (link_relation_id={$resultTags}) ORDER BY link_id DESC LIMIT 0, 10")) {

                                if(mysqli_num_rows($queryResults)!=0) {
    
                                    while($queryResultsData=mysqli_fetch_assoc($queryResults)) {
    
                                        extract($queryResultsData);
    
                                        
                                        echo "<div class='categories__post__item'>
                                            <div class='categories__post__item__pic small__item set-bg' data-setbg='{$post_image}' style='background-image: url(&quot;{$post_image}&quot;);'>
                                                <div class='post__meta'>
                                                    <h4>".date('d', strtotime($link_published))."</h4>
                                                    <span>".date('M', strtotime($link_published))."</span>
                                                </div>
                                            </div>
                                            <div class='categories__post__item__text'>
                                                <span class='post__label'>{$tag_title}</span>
                                                <h3><a href='{$link_url}'>{$post_title}</a></h3>
                                                <ul class='post__widget'>
                                                    <li>by <span>{$profile_display_name}</span></li>
                                                </ul>
                                                <p>{$meta_description}</p>
                                            </div>
                                        </div>";
    
                                    }
    
                                } else {

                                    echo '<h3 style="float: left; width: 100%; text-align: center; padding: 100px 0px" >No search result found</h3>';

                                }
    
                            }

                        } else {

                            echo '<h3 style="float: left; width: 100%; text-align: center; padding: 100px 0px" >No search result found</h3>';

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