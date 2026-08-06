<div class="transparent-box"></div>

<section class="paywall-editorial-section">
  <div class="paywall-editorial-card">
    <div class="paywall-editorial-header">
      <span class="paywall-kicker">SUBSCRIBER ACCESS ONLY</span>
      <h2>Subscribe to Continue Reading</h2>
      <p class="paywall-subdeck">Gain unlimited access to in-depth fintech analysis, proprietary market intelligence, and daily executive briefings.</p>
    </div>

    <div class="paywall-plans-container">
      <?php
      // Step 1: Get user's IP address
      $userIp = (defined('IP') && IP != '127.0.0.1') ? IP : '110.235.235.20';

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
      <div class="paywall-editorial-plan">
        <div class="plan-details">
          <h4 class="plan-name"><?php echo htmlspecialchars($productTitle); ?></h4>
          <div class="plan-price-tag"><?php echo htmlspecialchars($currencySymbol . ' ' . number_format($price)); ?></div>
        </div>
        <form action="/subscription/" method="post" class="plan-action-form">
          <input type="hidden" name="id" value="<?php echo $productId; ?>">
          <input type="hidden" name="redirect_url" value="<?php echo ACTIVE_URL; ?>">
          <button type="submit" class="paywall-editorial-btn">Select Plan &amp; Continue &rarr;</button>
        </form>
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
              $displayPrice = number_format($news_price);
          } else {
              $convertedPrice = round($news_price / 90); // Convert to USD
              $displayCurrency = '$';
              $displayPrice = (($convertedPrice < 99) ? '99' : $convertedPrice);
          }
      ?>
      <div class="paywall-editorial-plan single-unlock">
        <div class="plan-details">
          <h4 class="plan-name">Single Article Pass</h4>
          <div class="plan-price-tag"><?php echo htmlspecialchars($displayCurrency . ' ' . $displayPrice); ?></div>
        </div>
        <form action="/purchase/?order=news" method="post" class="plan-action-form">
          <input type="hidden" name="id" value="<?php echo $news_id; ?>">
          <input type="hidden" name="redirect_url" value="<?php echo ACTIVE_URL; ?>">
          <button type="submit" class="paywall-editorial-btn outline">Unlock Article &rarr;</button>
        </form>
      </div>
      <?php } ?>
    </div>
    
    <div class="paywall-footer-note">
      Already a subscriber? <a href="/login/">Sign in to your account</a>
    </div>
  </div>
</section>