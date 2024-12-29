<?php

require_once 'header.php';

?>
<section>
    <div class="row">
        <div class="container">
            <div class="left-container">
                <div class="categoryHeading">
                    <h1><?php echo $category_title ?></h1>
                </div>
                <?php

                for ($i = 0; $i < 100; $i++) {
                    if (isset($categorySinglePost[$i])) {
                        extract($categorySinglePost[$i]);
                        echo "<div class='news_box'><a href='{$link_url}'>
                                        <div class='feature_image'>
                                            <img src='{$news_image}' alt='{$news_title}'>
                                            " . (($news_paid == 1) ? "<img src='" . USER_IMAGES . "premium.png' />" : "") . "
        </div>
        <div class='news_intro'>
            <h2>{$news_title}</h2>
            <p><span>
                    " . date('d M Y, H:i A', strtotime($publishedTime)) . "
            </span></p>
            <p>{$meta_description}</p>
        </div>
        </a></div>";
                    }
                }
                ?>
                <?php

                require_once 'pagination.php';

                ?>
            </div>
            <?php

            require_once 'sidebar.php';

            ?>
        </div>
    </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>