<?php 
$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http' ;
$base_url .= '://'.$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

if(isset($_POST))
{
    $token = $_POST['transactionIdentifier'];
	$amount = $_POST['amount'];
	$date = $_POST['transactionDate'];
	$newDate = date("d-m-Y", strtotime($date));

	//echo $identifier.' | '.$newDate.'<br><br>';

    $admin_data = file_get_contents("./worldline_AdminData.json");
    $mer_array = json_decode($admin_data, true);

  	$arr_req = array(
  	    "merchant" => [
  	        "identifier" => $mer_array['merchantCode']
  	    ],
        "cart" => [ "" => ""
        ],
  	    "transaction" => [ "deviceIdentifier" => "S", "amount" => $amount, "currency" => $mer_array['currency'], "dateTime" => $newDate, "token" => $token, "requestType" => "R"]
  	);

	$finalJsonReq = json_encode($arr_req);

	//echo $finalJsonReq; die();

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
    $refundData = json_decode($res_result, true);
	/*$location = 'offline_verification.php';
	header("Location: $location?encrypt=$offlineVerifyData");
	echo "<pre>";print_r($offlineVerifyData);die();*/

    //echo "<pre>";print_r($refundData);die();

	echo '<table class="table" border = "1" align="center" cellpadding="2" cellspacing="0" style="width: 50%;text-align: center;">
        <thead>
          <tr class="info">
            <th>Field Name</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Merchant Code</td>
            <td>'.$refundData["merchantCode"].'</td>
          </tr>
          <tr>
            <td>Merchant Transaction Identifier</td>
            <td>'.$refundData["merchantTransactionIdentifier"].'</td>
          </tr>
          <tr>
            <td>Token Identifier</td>
            <td>'.$refundData["paymentMethod"]["paymentTransaction"]["identifier"].'</td>
          </tr>
          <tr>
            <td>Refund Identifier</td>
            <td>'.$refundData["paymentMethod"]["paymentTransaction"]["refundIdentifier"].'</td>
          </tr>
          <tr>
            <td>Amount</td>
            <td>'.$refundData["paymentMethod"]["paymentTransaction"]["amount"].'</td>
          </tr>
          <tr>
            <td>Message</td>
            <td>'.$refundData["paymentMethod"]["paymentTransaction"]["errorMessage"].'</td>
          </tr>
          <tr>
            <td>Status Code</td>
            <td>'.$refundData["paymentMethod"]["paymentTransaction"]["statusCode"].'</td>
          </tr>
          <tr>
            <td>Status Message</td>
            <td>'.$refundData["paymentMethod"]["paymentTransaction"]["statusMessage"].'</td>
          </tr>
          <tr>
            <td>Date & Time</td>
            <td>'.$refundData["paymentMethod"]["paymentTransaction"]["dateTime"].'</td>
          </tr>
        </tbody>
      </table>
      <br>
      <a href=' .$base_url. "refund.php" . '>Go Back To Refund Page</a>';
}

?>