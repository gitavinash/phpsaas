<?php

/** Update Password Module */

function updatePassword($connection, $data)
{
    $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
    if (file_exists($ssoFile)) {
        require_once $ssoFile;
    }

    extract($data);

    $pass = isset($pass) ? (string) $pass : '';
    $cnf_pass = isset($cnf_pass) ? (string) $cnf_pass : '';
    $transaction = isset($transaction) ? trim((string) $transaction) : '';
    $rawEmailParam = isset($email) ? trim((string) $email) : '';

    if ($pass == '' || $cnf_pass == '') {

        $msg = 'Please enter your password';
    } elseif (strcmp($pass, $cnf_pass)) {

        /** Check both password is match or not */
        $msg = 'Your Confirm Password is not matched';
    } else {

        $decodedEmail = base64_decode($rawEmailParam);
        $escEmail = mysqli_real_escape_string($connection, (string) $decodedEmail);
        $escTransaction = mysqli_real_escape_string($connection, $transaction);

        if ($escEmail !== '' && $escTransaction !== '' && ($checkUser = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_salt_key='{$escTransaction}' AND user_email='{$escEmail}' LIMIT 1"))) {

            if (mysqli_num_rows($checkUser) != 0) {

                extract(mysqli_fetch_assoc($checkUser));
                $uidInt = (int) $user_id;
                $resolvedAccess = function_exists('iswResolveSiteAccessForUser')
                    ? iswResolveSiteAccessForUser($connection, $uidInt, (int) SITE_ID)
                    : null;

                if (!$resolvedAccess || empty($resolvedAccess['has_access'])) {
                    $status = 'error';
                    $msg = (($resolvedAccess['reason'] ?? '') === 'disabled')
                        ? 'Your workspace access has been deactivated.'
                        : 'You do not have access to this workspace.';
                } else {
                    $dbPassword = hash('sha256', $transaction . $cnf_pass);

                    if ($updatePassword = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "user SET user_password='{$dbPassword}' WHERE user_id='{$uidInt}' AND user_email='{$escEmail}' AND user_salt_key='{$escTransaction}'")) {

                        $siteIds = [(int) SITE_ID];
                        if (function_exists('iswGetParentGroupInfo')) {
                            $groupInfo = iswGetParentGroupInfo($connection, (int) SITE_ID);
                            if (!empty($groupInfo['site_ids'])) {
                                $siteIds = $groupInfo['site_ids'];
                            }
                        }
                        if (in_array((int) SITE_ID, [49, 234], true)) {
                            $siteIds = array_unique(array_merge($siteIds, [49, 234]));
                        }
                        $idsSql = implode(',', array_map('intval', $siteIds));

                        if (function_exists('iswSsoEnsureSchema') && iswSsoEnsureSchema($connection)) {
                            @mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "sso_tokens WHERE sso_user_id={$uidInt}");
                        }

                        if ($destroySession = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id='{$uidInt}' AND us_site_id IN ({$idsSql})")) {
                            $status = 'success';
                            $msg = "Password updated successfully";
                            $email = $decodedEmail;

                            $passwordResetURL = SITE_URL . '/verify/password/?transaction=' . $transaction . '&email=' . base64_encode($decodedEmail);
                            require_once EMAIL_TEMPLATE . 'update-password-notification.php';
                        } else {
                            $msg = "Something went wrong while updating sessions";
                        }
                    } else {

                        $msg = 'Something is wrong while updating your password. Kindly contact to Admin';
                    }
                }
            } else {

                $msg = 'Invalid Action found';
            }
        }
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : 'Unable to update your password')
    );

    echo json_encode($result, TRUE);
}