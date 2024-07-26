<?php

$activeSubscription = false;
if (isset($_COOKIE['user_data'])) {
    $userSaltKey = base64_decode($_COOKIE['user_data']);

    if ($checkUser = mysqli_query($connection, "SELECT subscription_status, subscription_relation_id, subscription_type, subscription_mode, subscription_time FROM " . TABLE_PREFIX . "user LEFT JOIN " . TABLE_PREFIX . "subscription ON user_id = subscription_user_id WHERE user_salt_key = '{$userSaltKey}' AND subscription_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkUser) != 0) {
            extract(mysqli_fetch_assoc($checkUser));
            if ($subscription_status == 1) {
                $activeSubscription = true;

                // Fetch subscription details

                if ($subsriptionDetails = mysqli_query($connection, "SELECT {$subscription_type}_image, {$subscription_type}_title, {$subscription_type}_description, {$subscription_type}_highlight, {$subscription_type}_type, inventory_currency_symbol, inventory_price, inventory_mrp FROM " . TABLE_PREFIX . "{$subscription_type} INNER JOIN " . TABLE_PREFIX . "inventory ON {$subscription_type}_id=inventory_relation_id WHERE {$subscription_type}_id='{$subscription_relation_id}' AND {$subscription_type}_type='subscription' AND {$subscription_type}_site_id=" . SITE_ID)) {
                    if (mysqli_num_rows($subsriptionDetails) != 0) {
                        extract(mysqli_fetch_assoc($subsriptionDetails));
                    }
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>about.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>manage.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>subscribe.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>footer.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>details.min.css">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>thankyou.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <?php require_once DEFAULT_META; ?>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <?php

    // if (BASE_URL == '/thank-you/') {

    //     /** Send Email */

    //     $email = PROFILE_EMAIL;
    //     require_once APP_EMAIL_TEMPLATE . 'newSubscription.php';

    //     echo "<meta http-equiv='refresh' content='10; URL=/manage-subscription/'>";
    // }

    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KN6V4VKJN4"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-KN6V4VKJN4');
    </script>
</head>

<body oncontextmenu="return false;">
    <header>
        <nav>
            <div class="myHeader <?php echo (($link_type == 'page') ? 'about_bg' : '') ?>">
                <div class="container">
                    <div class="header_flex">
                        <div class="company-logo">
                            <a href="/"><img src="<?php echo SITE_LOGO ?>" class="img-fluid" alt="logo"></a>
                        </div>
                        <?php

                        if (isset($_COOKIE['user_data'])) { ?>

                        <div class="header_right">
                            <p class="user--name"><i class='bx bx-user-circle'></i><?php echo PROFILE_DISPLAY_NAME ?>
                            </p>
                        </div>

                        <!-- profile side--bar -->
                        <div class="profile--sideBar">
                            <div class="profile_container">
                                <div class="profile_cancel">
                                    <i class='bx bx-x'></i>
                                </div>
                                <div class="profile_header">
                                    <!-- <div class="profile--img">
                                        <img src="<?php echo PROFILE_IMAGE ?>" class="img-fluid" alt="user_img">
                                    </div> -->
                                    <div class="user--content">
                                        <h6><?php echo PROFILE_DISPLAY_NAME ?></h6>
                                        <a href="#"><?php echo PROFILE_EMAIL ?></a>
                                        <a href="/logout/">Logout</a>
                                    </div>
                                </div>

                                <?php

                                    if ($activeSubscription == true) {

                                        if ($subscription_mode == 'month') {
                                            $increseDuration = "+1 month";
                                        } else {
                                            $increseDuration = "+1 year";
                                        }

                                        $expiryDate = date('d M Y', strtotime($subscription_time . $increseDuration));;

                                        echo "<div class='manage_link'>
                                            <a href='/manage-subscription/'>MANAGE MY SUBSCRIPTION</a>
                                        </div>

                                        <div class='plan_card'>
                                            <h6>YOUR CURRENT PLAN</h6>
                                            <h3>{${$subscription_type . '_title'}}</h3>
                                            <p>Next billing date : {$expiryDate}</p>
                                        </div>";
                                    } else {
                                        echo "<div class='manage_link'>
                                            <a href='/subscribe/'>BUY SUBSCRIPTION</a>
                                        </div>";
                                    }

                                    ?>

                            </div>
                        </div>

                        <?php
                        } else { ?>


                        <div class="header_right">
                            <!-- <button class="subscribe">subscribe</button> -->
                            <a href="/subscribe/" class="subscribe">subscribe</a>
                            <!-- <button class="sign_in"></button> -->
                            <a href="/login/" class="sign_in">sign in</a>
                        </div>

                        <?php
                        }

                        ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main <?php echo (($link_type == 'page') ? 'style="border-top: 0px"' : '') ?>>