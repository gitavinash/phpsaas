<?php

if($checkSiteConfiguration=mysqli_query($connection, "SELECT site_configuration_name, site_configuration_value FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_name LIKE 'sms_%' AND site_configuration_status=1 AND site_configuration_site_id=".SITE_ID)) {
    
    if(mysqli_num_rows($checkSiteConfiguration)!=0) {
        
        while($siteConfigurationData=mysqli_fetch_assoc($checkSiteConfiguration)) {
            
            extract($siteConfigurationData);
            $configuration[$site_configuration_name]=$site_configuration_value;
            
        }

        extract($configuration);

        require_once 'sms-api-configuration.php';
        $smsStatus=file_get_contents($smsURL);
        
    }

}