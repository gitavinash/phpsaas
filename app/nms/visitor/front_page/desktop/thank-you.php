<?php

if (!isset($id)) {
  echo "Something went wrong";
  exit();
} else {
  $order_id = $id;
}

$orderType = ((isset($orderType)) ? $orderType : 'subscription');

switch ($orderType) {
  case 'news':
    $transactionQuery
      = "SELECT {$orderType}_id as product_id, {$orderType}_title as product_title, transaction_user_id, transaction_type FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "news ON order_product_id=news_id WHERE transaction_order_id=" . base64_decode($order_id) . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID;
    break;
  default:
    $transactionQuery
      = "SELECT product_id, product_title, transaction_user_id, transaction_type FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "product ON order_product_id=product_id WHERE transaction_order_id=" . base64_decode($order_id) . " AND transaction_status=1 AND transaction_site_id=" . SITE_ID;
    break;
}

if ($checkTransaction = mysqli_query($connection, $transactionQuery)) {
  if (mysqli_num_rows($checkTransaction) != 0) {
    extract(mysqli_fetch_assoc($checkTransaction));

    if (strpos(strtolower($product_title), "months") !== false) {
      $subscriptionDuration = 'months';
      $updateTimeStamp = "subscription_expiry = DATE_ADD(NOW(), INTERVAL 6 MONTH)";
      $updateTimeStampValue = "DATE_ADD(NOW(), INTERVAL 6 MONTH)";
    } else {
      $subscriptionDuration = 'year';
      $updateTimeStamp = "subscription_expiry = DATE_ADD(NOW(), INTERVAL 1 YEAR)";
      $updateTimeStampValue = "DATE_ADD(NOW(), INTERVAL 1 YEAR)";
    }

    if ($orderType != 'news') {
      if (!mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "subscription (
                subscription_site_id, subscription_user_id, subscription_relation_id,
                subscription_mode, subscription_type, subscription_time,
                subscription_expiry, subscription_status
            ) VALUES (
                '" . SITE_ID . "', '{$transaction_user_id}', '{$product_id}',
                '{$subscriptionDuration}', '{$transaction_type}', NOW(),
                {$updateTimeStampValue}, 1
            ) ON DUPLICATE KEY UPDATE
                subscription_relation_id = VALUES(subscription_relation_id),
                subscription_mode = VALUES(subscription_mode),
                subscription_type = VALUES(subscription_type),
                subscription_expiry = VALUES(subscription_expiry),
                subscription_status = VALUES(subscription_status)")) {
        if (DEVELOPMENT_MODE == true) {
          echo "Error updating subscription: " . mysqli_error($connection);
        }
      }
    }
  } else {
    echo "Invalid transaction found";
    exit();
  }
} else {
  echo mysqli_error($connection);
  echo "Something went wrong";
}
require_once 'header.php';

?>



<section class="ty-page-section">
  <div class="container">
    <div class="ty-layout-card">

      <!-- Top Decorative Accent Gradient -->
      <div class="ty-card-banner"></div>

      <!-- Icon & Headline Header -->
      <div class="ty-header">
        <div class="ty-success-icon-wrap">
          <div class="ty-icon-pulse"></div>
          <svg class="ty-check-svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>

        <?php if ($orderType == 'news') { ?>
          <span class="ty-kicker-tag">Article Access Active</span>
          <h1 class="ty-title">Thank You for Your Purchase</h1>
          <p class="ty-subtitle">Your article pass is active. You now have full access to this story and exclusive analysis on The Head and Tale.</p>
        <?php } else { ?>
          <span class="ty-kicker-tag">Executive Membership Active</span>
          <h1 class="ty-title">Thank You for Subscribing</h1>
          <p class="ty-subtitle">Welcome to <strong>The Head &amp; Tale</strong>. Your subscription has been confirmed and your access is fully unlocked.</p>
        <?php } ?>
      </div>

      <!-- Split 2-Column Content Grid -->
      <div class="ty-body-grid">

        <!-- Left: Receipt & Order Summary Box -->
        <div class="ty-col ty-receipt-col">
          <div class="ty-box-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
              <polyline points="14 2 14 8 20 8" />
              <line x1="16" y1="13" x2="8" y2="13" />
              <line x1="16" y1="17" x2="8" y2="17" />
              <polyline points="10 9 9 9 8 9" />
            </svg>
            <h3>Order Summary</h3>
          </div>

          <ul class="ty-receipt-list">
            <?php if (isset($order_id) && !empty($order_id)) { ?>
              <li class="ty-receipt-row">
                <span class="ty-receipt-label">Order Ref</span>
                <span class="ty-receipt-val ty-ref-code">#<?php echo htmlspecialchars(base64_decode($order_id)); ?></span>
              </li>
            <?php } ?>

            <?php if (!empty($product_title)) { ?>
              <li class="ty-receipt-row">
                <span class="ty-receipt-label">Item / Plan</span>
                <span class="ty-receipt-val"><?php echo htmlspecialchars($product_title); ?></span>
              </li>
            <?php } ?>

            <li class="ty-receipt-row">
              <span class="ty-receipt-label">Payment Status</span>
              <span class="ty-status-pill success">
                <span class="ty-dot"></span> Paid &amp; Active
              </span>
            </li>

            <li class="ty-receipt-row">
              <span class="ty-receipt-label">Date</span>
              <span class="ty-receipt-val"><?php echo date('M d, Y'); ?></span>
            </li>
          </ul>
        </div>

        <!-- Right: Unlocked Access & Action Buttons -->
        <div class="ty-col ty-access-col">
          <div class="ty-box-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
              <path d="M7 11V7a5 5 0 0 1 9.9-1" />
            </svg>
            <h3>Unlocked Benefits</h3>
          </div>

          <ul class="ty-features-list">
            <li>
              <span class="ty-feat-icon"><i class="ph ph-check"></i></span>
              <span>Full access to all stories, briefings &amp; archives</span>
            </li>
            <li>
              <span class="ty-feat-icon"><i class="ph ph-check"></i></span>
              <span>Fintech, AI &amp; Startup ecosystem reports</span>
            </li>
          </ul>

          <div class="ty-actions-wrapper">
            <?php if ($orderType == 'news') { ?>
              <a href="/" class="ty-btn-primary">Explore Stories &rarr;</a>
            <?php } else { ?>
              <a href="/" class="ty-btn-primary">Start Reading &rarr;</a>
              <a href="/manage-subscription/" class="ty-btn-secondary">Manage Account</a>
            <?php } ?>
          </div>
        </div>

      </div>

      <!-- Footer Info Bar -->
      <div class="ty-footer-bar">
        <div class="ty-footer-item">
          <i class="ph ph-lock-key"></i>
          <span>Secure Transaction</span>
        </div>
        <div class="ty-footer-item">
          <i class="ph ph-envelope-simple"></i>
          <span>Confirmation email sent</span>
        </div>
        <div class="ty-footer-item">
          <i class="ph ph-question"></i>
          <span>Questions? Contact <a href="mailto:info@theheadandtale.com">info@theheadandtale.com</a></span>
        </div>
      </div>

    </div>
  </div>
</section>
<?php

require_once 'footer.php';

?>