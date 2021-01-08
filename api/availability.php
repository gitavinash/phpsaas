<?php

    if(isset($domain)) {

        $url="https://domaincheck.httpapi.com/api/domains/available.json?auth-userid=745749&api-key=ymWEyxOhbLP8upKP8Ix6L63KXkMHtUZL&domain-name={$domain}&tlds=com";

        echo $url;
        exit();

        $smsStatus=file_get_contents($url);

    }