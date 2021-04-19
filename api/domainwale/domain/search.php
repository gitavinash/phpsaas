<?php

    if(isset($domain)) {

		if($domain!='') {

			$domain = str_replace('www.', '', $domain);
			$domain = str_replace('www', '', $domain);
			$domain = str_replace('/', '', $domain);
			$domain = str_replace(':', '', $domain);
			$domain = str_replace('https', '', $domain);
			$domain = str_replace('http', '', $domain);
			$domain = trim($domain);
			$domain = filter_var($domain, FILTER_SANITIZE_URL);
	
			/** Domain search API url */
	
			$url = "{$godaddy_url}{$godaddy_api_version}/domains/available?checkType=full";

			$domains=array(
				"{$domain}.com",
				"{$domain}.in",
				"{$domain}.net",
				"{$domain}.org"
			);

			$header = array(
				'Authorization: sso-key '.$godaddy_api_key.':'.$godaddy_api_salt,
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
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($domains, TRUE));
			$result = curl_exec($ch);
			curl_close($ch);
	
			$checkDomain = json_decode($result, true);

			if(isset($checkDomain['domains'])) {

				$status=200;
				$msg='Data fetch successfully';

			} else {

				$msg=$checkDomain['message'];

			}

		} else {

			$msg='Please enter the domain name';

		}
	
    } else {

        $msg='Please enter the domain name';

    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
        'domain_list' => ((isset($checkDomain['domains'])) ? $checkDomain['domains'] : '' )
    );

    echo json_encode($result, TRUE);