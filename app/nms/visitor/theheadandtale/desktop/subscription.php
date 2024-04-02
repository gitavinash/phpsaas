<?php

    $orderNumber = mt_rand(10000, 99999);
    function hmac_sha256($msg, $key) {
        return hash_hmac('sha256', $msg, $key);
    }

    $amount=(($duration=='monthly') ? '899' : '3299');
    $currencyCode=356;
    $merchantId = 'T_00738';
    $merchantTxnNo = 'THT_'.$orderNumber;
    $payType=0;
    $returnURL=SITE_URL.'/payment-response/';
    $transactionType='SALE';
    $txnDate=date('YmdHis');

    // Example usage:
    $message = $amount.$currencyCode.PROFILE_EMAIL.$merchantId.$merchantTxnNo.$payType.$returnURL.$transactionType.$txnDate;
    $key = "abc256";

    $hash = hmac_sha256($message, $key);
    $hashValue = bin2hex($hash);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment request processing | <?php echo SITE_NAME ?></title>
</head>

<body>
    <form id='myForm' name='myForm' action='https://qa.phicommerce.com/pg/api/sale' method='post'
        enctype="application/x-www-form-urlencoded">
        <input type="hidden" name="merchantID" value="<?php echo $merchantId ?>" />
        <input type="hidden" name="merchantTxnNo" value="<?php echo $merchantTxnNo ?>" />
        <input type="hidden" name="amount" value="<?php echo $amount ?>" />
        <input type="hidden" name="currencyCode" value="<?php echo $currencyCode ?>" />
        <input type="hidden" name="payType" value="<?php echo $payType ?>" />
        <input type="hidden" name="customerEmailId" value="<?php echo PROFILE_EMAIL ?>" />
        <input type="hidden" name="transactionType" value="<?php echo $transactionType ?>" />
        <input type="hidden" name="returnURL" value="<?php echo $returnURL ?>" />
        <input type="hidden" name="txnDate" value="<?php echo $txnDate ?>" />
        <input type="hidden" name="secureHash" value="<?php echo $hashValue ?>" />
    </form>
    <script>
    window.onload = function() {
        document.getElementById("myForm").submit();
    };
    </script>
</body>

</html>