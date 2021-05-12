<?php

    if(isset($domain)) {

		if($domain!='') {

			$domain = str_replace(' ', '', $domain);
			$domain = str_replace('www.', '', $domain);
			$domain = str_replace('www', '', $domain);
			$domain = str_replace('/', '', $domain);
			$domain = str_replace(':', '', $domain);
			$domain = str_replace('https', '', $domain);
			$domain = str_replace('http', '', $domain);
			$domain = trim($domain);
			$domain = filter_var($domain, FILTER_SANITIZE_URL);

			$domain=explode('.', $domain);
			// $domainExtension=((isset($domain[1])) ? $domain[1] : '' );
			$domain=$domain[0];
	
			/** Domain search API url */
	
			$url = GODADDY_URL.GODADDY_API_VERSION."/domains/available?checkType=full";

			$domains=array(
				// ((($domainExtension!='')) ? $domain.'.'.$domainExtension : $domain.'.com' ),
				"{$domain}.com",
				"{$domain}.in",
				"{$domain}.net",
				"{$domain}.org",
				"{$domain}.co.in",
			);

			$header = array(
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
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($domains, TRUE));
			$result = curl_exec($ch);
			curl_close($ch);
	
			$checkDomain = json_decode($result, true);

			if(isset($checkDomain['domains'])) {

				$status=200;
				$msg='Data fetch successfully';

			} else {

				$msg=$checkDomain;

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