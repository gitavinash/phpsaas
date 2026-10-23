<?php

/**
 * Resolve verification template path with fallback to system default template
 *
 * Checks in order:
 * 1. Local theme dedicated file (USER_PATH)
 * 2. System theme file (SYSTEM_PATH)
 * 3. System visitor theme file (SYSTEM_VISITOR_PATH)
 * 4. Admin theme file (ADMIN_PATH)
 * 5. Device-specific system default template (DEFAULT_VISITOR_PATH)
 * 6. Desktop system default template fallback
 * 7. Default user template (DEFAULT_USER_PATH)
 *
 * @param string|array $fileNames Single template filename or list of fallback filenames
 * @return string|null Absolute file path if found, or null
 */
function resolveVerifyTemplate($fileNames)
{
    $names = is_array($fileNames) ? $fileNames : [$fileNames];

    foreach ($names as $fileName) {
        $candidatePaths = [];

        if (defined('USER_PATH')) {
            $candidatePaths[] = USER_PATH . $fileName;
        }

        if (defined('SYSTEM_PATH')) {
            $candidatePaths[] = SYSTEM_PATH . $fileName;
        }

        if (defined('SYSTEM_VISITOR_PATH')) {
            $candidatePaths[] = SYSTEM_VISITOR_PATH . $fileName;
        }

        if (defined('ADMIN_PATH')) {
            $candidatePaths[] = ADMIN_PATH . $fileName;
        }

        if (defined('DEFAULT_VISITOR_PATH')) {
            $candidatePaths[] = DEFAULT_VISITOR_PATH . $fileName;
        }

        if (defined('ROOT')) {
            $candidatePaths[] = ROOT . '/app/system/visitor/default/desktop/' . $fileName;
        }

        if (defined('DEFAULT_USER_PATH')) {
            $candidatePaths[] = DEFAULT_USER_PATH . $fileName;
        }

        foreach ($candidatePaths as $path) {
            if (!empty($path) && file_exists($path)) {
                return $path;
            }
        }
    }

    return null;
}

/** User email verification */

function email($connection, $data)
{

    extract($data);
    global $table_prefix;

    $rawEmail = isset($email) ? $email : '';
    $decodedEmail = base64_decode($rawEmail);
    $userId = isset($user_id) ? $user_id : '';

    $message = 'Something went wrong';

    if (!empty($decodedEmail) && !empty($userId) && ($selectUser = mysqli_query($connection, "SELECT user_id, user_status FROM " . TABLE_PREFIX . "user WHERE user_email='{$decodedEmail}' AND user_salt_key='{$userId}'"))) {
        if (mysqli_num_rows($selectUser) != 0) {

            extract(mysqli_fetch_assoc($selectUser));

            if ($user_status == 0) {

                if (mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "user SET user_status=1, user_act_time=NOW() WHERE user_id='{$user_id}'")) {

                    $message = 'Thank you for verifying your email';
                } else {

                    $message = 'Something went wrong while verification';
                }
            } else {

                $message = 'Email is already verified';
            }
        } else {

            $message = 'Invalid verification request';
        }
    } else {

        $message = 'Invalid verification request';
    }

    $template = resolveVerifyTemplate(['email.php', 'verify-account.php', 'verify.php']);
    if ($template) {
        require_once $template;
        exit();
    }

    echo $message;
}

/** Reset Password */

function password($connection, $data)
{

    extract($data);
    global $table_prefix;

    $rawEmail = isset($email) ? $email : '';
    $decodedEmail = base64_decode($rawEmail);
    $transaction = isset($transaction) ? $transaction : '';

    // Ensure $_GET has transaction & raw base64 email for the template forms
    if (!isset($_GET['transaction']) && !empty($transaction)) {
        $_GET['transaction'] = $transaction;
    }
    if (!isset($_GET['email']) && !empty($rawEmail)) {
        $_GET['email'] = $rawEmail;
    }

    if (!empty($decodedEmail) && !empty($transaction) && ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$transaction}' AND user_email='{$decodedEmail}'"))) {

        if (mysqli_num_rows($checkUser) != 0) {

            $template = resolveVerifyTemplate('update-password.php');
            if ($template) {
                require_once $template;
                exit();
            } else {
                echo 'Password update template not found.';
                exit();
            }
        } else {

            $message = 'Invalid or expired password reset link. Please request a new password reset.';

            $template = resolveVerifyTemplate(['verify.php', 'email.php']);
            if ($template) {
                require_once $template;
                exit();
            } else {
                echo $message;
                exit();
            }
        }
    } else {
        $message = 'Invalid password reset request.';

        $template = resolveVerifyTemplate(['verify.php', 'email.php']);
        if ($template) {
            require_once $template;
            exit();
        } else {
            echo $message;
            exit();
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