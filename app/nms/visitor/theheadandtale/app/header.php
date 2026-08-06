<?php
require_once MENU_FUNCTIONS;
require_once DATA_FUNCTIONS;

$activeSubscription = false;
if (isset($_COOKIE['user_data'])) {
    require_once LOGIN_CHECK;
    $userSaltKey = base64_decode($_COOKIE['user_data']);

    if ($checkUser = mysqli_query($connection, "SELECT subscription_status, subscription_relation_id, subscription_type, subscription_mode, subscription_time, subscription_expiry FROM " . TABLE_PREFIX . "user LEFT JOIN " . TABLE_PREFIX . "subscription ON user_id = subscription_user_id WHERE user_salt_key = '{$userSaltKey}' AND CURDATE() <= subscription_expiry AND subscription_site_id=" . SITE_ID)) {
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

    // Check user active session

    if (!in_array($user_id, [9423, 9473])) {
        if ($checkUserSession = mysqli_query($connection, "SELECT us_active_device FROM " . TABLE_PREFIX . "user_sessions WHERE us_status=1 AND us_user_id='{$user_id}' AND us_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkUserSession) > 2) {
                $showLogoutBtn = 0;
                $excluded_paths = ['/manage-subscription/', '/terms-and-conditions/', '/about-us/', '/terms-and-conditions/', '/privacy-policy/', '/refund-policy/'];
                $is_excluded = false;
                foreach ($excluded_paths as $path) {
                    if (strpos(BASE_URL, $path) !== false) {
                        $is_excluded = true;
                        break;
                    }
                }

                if (!$is_excluded) {
                    require_once 'user-suspend.php';
                    exit();
                }
            } else {
                $showLogoutBtn = 1;
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.css" />
    <?php require_once DEFAULT_META ?>
</head>

<body class="light-theme">
    <!-- STRICT APP CONTAINER -->
    <div class="app-shell">
        <!-- GLOBAL TOP AREA (Fixed) -->
        <div class="top-app-bar" id="main-app-bar">
            <!-- GLOBAL HEADER -->
            <header class="app-header">
                <div class="header-left-group">
                    <?php
                    $isNewsPage = (isset($link_type) && $link_type === 'news') || isset($news_id) || (isset($link_relation_id) && !empty($news_title));
                    if ($isNewsPage) {
                        echo "<a href='javascript:history.back()' onclick='if(history.length > 1) { history.back(); return false; } else { window.location.href=\"/\"; }' class='icon-btn back-btn' aria-label='Back'><i class='ph ph-arrow-left'></i></a>";
                    }
                    ?>
                    <a href="/" class="logo">
                        <img
                            src="<?php echo SITE_LOGO ?>"
                            alt="<?php echo SITE_NAME ?>"
                            class="logo-img" />
                    </a>
                </div>
            </header>
        </div>

        <!-- MAIN SCROLLABLE CONTENT AREA -->
        <main class="app-body" id="main-content">