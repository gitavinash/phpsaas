<?php
if (!isset($orderId)) {
  echo "<h1>Something went wrong</h1>";
  exit();
}

$orderId = base64_decode($orderId);
$orderData = explode('_', $orderId);
$order_id = $orderData[0];

// Fetch payment status from Cashfree
$cashfreeStatus = getCashfreePaymentStatus($orderId);

$paymentStatus = $cashfreeStatus['order_status'] ?? 'UNKNOWN';
$paymentSessionId = $cashfreeStatus['payment_session_id'] ?? '';

function getCashfreePaymentStatus($orderId)
{
  // Determine API endpoint based on environment
  $baseUrl = (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE == TRUE)
    ? 'https://sandbox.cashfree.com/pg/orders'
    : 'https://api.cashfree.com/pg/orders';

  $url = $baseUrl . "/" . urlencode($orderId);

  if (!defined('PG_APP_ID') || !defined('PG_SECRET_KEY')) {
    error_log("Cashfree configuration missing: PG_APP_ID or PG_SECRET_KEY not defined");
    return null;
  }

  $ch = curl_init($url);

  curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
      "accept: application/json",
      "content-type: application/json",
      "x-api-version: 2023-08-01",
      "x-client-id: " . PG_APP_ID,
      "x-client-secret: " . PG_SECRET_KEY
    ],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $error = curl_error($ch);
  curl_close($ch);

  if ($error) {
    error_log("Cashfree API Error: " . $error);
    return null;
  }

  if ($httpCode !== 200) {
    error_log("Cashfree API HTTP Error: " . $httpCode . " Response: " . $response);
    return null;
  }

  $result = json_decode($response, true);

  if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Cashfree API JSON Decode Error: " . json_last_error_msg());
    return null;
  }

  return $result;
}

$transactionQuery = "SELECT transaction_id, transaction_status, transaction_user_id, transaction_type, order_msg FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id WHERE transaction_id=" . base64_decode($tid) . " AND order_site_id=" . SITE_ID . " AND transaction_site_id=" . SITE_ID;

if ($checkTransaction = mysqli_query($connection, $transactionQuery)) {
  if (mysqli_num_rows($checkTransaction) != 0) {
    extract(mysqli_fetch_assoc($checkTransaction));
    if ($transaction_status == 1) {
      header("Location: /manage-subscribers/");
      exit();
    } else {
      if (strtolower($paymentStatus) == 'paid') {
        $updateQuery = "UPDATE " . TABLE_PREFIX . "transaction SET transaction_status=1 WHERE transaction_id='{$transaction_id}' AND transaction_site_id=" . SITE_ID;
        if (!mysqli_query($connection, $updateQuery)) {
          if (DEVELOPMENT_MODE == true) {
            echo mysqli_error($connection);
          } else {
            echo "Something went wrong";
            exit();
          }
        }

        $addCorpSubscriptionQuery = "INSERT INTO " . TABLE_PREFIX . "corporate_subscription (cp_site_id, cp_admin_user_id, cp_used, cp_limit, cp_expiry) VALUES (" . SITE_ID . ", {$transaction_user_id}, 0, {$order_msg}, DATE_ADD(CURDATE(), INTERVAL 1 YEAR))";

        if (!mysqli_query($connection, $addCorpSubscriptionQuery)) {
          if (DEVELOPMENT_MODE == true) {
            echo mysqli_error($connection);
          } else {
            echo "Something went wrong";
            exit();
          }
        }
      }
    }
  } else {
    if (DEVELOPMENT_MODE == true) {
      echo $transactionQuery;
    } else {
      echo "Invalid transaction found";
    }
    exit();
  }
} else {
  echo mysqli_error($connection);
  echo "Something went wrong";
}

require_once 'header.php';
$isSuccess = (strtolower($paymentStatus) == 'paid');
?>

<style>
  .corp-status-page {
    background: #f8fafc;
    min-height: 80vh;
    padding: 60px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .corp-status-card {
    max-width: 640px;
    width: 100%;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    text-align: center;
  }

  .corp-status-banner {
    padding: 40px 24px 30px;
    background: <?php echo $isSuccess ? 'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)' : 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)'; ?>;
    border-bottom: 1px solid <?php echo $isSuccess ? '#bbf7d0' : '#fecaca'; ?>;
  }

  .status-icon-ring {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: <?php echo $isSuccess ? '#16a34a' : '#dc2626'; ?>;
    color: #ffffff;
    box-shadow: 0 8px 24px <?php echo $isSuccess ? 'rgba(22, 163, 74, 0.25)' : 'rgba(220, 38, 38, 0.25)'; ?>;
  }

  .status-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 8px;
  }

  .status-subtitle {
    font-size: 0.98rem;
    color: #475569;
    max-width: 480px;
    margin: 0 auto;
    line-height: 1.5;
  }

  .corp-status-body {
    padding: 32px 32px 40px;
  }

  .detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 32px;
    background: #f8fafc;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    text-align: left;
  }

  .detail-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .detail-label {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
  }

  .detail-val {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
  }

  .redirect-progress-box {
    margin-bottom: 24px;
    padding: 14px 18px;
    background: #f1f5f9;
    border-radius: 10px;
    font-size: 0.88rem;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .btn-status-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 14px 24px;
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff;
    background: var(--accent-color, #d93025);
    border-radius: 12px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(217, 48, 37, 0.25);
    transition: all 0.2s ease;
  }

  .btn-status-cta:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(217, 48, 37, 0.35);
  }

  @media (max-width: 576px) {
    .detail-grid {
      grid-template-columns: 1fr;
    }

    .corp-status-body {
      padding: 24px 20px 32px;
    }
  }
</style>

<div class="corp-status-page">
  <div class="corp-status-card">
    <div class="corp-status-banner">
      <div class="status-icon-ring">
        <?php if ($isSuccess): ?>
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        <?php else: ?>
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        <?php endif; ?>
      </div>
      <h1 class="status-title"><?php echo $isSuccess ? 'Corporate Membership Confirmed!' : 'Payment Unsuccessful'; ?></h1>
      <p class="status-subtitle">
        <?php echo $isSuccess
          ? 'Thank you for choosing The Head & Tale for your organization. Your corporate subscription seats are ready to allocate.'
          : 'We could not process your transaction. Please try again or contact info@theheadandtale.com for support.'; ?>
      </p>
    </div>

    <div class="corp-status-body">
      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label">Order Reference</span>
          <span class="detail-val">#<?php echo htmlspecialchars($order_id); ?></span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Payment Status</span>
          <span class="detail-val" style="color: <?php echo $isSuccess ? '#16a34a' : '#dc2626'; ?>"><?php echo strtoupper($paymentStatus); ?></span>
        </div>
        <?php if (isset($order_msg) && $order_msg > 0): ?>
          <div class="detail-item">
            <span class="detail-label">Total Licenses</span>
            <span class="detail-val"><?php echo htmlspecialchars($order_msg); ?> Seats</span>
          </div>
        <?php endif; ?>
        <div class="detail-item">
          <span class="detail-label">Billing Cycle</span>
          <span class="detail-val">Annual Access</span>
        </div>
      </div>

      <?php if ($isSuccess): ?>
        <div class="redirect-progress-box">
          <span>Redirecting to Corporate Desk in <strong id="countdownSec">5</strong>s...</span>
          <a href="/manage-subscribers/" style="color: var(--accent-color, #d93025); font-weight: 700; text-decoration: underline;">Skip Wait →</a>
        </div>
        <a href="/manage-subscribers/" class="btn-status-cta">Go to Subscriber Management Desk →</a>

        <script>
          let seconds = 5;
          const timer = setInterval(function() {
            seconds--;
            const el = document.getElementById('countdownSec');
            if (el) el.textContent = seconds;
            if (seconds <= 0) {
              clearInterval(timer);
              window.location.href = '/manage-subscribers/';
            }
          }, 1000);
        </script>
      <?php else: ?>
        <a href="/corporate-subscription/" class="btn-status-cta">Try Corporate Purchase Again →</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
require_once 'footer.php';
