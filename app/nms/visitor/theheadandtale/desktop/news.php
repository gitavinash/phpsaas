<?php

require_once 'header.php';

$newsContent = strip_tags($news_description);
$newsContent =  substr($newsContent, 0, ((SITE_VERSION == 'mobile') ? 111 : 130));
?>
<section class="newsDetails">
    <div class="container container--box">
        <div class="row">
            <div class="col-lg-7">
                <div class="news_img">
                    <figure>
                        <img src="<?php echo $news_image ?>" class="img-fluid" alt="">
                    </figure>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="news_right">
                    <h3><?php echo $news_title ?></h3>
                    <span><?php echo date('d M Y, h:i A', strtotime($link_published)) ?></span>
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
                    <a href="http://twitter.com/share?text=<?php echo urlencode($news_title) ?>&url=<?php echo SITE_URL . $link_url ?>"
                        target="_blank"><img src="<?php echo USER_IMAGES ?>social_media/twitter.png" class="share-icon"
                            alt=""></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo SITE_URL . $link_url ?>&title=<?php echo urlencode($news_title) ?>"
                        target="_blank"><img src="<?php echo USER_IMAGES ?>social_media/linkedin.png" class="share-icon"
                            alt=""></a>
                    <a href="https://wa.me/?text=<?php echo urlencode($news_title) . ' ' . SITE_URL . $link_url ?>"
                        target="_blank"><img src="<?php echo USER_IMAGES ?>social_media/whatsapp.png" class="share-icon"
                            alt=""></a>
                </div>

            </div>
            <div class="details_box">

                <?php

                $showFullNews = false;
                $showSubscriptionSection = false;
                $showSignUpSection = false;

                if (isset($_COOKIE['user_data'])) {
                    if ($activeSubscription == true) {
                        $showFullNews = true;
                    } else {
                        if ($news_paid == 1) {

                            // Check news is paid?

                            if ($checkPurchase = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "order INNER JOIN " . TABLE_PREFIX . "transaction ON transaction_order_id=order_id WHERE order_product_id={$link_relation_id} AND order_status=1 AND order_user_id=" . USER_ID . " AND order_site_id=" . SITE_ID . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID)) {
                                if (mysqli_num_rows($checkPurchase) != 0) {
                                    $showFullNews = true;
                                } else {
                                    $showSubscriptionSection = true;
                                }
                            }
                        } else {
                            $showFullNews = true;
                        }
                    }
                } else {
                    if ($news_paid == 1) {
                        $showSignUpSection = true;
                    } else {
                        $showFullNews = true;
                    }
                }

                echo "<div class='details_box__content {$news_paid} " . (($showFullNews == true) ? '' : 'text-gradient') . "'><p>" . (($showFullNews == true) ? $news_description : $newsContent) . "</p></div>";
                if ($showSignUpSection == true) {
                    echo "
                <div class='transparent-box'>
                </div>
                <section class='subscribe_wrapper'>
                        <div class='container'>
                            <div class='subscribe_wrapper__heading'>
                                <h6><a href='/signup/?refUrl=" . ACTIVE_URL . "&auth=true'>" . ((isset($news_price)) ? (($news_price != 0) ? "Unlock this story now for ₹ {$news_price}" : "Check our subscription plans") : "Check our subscription plans") . "</a></h6>
                                <h3>Stay ahead in fintech leadership with insightful news and ecosystem mastery</h3>
                            </div>
                        </div>
                    </section>";
                }
                if ($showSubscriptionSection == true) { ?>

                    <div class="transparent-box">
                    </div>
                    <section class="subscribe_wrapper">
                        <div class="container">
                            <div class="subscribe_wrapper__heading">
                                <h6><a href="/subscribe/">Subscribe to read the full story</a></h6>
                                <h3>Stay ahead in fintech leadership with insightful news and ecosystem mastery</h3>
                                <p>Access premium journalism by selecting a <a href="#"> Subscription Plan</a></p>
                            </div>

                            <div class="subscription_card">
                                <?php

                                if ($subsriptionPlan = mysqli_query($connection, "SELECT product_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON product_id=inventory_relation_id WHERE product_status=1 AND product_type='subscription' AND product_site_id=" . SITE_ID)) {
                                    if (mysqli_num_rows($subsriptionPlan) != 0) {
                                        while ($subsriptionPlanData = mysqli_fetch_assoc($subsriptionPlan)) {
                                            extract($subsriptionPlanData); ?>

                                            <div class="subscription_card__box">
                                                <p><?php echo $product_title ?></p>
                                                <h6><?php echo $inventory_currency_symbol ?> <?php echo $inventory_price ?></h6>
                                                <div class="continue">
                                                    <?php

                                                    if (isset($_COOKIE['user_data'])) {
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

                                if ($news_price != 0) { ?>
                                    <div class="subscription_card__box">
                                        <p>Unlock Article<span>( One time payment )</span></p>
                                        <h6><?php echo $inventory_currency_symbol ?> <?php echo $news_price ?></h6>
                                        <div class="continue">
                                            <?php

                                            if (isset($_COOKIE['user_data'])) {
                                                echo "
                                                                <form action='/purchase/?order=news' method='post' >
                                                                    <input type='hidden' name='id' value='{$news_id}'>
                                                                    <input type='submit' class='button' value='Unlock now'>
                                                                </form>
                                                            ";
                                            }

                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }

                                ?>

                            </div>
                        </div>
                    </section>

                <?php
                }


                ?>

            </div>

            <?php

            if ($news_owner_id == 9423) { ?>
                <div class="user_content">
                    <p>The author is Founder and Editor of The Head and Tale. She can be reached at
                        <a href="mailto:arti.singh@theheadandtale.com">arti.singh@theheadandtale.com</a><br />
                        Tweets <a href='https://twitter.com/artijourno' target="_blank"
                            style="font-style: italic">@artijourno</a>
                    </p>
                </div>
            <?php
            }

            ?>
            <div class="tags">
                <ul>
                    <?php

                    $tagName = explode(', ', $tagName);

                    for ($i = 0; $i < sizeof($tagName); $i++) {

                        echo "<li><a href='/tag/" . str_replace(' ', '-', $tagName[$i]) . "'>{$tagName[$i]}</a></li>";
                    }

                    ?>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>