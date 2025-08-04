<?php

if ($sendMessageTo != '') {

    $url = "https://bhashsms.com/api/sendmsgutil.php";

    // Encode all other values normally
    $query = http_build_query([
        "user"     => ((isset($whatsappAdmin)) ? $whatsappAdmin : "Aleya_BWA"),
        "pass"     => ((isset($whatsappPassword)) ? $whatsappPassword : "123456"),
        "sender"   => "BUZWAP",
        "phone"    => $sendMessageTo,
        "text"     => $templateName,
        "priority" => "wa",
        "stype"    => "normal"
    ]);

    // But Params: allow commas, encode only URL-sensitive characters (e.g., space → %20)
    $safeParams = str_replace(
        [' ', '+'],
        ['%20', '%20'], // ensure space stays as %20, not "+"
        $variablesValues
    );

    // Combine manually
    $fullUrl = $url . '?' . $query . '&Params=' . $safeParams;

    // Perform GET request
    $response = file_get_contents($fullUrl);
}