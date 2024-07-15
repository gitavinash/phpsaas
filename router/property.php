<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:

        /** Fetch data from database table  */

        if ($propertyData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "property INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=property_id WHERE property_status=1 AND link_type='property' AND property_id='{$link_relation_id}' AND property_site_id IN (" . SITE_ID . ", 55) ORDER BY property_time DESC LIMIT 0,10")) {

            if (mysqli_num_rows($propertyData) != 0) {

                extract(mysqli_fetch_assoc($propertyData));

                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    ob_start();

                    $propertyTypeTemplate = USER_PATH . strtolower(str_replace('_', '-', str_replace(' ', '-', $property_type))) . '-property.php';

                    if (file_exists(($propertyTypeTemplate))) {

                        /** Opening category property type */

                        require_once $propertyTypeTemplate;
                    } else {

                        /** Display content in default template */

                        require_once USER_PATH . 'property.php';
                    }

                    $cached = fopen(CACHE_FILE, 'w');
                    fwrite($cached, ob_get_contents());
                    fclose($cached);
                    ob_end_flush(); // Send the output to the browser

                }
            } else {

                echo 'Error';
            }
        } else {

            echo mysqli_error($connection);
        }

        break;
}