<div class="sidebar__item">
                    <div class="sidebar__about__item">
                        <div class="sidebar__item__title">
                            <h6>About us</h6>
                        </div>
                        <img src="<?php echo SITE_LOGO ?>" alt="">
                        <h6>We are <span><?php echo SITE_NAME ?></span></h6>
                        <p><?php echo SITE_DESC ?></p>
                        <a href="/about-us/" class="primary-btn">Read more</a>
                    </div>
                    <div class="sidebar__follow__item">
                        <div class="sidebar__item__title">
                            <h6>Follow me</h6>
                        </div>
                        <div class="sidebar__item__follow__links">
                            <a href="https://www.facebook.com/helloajnabi"><i class="fab fa-facebook"></i></a>
                            <a href="https://twitter.com/Hello_Ajnabi"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.youtube.com/channel/UC507HwLRcMaAqUNFTzSEK3w?sub_confirmation=1"><i class="fab fa-youtube"></i></a>
                            <a href="https://www.instagram.com/helloajnabi"><i class="fab fa-instagram"></i></a>
                            <a href="mailto:info@helloajnabi.com"><i class="fas fa-envelope-open"></i></a>
                        </div>
                    </div>
                    <div class="sidebar__feature__item">
                        <div class="sidebar__item__title">
                            <h6>Feature Posts</h6>
                        </div>

                        <?php
			
                                        // Feature post slider

                                        if($featurePostSlider=mysqli_query($connection, "SELECT link_url, post_title, post_image, category_title, link_published FROM ".TABLE_PREFIX."post INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=post_id INNER JOIN ".TABLE_PREFIX."category ON category_id=post_category WHERE link_type='post' AND link_is_featured=1 AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY link_published DESC LIMIT 0, 5")) {

                                            if(mysqli_num_rows($featurePostSlider)!=0) {

                                                $i=0;

                                                while($featurePostSliderData=mysqli_fetch_assoc($featurePostSlider)) {

                                                    extract($featurePostSliderData);
                                                    
                                                    if($i==0) { ?>

                                                        <div class="sidebar__feature__item__large set-bg" data-setbg="img/sidebar/feature-post.jpg" style="background-image: url(<?php echo $post_image ?>);">
                                                            <div class="sidebar__feature__item__large--text">
                                                                <span><?php echo $category_title ?></span>
                                                                <h5><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a>
                                                                </h5>
                                                            </div>
                                                        </div>

                                                    <?php
                                                    } else {

                                                        if($i==1) {

                                                            echo '<div class="sidebar__feature__item__list">';

                                                        } ?>

                                                        <div class="sidebar__feature__item__list__single">
                                                                <div class="post__meta">
                                                                    <h4><?php echo date('d', strtotime($link_published)) ?></h4>
                                                                    <span><?php echo date('M', strtotime($link_published)) ?></span>
                                                                </div>
                                                                <div class="post__text">
                                                                    <span><?php echo $tag_title ?></span>
                                                                    <h5><a href="<?php echo $link_url ?>"><?php echo $post_title ?></a></h5>
                                                                </div>
                                                            </div>

                                                        <?php
                                                    }
                                                    
                                                }

                                            }

                                        }

                                    ?>
                                </div>
                                <div class="sidebar__item__banner">
                                    <img src="img/sidebar/banner.jpg" alt="">
                                </div>
                                <div class="sidebar__item__categories">
                                    <div class="sidebar__item__title">
                                        <h6>Categories</h6>
                                    </div>
                                    <ul>
                                        <?php
                                        
                                            if($categoryList=mysqli_query($connection, "SELECT category_title, link_url FROM ".TABLE_PREFIX."link INNER JOIN ".TABLE_PREFIX."category ON category_id=link_relation_id WHERE link_type='category' AND link_status=200 AND link_site_id=".SITE_ID." ORDER BY category_title ASC")) {

                                                if(mysqli_num_rows($categoryList)!=0) {

                                                    while($categoryListData=mysqli_fetch_assoc($categoryList)) {

                                                        echo "<li><a href='{$categoryListData['link_url']}'>{$categoryListData['category_title']} <span></span></a></li>";

                                                    }

                                                }

                                            } else {

                                                echo mysqli_error($connection);

                                            }

                                        ?>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>