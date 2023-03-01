<?php

//echo "<pre>";print_r($_POST);die();
$admin_data = file_get_contents("./worldline_AdminData.json");
$mer_array = json_decode($admin_data, true);

$val = $_POST;

if($mer_array['typeOfPayment'] == "TEST"){
	$val['amount'] = 1;
}

if($mer_array['enableEmandate'] == 1 && $mer_array['enableSIDetailsAtMerchantEnd'] == 1){
	$val['debitStartDate'] = date("d-m-Y");
	$val['debitEndDate'] = date("d-m-Y", strtotime($val['debitEndDate']));
}

$datastring=$val['mrctCode']."|".$val['txn_id']."|".$val['amount']."|".$val['accNo']."|".$val['custID']."|".$val['mobNo']."|".$val['email']."|".$val['debitStartDate']."|".$val['debitEndDate']."|".$val['maxAmount']."|".$val['amountType']."|".$val['frequency']."|".$val['cardNumber']."|".$val['expMonth']."|".$val['expYear']."|".$val['cvvCode']."|".$val['SALT'];

$hashed = hash('sha512',$datastring);  

$data=array("hash"=>$hashed,"data"=>array($val['mrctCode'],$val['txn_id'],$val['amount'],$val['debitStartDate'],$val['debitEndDate'],$val['maxAmount'],$val['amountType'],$val['frequency'],$val['custID'],$val['mobNo'],$val['email'],$val['accNo'],$val['returnUrl'],$val['name'],$val['scheme'],$val['currency'],$val['accountName'],$val['ifscCode'],$val['accountType']));

echo json_encode($data);

?>



