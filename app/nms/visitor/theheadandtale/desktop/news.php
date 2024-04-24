<?php

    require_once 'header.php';

?>
<section class="newsDetails">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="news_img">
                    <img src="<?php echo $news_image ?>" class="img-fluid" alt="">
                </div>
            </div>
            <div class="col-lg-5">
                <div class="news_right">
                    <h3><?php echo $news_title ?></h3>
                    <span><?php echo date('M d, Y', strtotime($link_published)) ?></span>
                    <p><?php echo $meta_description ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="news">
    <div class="container">
        <div class="news_box__container">
            <div class="news_box__heading">
                <div class="news_box__heading--left">
                    <h6><?php echo $profile_display_name ?></h6>
                </div>
                <div class="social_link">
                    <p>Share the story on</p>
                    <a href="http://twitter.com/share?text=<?php echo $news_title ?>&url=<?php echo $link_url ?>"
                        target="_blank"><i class='bx bxl-twitter'></i></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $link_url ?>&title=<?php echo $news_title ?>"
                        target="_blank"><i class='bx bxl-linkedin' style="color: #0b65c2"></i></a>
                    <a href="https://wa.me/?text=<?php echo $news_title.' '.$link_url ?>" target="_blank"><i
                            class='bx bxl-whatsapp' style="color: #01e776"></i></a>
                </div>

            </div>
            <div class="details_box">
                <?php
                
                   if(!isset($_COOKIE['user_name'])) { ?>

                <div class="details_box__content">
                    <p><?php
                        $pos=strpos($news_description, ' ', 380);
                        echo substr($news_description,0, $pos ).'...';
                    ?></p>
                </div>

                <section class="subscribe_wrapper">
                    <div class="container">
                        <div class="subscribe_wrapper__heading">
                            <h6><a href="/signup/">Sign up now to read the full story</a></h6>
                            <h3>Stay ahead in fintech leadership with insightful news and ecosystem mastery</h3>
                            <!-- <p><a href="/register/"> Sign up now</a></p> -->
                        </div>

                        <!-- <div class="subscription_card">
                            <div class="subscription_card__box">
                                <p>1-month</p>
                                <h6>INR 799</h6>
                                <div class="continue">
                                    <?php
                                    
                                        echo "<a href='/login/?duration=monthly' class='button'>continue</a>";

                                    ?>
                                </div>
                            </div>
                            <div class="subscription_card__box">
                                <p>1-Year (Annual)</p>
                                <h6>INR 3299</h6>
                                <div class="continue">
                                    <?php
                                    
                                            echo "<a href='/login/?duration=yearly' class='button'>continue</a>";

                                    ?>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </section>

                <?php
                   } else {
                    if($news_paid==1) {

                        if($activeSubscription==true) { ?>

                <div class="details_box__content">
                    <p><?php echo $news_description ?></p>
                </div>

                <?php
                        } else { ?>

                <div class="details_box__content">
                    <p><?php
                        $pos=strpos($news_description, ' ', 100);
                        echo substr($news_description,0, $pos );
                    ?></p>
                </div>
                <section class="subscribe_wrapper">
                    <div class="container">
                        <div class="subscribe_wrapper__heading">
                            <h6>Subscribe to read the full story </h6>
                            <h3>Stay ahead in fintech leadership with insightful news and ecosystem mastery</h3>
                            <p>Access premium journalism by selecting a <a href="#"> Subscription Plan</a></p>
                        </div>

                        <div class="subscription_card">
                            <?php
           
            if($subsriptionPlan=mysqli_query($connection, "SELECT product_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price FROM ".TABLE_PREFIX."product INNER JOIN ".TABLE_PREFIX."inventory ON product_id=inventory_relation_id WHERE product_status=1 AND product_type='subscription' AND product_site_id=".SITE_ID)) {
                if(mysqli_num_rows($subsriptionPlan)!=0) {
                    while($subsriptionPlanData=mysqli_fetch_assoc($subsriptionPlan)) {
                        extract($subsriptionPlanData); ?>

                            <div class="subscription_card__box">
                                <p><?php echo $product_title ?></p>
                                <h6><?php echo $inventory_currency_symbol ?> <?php echo $inventory_price ?></h6>
                                <div class="continue">
                                    <?php
                                                    
                                                        if(isset($_COOKIE['user_data'])) {
                                                            echo "
                                                                <form action='/subscription/' method='post' >
                                                                    <input type='hidden' name='id' value='{$product_id}'>
                                                                    <input type='submit' class='button' value='continue'>
                                                                </form>
                                                            ";
                                                        } else {
                                                            echo "<a href='/login/?duration=monthly' class='button'>continue</a>";
                                                        }

                                                    ?>
                                </div>
                            </div>

                            <?php
                    }
                }
            }

           ?>
                        </div>
                    </div>
                </section>
                <?php
                }

                    } else { ?>

                <div class="details_box__content">
                    <p><?php echo $news_description ?></p>
                </div>

                <?php
                    }
                   }

                ?>
            </div>
            <?php
            
                if(isset($_COOKIE['user_data'])) { ?>
            <div class="user_content">
                <p>The author is the Founder and Editor of The Head and Tale. She can be reached at <a
                        href="mailto:<?php echo $profile_email ?>"><?php echo $profile_email ?></a></p>
            </div>
            <?php
                }

            ?>
        </div>
    </div>
</section>
<?php

    require_once 'footer.php';

?>