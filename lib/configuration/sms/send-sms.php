<?php

    $smsURL="http://103.16.101.52:8080/sendsms/bulksms?username=nutr-lask&password=anujpall&type=0&dlr=1&destination=91{$mobile}&source=LASKIN&message={$message}";
    $smsStatus=file_get_contents($smsURL);