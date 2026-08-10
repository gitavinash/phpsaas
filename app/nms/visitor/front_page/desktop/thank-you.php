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

<style>
  /* Dedicated Thank You Page Component Styles */
  .ty-page-section {
    padding: 60px 0 90px 0;
    min-height: calc(100vh - 380px);
    background-color: var(--bg-color, #fdfbf7);
    background-image:
      radial-gradient(circle at 50% 10%, rgba(217, 48, 37, 0.03) 0%, transparent 60%),
      radial-gradient(circle at 10% 90%, rgba(16, 185, 129, 0.03) 0%, transparent 50%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-body, "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif);
  }

  .ty-layout-card {
    width: 100%;
    max-width: 860px;
    margin: 0 auto;
    background: var(--white, #ffffff);
    border: 1px solid var(--border-color, #e0e0e0);
    border-radius: 16px;
    box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.06), 0 4px 12px rgba(0, 0, 0, 0.02);
    position: relative;
    overflow: hidden;
    box-sizing: border-box;
  }

  .ty-card-banner {
    height: 5px;
    background: var(--accent-color);
    /* background: linear-gradient(90deg, #d93025 0%, #f59e0b 50%, #10b981 100%); */
  }

  .ty-header {
    padding: 44px 40px 32px 40px;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .ty-success-icon-wrap {
    position: relative;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #ecfdf5;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    border: 2px solid #a7f3d0;
  }

  .ty-icon-pulse {
    position: absolute;
    top: -6px;
    left: -6px;
    right: -6px;
    bottom: -6px;
    border-radius: 50%;
    border: 2px solid rgba(16, 185, 129, 0.25);
    animation: tyPulseRing 2.5s infinite cubic-bezier(0.4, 0, 0.6, 1);
  }

  @keyframes tyPulseRing {
    0% {
      transform: scale(0.95);
      opacity: 0.8;
    }

    50% {
      transform: scale(1.1);
      opacity: 0.2;
    }

    100% {
      transform: scale(0.95);
      opacity: 0.8;
    }
  }

  .ty-kicker-tag {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
    padding: 3px 12px;
    border-radius: 4px;
    margin-bottom: 12px;
  }

  .ty-title {
    font-family: var(--font-heading, "Playfair Display", Georgia, serif);
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--primary-color, #0a0a0a);
    margin-bottom: 10px;
    line-height: 1.2;
    letter-spacing: -0.5px;
  }

  .ty-subtitle {
    font-size: 1.05rem;
    color: var(--text-secondary, #555555);
    max-width: 580px;
    line-height: 1.55;
    margin: 0;
  }

  .ty-body-grid {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 0;
  }

  .ty-col {
    padding: 32px 36px;
    box-sizing: border-box;
  }

  .ty-receipt-col {
    background: #f8fafc;
    border-right: 1px solid #f1f5f9;
  }

  .ty-box-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    color: var(--primary-color, #0a0a0a);
  }

  .ty-box-header h3 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    font-family: var(--font-body, "Inter", sans-serif);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #0a0a0a;
  }

  .ty-box-header svg {
    color: #64748b;
  }

  .ty-receipt-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .ty-receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 0.92rem;
  }

  .ty-receipt-row:last-child {
    border-bottom: none;
  }

  .ty-receipt-label {
    color: #64748b;
    font-weight: 500;
  }

  .ty-receipt-val {
    font-weight: 600;
    color: var(--primary-color, #0a0a0a);
  }

  .ty-receipt-val.ty-ref-code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: var(--accent-color, #d93025);
    background: #e2e8f0;
    padding: 2px 8px;
    border-radius: 4px;
  }

  .ty-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 12px;
  }

  .ty-status-pill.success {
    background: #dcfce7;
    color: #15803d;
  }

  .ty-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #16a34a;
  }

  .ty-access-col {
    background: var(--white, #ffffff);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .ty-features-list {
    list-style: none;
    padding: 0;
    margin: 0 0 24px 0;
  }

  .ty-features-list li {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.92rem;
    color: var(--text-secondary, #555555);
    margin-bottom: 12px;
  }

  .ty-feat-icon {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ecfdf5;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
  }

  .ty-actions-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .ty-btn-primary {
    display: block;
    width: 100%;
    text-align: center;
    padding: 13px 20px;
    background: var(--primary-color, #0a0a0a);
    color: var(--white, #ffffff) !important;
    font-weight: 700;
    font-size: 0.9rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(10, 10, 10, 0.12);
    box-sizing: border-box;
  }

  .ty-btn-primary:hover {
    background: var(--accent-color, #d93025);
    color: var(--white, #ffffff) !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(217, 48, 37, 0.2);
  }

  .ty-btn-secondary {
    display: block;
    width: 100%;
    text-align: center;
    padding: 12px 20px;
    background: transparent;
    color: var(--primary-color, #0a0a0a) !important;
    border: 1px solid var(--border-color, #e0e0e0);
    font-weight: 600;
    font-size: 0.9rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
    box-sizing: border-box;
  }

  .ty-btn-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--primary-color, #0a0a0a) !important;
  }

  .ty-footer-bar {
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    padding: 16px 36px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 0.82rem;
    color: #64748b;
  }

  .ty-footer-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .ty-footer-item i {
    font-size: 1rem;
    color: #94a3b8;
  }

  .ty-footer-item a {
    color: var(--accent-color, #d93025);
    text-decoration: underline;
    font-weight: 500;
  }

  @media (max-width: 768px) {
    .ty-body-grid {
      grid-template-columns: 1fr;
    }

    .ty-receipt-col {
      border-right: none;
      border-bottom: 1px solid #f1f5f9;
    }

    .ty-header {
      padding: 32px 20px 24px 20px;
    }

    .ty-title {
      font-size: 1.75rem;
    }

    .ty-col {
      padding: 24px 20px;
    }

    .ty-footer-bar {
      padding: 16px 20px;
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>

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