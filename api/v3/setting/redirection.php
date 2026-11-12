<?php

/**
 * V3 URL Redirections Management API Endpoint
 * Path: /api/v3/setting/redirection.php
 */

header('Content-Type: application/json; charset=utf-8');

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
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

// Enforce session / cookie authentication guard
require_once LOGIN_CHECK;

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId     = defined('SITE_ID') ? (int) SITE_ID : 0;
$userId     = defined('USER_ID') ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? strtolower(trim((string) $site_access_type)) : 'visitor';

// RBAC permission check
$isPrivilegedUser = in_array($accessType, ['administrator', 'admin', 'superadmin', 'operations', 'owner', 'author'], true);

if ($siteId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'invalid_site',
        'message' => 'Active site identifier could not be resolved.',
        'msg'     => 'Active site identifier could not be resolved.'
    ]);
    exit;
}

if (!$isPrivilegedUser) {
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'code'    => 'access_denied',
        'message' => 'Access denied for current user role.',
        'msg'     => 'Access denied for current user role.'
    ]);
    exit;
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

// Decode agnostic entity ID if provided (numeric or base64)
$entityId = 0;
$rawId = $reqData['id'] ?? null;
$hasRawId = ($rawId !== null && trim((string) $rawId) !== '');
if ($hasRawId) {
    if (is_numeric($rawId) && (int) $rawId > 0) {
        $entityId = (int) $rawId;
    } elseif (is_string($rawId)) {
        $trimmedId = trim($rawId);
        $decodedBytes = base64_decode($trimmedId, true);
        if ($decodedBytes !== false && base64_encode($decodedBytes) === $trimmedId && is_numeric($decodedBytes) && (int) $decodedBytes > 0) {
            $entityId = (int) $decodedBytes;
        }
    }
    if ($entityId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'code'    => 'invalid_id',
            'message' => 'A valid positive redirection ID is required.',
            'msg'     => 'A valid positive redirection ID is required.'
        ]);
        exit;
    }
}

$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

/**
 * Respond with standard JSON envelope and exit.
 */
function rmApiRespond(int $httpCode, array $payload): void
{
    http_response_code($httpCode);
    echo json_encode($payload);
    exit;
}

/**
 * Allowed HTTP redirection status codes & metadata.
 */
function rmApiStatusCatalog(): array
{
    return [
        '301' => ['label' => '301 (Moved Permanently)', 'short' => 'Moved Permanently', 'category' => 'permanent'],
        '302' => ['label' => '302 (Found / Temporary)', 'short' => 'Found (Temporary)', 'category' => 'temporary'],
        '307' => ['label' => '307 (Temporary Redirect)', 'short' => 'Temporary Redirect', 'category' => 'temporary'],
        '308' => ['label' => '308 (Permanent Redirect)', 'short' => 'Permanent Redirect', 'category' => 'permanent'],
        '300' => ['label' => '300 (Multiple Choices)', 'short' => 'Multiple Choices', 'category' => 'other'],
        '303' => ['label' => '303 (See Other)', 'short' => 'See Other', 'category' => 'other'],
        '304' => ['label' => '304 (Not Modified)', 'short' => 'Not Modified', 'category' => 'other'],
    ];
}

/**
 * Gather canonical hosts associated with the active site for host stripping & loop detection.
 */
function rmApiGetSiteMeta(mysqli $connection, string $tblPrefix, int $siteId): array
{
    $hosts = [];
    $protocol = defined('HTTP_PROTOCOL') ? HTTP_PROTOCOL : 'http://';
    $domain = defined('SITE_DOMAIN') ? strtolower(trim((string) SITE_DOMAIN)) : '';

    $siteRes = mysqli_query(
        $connection,
        "SELECT site_domain, site_url, site_name FROM `{$tblPrefix}site` WHERE site_id = {$siteId} LIMIT 1"
    );
    $siteName = defined('SITE_NAME') ? (string) SITE_NAME : '';
    $siteUrl = defined('SITE_URL') ? (string) SITE_URL : ($domain !== '' ? $protocol . $domain : '');

    if ($siteRes && ($siteRow = mysqli_fetch_assoc($siteRes))) {
        if (!empty($siteRow['site_domain'])) {
            $domain = strtolower(trim((string) $siteRow['site_domain']));
        }
        if (!empty($siteRow['site_url'])) {
            $parsed = parse_url((string) $siteRow['site_url']);
            if (!empty($parsed['host'])) {
                $hosts[] = strtolower($parsed['host']);
            }
            $siteUrl = rtrim((string) $siteRow['site_url'], '/');
        }
        if (!empty($siteRow['site_name'])) {
            $siteName = (string) $siteRow['site_name'];
        }
    }

    if ($domain !== '') {
        $hosts[] = preg_replace('/:\d+$/', '', $domain);
        $hosts[] = $domain;
    }

    if (defined('SITE_DOMAIN')) {
        $sd = strtolower(trim((string) SITE_DOMAIN));
        $hosts[] = $sd;
        $hosts[] = preg_replace('/:\d+$/', '', $sd);
    }

    $normalizedHosts = [];
    foreach ($hosts as $h) {
        $clean = strtolower(trim((string) $h));
        if ($clean === '') {
            continue;
        }
        $normalizedHosts[$clean] = true;
        if (strpos($clean, 'www.') === 0) {
            $normalizedHosts[substr($clean, 4)] = true;
        } else {
            $normalizedHosts['www.' . $clean] = true;
        }
    }

    $baseUrl = ($domain !== '') ? ($protocol . $domain) : rtrim($siteUrl, '/');

    return [
        'id'       => $siteId,
        'name'     => $siteName,
        'domain'   => $domain,
        'base_url' => rtrim($baseUrl, '/'),
        'hosts'    => array_keys($normalizedHosts),
    ];
}

/**
 * Normalize and validate source URL path so router/index.php + router/routerManager.php match reliably.
 */
function rmApiNormalizeFromUrl(string $raw, array $siteHosts): array
{
    $value = trim($raw);
    if ($value === '' || $value === '#') {
        return ['ok' => false, 'error' => 'Source URL path is required (for example: /old-page/).'];
    }

    if (preg_match('/^\s*(?:javascript|data|vbscript|mailto|tel):/i', $value)) {
        return ['ok' => false, 'error' => 'Source path cannot use a script or protocol scheme.'];
    }

    if (preg_match('/[\x00-\x1F\x7F\s]/', $value)) {
        return ['ok' => false, 'error' => 'Source path cannot contain spaces or control characters (use hyphens like /old-page/).'];
    }

    if (strpos($value, '//') === 0) {
        return ['ok' => false, 'error' => 'Please enter a relative path on this site starting with "/".'];
    }

    if (preg_match('#^https?://#i', $value)) {
        $parts = parse_url($value);
        if (!$parts || empty($parts['host'])) {
            return ['ok' => false, 'error' => 'Source URL format is invalid.'];
        }
        $host = strtolower($parts['host']);
        $hostWithPort = isset($parts['port']) ? ($host . ':' . $parts['port']) : $host;
        if (!in_array($host, $siteHosts, true) && !in_array($hostWithPort, $siteHosts, true)) {
            return ['ok' => false, 'error' => 'Source URL must belong to this website domain (for example: /old-page/).'];
        }
        if (!empty($parts['query']) || !empty($parts['fragment']) || strpos($value, '?') !== false || strpos($value, '#') !== false) {
            return ['ok' => false, 'error' => 'Source path cannot include "?" query parameters or "#" fragments.'];
        }
        $value = $parts['path'] ?? '/';
    } elseif (strpos($value, '?') !== false || strpos($value, '#') !== false) {
        return ['ok' => false, 'error' => 'Source path cannot include "?" query parameters or "#" fragments.'];
    }

    $value = preg_replace('#/+#', '/', '/' . ltrim($value, '/'));
    if ($value === '/' || $value === '') {
        return ['ok' => false, 'error' => 'The website homepage "/" cannot be redirected. Specify a specific path such as /old-page/.'];
    }

    // Match router/index.php trailing-slash rule for paths without a file extension dot
    if (strpos($value, '.') === false && substr($value, -1) !== '/') {
        $value .= '/';
    }

    $lower = strtolower($value);
    $reservedPrefixes = ['/dashboard', '/api', '/login', '/logout', '/register', '/sso', '/oauth', '/viewmedia'];
    foreach ($reservedPrefixes as $prefix) {
        if ($lower === $prefix || strpos($lower, $prefix . '/') === 0) {
            return ['ok' => false, 'error' => "Protected system route \"{$prefix}/\" cannot be overridden by a redirection."];
        }
    }

    if (strlen($value) > 255) {
        return ['ok' => false, 'error' => 'Source path must be 255 characters or fewer.'];
    }

    return ['ok' => true, 'value' => $value];
}

/**
 * Normalize and validate destination path or external URL, preventing self-loops.
 */
function rmApiNormalizeToUrl(string $raw, string $normalizedFrom, array $siteHosts): array
{
    $value = trim($raw);
    if ($value === '') {
        return ['ok' => false, 'error' => 'Destination URL is required.'];
    }

    if (preg_match('/^\s*(?:javascript|data|vbscript):/i', $value)) {
        return ['ok' => false, 'error' => 'Destination URL scheme is not allowed.'];
    }

    if (preg_match('/[\x00-\x1F\x7F\s]/', $value)) {
        return ['ok' => false, 'error' => 'Destination URL cannot contain spaces or control characters.'];
    }

    if (strpos($value, '//') === 0) {
        return ['ok' => false, 'error' => 'Use an explicit https:// URL or a root-relative path starting with "/".'];
    }

    $isAbsolute = preg_match('#^https?://#i', $value) === 1;
    $isRelative = isset($value[0]) && $value[0] === '/';

    if (!$isAbsolute && !$isRelative) {
        return ['ok' => false, 'error' => 'Destination must start with "/" for an internal path or "https://" / "http://" for an external URL.'];
    }

    if ($isAbsolute) {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return ['ok' => false, 'error' => 'Please enter a valid destination URL (including https:// or http://).'];
        }
        $parts = parse_url($value);
        if (!$parts || empty($parts['host'])) {
            return ['ok' => false, 'error' => 'Destination URL must include a valid host name.'];
        }
        $toHost = strtolower($parts['host']);
        $toHostWithPort = isset($parts['port']) ? ($toHost . ':' . $parts['port']) : $toHost;
        $sameSite = in_array($toHost, $siteHosts, true) || in_array($toHostWithPort, $siteHosts, true);
        if ($sameSite && empty($parts['query']) && empty($parts['fragment'])) {
            $toPath = preg_replace('#/+#', '/', '/' . ltrim($parts['path'] ?? '/', '/'));
            if (strpos($toPath, '.') === false && substr($toPath, -1) !== '/') {
                $toPath .= '/';
            }
            if ($toPath === $normalizedFrom) {
                return ['ok' => false, 'error' => 'Destination points back to the same source path and would cause a redirect loop.'];
            }
        }
    } else {
        $toClean = preg_replace('#/+#', '/', $value);
        $toPathOnly = strtok($toClean, '?#');
        $cmpPath = $toPathOnly;
        if ($cmpPath !== '/' && strpos($cmpPath, '.') === false && substr($cmpPath, -1) !== '/') {
            $cmpPath .= '/';
        }
        if (strpos($toClean, '?') === false && strpos($toClean, '#') === false && $cmpPath === $normalizedFrom) {
            return ['ok' => false, 'error' => 'Destination URL cannot be the same as the source path (infinite redirect loop).'];
        }
        $value = $toClean;
    }

    if (strlen($value) > 255) {
        return ['ok' => false, 'error' => 'Destination URL must be 255 characters or fewer.'];
    }

    return ['ok' => true, 'value' => $value];
}

/**
 * Hydrate a database row into a consistent API payload.
 */
function rmApiHydrateRow(array $row, array $siteMeta, array $statusCatalog): array
{
    $id = (int) ($row['redirection_id'] ?? 0);
    $fromRaw = trim((string) ($row['redirection_from'] ?? ''));
    $linkUrl = trim((string) ($row['resolved_link_url'] ?? ''));
    $effectiveFrom = $fromRaw !== '' ? $fromRaw : $linkUrl;
    if ($effectiveFrom !== '' && $effectiveFrom[0] !== '/' && !preg_match('#^https?://#i', $effectiveFrom)) {
        $effectiveFrom = '/' . $effectiveFrom;
    }

    $httpStatus = trim((string) ($row['redirection_http_status'] ?? '301'));
    $meta = $statusCatalog[$httpStatus] ?? [
        'label'    => $httpStatus . ' (Redirect)',
        'short'    => 'HTTP ' . $httpStatus,
        'category' => 'other'
    ];

    $linkId = isset($row['link_id']) && $row['link_id'] !== null ? (int) $row['link_id'] : null;
    $linkStatus = isset($row['link_status']) && $row['link_status'] !== null ? (int) $row['link_status'] : null;
    $rowStatus = (int) ($row['redirection_status'] ?? 1);

    $linkState = 'active';
    if ($linkId !== null && $linkStatus === 301) {
        $linkState = 'superseded';
    } elseif ($linkId === null || $linkStatus !== 200) {
        $linkState = 'unlinked';
    }

    return [
        'id'              => $id,
        'encoded_id'      => base64_encode((string) $id),
        'from_url'        => $effectiveFrom,
        'to_url'          => (string) ($row['redirection_to'] ?? ''),
        'http_status'     => $httpStatus,
        'status_label'    => $meta['label'],
        'status_short'    => $meta['short'],
        'status_category' => $meta['category'],
        'status'          => $rowStatus,
        'link_id'         => $linkId,
        'link_status'     => $linkStatus,
        'link_state'      => $linkState,
        'test_url'        => ($effectiveFrom !== '' && $siteMeta['base_url'] !== '') ? ($siteMeta['base_url'] . $effectiveFrom) : $effectiveFrom,
        'created_at'      => (string) ($row['redirection_generated'] ?? ''),
        'updated_at'      => (string) ($row['redirection_update'] ?? ''),
    ];
}

/**
 * Fetch aggregate KPI counters scoped strictly to the active tenant site.
 */
function rmApiFetchStats(mysqli $connection, string $tblPrefix, int $siteId): array
{
    $sql = "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN redirection_http_status IN ('301', '308') THEN 1 ELSE 0 END) AS permanent,
                SUM(CASE WHEN redirection_http_status IN ('302', '307') THEN 1 ELSE 0 END) AS temporary,
                SUM(CASE WHEN redirection_http_status NOT IN ('301', '308', '302', '307') THEN 1 ELSE 0 END) AS other
            FROM `{$tblPrefix}redirection`
            WHERE redirection_site_id = {$siteId}
              AND redirection_from != '#'";

    $res = mysqli_query($connection, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    return [
        'total'     => (int) ($row['total'] ?? 0),
        'permanent' => (int) ($row['permanent'] ?? 0),
        'temporary' => (int) ($row['temporary'] ?? 0),
        'other'     => (int) ($row['other'] ?? 0),
    ];
}

/**
 * Fetch one hydrated redirection row by ID for the active tenant site.
 */
function rmApiFetchById(mysqli $connection, string $tblPrefix, int $siteId, int $id, array $siteMeta, array $statusCatalog): ?array
{
    $sql = "SELECT
                r.redirection_id,
                r.redirection_from,
                r.redirection_to,
                r.redirection_http_status,
                r.redirection_status,
                r.redirection_generated,
                r.redirection_update,
                (
                    SELECT l.link_id
                    FROM `{$tblPrefix}link` l
                    WHERE l.link_relation_id = r.redirection_id
                      AND l.link_type = 'redirect'
                      AND l.link_site_id = {$siteId}
                    ORDER BY (l.link_status = 200) DESC, l.link_modified DESC, l.link_id DESC
                    LIMIT 1
                ) AS link_id,
                (
                    SELECT l.link_url
                    FROM `{$tblPrefix}link` l
                    WHERE l.link_relation_id = r.redirection_id
                      AND l.link_type = 'redirect'
                      AND l.link_site_id = {$siteId}
                    ORDER BY (l.link_status = 200) DESC, l.link_modified DESC, l.link_id DESC
                    LIMIT 1
                ) AS resolved_link_url,
                (
                    SELECT l.link_status
                    FROM `{$tblPrefix}link` l
                    WHERE l.link_relation_id = r.redirection_id
                      AND l.link_type = 'redirect'
                      AND l.link_site_id = {$siteId}
                    ORDER BY (l.link_status = 200) DESC, l.link_modified DESC, l.link_id DESC
                    LIMIT 1
                ) AS link_status
            FROM `{$tblPrefix}redirection` r
            WHERE r.redirection_id = {$id}
              AND r.redirection_site_id = {$siteId}
              AND r.redirection_from != '#'
            LIMIT 1";

    $res = mysqli_query($connection, $sql);
    if (!$res || mysqli_num_rows($res) === 0) {
        return null;
    }
    return rmApiHydrateRow(mysqli_fetch_assoc($res), $siteMeta, $statusCatalog);
}

$statusCatalog = rmApiStatusCatalog();
$siteMeta = rmApiGetSiteMeta($connection, $tblPrefix, $siteId);

try {
    // ----------------------------------------------------------------------
    // GET: List / Filter / Retrieve Single Redirection
    // ----------------------------------------------------------------------
    if ($method === 'GET') {
        if ($entityId > 0) {
            $item = rmApiFetchById($connection, $tblPrefix, $siteId, $entityId, $siteMeta, $statusCatalog);
            if (!$item) {
                rmApiRespond(404, [
                    'status'  => 'error',
                    'code'    => 'not_found',
                    'message' => 'Redirection rule not found.',
                    'msg'     => 'Redirection rule not found.'
                ]);
            }
            rmApiRespond(200, [
                'status'  => 'success',
                'message' => 'Redirection rule retrieved successfully.',
                'msg'     => 'Redirection rule retrieved successfully.',
                'data'    => $item,
                'stats'   => rmApiFetchStats($connection, $tblPrefix, $siteId),
                'site'    => ['id' => $siteMeta['id'], 'name' => $siteMeta['name'], 'domain' => $siteMeta['domain'], 'base_url' => $siteMeta['base_url']]
            ]);
        }

        $filterStatus = trim((string) ($reqData['http_status'] ?? ($reqData['status'] ?? 'all')));
        $filterSearch = trim((string) ($reqData['q'] ?? ($reqData['search'] ?? '')));

        $where = [
            "r.redirection_site_id = {$siteId}",
            "r.redirection_from != '#'"
        ];

        if ($filterStatus !== '' && strtolower($filterStatus) !== 'all' && $filterStatus !== '#') {
            if (!isset($statusCatalog[$filterStatus])) {
                rmApiRespond(400, [
                    'status'  => 'error',
                    'code'    => 'invalid_status_filter',
                    'message' => 'Invalid HTTP status filter value.',
                    'msg'     => 'Invalid HTTP status filter value.'
                ]);
            }
            $escFilterStatus = mysqli_real_escape_string($connection, $filterStatus);
            $where[] = "r.redirection_http_status = '{$escFilterStatus}'";
        }

        if ($filterSearch !== '') {
            $likeSafe = str_replace(['|', '%', '_'], ['||', '|%', '|_'], strtolower($filterSearch));
            $escSearch = mysqli_real_escape_string($connection, $likeSafe);
            $where[] = "(
                LOWER(r.redirection_from) LIKE '%{$escSearch}%' ESCAPE '|'
                OR LOWER(r.redirection_to) LIKE '%{$escSearch}%' ESCAPE '|'
                OR LOWER(r.redirection_http_status) LIKE '%{$escSearch}%' ESCAPE '|'
                OR EXISTS (
                    SELECT 1
                    FROM `{$tblPrefix}link` ls
                    WHERE ls.link_relation_id = r.redirection_id
                      AND ls.link_type = 'redirect'
                      AND ls.link_site_id = {$siteId}
                      AND LOWER(ls.link_url) LIKE '%{$escSearch}%' ESCAPE '|'
                )
            )";
        }

        $whereSql = implode(' AND ', $where);
        $listSql = "SELECT
                        r.redirection_id,
                        r.redirection_from,
                        r.redirection_to,
                        r.redirection_http_status,
                        r.redirection_status,
                        r.redirection_generated,
                        r.redirection_update,
                        (
                            SELECT l.link_id
                            FROM `{$tblPrefix}link` l
                            WHERE l.link_relation_id = r.redirection_id
                              AND l.link_type = 'redirect'
                              AND l.link_site_id = {$siteId}
                            ORDER BY (l.link_status = 200) DESC, l.link_modified DESC, l.link_id DESC
                            LIMIT 1
                        ) AS link_id,
                        (
                            SELECT l.link_url
                            FROM `{$tblPrefix}link` l
                            WHERE l.link_relation_id = r.redirection_id
                              AND l.link_type = 'redirect'
                              AND l.link_site_id = {$siteId}
                            ORDER BY (l.link_status = 200) DESC, l.link_modified DESC, l.link_id DESC
                            LIMIT 1
                        ) AS resolved_link_url,
                        (
                            SELECT l.link_status
                            FROM `{$tblPrefix}link` l
                            WHERE l.link_relation_id = r.redirection_id
                              AND l.link_type = 'redirect'
                              AND l.link_site_id = {$siteId}
                            ORDER BY (l.link_status = 200) DESC, l.link_modified DESC, l.link_id DESC
                            LIMIT 1
                        ) AS link_status
                    FROM `{$tblPrefix}redirection` r
                    WHERE {$whereSql}
                    ORDER BY r.redirection_update DESC, r.redirection_id DESC";

        $res = mysqli_query($connection, $listSql);
        if (!$res) {
            throw new Exception('Failed to query redirection rules.');
        }

        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = rmApiHydrateRow($row, $siteMeta, $statusCatalog);
        }

        rmApiRespond(200, [
            'status'  => 'success',
            'message' => 'Redirections retrieved successfully.',
            'msg'     => 'Redirections retrieved successfully.',
            'data'    => $items,
            'stats'   => rmApiFetchStats($connection, $tblPrefix, $siteId),
            'site'    => ['id' => $siteMeta['id'], 'name' => $siteMeta['name'], 'domain' => $siteMeta['domain'], 'base_url' => $siteMeta['base_url']]
        ]);
    }

    // ----------------------------------------------------------------------
    // Determine Mutation Intent (CREATE / UPDATE / DELETE)
    // ----------------------------------------------------------------------
    $action = strtolower(trim((string) ($reqData['action'] ?? '')));

    $isDelete = ($method === 'DELETE') || ($method === 'POST' && $action === 'delete');
    $isUpdate = ($method === 'PUT' || $method === 'PATCH')
        || ($method === 'POST' && in_array($action, ['update', 'edit'], true))
        || ($method === 'POST' && $action === '' && $entityId > 0);
    $isCreate = ($method === 'POST' && !$isDelete && !$isUpdate && ($action === '' || $action === 'create' || $action === 'publish'));

    if (!$isDelete && !$isUpdate && !$isCreate) {
        rmApiRespond(405, [
            'status'  => 'error',
            'code'    => 'method_not_allowed',
            'message' => "Method {$method} or action not allowed.",
            'msg'     => "Method {$method} or action not allowed."
        ]);
    }

    // ----------------------------------------------------------------------
    // DELETE: Remove Redirection & Linked Redirect Route Atomically
    // ----------------------------------------------------------------------
    if ($isDelete) {
        if ($entityId <= 0) {
            rmApiRespond(400, [
                'status'  => 'error',
                'code'    => 'invalid_id',
                'message' => 'A valid redirection ID is required for deletion.',
                'msg'     => 'A valid redirection ID is required for deletion.'
            ]);
        }

        mysqli_query($connection, "START TRANSACTION");

        $existingRes = mysqli_query(
            $connection,
            "SELECT redirection_id FROM `{$tblPrefix}redirection`
             WHERE redirection_id = {$entityId}
               AND redirection_site_id = {$siteId}
               AND redirection_from != '#'
             LIMIT 1 FOR UPDATE"
        );

        if (!$existingRes || mysqli_num_rows($existingRes) === 0) {
            mysqli_query($connection, "ROLLBACK");
            rmApiRespond(404, [
                'status'  => 'error',
                'code'    => 'not_found',
                'message' => 'Redirection rule not found for this website.',
                'msg'     => 'Redirection rule not found for this website.'
            ]);
        }

        $delLinks = mysqli_query(
            $connection,
            "DELETE FROM `{$tblPrefix}link`
             WHERE link_relation_id = {$entityId}
               AND link_type = 'redirect'
               AND link_site_id = {$siteId}"
        );
        $delRedir = mysqli_query(
            $connection,
            "DELETE FROM `{$tblPrefix}redirection`
             WHERE redirection_id = {$entityId}
               AND redirection_site_id = {$siteId}"
        );

        if (!$delLinks || !$delRedir) {
            mysqli_query($connection, "ROLLBACK");
            throw new Exception('Failed to delete redirection rule.');
        }

        mysqli_query($connection, "COMMIT");
        rmApiRespond(200, [
            'status'  => 'success',
            'message' => 'Redirection rule deleted successfully.',
            'msg'     => 'Redirection rule deleted successfully.',
            'data'    => ['deleted_id' => $entityId],
            'stats'   => rmApiFetchStats($connection, $tblPrefix, $siteId)
        ]);
    }

    // ----------------------------------------------------------------------
    // CREATE or UPDATE: Validate Fields
    // ----------------------------------------------------------------------
    $rawFrom   = (string) ($reqData['from_url'] ?? ($reqData['fromURL'] ?? ($reqData['redirection_from'] ?? '')));
    $rawTo     = (string) ($reqData['to_url'] ?? ($reqData['toURL'] ?? ($reqData['redirection_to'] ?? '')));
    $rawStatus = trim((string) ($reqData['http_status'] ?? ($reqData['httpCode'] ?? ($reqData['redirection_http_status'] ?? '301'))));

    if (!isset($statusCatalog[$rawStatus])) {
        rmApiRespond(400, [
            'status'  => 'error',
            'code'    => 'invalid_http_status',
            'field'   => 'http_status',
            'message' => 'Select a valid HTTP redirect status code (301, 302, 307, 308, 300, 303, or 304).',
            'msg'     => 'Select a valid HTTP redirect status code (301, 302, 307, 308, 300, 303, or 304).'
        ]);
    }

    $fromCheck = rmApiNormalizeFromUrl($rawFrom, $siteMeta['hosts']);
    if (!$fromCheck['ok']) {
        rmApiRespond(400, [
            'status'  => 'error',
            'code'    => 'invalid_from_url',
            'field'   => 'from_url',
            'message' => $fromCheck['error'],
            'msg'     => $fromCheck['error']
        ]);
    }
    $fromUrl = $fromCheck['value'];

    $toCheck = rmApiNormalizeToUrl($rawTo, $fromUrl, $siteMeta['hosts']);
    if (!$toCheck['ok']) {
        rmApiRespond(400, [
            'status'  => 'error',
            'code'    => 'invalid_to_url',
            'field'   => 'to_url',
            'message' => $toCheck['error'],
            'msg'     => $toCheck['error']
        ]);
    }
    $toUrl = $toCheck['value'];

    $escFrom   = mysqli_real_escape_string($connection, $fromUrl);
    $escTo     = mysqli_real_escape_string($connection, $toUrl);
    $escStatus = mysqli_real_escape_string($connection, $rawStatus);

    if ($isUpdate) {
        if ($entityId <= 0) {
            rmApiRespond(400, [
                'status'  => 'error',
                'code'    => 'invalid_id',
                'message' => 'A valid redirection ID is required to save changes.',
                'msg'     => 'A valid redirection ID is required to save changes.'
            ]);
        }

        mysqli_query($connection, "START TRANSACTION");

        $existingRes = mysqli_query(
            $connection,
            "SELECT redirection_id FROM `{$tblPrefix}redirection`
             WHERE redirection_id = {$entityId}
               AND redirection_site_id = {$siteId}
               AND redirection_from != '#'
             LIMIT 1 FOR UPDATE"
        );
        if (!$existingRes || mysqli_num_rows($existingRes) === 0) {
            mysqli_query($connection, "ROLLBACK");
            rmApiRespond(404, [
                'status'  => 'error',
                'code'    => 'not_found',
                'message' => 'Redirection rule not found for this website.',
                'msg'     => 'Redirection rule not found for this website.'
            ]);
        }

        // Reject duplicate active redirect rules for the same source path on this site
        $dupSql = "SELECT r.redirection_id
                   FROM `{$tblPrefix}redirection` r
                   WHERE r.redirection_site_id = {$siteId}
                     AND r.redirection_id != {$entityId}
                     AND r.redirection_from != '#'
                     AND (
                         r.redirection_from = '{$escFrom}'
                         OR EXISTS (
                             SELECT 1
                             FROM `{$tblPrefix}link` lx
                             WHERE lx.link_relation_id = r.redirection_id
                               AND lx.link_type = 'redirect'
                               AND lx.link_site_id = {$siteId}
                               AND lx.link_url = '{$escFrom}'
                               AND lx.link_status = 200
                         )
                     )
                   LIMIT 1";
        $dupRes = mysqli_query($connection, $dupSql);
        if ($dupRes && mysqli_num_rows($dupRes) > 0) {
            mysqli_query($connection, "ROLLBACK");
            rmApiRespond(409, [
                'status'  => 'error',
                'code'    => 'duplicate_from_url',
                'field'   => 'from_url',
                'message' => "A redirection rule for \"{$fromUrl}\" already exists. Edit the existing rule instead.",
                'msg'     => "A redirection rule for \"{$fromUrl}\" already exists. Edit the existing rule instead."
            ]);
        }

        // Supersede any existing non-redirect links pointing to the new source URL so router resolves this rule
        $supersedeOther = mysqli_query(
            $connection,
            "UPDATE `{$tblPrefix}link`
             SET link_status = 301
             WHERE link_url = '{$escFrom}'
               AND link_site_id = {$siteId}
               AND NOT (link_relation_id = {$entityId} AND link_type = 'redirect')"
        );

        // Preserve redirection_generated despite MySQL ON UPDATE CURRENT_TIMESTAMP
        $updRedir = mysqli_query(
            $connection,
            "UPDATE `{$tblPrefix}redirection`
             SET redirection_from = '{$escFrom}',
                 redirection_to = '{$escTo}',
                 redirection_http_status = '{$escStatus}',
                 redirection_status = 1,
                 redirection_generated = redirection_generated,
                 redirection_update = NOW()
             WHERE redirection_id = {$entityId}
               AND redirection_site_id = {$siteId}"
        );

        $checkLink = mysqli_query(
            $connection,
            "SELECT link_id
             FROM `{$tblPrefix}link`
             WHERE link_relation_id = {$entityId}
               AND link_type = 'redirect'
               AND link_site_id = {$siteId}
             ORDER BY link_id DESC
             LIMIT 1"
        );

        $syncLink = false;
        if ($checkLink && ($linkRow = mysqli_fetch_assoc($checkLink))) {
            $targetLinkId = (int) $linkRow['link_id'];
            $syncLink = mysqli_query(
                $connection,
                "UPDATE `{$tblPrefix}link`
                 SET link_url = '{$escFrom}',
                     link_status = 200,
                     link_modified = NOW()
                 WHERE link_id = {$targetLinkId}
                   AND link_site_id = {$siteId}"
            );
        } else {
            $syncLink = mysqli_query(
                $connection,
                "INSERT INTO `{$tblPrefix}link`
                 (link_url, link_relation_id, link_status, link_type, link_is_featured, link_index, link_owner_id, link_site_id, link_creation, link_published, link_modified)
                 VALUES
                 ('{$escFrom}', {$entityId}, 200, 'redirect', 0, '', {$userId}, {$siteId}, NOW(), NOW(), NOW())"
            );
        }

        if (!$supersedeOther || !$updRedir || !$syncLink) {
            mysqli_query($connection, "ROLLBACK");
            throw new Exception('Failed to update redirection rule.');
        }

        mysqli_query($connection, "COMMIT");
        $updatedItem = rmApiFetchById($connection, $tblPrefix, $siteId, $entityId, $siteMeta, $statusCatalog);

        rmApiRespond(200, [
            'status'  => 'success',
            'message' => 'Redirection rule updated successfully.',
            'msg'     => 'Redirection rule updated successfully.',
            'data'    => $updatedItem,
            'stats'   => rmApiFetchStats($connection, $tblPrefix, $siteId)
        ]);
    }

    // ----------------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------------
    mysqli_query($connection, "START TRANSACTION");

    $dupSql = "SELECT r.redirection_id
               FROM `{$tblPrefix}redirection` r
               WHERE r.redirection_site_id = {$siteId}
                 AND r.redirection_from != '#'
                 AND (
                     r.redirection_from = '{$escFrom}'
                     OR EXISTS (
                         SELECT 1
                         FROM `{$tblPrefix}link` lx
                         WHERE lx.link_relation_id = r.redirection_id
                           AND lx.link_type = 'redirect'
                           AND lx.link_site_id = {$siteId}
                           AND lx.link_url = '{$escFrom}'
                           AND lx.link_status = 200
                     )
                 )
               LIMIT 1";
    $dupRes = mysqli_query($connection, $dupSql);
    if ($dupRes && mysqli_num_rows($dupRes) > 0) {
        mysqli_query($connection, "ROLLBACK");
        rmApiRespond(409, [
            'status'  => 'error',
            'code'    => 'duplicate_from_url',
            'field'   => 'from_url',
            'message' => "A redirection rule for \"{$fromUrl}\" already exists. Edit the existing rule instead.",
            'msg'     => "A redirection rule for \"{$fromUrl}\" already exists. Edit the existing rule instead."
        ]);
    }

    // Supersede any existing site link matching fromUrl so router/routerManager routes to this redirect
    $supersedeLink = mysqli_query(
        $connection,
        "UPDATE `{$tblPrefix}link`
         SET link_status = 301
         WHERE link_url = '{$escFrom}'
           AND link_site_id = {$siteId}"
    );

    $insertRedir = mysqli_query(
        $connection,
        "INSERT INTO `{$tblPrefix}redirection`
         (redirection_parent_id, redirection_from, redirection_to, redirection_http_status, redirection_owner_id, redirection_site_id, redirection_status, redirection_generated, redirection_update)
         VALUES
         (0, '{$escFrom}', '{$escTo}', '{$escStatus}', {$userId}, {$siteId}, 1, NOW(), NOW())"
    );
    $newId = (int) mysqli_insert_id($connection);

    $insertLink = false;
    if ($newId > 0) {
        $insertLink = mysqli_query(
            $connection,
            "INSERT INTO `{$tblPrefix}link`
             (link_url, link_relation_id, link_status, link_type, link_is_featured, link_index, link_owner_id, link_site_id, link_creation, link_published, link_modified)
             VALUES
             ('{$escFrom}', {$newId}, 200, 'redirect', 0, '', {$userId}, {$siteId}, NOW(), NOW(), NOW())"
        );
    }

    if (!$supersedeLink || !$insertRedir || $newId <= 0 || !$insertLink) {
        mysqli_query($connection, "ROLLBACK");
        throw new Exception('Failed to create redirection rule.');
    }

    mysqli_query($connection, "COMMIT");
    $createdItem = rmApiFetchById($connection, $tblPrefix, $siteId, $newId, $siteMeta, $statusCatalog);

    rmApiRespond(200, [
        'status'  => 'success',
        'message' => 'Redirection rule created successfully.',
        'msg'     => 'Redirection rule created successfully.',
        'data'    => $createdItem,
        'stats'   => rmApiFetchStats($connection, $tblPrefix, $siteId)
    ]);
} catch (Exception $e) {
    rmApiRespond(500, [
        'status'  => 'error',
        'code'    => 'internal_error',
        'message' => 'An internal server error occurred while processing the redirection request.',
        'msg'     => 'An internal server error occurred while processing the redirection request.'
    ]);
}
