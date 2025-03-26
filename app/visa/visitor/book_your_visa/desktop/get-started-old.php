<?php

if (isset($id) && isset($serviceType) && isset($productType)) {

    $serviceId = base64_decode($id);

    $productInfo = mysqli_query($connection, "SELECT inventory_price, inventory_tax_status, inventory_tax_rate FROM " . TABLE_PREFIX . "inventory WHERE inventory_relation_id='{$serviceId}' AND inventory_status=1 AND inventory_type='{$productType}' AND  inventory_site_id=" . SITE_ID);

    if (mysqli_num_rows($productInfo) != 0) {

        extract(mysqli_fetch_assoc($productInfo));

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userType = 'client';
            mysqli_query($connection, "START TRANSACTION");

            // Process to checkout page

            if ($checkEmail = mysqli_query($connection, "SELECT user_email, user_id as userID, user_salt_key as saltKey, profile_display_name FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=user_id WHERE user_email='{$email}'")) {

                if (mysqli_num_rows($checkEmail) == 0) {

                    /** Check mobile number is available or not */

                    if ($checkMobile = mysqli_query($connection, "SELECT profile_mobile FROM " . TABLE_PREFIX . "profile WHERE profile_mobile='{$mobile}'")) {

                        if (mysqli_num_rows($checkMobile) == 0) {

                            /** Start the user registration process */

                            /* Encrypt password with salt key */

                            $password = 'test12345';

                            $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                            $enc_password = hash('sha256', $saltKey . $password);

                            $name = explode(' ', $name);
                            $first_name = $name[0];
                            $last_name = end($name);

                            $profile_display_name = $first_name . ' ' . $last_name;

                            /** Checking Username is avilable or not */

                            $username = strtolower(str_replace(' ', '', $first_name . $last_name));

                            if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
                                if (mysqli_num_rows($checkUsername) != 0) {
                                    $username = strtolower($first_name) . time();
                                }
                            }

                            $siteURL = $username . '.' . SITE_DOMAIN;
                        } else {

                            $status = 'warning';
                            $msg = 'This mobile number is already in use';
                        }

                        /* Insert data into user table */

                        $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_password, user_salt_key, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobile}', '{$enc_password}', '$saltKey', NOW(), '" . IP . "')");
                        $userID = mysqli_insert_id($connection);

                        if (!isset($userType)) {

                            $insertSite = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site(site_owner_id, site_url, site_status) VALUES('{$userID}', '{$siteURL}', 0)");
                            $siteID = mysqli_insert_id($connection);

                            $insertSiteApp = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_installation(site_installation_site_id, site_installation_parent_site_id, site_installation_user_id, site_installation_package_id, site_installation_status, site_installation_expiry_date) VALUES('{$siteID}', '{$siteID}', '{$userID}', 1, 1, '{$expiryDate}')");

                            $insertSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES('{$siteID}', '{$userID}', '{$userType}', 1)");

                            $insertSiteSetting = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_setting(site_setting_site_id, site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_thumbnail_width, site_thumbnail_height, site_mainfest_json, site_robots_txt) VALUES('{$siteID}', 10, 10, 10, 60, 223, 96, '" . json_encode(DEFAULT_MAINFEST_JSON, true) . "', '" . DEFAULT_ROBOTS_TXT . "')");
                        } else {
                            $insertSite = true;
                            $insertSiteApp = true;
                            $insertSiteSetting = true;
                        }

                        $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '{$first_name}', '{$last_name}', '{$mobile}', '{$profile_display_name}', '{$email}')");
                    }
                } else {

                    extract(mysqli_fetch_assoc($checkEmail));

                    $insertUser = true;
                    $insertSite = true;
                    $insertSiteApp = true;
                    $insertProfile = true;
                    $insertSiteSetting = true;
                }

                $insertSiteAccess = mysqli_query(
                    $connection,
                    "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(" . SITE_ID . ", '{$userID}', '{$userType}', 1)"
                );

                if ($insertUser && $insertSite && $insertSiteApp && $insertSiteAccess && $insertSiteSetting && $insertProfile) {

                    /** Send User's Email Verification Mail */

                    if ($paymentOption != 'full') {
                        $inventory_price = $inventory_price * 0.30;
                    }

                    if ($inventory_tax_status == 1) {
                        $taxAmount = number_format((float)($inventory_tax_rate / 100) * $inventory_price, 2, '.', '');
                    } else {
                        $taxAmount = 0;
                    }

                    $totalPrice = $inventory_price + $taxAmount;

                    if ($checkCart = mysqli_query($connection, "SELECT cart_id FROM " . TABLE_PREFIX . "cart WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "' AND cart_status!=1")) {
                        if (mysqli_num_rows($checkCart) == 0) {
                            $addToCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_user_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$user_id}', '{$serviceId}', '{$productType}', '1', '{$totalPrice}', 2, NOW())");
                        } else {
                            $addToCart = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_relation_id='{$serviceId}', cart_price='{$totalPrice}' WHERE cart_session_id='" . SESSION_ID . "' AND cart_site_id='" . SITE_ID . "'");
                        }
                    }

                    if ($checkOrder = mysqli_query($connection, "SELECT order_id FROM " . TABLE_PREFIX . "order WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "' AND order_status!=1")) {
                        if (
                            mysqli_num_rows($checkOrder) == 0
                        ) {
                            $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_product_price, order_status, order_generate) VALUES(" . SITE_ID . ", '{$user_id}', '" . SESSION_ID . "', '{$totalPrice}', 0, NOW())");
                            $order_id = base64_encode(mysqli_insert_id($connection));
                        } else {
                            extract(mysqli_fetch_assoc($checkOrder));
                            $addNewOrder = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_product_price='{$totalPrice}' WHERE order_cart_session_id='" . SESSION_ID . "' AND order_site_id='" . SITE_ID . "'");
                            $order_id = base64_encode($order_id);
                        }
                    }

                    if ($addToCart && $addNewOrder) {
                        mysqli_query($connection, "COMMIT");
                        header("Location: /shipping/?id={$order_id}", TRUE, 301);
                    } else {
                        mysqli_query($connection, "ROLLBACK");
                        echo "Something went wrong while process to subscription";
                    }
                } else {

                    $status = 'error';
                    $msg = 'Something is wrong while trying to register';
                    mysqli_query($connection, 'ROLLBACK');
                }
            } else {
                $msg = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Unable to run query');
            }
        } else {

            // Show enquiry form

            require_once 'header.php';

?>


            <section>
                <div class="introBox">
                    <h1><?php echo ucwords(urldecode($serviceType)) ?></h1>
                </div>
            </section>

            <section>
                <div class="col-lg-7 col-md-12 mx-auto">
                    <form method="post">
                        <div class="form-header">
                            <h2>
                                Kindly fill this form to continue.
                            </h2>
                        </div>
                        <div class="form-group">
                            <label>Full name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="emal" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Contact number</label>
                            <input type="number" name="mobile" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Requirment</label>
                            <textarea name="requirment" class="form-control" id=""></textarea>
                        </div>
                        <div class="form-group">
                            <label>Payment option</label>
                        </div>
                        <div class="inline-radio form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentOption" id="advancePayment" value="advance" checked>
                            <label class="form-check-label" for="advancePayment">
                                Advance Payment ( INR <?php echo $inventory_price * 0.30; ?> )
                            </label>
                        </div>
                        <div class="inline-radio form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="paymentOption" id="fullPayment" value="full">
                            <label class="form-check-label" for="fullPayment">
                                Full Payment ( INR <?php echo $inventory_price ?> )
                            </label>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Continue" class="btn btn-success">
                        </div>
                    </form>
                </div>
            </section>

<?php

            require_once 'footer.php';
        }
    } else {
        echo "Invalid request number found";
    }
} else {
    echo "Something went wrong";
}

exit();
