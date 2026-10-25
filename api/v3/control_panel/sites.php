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

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
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

        echo json_encode([
            'status' => 'success',
            'data' => [
                'site_id'               => (int)$site['site_id'],
                'site_name'             => htmlspecialchars($site['site_name']),
                'site_domain'           => htmlspecialchars($site['site_domain']),
                'site_url'              => htmlspecialchars($site['site_url']),
                'app_title'             => htmlspecialchars($site['app_title'] ?? 'Generic'),
                'theme_title'           => htmlspecialchars($site['theme_title'] ?? 'Default Theme'),
                'site_status'           => (int)$site['site_status'],
                'owner_name'            => htmlspecialchars($site['profile_display_name'] ?? $site['user_username'] ?? 'Owner'),
                'owner_email'           => htmlspecialchars($site['user_email'] ?? ''),
                'active_users'          => $activeUsers,
                'user_limit'            => $userLimit,
                'active_wa_devices'     => $activeWaDevices,
                'wa_device_limit'       => $waDeviceLimit,
                'storage_limit_mb'      => $storageLimitMb,
                'features'              => $features,
                'subscription_plan'     => $plan,
                'subscription_expiry'   => $expiry
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
    $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;

    $where = [];
    if (!empty($search)) {
        $where[] = "(s.site_name LIKE '%{$search}%' OR s.site_domain LIKE '%{$search}%' OR s.site_url LIKE '%{$search}%' OR u.user_email LIKE '%{$search}%' OR u.user_username LIKE '%{$search}%')";
    }
    if ($appId > 0) {
        $where[] = "s.site_app_id = {$appId}";
    }
    if ($status !== null) {
        $where[] = "s.site_status = {$status}";
    }
    $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

    $countSql = "SELECT COUNT(*) as total FROM {$tblPrefix}site s 
                 LEFT JOIN {$tblPrefix}user u ON s.site_owner_id = u.user_id 
                 {$whereSql}";
    $countRes = mysqli_query($connection, $countSql);
    $total = ($countRes && $r = mysqli_fetch_assoc($countRes)) ? (int)$r['total'] : 0;

    $sql = "SELECT s.*, a.app_title, a.app_path, t.theme_title, t.theme_path, u.user_email, u.user_username, u.user_mobile,
            (SELECT COUNT(*) FROM {$tblPrefix}site_access sa WHERE sa.site_access_site_id = s.site_id AND sa.site_access_status = 1) as users_count,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'user_limit' LIMIT 1) as user_limit,
            (SELECT site_configuration_value FROM {$tblPrefix}site_configuration sc WHERE sc.site_configuration_site_id = s.site_id AND sc.site_configuration_key = 'whatsapp_device_limit' LIMIT 1) as wa_limit,
            (SELECT COUNT(*) FROM {$tblPrefix}whatsapp_device wd WHERE wd.device_site_id = s.site_id AND wd.device_status = 1) as wa_devices_count
            FROM {$tblPrefix}site s
            LEFT JOIN {$tblPrefix}app a ON s.site_app_id = a.app_id
            LEFT JOIN {$tblPrefix}theme t ON s.site_theme_id = t.theme_id
            LEFT JOIN {$tblPrefix}user u ON s.site_owner_id = u.user_id
            {$whereSql}
            ORDER BY s.site_id DESC
            LIMIT {$offset}, {$limit}";

    $res = mysqli_query($connection, $sql);
    $sites = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $uLim = !empty($row['user_limit']) && is_numeric($row['user_limit']) ? (int)$row['user_limit'] : 10;
            $waLim = !empty($row['wa_limit']) && is_numeric($row['wa_limit']) ? (int)$row['wa_limit'] : 1;

            $sites[] = [
                'id' => (int)$row['site_id'],
                'name' => htmlspecialchars($row['site_name'] ?? ''),
                'domain' => htmlspecialchars($row['site_domain'] ?? ''),
                'url' => htmlspecialchars($row['site_url'] ?? ''),
                'app_id' => (int)$row['site_app_id'],
                'app_title' => htmlspecialchars($row['app_title'] ?? 'Generic'),
                'app_path' => htmlspecialchars($row['app_path'] ?? ''),
                'theme_id' => (int)$row['site_theme_id'],
                'theme_title' => htmlspecialchars($row['theme_title'] ?? 'Default Theme'),
                'dashboard_version' => (int)$row['site_dashboard_version'],
                'owner_id' => (int)$row['site_owner_id'],
                'owner_email' => htmlspecialchars($row['user_email'] ?? ''),
                'owner_name' => htmlspecialchars($row['user_username'] ?? ''),
                'owner_mobile' => htmlspecialchars($row['user_mobile'] ?? ''),
                'status' => (int)$row['site_status'],
                'users_count' => (int)$row['users_count'],
                'user_limit' => $uLim,
                'wa_devices_count' => (int)$row['wa_devices_count'],
                'wa_device_limit' => $waLim,
                'logo' => htmlspecialchars($row['site_logo'] ?? ''),
                'icon' => htmlspecialchars($row['site_icon'] ?? '')
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $sites,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => ceil($total / $limit)
    ]);
    exit();
}

// --------------------------------------------------------------------------
// 2. POST: Provision, Toggle Status, or Save Licenses & Features
// --------------------------------------------------------------------------
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = $payload['action'] ?? 'provision';
    $siteId = isset($payload['site_id']) ? (int)$payload['site_id'] : 0;

    // A. Save Licenses & Feature Toggles
    if ($action === 'save_license') {
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

        // Feature flags toggling
        $features = $payload['features'] ?? [];
        if (is_array($features)) {
            foreach ($features as $fKey => $fVal) {
                $flagVal = (!empty($fVal) && $fVal !== '0') ? '1' : '0';
                setSiteConfigValue($connection, $tblPrefix, $siteId, $fKey, $flagVal);
            }
        }

        // Update site status
        mysqli_query($connection, "UPDATE {$tblPrefix}site SET site_status = {$siteStatus} WHERE site_id = {$siteId}");

        echo json_encode([
            'status' => 'success',
            'msg' => 'Licenses and feature toggles updated successfully!',
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

    // C. 1-Click Provision New Site or Update Existing Site
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
