<?php

/** User email verification */

function email($connection, $data)
{

    extract($data);
    global $table_prefix;

    $email = base64_decode($email); // decode email variable

    if ($selectUser = mysqli_query($connection, "SELECT user_id, user_status FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}' AND user_salt_key='{$user_id}'")) {
        if (mysqli_num_rows($selectUser) != 0) {

            extract(mysqli_fetch_assoc($selectUser));

            if ($user_status == 0) {

                if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "user SET user_status=1, user_act_time=NOW() WHERE user_id='{$user_id}'")) {

                    $message = 'Thank you for verifying your email';
                    if (file_exists(USER_PATH . 'email.php')) {

                        require_once USER_PATH . 'email.php';
                    } else {

                        require_once DEFAULT_VISITOR_PATH . 'email.php';
                    }
                } else {

                    $message = 'Something went wrong while verification';
                }
            } else {

                $message = 'Email is already verified';
            }
        } else {

            $message = '<h1>Invalid request</h1>';
        }
    } else {

        $message = 'something went wrong';
    }

    echo $message;
}

/** Reset Password */

function password($connection, $data)
{

    extract($data);
    global $table_prefix;

    $email = base64_decode($email);

    if ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$transaction}' AND user_email='{$email}'")) {

        if (mysqli_num_rows($checkUser) != 0) {

            if (file_exists(USER_PATH . 'update-password.php')) {

                require_once USER_PATH . 'update-password.php';
            } else {

                require_once SYSTEM_PATH . 'update-password.php';
            }
        } else {

            $message = 'Something is wrong';

            if (file_exists(USER_PATH . 'verify.php')) {

                require_once USER_PATH . 'verify.php';
            } else {

                require_once SYSTEM_PATH . 'verify.php';
            }
        }
    }
}

function newsletter($connection, $data)
{

    if (!isset($_GET['data'], $_GET['sig'])) {
        http_response_code(400);
        exit('Invalid request');
    }

    $data = $_GET['data'];
    $sig  = $_GET['sig'];

    // Verify signature
    $expectedSig = hash_hmac('sha256', $data, EMAIL_VERIFY_SECRET);

    if (!hash_equals($expectedSig, $sig)) {
        http_response_code(403);
        exit('Invalid signature');
    }

    // Decode payload
    $payload = json_decode(base64_decode($data), true);

    if (!$payload) {
        http_response_code(400);
        exit('Invalid payload');
    }

    // Validate purpose
    if (($payload['purpose'] ?? '') !== 'email_verify') {
        http_response_code(403);
        exit('Invalid purpose');
    }

    // Validate expiry
    if (($payload['exp'] ?? 0) < time()) {
        http_response_code(410);
        exit('Verification link expired');
    }

    $email = strtolower(trim($payload['email']));

    if ($checkEmail = mysqli_query($connection, "SELECT newsletter_id FROM " . TABLE_PREFIX . "newsletter WHERE newsletter_email='{$email}' AND newsletter_site_id='" . SITE_ID . "'")) {

        if (mysqli_num_rows($checkEmail) != 0) {

            if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "newsletter SET newsletter_status=1, newsletter_verified_at=NOW() WHERE newsletter_email='{$email}' AND newsletter_site_id='" . SITE_ID . "'")) {

                // Redirect to thank you page after successful verification
                header('Location: /thankyou_newsletter/', true, 301);
                exit();
            } else {

                echo 'Something went wrong while verifying your email.';
            }
        } else {

            echo 'No subscription found for this email.';
        }
    } else {

        echo 'Something went wrong';
    }
}