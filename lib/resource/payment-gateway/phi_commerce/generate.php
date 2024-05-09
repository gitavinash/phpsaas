<?php

if ($checkOrderStatus = mysqli_query($connection, "SELECT order_user_id as userId, order_id, transaction_id, transaction_amount, cart_currency, profile_mobile, profile_email, profile_display_name, GROUP_CONCAT({$orderType}_title, ',') as cartProduct FROM " . TABLE_PREFIX . "cart INNER JOIN " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "{$orderType} ON cart_relation_id={$orderType}_id INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=transaction_user_id WHERE transaction_id='{$id}' AND transaction_site_id=" . SITE_ID . " AND order_cart_session_id=cart_session_id")) {
    if (mysqli_num_rows($checkOrderStatus) != 0) {
        extract(mysqli_fetch_assoc($checkOrderStatus));
    } else {
        echo "Invalid transaction request received";
        exit();
    }
} else {
    echo "Something went wrong while trying to fetch transaction details";
    exit();
}

$amount = $transaction_amount;
$currencyCode = 356;
$customerEmailId = PROFILE_EMAIL;
$customerMobile = $profile_mobile;
$customerName = PROFILE_DISPLAY_NAME;
$merchantId = PG_MERCHANT_ID;
$merchantTxnNo = ((DEVELOPMENT_MODE == true) ? time() . $transaction_id : $transaction_id);
$payType = 0;
$returnURL = SITE_URL . "/payment/status/?id=" . base64_encode($order_id) . "&txn_id=" . base64_encode($transaction_id);
$transactionType = 'SALE';
$txnDate = date('YmdHis');

// request values for hash value:
$message = $amount . $currencyCode . $customerEmailId . (($customerMobile != '') ? $customerMobile :
    '') . $customerName . $merchantId . $merchantTxnNo . $payType . $returnURL . $transactionType . $txnDate;
$key = PG_ENCRYPTION_KEY;

// hmac function with sha256 to create hash Value

$hashValue = hash_hmac('sha256', $message, $key, true);
$hashValue = bin2hex($hashValue);
$hashValue = strtolower($hashValue);

$requestBody = json_encode(array(
    "amount" => $amount,
    "currencyCode" => 356,
    "customerEmailID" => $customerEmailId,
    "customerMobileNo" => $customerMobile,
    "customerName" => $customerName,
    "merchantId" => $merchantId,
    "merchantTxnNo" => $merchantTxnNo,
    "payType" => $payType,
    "returnURL" => $returnURL,
    "transactionType" => $transactionType,
    "txnDate" => $txnDate,
    "secureHash" => $hashValue
), true);

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://secure-ptg.payphi.com/pg/api/v2/initiateSale',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $requestBody,
));

$response = curl_exec($curl);
$response = json_decode($response, TRUE);
curl_close($curl);

if ($response['tranCtx'] != '') {
    header("Location: https://secure-ptg.payphi.com/pg/api/v2/authRedirect?tranCtx={$response['tranCtx']}", TRUE, 301);
} else {
    echo $response['responseDescription'];
}
exit();
