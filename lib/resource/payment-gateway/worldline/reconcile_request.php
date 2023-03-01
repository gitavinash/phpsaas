<?php 
$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http' ;
$base_url .= '://'.$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

if(isset($_POST))
{
  set_time_limit(300);
	$identifier = trim($_POST['transactionIdentifier']);
  $identifierArray = explode(',', $identifier);
  $startDate = new \DateTime($_POST['fromtransactionDate']);
  $endDate = clone $startDate;
  $endDate->modify($_POST['totransactionDate'].' +1 day');
  $interval = new DateInterval('P1D');
  $period = new DatePeriod($startDate, $interval, $endDate);
  foreach ($period as $date) {
    $dates[] = $date->format('d-m-Y');
  }

    $admin_data = file_get_contents("./worldline_AdminData.json");
    $mer_array = json_decode($admin_data, true);

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
    foreach ($identifierArray as $id) {
      foreach ($dates as $date) {
        $arr_req = array(
            "merchant" => [
                "identifier" => $mer_array['merchantCode']
            ],
            "transaction" => [ "deviceIdentifier" => "S", "currency" => $mer_array['currency'], "identifier" => $id, "dateTime" => $date, "requestType" => "O"]
        );
        $finalJsonReq = json_encode($arr_req);
        $res_result = callAPI($method, $url, $finalJsonReq);
        $reconciliationData = json_decode($res_result, true);
        if($reconciliationData["paymentMethod"]["paymentTransaction"]["statusCode"] == '9999'){
          $statusCode = $reconciliationData["paymentMethod"]["paymentTransaction"]["statusCode"];
        }
        if ($reconciliationData["paymentMethod"]["paymentTransaction"]["statusCode"] == '0300'){
          if(!(isset($success)&&!empty($success))){
            echo ('<table class="table table-bordered table-hover" border = "1" cellpadding="2" cellspacing="0" style="width: 30%;text-align: center;">
                <thead>
                  <tr class="info">
                    <th>Field Name</th>
                    <th>Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Merchant Code</td>
                    <td>'.$reconciliationData["merchantCode"].'</td>
                  </tr>
                  <tr>
                    <td>Merchant Transaction Identifier</td>
                    <td>'.$reconciliationData["merchantTransactionIdentifier"].'</td>
                  </tr>
                  <tr>
                    <td>Token Identifier</td>
                    <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["identifier"].'</td>
                  </tr>
                  <tr>
                    <td>Amount</td>
                    <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["amount"].'</td>
                  </tr>
                  <tr>
                    <td>Message</td>
                    <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["errorMessage"].'</td>
                  </tr>
                  <tr>
                    <td>Status Code</td>
                    <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["statusCode"].'</td>
                  </tr>
                  <tr>
                    <td>Status Message</td>
                    <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["statusMessage"].'</td>
                  </tr>
                  <tr>
                    <td>Date & Time</td>
                    <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["dateTime"].'</td>
                  </tr>
                </tbody>
              </table>
              <br><br>'
            );
            break;
          }
          $success = $reconciliationData;
        }
      }
      if(!empty($statusCode) && ($reconciliationData["paymentMethod"]["paymentTransaction"]["statusCode"] == '9999')|| ($reconciliationData["paymentMethod"]["paymentTransaction"]["statusCode"] == '0399')){
        echo ('<table class="table table-bordered table-hover" border = "1" cellpadding="2" cellspacing="0" style="width: 30%;text-align: center;">
              <thead>
                <tr class="info">
                  <th>Field Name</th>
                  <th>Value</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Merchant Code</td>
                  <td>'.$reconciliationData["merchantCode"].'</td>
                </tr>
                <tr>
                  <td>Merchant Transaction Identifier</td>
                  <td>'.$reconciliationData["merchantTransactionIdentifier"].'</td>
                </tr>
                <tr>
                  <td>Token Identifier</td>
                  <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["identifier"].'</td>
                </tr>
                <tr>
                  <td>Amount</td>
                  <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["amount"].'</td>
                </tr>
                <tr>
                  <td>Message</td>
                  <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["errorMessage"].'</td>
                </tr>
                <tr>
                  <td>Status Code</td>
                  <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["statusCode"].'</td>
                </tr>
                <tr>
                  <td>Status Message</td>
                  <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["statusMessage"].'</td>
                </tr>
                <tr>
                  <td>Date & Time</td>
                  <td>'.$reconciliationData["paymentMethod"]["paymentTransaction"]["dateTime"].'</td>
                </tr>
              </tbody>
            </table>
            <br><br>'
          );
      }
    }
    echo '<a href=' .$base_url. "reconciliation.php" . '>Go Back To Reconciliation Page</a>';
}
?>