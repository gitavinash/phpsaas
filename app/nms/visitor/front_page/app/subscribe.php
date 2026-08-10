<?php

if (SITE_ID == 234) {
  $redirectURL = HTTP_PROTOCOL . MAIN_DOMAIN . '/manage-subscription/';
  header("Location: {$redirectURL}", true, 301);
  exit;
}

if (DEVELOPMENT_MODE == false) {
  $fetchIPData = file_get_contents("http://ip-api.com/json/" . ((IP == '127.0.0.1') ? '110.235.235.20' : IP) . "?fields=status,message,continent,country,regionName,city,zip,lat,lon,isp,org");
  extract(json_decode($fetchIPData, TRUE));
} else {
  $country = 'india';
}

require_once 'header.php';

if ($activeSubscription == true) {
  if ($subscription_mode == 'year') { ?>
    <script>
      window.location.href = '/manage-subscription/';
    </script>
<?php
  }
}

?>

<section class="category-page" style="padding-bottom: 60px;">
  <!-- Full-Width Subscription Hero Banner -->
  <div class="category-header-hero">
    <div class="container">
      <nav class="article-breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span class="sep">/</span>
        <a href="#">Membership</a>
        <span class="sep">/</span>
        <span class="current">Subscribe</span>
      </nav>

      <div class="category-hero-content">
        <span class="category-hero-kicker">EXECUTIVE MEMBERSHIP</span>
        <h1>Subscribe for Unlimited Intelligence</h1>
        <p class="category-hero-desc">Independent reporting, proprietary market data, and deep-dive ecosystem analysis delivered directly to your desk.</p>
      </div>
    </div>
  </div>

  <div class="container">

    <!-- Pricing Plans Grid -->
    <div class="subscribe-plans-grid">
      <?php
      $subscription_relation_id = ((isset($subscription_relation_id)) ? $subscription_relation_id : 0);

      if ($subsriptionPlan = mysqli_query($connection, "SELECT product_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price, link_is_featured FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=product_id AND link_status=200 AND link_site_id=" . SITE_ID . " AND link_type='product' INNER JOIN " . TABLE_PREFIX . "inventory ON product_id=inventory_relation_id WHERE product_status=1 AND product_category='" . ((strtolower($country) == 'india') ? '231' : '291') . "' AND product_type='subscription' AND product_site_id=" . SITE_ID . " ORDER BY inventory_price DESC")) {
        if (mysqli_num_rows($subsriptionPlan) != 0) {
          while ($subsriptionPlanData = mysqli_fetch_assoc($subsriptionPlan)) {
            extract($subsriptionPlanData);
            if ($product_id != $subscription_relation_id) {
              $priceFormatted = number_format((float)$inventory_price, 2, '.', '');
              $priceParts = explode('.', $priceFormatted);
              $priceMain = number_format((float)$priceParts[0]);
              $priceDec = isset($priceParts[1]) ? '.' . $priceParts[1] : '.00';
              $hasMrp = (isset($inventory_mrp) && (float)$inventory_mrp > (float)$inventory_price);
              $savingsPercent = $hasMrp ? round((((float)$inventory_mrp - (float)$inventory_price) / (float)$inventory_mrp) * 100) : 0;
              $isFeatured = !empty($link_is_featured) || stristr($product_title, 'year');
      ?>
              <div class="subscribe-plan-card <?php echo $isFeatured ? 'featured' : ''; ?>">
                <?php if ($isFeatured) { ?>
                  <span class="subscribe-plan-badge">MOST POPULAR</span>
                <?php } ?>

                <div class="plan-details">
                  <h3 class="plan-name"><?php echo htmlspecialchars($product_title); ?></h3>
                  <div class="plan-price-tag">
                    <?php echo htmlspecialchars($inventory_currency_symbol); ?> <?php echo htmlspecialchars($priceMain); ?><span style="font-size: 1rem; color: var(--text-secondary);"><?php echo htmlspecialchars($priceDec); ?></span>
                  </div>
                  <?php if ($hasMrp) { ?>
                    <div style="font-size: 0.85rem; color: #888; margin-top: 4px;">
                      <s><?php echo htmlspecialchars($inventory_currency_symbol) . htmlspecialchars(number_format((float)$inventory_mrp, 0)); ?></s>
                      <?php if ($savingsPercent > 0) { ?>
                        <span style="color: #10b981; font-weight: 700; margin-left: 6px;"><?php echo $savingsPercent; ?>% OFF</span>
                      <?php } ?>
                    </div>
                  <?php } ?>
                </div>

                <ul style="list-style: none; padding: 0; margin: 24px 0 28px 0; font-size: 0.92rem; color: var(--text-secondary); text-align: left;">
                  <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                    <svg width="18" height="18" fill="#10b981" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    </svg>
                    Unlimited access to all premium stories &amp; archives
                  </li>
                  <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                    <svg width="18" height="18" fill="#10b981" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    </svg>
                    Concise explainers &amp; ecosystem briefings
                  </li>
                  <li style="display: flex; align-items: center; gap: 10px;">
                    <svg width="18" height="18" fill="#10b981" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    </svg>
                    Cancel auto-renewal anytime
                  </li>
                </ul>

                <div>
                  <?php if (isset($_COOKIE['user_data'])): ?>
                    <form action="/subscription/" method="post">
                      <input type="hidden" name="id" value="<?php echo $product_id; ?>">
                      <input type="submit" class="btn <?php echo $isFeatured ? 'btn-primary' : 'btn-outline-secondary'; ?>" style="width: 100%; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; padding: 12px;" value="Select Plan &rarr;">
                    </form>
                  <?php else: ?>
                    <a href="/login/?duration=monthly" class="btn <?php echo $isFeatured ? 'btn-primary' : 'btn-outline-secondary'; ?>" style="display: block; width: 100%; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; padding: 12px; text-align: center;">Continue to Sign In &rarr;</a>
                  <?php endif; ?>
                </div>
              </div>
      <?php
            }
          }
        }
      }
      ?>
    </div>

    <!-- Trust Bar -->
    <div class="subscribe-trust-bar">
      <div class="subscribe-trust-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
          <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
        <span>256-Bit Encrypted Payment</span>
      </div>
      <div class="subscribe-trust-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
        </svg>
        <span>Instant Access Activation</span>
      </div>
      <div class="subscribe-trust-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
          <polyline points="22,6 12,13 2,6" />
        </svg>
        <span>Priority Support: info@theheadandtale.com</span>
      </div>
    </div>

    <!-- FAQ Accordion Section -->
    <div class="subscribe-faq-section">
      <h2 class="subscribe-faq-heading">Frequently Asked Questions</h2>

      <div class="subscribe-faq-item">
        <div class="subscribe-faq-question">
          <span>What do I get with a subscription?</span>
          <span class="subscribe-faq-icon">+</span>
        </div>
        <div class="subscribe-faq-answer">
          <p>Subscribers receive full, unrestricted access to all long-form investigative reports, concise ecosystem explainers, proprietary market data, and complete historical archives without ads or paywalls.</p>
        </div>
      </div>

      <div class="subscribe-faq-item">
        <div class="subscribe-faq-question">
          <span>Can I get a trial before purchasing?</span>
          <span class="subscribe-faq-icon">+</span>
        </div>
        <div class="subscribe-faq-answer">
          <p>We do not offer free trial periods. However, we periodically publish complimentary public briefings. To access subscriber-only stories, choose a plan above.</p>
        </div>
      </div>

      <div class="subscribe-faq-item">
        <div class="subscribe-faq-question">
          <span>Do I need an account to buy a subscription?</span>
          <span class="subscribe-faq-icon">+</span>
        </div>
        <div class="subscribe-faq-answer">
          <p>Yes. You will be prompted to sign in via Google OAuth or Email before completing your payment so your subscription is immediately bound to your member account.</p>
        </div>
      </div>

      <div class="subscribe-faq-item">
        <div class="subscribe-faq-question">
          <span>Can I cancel or disable auto-renewal?</span>
          <span class="subscribe-faq-icon">+</span>
        </div>
        <div class="subscribe-faq-answer">
          <p>Yes. You can disable auto-renewal anytime from your account dashboard under <a href="/manage-subscription/" style="color: var(--accent-color); font-weight: 600;">Manage My Subscription</a>. Your access will remain active through the end of your current paid billing period.</p>
        </div>
      </div>
    </div>

  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.subscribe-faq-item');
    faqItems.forEach(item => {
      const q = item.querySelector('.subscribe-faq-question');
      q.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        faqItems.forEach(el => el.classList.remove('active'));
        if (!isActive) {
          item.classList.add('active');
        }
      });
    });
  });
</script>

<?php require_once 'footer.php'; ?>