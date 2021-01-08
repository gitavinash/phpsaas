<?php

    if(isset($domain)) {

        $url="https://domaincheck.httpapi.com/api/domains/available.json?auth-userid=745749&api-key=ymWEyxOhbLP8upKP8Ix6L63KXkMHtUZL&domain-name={$domain}&tlds=com";

        $smsStatus=file_get_contents($url);

    }

    $result=array(
        'status' => 200,
        'msg' => $smsStatus
    )

    echo json_encode($result, TRUE);