<?php

/**
 * V3 Website Configuration API Endpoint
 * Path: /api/v3/setting/configuration.php
 */

header('Content-Type: application/json');

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$siteId = defined('SITE_ID') ? (int)SITE_ID : 0;

// Host fallback if SITE_ID is not defined or 0
if ($siteId <= 0) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $escapedHost = mysqli_real_escape_string($connection, $host);
    $siteQuery = mysqli_query($connection, "SELECT site_id FROM " . TABLE_PREFIX . "site WHERE site_domain = '{$escapedHost}' OR site_url LIKE '%{$escapedHost}%' LIMIT 1");
    if ($siteQuery && mysqli_num_rows($siteQuery) > 0) {
        $row = mysqli_fetch_assoc($siteQuery);
        $siteId = (int)$row['site_id'];
    }
}

if ($siteId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Active site identifier could not be resolved.'
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 1. GET: Fetch Current Website Configuration
// --------------------------------------------------------------------------
if ($method === 'GET') {
    $sql = "SELECT s.site_id, s.site_name, s.site_description, s.site_keyword, s.site_lang,
                   s.site_index, s.site_status, s.site_domain, s.site_app_id,
                   s.site_logo, s.site_icon,
                   ss.site_theme_color, ss.site_custom_domain
            FROM " . TABLE_PREFIX . "site s
            LEFT JOIN " . TABLE_PREFIX . "site_setting ss ON ss.site_setting_site_id = s.site_id
            WHERE s.site_id = {$siteId}
            LIMIT 1";

    $res = mysqli_query($connection, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'msg' => 'Website configuration not found.'
        ]);
        exit;
    }

    $site = mysqli_fetch_assoc($res);

    // Read site_timezone from isw_site_configuration
    $siteTimezone = 'Asia/Kolkata';
    $tzQuery = mysqli_query($connection, "SELECT site_configuration_value FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'site_timezone' LIMIT 1");
    if ($tzQuery && $tzRow = mysqli_fetch_assoc($tzQuery)) {
        if (!empty($tzRow['site_configuration_value'])) {
            $siteTimezone = trim($tzRow['site_configuration_value']);
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'site_id' => (int)$site['site_id'],
            'site_name' => $site['site_name'] ?? '',
            'site_description' => $site['site_description'] ?? '',
            'site_keyword' => $site['site_keyword'] ?? '',
            'site_lang' => $site['site_lang'] ?: 'en',
            'site_timezone' => $siteTimezone,
            'site_index' => $site['site_index'] ?: 'index, follow',
            'site_domain' => $site['site_domain'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : ''),
            'site_logo' => $site['site_logo'] ?? '',
            'site_icon' => $site['site_icon'] ?? '',
            'site_theme_color' => $site['site_theme_color'] ?: '#4f46e5',
            'site_app' => $site['site_app'] ?? 'cms',
            'site_status' => (int)($site['site_status'] ?? 1)
        ]
    ]);
    exit;
}

// --------------------------------------------------------------------------
// 2. POST / PUT: Update Website Configuration
// --------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true) ?? [];
    $data = !empty($_POST) ? $_POST : $jsonData;

    $siteName = trim($data['site_name'] ?? '');
    $siteDescription = trim($data['site_description'] ?? '');
    $siteKeyword = trim($data['site_keyword'] ?? '');
    $siteLang = trim($data['site_lang'] ?? 'en');
    $siteIndex = trim($data['site_index'] ?? 'index, follow');
    $siteTheme = trim($data['site_theme'] ?? ($data['site_theme_color'] ?? '#4f46e5'));

    if (empty($siteName)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'warning',
            'msg' => 'Website Name is required.'
        ]);
        exit;
    }

    $rootDir    = defined('ROOT') ? ROOT : (!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3));
    $siteDomain = defined('SITE_DOMAIN') ? SITE_DOMAIN : 'default';
    $uploadPath = $rootDir . '/upload/' . $siteDomain . '/' . date('Y/m/');

    if (!is_dir($uploadPath)) {
        if (!@mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
            $uploadPath = $rootDir . '/upload/' . date('Y/m/');
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
        }
    }

    // 1. Handle Website Logo Upload
    $newSiteLogo = '';
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $allowedLogoExts = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
        $ext = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedLogoExts)) {
            $uniquePrefix = time() . '_' . bin2hex(random_bytes(4));
            $cleanName    = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $_FILES['site_logo']['name']));
            $filename     = 'logo_' . $siteId . '_' . $uniquePrefix . '_' . $cleanName;
            $uploadFile   = $uploadPath . $filename;
            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $uploadFile)) {
                $newSiteLogo = str_replace($rootDir, '', $uploadFile);
            }
        }
    }

    // 2. Handle Website Favicon / Icon Upload
    $newSiteIcon = '';
    if (isset($_FILES['site_icon']) && $_FILES['site_icon']['error'] === UPLOAD_ERR_OK) {
        $allowedIconExts = ['ico', 'png', 'svg', 'webp', 'jpg', 'jpeg'];
        $ext = strtolower(pathinfo($_FILES['site_icon']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedIconExts)) {
            $uniquePrefix = time() . '_' . bin2hex(random_bytes(4));
            $cleanName    = strtolower(preg_replace('~[^\p{L}\p{N}\._-]+~u', '', $_FILES['site_icon']['name']));
            $filename     = 'favicon_' . $siteId . '_' . $uniquePrefix . '_' . $cleanName;
            $uploadFile   = $uploadPath . $filename;
            if (move_uploaded_file($_FILES['site_icon']['tmp_name'], $uploadFile)) {
                $newSiteIcon = str_replace($rootDir, '', $uploadFile);
            }
        }
    }

    $escapedName = mysqli_real_escape_string($connection, $siteName);
    $escapedDesc = mysqli_real_escape_string($connection, $siteDescription);
    $escapedKw   = mysqli_real_escape_string($connection, $siteKeyword);
    $escapedLang = mysqli_real_escape_string($connection, $siteLang);
    $escapedIdx  = mysqli_real_escape_string($connection, $siteIndex);
    $escapedThm  = mysqli_real_escape_string($connection, $siteTheme);

    $logoClause = !empty($newSiteLogo) ? ", site_logo = '" . mysqli_real_escape_string($connection, $newSiteLogo) . "'" : "";
    $iconClause = !empty($newSiteIcon) ? ", site_icon = '" . mysqli_real_escape_string($connection, $newSiteIcon) . "'" : "";

    mysqli_query($connection, "START TRANSACTION");

    $updateSite = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site SET
        site_name = '{$escapedName}',
        site_description = '{$escapedDesc}',
        site_keyword = '{$escapedKw}',
        site_lang = '{$escapedLang}',
        site_index = '{$escapedIdx}'
        {$logoClause}
        {$iconClause}
        WHERE site_id = {$siteId}");

    // Check if site_setting row exists
    $checkSetting = mysqli_query($connection, "SELECT site_setting_id FROM " . TABLE_PREFIX . "site_setting WHERE site_setting_site_id = {$siteId} LIMIT 1");
    if ($checkSetting && mysqli_num_rows($checkSetting) > 0) {
        $updateSetting = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_setting SET
            site_theme_color = '{$escapedThm}'
            WHERE site_setting_site_id = {$siteId}");
    } else {
        $updateSetting = mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_setting(site_setting_site_id, site_theme_color) VALUES({$siteId}, '{$escapedThm}')");
    }

    // Save site_timezone into isw_site_configuration
    $siteTimezone = trim($data['site_timezone'] ?? 'Asia/Kolkata');
    if (empty($siteTimezone)) {
        $siteTimezone = 'Asia/Kolkata';
    }
    $escapedTz = mysqli_real_escape_string($connection, $siteTimezone);
    $checkTz = mysqli_query($connection, "SELECT site_configuration_id FROM " . TABLE_PREFIX . "site_configuration WHERE site_configuration_site_id = {$siteId} AND site_configuration_key = 'site_timezone' LIMIT 1");
    if ($checkTz && mysqli_num_rows($checkTz) > 0) {
        $tzRow = mysqli_fetch_assoc($checkTz);
        mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "site_configuration SET site_configuration_value = '{$escapedTz}', site_configuration_status = 1 WHERE site_configuration_id = " . (int)$tzRow['site_configuration_id']);
    } else {
        mysqli_query($connection, "INSERT INTO " . TABLE_PREFIX . "site_configuration (site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status, site_configuration_global) VALUES ({$siteId}, 'site_timezone', '{$escapedTz}', 1, 0)");
    }

    if ($updateSite && $updateSetting) {
        mysqli_query($connection, "COMMIT");
        echo json_encode([
            'status' => 'success',
            'msg' => 'Website configuration and brand assets saved successfully.',
            'site_timezone' => $siteTimezone,
            'site_logo' => $newSiteLogo,
            'site_icon' => $newSiteIcon
        ]);
    } else {
        mysqli_query($connection, "ROLLBACK");
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg' => 'Failed to save configuration: ' . mysqli_error($connection)
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode([
    'status' => 'error',
    'msg' => 'Method not allowed.'
]);
