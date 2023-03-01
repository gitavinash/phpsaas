<?php 

//echo "<pre>";print_r($_POST);die();
if(isset($_POST)){

	$data = array(
		'merchantCode'							=> $_POST['merchantCode'],
	    'merchantSchemeCode' 					=> $_POST['merchantSchemeCode'],
	    'salt'									=> $_POST['salt'],
	    'typeOfPayment' 						=> $_POST['typeOfPayment'],
	    'currency' 								=> $_POST['currency'],
	    'primaryColor' 							=> $_POST['primaryColor'],
	    'secondaryColor'						=> $_POST['secondaryColor'],
	    'buttonColor1' 							=> $_POST['buttonColor1'],
	    'buttonColor2' 							=> $_POST['buttonColor2'],
	    'logoURL'			 					=> $_POST['logoURL'],
	    'enableExpressPay' 						=> $_POST['enableExpressPay'],
	    'separateCardMode' 						=> $_POST['separateCardMode'],
	    'enableNewWindowFlow'		 			=> $_POST['enableNewWindowFlow'],
	    'merchantMessage' 						=> $_POST['merchantMessage'],
	    'disclaimerMessage' 					=> $_POST['disclaimerMessage'],
	    'paymentMode' 							=> $_POST['paymentMode'],
	    'paymentModeOrder' 						=> $_POST['paymentModeOrder'],
	    'enableInstrumentDeRegistration' 		=> $_POST['enableInstrumentDeRegistration'],
	    'transactionType'						=> $_POST['transactionType'],
	    'hideSavedInstruments' 					=> $_POST['hideSavedInstruments'],
	    'saveInstrument' 						=> $_POST['saveInstrument'],
	    'displayTransactionMessageOnPopup' 		=> $_POST['displayTransactionMessageOnPopup'],
	    'embedPaymentGatewayOnPage' 			=> $_POST['embedPaymentGatewayOnPage'],
	    'enableEmandate' 						=> $_POST['enableEmandate'],
	    'hideSIConfirmation'					=> $_POST['hideSIConfirmation'],
	    'expandSIDetails'						=> $_POST['expandSIDetails'],
	    'enableDebitDay'						=> $_POST['enableDebitDay'],
	    'showSIResponseMsg' 					=> $_POST['showSIResponseMsg'],
	    'showSIConfirmation'					=> $_POST['showSIConfirmation'],
	    'enableTxnForNonSICards' 				=> $_POST['enableTxnForNonSICards'],
	    'showAllModesWithSI' 					=> $_POST['showAllModesWithSI'],
	    'enableSIDetailsAtMerchantEnd' 			=> $_POST['enableSIDetailsAtMerchantEnd']
	);

	$newData = json_encode($data);

	$name = "worldline_AdminData"; 
    $file_name = $name.'.json';
    $path = $name.'.json';
	if(file_exists($path))
	{  
	    //echo 1;die();
	    unlink($path);
	    if(file_put_contents( "$file_name", $newData ) ) 
	    { 
	        //echo 'Admin Content Updated';
	        $location = 'admin.php';
	        header("Location: $location?message=success");
	    }
	    else
	    { 
	        echo 'There is some error'; 
	    }
	}
	else
	{
		if(file_put_contents( "$file_name", $newData ) ) 
	    { 
	        //echo $file_name .' file created';
	        $location = 'admin.php';
	        header("Location: $location?message=success");
	    } 
	    else
	    { 
	        echo 'There is some error'; 
	    }
	}	
}

?>