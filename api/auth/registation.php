<?php

require_once COMMUNICATION_FUNCTIONS;
$ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
if (file_exists($ssoFile)) {
    require_once $ssoFile;
}
header('Content-Type: application/json');

// ==== Initialize Response ====
$response = [
    'status' => 'error',
    'msg' => 'Invalid request',
    'id' => 0
];
$httpStatus = 400;

// ==== Validate POST Request ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $expiryDate = ((isset($expiryDate)) ? $expiryDate : date('Y-m-d', strtotime('+1 year')));
    $userIdIdentifier = isset($user_id) ? trim((string) $user_id) : '';
    $escUidIdent = mysqli_real_escape_string($connection, $userIdIdentifier);

    if ($userIdIdentifier === '') {
        $response['msg'] = 'Missing user identifier';
    } else {
        $findMode = isset($email) ? 'mobile' : (isset($mobile) ? 'email' : 'email_or_mobile');
        $lookup = function_exists('iswFindUserAndSiteAccessByIdentifier')
            ? iswFindUserAndSiteAccessByIdentifier($connection, $userIdIdentifier, (int) SITE_ID, $findMode)
            : ['reason' => 'no_access', 'resolved' => null];

        if (empty($lookup['resolved']) || empty($lookup['resolved']['has_access'])) {
            $httpStatus = 403;
            $response['msg'] = (($lookup['reason'] ?? '') === 'disabled')
                ? 'Your workspace access has been deactivated.'
                : 'You do not have access to this workspace.';
        } else {
            $userID = (int) ($lookup['resolved']['user']['user_id'] ?? 0);
            $fullNameStr = isset($full_name) ? trim((string) $full_name) : '';
            $displayName = explode(' ', $fullNameStr);
            $first_name = mysqli_real_escape_string($connection, (string) ($displayName[0] ?? ''));
            $last_name  = mysqli_real_escape_string($connection, (string) ($displayName[1] ?? ''));
            $escFullName = mysqli_real_escape_string($connection, $fullNameStr);

            mysqli_query($connection, "START TRANSACTION");

            if (isset($email)) {
                $escEmail = mysqli_real_escape_string($connection, trim((string) $email));
                $updateUserQuery = "UPDATE " . TABLE_PREFIX . "user SET user_email='{$escEmail}', user_status=1 WHERE user_id={$userID}";
                $updateProfileQuery = "UPDATE " . TABLE_PREFIX . "profile SET profile_display_name='{$escFullName}', profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_email='{$escEmail}', profile_email_status=1 WHERE profile_user_id={$userID}";
            } elseif (isset($mobile)) {
                $escMobile = mysqli_real_escape_string($connection, trim((string) $mobile));
                $updateUserQuery = "UPDATE " . TABLE_PREFIX . "user SET user_mobile='{$escMobile}', user_status=1 WHERE user_id={$userID}";
                $updateProfileQuery = "UPDATE " . TABLE_PREFIX . "profile SET profile_display_name='{$escFullName}', profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_mobile='{$escMobile}', profile_mobile_status=1 WHERE profile_user_id={$userID}";
            } else {
                $updateUserQuery = '';
                $updateProfileQuery = '';
            }

            if ($updateUserQuery !== '' && $updateProfileQuery !== '') {
                $updateUser = mysqli_query($connection, $updateUserQuery);
                $updateProfile = mysqli_query($connection, $updateProfileQuery);

                if ($updateProfile && $updateUser) {
                    $httpStatus = 201;
                    $response['status'] = 'success';
                    $response['msg'] = "Registration completed successfully.";
                    mysqli_query($connection, "COMMIT");
                } else {
                    $response['msg'] = ((DEVELOPMENT_MODE == true) ? mysqli_error($connection) : 'Something went wrong');
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {
                mysqli_query($connection, "ROLLBACK");
                $response['msg'] = 'Missing email or mobile parameter';
            }
        }
    }
} else {
    $response['msg'] = 'Invalid request method';
}

// ==== Output Response ====
if (isset($showOutput)) {
    if ($showOutput == true) {
        http_response_code($httpStatus);
        echo json_encode($response);
    }
} else {
    http_response_code($httpStatus);
    echo json_encode($response);
}