<?php 

$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http' ;
$base_url .= '://'.$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

if(isset($_POST))
{
  $transactionIdentifier = str_shuffle("0123456789");
	$date = date("dmY", strtotime($_POST['transactionDate']));
  $admin_data = file_get_contents("../worldline_AdminData.json");
  $mer_array = json_decode($admin_data, true);
	$arr_req = array(
	    "merchant" => [
	      "identifier" => $mer_array['merchantCode']
	    ],
      "payment" => [
        "instrument" => [
          "identifier" => $mer_array['merchantSchemeCode']
        ],
        "instruction" => [
          "amount"=> $_POST['amount'],
          "endDateTime"=> $date,
          "identifier"=> $_POST['mandateRegistrationId']
        ]
      ],
	    "transaction" => [ 
        "deviceIdentifier" => "S",
        "type" => $_POST['transactionType'],
        "currency" => $mer_array['currency'],
        "identifier" => $transactionIdentifier,
        "subType" => "003",
        "requestType" => "TSI"
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
       if(!$result){die("Connection Failure !! Try after some time.");}
       curl_close($curl);
       return $result;
    }

    $method = 'POST';
    $url = "https://www.paynimo.com/api/paynimoV2.req";
    $res_result = callAPI($method, $url, $finalJsonReq);
    $schedulingData = json_decode($res_result, true);
    // var_dump('<pre>',$schedulingData);die;

    	echo '<table class="table table-bordered table-hover" border = "1" cellpadding="2" cellspacing="0" style="width: 50%;text-align: center;">
        <thead>
          <tr class="info">
            <th>Field Name</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Status Code</td>
            <td>'.$schedulingData["paymentMethod"]["paymentTransaction"]["statusCode"].'</td>
          </tr>
          <tr>
            <td>Merchant transaction reference no </td>
            <td>'.$schedulingData["merchantTransactionIdentifier"].'</td>
          </tr>
          <tr>
            <td>worldline Merchant Transaction ID </td>
            <td>'.$schedulingData["paymentMethod"]["paymentTransaction"]["identifier"].'</td>
          </tr>
          <tr>
            <td>Message</td>
            <td>'.$schedulingData["paymentMethod"]["paymentTransaction"]["errorMessage"].'</td>
          </tr>
          <tr>
            <td>Amount</td>
            <td>'.$schedulingData["paymentMethod"]["paymentTransaction"]["amount"].'</td>
          </tr>
          <tr>
            <td>Date</td>
            <td>'.$schedulingData["paymentMethod"]["paymentTransaction"]["dateTime"].'</td>
          </tr>
        </tbody>
      </table>
      <br>
      <a href=' .$base_url. "transaction-scheduling.php" . '>Go Back To Transaction Scheduling</a>';
}

?>