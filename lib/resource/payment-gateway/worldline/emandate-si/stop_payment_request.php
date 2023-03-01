<?php 

$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http' ;
$base_url .= '://'.$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

if(isset($_POST))
{
    $transactionIdentifier = str_shuffle("0123456789");
    $admin_data = file_get_contents("../worldline_AdminData.json");
    $mer_array = json_decode($admin_data, true);

  	$arr_req = array(
  	    "merchant"=> [
        "webhookEndpointURL"=> "",
        "responseType"=> "",
        "responseEndpointURL"=> "",
        "description"=> "",
        "identifier"=> $mer_array['merchantCode'],
        "webhookType"=> ""
      ],
      "cart"=> [
        "item"=> [
          [
            "description"=> "",
            "providerIdentifier"=> "",
            "surchargeOrDiscountAmount"=> "",
            "amount"=> "",
            "comAmt"=> "",
            "sKU"=> "",
            "reference"=> "",
            "identifier"=> ""
          ]
        ],
        "reference"=> "",
        "identifier"=> "",
        "description"=> "",
        "Amount"=> ""
      ],
      "payment"=> [
        "method"=> [
          "token"=> "",
          "type"=> ""
        ],
        "instrument"=> [
          "expiry"=> [
            "year"=> "",
            "month"=> "",
            "dateTime"=> ""
          ],
          "provider"=> "",
          "iFSC"=> "",
          "holder"=> [
            "name"=> "",
            "address"=> [
              "country"=> "",
              "street"=> "",
              "state"=> "",
              "city"=> "",
              "zipCode"=> "",
              "county"=> ""
            ]
          ],
          "bIC"=> "",
          "type"=> "",
          "action"=> "",
          "mICR"=> "",
          "verificationCode"=> "",
          "iBAN"=> "",
          "processor"=> "",
          "issuance"=> [
            "year"=> "",
            "month"=> "",
            "dateTime"=> ""
          ],
          "alias"=> "",
          "identifier"=> "",
          "token"=> "",
          "authentication"=> [
            "token"=> "",
            "type"=> "",
            "subType"=> ""
          ],
          "subType"=> "",
          "issuer"=> "",
          "acquirer"=> ""
        ],
        "instruction"=> [
          "occurrence"=> "",
          "amount"=> "",
          "frequency"=> "",
          "type"=> "",
          "description"=> "",
          "action"=> "",
          "limit"=> "",
          "endDateTime"=> "",
          "identifier"=> "",
          "reference"=> "",
          "startDateTime"=> "",
          "validity"=> ""
        ]
      ],
      "transaction"=> [
        "deviceIdentifier"=> "S",
        "smsSending"=> "",
        "amount"=> "",
        "forced3DSCall "=> "",
        "type"=> "001",
        "description"=> "",
        "currency"=> $mer_array['currency'],
        "isRegistration"=> "",
        "identifier"=> $transactionIdentifier,
        "dateTime"=> "",
        "token"=> $_POST['tpslTransactionID'],
        "securityToken"=> "",
        "subType"=> "006",
        "requestType"=> "TSI",
        "reference"=> "",
        "merchantInitiated"=> "",
        "merchantRefNo"=> ""
      ],
      "consumer"=> [
        "mobileNumber"=> "",
        "emailID"=> "",
        "identifier"=> "",
        "accountNo"=> ""
      ]
  	);

	$finalJsonReq = json_encode($arr_req);

    function callAPI($method, $url, $finalJsonReq)
    {
        $curl = curl_init();
        switch ($method){
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($finalJsonReq)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $finalJsonReq);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($finalJsonReq)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $finalJsonReq);                              
                break;
            default:
                if ($finalJsonReq)
                    $url = sprintf("%s?%s", $url, http_build_query($finalJsonReq));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result)
        {
            die("Connection Failure !! Try after some time.");
        }
        curl_close($curl);
        return $result;
    }

    $method = 'POST';
    $url = "https://www.paynimo.com/api/paynimoV2.req";
    $res_result = callAPI($method, $url, $finalJsonReq);
    $stopResponseData = json_decode($res_result, true);

	echo '<table class="table" border = "1" cellpadding="2" cellspacing="0" style="width: 50%;text-align: center;">
        <thead>
          <tr class="info">
            <th>Field Name</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Status Code</td>
            <td>'.$stopResponseData["paymentMethod"]["paymentTransaction"]["statusCode"].'</td>
          </tr>
          <tr>
            <td>Merchant Transaction Reference No.</td>
            <td>'.$stopResponseData["merchantTransactionIdentifier"].'</td>
          </tr>
          <tr>
            <td>Message</td>
            <td>'.$stopResponseData["paymentMethod"]["error"]["desc"].'</td>
          </tr>
        </tbody>
      </table>
      <br>
      <a href=' .$base_url. "stop-payment.php" . '>Go Back To Stop Payment</a>';
}
?>