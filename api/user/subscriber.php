<?php

function subscriber($connection, $data)
{

    extract($data);

    $msg = '';
    $status = 'warning';

    if ($email == '') {

        $msg = 'Please enter a valid email';
    }

    if ($name == '') {

        $msg = 'Please enter a name';
    } else {
        $fullName = explode(' ', $name);
        $first_name = $fullName[0];
        $last_name = str_replace($first_name, '', $name);
    }

    if ($mobile == '') {

        $msg = 'Please enter a mobile number';
    }

    if ($password == '') {

        $msg = 'Password cannot be blank';
    }

    if ($userRole == '') {

        $msg = 'Please select user role';
    }

    // User registraion process

    if ($msg == '') {

        if ($checkEmail = mysqli_query($connection, "SELECT user_email FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}'")) {

            if (mysqli_num_rows($checkEmail) == 0) {

                /** Check mobile number is available or not */

                if ($checkMobile = mysqli_query($connection, "SELECT profile_mobile FROM " . TABLE_PREFIX . "profile WHERE profile_mobile='{$mobile}'")) {

                    if (mysqli_num_rows($checkMobile) == 0) {

                        /** Start the user registration process */

                        /* Encrypt password with salt key */

                        $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());
                        $enc_password = hash('sha256', $saltKey . $password);

                        /** Checking Username is avilable or not */

                        $username = strtolower(str_replace(' ', '', $first_name . $last_name));

                        if ($checkUsername = mysqli_query($connection, "SELECT user_username FROM " . TABLE_PREFIX . "user WHERE user_username='{$username}'")) {
                            if (mysqli_num_rows($checkUsername) != 0) {
                                $username = strtolower($first_name) . time();
                            }
                        }

                        mysqli_query($connection, "START TRANSACTION");

                        /* Insert data into user table */

                        $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip) VALUES('{$username}', '{$email}', '{$mobile}', '{$enc_password}', '$saltKey', 1, NOW(), '" . IP . "')");
                        $userID = mysqli_insert_id($connection);

                        $insertSiteAccess = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_access(site_access_site_id, site_access_user_id, site_access_type, site_access_status) VALUES(" . SITE_ID . ", '{$userID}', '{$userRole}', 1)");

                        $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_mobile, profile_display_name, profile_email) VALUES('{$userID}', '{$name}', '{$mobile}', '{$name}', '{$email}')");

                        if ($insertUser && $insertSiteAccess && $insertProfile) {

                            $status = 'success';
                            $msg = 'You are successfully registered';
                            mysqli_query($connection, 'COMMIT');
                        } else {

                            $status = 'error';
                            $msg = 'Something is wrong while trying to register';
                            mysqli_query($connection, 'ROLLBACK');
                        }
                    } else {

                        $status = 'warning';
                        $msg = 'This mobile number is already in use';
                    }
                }
            } else {

                $status = 'warning';
                $msg = 'This email is already in use.';
            }
        }
    }

    $msg = urlencode($msg);

    if ($status === 'success') {

        if (isset($autologin)) {
            require_once API_MODULES . 'auth/autologin.php';
        } else {
            $startRedirection = true;
        }

        if ($startRedirection) {
            if (isset($baseUrl)) {
                header("Content-Type: text/html; charset=UTF-8");

?>
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Redirect to new url.</title>
                    <script>
                        window.onload = function() {
                            console.log("Form submission initiated."); // Debugging log
                            document.getElementById("autoSubmitForm").submit();
                        };
                    </script>
                </head>

                <body>
                    <form id="autoSubmitForm" action="<?php echo $baseUrl ?>" method="<?php echo $method ?>">
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <input type="hidden" name="redirect_url" value="<?php echo $hardRedirectURL ?>">
                        <noscript>
                            <p>JavaScript is disabled. Please click the submit button.</p>
                            <button type="submit">Submit</button>
                        </noscript>
                    </form>
                </body>

                </html>

<?
            } else {
                header("Location: {$hardRedirectURL}?id=" . base64_encode($userID) . "&responseMsg={$msg}", TRUE, 301);
            }
        } else {
            echo "Something went wrong is redirection -> " . $msg;
        }
    } else {
        header("Location: /signup/?responseMsg={$msg}&status={$status}", TRUE, 301);
    }

    exit();
}
