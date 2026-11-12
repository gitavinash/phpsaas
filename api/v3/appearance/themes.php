<?php

/**
 * V3 Website Themes Management & Live Preview API Endpoint
 * Path: /api/v3/appearance/themes.php
 * Route: /api/v3/appearance/themes/
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/') : dirname(__DIR__, 3);
if (!isset($connection)) {
    if (file_exists($docRoot . '/lib/modules/siteStatus.php')) {
        require_once $docRoot . '/lib/modules/siteStatus.php';
    } elseif (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
        if (file_exists($docRoot . '/lib/modules/connection.php')) {
            require_once $docRoot . '/lib/modules/connection.php';
        }
    }
}
if (!defined('LOGIN_CHECK') && file_exists($docRoot . '/lib/configuration/index.php')) {
    require_once $docRoot . '/lib/configuration/index.php';
}
require_once $docRoot . '/lib/modules/themePreview.php';

// Enforce session / cookie authentication guard
require_once LOGIN_CHECK;

$tblPrefix  = defined('TABLE_PREFIX') ? (string) TABLE_PREFIX : 'isw_';
$siteId     = defined('SITE_ID') ? (int) SITE_ID : 0;
$userId     = defined('USER_ID') ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? strtolower(trim((string) $site_access_type)) : 'visitor';

$isPrivilegedUser = in_array($accessType, ['administrator', 'admin', 'superadmin', 'operations', 'owner', 'author'], true);

function tmApiRespond(int $httpCode, array $payload): void
{
    http_response_code($httpCode);
    echo json_encode($payload);
    exit;
}

if ($siteId <= 0) {
    tmApiRespond(400, [
        'status'  => 'error',
        'code'    => 'invalid_site',
        'message' => 'Active site identifier could not be resolved.',
        'msg'     => 'Active site identifier could not be resolved.',
    ]);
}

if (!$isPrivilegedUser) {
    tmApiRespond(403, [
        'status'  => 'error',
        'code'    => 'access_denied',
        'message' => 'Access denied for current user role.',
        'msg'     => 'Access denied for current user role.',
    ]);
}

// Parse input payload cleanly (JSON body, POST, GET)
$rawInput = file_get_contents('php://input');
$jsonBody = [];
if (is_string($rawInput) && $rawInput !== '') {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $jsonBody = $decoded;
    }
}
$reqData = array_merge($_GET, $_POST, $jsonBody);

/**
 * Resolve requested theme ID from numeric or base64 value.
 */
function tmApiParseThemeId(array $reqData): int
{
    $rawId = $reqData['theme_id'] ?? ($reqData['id'] ?? null);
    if ($rawId === null) {
        return 0;
    }
    $trimmed = trim((string) $rawId);
    if ($trimmed === '') {
        return 0;
    }
    if (ctype_digit($trimmed) && (int) $trimmed > 0) {
        return (int) $trimmed;
    }
    $decoded = base64_decode($trimmed, true);
    if ($decoded !== false && base64_encode($decoded) === $trimmed && ctype_digit($decoded) && (int) $decoded > 0) {
        return (int) $decoded;
    }
    return -1;
}

/**
 * Fetch current tenant site's app & live theme context from DB.
 */
function tmApiFetchSiteContext(mysqli $connection, string $tblPrefix, int $siteId, string $docRoot): ?array
{
    $sql = "SELECT
                s.site_id,
                s.site_name,
                s.site_domain,
                s.site_url,
                s.site_app_id,
                s.site_theme_id,
                a.app_title,
                a.app_path,
                t.theme_title AS live_theme_title,
                t.theme_path AS live_theme_path,
                t.theme_version AS live_theme_version
            FROM `{$tblPrefix}site` s
            INNER JOIN `{$tblPrefix}app` a ON a.app_id = s.site_app_id
            LEFT JOIN `{$tblPrefix}theme` t ON t.theme_id = s.site_theme_id
            WHERE s.site_id = {$siteId}
            LIMIT 1";
    $res = mysqli_query($connection, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        return null;
    }
    $row = mysqli_fetch_assoc($res);

    $protocol = iswThemePreviewIsHttps() ? 'https://' : 'http://';
    $activeHost = !empty($_SERVER['HTTP_HOST']) ? trim((string) $_SERVER['HTTP_HOST']) : '';
    $dbDomain = trim((string) ($row['site_domain'] ?? ''));
    $baseUrl = $activeHost !== ''
        ? ($protocol . $activeHost)
        : ($dbDomain !== '' ? ($protocol . $dbDomain) : rtrim((string) ($row['site_url'] ?? ''), '/'));

    return [
        'id'                 => (int) $row['site_id'],
        'name'               => (string) ($row['site_name'] ?? ''),
        'domain'             => $activeHost !== '' ? $activeHost : $dbDomain,
        'base_url'           => rtrim($baseUrl, '/'),
        'app_id'             => (int) ($row['site_app_id'] ?? 0),
        'app_title'          => (string) ($row['app_title'] ?? ''),
        'app_path'           => (string) ($row['app_path'] ?? ''),
        'live_theme_id'      => (int) ($row['site_theme_id'] ?? 0),
        'live_theme_title'   => (string) ($row['live_theme_title'] ?? ''),
        'live_theme_path'    => (string) ($row['live_theme_path'] ?? ''),
        'live_theme_version' => (string) ($row['live_theme_version'] ?? ''),
        'doc_root'           => $docRoot,
    ];
}

/**
 * Normalize optional theme preview image URL and check local existence.
 */
function tmApiResolveThemeImage(string $rawImage, string $docRoot): string
{
    $img = trim($rawImage);
    if ($img === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $img)) {
        return $img;
    }
    $rel = '/' . ltrim($img, '/');
    if (is_file($docRoot . $rel)) {
        return $rel;
    }
    return '';
}

/**
 * Purge cached static HTML files for the active site domain when switching themes.
 */
function tmApiPurgeSiteHtmlCache(string $docRoot, array $siteCtx): void
{
    $candidates = [];
    if (defined('SITE_DOMAIN') && SITE_DOMAIN !== '') {
        $candidates[] = (string) SITE_DOMAIN;
    }
    if (!empty($siteCtx['domain'])) {
        $candidates[] = (string) $siteCtx['domain'];
    }
    foreach (array_unique($candidates) as $dom) {
        $cleanDom = preg_replace('/[^A-Za-z0-9._:-]/', '', $dom);
        if ($cleanDom === '') {
            continue;
        }
        $siteCacheDir = $docRoot . '/cache/' . $cleanDom;
        if (!is_dir($siteCacheDir)) {
            continue;
        }
        foreach (['desktop', 'mobile'] as $ver) {
            $dir = $siteCacheDir . '/' . $ver;
            if (!is_dir($dir)) {
                continue;
            }
            $files = @glob($dir . '/*.html');
            if (is_array($files)) {
                foreach ($files as $f) {
                    if (is_file($f)) {
                        @unlink($f);
                    }
                }
            }
        }
    }
}

/**
 * Build full themes payload (catalog for current app, active live theme, preview status, stats).
 */
function tmApiBuildPayload(
    mysqli $connection,
    string $tblPrefix,
    array $siteCtx,
    string $searchFilter = '',
    ?array $previewOverride = null
): array {
    $siteId = (int) $siteCtx['id'];
    $siteAppId = (int) $siteCtx['app_id'];
    $siteAppPath = (string) $siteCtx['app_path'];
    $liveThemeId = (int) $siteCtx['live_theme_id'];
    $baseUrl = (string) $siteCtx['base_url'];
    $docRoot = (string) $siteCtx['doc_root'];

    if ($previewOverride !== null) {
        $previewActive = !empty($previewOverride['active']);
        $previewThemeRow = $previewOverride['theme'] ?? null;
    } else {
        $resolvedPreview = iswResolveSiteThemePreview($connection, $siteId, $siteAppId, $siteAppPath, $liveThemeId);
        $previewActive = !empty($resolvedPreview['active']);
        $previewThemeRow = $resolvedPreview['theme'] ?? null;
    }
    $previewThemeId = ($previewActive && is_array($previewThemeRow)) ? (int) ($previewThemeRow['theme_id'] ?? 0) : 0;

    $sql = "SELECT
                t.theme_id,
                t.theme_app_id,
                t.theme_package_id,
                t.theme_title,
                t.theme_description,
                t.theme_image,
                t.theme_path,
                t.theme_version,
                t.theme_status,
                t.theme_publish_time,
                t.theme_update_time,
                a.app_title,
                a.app_path
            FROM `{$tblPrefix}theme` t
            LEFT JOIN `{$tblPrefix}app` a ON a.app_id = t.theme_app_id
            WHERE (
                (t.theme_app_id = {$siteAppId} AND t.theme_status = 1)
                OR t.theme_id = {$liveThemeId}
            )
            ORDER BY (t.theme_id = {$liveThemeId}) DESC, t.theme_title ASC, t.theme_id ASC";

    $res = mysqli_query($connection, $sql);
    if (!$res) {
        throw new Exception('Failed to query themes catalog for this application.');
    }

    $allItems = [];
    $readyCount = 0;
    $missingCount = 0;
    $currentThemeTitle = (string) ($siteCtx['live_theme_title'] ?? '');
    $previewThemeTitle = ($previewActive && is_array($previewThemeRow)) ? (string) ($previewThemeRow['theme_title'] ?? '') : '';

    while ($row = mysqli_fetch_assoc($res)) {
        $tid = (int) ($row['theme_id'] ?? 0);
        $tAppId = (int) ($row['theme_app_id'] ?? 0);
        $tPath = (string) ($row['theme_path'] ?? '');
        $avail = iswCheckThemeTemplateAvailability($siteAppPath, $tPath);
        $templateExists = !empty($avail['template_exists']);
        $isActive = ($tid === $liveThemeId);
        $isPreviewing = ($previewActive && $tid === $previewThemeId);

        if ($templateExists) {
            $readyCount++;
        } else {
            $missingCount++;
        }
        if ($isActive && $currentThemeTitle === '') {
            $currentThemeTitle = (string) ($row['theme_title'] ?? '');
        }

        $previewUrl = $templateExists ? iswBuildThemePreviewUrl($baseUrl, $siteId, $tid) : '';

        $item = [
            'id'              => $tid,
            'encoded_id'      => base64_encode((string) $tid),
            'app_id'          => $tAppId,
            'app_title'       => (string) ($row['app_title'] ?? $siteCtx['app_title']),
            'app_path'        => (string) ($row['app_path'] ?? $siteAppPath),
            'package_id'      => (int) ($row['theme_package_id'] ?? 0),
            'title'           => trim((string) ($row['theme_title'] ?? 'Untitled Theme')),
            'description'     => trim((string) ($row['theme_description'] ?? '')),
            'image'           => tmApiResolveThemeImage((string) ($row['theme_image'] ?? ''), $docRoot),
            'path'            => $tPath,
            'version'         => trim((string) ($row['theme_version'] ?? '1.0')),
            'status'          => (int) ($row['theme_status'] ?? 0),
            'is_current_app'  => ($tAppId === $siteAppId),
            'template_exists' => $templateExists,
            'has_desktop'     => !empty($avail['has_desktop']),
            'has_mobile'      => !empty($avail['has_mobile']),
            'is_active'       => $isActive,
            'is_previewing'   => $isPreviewing,
            'preview_url'     => $previewUrl,
            'created_at'      => (string) ($row['theme_publish_time'] ?? ''),
            'updated_at'      => (string) ($row['theme_update_time'] ?? ''),
        ];

        if ($searchFilter !== '') {
            $haystack = strtolower(
                $item['title'] . ' ' . $item['path'] . ' ' . $item['description'] . ' ' . $item['version']
            );
            if (strpos($haystack, strtolower($searchFilter)) === false) {
                continue;
            }
        }

        $allItems[] = $item;
    }

    $activePreviewUrl = ($previewActive && $previewThemeId > 0)
        ? iswBuildThemePreviewUrl($baseUrl, $siteId, $previewThemeId)
        : '';

    return [
        'data' => $allItems,
        'site' => [
            'id'                 => $siteId,
            'name'               => $siteCtx['name'],
            'domain'             => $siteCtx['domain'],
            'base_url'           => $baseUrl,
            'app_id'             => $siteAppId,
            'app_title'          => $siteCtx['app_title'],
            'app_path'           => $siteAppPath,
            'live_theme_id'      => $liveThemeId,
            'live_theme_title'   => $currentThemeTitle,
            'live_theme_path'    => $siteCtx['live_theme_path'],
            'live_theme_version' => $siteCtx['live_theme_version'],
        ],
        'preview' => [
            'active'      => ($previewActive && $previewThemeId > 0),
            'theme_id'    => $previewThemeId,
            'theme_title' => $previewThemeTitle,
            'theme_path'  => ($previewActive && is_array($previewThemeRow)) ? (string) ($previewThemeRow['theme_path'] ?? '') : '',
            'preview_url' => $activePreviewUrl,
            'exit_url'    => $baseUrl . '/?_preview_theme=exit',
        ],
        'stats' => [
            'total'               => $readyCount + $missingCount,
            'ready'               => $readyCount,
            'missing'             => $missingCount,
            'current_theme_title' => $currentThemeTitle,
            'preview_theme_title' => $previewThemeTitle,
        ],
    ];
}

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

try {
    $siteCtx = tmApiFetchSiteContext($connection, $tblPrefix, $siteId, $docRoot);
    if (!$siteCtx) {
        tmApiRespond(404, [
            'status'  => 'error',
            'code'    => 'site_not_found',
            'message' => 'Active site configuration could not be loaded.',
            'msg'     => 'Active site configuration could not be loaded.',
        ]);
    }

    $searchFilter = trim((string) ($reqData['q'] ?? ($reqData['search'] ?? '')));

    if ($method === 'GET') {
        $payload = tmApiBuildPayload($connection, $tblPrefix, $siteCtx, $searchFilter);
        tmApiRespond(200, array_merge([
            'status'  => 'success',
            'message' => 'Themes loaded successfully.',
            'msg'     => 'Themes loaded successfully.',
        ], $payload));
    }

    if (!in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
        tmApiRespond(405, [
            'status'  => 'error',
            'code'    => 'method_not_allowed',
            'message' => "HTTP method {$method} is not supported.",
            'msg'     => "HTTP method {$method} is not supported.",
        ]);
    }

    $action = strtolower(trim((string) ($reqData['action'] ?? '')));
    if ($action === '' && $method === 'PUT') {
        $action = 'apply';
    }

    // ------------------------------------------------------------------
    // Exit active preview mode
    // ------------------------------------------------------------------
    if (in_array($action, ['exit_preview', 'stop_preview', 'clear_preview'], true)) {
        iswClearThemePreviewCookie();
        $payload = tmApiBuildPayload($connection, $tblPrefix, $siteCtx, $searchFilter, [
            'active' => false,
            'theme'  => null,
        ]);
        tmApiRespond(200, array_merge([
            'status'  => 'success',
            'message' => 'Website preview mode ended. Your browser now displays the live theme.',
            'msg'     => 'Website preview mode ended. Your browser now displays the live theme.',
        ], $payload));
    }

    // ------------------------------------------------------------------
    // Validate target theme for preview or live activation
    // ------------------------------------------------------------------
    $targetThemeId = tmApiParseThemeId($reqData);
    if ($targetThemeId <= 0) {
        tmApiRespond(400, [
            'status'  => 'error',
            'code'    => 'invalid_theme_id',
            'message' => 'A valid theme_id is required.',
            'msg'     => 'A valid theme_id is required.',
        ]);
    }

    $targetSql = "SELECT
                    t.theme_id,
                    t.theme_app_id,
                    t.theme_package_id,
                    t.theme_title,
                    t.theme_description,
                    t.theme_image,
                    t.theme_path,
                    t.theme_version,
                    t.theme_status
                  FROM `{$tblPrefix}theme` t
                  WHERE t.theme_id = {$targetThemeId}
                  LIMIT 1";
    $targetRes = mysqli_query($connection, $targetSql);
    if (!$targetRes || mysqli_num_rows($targetRes) === 0) {
        tmApiRespond(404, [
            'status'  => 'error',
            'code'    => 'theme_not_found',
            'message' => 'The selected theme does not exist.',
            'msg'     => 'The selected theme does not exist.',
        ]);
    }
    $targetTheme = mysqli_fetch_assoc($targetRes);

    $targetAppId = (int) ($targetTheme['theme_app_id'] ?? 0);
    $liveThemeId = (int) $siteCtx['live_theme_id'];
    if ($targetAppId !== (int) $siteCtx['app_id'] && $targetThemeId !== $liveThemeId) {
        tmApiRespond(403, [
            'status'  => 'error',
            'code'    => 'theme_app_mismatch',
            'message' => 'Selected theme does not belong to this website\'s current application.',
            'msg'     => 'Selected theme does not belong to this website\'s current application.',
        ]);
    }

    if ((int) ($targetTheme['theme_status'] ?? 0) !== 1 && $targetThemeId !== $liveThemeId) {
        tmApiRespond(400, [
            'status'  => 'error',
            'code'    => 'theme_inactive',
            'message' => 'Selected theme is currently inactive.',
            'msg'     => 'Selected theme is currently inactive.',
        ]);
    }

    $avail = iswCheckThemeTemplateAvailability((string) $siteCtx['app_path'], (string) ($targetTheme['theme_path'] ?? ''));
    if (empty($avail['template_exists'])) {
        tmApiRespond(422, [
            'status'  => 'error',
            'code'    => 'theme_files_missing',
            'message' => 'Template files for "' . $targetTheme['theme_title'] . '" were not found on disk and cannot be previewed or activated.',
            'msg'     => 'Template files for "' . $targetTheme['theme_title'] . '" were not found on disk and cannot be previewed or activated.',
        ]);
    }

    // ------------------------------------------------------------------
    // Start isolated preview without modifying isw_site.site_theme_id
    // ------------------------------------------------------------------
    if (in_array($action, ['preview', 'start_preview'], true)) {
        iswSetThemePreviewCookie($siteId, $targetThemeId);
        $previewUrl = iswBuildThemePreviewUrl((string) $siteCtx['base_url'], $siteId, $targetThemeId);
        $payload = tmApiBuildPayload($connection, $tblPrefix, $siteCtx, $searchFilter, [
            'active' => true,
            'theme'  => $targetTheme,
        ]);
        tmApiRespond(200, array_merge([
            'status'      => 'success',
            'message'     => 'Preview mode enabled for "' . $targetTheme['theme_title'] . '". Live visitors are unaffected.',
            'msg'         => 'Preview mode enabled for "' . $targetTheme['theme_title'] . '". Live visitors are unaffected.',
            'preview_url' => $previewUrl,
        ], $payload));
    }

    // ------------------------------------------------------------------
    // Apply theme live to isw_site.site_theme_id
    // ------------------------------------------------------------------
    if (in_array($action, ['apply', 'activate', 'switch'], true)) {
        if ($targetThemeId === $liveThemeId) {
            iswClearThemePreviewCookie();
            $payload = tmApiBuildPayload($connection, $tblPrefix, $siteCtx, $searchFilter, [
                'active' => false,
                'theme'  => null,
            ]);
            tmApiRespond(200, array_merge([
                'status'  => 'success',
                'message' => '"' . $targetTheme['theme_title'] . '" is already the live theme for this website.',
                'msg'     => '"' . $targetTheme['theme_title'] . '" is already the live theme for this website.',
            ], $payload));
        }

        $updSql = "UPDATE `{$tblPrefix}site`
                   SET site_theme_id = {$targetThemeId}
                   WHERE site_id = {$siteId}
                   LIMIT 1";
        if (!mysqli_query($connection, $updSql)) {
            throw new Exception('Failed to update the live website theme.');
        }

        tmApiPurgeSiteHtmlCache($docRoot, $siteCtx);
        iswClearThemePreviewCookie();

        $updatedSiteCtx = tmApiFetchSiteContext($connection, $tblPrefix, $siteId, $docRoot) ?? $siteCtx;
        $payload = tmApiBuildPayload($connection, $tblPrefix, $updatedSiteCtx, $searchFilter, [
            'active' => false,
            'theme'  => null,
        ]);

        tmApiRespond(200, array_merge([
            'status'  => 'success',
            'message' => 'Theme switched to "' . $targetTheme['theme_title'] . '". Live website visitors will now see this theme.',
            'msg'     => 'Theme switched to "' . $targetTheme['theme_title'] . '". Live website visitors will now see this theme.',
        ], $payload));
    }

    tmApiRespond(400, [
        'status'  => 'error',
        'code'    => 'invalid_action',
        'message' => 'Unsupported action. Use apply, preview, or exit_preview.',
        'msg'     => 'Unsupported action. Use apply, preview, or exit_preview.',
    ]);
} catch (Throwable $e) {
    error_log('ISW Appearance Themes API Error: ' . $e->getMessage());
    tmApiRespond(500, [
        'status'  => 'error',
        'code'    => 'server_error',
        'message' => 'An unexpected error occurred while processing the theme request.',
        'msg'     => 'An unexpected error occurred while processing the theme request.',
    ]);
}

