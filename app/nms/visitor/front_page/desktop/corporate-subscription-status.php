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

<div class="corp-status-page">
  <div class="corp-status-card">
    <div class="corp-status-banner <?php echo $isSuccess ? 'success' : 'error'; ?>">
      <div class="status-icon-ring <?php echo $isSuccess ? 'success' : 'error'; ?>">
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
          ? 'Thank you for choosing ' . SITE_NAME . ' for your organization. Your corporate subscription seats are ready to allocate.'
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
          <span class="detail-val <?php echo $isSuccess ? 'text-success' : 'text-danger'; ?>"><?php echo strtoupper($paymentStatus); ?></span>
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
          <a href="/manage-subscribers/" class="corp-status-skip-link">Skip Wait →</a>
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
