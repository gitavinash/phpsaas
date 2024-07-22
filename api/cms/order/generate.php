<?php

function generate($connection, $data)
{

    extract($data);

    $responseCode = 500;
    $enquiryId = base64_decode($request_id);

    if ($amount != 0) {
        mysqli_query($connection, "START TRANSACTION");

        // Validate request id

        $checkRequest = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='{$enquiryId}' AND enquiry_status=0 AND enquiry_site_id=" . SITE_ID);

        if (mysqli_num_rows($checkRequest) != 0) {
            extract(mysqli_fetch_assoc($checkRequest));

            // Generate new order

            mysqli_query($connection, "START TRANSACTION");

            $userData = mysqli_query($connection, "SELECT profile_user_id as userId, profile_display_name, profile_email as enquiry_email, profile_mobile as enquiry_mobile FROM " . TABLE_PREFIX . "profile WHERE profile_email='{$enquiry_email}' OR profile_mobile='{$enquiry_mobile}'");

            if (mysqli_num_rows($userData) == 0) {

                /** Start the user registration process */

                $password = time();

                /* Encrypt password with salt key */

                $saltKey = hash('sha256', uniqid(mt_rand(), true) . $enquiry_email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                $enc_password = hash('sha256', $saltKey . $password);

                $profile_display_name = $enquiry_first_name . ' ' . $enquiry_last_name;

                /** Checking Username is avilable or not */

                $username = strtolower(str_replace(' ', '', $enquiry_first_name . $enquiry_last_name));

                if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
                    if (mysqli_num_rows($checkUsername) != 0) {
                        $username = strtolower($enquiry_last_name) . time();
                    }
                }

                mysqli_query($connection, "START TRANSACTION");

                /* Insert data into user table */

                $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$enquiry_email}', '{$enquiry_mobile}', '{$enc_password}', '$saltKey', 1, NOW(), '" . IP . "')");
                $userId = mysqli_insert_id($connection);

                $insertSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(" . SITE_ID . ", '{$userId}', 'customer', 1)");

                $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_last_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userId}', '{$enquiry_first_name}', '{$enquiry_last_name}', '{$enquiry_mobile}', '{$profile_display_name}', '{$enquiry_last_name}')");

                if ($insertUser && $insertSiteAccess && $insertProfile) {
                    $status = 'success';
                    $msg = 'You are successfully registered';
                } else {
                    echo mysqli_error($connection);
                }
            } else {

                extract(mysqli_fetch_assoc($userData));
            }

            // Create new order

            $addNewOrder = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "order(order_site_id, order_user_id, order_cart_session_id, order_total_price, order_status, order_generate) VALUES(" . SITE_ID . ", '{$userId}', '" . SESSION_ID . "', '{$amount}', 0, NOW())");
            $orderId = mysqli_insert_id($connection);

            $createNewCart = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "cart(cart_session_id, cart_site_id, cart_relation_id, cart_relation_type, cart_qty, cart_price, cart_status, cart_generated) VALUES('" . SESSION_ID . "', " . SITE_ID . ", '{$orderId}', 'link', '1', '{$amount}', 0, NOW())");

            $initiateTransaction = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "transaction(transaction_site_id, transaction_user_id, transaction_order_id, transaction_method, transaction_gateway, transaction_currency, transaction_amount, transaction_type, transaction_status) VALUES(" . SITE_ID . ", '{$userId}', '{$orderId}', 'online', '" . PG_NAME . "', '{$currency}', '{$amount}', 'link', 0)");
            $transaction_id = base64_encode(mysqli_insert_id($connection));

            $redirectUrl = ((isset($redirectUrl)) ? $redirectUrl : SITE_DOMAIN . '/thank-you/');

            if ($addNewOrder && $createNewCart && $initiateTransaction) {
                mysqli_query($connection, "COMMIT");
                $redirectUrl = SITE_URL . '/payment/generate/?id=' . $transaction_id . '&pg_type=link&orderType=link&redirect=' . $redirectUrl;
                $responseCode = 201;
                $status = 'success';
                $msg = 'Redirect to payment gateway';
            } else {
                mysqli_query($connection, "ROLLBACK");
                $status = 'error';
                $msg = 'Something went wrong while trying to redirect to payment gateway';
            }
        } else {
            $msg = 'Invalid request id found';
        }
    } else {
        $msg = 'Amount must be greater than 0';
    }

    http_response_code($responseCode);
    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Invalid response'),
        'url' => ((isset($redirectUrl)) ? $redirectUrl : '#')
    );

    echo json_encode($result, TRUE);
    exit();
}