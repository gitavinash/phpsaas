<?php

date_default_timezone_set('Asia/Calcutta');
$strCurDate = date('d-m-Y');

if (isset($_SERVER['HTTPS'])) {
    $host = 'https';
} else {
    $host = 'http';
}

$admin_data = file_get_contents("./worldline_AdminData.json");
$mer_array = json_decode($admin_data, true);
?>

<html>

<head>
    <title>Worldline Admin</title>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="<?php echo $host . "://" . $_SERVER["HTTP_HOST"] . '/php-checkoutjs/assets/css/bootstrap.min.css'; ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo $host . "://" . $_SERVER["HTTP_HOST"] . '/php-checkoutjs/assets/js/bootstrap.min.js'; ?>"></script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <img src="./assets/image/worldline.png" alt="worldline" style="width: 400px; padding:0;margin:0;">
                <h2>Worldline Admin Details</h2>

                <?php if (isset($_GET['message']) && $_GET['message'] == 'success') { ?>
                    <div class="alert alert-success">
                        <strong>Admin Details updated successfully !!</strong>
                    </div>
                <?php } ?>

                <form method="POST" id="myform" action="submit_request.php">
                    <table class="table table-bordered table-hover">
                        <tr class="info">
                            <th width="30%">Description</th>
                            <th width="70%">Worldline ePayments is India's leading digital payment solutions company. Being a company with more than 45 years of global payment experience, we are present in India for over 20 years and are powering over 550,000 businesses with our tailored payment solution.</th>
                        </tr>
                        <tr>
                            <td><label>Merchant Code <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="text" name="merchantCode" value="<?php if (isset($mer_array['merchantCode'])) {
                                                                                                        echo $mer_array['merchantCode'];
                                                                                                    } ?>" /></td>
                        </tr>
                        <tr>
                            <td><label>Merchant Scheme Code <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="text" name="merchantSchemeCode" value="<?php if (isset($mer_array['merchantSchemeCode'])) {
                                                                                                                echo $mer_array['merchantSchemeCode'];
                                                                                                            } ?>" /></td>
                        </tr>
                        <tr>
                            <td><label>SALT <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="text" name="salt" value="<?php if (isset($mer_array['salt'])) {
                                                                                                echo $mer_array['salt'];
                                                                                            } ?>" /></td>
                        </tr>
                        <tr>
                            <td><label>Type of Payment <span style="color:red;">*</span></label></td>
                            <td>
                                <select class="form-control" name="typeOfPayment">
                                    <option value="TEST" <?php if (isset($mer_array['typeOfPayment']) && $mer_array['typeOfPayment'] == "TEST") {
                                                                echo 'selected="selected"';
                                                            } ?>>TEST</option>
                                    <option value="LIVE" <?php if (isset($mer_array['typeOfPayment']) && $mer_array['typeOfPayment'] == "LIVE") {
                                                                echo 'selected="selected"';
                                                            } ?>>LIVE</option>
                                </select><br>
                                <p>For TEST mode amount will be charge 1</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Currency</label></td>
                            <td>
                                <select class="form-control" name="currency">
                                    <option value="INR" <?php if (isset($mer_array['currency']) && $mer_array['currency'] == "INR") {
                                                            echo 'selected="selected"';
                                                        } ?>>INR</option>
                                    <option value="USD" <?php if (isset($mer_array['currency']) && $mer_array['currency'] == "USD") {
                                                            echo 'selected="selected"';
                                                        } ?>>USD</option>
                                    <option value="YEN" <?php if (isset($mer_array['currency']) && $mer_array['currency'] == "YEN") {
                                                            echo 'selected="selected"';
                                                        } ?>>YEN</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Primary Color</label></td>
                            <td><input class="form-control" type="text" name="primaryColor" value="<?php if (isset($mer_array['primaryColor'])) {
                                                                                                        echo $mer_array['primaryColor'];
                                                                                                    } ?>" /><br>
                                <p>Color value can be hex, rgb or actual color name</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Secondary Color</label></td>
                            <td><input class="form-control" type="text" name="secondaryColor" value="<?php if (isset($mer_array['secondaryColor'])) {
                                                                                                            echo $mer_array['secondaryColor'];
                                                                                                        } ?>" /><br>
                                <p>Color value can be hex, rgb or actual color name</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Button Color 1</label></td>
                            <td><input class="form-control" type="text" name="buttonColor1" value="<?php if (isset($mer_array['buttonColor1'])) {
                                                                                                        echo $mer_array['buttonColor1'];
                                                                                                    } ?>" /><br>
                                <p>Color value can be hex, rgb or actual color name</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Button Color 2</label></td>
                            <td><input class="form-control" type="text" name="buttonColor2" value="<?php if (isset($mer_array['buttonColor2'])) {
                                                                                                        echo $mer_array['buttonColor2'];
                                                                                                    } ?>" /><br>
                                <p>Color value can be hex, rgb or actual color name</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Logo URL</label></td>
                            <td><input class="form-control" type="text" name="logoURL" value="<?php if (isset($mer_array['logoURL'])) {
                                                                                                    echo $mer_array['logoURL'];
                                                                                                } else{echo "https://www.paynimo.com/CompanyDocs/company-logo-md.png";} ?>" /><br>
                                <p>An absolute URL pointing to a logo image of merchant which will show on checkout popup</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Enable ExpressPay</label></td>
                            <td>
                                <select class="form-control" name="enableExpressPay">
                                    <option value="1" <?php if (isset($mer_array['enableExpressPay']) && $mer_array['enableExpressPay'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['enableExpressPay']) && $mer_array['enableExpressPay'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>To enable saved payments set its value to yes</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Separate Card Mode</label></td>
                            <td>
                                <select class="form-control" name="separateCardMode">
                                    <option value="1" <?php if (isset($mer_array['separateCardMode']) && $mer_array['separateCardMode'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['separateCardMode']) && $mer_array['separateCardMode'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>If this feature is enabled checkout shows two separate payment mode(Credit Card and Debit Card)</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Enable New Window Flow</label></td>
                            <td>
                                <select class="form-control" name="enableNewWindowFlow">
                                    <option value="1" <?php if (isset($mer_array['enableNewWindowFlow']) && $mer_array['enableNewWindowFlow'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['enableNewWindowFlow']) && $mer_array['enableNewWindowFlow'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>If this feature is enabled, then bank page will open in new window</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Merchant Message</label></td>
                            <td><input class="form-control" type="text" name="merchantMessage" value="<?php if (isset($mer_array['merchantMessage'])) {
                                                                                                            echo $mer_array['merchantMessage'];
                                                                                                        } ?>" /></td>
                        </tr>
                        <tr>
                            <td><label>Disclaimer Message</label></td>
                            <td><input class="form-control" type="text" name="disclaimerMessage" value="<?php if (isset($mer_array['disclaimerMessage'])) {
                                                                                                            echo $mer_array['disclaimerMessage'];
                                                                                                        } ?>" /></td>
                        </tr>

                        <tr>
                            <td><label>Payment Mode</label></td>
                            <td>
                                <select class="form-control" name="paymentMode">
                                    <option value="all" selected="selected">all</option>
                                    <option value="cards">cards</option>
                                    <option value="netBanking">netBanking</option>
                                    <option value="UPI">UPI</option>
                                    <option value="imps">imps</option>
                                    <option value="wallets">wallets</option>
                                    <option value="cashCards">cashCards</option>
                                    <option value="NEFTRTGS">NEFTRTGS</option>
                                    <option value="emiBanks">emiBanks</option>
                                </select><br>
                                <p>If Bank selection is at worldline ePayments India Pvt. Ltd. (a Worldline brand) end then select all, if bank selection at Merchant end then pass appropriate mode respective to selected option</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Payment Mode Order</label></td>
                            <td><textarea class="form-control" rows="3" name="paymentModeOrder"><?php if (isset($mer_array['paymentModeOrder'])) {
                                                                                                    echo $mer_array['paymentModeOrder'];
                                                                                                } ?></textarea><br>
                                <p>Please pass order in this format: cards,netBanking,imps,wallets,cashCards,UPI,MVISA,debitPin,NEFTRTGS,emiBanks. Merchant can define their payment mode order</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Enable InstrumentDeRegistration</label></td>
                            <td>
                                <select class="form-control" name="enableInstrumentDeRegistration">
                                    <option value="1" <?php if (isset($mer_array['enableInstrumentDeRegistration']) && $mer_array['enableInstrumentDeRegistration'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['enableInstrumentDeRegistration']) && $mer_array['enableInstrumentDeRegistration'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>If this feature is enabled, you will have an option to delete saved cards</p>
                            </td>
                        </tr>

                        <tr>
                            <td><label>Transaction Type</label></td>
                            <td>
                                <select class="form-control" name="transactionType">
                                    <option value="SALE" <?php if (isset($mer_array['transactionType']) && $mer_array['transactionType'] == 'SALE') {
                                                                echo 'selected="selected"';
                                                            } ?>>SALE</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Hide SavedInstruments</label></td>
                            <td>
                                <select class="form-control" name="hideSavedInstruments">
                                    <option value="1" <?php if (isset($mer_array['hideSavedInstruments']) && $mer_array['hideSavedInstruments'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['hideSavedInstruments']) && $mer_array['hideSavedInstruments'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>If enabled checkout hides saved payment options even in case of enableExpressPay is enabled</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Save Instrument</label></td>
                            <td>
                                <select class="form-control" name="saveInstrument">
                                    <option value="1" <?php if (isset($mer_array['saveInstrument']) && $mer_array['saveInstrument'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['saveInstrument']) && $mer_array['saveInstrument'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to vault instrument</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Display Transaction Message On Popup</label></td>
                            <td>
                                <select class="form-control" name="displayTransactionMessageOnPopup">
                                    <option value="1" <?php if (isset($mer_array['displayTransactionMessageOnPopup']) && $mer_array['displayTransactionMessageOnPopup'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['displayTransactionMessageOnPopup']) && $mer_array['displayTransactionMessageOnPopup'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Embed Payment Gateway On Page</label></td>
                            <td>
                                <select class="form-control" name="embedPaymentGatewayOnPage">
                                    <option value="1" <?php if (isset($mer_array['embedPaymentGatewayOnPage']) && $mer_array['embedPaymentGatewayOnPage'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['embedPaymentGatewayOnPage']) && $mer_array['embedPaymentGatewayOnPage'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Enable Emandate/SI</label></td>
                            <td>
                                <select class="form-control" name="enableEmandate">
                                    <option value="1" <?php if (isset($mer_array['enableEmandate']) && $mer_array['enableEmandate'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['enableEmandate']) && $mer_array['enableEmandate'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable eMandate using this feature</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Hide SI Confirmation</label></td>
                            <td>
                                <select class="form-control" name="hideSIConfirmation">
                                    <option value="1" <?php if (isset($mer_array['hideSIConfirmation']) && $mer_array['hideSIConfirmation'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['hideSIConfirmation']) && $mer_array['hideSIConfirmation'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to hide SI details from the customer</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Expand SI Details</label></td>
                            <td>
                                <select class="form-control" name="expandSIDetails">
                                    <option value="1" <?php if (isset($mer_array['expandSIDetails']) && $mer_array['expandSIDetails'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['expandSIDetails']) && $mer_array['expandSIDetails'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to show eMandate/eNACH/eSign details in expanded mode by default</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Enable Debit Day</label></td>
                            <td>
                                <select class="form-control" name="enableDebitDay">
                                    <option value="1" <?php if (isset($mer_array['enableDebitDay']) && $mer_array['enableDebitDay'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['enableDebitDay']) && $mer_array['enableDebitDay'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to acccept debit day value eMandate/eNACH/eSign registration</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Show SI Response Msg</label></td>
                            <td>
                                <select class="form-control" name="showSIResponseMsg">
                                    <option value="1" <?php if (isset($mer_array['showSIResponseMsg']) && $mer_array['showSIResponseMsg'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['showSIResponseMsg']) && $mer_array['showSIResponseMsg'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to show eMandate/eNACH/eSign registrations details also in final checkout response</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Show SI Confirmation</label></td>
                            <td>
                                <select class="form-control" name="showSIConfirmation">
                                    <option value="1" <?php if (isset($mer_array['showSIConfirmation']) && $mer_array['showSIConfirmation'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['showSIConfirmation']) && $mer_array['showSIConfirmation'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to show confirmation screen for registration</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Enable Txn For NonSI Cards</label></td>
                            <td>
                                <select class="form-control" name="enableTxnForNonSICards">
                                    <option value="1" <?php if (isset($mer_array['enableTxnForNonSICards']) && $mer_array['enableTxnForNonSICards'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['enableTxnForNonSICards']) && $mer_array['enableTxnForNonSICards'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to proceed with a normal transaction with same card details</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Show All Modes with SI</label></td>
                            <td>
                                <select class="form-control" name="showAllModesWithSI">
                                    <option value="1" <?php if (isset($mer_array['showAllModesWithSI']) && $mer_array['showAllModesWithSI'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['showAllModesWithSI']) && $mer_array['showAllModesWithSI'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select><br>
                                <p>Enable this feature to show all modes with SI</p>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Enable SI Details At Merchant End</label></td>
                            <td>
                                <select class="form-control" name="enableSIDetailsAtMerchantEnd">
                                    <option value="1" <?php if (isset($mer_array['enableSIDetailsAtMerchantEnd']) && $mer_array['enableSIDetailsAtMerchantEnd'] == 1) {
                                                            echo 'selected="selected"';
                                                        } ?>>Enable</option>
                                    <option value="0" <?php if (isset($mer_array['enableSIDetailsAtMerchantEnd']) && $mer_array['enableSIDetailsAtMerchantEnd'] == 0) {
                                                            echo 'selected="selected"';
                                                        } ?>>Disable</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td colspan=2>
                                <input class="btn btn-info" type="submit" name="submit" value="Submit" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>


    <!-- <script type="text/javascript" src="https://www.paynimo.com/Paynimocheckout/server/lib/checkout.js"></script> -->


    <!-- <script type="text/javascript">
    $(document).ready(function() {

        $("#btnSubmit").click(function(e){

            e.preventDefault();
            var str = $("#form").serialize();

            alert(str);
            
            $.ajax({
                    type: 'POST',
                    cache: false,
                    data: str,
                    url: "payment_response.php",                                            
                    success: function (response)
                    {
                        var obj = JSON.parse(response);
                        function handleResponse(res)
                        {
                            if (typeof res != 'undefined' && typeof res.paymentMethod != 'undefined' && typeof res.paymentMethod.paymentTransaction != 'undefined' && typeof res.paymentMethod.paymentTransaction.statusCode != 'undefined' && res.paymentMethod.paymentTransaction.statusCode == '0300') {
                        // success block
                        // alert(123);
                            } else if (typeof res != 'undefined' && typeof res.paymentMethod != 'undefined' && typeof res.paymentMethod.paymentTransaction != 'undefined' && typeof res.paymentMethod.paymentTransaction.statusCode != 'undefined' && res.paymentMethod.paymentTransaction.statusCode == '0398') {
                        // initiated block
                        // alert(456);
                            } else {
                        // error block
                        // alert(789);
                            }   
                        };

                        var configJson = 
                        {
                            'tarCall': false,
                            'features': {
                                'showPGResponseMsg': true,
                                'enableNewWindowFlow': true,   //for hybrid applications please disable this by passing false
                                'enableAbortResponse': false,
                                'enableExpressPay': false,  //if unique customer identifier is passed then save card functionality for end  end customer
                                'enableInstrumentDeRegistration': false,  //if unique customer identifier is passed then option to delete saved card by end customer
                                'enableMerTxnDetails': true,                        
                            },
                            
                            'consumerData': {
                                'deviceId': 'WEBSH2',
                                //possible values 'WEBSH1', 'WEBSH2' and 'WEBMD5'
                                //'debitDay':'10',
                                'token': obj['hash'],
                                'returnUrl': obj['data'][12],
                                /*'redirectOnClose': 'https://www.tekprocess.co.in/MerchantIntegrationClient/MerchantResponsePage.jsp',*/
                                'responseHandler': handleResponse,
                                'paymentMode': 'all',
                                'merchantLogoUrl': 'https://www.paynimo.com/CompanyDocs/company-logo-md.png',  //provided merchant logo will be displayed
                                'merchantId': obj['data'][0],
                                'currency': obj['data'][15],
                                'consumerId': obj['data'][8],  //Your unique consumer identifier to register a eMandate/eNACH
                                'consumerMobileNo': obj['data'][9],
                                'consumerEmailId': obj['data'][10],
                                'txnId': obj['data'][1],   //Unique merchant transaction ID
                                'items': [{
                                    'itemId': obj['data'][14],
                                    'amount': obj['data'][2],
                                    'comAmt': '0'
                                }],
                                'cartDescription': '}{custname:'+obj['data'][13],
                                'merRefDetails': [
                                    {"name": "Txn. Ref. ID", "value": obj['data'][1]}
                                ],
                                'customStyle': {
                                    'PRIMARY_COLOR_CODE': '#3977b7',   //merchant primary color code
                                    'SECONDARY_COLOR_CODE': '#FFFFFF',   //provide merchant's suitable color code
                                    'BUTTON_COLOR_CODE_1': '#1969bb',   //merchant's button background color code
                                    'BUTTON_COLOR_CODE_2': '#FFFFFF'   //provide merchant's suitable color code for button text
                                },
                                'accountNo': obj['data'][11],    //Pass this if accountNo is captured at merchant side for eMandate/eNACH
                                //'accountHolderName': 'Name',  //Pass this if accountHolderName is captured at merchant side for ICICI eMandate & eNACH registration this is mandatory field, if not passed from merchant Customer need to enter in Checkout UI.
                                //'ifscCode': 'YESB0000298',        //Pass this if ifscCode is captured at merchant side.
                                //'accountType': 'Saving',  //Required for eNACH registration this is mandatory field
                                'debitStartDate': obj['data'][3],
                                'debitEndDate': obj['data'][4],
                                'maxAmount': obj['data'][5],
                                'amountType': obj['data'][6],
                                'frequency': obj['data'][7]  //  Available options DAIL, WEEK, MNTH, QURT, MIAN, YEAR, BIMN and ADHO
                            }
                        };
                        
                        //console.log(configJson);       

                        $.pnCheckout(configJson);
                        if(configJson.features.enableNewWindowFlow)
                        {
                            pnCheckoutShared.openNewWindow();
                        }
                    }
            });

        });
    });
</script> -->

</body>

</html>