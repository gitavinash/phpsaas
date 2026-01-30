<?php

require_once LOGIN_CHECK;

$msg = 'Invalid request recevied';
$icon = 'error';
$title = 'Error';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($name == '') {
        $msg = 'Name is required';
    } elseif ($email == '') {
        $msg = 'Email is required';
    } else {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $msg = 'Invalid email format';
        } else {
            mysqli_query($connection, "START TRANSACTION");

            if ($checkLimit = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "corporate_subscription WHERE cp_admin_user_id='" . USER_ID . "' AND cp_used<cp_limit AND cp_site_id='" . SITE_ID . "'")) {
                if (mysqli_num_rows($checkLimit) > 0) {
                    extract(mysqli_fetch_assoc($checkLimit));
                    // Check if email already exists

                    $checkEmail = mysqli_query($connection, "SELECT user_id FROM " . TABLE_PREFIX . "user WHERE user_email='{$email}'");
                    if (mysqli_num_rows($checkEmail) > 0) {
                        $msg = 'This email is already registered';
                        extract(mysqli_fetch_assoc($checkEmail));
                        $insertUser = true;
                        $insertProfile = true;
                    } else {
                        $saltKey = hash('sha256', uniqid(mt_rand(), true) . $email . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12) . time());

                        $insertUser = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "user(user_username, user_email, user_mobile, user_salt_key, user_status) VALUES (NOW(), '{$email}', NOW(), '{$saltKey}', 0)");
                        $user_id = mysqli_insert_id($connection);

                        $insertProfile = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "profile(profile_user_id, profile_first_name, profile_display_name, profile_email) VALUES ({$user_id}, '{$name}', '{$name}', '{$email}')");
                    }

                    // Add Subscriiption

                    if ($checkSubscription = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "subscription WHERE subscription_user_id='{$user_id}' AND subscription_site_id=" . SITE_ID)) {
                        if (mysqli_num_rows($checkSubscription) > 0) {
                            $msg = 'This email is already subscribed';
                        } else {
                            $insertSubscription = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "subscription(subscription_user_id, subscription_refer_by, subscription_site_id, subscription_status, subscription_expiry) VALUES ({$user_id}, " . USER_ID . "," . SITE_ID . ", 1, '{$cp_expiry}')");

                            $deductCredit = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "corporate_subscription SET cp_used=cp_used+1 WHERE cp_admin_user_id='" . USER_ID . "' AND cp_site_id=" . SITE_ID . " AND cp_used<cp_limit");

                            if ($insertUser && $insertProfile && $insertSubscription && $deductCredit) {
                                mysqli_query($connection, "COMMIT");
                                $msg = 'Subscriber added successfully';
                                $icon = 'success';
                                $title = 'Success';
                            } else {
                                mysqli_query($connection, "ROLLBACK");
                                $msg = 'Failed to add subscriber. Please try again.';
                            }
                        }
                    } else {
                        mysqli_query($connection, "ROLLBACK");
                        $msg = 'Database error: Unable to check existing subscriptions';
                    }
                } else {
                    $msg = 'No active subscription found. Please subscribe to add corporate subscribers.';
                }
            } else {
                $msg = 'Database error: Unable to check subscription limit';
                mysqli_query($connection, "ROLLBACK");
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Corporate Subscriber</title>
    <!-- SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <h1>Please wait...</h1>
    <script>
    // Display SweetAlert popup if message is set
    <?php if (isset($msg) && !empty($msg)): ?>
    Swal.fire({
        icon: '<?php echo htmlspecialchars($icon ?? 'error'); ?>',
        title: '<?php echo htmlspecialchars($title ?? 'Alert'); ?>',
        text: '<?php echo htmlspecialchars($msg); ?>',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        allowOutsideClick: false
    }).then(() => {
        window.location.href = '/manage-subscribers/';
    });
    <?php endif; ?>
    </script>

</body>

</html>