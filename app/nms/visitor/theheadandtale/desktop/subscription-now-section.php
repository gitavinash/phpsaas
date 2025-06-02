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

            $fetchIPData = file_get_contents("http://ip-api.com/json/" . ((IP == '127.0.0.1') ? '110.235.235.20' : IP) . "?fields=status,message,continent,country,regionName,city,zip,lat,lon,isp,org");

            extract(json_decode($fetchIPData, TRUE));


            if ($subsriptionPlan = mysqli_query($connection, "SELECT product_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON product_id=inventory_relation_id WHERE product_status=1 AND product_category='" . ((strtolower($country) == 'india') ? '231' : '291') . "' AND product_type='subscription' AND product_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($subsriptionPlan) != 0) {
                    while ($subsriptionPlanData = mysqli_fetch_assoc($subsriptionPlan)) {
                        extract($subsriptionPlanData); ?>

            <div class="subscription_card__box">
                <p><?php echo $product_title ?></p>
                <h6><?php echo $inventory_currency_symbol ?> <?php echo $inventory_price ?></h6>
                <div class="continue">
                    <?php

                                // if (isset($_COOKIE['user_data'])) {
                                //     echo "
                                //                                 <form action='/subscription/' method='post' >
                                //                                     <input type='hidden' name='id' value='{$product_id}'>
                                //                                     <input type='hidden' name='redirect_url' value='" . ACTIVE_URL . "'>
                                //                                     <input type='submit' class='button' value='continue'>
                                //                                 </form>
                                //                             ";
                                // } else {
                                //     echo "<a href='/login/?duration=monthly' class='button'>continue</a>";
                                // }

                                echo "
                                                                <form action='/subscription/' method='post' >
                                                                    <input type='hidden' name='id' value='{$product_id}'>
                                                                    <input type='hidden' name='redirect_url' value='" . ACTIVE_URL . "'>
                                                                    <input type='submit' class='button' value='continue'>
                                                                </form>
                                                            ";

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

                        // if (isset($_COOKIE['user_data'])) {
                        //     echo "
                        //                                         <form action='/purchase/?order=news' method='post' >
                        //                                             <input type='hidden' name='id' value='{$news_id}'>
                        //                                             <input type='hidden' name='redirect_url' value='" . ACTIVE_URL . "'>
                        //                                             <input type='submit' class='button' value='Unlock now'>
                        //                                         </form>
                        //                                     ";
                        // }

                        echo "
                                                                <form action='/purchase/?order=news' method='post' >
                                                                    <input type='hidden' name='id' value='{$news_id}'>
                                                                    <input type='hidden' name='redirect_url' value='" . ACTIVE_URL . "'>
                                                                    <input type='submit' class='button' value='Unlock now'>
                                                                </form>
                                                            ";

                        ?>
                </div>
            </div>
            <?php
            }

            ?>

        </div>
    </div>
</section>