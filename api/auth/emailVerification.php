<?php

/** Sending verification email module */

function emailVerification($connection, $data)
{
    $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
    if (file_exists($ssoFile)) {
        require_once $ssoFile;
    }

    extract($data);

    $rawEmail = isset($email) ? trim((string) $email) : '';
    $lookup = function_exists('iswFindUserAndSiteAccessByIdentifier')
        ? iswFindUserAndSiteAccessByIdentifier($connection, $rawEmail, (int) SITE_ID, 'email')
        : ['reason' => 'not_found', 'resolved' => null];

    if (!empty($lookup['resolved']['user'])) {
        $resolvedUser = $lookup['resolved']['user'];
        $user_salt_key = (string) ($resolvedUser['user_salt_key'] ?? '');
        $user_status = (int) ($resolvedUser['user_status'] ?? 0);
        $email = (string) ($resolvedUser['user_email'] ?? $rawEmail);

        /** Send User's Email Verification Mail */
        $verifyURL = SITE_DOMAIN . '/verify/email/?user_id=' . $user_salt_key . '&email=' . base64_encode($email);

        require_once EMAIL_TEMPLATE . 'email-verification.php';

        $status = 'success';
        $msg = 'New verification mail has been sent';
    } else {
        $status = 'warning';
        $msg = 'No Email Found';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Unknown Error')
    );

    echo json_encode($result, TRUE);
}
