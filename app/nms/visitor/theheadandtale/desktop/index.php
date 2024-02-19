<?php

    require_once 'header.php'

?>
<section>
    <div class="container">
        <div class="row">
            <?php
            
                    if($newsArticles=mysqli_query($connection, "SELECT news_title, news_image, news_description, link_url, link_published, meta_description FROM ".TABLE_PREFIX."news INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=news_id INNER JOIN ".TABLE_PREFIX."meta ON meta_relation_id = link_id WHERE link_type='news' AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_id DESC LIMIT 0, 5")) {

                        if(mysqli_num_rows($newsArticles)!=0) {

                            while($newsData=mysqli_fetch_assoc($newsArticles)) {

                                extract($newsData);

                                echo "<a href='{$link_url}' class='news_box'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt=''>
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span><strong>Published on</strong>
                    ".date('d M Y', strtotime($link_published))."
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a>";

        }

        } else {

        echo "<p>No news found</p>";

        }

        } else {

        echo mysqli_error($connection);
        exit();

        }

        ?>
        </div>
    </div>
    </div>
</section>

<section class="newsletter_wrapper">
    <div class="container">
        <div class="row">
            <div class="newsletter">
                <form action="">
                    <h3>Get them in our newsletter</h3>
                    <input type="text" class="input newsletter_input" placeholder="Enter your email to subscribe">
                    <input type="submit" class="btn newsletter_btn" value="Subscriber now">
                </form>
            </div>
        </div>
    </div>
</section>
<?php

    require_once 'footer.php';

?>