<?php

    if($domainName!='') {

        $domainName = str_replace('www.', '', $domainName);
		$domainName = str_replace('www', '', $domainName);
		$domainName = str_replace('/', '', $domainName);
		$domainName = str_replace(':', '', $domainName);
		$domainName = str_replace('https', '', $domainName);
		$domainName = str_replace('http', '', $domainName);
		$domainName = trim($domainName);
		$domainName = filter_var($domainName, FILTER_SANITIZE_URL);

        /** Domain search API url */

        $url = "{$godaddy_url}{$godaddy_api_version}/domains/available";

		$domainList=array(
			"{$domainName}.in",
			"{$domainName}.com",
			"{$domainName}.net",
			"{$domainName}.org",
		);
		
		$bodyContent=json_encode(array(
			'domains' => $domainList,
			'checkType' => 'FULL'
		), TRUE);

		$header = array(
			'Authorization: sso-key '.$godaddy_api_key.':'.$godaddy_api_salt.'',
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

		$checkDomain = json_decode($result, true);

        print_r($checkDomain);

    } else {

        $msg='Please enter the domain name';

    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error' ),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response' )
    );

    echo json_encode($result, TRUE);