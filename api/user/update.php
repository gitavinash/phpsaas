<?php

require_once LOGIN_CHECK;

if (!function_exists('clean_social_link')) {
    function clean_social_link($url)
    {
        // Remove control characters and trim
        return trim(preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $url));
    }
}

function update($connection, $data)
{
    if (!is_array($data)) {
        $data = [];
    }

    // router/request/post.php passes pre-escaped $secure strings; normalize raw strings cleanly when matching $_POST
    $getRaw = static function (string $key, $default = '') use ($connection, $data) {
        $val = $data[$key] ?? $default;
        if (!is_string($val)) {
            return is_scalar($val) ? (string) $val : (string) $default;
        }
        if (isset($_POST[$key]) && is_string($_POST[$key]) && mysqli_real_escape_string($connection, $_POST[$key]) === $val) {
            return $_POST[$key];
        }
        return $val;
    };

    $id         = trim($getRaw('id', $_GET['id'] ?? ''));
    $first_name = trim($getRaw('first_name', ''));
    $last_name  = trim($getRaw('last_name', ''));
    $mobile     = trim($getRaw('mobile', ''));
    $email      = trim($getRaw('email', ''));
    $userRole   = trim($getRaw('userRole', ''));
    $company    = $data['company'] ?? null;
    $warehouse  = $data['warehouse'] ?? null;

    $decodedId = base64_decode($id, true);
    $user_id   = ($decodedId !== false && ctype_digit((string) $decodedId))
        ? (int) $decodedId
        : (int) base64_decode($id);

    $editorParam = '';
    $editorRaw   = $_GET['editor'] ?? ($data['editor'] ?? null);
    if ($editorRaw !== null && $editorRaw !== '' && $editorRaw !== '0' && $editorRaw !== 'false') {
        $editorParam = '&editor=' . urlencode((string) $editorRaw);
    }

    $msg    = '';
    $status = 'warning';

    if ($first_name === '') {
        $msg = 'Please enter a first name';
    }

    if ($mobile === '') {
        $msg = 'Please enter a mobile number';
    }

    if ($userRole === '' || $userRole === '0') {
        $msg = 'Please select user role';
    }

    if ($user_id <= 0) {
        $status = 'error';
        $msg = 'Invalid user request received';
    }

    // Social Media Links JSON (profile_meta table, key: social_links)
    $social_links = [];
    $facebook_link = clean_social_link($getRaw('facebook_link', ''));
    $youtube_link  = clean_social_link($getRaw('youtube_link', ''));
    $twitter_link  = clean_social_link($getRaw('twitter_link', ''));
    $linkedin_link = clean_social_link($getRaw('linkedin_link', ''));
    if ($facebook_link !== '') $social_links['facebook'] = $facebook_link;
    if ($youtube_link !== '') $social_links['youtube'] = $youtube_link;
    if ($twitter_link !== '') $social_links['twitter'] = $twitter_link;
    if ($linkedin_link !== '') $social_links['linkedin'] = $linkedin_link;

    $social_links_json = json_encode($social_links, JSON_UNESCAPED_SLASHES);
    if ($social_links_json === false) {
        $social_links_json = '{}';
    }

    $hasBio   = array_key_exists('bio', $data);
    $cleanBio = $hasBio ? trim($getRaw('bio', '')) : '';

    if ($msg === '' && !empty($social_links) && mb_strlen($social_links_json, 'UTF-8') > 255) {
        $msg = 'Social media links exceed the 255 character limit';
    }

    if ($msg === '' && $hasBio && mb_strlen($cleanBio, 'UTF-8') > 255) {
        $msg = 'Bio must be 255 characters or fewer';
    }

    // User registration/update process
    if ($msg === '') {
        $escEmail      = mysqli_real_escape_string($connection, $email);
        $escMobile     = mysqli_real_escape_string($connection, $mobile);
        $escFirstName  = mysqli_real_escape_string($connection, $first_name);
        $escLastName   = mysqli_real_escape_string($connection, $last_name);
        $displayName   = trim($first_name . ' ' . $last_name);
        $escDispName   = mysqli_real_escape_string($connection, $displayName);
        $escUserRole   = mysqli_real_escape_string($connection, $userRole);
        $siteId        = (int) SITE_ID;

        $verifySql = "SELECT u.user_email, sa.site_access_meta
                      FROM " . TABLE_PREFIX . "user u
                      INNER JOIN " . TABLE_PREFIX . "site_access sa
                              ON sa.site_access_user_id = u.user_id
                             AND sa.site_access_site_id = {$siteId}
                      WHERE u.user_id = {$user_id}
                        AND u.user_email = '{$escEmail}'
                        AND u.user_mobile = '{$escMobile}'
                      LIMIT 1";

        if ($verifyUser = mysqli_query($connection, $verifySql)) {
            if (mysqli_num_rows($verifyUser) != 0) {
                $existingRow = mysqli_fetch_assoc($verifyUser);
                mysqli_query($connection, "START TRANSACTION");

                try {
                    $updateProfileQuery = mysqli_query(
                        $connection,
                        "UPDATE " . TABLE_PREFIX . "profile
                         SET profile_first_name='{$escFirstName}',
                             profile_last_name='{$escLastName}',
                             profile_display_name='{$escDispName}'
                         WHERE profile_user_id='{$user_id}'"
                    );

                    // Preserve existing site_access_meta unless company is submitted (for Itinerary/Inventory management)
                    $site_access_meta = (string) ($existingRow['site_access_meta'] ?? '');
                    if (isset($company)) {
                        if (!isset($warehouse)) {
                            $warehouseVal = 'all';
                        } else {
                            $warehouseVal = ($warehouse != '') ? $warehouse : 'all';
                        }
                        $site_access_meta = json_encode([
                            'company'   => $company,
                            'warehouse' => $warehouseVal,
                        ]);
                        if ($site_access_meta === false) {
                            $site_access_meta = '';
                        }
                    }
                    $escSiteAccessMeta = mysqli_real_escape_string($connection, $site_access_meta);

                    $updateUserAccessQuery = mysqli_query(
                        $connection,
                        "UPDATE " . TABLE_PREFIX . "site_access
                         SET site_access_type='{$escUserRole}',
                             site_access_meta='{$escSiteAccessMeta}'
                         WHERE site_access_user_id='{$user_id}'
                           AND site_access_site_id={$siteId}"
                    );

                    $metaKey      = 'social_links';
                    $socialMetaOk = true;
                    if (!empty($social_links)) {
                        $escSocialJson = mysqli_real_escape_string($connection, $social_links_json);
                        $checkMeta     = mysqli_query($connection, "SELECT profile_meta_id FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}'");
                        if ($checkMeta && mysqli_num_rows($checkMeta) > 0) {
                            $socialMetaOk = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile_meta SET profile_meta_value='{$escSocialJson}' WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}'");
                        } else {
                            $socialMetaOk = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile_meta (profile_meta_profile_id, profile_meta_key, profile_meta_value) VALUES ('{$user_id}', '{$metaKey}', '{$escSocialJson}')");
                        }
                    } else {
                        $socialMetaOk = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$metaKey}'");
                    }

                    // Bio Meta (profile_meta table, key: bio)
                    $bioMetaOk = true;
                    if ($hasBio) {
                        $bioKey = 'bio';
                        if ($cleanBio !== '') {
                            $escBio       = mysqli_real_escape_string($connection, $cleanBio);
                            $checkBioMeta = mysqli_query($connection, "SELECT profile_meta_id FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$bioKey}'");
                            if ($checkBioMeta && mysqli_num_rows($checkBioMeta) > 0) {
                                $bioMetaOk = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "profile_meta SET profile_meta_value='{$escBio}' WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$bioKey}'");
                            } else {
                                $bioMetaOk = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile_meta (profile_meta_profile_id, profile_meta_key, profile_meta_value) VALUES ('{$user_id}', '{$bioKey}', '{$escBio}')");
                            }
                        } else {
                            $bioMetaOk = mysqli_query($connection, "DELETE FROM " . TABLE_PREFIX . "profile_meta WHERE profile_meta_profile_id='{$user_id}' AND profile_meta_key='{$bioKey}'");
                        }
                    }

                    if ($updateProfileQuery && $updateUserAccessQuery && $socialMetaOk && $bioMetaOk) {
                        $status = 'success';
                        $msg    = 'User update successfully';
                        mysqli_query($connection, "COMMIT");
                    } else {
                        $status = 'error';
                        $msg    = 'Something went wrong while updating the user';
                        mysqli_query($connection, "ROLLBACK");
                    }
                } catch (\Throwable $e) {
                    @mysqli_query($connection, "ROLLBACK");
                    $status = 'error';
                    $msg    = 'Something went wrong while updating the user';
                }
            } else {
                $status = 'error';
                $msg    = 'Invalid user request received';
            }
        } else {
            $status = 'error';
            $msg    = 'Something went wrong while verifying the user';
        }
    }

    $msg = urlencode($msg);

    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }

    $encodedId = urlencode(base64_encode((string) $user_id));

    if (SITE_DASHBOARD_VERSION == 1) {
        header("Location: /dashboard/user/?type=edit{$editorParam}&id={$encodedId}&responseMsg={$msg}", true, 302);
    } else {
        header("Location: /dashboard/user/edit/?id={$encodedId}&responseMsg={$msg}&status={$status}", true, 302);
    }

    exit();
}