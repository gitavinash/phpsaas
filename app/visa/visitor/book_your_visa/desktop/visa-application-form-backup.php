<?php

$redirectToMobileAuth = true;
require_once LOGIN_CHECK;
require_once 'header.php';

if ($checkPackage = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "visa_package INNER JOIN " . TABLE_PREFIX . "visa_country ON visa_country_id=visa_package_country_id WHERE visa_package_id = '" . base64_decode($package_id) . "' AND visa_package_site_id = " . SITE_ID . " AND visa_country_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkPackage) > 0) {
        extract(mysqli_fetch_assoc($checkPackage));
    }
}

if (isset($step)) {
    if ($step != '') {
        switch ($step) {
            case 2:
                require_once USER_PATH . 'visa-application-forms/persoanl-details.php';
                break;
            case 3:
                require_once USER_PATH . 'visa-application-forms/upload-documents.php';
                break;
            default:
                require_once USER_PATH . 'visa-application-forms/travel-details.php';
                break;
        }
    } else {
        require_once ERROR_404;
    }
} else {
    require_once ERROR_404;
}

require_once 'footer.php';