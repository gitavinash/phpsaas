<div class="transparent-box"></div>

<section class="subscribe_wrapper">
    <div class="container">
        <div class="subscribe_wrapper__heading">
            <h6><a>Subscribe to read the full story</a></h6>
            <h3>Stay ahead in fintech leadership with insightful news and ecosystem mastery</h3>
            <p>Access premium journalism by selecting a <a href="#">Subscription Plan</a></p>
        </div>

        <div class="subscription_card">
            <?php
            // Step 1: Get user's IP address
            $userIp = (defined('IP') && IP != '127.0.0.1') ? IP : '110.235.235.20';
            // $userIp = (defined('IP') && IP != '127.0.0.1') ? IP : '103.117.168.0'; // US IP address

            // Step 2: Get user's country from IP-API
            $ipApiUrl = "http://ip-api.com/json/{$userIp}?fields=status,country,message";
            $ipResponse = @file_get_contents($ipApiUrl);

            $country = '';
            if ($ipResponse !== false) {
                $ipData = json_decode($ipResponse, true);
                if (isset($ipData['status']) && $ipData['status'] === 'success') {
                    $country = strtolower($ipData['country']);
                }
            }

            // Step 3: Set product category based on country
            $categoryId = ($country === 'india') ? '231' : '291';

            // Step 4: Show subscription plans ONLY if SITE_ID != 234
            if (defined('SITE_ID') && SITE_ID != 234) {
                $query = "
                    SELECT 
                        product_id, 
                        product_title, 
                        inventory_currency_symbol, 
                        inventory_mrp, 
                        inventory_price 
                    FROM " . TABLE_PREFIX . "product 
                    INNER JOIN " . TABLE_PREFIX . "inventory 
                        ON product_id = inventory_relation_id 
                    WHERE 
                        product_status = 1 
                        AND product_category = '$categoryId' 
                        AND product_type = 'subscription' 
                        AND product_site_id IN ( " . SITE_ID . ", 234 )
                    ORDER BY inventory_price DESC
                ";

                $subscriptionPlan = mysqli_query($connection, $query);

                if ($subscriptionPlan && mysqli_num_rows($subscriptionPlan) > 0) {
                    while ($plan = mysqli_fetch_assoc($subscriptionPlan)) {
                        $productId = $plan['product_id'];
                        $productTitle = $plan['product_title'];
                        $currencySymbol = $plan['inventory_currency_symbol'];
                        $price = $plan['inventory_price'];
            ?>
            <div class="subscription_card__box">
                <p><?php echo htmlspecialchars($productTitle); ?></p>
                <h6><?php echo htmlspecialchars($currencySymbol . ' ' . $price); ?></h6>
                <div class="continue">
                    <form action="/subscription/" method="post">
                        <input type="hidden" name="id" value="<?php echo $productId; ?>">
                        <input type="hidden" name="redirect_url" value="<?php echo ACTIVE_URL; ?>">
                        <input type="submit" class="button" value="Continue">
                    </form>
                </div>
            </div>
            <?php
                    }
                }
            }

            // Step 5: Show one-time unlock for all users if applicable
            if (isset($news_price, $news_id) && $news_price > 0) {
                $isIndia = ($country === 'india');
                if ($isIndia) {
                    $displayCurrency = '₹';
                    $displayPrice = $news_price;
                } else {
                    $convertedPrice = round($news_price / 90); // Convert to USD
                    $displayCurrency = '$';
                    $displayPrice = (($convertedPrice < 99) ? '99' : $convertedPrice);
                }
                ?>
            <div class="subscription_card__box">
                <p>Unlock Article <span>(One-time payment)</span></p>
                <h6><?php echo htmlspecialchars($displayCurrency . ' ' . $displayPrice); ?></h6>
                <div class="continue">
                    <form action="/purchase/?order=news" method="post">
                        <input type="hidden" name="id" value="<?php echo $news_id; ?>">
                        <input type="hidden" name="redirect_url" value="<?php echo ACTIVE_URL; ?>">
                        <input type="submit" class="button" value="Unlock now">
                    </form>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>