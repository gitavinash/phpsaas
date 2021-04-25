<?php

    $paymentGatewayRequestType=((isset($urlStructure[1])) ? $urlStructure[1] : 'generate' );

    if($paymentGatewayRequestType=='generate') {

        // Payment gateway generate file

        require_once PAYMENT_GATEWAY.$paymentGatewayRequestType.'.php';

    } else {

        // fetch payment file according to request type

        if(file_exists(PAYMENT_GATEWAY.PG_NAME.'/'.$paymentGatewayRequestType.'.php')) {

            require_once PAYMENT_GATEWAY.PG_NAME.'/'.$paymentGatewayRequestType.'.php';

        } else {

            SYSTEM_THEME.'payment-gateway-file-missing.php';

        }

    }