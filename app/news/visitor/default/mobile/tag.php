<?php

    require_once 'header.php';

?>

    <!-- Advertisment Box -->
                                        
    <section class="in-article-ad-box" ></section>

    <section class="bgD01F29 no-padding" >

        <!-- Category Name -->

        <h1 class="category-title"><?php echo $tag_title ?></h1>

    </section>

    <section>

        <div class="categoryBox">

            <?php

                $j=1;
            
                for($i=0; $i<(sizeof($tagPost)); $i++) {

                    extract($tagPost[$i]);

                    echo "
                        <div class='post-box'>
                            <a href='{$link_url}'>
                                <div class='text-box'>
                                    <span>{$tag_title}</span>
                                    <h2>{$news_title}</h2>
                                </div>
                                <div class='img-box'>";
                                    if($i==2) {
                                        echo "<span><img src='".USER_IMAGES."live-icon.gif' class='live' /></span>";
                                    }
                                    echo "<img src='{$news_image}' class='preview' alt='{$news_title}'>
                                </div>
                            </a>
                        </div>";

                    if($j==5) { ?>

                        <!-- Advertisment Box -->
                                
                        <section class="in-article-ad-box" ></section>

                        <?php

                        $j=1;

                    }

                    $j++;

                }

            ?>
        </div>

    </section>

<?php

    require_once 'footer.php';

?>