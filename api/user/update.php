<?php

require_once LOGIN_CHECK;
function update($connection, $data)
{

    extract($data);
    $user_id = base64_decode($id);

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

                // Social Media Links JSON (profile_meta table, key: social_links)
                function clean_social_link($url)
                {
                    // Remove control characters and trim
                    return trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $url));
                }
                $social_links = array();
                if (!empty($facebook_link)) $social_links['facebook'] = clean_social_link($facebook_link);
                if (!empty($youtube_link)) $social_links['youtube'] = clean_social_link($youtube_link);
                if (!empty($twitter_link)) $social_links['twitter'] = clean_social_link($twitter_link);
                if (!empty($linkedin_link)) $social_links['linkedin'] = clean_social_link($linkedin_link);

                $social_links_json = json_encode($social_links, TRUE);
                $metaKey = 'social_links';
                $socialMetaOk = true;
                if (!empty($social_links)) {
                    $checkMeta = mysqli_query($connection, "SELECT profile_meta_id FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}'");
                    if ($checkMeta && mysqli_num_rows($checkMeta) > 0) {
                        // Update
                        $socialMetaOk = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile_meta SET profile_meta_value='" . mysqli_real_escape_string($connection, $social_links_json) . "' WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}'");
                    } else {
                        // Insert
                        $socialMetaOk = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile_meta (profile_meta_profile_id, profile_meta_key, profile_meta_value) VALUES ('{$user_id}', '{$metaKey}', '" . mysqli_real_escape_string($connection, $social_links_json) . "')");
                    }
                } else {
                    // If all links are empty, optionally remove the meta row
                    $socialMetaOk = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}'");
                }

                // Optional: Fix existing bad JSON for this user (one-time repair)
                /*
                $fixQuery = mysqli_query($connection, "SELECT profile_meta_id, profile_meta_value FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}'");
                if ($fixQuery && mysqli_num_rows($fixQuery) > 0) {
                    $fixRow = mysqli_fetch_assoc($fixQuery);
                    $raw = $fixRow['profile_meta_value'];
                    $clean = preg_replace('/[\x00-\x1F\x7F]/u', '', $raw);
                    $arr = json_decode($clean, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
                        $fixedJson = json_encode($arr, JSON_UNESCAPED_SLASHES);
                        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile_meta SET profile_meta_value='" . mysqli_real_escape_string($connection, $fixedJson) . "' WHERE profile_meta_id='{$fixRow['profile_meta_id']}'");
                    }
                }
                */

                if ($updateProfileQuery && $updateUserAccessQuery && $socialMetaOk) {
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

    if (SITE_DASHBOARD_VERSION == 1) {
        header("Location: /dashboard/user/?type=edit&id=" . base64_encode($user_id) . "&responseMsg={$msg}", TRUE, 301);
    } else {
        header("Location: /dashboard/user/edit/?id=" . base64_encode($user_id) . "&responseMsg={$msg}&status={$status}", TRUE, 301);
    }

    exit();
}