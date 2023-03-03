<?php

$amountType = 'F';
$cart_currency = ((isset($cart_currency)) ? $cart_currency : 'INR');
$strCurDate = date('d-m-Y');
$debitStartDate = date('d-m-Y');
$debitEndDate = date('d-m-Y');
$orderId = 'OD' . $order_id;
$userId = ((isset($userId)) ? $userId : 0);
$accountName = "";
$accountNo = "";
$accountIFSC = "";
$accountType = "";
$cardNo = "";
$cardExpiryMonth = "";
$cardExpiryYear = "";
$cardCVV = "";
$maxAmount = $order_total_price;
$paymentFrequency = ((isset($paymentFrequency)) ? $paymentFrequency : 'ADHO');
$paymentResponseURL = SITE_URL . "/payment/status/";

$datastring = PG_MERCHANT_CODE . "|" . $orderId . "|" . $order_total_price . "|" . $accountNo . "|" . $userId . "|" . $profile_mobile . "|" . $profile_email . "|" . $debitStartDate . "|" . $debitEndDate . "|" . $maxAmount . "|" . $amountType . "|" . $paymentFrequency . "|" . $cardNo . "|" . $cardExpiryMonth . "|" . $cardExpiryYear . "|" . $cardCVV . "|" . PG_SALT_KEY;

$hashed = hash('sha512', $datastring);

$data = array("hash" => $hashed, "data" => array(PG_MERCHANT_CODE, $orderId, $order_total_price, $debitStartDate, $debitEndDate, $maxAmount, $amountType, $paymentFrequency, $userId, $profile_mobile, $profile_email, $accountNo, $paymentResponseURL, $profile_display_name, PG_SCHEMA, $cart_currency, $accountName, $accountIFSC, $accountType));

?>


<html>

<head>
    <title>Payment Checkout</title>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1" />
    </script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="worldline_embeded_popup"></div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://www.paynimo.com/Paynimocheckout/server/lib/checkout.js"></script>

    <script type="text/javascript">
    function handleResponse(res) {
        if (typeof res != 'undefined' && typeof res.paymentMethod !=
            'undefined' && typeof res.paymentMethod.paymentTransaction !=
            'undefined' && typeof res.paymentMethod.paymentTransaction
            .statusCode != 'undefined' && res.paymentMethod
            .paymentTransaction.statusCode == '0300') {
            // success block
        } else if (typeof res != 'undefined' && typeof res.paymentMethod !=
            'undefined' && typeof res.paymentMethod.paymentTransaction !=
            'undefined' && typeof res.paymentMethod.paymentTransaction
            .statusCode != 'undefined' && res.paymentMethod
            .paymentTransaction.statusCode == '0398') {
            // initiated block
        } else {
            // error block
        }
    };

    var configJson = {
        'tarCall': false,
        'features': {
            'showPGResponseMsg': true,
            'enableNewWindowFlow': true,
            'enableAbortResponse': true,
            'enableExpressPay': true,
            'enableInstrumentDeRegistration': false,
            'enableMerTxnDetails': true,
            'siDetailsAtMerchantEnd': false,
            'enableSI': false,
            'hideSIDetails': false,
            'enableDebitDay': false,
            'expandSIDetails': false,
            'enableTxnForNonSICards': false,
            'showSIConfirmation': false,
            'showSIResponseMsg': false,
        },
        'consumerData': {
            'deviceId': 'WEBSH2',
            'token': "<?php echo $hashed ?>",
            'returnUrl': "<?php echo $paymentResponseURL ?>",
            'responseHandler': handleResponse,
            'paymentMode': 'all',
            'checkoutElement': '',
            'merchantLogoUrl': "<?php echo SITE_LOGO ?>",
            'merchantId': "<?php echo PG_MERCHANT_CODE ?>",
            'currency': "<?php echo $cart_currency ?>",
            'consumerId': "<?php echo $userId ?>",
            'consumerMobileNo': "<?php echo $profile_mobile ?>",
            'consumerEmailId': "<?php echo $profile_email ?>",
            'txnId': "<?php echo $orderId ?>",
            'items': [{
                'itemId': "<?php echo PG_SCHEMA ?>",
                'amount': "<?php echo $order_total_price ?>",
                'comAmt': '0'
            }],
            'cartDescription': "",
            'merRefDetails': [{
                "name": "Txn. Ref. ID",
                "value": "<?php echo $orderId ?>"
            }],
            'customStyle': {
                'PRIMARY_COLOR_CODE': '#3977b7',
                'SECONDARY_COLOR_CODE': '#FFFFFF',
                'BUTTON_COLOR_CODE_1': '#1969bb',
                'BUTTON_COLOR_CODE_2': '#FFFFFF'
            },
            'accountNo': "<?php echo $accountNo ?>",
            'accountHolderName': "<?php echo $accountName ?>",
            'ifscCode': "<?php echo $accountIFSC ?>",
            'accountType': "<?php echo $accountType ?>",
            'debitStartDate': "<?php echo $debitStartDate ?>",
            'debitEndDate': "<?php echo $debitEndDate ?>",
            'maxAmount': "<?php echo $maxAmount ?>",
            'amountType': "<?php echo $amountType ?>",
            'frequency': "<?php echo $paymentFrequency ?>"
        }
    };

    console.log(configJson);

    $.pnCheckout(configJson);
    if (configJson.features.enableNewWindowFlow) {
        pnCheckoutShared.openNewWindow();
    };
    </script>

</body>

</html>