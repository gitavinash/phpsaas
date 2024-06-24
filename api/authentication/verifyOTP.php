<?php

function verifyOTP($connection, $data)
{

    extract($data);

    $otp_id = base64_decode($otp_id);

    if ($checkOtpId = mysqli_query($connection, "SELECT otp_id, otp_user_id, otp_code, otp_status, otp_expire FROM " . TABLE_PREFIX . "otp WHERE otp_id='{$otp_id}'")) {

        if (mysqli_num_rows($checkOtpId) != 0) {

            extract(mysqli_fetch_assoc($checkOtpId));

            if (time() <= strtotime($otp_expire)) {

                if ($otp_status == 0) {

                    if ($otp_code == $otp) {

                        if ($updateOTP = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "otp SET otp_status=1 WHERE otp_id='{$otp_id}'")) {

                            if ($checkUser = mysqli_query($connection, "SELECT user_id, user_password, user_salt_key, user_status, user_type_code as user_type, site_app_id, profile_display_name, profile_image, site_access_site_id, site_access_meta, site_access_status FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON user_id=profile_user_id INNER JOIN " . TABLE_PREFIX . "site_access ON site_access_user_id=user_id INNER JOIN " . TABLE_PREFIX . "site ON site_id=site_access_site_id INNER JOIN " . TABLE_PREFIX . "user_type ON user_type_code=site_access_type WHERE user_mobile='{$mobile}' AND site_access_site_id=" . SITE_ID . " LIMIT 1")) {

                                if (mysqli_num_rows($checkUser) != 0) {

                                    extract(mysqli_fetch_assoc($checkUser));

                                    /** User Session Data */

                                    ob_start();

                                    setcookie('user_name', base64_encode($profile_display_name), time() + 30 * 24 * 60 * 60, '/');
                                    setcookie('user_image', base64_encode($profile_image), time() + 30 * 24 * 60 * 60, '/');
                                    setcookie('user_data', base64_encode($user_salt_key), time() + 30 * 24 * 60 * 60, '/');
                                    setcookie('user_meta', base64_encode($site_access_meta), time() + 30 * 24 * 60 * 60, '/');
                                    setcookie('user_type', base64_encode($user_type), time() + 30 * 24 * 60 * 60, '/');
                                    setcookie('site_app', base64_encode(SITE_APP), time() + 30 * 24 * 60 * 60, '/');

                                    ob_end_flush();

                                    if ($site_access_status == 1) {

                                        $status = 200;
                                        $msg = 'Login Successfully';
                                        $url = '/dashboard/';
                                    } else {

                                        $status = 'warning';
                                        $msg = 'Your account was deactivated';
                                    }
                                } else {

                                    if (isset($redirectUrl)) {

                                        /** Redirect to registrer page */

                                        $status = 200;
                                        $url = $redirectUrl . '?mobile=' . $mobile;
                                        $msg = 'New user found';
                                    } else {

                                        /** Start Registration process into current website */

                                        if (mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(" . SITE_ID . ", '{$otp_user_id}', 'member', 1)")) {

                                            $status = 200;
                                            $msg = 'Login Successfully';
                                            $url = '/dashboard/';
                                        } else {

                                            $msg = 'Something is wrong while registrating into app';
                                        }
                                    }
                                }
                            } else {

                                $msg = 'Error while searching the user';
                            }
                        } else {

                            $msg = 'Something is wrong while verifying OTP';
                        }
                    } else {

                        $msg = 'Invalid OTP';
                    }
                } else {

                    $status = 'warning';
                    $msg = 'OTP is already verfied';
                }
            } else {

                $msg = 'OTP is expired';
            }
        } else {

            $msg = 'Invalid OTP transaction found';
        }
    } else {

        $msg = 'Technical error';
    }

    $result = array(
        'status' => ((isset($status)) ? $status : 'error'),
        'msg' => ((isset($msg)) ? $msg : ''),
        'url' => ((isset($url)) ? $url : '')
    );

    echo json_encode($result, true);
}