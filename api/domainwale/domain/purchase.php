<?php

    if(isset($domain)) {

        // if(isset($transaction_id)) {

        //     $transaction_id=base64_decode($transaction_id);

        //     if($checkTransaction-my)

        // } else {

        //     $msg='Transaction id is missing';

        // }

        $domain='avinashkumarsharmadelhiwale.com';

        $bodyContent = '{
            "consent": {
               "agreedAt": "'.date('Y-m-d\TH:i:s\Z').'",
               "agreedBy": "'.IP.'",
               "agreementKeys": [
                 "DNRA"
               ]
             },
             "contactAdmin": {
               "addressMailing": {
                 "address1": "Gomti Nagar",
                 "address2": "Lucknow",
                 "city": "Lucknow",
                 "country": "IN",
                 "postalCode": "226010",
                 "state": "Uttar pradesh"
               },
               "email": "user@example.com",
               "fax": "",
               "jobTitle": "Full Stack Developer",
               "nameFirst": "Hemant",
               "nameLast": "Vishwakarma",
               "nameMiddle": "",
               "organization": "Wizweb Technology",
               "phone": "+91.9876543210"
             },
             "contactBilling": {
               "addressMailing": {
                "address1": "Gomti Nagar",
                 "address2": "Lucknow",
                 "city": "Lucknow",
                 "country": "IN",
                 "postalCode": "226010",
                 "state": "Uttar pradesh"
               },
               "email": "user@example.com",
               "fax": "",
               "jobTitle": "Full Stack Developer",
               "nameFirst": "Hemant",
               "nameLast": "Vishwakarma",
               "nameMiddle": "",
               "organization": "Wizweb Technology",
               "phone": "+91.9876543210"
             },
             "contactRegistrant": {
               "addressMailing": {
                 "address1": "Gomti Nagar",
                 "address2": "Lucknow",
                 "city": "Lucknow",
                 "country": "IN",
                 "postalCode": "226010",
                 "state": "Uttar pradesh"
               },
               "email": "user@example.com",
               "fax": "",
               "jobTitle": "Full Stack Developer",
               "nameFirst": "Hemant",
               "nameLast": "Vishwakarma",
               "nameMiddle": "",
               "organization": "Wizweb Technology",
               "phone": "+91.9876543210"
             },
             "contactTech": {
               "addressMailing": {
                 "address1": "Gomti Nagar",
                 "address2": "Lucknow",
                 "city": "Lucknow",
                 "country": "IN",
                 "postalCode": "226100",
                 "state": "Uttar pradesh"
               },
               "email": "user@example.com",
               "fax": "",
               "jobTitle": "Full Stack Developer",
               "nameFirst": "Hemant",
               "nameLast": "Vishwakarma",
               "nameMiddle": "",
               "organization": "Wizweb Technology",
               "phone": "+91.9807321564"
             },
             "domain": "'.$domain.'",
             "nameServers": [
               "ns1.domainwale.in",
               "ns2.domainwale.in"
             ],
             "period": 2,
             "renewAuto": true
           }';

        echo $bodyContent;
        exit();

        /** Domain purchase API call */
	
		$url = "{GODADDY_URL}{GODADDY_API_VERSION}/domains/purchase/";

		$header = array(
            'X-Shopper-Id: '.$godaddy_shopper_id,
			'Authorization: sso-key '.GODADDY_API_KEY.':'.GODADDY_API_SALT,
			"Content-Type: application/json",
			'Accept: application/json'
		);
	
			$ch = curl_init();
			$timeout=60;
	
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);
			$result = curl_exec($ch);
			curl_close($ch);
	
			$purchaseDomainStatus = json_decode($result, true);

            print_r($purchaseDomainStatus);
            exit();

    } else {

        $msg='Please enter a domain name';

    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
        'domain_list' => ((isset($purchaseDomainStatus['domains'])) ? $purchaseDomainStatus['domains'] : '' )
    );

    echo json_encode($result, TRUE);