<?php

    $paymentGateway=((isset($urlStructure[1])) ? $urlStructure[1] : DEFAULT_PAYMENT_GATEWAY );
    $paymentGatewayAction=((isset($urlStructure[2])) ? $urlStructure[2] : 'generate' );

    define('PAYMENT_GATEWAY', RESOURCE.'payment/'.$paymentGateway.'/'.$paymentGatewayAction.'.php');

    /** Check payment gateway modules is available or not */

    if(file_exists(PAYMENT_GATEWAY)) {

        if($paymentGatewayAction=='generate') {

            /** fetch transaction data from database */

            $transactionID=mysqli_real_escape_string($connection, base64_decode($_GET['request']));

            if($checkTransaction=mysqli_query($connection, "SELECT * FROM {$table_prefix}transaction WHERE transaction_id='{$transactionID}' AND transaction_gateway='{$paymentGateway}'")) {

                if(mysqli_num_rows($checkTransaction)!=0) {

                    extract(mysqli_fetch_assoc($checkTransaction));

                    if($transaction_status!=0) {

                        /** If transaction is already initial before then stop here */

                        echo 'This payment link is broken or not valid';
                        exit();

                    }

                } else {

                    echo 'Unable to found this payment request';

                }

            }

        }

        /** Include required payment gateway file */

        require_once PAYMENT_GATEWAY;

    } else {

        echo "Something is wrong";

    }

    exit();