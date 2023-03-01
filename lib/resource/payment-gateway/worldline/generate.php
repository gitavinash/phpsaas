<?php

$strNo = rand(1, 10000000000);
$strNo1 = rand(1, 1000000);

$strCurDate = date('d-m-Y');

$debitStartDate = "";
$debitEndDate = "";
$orderId = 'OD' . $order_id;
$userId = ((isset($userId)) ? $userId : 0);
$accountName = "";
$accountNo = (int)"";
$accountIFSC = "";
$accountType = "";
$cardNo = (int)"";
$cardExpiryMonth = (int)"";
$cardExpiryYear = (int)"";
$cardCVV = (int)"";
$maxAmount = (int)"";
$paymentFrequency = ((isset($paymentFrequency)) ? $paymentFrequency : 'ADHO');
$paymentResponseURL = SITE_URL . "/payment/status/";

$datastring = PG_MERCHANT_CODE . "|" . $orderId . "|" . $order_total_price . "|" . $accountNo . "|" . $userId . "|" . $profile_mobile . "|" . $profile_email . "|" . $debitStartDate . "|" . $debitEndDate . "|" . $maxAmount . "|" . $cart_currency . "|" . $paymentFrequency . "|" . $cardNo . "|" . $cardExpiryMonth . "|" . $cardExpiryYear . "|" . $cardCVV . "|" . PG_SALT_KEY;

$hashed = hash('sha512', $datastring);

$data = array("hash" => $hashed, "data" => array(PG_MERCHANT_CODE, $orderId, $order_total_price, $debitStartDate, $debitEndDate, $maxAmount, $cart_currency, $paymentFrequency, $userId, $profile_mobile, $profile_email, $accountNo, $paymentResponseURL, $profile_display_name, PG_SCHEMA, $cart_currency, $accountName, $accountIFSC, $accountType));

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
            'enableNewWindowFlow': true, //for hybrid applications please disable this by passing false
            'enableAbortResponse': true,
            'enableExpressPay': true, //if unique customer identifier is passed then save card functionality for end  end customer
            'enableInstrumentDeRegistration': false, //if unique customer identifier is passed then option to delete saved card by end customer
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
            //possible values 'WEBSH1', 'WEBSH2' and 'WEBMD5'
            //'debitDay':'10',
            'token': "<?php echo $hashed ?>",
            'returnUrl': "<?php echo $paymentResponseURL ?>",
            /*'redirectOnClose': 'https://www.tekprocess.co.in/MerchantIntegrationClient/MerchantResponsePage.jsp',*/
            'responseHandler': handleResponse,
            'paymentMode': 'all',
            'checkoutElement': '',
            'merchantLogoUrl': "<?php echo SITE_LOGO ?>", //provided merchant logo will be displayed
            'merchantId': "<?php echo PG_MERCHANT_CODE ?>",
            'currency': "<?php echo $cart_currency ?>",
            'consumerId': "c<?php echo $userId ?>}", //Your unique consumer identifier to register a eMandate/eNACH
            'consumerMobileNo': "<?php echo $profile_mobile ?>",
            'consumerEmailId': "<?php echo $profile_email ?>",
            'txnId': "<?php echo $orderId ?>", //Unique merchant transaction ID
            'items': [{
                'itemId': "<?php echo PG_SCHEMA ?>",
                'amount': "<?php echo $order_total_price ?>",
                'comAmt': '0'
            }],
            'cartDescription': '}{custname: Avinash Kumar',
            'merRefDetails': [{
                "name": "Txn. Ref. ID",
                "value": "<?php echo $orderId ?>"
            }],
            'customStyle': {
                'PRIMARY_COLOR_CODE': '#3977b7', //merchant primary color code
                'SECONDARY_COLOR_CODE': '#FFFFFF', //provide merchant's suitable color code
                'BUTTON_COLOR_CODE_1': '#1969bb', //merchant's button background color code
                'BUTTON_COLOR_CODE_2': '#FFFFFF' //provide merchant's suitable color code for button text
            },
            'accountNo': "<?php echo $accountNo ?>", //Pass this if accountNo is captured at merchant side for eMandate/eNACH
            'accountHolderName': "<?php echo $accountName ?>", //Pass this if accountHolderName is captured at merchant side for ICICI eMandate & eNACH registration this is mandatory field, if not passed from merchant Customer need to enter in Checkout UI.
            'ifscCode': "<?php echo $accountIFSC ?>", //Pass this if ifscCode is captured at merchant side.
            'accountType': "<?php echo $accountType ?>", //Required for eNACH registration this is mandatory field
            'debitStartDate': "<?php echo $debitStartDate ?>",
            'debitEndDate': "<?php echo $debitEndDate ?>",
            'maxAmount': "<?php echo $maxAmount ?>",
            'amountType': "<?php echo $accountType ?>",
            'frequency': "<?php echo $paymentFrequency ?>" //  Available options DAIL, WEEK, MNTH, QURT, MIAN, YEAR, BIMN and ADHO
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