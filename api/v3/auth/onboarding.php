<?php

/**
 * V3 Authentication API - App-Specific Onboarding & Workspace Provisioning
 * Path: /api/v3/auth/onboarding.php
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

// Support GET to retrieve onboarding profile information (e.g. pre-registered agency name)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = strtolower(trim($_GET['setup_email'] ?? ($_GET['email'] ?? '')));
    if (empty($email) && !empty($_GET['setup_id'])) {
        $decoded = base64_decode($_GET['setup_id']);
        if ($decoded && filter_var($decoded, FILTER_VALIDATE_EMAIL)) {
            $email = strtolower(trim($decoded));
        }
    }
    if (empty($email) && !empty($_SESSION['user_email'])) {
        $email = strtolower(trim($_SESSION['user_email']));
    } elseif (empty($email) && !empty($_COOKIE['user_email'])) {
        $decodedCookie = base64_decode($_COOKIE['user_email']);
        if ($decodedCookie && filter_var($decodedCookie, FILTER_VALIDATE_EMAIL)) {
            $email = strtolower(trim($decodedCookie));
        }
    }

    $bizName = '';
    $siteDomain = '';
    $userName = '';
    $appCode = 'aleya';

    if (!empty($email)) {
        $escEmail = mysqli_real_escape_string($connection, $email);
        $userQ = mysqli_query($connection, "
            SELECT u.user_id, p.profile_display_name, s.site_name, s.site_domain, s.site_app_id
            FROM {$tblPrefix}user u
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            LEFT JOIN {$tblPrefix}site s ON s.site_owner_id = u.user_id AND s.site_id > 1
            WHERE u.user_email = '{$escEmail}'
            ORDER BY s.site_id DESC
            LIMIT 1
        ");
        if ($userQ && $row = mysqli_fetch_assoc($userQ)) {
            $bizName = $row['site_name'] ?: '';
            $siteDomain = $row['site_domain'] ?: '';
            $userName = $row['profile_display_name'] ?: '';
            if ((int)$row['site_app_id'] === 20) {
                $appCode = 'aleya';
            }
        }
    }

    if (empty($bizName) && !empty($_SESSION['business_name'])) {
        $bizName = $_SESSION['business_name'];
    } elseif (empty($bizName) && !empty($_SESSION['site_name'])) {
        $bizName = $_SESSION['site_name'];
    }

    $subdomain = '';
    if (!empty($siteDomain)) {
        $parts = explode('.', $siteDomain);
        $subdomain = $parts[0] ?? '';
    }
    if (empty($subdomain) && !empty($bizName)) {
        $subdomain = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $bizName));
    }

    echo json_encode([
        'status'        => 'success',
        'email'         => $email,
        'business_name' => $bizName,
        'user_name'     => $userName,
        'subdomain'     => $subdomain,
        'app'           => $appCode
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Method not allowed. Use POST or GET.'
    ]);
    exit;
}

$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true) ?? [];
$data = !empty($_POST) ? $_POST : $jsonData;

$setupEmail   = strtolower(trim($data['setup_email'] ?? ($data['email'] ?? '')));
$businessName = trim($data['business_name'] ?? ($data['site_name'] ?? ($data['store_name'] ?? '')));
$subdomain    = strtolower(trim($data['subdomain'] ?? ($data['domain'] ?? '')));
$appType      = strtolower(trim($data['app_type'] ?? ($data['app'] ?? 'aleya')));
$themeColor   = trim($data['theme_color'] ?? ($appType === 'aleya' ? '#00647a' : '#4f46e5'));
$category     = trim($data['category'] ?? '');
$currency     = trim($data['currency'] ?? 'INR');
$packageId    = (int)($data['package_id'] ?? ($data['plan_id'] ?? 0));
$paymentId    = trim((string)($data['payment_id'] ?? ''));
$paymentStatus = trim((string)($data['payment_status'] ?? ($paymentId ? 'completed' : 'pending')));
$copyTourData = !isset($data['copy_tour_packages']) || in_array($data['copy_tour_packages'], [1, '1', true, 'true'], true);

// If business name was not passed in POST, lookup previously registered site name
if (empty($businessName) && !empty($setupEmail)) {
    $escSetupEmail = mysqli_real_escape_string($connection, $setupEmail);
    $siteQ = mysqli_query($connection, "
        SELECT s.site_name, s.site_domain 
        FROM {$tblPrefix}site s
        JOIN {$tblPrefix}user u ON u.user_id = s.site_owner_id
        WHERE u.user_email = '{$escSetupEmail}' AND s.site_id > 1
        ORDER BY s.site_id DESC
        LIMIT 1
    ");
    if ($siteQ && $siteRow = mysqli_fetch_assoc($siteQ)) {
        $businessName = trim($siteRow['site_name']);
        if (empty($subdomain) && !empty($siteRow['site_domain'])) {
            $parts = explode('.', $siteRow['site_domain']);
            $subdomain = $parts[0] ?? '';
        }
    }
}

if (empty($businessName) && !empty($_SESSION['business_name'])) {
    $businessName = $_SESSION['business_name'];
} elseif (empty($businessName) && !empty($_SESSION['site_name'])) {
    $businessName = $_SESSION['site_name'];
}

if (empty($businessName)) {
    $businessName = 'My Travel Workspace';
}

// Clean subdomain
$subdomain = preg_replace('/[^a-zA-Z0-9-]/', '', $subdomain);
if (empty($subdomain)) {
    $subdomain = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $businessName));
}

$mainDomain = defined('MAIN_DOMAIN') ? MAIN_DOMAIN : 'blumox.com';
$fullDomain = $subdomain . '.' . $mainDomain;

// Map App Type to App ID
$appIdMap = [
    'system'     => 1,
    'cms'        => 2,
    'ecom'       => 7,
    'ecommerce'  => 7,
    'gift'       => 9,
    'ecom_gift'  => 9,
    'e_com_gift' => 9,
    'aleya'      => 20,
    'spa'        => 28,
    'travel'     => 26
];
$resolvedAppId = $appIdMap[$appType] ?? 20;

// Resolve User ID
$userId = 0;
$displayName = $businessName;
$userSalt = '';

if (!empty($setupEmail)) {
    $escSetupEmail = mysqli_real_escape_string($connection, $setupEmail);
    $userQ = mysqli_query($connection, "SELECT user_id, user_salt_key FROM {$tblPrefix}user WHERE user_email = '{$escSetupEmail}' LIMIT 1");
    if ($userQ && $userRow = mysqli_fetch_assoc($userQ)) {
        $userId = (int)$userRow['user_id'];
        $userSalt = (string)$userRow['user_salt_key'];
    }
}

if ($userId <= 0 && isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0) {
    $userId = (int)$_SESSION['user_id'];
}

// If user does not exist, auto-create registered user record
if ($userId <= 0) {
    $userSalt = hash('sha256', uniqid(mt_rand(), true) . $setupEmail . time());
    $autoPassword = hash('sha256', $userSalt . uniqid('pass_', true));
    $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $subdomain)) ?: 'user' . random_int(100, 999);
    $escUser = mysqli_real_escape_string($connection, $baseUsername);
    $escEmail = mysqli_real_escape_string($connection, $setupEmail ?: ($baseUsername . '@' . $mainDomain));
    $rawMobile = preg_replace('/\D/', '', (string)($data['mobile'] ?? ''));
    $userMobile = (!empty($rawMobile) && strlen($rawMobile) >= 10) ? $rawMobile : ('9' . random_int(100000000, 999999999));
    $escMobile = mysqli_real_escape_string($connection, $userMobile);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}user 
            (user_parent_id, user_username, user_email, user_mobile, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip)
        VALUES 
            (0, '{$escUser}', '{$escEmail}', '{$escMobile}', '{$autoPassword}', '{$userSalt}', 1, NOW(), '{$clientIp}')
    ");
    $userId = mysqli_insert_id($connection);

    if ($userId > 0) {
        $escBiz = mysqli_real_escape_string($connection, $businessName);
        mysqli_query($connection, "
            INSERT INTO {$tblPrefix}profile 
                (profile_user_id, profile_first_name, profile_last_name, profile_display_name, profile_email, profile_mobile)
            VALUES 
                ({$userId}, '{$escBiz}', 'Admin', '{$escBiz}', '{$escEmail}', '')
        ");
    }
}

if ($userId <= 0) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Could not identify or create user profile for onboarding.']);
    exit;
}

$escBizName = mysqli_real_escape_string($connection, $businessName);
$escFullDomain = mysqli_real_escape_string($connection, $fullDomain);
$escThemeColor = mysqli_real_escape_string($connection, $themeColor);

// Determine or Create New Site
$siteId = 0;
// Check if user already owns a dedicated tenant site (excluding main root site 1)
$existingSiteQ = mysqli_query($connection, "
    SELECT site_id 
    FROM {$tblPrefix}site 
    WHERE site_owner_id = {$userId} AND site_id > 1 
    ORDER BY site_id DESC 
    LIMIT 1
");

if ($existingSiteQ && mysqli_num_rows($existingSiteQ) > 0) {
    $existingRow = mysqli_fetch_assoc($existingSiteQ);
    $siteId = (int)$existingRow['site_id'];
    mysqli_query($connection, "
        UPDATE {$tblPrefix}site 
        SET 
            site_name = '{$escBizName}',
            site_domain = '{$escFullDomain}',
            site_url = 'https://{$escFullDomain}',
            site_app_id = {$resolvedAppId},
            site_theme_id = 1,
            site_dashboard_version = 3,
            site_status = 1
        WHERE site_id = {$siteId}
    ");
} else {
    // Create new site for this user
    $insertNewSite = mysqli_query($connection, "
        INSERT INTO {$tblPrefix}site 
            (site_owner_id, site_domain, site_url, site_name, site_app_id, site_theme_id, site_dashboard_version, site_status)
        VALUES 
            ({$userId}, '{$escFullDomain}', 'https://{$escFullDomain}', '{$escBizName}', {$resolvedAppId}, 1, 3, 1)
    ");
    $siteId = mysqli_insert_id($connection);
}

if ($siteId <= 0) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Failed to provision tenant workspace site.']);
    exit;
}

// 1. Give Administrator access to registered user for this site
$checkAccess = mysqli_query($connection, "
    SELECT site_access_id 
    FROM {$tblPrefix}site_access 
    WHERE site_access_site_id = {$siteId} AND site_access_user_id = {$userId} 
    LIMIT 1
");

if ($checkAccess && mysqli_num_rows($checkAccess) > 0) {
    mysqli_query($connection, "
        UPDATE {$tblPrefix}site_access 
        SET site_access_type = 'administrator', site_access_status = 1 
        WHERE site_access_site_id = {$siteId} AND site_access_user_id = {$userId}
    ");
} else {
    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}site_access 
            (site_access_site_id, site_access_user_id, site_access_type, site_access_status, site_access_meta)
        VALUES 
            ({$siteId}, {$userId}, 'administrator', 1, '')
    ");
}

// 2. Fetch and assign Package from isw_package where package_app_id = site_app_id
if ($packageId <= 0) {
    $pkgLookup = mysqli_query($connection, "
        SELECT package_id 
        FROM {$tblPrefix}package 
        WHERE package_app_id = {$resolvedAppId} AND package_status = 1 
        ORDER BY CAST(package_price_inr AS UNSIGNED) ASC 
        LIMIT 1
    ");
    if ($pkgLookup && $pkgRow = mysqli_fetch_assoc($pkgLookup)) {
        $packageId = (int)$pkgRow['package_id'];
    } else {
        $packageId = $resolvedAppId; // Fallback
    }
}

// Record site installation with package
$expiryDate = date('Y-m-d', strtotime('+1 year'));
$checkInstall = mysqli_query($connection, "SELECT site_installation_id FROM {$tblPrefix}site_installation WHERE site_installation_site_id = {$siteId} LIMIT 1");
if ($checkInstall && mysqli_num_rows($checkInstall) > 0) {
    mysqli_query($connection, "
        UPDATE {$tblPrefix}site_installation 
        SET site_installation_package_id = {$packageId}, site_installation_status = 1, site_installation_expiry_date = '{$expiryDate}' 
        WHERE site_installation_site_id = {$siteId}
    ");
} else {
    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}site_installation 
            (site_installation_site_id, site_installation_parent_site_id, site_installation_user_id, site_installation_package_id, site_installation_status, site_installation_expiry_date)
        VALUES 
            ({$siteId}, {$siteId}, {$userId}, {$packageId}, 1, '{$expiryDate}')
    ");
}

// 3. Site settings
$checkSetting = mysqli_query($connection, "SELECT site_setting_id FROM {$tblPrefix}site_setting WHERE site_setting_site_id = {$siteId} LIMIT 1");
if ($checkSetting && mysqli_num_rows($checkSetting) > 0) {
    mysqli_query($connection, "
        UPDATE {$tblPrefix}site_setting 
        SET site_theme_color = '{$escThemeColor}' 
        WHERE site_setting_site_id = {$siteId}
    ");
} else {
    mysqli_query($connection, "
        INSERT INTO {$tblPrefix}site_setting 
            (site_setting_site_id, site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_theme_color)
        VALUES 
            ({$siteId}, 10, 10, 10, 85, '{$escThemeColor}')
    ");
}

// 4. Parse tour package destinations for migration (executed upon successful payment)
$destinationsInput = [];
if ($resolvedAppId === 20 && $copyTourData) {
    $destinationsInput = $data['destinations'] ?? ($data['selected_destinations'] ?? []);
    if (is_string($destinationsInput)) {
        $destinationsInput = array_filter(array_map('trim', explode(',', $destinationsInput)));
    }
    if (!is_array($destinationsInput)) {
        $destinationsInput = [];
    }
}

// 5. Create Subscription Order & Transaction for Payment Gateway
$packageId = (int)($data['package_id'] ?? ($data['plan_id'] ?? 6));
$pkgTitle = 'CRM Starter';
$basePrice = 1499;

if ($packageId > 0) {
    $pkgQ = mysqli_query($connection, "SELECT * FROM {$tblPrefix}package WHERE package_id = {$packageId} LIMIT 1");
    if ($pkgQ && $pRow = mysqli_fetch_assoc($pkgQ)) {
        $pkgTitle = $pRow['package_title'];
        if ($currency === 'USD' && !empty($pRow['package_price_usd'])) {
            $basePrice = (int)$pRow['package_price_usd'];
        } else {
            $basePrice = (int)$pRow['package_price_inr'];
        }
    }
}

// 18% GST for INR orders
$gstAmount = ($currency === 'INR') ? (int)round($basePrice * 0.18) : 0;
$orderTotalPrice = $basePrice + $gstAmount;

// Determine platform host site where payment is processed
$currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$platformSiteId = 1;
$hostQ = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$currentHost}' LIMIT 1");
if ($hostQ && $hRow = mysqli_fetch_assoc($hostQ)) {
    $platformSiteId = (int)$hRow['site_id'];
} elseif (defined('SITE_ID') && (int)SITE_ID > 0) {
    $platformSiteId = (int)SITE_ID;
}

// Resolve cart session ID
$cartSessionId = '';
if (session_status() === PHP_SESSION_ACTIVE) {
    if (empty($_SESSION['active_session_id'])) {
        $_SESSION['active_session_id'] = hash('sha256', uniqid(mt_rand(), true) . time());
    }
    $cartSessionId = $_SESSION['active_session_id'];
} elseif (defined('SESSION_ID') && !empty(SESSION_ID)) {
    $cartSessionId = SESSION_ID;
} else {
    $cartSessionId = hash('sha256', uniqid(mt_rand(), true) . time());
}

// 5a. Create Order in isw_order
$orderMetaInfo = [
    'type'               => 'workspace_subscription',
    'package_id'         => $packageId,
    'package_title'      => $pkgTitle,
    'base_price'         => $basePrice,
    'gst'                => $gstAmount,
    'total_price'        => $orderTotalPrice,
    'site_id'            => $siteId,
    'subdomain'          => $subdomain,
    'domain'             => $fullDomain,
    'app_type'           => $appType,
    'copy_tour_packages' => ($resolvedAppId === 20 && $copyTourData) ? 1 : 0,
    'destinations'       => $destinationsInput
];
$escOrderMsg = mysqli_real_escape_string($connection, json_encode($orderMetaInfo, JSON_UNESCAPED_UNICODE));

$insertOrderSql = "
    INSERT INTO {$tblPrefix}order 
        (order_site_id, order_user_id, order_cart_session_id, order_product_id, order_product_type, order_product_qty, order_product_price, order_delivery_charges, order_total_price, order_currency, order_status, order_payment_mode, order_msg, order_generate)
    VALUES 
        ({$platformSiteId}, {$userId}, '{$cartSessionId}', '{$packageId}', 'package', '1', '{$basePrice}', '0', '{$orderTotalPrice}', '{$currency}', 0, 'online', '{$escOrderMsg}', NOW())
";
mysqli_query($connection, $insertOrderSql);
$orderId = (int)mysqli_insert_id($connection);

// 5b. Create Cart Entry in isw_cart
$currSymbol = ($currency === 'USD') ? '$' : '₹';
$insertCartSql = "
    INSERT INTO {$tblPrefix}cart 
        (cart_session_id, cart_site_id, cart_user_id, cart_relation_id, cart_relation_type, cart_qty, cart_currency, cart_currency_symbol, cart_price, cart_status, cart_generated, cart_updated)
    VALUES 
        ('{$cartSessionId}', {$platformSiteId}, {$userId}, {$packageId}, 'package', 1, '{$currency}', '{$currSymbol}', '{$orderTotalPrice}', 2, NOW(), NOW())
";
mysqli_query($connection, $insertCartSql);
$cartId = (int)mysqli_insert_id($connection);

// 5c. Resolve Payment Gateway name
$pgName = 'razorpay';
$pgQ = mysqli_query($connection, "
    SELECT site_configuration_value 
    FROM {$tblPrefix}site_configuration 
    WHERE site_configuration_site_id = {$platformSiteId} AND site_configuration_key = 'pg_name' AND site_configuration_status = 1 
    LIMIT 1
");
if ($pgQ && $pgRow = mysqli_fetch_assoc($pgQ)) {
    if (!empty($pgRow['site_configuration_value'])) {
        $pgName = strtolower(trim($pgRow['site_configuration_value']));
    }
}
$escPgName = mysqli_real_escape_string($connection, $pgName);

// 5d. Create Transaction in isw_transaction
$insertTxnSql = "
    INSERT INTO {$tblPrefix}transaction 
        (transaction_site_id, transaction_user_id, transaction_order_id, transaction_cart_id, transaction_method, transaction_gateway, transaction_currency, transaction_amount, transaction_type, transaction_status, transaction_time)
    VALUES 
        ({$platformSiteId}, {$userId}, '{$orderId}', '{$cartId}', 'online', '{$escPgName}', '{$currency}', '{$orderTotalPrice}', 'package', 0, NOW())
";
mysqli_query($connection, $insertTxnSql);
$transactionId = (int)mysqli_insert_id($connection);

$encodedTxnId = base64_encode((string)$transactionId);
$paymentGatewayUrl = '/payment/generate/?id=' . $encodedTxnId . '&pg_type=order&orderType=package';

// 6. Establish Session & Cookies with Administrator role for this site
$cookieExpiry = time() + (30 * 24 * 60 * 60);
$cookieDomain = '.' . ltrim($mainDomain, '.');
if (filter_var($mainDomain, FILTER_VALIDATE_IP) || strpos($mainDomain, 'localhost') !== false) {
    $cookieDomain = '';
}
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

@setcookie('user_type', base64_encode('administrator'), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('user_site_id', base64_encode((string)$siteId), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('site_app', base64_encode($appType), $cookieExpiry, '/', $cookieDomain, $isHttps, false);
@setcookie('isLogin', '1', $cookieExpiry, '/', $cookieDomain, $isHttps, false);

$_COOKIE['user_site_id'] = base64_encode((string)$siteId);

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION['site_id']      = $siteId;
    $_SESSION['user_site_id'] = $siteId;
    $_SESSION['user_id']      = $userId;
    $_SESSION['user_role']    = 'administrator';
    $_SESSION['user_type']    = 'administrator';
    $_SESSION['site_app']     = $appType;
    $_SESSION['site_name']    = $businessName;
}

echo json_encode([
    'status'         => 'success',
    'msg'            => 'Order #OD' . $orderId . ' generated successfully! Connecting to payment gateway...',
    'order_id'       => $orderId,
    'transaction_id' => $encodedTxnId,
    'payment_url'    => $paymentGatewayUrl,
    'redirect_url'   => $paymentGatewayUrl,
    'url'            => $paymentGatewayUrl,
    'total_amount'   => $orderTotalPrice,
    'package_title'  => $pkgTitle,
    'site_id'        => $siteId,
    'user_id'        => $userId,
    'role'           => 'administrator',
    'package_id'     => $packageId,
    'copy_tours'     => ($resolvedAppId === 20 && $copyTourData) ? 1 : 0,
    'destinations'   => count($destinationsInput),
    'workspace'      => [
        'id'     => $siteId,
        'name'   => $businessName,
        'domain' => $fullDomain,
        'app'    => $appType,
        'color'  => $themeColor
    ]
], JSON_UNESCAPED_UNICODE);
exit;
