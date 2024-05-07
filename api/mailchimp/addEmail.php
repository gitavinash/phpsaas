<?php

$status = "warning";
$msg = '';

if (!defined('MAILCHIMP_API_KEY')) {
    $msg = 'Mailchimp API key is missing';
} else {

    $apiKey =  'apikey ' . MAILCHIMP_API_KEY;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://us13.api.mailchimp.com/3.0/lists/{$list}/members",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "{
                'email_address': '{$email}',
                'status': 'subscribed'
            }",
        CURLOPT_HTTPHEADER => array(
            "Authorization: {$apiKey}",
        ),
    ));

    $response = json_decode(curl_exec($curl), TRUE);
    curl_close($curl);

    if ($response['status'] == 200) {
        $status = "success";
        $msg = 'Email register successfully';
    } else {
        $status == "error";
        $msg = $response['title'] . ' - ' . $response['detail'];
    }
}

echo json_encode(array(
    'status' => $status,
    'msg' => $msg
), TRUE);
