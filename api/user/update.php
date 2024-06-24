<?php

require_once LOGIN_CHECK;
function update($connection, $data)
{

    extract($data);
    $user_id = base64_decode($user_id);

    $msg = '';
    $status = 'warning';

    if ($first_name == '') {

        $msg = 'Please enter a first name';
    }

    if ($mobile == '') {

        $msg = 'Please enter a mobile number';
    }

    if ($userRole == '') {

        $msg = 'Please select user role';
    }

    // User registraion process

    if ($msg == '') {

        if ($verifyUser = mysqli_query($connection, "SELECT user_email FROM " . TABLE_PREFIX . "user WHERE user_id='{$user_id}' AND user_email='{$email}' AND user_mobile='{$mobile}'")) {

            if (mysqli_num_rows($verifyUser) != 0) {

                mysqli_query($connection, "START TRANSACTION");

                $displayName = $first_name . ' ' . $last_name;

                $updateProfileQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile SET profile_first_name='{$first_name}', profile_last_name='{$last_name}', profile_display_name='{$displayName}' WHERE profile_user_id='{$user_id}'");

                $site_access_meta = '';

                // For Intenary management

                if (isset($company)) {
                    if (!isset($warehouse)) {
                        $warehouse = 'all';
                    } else {
                        $warehouse = (($warehouse != '') ? $warehouse : 'all');
                    }
                    $site_access_meta = json_encode(array(
                        'company' => $company,
                        'warehouse' => $warehouse
                    ), TRUE);
                }

                $updateUserAccessQuery = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_access SET site_access_type='{$userRole}', site_access_meta='{$site_access_meta}' WHERE site_access_user_id='{$user_id}' AND site_access_site_id=" . SITE_ID);

                if ($updateProfileQuery && $updateUserAccessQuery) {
                    $status = 'success';
                    $msg = 'User update successfully';
                    mysqli_query($connection, "COMMIT");
                } else {
                    $status = 'error';
                    $msg = 'Something went wrong while updating the user';
                    mysqli_query($connection, "ROLLBACK");
                }
            } else {

                $status = 'error';
                $msg = 'Invalid user request received';
            }
        }
    }

    $msg = urlencode($msg);
    header("Location: /dashboard/user/edit/?id=" . base64_encode($user_id) . "&responseMsg={$msg}&status={$status}", TRUE, 301);

    exit();
}