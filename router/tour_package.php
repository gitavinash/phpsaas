<?php

/** Check link status */

switch ($link_status) {

    case 301:
        require_once ROUTER . '301.php';
        break;

    default:

        /** Fetch data from database table  */

        $packageQuery = "SELECT 
    tp.*,
    l.*,
    tpp.*
FROM " . TABLE_PREFIX . "tour_package tp
LEFT JOIN " . TABLE_PREFIX . "link l 
    ON l.link_relation_id = tp.tour_package_id
    AND l.link_status = 200
    AND l.link_site_id = " . SITE_ID . "
    AND l.link_type = 'tour_package'
LEFT JOIN (
    SELECT 
        tour_package_price_parent_id,
        MIN(tour_package_price_selling_adult) AS tour_package_price_selling_adult
    FROM " . TABLE_PREFIX . "tour_package_price
    WHERE tour_package_price_site_id = " . SITE_ID . "
    GROUP BY tour_package_price_parent_id
) tpp_min ON tpp_min.tour_package_price_parent_id = tp.tour_package_id
LEFT JOIN " . TABLE_PREFIX . "tour_package_price tpp 
    ON tpp.tour_package_price_parent_id = tp.tour_package_id
    AND tpp.tour_package_price_site_id = " . SITE_ID . "
    AND tpp.tour_package_price_selling_adult = tpp_min.tour_package_price_selling_adult
WHERE l.link_id = '15860'
  AND tp.tour_package_status != 3
  AND tp.tour_package_site_id = " . SITE_ID;

        if ($tourPackageData = mysqli_query($connection, $packageQuery)) {

            if (mysqli_num_rows($tourPackageData) != 0) {

                extract(mysqli_fetch_assoc($tourPackageData));

                $tourPackageSection = ((isset($tour_package_section)) ? json_decode(base64_decode($tour_package_section), true) : '');

                if (file_exists(ROUTER . $link_status . '.php')) {

                    /** Include status router file */

                    require_once ROUTER . $link_status . '.php';
                } else {

                    // ob_start();

                    /** Check category template is avilable or not */

                    $checkCategoryTemplate = USER_PATH . strtolower($tour_package_type) . '-page.php';
                    $checkPageNameTemplate = USER_PATH . strtolower(str_replace(' ', '-', $tour_package_title)) . '-tour-package.php';

                    if (file_exists($checkCategoryTemplate)) {

                        /** Opening category page template */

                        require_once $checkCategoryTemplate;
                    } else if (file_exists($checkPageNameTemplate)) {

                        /** Open template as same name of page name */

                        require_once $checkPageNameTemplate;
                    } else {

                        /** Display content in default template */

                        require_once USER_PATH . 'tour-package.php';
                    }

                    // $cached = fopen($cacheFileName, 'w');
                    // fwrite($cached, ob_get_contents());
                    // fclose($cached);
                    // ob_end_flush(); // Send the output to the browser

                }
            } else {

                if (DEVELOPMENT_MODE == TRUE) {
                    echo "SELECT * FROM " . TABLE_PREFIX . "tour_package LEFT JOIN " . TABLE_PREFIX . "link ON link_relation_id=tour_package_id AND link_status=200 AND link_site_id=" . SITE_ID . " AND link_type='tour_package' WHERE link_id='{$link_id}' AND tour_package_status!=3 AND tour_package_site_id=" . APP_SITE_ID . " ORDER BY tour_package_created_at DESC";
                } else {
                    echo "Error";
                }
            }
        } else {

            echo mysqli_error($connection);
        }
}