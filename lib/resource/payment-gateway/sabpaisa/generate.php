<?php

require_once MODULES . 'encryptor.php';

$payerName = 'YUVRAJ MISHRA';
$payerEmail = 'yuvraj.mishra@sabpaisa.in';
$payerMobile = '9990026008';
$payerAddress = 'Patna, Bihar';

$mcc = 5137;
$channelId = 'W';
$callbackUrl = SITE_DOMAIN . '/payment/status/';

$encData = "?clientCode=" . PG_CLIENT_CODE . "&transUserName=" . PG_USERNAME . "&transUserPassword=" . PG_PASSWORD . "&amount=" . $order_total_price .
    "&amountType=" . $cart_currency . "&clientTxnId=OD" . $order_id . "&payerName=" . $payerName . "&payerMobile=" . $payerMobile .
    "&payerEmail=" . $payerEmail . "&mcc=" . $mcc . "&channelId=" . $channelId . "&callbackUrl=" . $callbackUrl;

$AesCipher = new Encryptor();
$data = $AesCipher->encrypt(PG_AUTH_KEY, PG_AUTH_IV, $encData);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to payment gateway</title>
    <?php require_once DEFAULT_META ?>
</head>

<body>

</body onload="document.forms['redirectToPaymentGateway'].submit()">
<p>Redirecting to payment gateway</p>
<form action="https://securepay.sabpaisa.in/SabPaisa/sabPaisaInit?v=1" method="post" id="redirectToPaymentGateway">
    <input type="hidden" name="encData" value="<?php echo $data ?>" id="frm1">
    <input type="hidden" name="clientCode" value="<?php echo PG_CLIENT_CODE ?>" id="frm2">
</form>
<script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#redirectToPaymentGateway').submit();
    });
</script>

</html>