<?php

    require_once 'header.php';

?>

    <!-- Advertisment Box -->
                                    
    <section class="in-article-ad-box" ></section>

    <section class="bgD01F29 no-padding" >

        <!-- Category Name -->

        <h1 class="category-title"><?php echo $category_title ?></h1>

    </section>

    <section>

        <div class="categoryBox">

            <?php

                $j=1;
            
                for($i=0; $i<(sizeof($categorySinglePost)); $i++) {

                    extract($categorySinglePost[$i]);

                    echo "
                        <div class='post-box'>
                            <a href='/amp{$link_url}'>
                                <div class='text-box'>
                                    <span>{$category_title}</span>
                                    <h2>{$news_title}</h2>
                                </div>
                                <div class='img-box'>";
                                    if($i==2) {
                                        echo "<span><amp-img src='".USER_IMAGES."live-icon.gif' class='live'  layout='fixed' width='40' height='13' alt='LIVE : {$news_title}' ></amp-img></span>";
                                    }
                                    echo "<amp-img src='{$news_image}' class='preview' layout='responsive' width='500' height='300' alt='{$news_title}'></amp-img>
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