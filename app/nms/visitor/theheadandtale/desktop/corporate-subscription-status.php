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

    // Verify required configuration constants exist
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

$transactionQuery
    = "SELECT transaction_id, transaction_status, transaction_user_id, transaction_type, order_msg FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id WHERE transaction_order_id=" . $order_id . " AND transaction_payment_id='" . $paymentSessionId . "' AND order_site_id=" . SITE_ID . "  AND transaction_site_id=" . SITE_ID;

if ($checkTransaction = mysqli_query($connection, $transactionQuery)) {
    if (mysqli_num_rows($checkTransaction) != 0) {
        extract(mysqli_fetch_assoc($checkTransaction));
        if ($paymentStatus == 'PAID') {
            echo "UPDATE " . TABLE_PREFIX . "transaction SET transaction_status=1 WHERE transaction_id='{$transaction_id}' AND transaction_site_id=" . SITE_ID;
            exit();
            // Update transaction status to completed
            mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_status=3 WHERE transaction_id='{$transaction_id}' AND transaction_site_id=" . SITE_ID);
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

<section class="thankyou_wrapper">
    <div class="container">
        <div class="thankyou_wrapper__content" style="margin-bottom: 200px">
            <?php

            if (strtolower($paymentStatus) == 'paid') { ?>
            <h1>Thank You for Purchase!</h1>
            <p>Thank you for choosing to read an article from The Head and Tale. We hope you enjoyed it!</p>
            <p>We’re glad to have you with us and invite you to explore more of our thought-provoking content, exclusive
                news, and in-depth reports. If you have any questions, feedback, or suggestions, please feel free to
                contact us at <a href="mailto: info@theheadandtale.com">info@theheadandtale.com</a>.</p>
            <?php
            } else { ?>
            <h1>Payment Failed</h1>
            <p>Unfortunately, your payment could not be processed.</p>
            <p>Please try again or contact our support team at <a
                    href="mailto:info@theheadandtale.com">info@theheadandtale.com</a> for assistance.</p>
            <?php
            }

            ?>
        </div>
    </div>
</section>

<?php
require_once 'footer.php';