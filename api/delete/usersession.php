<?php

function usersession($connection, $data)
{
    $ssoFile = defined('MODULES') ? MODULES . 'sso.php' : dirname(__DIR__, 2) . '/lib/modules/sso.php';
    if (file_exists($ssoFile)) {
        require_once $ssoFile;
    }

    extract($data);

    if (isset($id)) {

        $decodedUserId = (int) base64_decode($id);
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
        $siteIdsSql = implode(',', array_map('intval', $siteIds));

        if ($checkUserSession = mysqli_query($connection, "SELECT us_user_id FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id='{$decodedUserId}' AND us_site_id IN ({$siteIdsSql})")) {

            if (mysqli_num_rows($checkUserSession) != 0) {

                extract(mysqli_fetch_assoc($checkUserSession));

                if ($deleteUserSession = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "user_sessions WHERE us_user_id='{$decodedUserId}' AND us_site_id IN ({$siteIdsSql})")) {
                    if (function_exists('iswSsoEnsureSchema') && iswSsoEnsureSchema($connection)) {
                        @mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "sso_tokens WHERE sso_user_id='{$decodedUserId}'");
                    }
                    $status = 200;
                    $msg = 'User session delete successfully';
                    mysqli_query($connection, "COMMIT");
                } else {

                    $msg = 'Something is wrong while deleting user session';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $msg = 'Invalid request found';
            }
        } else {

            $msg = 'Technical error';
        }
    } else {

        $msg = 'Something is wrong while sending request to server';
    }

    $result = array(
        'status' => (isset($status)) ? $status : 'error',
        'msg' => ((isset($msg)) ? $msg : 'Invalid response')
    );

    echo json_encode($result, TRUE);
}