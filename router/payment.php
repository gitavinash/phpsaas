<?php

    if($checkPaymentGateway=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_site_id=".SITE_ID." AND site_configuration_name LIKE 'pg_%' AND site_configuration_status=1")) {

        if(mysqli_num_rows($checkPaymentGateway)!=0) {

            while($paymentGatewayData=mysqli_fetch_assoc($checkPaymentGateway)) {
            
                extract($paymentGatewayData);
                $configuration[$site_configuration_name]=$site_configuration_value;
                
            }
    
            extract($configuration);

            $pgFile = PAYMENT_GATEWAY.$pg_name.'/'.((isset($urlStructure[1])) ? $urlStructure[1] : 'generate' ).'.php';

            if(file_exists($pgFile)) {

                require_once $pgFile;

            } else {

                echo 'Something is wrong while calling payment gateway function';

            }

        } else {

            echo "Something is wrong please contact to system administrator";

        }

    }
    exit();