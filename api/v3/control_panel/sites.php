<?php

/**
 * Control Panel: Multi-tenant Sites Provisioning, Feature Flags & License Quotas API Endpoint
 * Path: /api/v3/control_panel/sites.php
 */

header("Content-Type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';33
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Helper function to get a site config value
function getSiteConfigValue($connection, $tblPrefix, $siteId, $key, $default = '') {
    $escKey = mysqli_real_escape_string($connection, $key);
    $q = mysqli_query($connection, "SELECT site_configuration_value FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = '{$escKey}' LIMIT 1");
    if ($q && $row = mysqli_fetch_assoc($q)) {
        return $row['site_configuration_value'];
    }
    return $default;
}

// Helper function to set/upsert a site config value
function setSiteConfigValue($connection, $tblPrefix, $siteId, $key, $value, $status = 1) {
    $escKey = mysqli_real_escape_string($connection, $key);
    $escVal = mysqli_real_escape_string($connection, (string)$value);
    $chk = mysqli_query($connection, "SELECT site_configuration_id FROM {$tblPrefix}site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = '{$escKey}' LIMIT 1");
    if ($chk && mysqli_num_rows($chk) > 0) {
        $row = mysqli_fetch_assoc($chk);
        $cfgId = (int)$row['site_configuration_id'];
        return mysqli_query($connection, "UPDATE {$tblPrefix}site_configuration SET site_configuration_value = '{$escVal}', site_configuration_status = {$status} WHERE site_configuration_id = {$cfgId}");
    } else {
        return mysqli_query($connection, "INSERT INTO {$tblPrefix}site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_global, site_configuration_status) VALUES ({$siteId}, '{$escKey}', '{$escVal}', 0, {$status})");
    }
}

// --------------------------------------------------------------------------
// 1. GET Requests: List Sites OR Fetch Single Site License & Features
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';

    // A. Fetch Site License & Features Details
    if ($action === 'get_license') {
        $siteId = isset($_GET['site_id']) ? (int)$_GET['site_id'] : 0;
        if ($siteId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid Site ID is required.']);
            exit();
        }

        $sQ = mysqli_query($connection, "SELECT s.*, a.app_title, a.app_path, t.theme_title, u.user_email, u.user_username, p.profile_display_name
            FROM {$tblPrefix}site s
            LEFT JOIN {$tblPrefix}app a ON s.site_app_id = a.app_id
            LEFT JOIN {$tblPrefix}theme t ON s.site_theme_id = t.theme_id
            LEFT JOIN {$tblPrefix}user u ON s.site_owner_id = u.user_id
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            WHERE s.site_id = {$siteId} LIMIT 1");

        if (!$sQ || mysqli_num_rows($sQ) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Site not found.']);
            exit();
        }
        $site = mysqli_fetch_assoc($sQ);

        // Count active users on site
        $uCountQ = mysqli_query($connection, "SELECT COUNT(DISTINCT site_access_user_id) as total FROM {$tblPrefix}site_access WHERE site_access_site_id = {$siteId} AND site_access_status = 1");
        $activeUsers = ($uCountQ && $r = mysqli_fetch_assoc($uCountQ)) ? (int)$r['total'] : 0;

        // Count connected WhatsApp devices
        $waCountQ = mysqli_query($connection, "SELECT COUNT(*) as total FROM {$tblPrefix}whatsapp_device WHERE device_site_id = {$siteId} AND device_status = 1");
        $activeWaDevices = ($waCountQ && $r = mysqli_fetch_assoc($waCountQ)) ? (int)$r['total'] : 0;

        // Fetch Limits & Feature flags from site_configuration
        $userLimit = (int)getSiteConfigValue($connection, $tblPrefix, $siteId, 'user_limit', 10);
        if ($userLimit <= 0) $userLimit = 10;

        $waDeviceLimit = (int)getSiteConfigValue($connection, $tblPrefix, $siteId, 'whatsapp_device_limit', 1);
        if ($waDeviceLimit <= 0) $waDeviceLimit = 1;

        $storageLimitMb = (int)getSiteConfigValue($connection, $tblPrefix, $siteId, 'storage_limit_mb', 5120);

        $features = [
            'whatsapp_enable'      => getSiteConfigValue($connection, $tblPrefix, $siteId, 'whatsapp_enable', '1') === '1',
            'feature_leads'        => getSiteConfigValue($connection, $tblPrefix, $siteId, 'feature_leads', '1') === '1',
            'feature_accounts'     => getSiteConfigValue($connection, $tblPrefix, $siteId, 'feature_accounts', '1') === '1',
            'feature_transporters' => getSiteConfigValue($connection, $tblPrefix, $siteId, 'feature_transporters', '1') === '1',
            'feature_packages'     => getSiteConfigValue($connection, $tblPrefix, $siteId, 'feature_packages', '1') === '1',
            'feature_api_access'   => getSiteConfigValue($connection, $tblPrefix, $siteId, 'feature_api_access', '0') === '1',
            'site_custom_domain'   => getSiteConfigValue($connection, $tblPrefix, $siteId, 'site_custom_domain', '1') === '1'
        ];

        $plan = getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_plan', 'Professional SaaS');
        $expiry = getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_expiry', date('Y-m-d', strtotime('+1 year')));
        $lastReminder = getSiteConfigValue($connection, $tblPrefix, $siteId, 'last_renewal_reminder_sent', '');
        $reminderCount = (int)getSiteConfigValue($connection, $tblPrefix, $siteId, 'renewal_reminder_count', 0);

        // Subscription Pricing & Renewal Override Configurations
        $priceMonthly = (float)getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_price_monthly', 0.00);
        $priceYearly = (float)getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_price_yearly', 0.00);
        $currency = getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_currency', 'INR');
        $billingInterval = getSiteConfigValue($connection, $tblPrefix, $siteId, 'billing_interval', 'yearly');
        $priceOverride = getSiteConfigValue($connection, $tblPrefix, $siteId, 'renewal_price_override', '1') === '1';
        $renewalNotes = getSiteConfigValue($connection, $tblPrefix, $siteId, 'renewal_pricing_notes', '');

        // Fetch theme color from site_setting
        $themeColor = '#2563eb';
        $ssQ = mysqli_query($connection, "SELECT site_theme_color FROM {$tblPrefix}site_setting WHERE site_setting_site_id = {$siteId} LIMIT 1");
        if ($ssQ && $ssRow = mysqli_fetch_assoc($ssQ)) {
            if (!empty($ssRow['site_theme_color'])) {
                $themeColor = $ssRow['site_theme_color'];
            }
        }

        $effDomain = !empty($site['site_domain']) ? trim($site['site_domain']) : (!empty($site['site_url']) ? trim($site['site_url']) : '');
        $effName = !empty($site['site_name']) ? trim($site['site_name']) : (!empty($effDomain) ? $effDomain : 'Site #' . (int)$site['site_id']);

        echo json_encode([
            'status' => 'success',
            'data' => [
                'site_id'                    => (int)$site['site_id'],
                'site_name'                  => htmlspecialchars($effName),
                'site_domain'                => htmlspecialchars($effDomain),
                'site_url'                   => htmlspecialchars($site['site_url'] ?? ''),
                'site_app_id'                => (int)$site['site_app_id'],
                'app_title'                  => htmlspecialchars($site['app_title'] ?? 'Generic'),
                'site_theme_id'              => (int)$site['site_theme_id'],
                'theme_title'                => htmlspecialchars($site['theme_title'] ?? 'Default Theme'),
                'dashboard_version'          => (int)$site['site_dashboard_version'],
                'site_status'                => (int)$site['site_status'],
                'site_theme_color'           => htmlspecialchars($themeColor),
                'owner_id'                   => (int)$site['site_owner_id'],
                'owner_name'                 => htmlspecialchars($site['profile_display_name'] ?? $site['user_username'] ?? 'Owner'),
                'owner_email'                => htmlspecialchars($site['user_email'] ?? ''),
                'owner_mobile'               => htmlspecialchars($site['user_mobile'] ?? ''),
                'active_users'               => $activeUsers,
                'user_limit'                 => $userLimit,
                'active_wa_devices'          => $activeWaDevices,
                'wa_device_limit'            => $waDeviceLimit,
                'storage_limit_mb'           => $storageLimitMb,
                'features'                   => $features,
                'subscription_plan'          => $plan,
                'subscription_expiry'        => $expiry,
                'subscription_price_monthly' => $priceMonthly,
                'subscription_price_yearly'  => $priceYearly,
                'subscription_currency'      => $currency,
                'billing_interval'           => $billingInterval,
                'renewal_price_override'     => $priceOverride,
                'renewal_pricing_notes'      => $renewalNotes,
                'last_reminder_sent'         => $lastReminder,
                'reminder_count'             => $reminderCount
            ]
        ]);
        exit();
    }

    // B. Default: Paginated Sites List
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(5, (int)$_GET['limit'])) : 25;
    $offset = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
    $appId = isset($_GET['app_id']) && is_numeric($_GET['app_id']) ? (int)$_GET['app_id'] : 0;
    $rawStatus = isset($_GET['status']) ? trim($_GET['status']) : '';
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
    $planFilter = isset($_GET['plan']) ? mysqli_real_escape_string($connection, trim($_GET['plan'])) : '';

    $where = [];
    if (!empty($search)) {
        $where[] = "(s.site_name LIKE '%{$search}%' OR s.site_domain LIKE '%{$search}%' OR s.site_url LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%' OR u.user_username LIKE '%{$search}%' OR u.user_mobile LIKE '%{$search}%' OR p.profile_display_name LIKE '%{$search}%')";
    }
    if ($appId > 0) {
        $where[] = "s.site_app_id = {$appId}";
    }
    if ($rawStatus === 'expiring') {
        $where[] = "EXISTS (SELECT 1 FROM {$tblPrefix}site_configuration sc_exp WHERE sc_exp.site_configuration_site_id = s.site_id AND sc_exp.site_configuration_key = 'subscription_expiry' AND sc_exp.site_configuration_value != '' AND sc_exp.site_configuration_value <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))";
    } elseif ($rawStatus !== '' && ($rawStatus === '1' || $rawStatus === '0')) {
        $statusInt = (int)$rawStatus;
        $where[] = "s.site_status = {$statusInt}";
    }
    if (!empty($planFilter)) {
        $where[] = "EXISTS (SELECT 1 FROM {$tblPrefix}site_configuration sc_p WHERE sc_p.site_configuration_site_id = s.site_id AND sc_p.site_configuration_key = 'subscription_plan' AND sc_p.site_configuration_value = '{$planFilter}')";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    $countSql = "SELECT COUNT(*) as total FROM {$tblPrefix}site s 
                 LEFT JOIN {$tblPrefix}user u ON s.site_owner_id = u.user_id 
                 LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id 
                 {$whereSql}";
    $countRes = mysqli_query($connection, $countSql);
    $total = ($countRes && $r = mysqli_fetch_assoc($countRes)) ? (int)$r['total'] : 0;

    $orderBy = "s.site_id DESC";
    if ($sort === 'oldest') {
        $orderBy = "s.site_id ASC";
    } elseif ($sort === 'name_asc') {
        $orderBy = "s.site_name ASC";
    } elseif ($sort === 'domain_asc') {
        $orderBy = "s.site_domain ASC";
    } elseif ($sort === 'expiry_asc') {
        $orderBy = "subscription_expiry ASC, s.site_id DESC";
    }

    $sql = "SELECT s.*, a.app_title, a.app_path, t.theme_title, t.theme_path, u.user_email, u.user_username, u.user_mobile, u.user_reg_time,
            p.profile_display_name,
            (SELECT site_installation_activation_time FROM {$tblPrefix}site_installation si WHERE si.site_installation_site_id = s.site_id ORDER BY site_installation_id DESC LIMIT 1) as activation_time,
            (SELECT site_installation_expiry_date FROM {$tblPrefix}site_installation si WHERE si.site_installation_site_id = s.site_id ORDER BY site_installation_id DESC LIMIT 1) as installation_expiry_date,
            (SELECT COUNT(*) FROM {$tblPrefix}site_access sa WHERE sa.site_access_site_id = s.site_id AND sa.site_access_status = 1) as users_count,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'user_limit' LIMIT 1) as user_limit,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'whatsapp_device_limit' LIMIT 1) as wa_limit,
            (SELECT COUNT(*) FROM {$tblPrefix}whatsapp_device wd WHERE wd.device_site_id = s.site_id AND wd.device_status = 1) as wa_devices_count,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'subscription_plan' LIMIT 1) as subscription_plan,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'subscription_expiry' LIMIT 1) as subscription_expiry,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'last_renewal_reminder_sent' LIMIT 1) as last_reminder_sent,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'subscription_price_monthly' LIMIT 1) as price_monthly,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'subscription_price_yearly' LIMIT 1) as price_yearly,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'subscription_currency' LIMIT 1) as currency,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'billing_interval' LIMIT 1) as billing_interval,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'renewal_price_override' LIMIT 1) as renewal_price_override,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'partner_id' LIMIT 1) as partner_id_cfg,
            (SELECT cpc.commission_partner_id FROM {$tblPrefix}control_panel_commission cpc WHERE cpc.commission_site_id = s.site_id ORDER BY cpc.commission_id DESC LIMIT 1) as partner_id_comm
            FROM {$tblPrefix}site s
            LEFT JOIN {$tblPrefix}app a ON s.site_app_id = a.app_id
            LEFT JOIN {$tblPrefix}theme t ON s.site_theme_id = t.theme_id
            LEFT JOIN {$tblPrefix}user u ON s.site_owner_id = u.user_id
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            {$whereSql}
            ORDER BY {$orderBy}
            LIMIT {$offset}, {$limit}";

    $res = mysqli_query($connection, $sql);
    $sites = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $uLim = !empty($row['user_limit']) && is_numeric($row['user_limit']) ? (int)$row['user_limit'] : 10;
            $waLim = !empty($row['wa_limit']) && is_numeric($row['wa_limit']) ? (int)$row['wa_limit'] : 1;

            $siteDomain = !empty($row['site_domain']) ? trim($row['site_domain']) : (!empty($row['site_url']) ? trim($row['site_url']) : '');
            $rawName = !empty($row['site_name']) ? trim($row['site_name']) : '';
            $siteName = !empty($rawName) ? $rawName : (!empty($siteDomain) ? $siteDomain : ('Site #' . (int)$row['site_id']));
            $rawOwnerName = !empty($row['profile_display_name']) ? trim($row['profile_display_name']) : (!empty($row['user_username']) ? trim($row['user_username']) : '');

            // Activation date calculation (installation activation time -> user reg time)
            $rawAct = !empty($row['activation_time']) ? $row['activation_time'] : (!empty($row['user_reg_time']) ? $row['user_reg_time'] : '');
            $activeDate = '';
            if (!empty($rawAct) && $rawAct !== '0000-00-00 00:00:00' && $rawAct !== '0000-00-00') {
                $activeDate = substr($rawAct, 0, 10);
            }

            // Expiry date calculation (config expiry -> installation expiry)
            $rawExp = !empty($row['subscription_expiry']) ? $row['subscription_expiry'] : (!empty($row['installation_expiry_date']) ? $row['installation_expiry_date'] : '');
            $expiryDate = '';
            if (!empty($rawExp) && $rawExp !== '0000-00-00' && $rawExp !== '0000-00-00 00:00:00') {
                $expiryDate = substr($rawExp, 0, 10);
            }

            $sites[] = [
                'id' => (int)$row['site_id'],
                'name' => htmlspecialchars($siteName),
                'domain' => htmlspecialchars($siteDomain),
                'url' => htmlspecialchars($row['site_url'] ?? ''),
                'app_id' => (int)$row['site_app_id'],
                'app_title' => htmlspecialchars($row['app_title'] ?? 'Generic'),
                'app_path' => htmlspecialchars($row['app_path'] ?? ''),
                'theme_id' => (int)$row['site_theme_id'],
                'theme_title' => htmlspecialchars($row['theme_title'] ?? 'Default Theme'),
                'dashboard_version' => (int)$row['site_dashboard_version'],
                'owner_id' => (int)$row['site_owner_id'],
                'owner_name' => htmlspecialchars($rawOwnerName),
                'owner_username' => htmlspecialchars($row['user_username'] ?? ''),
                'owner_email' => htmlspecialchars($row['user_email'] ?? ''),
                'owner_mobile' => htmlspecialchars($row['user_mobile'] ?? ''),
                'active_date' => $activeDate,
                'status' => (int)$row['site_status'],
                'users_count' => (int)$row['users_count'],
                'user_limit' => $uLim,
                'wa_devices_count' => (int)$row['wa_devices_count'],
                'wa_device_limit' => $waLim,
                'subscription_plan' => htmlspecialchars($row['subscription_plan'] ?? 'Professional SaaS'),
                'subscription_expiry' => $expiryDate,
                'subscription_price_monthly' => !empty($row['price_monthly']) ? (float)$row['price_monthly'] : 0.0,
                'subscription_price_yearly' => !empty($row['price_yearly']) ? (float)$row['price_yearly'] : 0.0,
                'subscription_currency' => htmlspecialchars($row['currency'] ?? 'INR'),
                'billing_interval' => htmlspecialchars($row['billing_interval'] ?? 'yearly'),
                'renewal_price_override' => ($row['renewal_price_override'] ?? '1') === '1',
                'last_reminder_sent' => htmlspecialchars($row['last_reminder_sent'] ?? ''),
                'partner_id' => !empty($row['partner_id_cfg']) ? (int)$row['partner_id_cfg'] : (!empty($row['partner_id_comm']) ? (int)$row['partner_id_comm'] : 0),
                'partner_name' => (!empty($row['partner_id_cfg']) || !empty($row['partner_id_comm'])) ? (function() use ($connection, $tblPrefix, $row) {
                    $pid = !empty($row['partner_id_cfg']) ? (int)$row['partner_id_cfg'] : (int)$row['partner_id_comm'];
                    $pr = mysqli_query($connection, "SELECT COALESCE(p.profile_display_name, u.user_username, u.user_email) as pn FROM {$tblPrefix}user u LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id WHERE u.user_id = {$pid} LIMIT 1");
                    return ($pr && $prow = mysqli_fetch_assoc($pr)) ? htmlspecialchars($prow['pn']) : '';
                })() : '',
                'logo' => htmlspecialchars($row['site_logo'] ?? ''),
                'icon' => htmlspecialchars($row['site_icon'] ?? '')
            ];
        }
    }

    // Global KPI Metrics for Client Sites
    $metrics = [
        'total' => 0,
        'active' => 0,
        'suspended' => 0,
        'expiring_soon' => 0
    ];
    $mQ = mysqli_query($connection, "SELECT 
        COUNT(*) as total, 
        SUM(CASE WHEN site_status = 1 THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN site_status = 0 THEN 1 ELSE 0 END) as suspended
        FROM {$tblPrefix}site");
    if ($mQ && $mRow = mysqli_fetch_assoc($mQ)) {
        $metrics['total'] = (int)$mRow['total'];
        $metrics['active'] = (int)$mRow['active'];
        $metrics['suspended'] = (int)$mRow['suspended'];
    }

    $expSoonQ = mysqli_query($connection, "SELECT COUNT(*) as exp_count 
        FROM {$tblPrefix}site_configuration 
        WHERE site_configuration_key = 'subscription_expiry' 
          AND site_configuration_value != '' 
          AND site_configuration_value <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
    if ($expSoonQ && $expRow = mysqli_fetch_assoc($expSoonQ)) {
        $metrics['expiring_soon'] = (int)$expRow['exp_count'];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $sites,
        'total' => $total,
        'metrics' => $metrics,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Provision, Toggle Status, Save Licenses/Settings, or Send Renewal Reminder
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? ($_GET['action'] ?? 'provision');
    $siteId = isset($payload['site_id']) ? (int)$payload['site_id'] : (isset($_GET['site_id']) ? (int)$_GET['site_id'] : 0);

    // A. Send Renewal Reminder (WhatsApp & Email)
    if ($action === 'send_renewal_reminder') {
        if ($siteId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid Site ID is required.']);
            exit();
        }

        $sQ = mysqli_query($connection, "SELECT s.*, u.user_email, u.user_mobile, p.profile_display_name, a.app_title
            FROM {$tblPrefix}site s
            LEFT JOIN {$tblPrefix}user u ON s.site_owner_id = u.user_id
            LEFT JOIN {$tblPrefix}profile p ON p.profile_user_id = u.user_id
            LEFT JOIN {$tblPrefix}app a ON s.site_app_id = a.app_id
            WHERE s.site_id = {$siteId} LIMIT 1");

        if (!$sQ || mysqli_num_rows($sQ) === 0) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'msg' => 'Site not found.']);
            exit();
        }
        $site = mysqli_fetch_assoc($sQ);

        $clientName = trim($site['profile_display_name'] ?? 'Valued Partner');
        $clientEmail = trim($site['user_email'] ?? '');
        $clientMobile = trim($site['user_mobile'] ?? '');
        $siteName = trim($site['site_name'] ?? $site['site_domain']);
        $siteDomain = trim($site['site_domain']);
        $plan = getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_plan', 'Professional SaaS');
        $expiry = getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_expiry', date('Y-m-d'));

        // Subscription Pricing for Reminder
        $priceMonthly = (float)getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_price_monthly', 0.00);
        $priceYearly = (float)getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_price_yearly', 0.00);
        $currency = getSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_currency', 'INR');
        $billingInterval = $payload['billing_interval'] ?? getSiteConfigValue($connection, $tblPrefix, $siteId, 'billing_interval', 'yearly');

        // Check for manual reminder price override passed in payload
        if (isset($payload['renewal_price']) && is_numeric($payload['renewal_price'])) {
            $renewalAmount = (float)$payload['renewal_price'];
        } else {
            $renewalAmount = ($billingInterval === 'monthly') ? $priceMonthly : $priceYearly;
        }

        $currencySymbols = [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'AED '
        ];
        $currSym = $currencySymbols[$currency] ?? ($currency . ' ');
        $formattedAmount = ($renewalAmount > 0) ? ($currSym . number_format($renewalAmount, 2)) : 'Standard Plan Rate';

        $channels = $payload['channels'] ?? ['email', 'whatsapp'];
        if (!is_array($channels)) {
            $channels = ['email', 'whatsapp'];
        }
        $customMessage = trim($payload['message'] ?? '');

        // Default renewal message if not provided
        if (empty($customMessage)) {
            $intervalText = ($billingInterval === 'monthly') ? 'per month' : (($billingInterval === 'quarterly') ? 'per quarter' : 'per year');
            $customMessage = "Dear {$clientName},\n\nThis is a renewal reminder for your SaaS subscription for {$siteName} ({$siteDomain}).\n\nPlan Tier: {$plan}\nRenewal Due Date: {$expiry}\nRenewal Investment: {$formattedAmount} ({$intervalText})\n\nPlease renew your subscription to ensure uninterrupted service, user access, and automated workflows.\n\nRenewal Link: http://{$siteDomain}/dashboard/\n\nThank you,\nBlumox Operations Team";
        }

        $deliveryResults = [];
        $reminderSent = false;

        // 1. WhatsApp Delivery
        if (in_array('whatsapp', $channels)) {
            if (!empty($clientMobile)) {
                $helperPath = (defined('ROOT') ? ROOT : dirname(__DIR__, 2)) . '/api/v3/whatsapp/device_helper.php';
                if (file_exists($helperPath)) {
                    require_once $helperPath;
                }
                if (function_exists('sendWhatsAppDirectMessage')) {
                    $systemSiteId = defined('SITE_ID') ? (int)SITE_ID : 1;
                    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
                    $waRes = @sendWhatsAppDirectMessage($connection, $systemSiteId, $currentUserId, $clientMobile, $customMessage);
                    if (!empty($waRes['status']) && $waRes['status'] === 'success') {
                        $deliveryResults['whatsapp'] = 'Delivered successfully via WhatsApp device';
                        $reminderSent = true;
                    } else {
                        $waErr = !empty($waRes['msg']) ? $waRes['msg'] : 'Device not connected or offline';
                        $deliveryResults['whatsapp'] = $waErr . ' (Sync device via WhatsApp CRM)';
                    }
                } else {
                    $deliveryResults['whatsapp'] = 'WhatsApp gateway helper unavailable';
                }
            } else {
                $deliveryResults['whatsapp'] = 'Skipped (No client mobile)';
            }
        }

        // 2. Email Delivery via SMTP & PHPMailer
        if (in_array('email', $channels)) {
            if (!empty($clientEmail)) {
                $subject = "Subscription Renewal Reminder: {$siteName} ({$siteDomain})";

                // Resolve SMTP Settings (prioritize site 1, fallback site 29 or configured site)
                $smtpRes = mysqli_query($connection, "SELECT site_configuration_key, site_configuration_value 
                    FROM {$tblPrefix}site_configuration 
                    WHERE site_configuration_site_id IN (1, 29) 
                      AND (site_configuration_key LIKE 'email_smtp_%' OR site_configuration_key = 'enable_smtp_email') 
                    ORDER BY (site_configuration_site_id = 1) DESC");
                $smtpCfg = [];
                if ($smtpRes) {
                    while ($sRow = mysqli_fetch_assoc($smtpRes)) {
                        if (!isset($smtpCfg[$sRow['site_configuration_key']])) {
                            $smtpCfg[$sRow['site_configuration_key']] = $sRow['site_configuration_value'];
                        }
                    }
                }

                $smtpHost = !empty($smtpCfg['email_smtp_host']) ? $smtpCfg['email_smtp_host'] : 'smtp.zeptomail.in';
                $smtpPort = !empty($smtpCfg['email_smtp_port']) ? (int)$smtpCfg['email_smtp_port'] : 587;
                $smtpUser = !empty($smtpCfg['email_smtp_username']) ? $smtpCfg['email_smtp_username'] : 'emailapikey';
                $smtpPass = !empty($smtpCfg['email_smtp_password']) ? $smtpCfg['email_smtp_password'] : '';
                $smtpFromEmail = !empty($smtpCfg['email_smtp_email']) ? $smtpCfg['email_smtp_email'] : 'alerts@blumox.com';
                $smtpFromName = !empty($smtpCfg['email_smtp_name']) ? $smtpCfg['email_smtp_name'] : 'Blumox Cloud Billing';
                $smtpReplyEmail = !empty($smtpCfg['email_smtp_reply_email']) ? $smtpCfg['email_smtp_reply_email'] : 'support@blumox.com';
                $smtpReplyName = !empty($smtpCfg['email_smtp_reply_name']) ? $smtpCfg['email_smtp_reply_name'] : 'Blumox Support';
                $smtpSsl = strtolower($smtpCfg['email_smtp_ssl'] ?? 'tls');

                // Build modern responsive HTML email body
                $htmlBodyContent = nl2br(htmlspecialchars($customMessage));
                $htmlEmail = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>" . htmlspecialchars($subject) . "</title>
                </head>
                <body style='margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f8fafc; padding: 32px 16px;'>
                        <tr>
                            <td align='center'>
                                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);'>
                                    <tr>
                                        <td style='padding: 24px 28px; background: #0f172a; color: #ffffff; text-align: left;'>
                                            <h2 style='margin: 0; font-size: 19px; font-weight: 700; letter-spacing: -0.3px;'>Blumox Cloud &bull; Subscription Renewal</h2>
                                            <p style='margin: 4px 0 0; font-size: 13px; color: #94a3b8;'>Tenant: <strong>" . htmlspecialchars($siteName) . "</strong> (" . htmlspecialchars($siteDomain) . ")</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 28px 28px 20px;'>
                                            <div style='background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; font-size: 14px; line-height: 1.65; color: #334155;'>
                                                {$htmlBodyContent}
                                            </div>
                                            <div style='text-align: center; margin: 28px 0 16px;'>
                                                <a href='http://" . htmlspecialchars($siteDomain) . "/dashboard/' target='_blank' style='display: inline-block; background-color: #2563eb; color: #ffffff; padding: 13px 32px; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 8px;'>Open Client Dashboard &rarr;</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 18px 28px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; font-size: 12px; color: #64748b; line-height: 1.5;'>
                                            <p style='margin: 0;'>Sent by <strong>Blumox Technologies</strong> Cloud Operations.<br>For billing queries, reply to this email or contact <a href='mailto:support@blumox.com' style='color: #2563eb; text-decoration: none;'>support@blumox.com</a>.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>";

                // Require PHPMailer files
                $phpMailerBase = (defined('ROOT') ? ROOT : dirname(__DIR__, 2)) . '/lib/configuration/mail/';
                if (file_exists($phpMailerBase . 'php_mailer.php')) {
                    require_once $phpMailerBase . 'exception.php';
                    require_once $phpMailerBase . 'php_mailer.php';
                    require_once $phpMailerBase . 'smtp.php';
                }

                $emailSent = false;
                $smtpError = '';

                if (class_exists('\\PHPMailer\\PHPMailer\\PHPMailer') && !empty($smtpPass)) {
                    try {
                        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = $smtpHost;
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtpUser;
                        $mail->Password = $smtpPass;
                        $mail->Port = $smtpPort;
                        if ($smtpPort === 465 || $smtpSsl === 'ssl' || $smtpSsl === 'true') {
                            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                        } else {
                            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        }
                        $mail->setFrom($smtpFromEmail, $smtpFromName);
                        $mail->addAddress($clientEmail, $clientName);
                        if (!empty($smtpReplyEmail) && filter_var($smtpReplyEmail, FILTER_VALIDATE_EMAIL)) {
                            $mail->addReplyTo($smtpReplyEmail, $smtpReplyName);
                        }
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = $subject;
                        $mail->Body    = $htmlEmail;
                        $mail->AltBody = $customMessage;
                        $mail->Timeout = 10;

                        $mail->send();
                        $emailSent = true;
                        $deliveryResults['email'] = "Delivered to {$clientEmail} via SMTP";
                        $reminderSent = true;
                    } catch (\Exception $e) {
                        $smtpError = $e->getMessage();
                        // Fallback to ZeptoMail HTTP REST API (port 443)
                        if (stripos($smtpHost, 'zeptomail') !== false && !empty($smtpPass)) {
                            $zeptoKey = 'Zoho-enczapikey ' . $smtpPass;
                            $zeptoPayload = json_encode([
                                'from' => ['address' => $smtpFromEmail, 'name' => $smtpFromName],
                                'to' => [['email_address' => ['address' => $clientEmail, 'name' => $clientName]]],
                                'subject' => $subject,
                                'htmlbody' => $htmlEmail
                            ]);
                            $ch = curl_init('https://api.zeptomail.in/v1.1/email');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $zeptoPayload);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Accept: application/json',
                                'Content-Type: application/json',
                                'Authorization: ' . $zeptoKey
                            ]);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            $zRes = curl_exec($ch);
                            $zCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            curl_close($ch);
                            if ($zCode >= 200 && $zCode < 300) {
                                $emailSent = true;
                                $deliveryResults['email'] = "Delivered to {$clientEmail} via ZeptoMail API";
                                $reminderSent = true;
                            } else {
                                $deliveryResults['email'] = "SMTP error: {$smtpError} (Zepto API code: {$zCode})";
                            }
                        } else {
                            $deliveryResults['email'] = "SMTP error: {$smtpError}";
                        }
                    }
                }

                if (!$emailSent) {
                    // Fallback to mail() with proper headers
                    $headers  = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: {$smtpFromName} <{$smtpFromEmail}>\r\n";
                    $headers .= "Reply-To: {$smtpReplyEmail}\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion();
                    $sent = @mail($clientEmail, $subject, $htmlEmail, $headers);
                    if ($sent) {
                        $deliveryResults['email'] = "Delivered to {$clientEmail} via server mail";
                        $reminderSent = true;
                    } elseif (empty($deliveryResults['email'])) {
                        $deliveryResults['email'] = "Delivery failed: {$smtpError}";
                    }
                }
            } else {
                $deliveryResults['email'] = 'Skipped (No client email)';
            }
        }

        // Record reminder log in site_configuration
        setSiteConfigValue($connection, $tblPrefix, $siteId, 'last_renewal_reminder_sent', date('Y-m-d H:i:s'));
        $prevCount = (int)getSiteConfigValue($connection, $tblPrefix, $siteId, 'renewal_reminder_count', 0);
        setSiteConfigValue($connection, $tblPrefix, $siteId, 'renewal_reminder_count', $prevCount + 1);

        echo json_encode([
            'status' => 'success',
            'msg' => 'Renewal reminder processed successfully.',
            'data' => [
                'site_id' => $siteId,
                'domain' => $siteDomain,
                'recipient_name' => $clientName,
                'expiry_date' => $expiry,
                'delivery' => $deliveryResults,
                'sent_at' => date('Y-m-d H:i:s')
            ]
        ]);
        exit();
    }

    // B. Save Licenses, Quotas, Feature Toggles, & Site Settings
    if ($action === 'save_license' || $action === 'save_settings') {
        if ($siteId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid Site ID is required.']);
            exit();
        }

        $userLimit = isset($payload['user_limit']) ? max(1, (int)$payload['user_limit']) : 10;
        $waDeviceLimit = isset($payload['whatsapp_device_limit']) ? max(1, (int)$payload['whatsapp_device_limit']) : (isset($payload['wa_device_limit']) ? max(1, (int)$payload['wa_device_limit']) : 1);
        $storageLimitMb = isset($payload['storage_limit_mb']) ? max(100, (int)$payload['storage_limit_mb']) : 5120;
        $plan = trim($payload['subscription_plan'] ?? 'Professional SaaS');
        $expiry = trim($payload['subscription_expiry'] ?? '');
        $siteStatus = isset($payload['site_status']) ? (int)$payload['site_status'] : 1;

        // Upsert license configurations
        setSiteConfigValue($connection, $tblPrefix, $siteId, 'user_limit', $userLimit);
        setSiteConfigValue($connection, $tblPrefix, $siteId, 'whatsapp_device_limit', $waDeviceLimit);
        setSiteConfigValue($connection, $tblPrefix, $siteId, 'storage_limit_mb', $storageLimitMb);
        setSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_plan', $plan);
        if (!empty($expiry)) {
            setSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_expiry', $expiry);
        }

        // Subscription Pricing & Renewal Overrides
        if (isset($payload['subscription_price_monthly'])) {
            $priceM = max(0, (float)$payload['subscription_price_monthly']);
            setSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_price_monthly', $priceM);
        }
        if (isset($payload['subscription_price_yearly'])) {
            $priceY = max(0, (float)$payload['subscription_price_yearly']);
            setSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_price_yearly', $priceY);
        }
        if (!empty($payload['subscription_currency'])) {
            $curr = trim($payload['subscription_currency']);
            setSiteConfigValue($connection, $tblPrefix, $siteId, 'subscription_currency', $curr);
        }
        if (!empty($payload['billing_interval'])) {
            $bInt = trim($payload['billing_interval']);
            setSiteConfigValue($connection, $tblPrefix, $siteId, 'billing_interval', $bInt);
        }
        if (isset($payload['renewal_price_override'])) {
            $overrideVal = !empty($payload['renewal_price_override']) ? '1' : '0';
            setSiteConfigValue($connection, $tblPrefix, $siteId, 'renewal_price_override', $overrideVal);
        }
        if (isset($payload['renewal_pricing_notes'])) {
            $rNotes = trim($payload['renewal_pricing_notes']);
            setSiteConfigValue($connection, $tblPrefix, $siteId, 'renewal_pricing_notes', $rNotes);
        }

        // Feature flags toggling
        $features = $payload['features'] ?? [];
        if (is_array($features)) {
            foreach ($features as $fKey => $fVal) {
                $flagVal = (!empty($fVal) && $fVal !== '0') ? '1' : '0';
                setSiteConfigValue($connection, $tblPrefix, $siteId, $fKey, $flagVal);
            }
        }

        // Update site table fields if provided
        $siteName = trim($payload['site_name'] ?? '');
        $siteUrl = trim($payload['site_url'] ?? '');
        $themeId = isset($payload['site_theme_id']) ? (int)$payload['site_theme_id'] : 0;
        $dashboardVersion = isset($payload['site_dashboard_version']) ? (int)$payload['site_dashboard_version'] : 0;

        $updateCols = ["site_status = {$siteStatus}"];
        if (!empty($siteName)) {
            $escN = mysqli_real_escape_string($connection, $siteName);
            $updateCols[] = "site_name = '{$escN}'";
        }
        if (!empty($siteUrl)) {
            $escU = mysqli_real_escape_string($connection, strtolower($siteUrl));
            $updateCols[] = "site_url = '{$escU}'";
        }
        if ($themeId > 0) {
            $updateCols[] = "site_theme_id = {$themeId}";
        }
        if ($dashboardVersion > 0) {
            $updateCols[] = "site_dashboard_version = {$dashboardVersion}";
        }

        mysqli_query($connection, "UPDATE {$tblPrefix}site SET " . implode(', ', $updateCols) . " WHERE site_id = {$siteId}");

        // Update theme color in site_setting if provided
        if (!empty($payload['site_theme_color'])) {
            $escColor = mysqli_real_escape_string($connection, trim($payload['site_theme_color']));
            $ssChk = mysqli_query($connection, "SELECT site_setting_id FROM {$tblPrefix}site_setting WHERE site_setting_site_id = {$siteId} LIMIT 1");
            if ($ssChk && mysqli_num_rows($ssChk) > 0) {
                mysqli_query($connection, "UPDATE {$tblPrefix}site_setting SET site_theme_color = '{$escColor}' WHERE site_setting_site_id = {$siteId}");
            } else {
                mysqli_query($connection, "INSERT INTO {$tblPrefix}site_setting (site_setting_site_id, site_theme_color, site_background_color, site_custom_domain, site_pagination_limit, site_sitemap_limit, site_feed_limit, site_image_quality, site_thumbnail_width, site_thumbnail_height, site_ads_txt, site_mainfest_json, site_robots_txt)
                                           VALUES ({$siteId}, '{$escColor}', '#ffffff', 1, 20, 1000, 50, 80, '150', '150', '', '', '')");
            }
        }

        echo json_encode([
            'status' => 'success',
            'msg' => 'Site settings, licenses, and configurations updated successfully!',
            'site_id' => $siteId
        ]);
        exit();
    }

    // B. Toggle Status
    if ($action === 'toggle_status') {
        if ($siteId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Site ID']);
            exit();
        }
        $newStatus = !empty($payload['status']) ? 1 : 0;
        $up = mysqli_query($connection, "UPDATE {$tblPrefix}site SET site_status = {$newStatus} WHERE site_id = {$siteId}");
        if ($up) {
            echo json_encode(['status' => 'success', 'msg' => 'Site status updated successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
        exit();
    }

    // C. Assign / Change Referral Partner for Site
    if ($action === 'assign_partner') {
        if ($siteId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid Site ID']);
            exit();
        }

        $partnerId = isset($payload['partner_id']) ? (int)$payload['partner_id'] : 0;
        $commissionRate = isset($payload['commission_rate']) ? (float)$payload['commission_rate'] : 10.0;
        $commissionAmount = isset($payload['commission_amount']) ? (float)$payload['commission_amount'] : 1000.0;
        $notes = trim($payload['notes'] ?? '');

        // Store partner_id in site_configuration
        setSiteConfigValue($connection, $tblPrefix, $siteId, 'partner_id', (string)$partnerId);

        if ($partnerId > 0) {
            $escNotes = mysqli_real_escape_string($connection, !empty($notes) ? $notes : "Referral partner assigned from Control Panel");
            $chkComm = mysqli_query($connection, "SELECT commission_id FROM {$tblPrefix}control_panel_commission WHERE commission_site_id = {$siteId} LIMIT 1");
            if ($chkComm && mysqli_num_rows($chkComm) > 0) {
                $cRow = mysqli_fetch_assoc($chkComm);
                $cId = (int)$cRow['commission_id'];
                mysqli_query($connection, "UPDATE {$tblPrefix}control_panel_commission 
                                           SET commission_partner_id = {$partnerId}, commission_rate_percent = {$commissionRate}" . (!empty($notes) ? ", commission_notes = '{$escNotes}'" : "") . " 
                                           WHERE commission_id = {$cId}");
            } else {
                mysqli_query($connection, "INSERT INTO {$tblPrefix}control_panel_commission 
                                           (commission_partner_id, commission_site_id, commission_rate_percent, commission_amount, commission_status, commission_notes)
                                           VALUES ({$partnerId}, {$siteId}, {$commissionRate}, {$commissionAmount}, 'pending', '{$escNotes}')");
            }
        }

        echo json_encode([
            'status' => 'success',
            'msg' => $partnerId > 0 ? 'Referral partner assigned to site successfully!' : 'Partner unassigned from site.',
            'site_id' => $siteId,
            'partner_id' => $partnerId
        ]);
        exit();
    }

    // D. 1-Click Provision New Site or Update Existing Site
    $siteName = trim($payload['site_name'] ?? '');
    $siteDomain = trim($payload['site_domain'] ?? '');
    $siteUrl = trim($payload['site_url'] ?? '');
    $appId = isset($payload['site_app_id']) ? (int)$payload['site_app_id'] : 0;
    $themeId = isset($payload['site_theme_id']) ? (int)$payload['site_theme_id'] : 0;
    $dashboardVersion = isset($payload['site_dashboard_version']) ? (int)$payload['site_dashboard_version'] : 3;
    $status = isset($payload['site_status']) ? (int)$payload['site_status'] : 1;
    $ownerEmail = trim($payload['owner_email'] ?? '');
    $partnerId = isset($payload['partner_id']) ? (int)$payload['partner_id'] : 0;

    // License Quotas from Provisioning Form
    $userLimit = isset($payload['user_limit']) ? max(1, (int)$payload['user_limit']) : 10;
    $waDeviceLimit = isset($payload['whatsapp_device_limit']) ? max(1, (int)$payload['whatsapp_device_limit']) : 1;

    if (empty($siteName) || empty($siteDomain) || $appId <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'msg' => 'Site Name, Domain, and App selection are required.']);
        exit();
    }

    if (empty($siteUrl)) {
        $siteUrl = $siteDomain;
    }

    $escName = mysqli_real_escape_string($connection, $siteName);
    $escDomain = mysqli_real_escape_string($connection, strtolower($siteDomain));
    $escUrl = mysqli_real_escape_string($connection, strtolower($siteUrl));

    if ($themeId <= 0) {
        $thQ = mysqli_query($connection, "SELECT theme_id FROM {$tblPrefix}theme WHERE theme_app_id = {$appId} AND theme_status = 1 ORDER BY theme_id ASC LIMIT 1");
        if ($thQ && $thRow = mysqli_fetch_assoc($thQ)) {
            $themeId = (int)$thRow['theme_id'];
        } else {
            $themeId = 1;
        }
    }

    if ($siteId > 0) {
        $updateSql = "UPDATE {$tblPrefix}site SET 
                        site_name = '{$escName}',
                        site_domain = '{$escDomain}',
                        site_url = '{$escUrl}',
                        site_app_id = {$appId},
                        site_theme_id = {$themeId},
                        site_dashboard_version = {$dashboardVersion},
                        site_status = {$status}
                      WHERE site_id = {$siteId}";
        if (mysqli_query($connection, $updateSql)) {
            echo json_encode(['status' => 'success', 'msg' => 'Site details updated successfully.', 'site_id' => $siteId]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    } else {
        $chk = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_domain = '{$escDomain}' OR site_url = '{$escUrl}' LIMIT 1");
        if (mysqli_num_rows($chk) > 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => "Domain or URL '{$siteDomain}' is already in use by another site."]);
            exit();
        }

        $ownerId = 1;
        if (!empty($ownerEmail)) {
            $escEmail = mysqli_real_escape_string($connection, $ownerEmail);
            $uQ = mysqli_query($connection, "SELECT user_id FROM {$tblPrefix}user WHERE user_email = '{$escEmail}' LIMIT 1");
            if ($uQ && $uRow = mysqli_fetch_assoc($uQ)) {
                $ownerId = (int)$uRow['user_id'];
            } else {
                $cleanUser = substr(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $ownerEmail)[0]), 0, 14);
                $username = ($cleanUser ?: 'owner') . '_' . rand(100, 999);
                $salt = hash('sha256', uniqid(mt_rand(), true) . time());
                $defPass = hash('sha256', $salt . 'Blumox@123');
                $escUser = mysqli_real_escape_string($connection, $username);
                $regIp = mysqli_real_escape_string($connection, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                mysqli_query($connection, "INSERT INTO {$tblPrefix}user (user_parent_id, user_username, user_email, user_password, user_salt_key, user_status, user_reg_time, user_reg_ip, user_refer_id) 
                                           VALUES (0, '{$escUser}', '{$escEmail}', '{$defPass}', '{$salt}', 1, NOW(), '{$regIp}', 0)");
                $ownerId = mysqli_insert_id($connection);
                mysqli_query($connection, "INSERT INTO {$tblPrefix}profile (profile_user_id, profile_display_name, profile_email) VALUES ({$ownerId}, '{$escName} Owner', '{$escEmail}')");
            }
        }

        $insertSite = "INSERT INTO {$tblPrefix}site (site_owner_id, site_lang, site_domain, site_url, site_name, site_app_id, site_theme_id, site_dashboard_version, site_status)
                       VALUES ({$ownerId}, 'en', '{$escDomain}', '{$escUrl}', '{$escName}', {$appId}, {$themeId}, {$dashboardVersion}, {$status})";
        if (mysqli_query($connection, $insertSite)) {
            $newSiteId = mysqli_insert_id($connection);

            mysqli_query($connection, "INSERT INTO {$tblPrefix}site_access (site_access_site_id, site_access_user_id, site_access_type, site_access_status)
                                       VALUES ({$newSiteId}, {$ownerId}, 'administrator', 1)");

            mysqli_query($connection, "INSERT INTO {$tblPrefix}site_setting (site_setting_site_id, site_theme_color, site_background_color)
                                       VALUES ({$newSiteId}, '#2798F5', '#ffffff')");

            // Seed user limit and whatsapp device limit
            setSiteConfigValue($connection, $tblPrefix, $newSiteId, 'user_limit', $userLimit);
            setSiteConfigValue($connection, $tblPrefix, $newSiteId, 'whatsapp_device_limit', $waDeviceLimit);
            setSiteConfigValue($connection, $tblPrefix, $newSiteId, 'subscription_plan', 'Professional SaaS');
            setSiteConfigValue($connection, $tblPrefix, $newSiteId, 'subscription_expiry', date('Y-m-d', strtotime('+1 year')));

            if ($partnerId > 0) {
                mysqli_query($connection, "INSERT INTO {$tblPrefix}control_panel_commission (commission_partner_id, commission_site_id, commission_rate_percent, commission_amount, commission_status, commission_notes)
                                           VALUES ({$partnerId}, {$newSiteId}, 10.00, 1000.00, 'pending', 'Automatic commission record for newly provisioned site {$escDomain}')");
            }

            echo json_encode([
                'status' => 'success',
                'msg' => 'Client site provisioned successfully with configured licenses!',
                'site_id' => $newSiteId,
                'domain' => $siteDomain
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => mysqli_error($connection)]);
        }
    }
    exit();
}
