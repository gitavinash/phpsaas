<?php

/**
 * Shared Repository & Helpers for CRM Order Listing and Multi-Format Export.
 * Not directly executable via HTTP.
 */

if (!defined('CRM_ORDER_API_ENTRYPOINT') || realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8', true);
    echo json_encode([
        'status'      => 'error',
        'status_code' => 403,
        'message'     => 'Direct access forbidden.'
    ]);
    exit;
}

/**
 * Bootstraps database, configuration, and authentication for /api/crm/order/* endpoints.
 *
 * @param string $errorFormat 'json' | 'html'
 * @return array{connection:mysqli, tblPrefix:string, siteId:int, userId:int, accessType:string, redactPii:bool}
 */
function crmOrderApiBootstrap(string $errorFormat = 'json'): array
{
    global $connection, $site_access_type;

    if (!isset($connection) || !($connection instanceof mysqli)) {
        $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? rtrim((string)$_SERVER['DOCUMENT_ROOT'], '/') : realpath(dirname(__DIR__, 3));
        if (!$docRoot) {
            $docRoot = dirname(__DIR__, 3);
        }
        $siteStatusPath = $docRoot . '/lib/modules/siteStatus.php';
        $configIdxPath  = $docRoot . '/lib/configuration/index.php';

        if (file_exists($siteStatusPath)) {
            require_once $siteStatusPath;
        }
        if (file_exists($configIdxPath)) {
            require_once $configIdxPath;
        }
    }

    if (!isset($connection) || !($connection instanceof mysqli)) {
        crmOrderRespondAuthError(500, 'Database connection unavailable.', $errorFormat);
    }

    if (empty($_COOKIE['user_data']) || empty($_COOKIE['device_id']) || empty($_COOKIE['site_app'])) {
        crmOrderRespondAuthError(401, 'Authentication required. Please log in to continue.', $errorFormat);
    }

    $decodedSaltKey = base64_decode((string)$_COOKIE['user_data'], true);
    $deviceIdCookie = trim((string)$_COOKIE['device_id']);
    if ($decodedSaltKey === false || trim($decodedSaltKey) === '' || $deviceIdCookie === '') {
        crmOrderRespondAuthError(401, 'Invalid session credentials. Please log in again.', $errorFormat);
    }

    $tblPrefixPreview = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
    $siteCondSql = (defined('SITE_ID') && (SITE_ID == 234 || SITE_ID == 49))
        ? 'sa.site_access_site_id IN (49, 234)'
        : ('sa.site_access_site_id = ' . (defined('SITE_ID') ? (int)SITE_ID : 0));

    $preAuthSql = "
        SELECT sa.site_access_user_id, sa.site_access_type
        FROM {$tblPrefixPreview}user u
        INNER JOIN {$tblPrefixPreview}site_access sa ON sa.site_access_user_id = u.user_id
        INNER JOIN {$tblPrefixPreview}user_sessions us ON us.us_user_id = u.user_id AND us.us_site_id = sa.site_access_site_id
        WHERE u.user_salt_key = ?
          AND us.us_active_device = ?
          AND {$siteCondSql}
        LIMIT 1
    ";
    $verifiedRole = '';
    if ($stmt = mysqli_prepare($connection, $preAuthSql)) {
        mysqli_stmt_bind_param($stmt, 'ss', $decodedSaltKey, $deviceIdCookie);
        mysqli_stmt_execute($stmt);
        $preRes = mysqli_stmt_get_result($stmt);
        $preRow = $preRes ? mysqli_fetch_assoc($preRes) : null;
        mysqli_stmt_close($stmt);
        if (!$preRow) {
            crmOrderRespondAuthError(401, 'Session invalid or expired. Please log in again.', $errorFormat);
        }
        $verifiedRole = strtolower(trim((string)($preRow['site_access_type'] ?? '')));
    } else {
        crmOrderRespondAuthError(500, 'Unable to verify user session.', $errorFormat);
    }

    if (defined('LOGIN_CHECK') && file_exists(LOGIN_CHECK)) {
        require_once LOGIN_CHECK;
    } else {
        crmOrderRespondAuthError(500, 'Authentication module unavailable.', $errorFormat);
    }

    $accessType = $verifiedRole !== ''
        ? $verifiedRole
        : strtolower(trim((string)(isset($site_access_type) ? $site_access_type : (defined('USER_TYPE') ? USER_TYPE : ''))));
    $allowedRoles = ['administrator', 'admin', 'superadmin', 'operations', 'owner', 'agent', 'digital_marketing'];

    if (!in_array($accessType, $allowedRoles, true)) {
        crmOrderRespondAuthError(403, 'Access denied for current account role.', $errorFormat);
    }

    $siteId = 0;
    if (defined('APP_SITE_ID') && (int)APP_SITE_ID > 0) {
        $siteId = (int)APP_SITE_ID;
    } elseif (defined('SITE_ID') && (int)SITE_ID > 0) {
        $siteId = (int)SITE_ID;
    }

    if ($siteId <= 0) {
        crmOrderRespondAuthError(403, 'Workspace site context could not be resolved.', $errorFormat);
    }

    $tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
    $userId    = defined('USER_ID') ? (int)USER_ID : 0;
    $redactPii = ($accessType === 'digital_marketing');

    return [
        'connection' => $connection,
        'tblPrefix'  => $tblPrefix,
        'siteId'     => $siteId,
        'userId'     => $userId,
        'accessType' => $accessType,
        'redactPii'  => $redactPii,
    ];
}

/**
 * Emits a clean HTTP auth/authorization error response and halts execution.
 */
function crmOrderRespondAuthError(int $code, string $message, string $errorFormat = 'json'): void
{
    http_response_code($code);
    if ($errorFormat === 'html') {
        header('Content-Type: text/html; charset=utf-8', true);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Error ' . $code . '</title></head><body><main><h1>Error ' . $code . '</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p></main></body></html>';
    } else {
        header('Content-Type: application/json; charset=utf-8', true);
        echo json_encode([
            'status'      => 'error',
            'status_code' => $code,
            'message'     => $message,
        ]);
    }
    exit;
}

/**
 * Collects merged request parameters from GET, POST, and JSON body.
 */
function crmOrderReadRequestData(): array
{
    $reqData  = $_POST;
    $rawInput = @file_get_contents('php://input');
    if (is_string($rawInput) && trim($rawInput) !== '') {
        $decoded = json_decode($rawInput, true);
        if (is_array($decoded)) {
            $reqData = array_merge($reqData, $decoded);
        }
    }
    return array_merge($_GET, $reqData);
}

/**
 * Canonical CRM status metadata map (0..6).
 *
 * @return array<string, array{code:string, label:string, class:string}>
 */
function crmOrderStatusCatalog(): array
{
    return [
        '0' => ['code' => '0', 'label' => 'Pending',   'class' => 'status-pending'],
        '1' => ['code' => '1', 'label' => 'Confirm',   'class' => 'status-confirm'],
        '2' => ['code' => '2', 'label' => 'Reject',    'class' => 'status-reject'],
        '3' => ['code' => '3', 'label' => 'Cancel',    'class' => 'status-cancel'],
        '4' => ['code' => '4', 'label' => 'Waiting',   'class' => 'status-waiting'],
        '5' => ['code' => '5', 'label' => 'RTO',       'class' => 'status-rto'],
        '6' => ['code' => '6', 'label' => 'Delivered', 'class' => 'status-delivered'],
    ];
}

function crmOrderStatusMeta($rawCode): array
{
    $code = trim((string)$rawCode);
    $catalog = crmOrderStatusCatalog();
    if (isset($catalog[$code])) {
        return $catalog[$code];
    }
    return [
        'code'  => $code,
        'label' => $code === '' ? 'Unknown' : ('Status ' . $code),
        'class' => 'status-pending',
    ];
}

/**
 * Validates and normalizes YYYY-MM-DD / DD/MM/YYYY / MM/DD/YYYY dates cleanly.
 */
function crmOrderNormalizeDateString(?string $raw, bool $preferDayFirst = false): string
{
    $val = trim((string)$raw);
    if ($val === '') {
        return '';
    }

    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $val, $m)) {
        if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
            return $m[1] . '-' . $m[2] . '-' . $m[3];
        }
    }

    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $val, $m)) {
        $p1 = (int)$m[1];
        $p2 = (int)$m[2];
        $yr = (int)$m[3];
        if ($p1 > 12 && checkdate($p2, $p1, $yr)) {
            return sprintf('%04d-%02d-%02d', $yr, $p2, $p1);
        }
        if ($p2 > 12 && checkdate($p1, $p2, $yr)) {
            return sprintf('%04d-%02d-%02d', $yr, $p1, $p2);
        }
        if ($preferDayFirst && checkdate($p2, $p1, $yr)) {
            return sprintf('%04d-%02d-%02d', $yr, $p2, $p1);
        }
        if (checkdate($p1, $p2, $yr)) {
            return sprintf('%04d-%02d-%02d', $yr, $p1, $p2);
        }
    }

    if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $val, $m)) {
        if (checkdate((int)$m[2], (int)$m[1], (int)$m[3])) {
            return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
        }
    }

    return '';
}

/**
 * Decodes literal escaped newline/tab sequences and collapses noisy whitespace.
 */
function crmOrderNormalizeText(?string $raw): string
{
    $str = (string)$raw;
    if ($str === '') {
        return '';
    }
    $str = str_replace(
        ['\\r\\n', '\\n', '\\r', '\\t', "\\'", '\\"', "\r\n", "\r", "\n", "\t", "\xC2\xA0"],
        [' ', ' ', ' ', ' ', "'", '"', ' ', ' ', ' ', ' ', ' '],
        $str
    );
    $collapsed = preg_replace('/\s+/u', ' ', $str);
    return trim($collapsed === null ? $str : $collapsed);
}

/**
 * Normalizes delivery slot/time values (clock ranges and internal slugs like "morning_0", "fixed").
 */
function crmOrderFormatSlot(?string $slotRaw): string
{
    $slot = crmOrderNormalizeText($slotRaw);
    if ($slot === '') {
        return '';
    }

    if (preg_match('/^(\d{1,2}:\d{2}(?:\s*[AP]M)?)\s*-\s*(\d{1,2}:\d{2}(?:\s*[AP]M)?)$/i', $slot, $m)) {
        $t1 = strtotime(trim($m[1]));
        $t2 = strtotime(trim($m[2]));
        if ($t1 !== false && $t2 !== false) {
            return date('h:i A', $t1) . ' - ' . date('h:i A', $t2);
        }
    }

    $cleaned = preg_replace('/[_-]\d+$/', '', $slot);
    $cleaned = trim(str_replace(['_', '-'], ' ', (string)$cleaned));
    $cleaned = trim((string)preg_replace('/\s+/', ' ', $cleaned));
    $lower   = strtolower($cleaned);

    $slotLabels = [
        'standard'   => 'Standard',
        'fixed'      => 'Fixed Time',
        'fixed time' => 'Fixed Time',
        'morning'    => 'Morning',
        'afternoon'  => 'Afternoon',
        'evening'    => 'Evening',
        'midnight'   => 'Midnight',
        'express'    => 'Express',
        'same day'   => 'Same Day',
        'sameday'    => 'Same Day',
    ];
    if (isset($slotLabels[$lower])) {
        return $slotLabels[$lower];
    }

    if ($cleaned !== '' && $cleaned === $lower) {
        return ucwords($cleaned);
    }

    return $slot;
}

/**
 * Formats payment mode slug into readable label.
 */
function crmOrderPaymentLabel(?string $modeRaw): string
{
    $mode = trim((string)$modeRaw);
    if ($mode === '') {
        return 'Unspecified';
    }
    $withSpaces = preg_replace('/([a-z])([A-Z])/', '$1 $2', str_replace(['_', '-'], ' ', $mode));
    return ucwords(trim((string)$withSpaces));
}

/**
 * Normalizes transaction_status code/value into a readable label.
 */
function crmOrderTransactionStatusLabel(?string $rawStatus): string
{
    $val = strtolower(trim((string)$rawStatus));
    if ($val === '') {
        return '';
    }
    $map = [
        '1'        => 'Paid',
        '200'      => 'Paid',
        'success'  => 'Paid',
        'paid'     => 'Paid',
        'captured' => 'Paid',
        '0'        => 'Initiated',
        '2'        => 'Pending',
        'pending'  => 'Pending',
        '3'        => 'Failed',
        'failed'   => 'Failed',
    ];
    if (isset($map[$val])) {
        return $map[$val];
    }
    return ucwords(str_replace(['_', '-'], ' ', $val));
}

/**
 * Parses numeric amounts (stripping formatting commas) into float or null.
 */
function crmOrderParseNumber($raw): ?float
{
    $trimmed = trim((string)$raw);
    if ($trimmed === '' || strpos($trimmed, '|') !== false || preg_match('/,\s+/', $trimmed)) {
        return null;
    }
    $str = str_replace(',', '', $trimmed);
    if ($str === '' || !preg_match('/^-?\d+(?:\.\d+)?$/', $str)) {
        return null;
    }
    return (float)$str;
}

/**
 * Formats a float into a clean compact decimal string (drops trailing .00).
 */
function crmOrderFormatDecimal(float $num): string
{
    if (abs($num) < 0.00001) {
        return '0';
    }
    $formatted = number_format($num, 2, '.', '');
    $trimmed   = rtrim(rtrim($formatted, '0'), '.');
    return $trimmed === '' ? '0' : $trimmed;
}

/**
 * Formats a numeric price for human-readable reports (grouped thousands, decimals only if fractional).
 */
function crmOrderFormatMoney(float $num): string
{
    $rounded  = round($num, 2);
    $hasCents = abs($rounded - floor($rounded)) >= 0.005;
    return number_format($rounded, $hasCents ? 2 : 0, '.', ',');
}

/**
 * Resolves the storefront website base URL for building clickable product links.
 */
function crmOrderResolveWebsiteBaseUrl(mysqli $conn, string $tblPrefix, int $siteId): string
{
    if (defined('SITE_URL')) {
        $definedUrl = rtrim(trim((string)SITE_URL), '/');
        if (preg_match('#^https?://#i', $definedUrl)) {
            return $definedUrl;
        }
    }

    $httpHost = trim((string)($_SERVER['HTTP_HOST'] ?? ''));
    if ($httpHost !== '') {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443)
            || (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === false);
        return ($isHttps ? 'https://' : 'http://') . $httpHost;
    }

    $siteTable    = $tblPrefix . 'site';
    $settingTable = $tblPrefix . 'site_setting';
    $sql = "
        SELECT s.site_domain, s.site_url, COALESCE(ss.site_custom_domain, 0) AS site_custom_domain
        FROM {$siteTable} s
        LEFT JOIN {$settingTable} ss ON ss.site_setting_site_id = s.site_id
        WHERE s.site_id = " . (int)$siteId . "
        LIMIT 1
    ";
    if ($res = mysqli_query($conn, $sql)) {
        if ($row = mysqli_fetch_assoc($res)) {
            $domain = trim((string)($row['site_domain'] ?? ''));
            $altUrl = trim((string)($row['site_url'] ?? ''));
            $host   = ((int)$row['site_custom_domain'] === 1 && $domain !== '') ? $domain : ($altUrl !== '' ? $altUrl : $domain);
            if ($host !== '') {
                if (preg_match('#^https?://#i', $host)) {
                    return rtrim($host, '/');
                }
                $isLocal = (stripos($host, '.local') !== false || stripos($host, 'localhost') !== false || preg_match('/^\d+\.\d+\.\d+\.\d+/', $host));
                return ($isLocal ? 'http://' : 'https://') . rtrim($host, '/');
            }
        }
    }

    return '';
}

/**
 * Combines resolved site base URL and relative link path cleanly.
 */
function crmOrderBuildWebsiteUrl(string $siteBaseUrl, ?string $rawPath): string
{
    $path = trim((string)$rawPath);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $cleanPath = '/' . ltrim($path, '/');
    return $siteBaseUrl !== '' ? ($siteBaseUrl . $cleanPath) : $cleanPath;
}

/**
 * Redacts phone numbers / emails / addresses for digital_marketing role.
 */
function crmOrderMaskString(string $val, string $kind = 'text'): string
{
    $val = trim($val);
    if ($val === '') {
        return '';
    }
    if ($kind === 'phone') {
        $len = strlen($val);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }
        return substr($val, 0, 2) . str_repeat('*', max(2, $len - 4)) . substr($val, -2);
    }
    if ($kind === 'email') {
        $parts = explode('@', $val, 2);
        if (count($parts) === 2) {
            $local = $parts[0];
            $prefix = strlen($local) > 2 ? substr($local, 0, 2) : substr($local, 0, 1);
            return $prefix . '***@' . $parts[1];
        }
    }
    return '[Restricted]';
}

/**
 * Parses and normalizes filter parameters cleanly.
 */
function crmOrderParseFilters(array $reqData): array
{
    // Status: 'all' or numeric code(s) 0..6
    $rawStatus = trim((string)($reqData['status'] ?? 'all'));
    $statusCodes = [];
    $statusNormalized = 'all';

    if ($rawStatus !== '' && strtolower($rawStatus) !== 'all') {
        foreach (explode(',', $rawStatus) as $part) {
            $part = trim($part);
            if ($part !== '' && ctype_digit($part) && (int)$part >= 0 && (int)$part <= 6) {
                $statusCodes[] = (string)(int)$part;
            }
        }
        $statusCodes = array_values(array_unique($statusCodes));
        if (!empty($statusCodes)) {
            $statusNormalized = implode(',', $statusCodes);
        }
    }

    // Date basis & range
    $dateBasis = strtolower(trim((string)($reqData['date_basis'] ?? 'created')));
    if (!in_array($dateBasis, ['created', 'delivery'], true)) {
        $dateBasis = 'created';
    }

    $startDate = crmOrderNormalizeDateString((string)($reqData['start_date'] ?? ''), true);
    $endDate   = crmOrderNormalizeDateString((string)($reqData['end_date'] ?? ''), true);

    if ($startDate === '' && $endDate === '' && !empty($reqData['daterange'])) {
        $drParts = explode(' - ', (string)$reqData['daterange']);
        if (count($drParts) === 2) {
            $startDate = crmOrderNormalizeDateString($drParts[0], true);
            $endDate   = crmOrderNormalizeDateString($drParts[1], true);
        }
    }

    if ($startDate !== '' && $endDate === '') {
        $endDate = $startDate;
    } elseif ($endDate !== '' && $startDate === '') {
        $startDate = $endDate;
    }
    if ($startDate !== '' && $endDate !== '' && $startDate > $endDate) {
        $tmp       = $startDate;
        $startDate = $endDate;
        $endDate   = $tmp;
    }

    // Payment mode
    $paymentMode = trim((string)($reqData['payment_mode'] ?? 'all'));
    if ($paymentMode === '') {
        $paymentMode = 'all';
    }

    // City
    $city = trim((string)($reqData['city'] ?? 'all'));
    if ($city === '') {
        $city = 'all';
    }

    // Search query
    $search = trim((string)($reqData['q'] ?? ''));
    if (function_exists('mb_substr')) {
        $search = mb_substr($search, 0, 120, 'UTF-8');
    } else {
        $search = substr($search, 0, 120);
    }

    // Sort order
    $sortBy = strtolower(trim((string)($reqData['sort_by'] ?? 'newest')));
    $allowedSorts = ['newest', 'oldest', 'delivery_desc', 'delivery_asc', 'amount_desc', 'amount_asc'];
    if (!in_array($sortBy, $allowedSorts, true)) {
        $sortBy = 'newest';
    }

    // Pagination
    $page  = max(1, (int)($reqData['page'] ?? 1));
    $limit = (int)($reqData['limit'] ?? 25);
    if ($limit < 10) {
        $limit = 10;
    } elseif ($limit > 100) {
        $limit = 100;
    }

    return [
        'status'       => $statusNormalized,
        'status_codes' => $statusCodes,
        'date_basis'   => $dateBasis,
        'start_date'   => $startDate,
        'end_date'     => $endDate,
        'payment_mode' => $paymentMode,
        'city'         => $city,
        'q'            => $search,
        'sort_by'      => $sortBy,
        'page'         => $page,
        'limit'        => $limit,
    ];
}

/**
 * Parses comma-separated IDs / quantities / prices safely.
 *
 * @return int[]|string[]
 */
function crmOrderSplitCsvValues(?string $raw): array
{
    $str = trim((string)$raw);
    if ($str === '') {
        return [];
    }
    $parts = explode(',', $str);
    $out = [];
    foreach ($parts as $p) {
        $out[] = trim($p);
    }
    return $out;
}

/**
 * Fetches normalized CRM orders dataset + summary + facets for list & export endpoints.
 *
 * @return array{
 *   rows: array<int, array<string, mixed>>,
 *   pagination: array{total:int, page:int, limit:int, total_pages:int},
 *   summary: array<string, mixed>,
 *   facets: array<string, mixed>
 * }
 */
function crmOrderFetchDataset(
    mysqli $conn,
    string $tblPrefix,
    int $siteId,
    array $filters,
    bool $redactPii = false,
    bool $forExport = false,
    bool $includeFacets = false
): array {
    $siteIdSafe  = (int)$siteId;
    $siteBaseUrl = crmOrderResolveWebsiteBaseUrl($conn, $tblPrefix, $siteIdSafe);
    $orderTable  = $tblPrefix . 'order';
    $metaTable   = $tblPrefix . 'order_meta';
    $addrTable   = $tblPrefix . 'address';
    $profTable   = $tblPrefix . 'profile';
    $userTable   = $tblPrefix . 'user';
    $cityTable   = $tblPrefix . 'city';
    $stateTable  = $tblPrefix . 'state';
    $ctryTable   = $tblPrefix . 'country';
    $prodTable   = $tblPrefix . 'product';
    $invTable    = $tblPrefix . 'inventory';
    $linkTable   = $tblPrefix . 'link';
    $txTable     = $tblPrefix . 'transaction';

    // Derived subquery extracting normalized deliveryDate and delivery_photo once per order
    $dmSubquery = "
        SELECT
            order_relation_id,
            MAX(
                CASE
                    WHEN order_meta_key = 'deliveryDate' AND order_meta_value REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
                        THEN DATE(STR_TO_DATE(order_meta_value, '%m/%d/%Y'))
                    WHEN order_meta_key = 'deliveryDate' AND order_meta_value REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}'
                        THEN DATE(LEFT(order_meta_value, 10))
                    WHEN order_meta_key = 'deliveryDate' AND order_meta_value REGEXP '^[0-9]{2}-[0-9]{2}-[0-9]{4}$'
                        THEN DATE(STR_TO_DATE(order_meta_value, '%d-%m-%Y'))
                    ELSE NULL
                END
            ) AS delivery_date_val,
            MAX(
                CASE
                    WHEN order_meta_key = 'delivery_photo' AND TRIM(order_meta_value) != ''
                        THEN order_meta_value
                    ELSE NULL
                END
            ) AS delivery_photo_val
        FROM {$metaTable}
        WHERE order_meta_key IN ('deliveryDate', 'delivery_photo')
        GROUP BY order_relation_id
    ";

    $baseWhere = [
        "o.order_site_id = {$siteIdSafe}",
        "TRIM(COALESCE(o.order_product_id, '')) != ''",
        "TRIM(COALESCE(o.order_product_id, '')) != '0'",
        "LOWER(COALESCE(o.order_product_type, 'product')) IN ('product', '', 'order')",
        "NOT EXISTS (SELECT 1 FROM {$prodTable} psub WHERE FIND_IN_SET(psub.product_id, REPLACE(o.order_product_id, ' ', '')) > 0 AND psub.product_type = 'subscription')",
    ];

    $nonStatusWhere = $baseWhere;

    // Date range filter
    if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
        $startEsc = mysqli_real_escape_string($conn, $filters['start_date']);
        $endEsc   = mysqli_real_escape_string($conn, $filters['end_date']);
        if ($filters['date_basis'] === 'delivery') {
            $cond = "COALESCE(dm.delivery_date_val, DATE(o.order_generate)) BETWEEN '{$startEsc}' AND '{$endEsc}'";
        } else {
            $cond = "DATE(o.order_generate) BETWEEN '{$startEsc}' AND '{$endEsc}'";
        }
        $nonStatusWhere[] = $cond;
    }

    // Payment mode filter
    if ($filters['payment_mode'] !== 'all') {
        if ($filters['payment_mode'] === 'unspecified') {
            $nonStatusWhere[] = "TRIM(COALESCE(o.order_payment_mode, '')) = ''";
        } else {
            $pmEsc = mysqli_real_escape_string($conn, $filters['payment_mode']);
            $nonStatusWhere[] = "LOWER(TRIM(COALESCE(o.order_payment_mode, ''))) = LOWER('{$pmEsc}')";
        }
    }

    // City filter
    if ($filters['city'] !== 'all') {
        $cityEsc = mysqli_real_escape_string($conn, $filters['city']);
        $nonStatusWhere[] = "LOWER(TRIM(COALESCE(c.city_name, ''))) = LOWER('{$cityEsc}')";
    }

    // Free-text keyword filter
    if ($filters['q'] !== '') {
        $qRaw = $filters['q'];
        $qEsc = mysqli_real_escape_string($conn, $qRaw);
        $qOrderCheck = preg_replace('/^OD#?\s*/i', '', $qRaw);
        $qOrderDigits = preg_replace('/[^0-9]/', '', (string)$qOrderCheck);

        $searchOr = [
            "CAST(o.order_id AS CHAR) LIKE '%{$qEsc}%'",
            "COALESCE(p.profile_display_name, '') LIKE '%{$qEsc}%'",
            "COALESCE(p.profile_first_name, '') LIKE '%{$qEsc}%'",
            "COALESCE(p.profile_last_name, '') LIKE '%{$qEsc}%'",
            "COALESCE(p.profile_mobile, '') LIKE '%{$qEsc}%'",
            "COALESCE(u.user_mobile, '') LIKE '%{$qEsc}%'",
            "COALESCE(u.user_email, '') LIKE '%{$qEsc}%'",
            "COALESCE(p.profile_email, '') LIKE '%{$qEsc}%'",
            "COALESCE(a.address_name, '') LIKE '%{$qEsc}%'",
            "COALESCE(a.address_mobile, '') LIKE '%{$qEsc}%'",
            "COALESCE(a.address_alternate_mobile, '') LIKE '%{$qEsc}%'",
            "COALESCE(a.address_area, '') LIKE '%{$qEsc}%'",
            "COALESCE(a.address_pin_code, '') LIKE '%{$qEsc}%'",
            "COALESCE(c.city_name, '') LIKE '%{$qEsc}%'",
            "COALESCE(s.state_name, '') LIKE '%{$qEsc}%'",
            "EXISTS (SELECT 1 FROM {$prodTable} prq WHERE prq.product_site_id = {$siteIdSafe} AND FIND_IN_SET(prq.product_id, REPLACE(o.order_product_id, ' ', '')) > 0 AND prq.product_title LIKE '%{$qEsc}%')",
            "EXISTS (SELECT 1 FROM {$metaTable} omq WHERE omq.order_relation_id = o.order_id AND omq.order_meta_key IN ('import_prodcut', 'occasion', 'import_occasion', 'message') AND omq.order_meta_value LIKE '%{$qEsc}%')",
        ];
        if ($qOrderDigits !== '' && ctype_digit($qOrderDigits)) {
            $searchOr[] = 'o.order_id = ' . (int)$qOrderDigits;
            if (preg_match('/^od/i', $qRaw)) {
                $qDigitsEsc = mysqli_real_escape_string($conn, $qOrderDigits);
                $searchOr[] = "CAST(o.order_id AS CHAR) LIKE '%{$qDigitsEsc}%'";
            }
        }
        $nonStatusWhere[] = '(' . implode(' OR ', $searchOr) . ')';
    }

    $whereWithStatus = $nonStatusWhere;
    if (!empty($filters['status_codes'])) {
        $inList = implode(',', array_map('intval', $filters['status_codes']));
        $whereWithStatus[] = "o.order_status IN ({$inList})";
    }

    $joinsSql = "
        FROM {$orderTable} o
        LEFT JOIN {$addrTable} a  ON a.address_id = o.order_address_id
        LEFT JOIN {$cityTable} c  ON c.city_id = a.address_city_id
        LEFT JOIN {$stateTable} s ON s.state_id = a.address_state_id
        LEFT JOIN {$ctryTable} co ON co.country_id = a.address_country_id
        LEFT JOIN {$profTable} p  ON p.profile_user_id = o.order_user_id
        LEFT JOIN {$userTable} u  ON u.user_id = o.order_user_id
        LEFT JOIN ({$dmSubquery}) dm ON dm.order_relation_id = o.order_id
    ";

    $whereSql       = implode(' AND ', $whereWithStatus);
    $nonStatusSql   = implode(' AND ', $nonStatusWhere);

    // Summary & status breakdown counts (status breakdown respects other filters like date/search/payment!)
    $totalRows       = 0;
    $filteredRevenue = 0.0;
    $statusCounts    = ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0];

    if (!$forExport) {
        $summarySql = "
            SELECT
                o.order_status,
                COUNT(*) AS cnt,
                COALESCE(SUM(CASE WHEN REPLACE(TRIM(o.order_total_price), ',', '') REGEXP '^[0-9]+(\\.[0-9]+)?$' THEN CAST(REPLACE(TRIM(o.order_total_price), ',', '') AS DECIMAL(12,2)) ELSE 0 END), 0) AS rev
            {$joinsSql}
            WHERE {$nonStatusSql}
            GROUP BY o.order_status
        ";
        if ($sRes = mysqli_query($conn, $summarySql)) {
            $hasStatusFilter = !empty($filters['status_codes']);
            while ($sRow = mysqli_fetch_assoc($sRes)) {
                $stCode = trim((string)$sRow['order_status']);
                $cnt    = (int)$sRow['cnt'];
                $rev    = (float)$sRow['rev'];
                if (isset($statusCounts[$stCode])) {
                    $statusCounts[$stCode] += $cnt;
                }
                if (!$hasStatusFilter || in_array($stCode, $filters['status_codes'], true)) {
                    $totalRows += $cnt;
                    $filteredRevenue += $rev;
                }
            }
        }
    } else {
        $cntSql = "SELECT COUNT(*) AS cnt {$joinsSql} WHERE {$whereSql}";
        if ($cRes = mysqli_query($conn, $cntSql)) {
            if ($cRow = mysqli_fetch_assoc($cRes)) {
                $totalRows = (int)$cRow['cnt'];
            }
        }
    }

    // Sorting
    switch ($filters['sort_by']) {
        case 'oldest':
            $orderBy = 'o.order_id ASC';
            break;
        case 'delivery_desc':
            $orderBy = 'COALESCE(dm.delivery_date_val, DATE(o.order_generate)) DESC, o.order_id DESC';
            break;
        case 'delivery_asc':
            $orderBy = 'COALESCE(dm.delivery_date_val, DATE(o.order_generate)) ASC, o.order_id ASC';
            break;
        case 'amount_desc':
            $orderBy = "CASE WHEN REPLACE(TRIM(o.order_total_price), ',', '') REGEXP '^[0-9]+(\\.[0-9]+)?$' THEN CAST(REPLACE(TRIM(o.order_total_price), ',', '') AS DECIMAL(12,2)) ELSE 0 END DESC, o.order_id DESC";
            break;
        case 'amount_asc':
            $orderBy = "CASE WHEN REPLACE(TRIM(o.order_total_price), ',', '') REGEXP '^[0-9]+(\\.[0-9]+)?$' THEN CAST(REPLACE(TRIM(o.order_total_price), ',', '') AS DECIMAL(12,2)) ELSE 0 END ASC, o.order_id DESC";
            break;
        case 'newest':
        default:
            $orderBy = 'o.order_id DESC';
            break;
    }

    $page       = $filters['page'];
    $limit      = $filters['limit'];
    $totalPages = $totalRows > 0 ? (int)ceil($totalRows / $limit) : 1;
    if ($page > $totalPages) {
        $page = $totalPages;
    }
    $offset = max(0, ($page - 1) * $limit);

    // Export safety cap (generous enough for full tenant history without OOM)
    $exportCap = 10000;
    $limitSql  = $forExport ? "LIMIT 0, {$exportCap}" : "LIMIT {$offset}, {$limit}";

    $dataSql = "
        SELECT
            o.order_id,
            o.order_user_id,
            o.order_address_id,
            o.order_product_id,
            o.order_product_qty,
            o.order_product_price,
            o.order_currency,
            o.order_delivery_charges,
            o.order_total_price,
            o.order_coupon,
            o.order_gift,
            o.order_msg,
            o.order_status,
            o.order_payment_mode,
            o.order_generate,
            o.order_update,
            p.profile_display_name,
            p.profile_first_name,
            p.profile_last_name,
            p.profile_mobile,
            p.profile_alt_mobile,
            p.profile_email,
            u.user_mobile,
            u.user_email,
            a.address_name,
            a.address_mobile,
            a.address_alternate_mobile,
            a.address_house_no,
            a.address_area,
            a.address_landmark,
            a.address_pin_code,
            a.address_type,
            c.city_name,
            s.state_name,
            co.country_name,
            dm.delivery_date_val,
            dm.delivery_photo_val
        {$joinsSql}
        WHERE {$whereSql}
        ORDER BY {$orderBy}
        {$limitSql}
    ";

    $rawRows         = [];
    $allOrderIds     = [];
    $allProductIdSet = [];

    if ($res = mysqli_query($conn, $dataSql)) {
        while ($row = mysqli_fetch_assoc($res)) {
            $rawRows[]     = $row;
            $oid           = (int)$row['order_id'];
            $allOrderIds[] = $oid;

            foreach (crmOrderSplitCsvValues($row['order_product_id'] ?? '') as $pidRaw) {
                if (ctype_digit($pidRaw) && (int)$pidRaw > 0) {
                    $allProductIdSet[(int)$pidRaw] = true;
                }
            }
        }
    }

    // Batch fetch products & inventory
    $productMap = [];
    if (!empty($allProductIdSet)) {
        $prodIdsSql = implode(',', array_keys($allProductIdSet));
        $prodSql = "
            SELECT
                pr.product_id,
                pr.product_title,
                pr.product_image,
                pr.product_thumbnail,
                inv.inventory_sku,
                inv.inventory_currency,
                inv.inventory_currency_symbol,
                inv.inventory_price
            FROM {$prodTable} pr
            LEFT JOIN {$invTable} inv
                ON inv.inventory_relation_id = pr.product_id
               AND inv.inventory_site_id = {$siteIdSafe}
               AND inv.inventory_type = 'product'
            WHERE pr.product_id IN ({$prodIdsSql})
        ";
        if ($pRes = mysqli_query($conn, $prodSql)) {
            while ($pRow = mysqli_fetch_assoc($pRes)) {
                $pid = (int)$pRow['product_id'];
                if (!isset($productMap[$pid])) {
                    $productMap[$pid] = $pRow;
                }
            }
        }
    }

    // Batch fetch storefront product website links (prefer canonical 200 URLs over 301 redirects)
    $productLinkMap = [];
    if (!empty($allProductIdSet)) {
        $prodIdsSql = implode(',', array_keys($allProductIdSet));
        $linkSql = "
            SELECT link_relation_id, link_url, link_status
            FROM {$linkTable}
            WHERE link_site_id = {$siteIdSafe}
              AND link_type = 'product'
              AND TRIM(COALESCE(link_url, '')) != ''
              AND link_relation_id IN ({$prodIdsSql})
            ORDER BY CASE WHEN link_status = 200 THEN 0 ELSE 1 END, link_id DESC
        ";
        if ($lRes = mysqli_query($conn, $linkSql)) {
            while ($lRow = mysqli_fetch_assoc($lRes)) {
                $pid = (int)$lRow['link_relation_id'];
                if (!isset($productLinkMap[$pid])) {
                    $productLinkMap[$pid] = trim((string)$lRow['link_url']);
                }
            }
        }
    }

    // Batch fetch order_meta & latest transaction per order
    $metaMap = [];
    $txMap   = [];
    if (!empty($allOrderIds)) {
        $orderIdListSql = implode(',', array_map('intval', $allOrderIds));
        $metaSql = "
            SELECT order_relation_id, order_meta_key, order_meta_value
            FROM {$metaTable}
            WHERE order_relation_id IN ({$orderIdListSql})
        ";
        if ($mRes = mysqli_query($conn, $metaSql)) {
            while ($mRow = mysqli_fetch_assoc($mRes)) {
                $oid = (int)$mRow['order_relation_id'];
                $k   = trim((string)$mRow['order_meta_key']);
                $v   = trim((string)$mRow['order_meta_value']);
                if ($k !== '' && $v !== '') {
                    $metaMap[$oid][strtolower($k)] = $v;
                }
            }
        }

        $txSql = "
            SELECT transaction_order_id, transaction_reference_id, transaction_payment_id, transaction_payment_mode, transaction_status
            FROM {$txTable}
            WHERE transaction_site_id = {$siteIdSafe}
              AND transaction_order_id IN ('" . implode("','", array_map('intval', $allOrderIds)) . "')
            ORDER BY transaction_id DESC
        ";
        if ($tRes = mysqli_query($conn, $txSql)) {
            while ($tRow = mysqli_fetch_assoc($tRes)) {
                $oid = (int)$tRow['transaction_order_id'];
                if (!isset($txMap[$oid])) {
                    $txMap[$oid] = $tRow;
                }
            }
        }
    }

    // Normalize structured output rows
    $rows = [];
    $dominantSymbol = '₹';

    foreach ($rawRows as $r) {
        $oid         = (int)$r['order_id'];
        $statusMeta  = crmOrderStatusMeta($r['order_status'] ?? '0');
        $orderMeta   = $metaMap[$oid] ?? [];
        $tx          = $txMap[$oid] ?? null;

        // Product line items
        $pids   = crmOrderSplitCsvValues($r['order_product_id'] ?? '');
        $qtys   = crmOrderSplitCsvValues($r['order_product_qty'] ?? '');
        $prices = crmOrderSplitCsvValues($r['order_product_price'] ?? '');

        $items          = [];
        $totalQty       = 0;
        $computedSub    = 0.0;
        $currencySymbol = '';
        $primaryImage   = '';
        $summaryParts   = [];
        $itemNames      = [];
        $itemSkus       = [];
        $itemQtys       = [];
        $itemUnitPrices = [];
        $itemUrls       = [];

        $itemCount = max(count($pids), count($qtys), count($prices));
        for ($idx = 0; $idx < $itemCount; $idx++) {
            $pidRaw   = $pids[$idx] ?? '';
            $qtyRaw   = $qtys[$idx] ?? ($orderMeta['import_qty'] ?? '1');
            $qtyNum   = (is_numeric($qtyRaw) && (float)$qtyRaw > 0) ? (int)$qtyRaw : 1;
            $priceRaw = $prices[$idx] ?? '';

            $pidInt = ctype_digit($pidRaw) ? (int)$pidRaw : 0;
            $pInfo  = ($pidInt > 0 && isset($productMap[$pidInt])) ? $productMap[$pidInt] : null;
            if ($pInfo) {
                $title = crmOrderNormalizeText($pInfo['product_title'] ?? '');
                $sku   = crmOrderNormalizeText($pInfo['inventory_sku'] ?? '');
                $img   = trim((string)($pInfo['product_image'] ?: $pInfo['product_thumbnail'] ?: ''));
                if ($currencySymbol === '' && !empty($pInfo['inventory_currency_symbol'])) {
                    $currencySymbol = trim((string)$pInfo['inventory_currency_symbol']);
                }
                if ($priceRaw === '' && isset($pInfo['inventory_price'])) {
                    $priceRaw = (string)$pInfo['inventory_price'];
                }
            } else {
                $importProd = crmOrderNormalizeText($orderMeta['import_prodcut'] ?? '');
                if ($importProd !== '') {
                    $title = $importProd;
                } elseif (ctype_digit($pidRaw)) {
                    $title = 'Product #' . $pidRaw . ' (Archived)';
                } elseif ($pidRaw !== '') {
                    $title = crmOrderNormalizeText($pidRaw);
                } else {
                    $title = 'N/A';
                }
                $sku = '';
                $img = '';
            }

            if ($primaryImage === '' && $img !== '') {
                $primaryImage = $img;
            }

            $urlPath    = ($pidInt > 0 && isset($productLinkMap[$pidInt])) ? $productLinkMap[$pidInt] : '';
            $websiteUrl = crmOrderBuildWebsiteUrl($siteBaseUrl, $urlPath);

            $unitNum = crmOrderParseNumber($priceRaw);
            if ($unitNum !== null) {
                $computedSub += ($unitNum * $qtyNum);
            }
            $unitPriceClean = $unitNum !== null ? crmOrderFormatDecimal($unitNum) : crmOrderNormalizeText($priceRaw);
            $totalQty += $qtyNum;

            $labelPart = $title . ' x ' . $qtyNum;
            if ($sku !== '') {
                $labelPart .= ' [' . $sku . ']';
            }
            $summaryParts[] = $labelPart;

            $itemNames[] = $title;
            if ($sku !== '') {
                $itemSkus[] = $sku;
            }
            $itemQtys[] = (string)$qtyNum;
            if ($unitPriceClean !== '') {
                $itemUnitPrices[] = $unitPriceClean;
            }
            if ($websiteUrl !== '') {
                $itemUrls[] = $websiteUrl;
            }

            $items[] = [
                'id'             => $pidRaw,
                'title'          => $title,
                'sku'            => $sku,
                'qty'            => $qtyNum,
                'unit_price'     => $unitPriceClean,
                'unit_price_num' => $unitNum,
                'url_path'       => $urlPath,
                'website_url'    => $websiteUrl,
                'image'          => $img,
            ];
        }

        if ($currencySymbol === '') {
            $currCode = strtoupper(trim((string)($r['order_currency'] ?? 'INR')));
            $currencySymbol = ($currCode === 'USD') ? '$' : (($currCode === 'EUR') ? '€' : (($currCode === 'GBP') ? '£' : '₹'));
        }
        $dominantSymbol = $currencySymbol;

        $currCode        = strtoupper(trim((string)($r['order_currency'] ?? 'INR')));
        $deliveryCharges = trim((string)($r['order_delivery_charges'] ?? '0'));
        if ($deliveryCharges === '' && isset($orderMeta['deliveryfees'])) {
            $deliveryCharges = trim((string)$orderMeta['deliveryfees']);
        }
        $deliveryNum          = crmOrderParseNumber($deliveryCharges);
        $deliveryNum          = $deliveryNum !== null ? $deliveryNum : 0.0;
        $deliveryChargesClean = crmOrderFormatDecimal($deliveryNum);

        $totalAmountRaw = trim((string)($r['order_total_price'] ?? ''));
        $totalAmountNum = crmOrderParseNumber($totalAmountRaw);
        if ($totalAmountNum === null && $computedSub > 0) {
            $totalAmountNum = $computedSub + $deliveryNum;
        }
        if ($totalAmountNum === null) {
            $totalAmountNum = 0.0;
        }
        $totalAmountClean = crmOrderFormatDecimal($totalAmountNum);

        if ($computedSub > 0) {
            $subtotalNum = $computedSub;
        } else {
            $singleProdNum = crmOrderParseNumber($r['order_product_price'] ?? '');
            $subtotalNum   = $singleProdNum !== null ? $singleProdNum : max(0.0, $totalAmountNum - $deliveryNum);
        }
        $productSubtotalClean = crmOrderFormatDecimal($subtotalNum);

        $amountFormatted = crmOrderFormatMoney($totalAmountNum);
        $amountDisplay   = $currencySymbol . ' ' . $amountFormatted;

        $uniqueUrls = array_values(array_unique($itemUrls));
        $primaryUrl = $uniqueUrls[0] ?? '';

        if ($forExport) {
            $stCode = trim((string)($statusMeta['code'] ?? ''));
            if (isset($statusCounts[$stCode])) {
                $statusCounts[$stCode]++;
            }
            $filteredRevenue += $totalAmountNum;
        }

        // Sender details (Customer / Billing placer)
        $senderName = crmOrderNormalizeText($r['profile_display_name'] ?? '');
        if ($senderName === '') {
            $senderName = crmOrderNormalizeText(trim((string)($r['profile_first_name'] ?? '') . ' ' . (string)($r['profile_last_name'] ?? '')));
        }
        if ($senderName === '') {
            $senderName = 'Customer #' . (int)($r['order_user_id'] ?? 0);
        }
        $senderMobile = trim((string)($r['profile_mobile'] ?: ($r['user_mobile'] ?: '')));
        $senderAltMob = trim((string)($r['profile_alt_mobile'] ?? ''));
        $senderEmail  = trim((string)($r['profile_email'] ?: ($r['user_email'] ?: '')));

        if ($redactPii) {
            $senderMobile = crmOrderMaskString($senderMobile, 'phone');
            $senderAltMob = crmOrderMaskString($senderAltMob, 'phone');
            $senderEmail  = crmOrderMaskString($senderEmail, 'email');
        }

        $senderParts = array_filter([$senderName, $senderMobile, $senderEmail], function ($v) {
            return $v !== '';
        });

        // Receiver details (Shipping recipient)
        $receiverName    = crmOrderNormalizeText($r['address_name'] ?? '');
        $receiverMobile  = trim((string)($r['address_mobile'] ?? ''));
        $receiverAltMob  = trim((string)($r['address_alternate_mobile'] ?? ''));
        $houseNo         = crmOrderNormalizeText($r['address_house_no'] ?? '');
        $area            = crmOrderNormalizeText($r['address_area'] ?? '');
        $landmark        = crmOrderNormalizeText($r['address_landmark'] ?? '');
        $city            = crmOrderNormalizeText($r['city_name'] ?? '');
        $state           = crmOrderNormalizeText($r['state_name'] ?? '');
        $country         = crmOrderNormalizeText($r['country_name'] ?? '');
        $pinCode         = trim((string)($r['address_pin_code'] ?? ''));
        $addressType     = trim((string)($r['address_type'] ?? ''));

        if ($redactPii) {
            $receiverMobile = crmOrderMaskString($receiverMobile, 'phone');
            $receiverAltMob = crmOrderMaskString($receiverAltMob, 'phone');
            $houseNo        = $houseNo !== '' ? '[Restricted]' : '';
            $area           = $area !== '' ? '[Restricted]' : '';
            $landmark       = '';
        }

        $streetParts = array_filter([$houseNo, $area, $landmark !== '' ? ('Near ' . $landmark) : ''], function ($v) {
            return trim((string)$v) !== '';
        });
        $streetAddress = implode(', ', $streetParts);

        $addrPieces = array_filter([$houseNo, $area, $landmark !== '' ? ('Near ' . $landmark) : '', $city, $state, $pinCode, $country], function ($v) {
            return trim((string)$v) !== '';
        });
        $fullAddress = implode(', ', $addrPieces);

        $receiverParts = array_filter([
            $receiverName,
            $receiverMobile,
            $receiverAltMob ? ('Alt: ' . $receiverAltMob) : '',
            $fullAddress
        ], function ($v) {
            return $v !== '';
        });

        // Delivery details
        $rawDelivDate = trim((string)($r['delivery_date_val'] ?: ($orderMeta['deliverydate'] ?? '')));
        $delivIso     = crmOrderNormalizeDateString($rawDelivDate, false);
        $delivDisplay = $delivIso !== '' ? date('d/m/Y', strtotime($delivIso)) : ($rawDelivDate !== '' ? $rawDelivDate : 'Not scheduled');

        $delivSlot = crmOrderFormatSlot($orderMeta['deliveryslot'] ?? '');
        $delivTime = crmOrderFormatSlot($orderMeta['deliverytime'] ?? ($orderMeta['preferredtime'] ?? ($orderMeta['timeslot'] ?? '')));
        $occasion  = crmOrderNormalizeText($orderMeta['occasion'] ?? ($orderMeta['import_occasion'] ?? ''));
        if (strcasecmp($occasion, 'other') === 0) {
            $occasion = 'Other';
        }
        $giftMsg   = crmOrderNormalizeText($orderMeta['message'] ?? ($r['order_msg'] ?? ''));
        $delivComment  = crmOrderNormalizeText($orderMeta['delivery_comment'] ?? '');
        $returnReason  = crmOrderNormalizeText($orderMeta['return_reason'] ?? '');
        $returnComment = crmOrderNormalizeText($orderMeta['return_comment'] ?? '');
        $rawPhotoPath  = trim((string)($r['delivery_photo_val'] ?: ($orderMeta['delivery_photo'] ?? '')));
        $photoUrl      = $rawPhotoPath !== '' ? crmOrderBuildWebsiteUrl($siteBaseUrl, $rawPhotoPath) : '';
        $photoStatus   = ($photoUrl !== '') ? 'Uploaded' : 'Not Uploaded';

        $delivSummaryParts = array_filter([
            $delivDisplay !== 'Not scheduled' ? ('Date: ' . $delivDisplay) : '',
            $delivSlot !== '' ? ('Slot: ' . $delivSlot) : '',
            ($delivTime !== '' && strcasecmp($delivTime, $delivSlot) !== 0) ? ('Time: ' . $delivTime) : '',
            $occasion !== '' ? ('Occasion: ' . $occasion) : '',
            $giftMsg !== '' ? ('Msg: ' . $giftMsg) : '',
            $delivComment !== '' ? ('Note: ' . $delivComment) : '',
        ], function ($v) {
            return $v !== '';
        });

        // Payment details
        $payModeRaw    = trim((string)($r['order_payment_mode'] ?? ''));
        $payModeLabel  = crmOrderPaymentLabel($payModeRaw);
        $txRef         = $tx ? trim((string)($tx['transaction_reference_id'] ?: ($tx['transaction_payment_id'] ?? ''))) : '';
        $txStatus      = $tx ? trim((string)($tx['transaction_status'] ?? '')) : '';
        $txStatusLabel = crmOrderTransactionStatusLabel($txStatus);
        $couponCode    = crmOrderNormalizeText($r['order_coupon'] ?? '');

        $createdRaw         = trim((string)($r['order_generate'] ?? ''));
        $createdTs          = ($createdRaw !== '' && strtotime($createdRaw) !== false) ? (int)strtotime($createdRaw) : 0;
        $createdDisplay     = $createdTs > 0 ? date('d/m/Y h:i A', $createdTs) : 'N/A';
        $createdDateDisplay = $createdTs > 0 ? date('d/m/Y', $createdTs) : 'N/A';
        $createdTimeDisplay = $createdTs > 0 ? date('h:i A', $createdTs) : '';

        $rows[] = [
            'order_id'             => $oid,
            'order_code'           => 'OD' . $oid,
            'view_url'             => 'order/?type=view&id=' . base64_encode((string)$oid),
            'created_at'           => $createdRaw,
            'created_display'      => $createdDisplay,
            'created_date_display' => $createdDateDisplay,
            'created_time_display' => $createdTimeDisplay,
            'created_ts'           => $createdTs,
            'status'          => $statusMeta,
            'product'         => [
                'items'                => $items,
                'count'                => count($items),
                'total_qty'            => max(1, $totalQty),
                'primary_title'        => $items[0]['title'] ?? 'N/A',
                'primary_url'          => $primaryUrl,
                'primary_image'        => $primaryImage,
                'names_text'           => !empty($itemNames) ? implode(' | ', $itemNames) : 'N/A',
                'skus_text'            => implode(' | ', $itemSkus),
                'qtys_text'            => !empty($itemQtys) ? implode(' | ', $itemQtys) : '1',
                'unit_prices_text'     => implode(' | ', $itemUnitPrices),
                'links_text'           => implode(' | ', $uniqueUrls),
                'summary_text'         => !empty($summaryParts) ? implode('; ', $summaryParts) : 'N/A',
                'currency_code'        => $currCode !== '' ? $currCode : 'INR',
                'currency_symbol'      => $currencySymbol,
                'subtotal'             => $productSubtotalClean,
                'subtotal_num'         => $subtotalNum,
                'delivery_charges'     => $deliveryChargesClean,
                'delivery_charges_num' => $deliveryNum,
                'coupon'               => $couponCode,
                'total_amount'         => $totalAmountClean,
                'total_amount_num'     => $totalAmountNum,
                'amount_formatted'     => $amountFormatted,
                'amount_display'       => $amountDisplay,
            ],
            'sender' => [
                'user_id'      => (int)($r['order_user_id'] ?? 0),
                'name'         => $senderName,
                'first_name'   => trim((string)($r['profile_first_name'] ?? '')),
                'last_name'    => trim((string)($r['profile_last_name'] ?? '')),
                'mobile'       => $senderMobile,
                'alt_mobile'   => $senderAltMob,
                'email'        => $senderEmail,
                'summary_text' => !empty($senderParts) ? implode(' | ', $senderParts) : 'N/A',
            ],
            'receiver' => [
                'name'           => $receiverName !== '' ? $receiverName : 'N/A',
                'mobile'         => $receiverMobile,
                'alt_mobile'     => $receiverAltMob,
                'address_type'   => $addressType !== '' ? ucfirst($addressType) : '',
                'house_no'       => $houseNo,
                'area'           => $area,
                'landmark'       => $landmark,
                'street_address' => $streetAddress,
                'city'           => $city,
                'state'          => $state,
                'country'        => $country,
                'pin_code'       => $pinCode,
                'full_address'   => $fullAddress,
                'summary_text'   => !empty($receiverParts) ? implode(' | ', $receiverParts) : 'N/A',
            ],
            'delivery' => [
                'date_iso'       => $delivIso,
                'date_display'   => $delivDisplay,
                'slot'           => $delivSlot,
                'time'           => $delivTime,
                'occasion'       => $occasion,
                'message'        => $giftMsg,
                'comment'        => $delivComment,
                'return_reason'  => $returnReason,
                'return_comment' => $returnComment,
                'photo_url'      => $photoUrl,
                'photo_status'   => $photoStatus,
                'summary_text'   => !empty($delivSummaryParts) ? implode(' | ', $delivSummaryParts) : 'No delivery instructions',
            ],
            'payment' => [
                'mode'                     => $payModeRaw,
                'mode_label'               => $payModeLabel,
                'transaction_ref'          => $txRef,
                'transaction_status'       => $txStatus,
                'transaction_status_label' => $txStatusLabel,
            ],
        ];
    }

    // Dynamic facet options for UI dropdowns
    $facets = [];
    if ($includeFacets) {
        $paymentModes = [];
        $pmSql = "
            SELECT DISTINCT TRIM(COALESCE(order_payment_mode, '')) AS pm
            FROM {$orderTable}
            WHERE order_site_id = {$siteIdSafe}
              AND TRIM(COALESCE(order_product_id, '')) != ''
            ORDER BY pm ASC
        ";
        if ($pmRes = mysqli_query($conn, $pmSql)) {
            $hasUnspecified = false;
            while ($pmRow = mysqli_fetch_assoc($pmRes)) {
                $pmVal = trim((string)$pmRow['pm']);
                if ($pmVal === '') {
                    $hasUnspecified = true;
                } else {
                    $paymentModes[] = [
                        'value' => $pmVal,
                        'label' => crmOrderPaymentLabel($pmVal),
                    ];
                }
            }
            if ($hasUnspecified) {
                $paymentModes[] = ['value' => 'unspecified', 'label' => 'Unspecified'];
            }
        }

        $cities = [];
        $citySql = "
            SELECT DISTINCT TRIM(c.city_name) AS city_name
            FROM {$orderTable} o
            INNER JOIN {$addrTable} a ON a.address_id = o.order_address_id
            INNER JOIN {$cityTable} c ON c.city_id = a.address_city_id
            WHERE o.order_site_id = {$siteIdSafe}
              AND TRIM(COALESCE(o.order_product_id, '')) != ''
              AND TRIM(COALESCE(c.city_name, '')) != ''
            ORDER BY c.city_name ASC
            LIMIT 250
        ";
        if ($cRes = mysqli_query($conn, $citySql)) {
            while ($cRow = mysqli_fetch_assoc($cRes)) {
                $cities[] = $cRow['city_name'];
            }
        }

        $facets = [
            'statuses'      => array_values(crmOrderStatusCatalog()),
            'payment_modes' => $paymentModes,
            'cities'        => $cities,
        ];
    }

    return [
        'rows'       => $rows,
        'pagination' => [
            'total'       => $totalRows,
            'page'        => $forExport ? 1 : $page,
            'limit'       => $forExport ? count($rows) : $limit,
            'total_pages' => $forExport ? 1 : $totalPages,
        ],
        'summary' => [
            'total_orders'     => $totalRows,
            'filtered_revenue' => round($filteredRevenue, 2),
            'currency_symbol'  => $dominantSymbol,
            'status_counts'    => $statusCounts,
        ],
        'facets' => $facets,
    ];
}

/**
 * Prevents spreadsheet CSV/XLSX formula injection (`=`, `+`, `-`, `@`, tab, CR).
 */
function crmOrderSafeSpreadsheetCell($value): string
{
    $str = (string)$value;
    $str = str_replace(["\r\n", "\r", "\n"], ' ', $str);
    $trimmed = ltrim($str);
    if ($trimmed === '') {
        return $str;
    }
    $first = $trimmed[0];
    if (in_array($first, ['=', '+', '-', '@', "\t"], true)) {
        // Keep genuine signed plain numbers unprefixed? No, better safe: only allow purely negative integer/decimal if needed, otherwise quote all formula prefixes
        if ($first === '-' && preg_match('/^-\d+(\.\d+)?$/', $trimmed)) {
            return $str;
        }
        return "'" . $str;
    }
    return $str;
}

/**
 * Formats an integer 1-based column index into Excel column letters (1 -> A, 27 -> AA).
 */
function crmOrderExcelColumnName(int $colIndex): string
{
    $name = '';
    while ($colIndex > 0) {
        $mod  = ($colIndex - 1) % 26;
        $name = chr(65 + $mod) . $name;
        $colIndex = (int)(($colIndex - $mod) / 26);
    }
    return $name;
}

/**
 * Strips XML 1.0 invalid control characters before writing into OOXML cells.
 */
function crmOrderXmlSafeString(string $str): string
{
    $clean = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $str);
    return $clean === null ? '' : $clean;
}

/**
 * Streams a native OpenXML .xlsx spreadsheet to stdout without external Composer dependencies.
 * Supports numeric cell typing, clickable external hyperlinks, semantic status badges, zebra rows,
 * frozen header rows, and custom column widths.
 *
 * @param string[] $headers
 * @param array<int, array<int, string>> $rows
 * @param array{
 *   colWidths?: array<int, int|float>,
 *   integerCols?: int[],
 *   numericCols?: int[],
 *   boldNumericCols?: int[],
 *   linkCols?: int[],
 *   boldAccentCols?: int[],
 *   statusCol?: int|null
 * } $options
 */
function crmOrderStreamXlsx(string $filename, array $headers, array $rows, string $sheetTitle = 'CRM Orders', array $options = []): void
{
    if (!class_exists('ZipArchive') || !class_exists('XMLWriter')) {
        crmOrderRespondAuthError(500, 'Server missing ZipArchive or XMLWriter extension for XLSX export.');
    }

    $tmpPath = tempnam(sys_get_temp_dir(), 'crm_orders_xlsx_');
    if ($tmpPath === false) {
        crmOrderRespondAuthError(500, 'Unable to allocate temporary file for XLSX generation.');
    }

    $zip = new ZipArchive();
    if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        @unlink($tmpPath);
        crmOrderRespondAuthError(500, 'Unable to create XLSX archive.');
    }

    $safeTitle = preg_replace('/[:\\\\\/\?\*\[\]]/', ' ', trim($sheetTitle));
    $safeTitle = substr($safeTitle !== '' ? $safeTitle : 'CRM Orders', 0, 31);

    $colWidthOverrides = is_array($options['colWidths'] ?? null) ? $options['colWidths'] : [];
    $integerColMap     = array_flip(array_map('intval', (array)($options['integerCols'] ?? [])));
    $numericColMap     = array_flip(array_map('intval', (array)($options['numericCols'] ?? [])));
    $boldNumericColMap = array_flip(array_map('intval', (array)($options['boldNumericCols'] ?? [])));
    $linkColMap        = array_flip(array_map('intval', (array)($options['linkCols'] ?? [])));
    $boldAccentColMap  = array_flip(array_map('intval', (array)($options['boldAccentCols'] ?? [])));
    $statusCol         = isset($options['statusCol']) && $options['statusCol'] !== null ? (int)$options['statusCol'] : null;

    // [Content_Types].xml
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . '</Types>';

    // _rels/.rels
    $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';

    // xl/_rels/workbook.xml.rels
    $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';

    // xl/workbook.xml
    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
        . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets>'
        . '<sheet name="' . htmlspecialchars($safeTitle, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '" sheetId="1" r:id="rId1"/>'
        . '</sheets>'
        . '</workbook>';

    /*
     * xl/styles.xml style map (cellXfs index):
     *  0: Text even (#FFFFFF)          1: Header (#0F172A bold white)
     *  2: Text odd zebra (#F8FAFC)     3: Integer even (numFmt 1 = 0)
     *  4: Integer odd (numFmt 1 = 0)   5: Decimal even (numFmt 4 = #,##0.00)
     *  6: Decimal odd (numFmt 4)       7: Order Price highlight (#EEF2FF bold #,##0.00)
     *  8: Hyperlink even (#1D4ED8 u)   9: Hyperlink odd (#1D4ED8 u, #F8FAFC fill)
     * 10: Bold accent even (#0F172A)  11: Bold accent odd (#0F172A, #F8FAFC fill)
     * 12: Status green (#ECFDF5/#065F46) 13: Status amber (#FFFBEB/#92400E)
     * 14: Status red (#FEF2F2/#991B1B)
     */
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="7">'
        . '<font><sz val="10"/><color rgb="FF1E293B"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="10"/><color rgb="FF0F172A"/><name val="Calibri"/></font>'
        . '<font><u/><sz val="10"/><color rgb="FF1D4ED8"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="10"/><color rgb="FF065F46"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="10"/><color rgb="FF92400E"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="10"/><color rgb="FF991B1B"/><name val="Calibri"/></font>'
        . '</fonts>'
        . '<fills count="8">'
        . '<fill><patternFill patternType="none"/></fill>'
        . '<fill><patternFill patternType="gray125"/></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/></patternFill></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FFEEF2FF"/></patternFill></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/></patternFill></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FFFFFBEB"/></patternFill></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FFFEF2F2"/></patternFill></fill>'
        . '</fills>'
        . '<borders count="2">'
        . '<border><left style="thin"><color rgb="FFE2E8F0"/></left><right style="thin"><color rgb="FFE2E8F0"/></right><top style="thin"><color rgb="FFE2E8F0"/></top><bottom style="thin"><color rgb="FFE2E8F0"/></bottom></border>'
        . '<border><left style="thin"><color rgb="FF0F172A"/></left><right style="thin"><color rgb="FF0F172A"/></right><top style="thin"><color rgb="FF0F172A"/></top><bottom style="thin"><color rgb="FF0F172A"/></bottom></border>'
        . '</borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="15">'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>'
        . '<xf numFmtId="0" fontId="0" fillId="3" borderId="0" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        . '<xf numFmtId="1" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
        . '<xf numFmtId="1" fontId="0" fillId="3" borderId="0" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
        . '<xf numFmtId="4" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
        . '<xf numFmtId="4" fontId="0" fillId="3" borderId="0" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
        . '<xf numFmtId="4" fontId="2" fillId="4" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
        . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        . '<xf numFmtId="0" fontId="3" fillId="3" borderId="0" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        . '<xf numFmtId="0" fontId="2" fillId="3" borderId="0" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
        . '<xf numFmtId="0" fontId="4" fillId="5" borderId="0" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>'
        . '<xf numFmtId="0" fontId="5" fillId="6" borderId="0" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>'
        . '<xf numFmtId="0" fontId="6" fillId="7" borderId="0" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>'
        . '</cellXfs>'
        . '</styleSheet>';

    // Compute clean column widths respecting overrides and sample lengths
    $colCount  = count($headers);
    $colWidths = [];
    foreach ($headers as $i => $h) {
        if (isset($colWidthOverrides[$i])) {
            $colWidths[$i] = (float)$colWidthOverrides[$i];
        } else {
            $colWidths[$i] = max(12, min(42, strlen((string)$h) + 5));
        }
    }
    if (empty($colWidthOverrides)) {
        foreach (array_slice($rows, 0, 50) as $sampleRow) {
            for ($i = 0; $i < $colCount; $i++) {
                $len = strlen((string)($sampleRow[$i] ?? ''));
                if ($len + 4 > $colWidths[$i]) {
                    $colWidths[$i] = min(46, $len + 4);
                }
            }
        }
    }

    $hyperlinks   = [];
    $urlRelMap    = [];
    $nextRelIndex = 1;

    $sheetXmlPath = $tmpPath . '.sheet.xml';
    $xw = new XMLWriter();
    $xw->openUri($sheetXmlPath);
    $xw->startDocument('1.0', 'UTF-8', 'yes');
    $xw->startElement('worksheet');
    $xw->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $xw->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

    // Freeze top header row
    $xw->startElement('sheetViews');
    $xw->startElement('sheetView');
    $xw->writeAttribute('tabSelected', '1');
    $xw->writeAttribute('workbookViewId', '0');
    $xw->startElement('pane');
    $xw->writeAttribute('ySplit', '1');
    $xw->writeAttribute('topLeftCell', 'A2');
    $xw->writeAttribute('activePane', 'bottomLeft');
    $xw->writeAttribute('state', 'frozen');
    $xw->endElement();
    $xw->endElement();
    $xw->endElement();

    // Column widths
    $xw->startElement('cols');
    for ($i = 0; $i < $colCount; $i++) {
        $xw->startElement('col');
        $xw->writeAttribute('min', (string)($i + 1));
        $xw->writeAttribute('max', (string)($i + 1));
        $xw->writeAttribute('width', (string)$colWidths[$i]);
        $xw->writeAttribute('customWidth', '1');
        $xw->endElement();
    }
    $xw->endElement();

    $xw->startElement('sheetData');

    // Header row (Row 1)
    $xw->startElement('row');
    $xw->writeAttribute('r', '1');
    $xw->writeAttribute('ht', '26');
    $xw->writeAttribute('customHeight', '1');
    for ($c = 0; $c < $colCount; $c++) {
        $cellRef = crmOrderExcelColumnName($c + 1) . '1';
        $xw->startElement('c');
        $xw->writeAttribute('r', $cellRef);
        $xw->writeAttribute('t', 'inlineStr');
        $xw->writeAttribute('s', '1');
        $xw->startElement('is');
        $xw->writeElement('t', crmOrderXmlSafeString((string)$headers[$c]));
        $xw->endElement();
        $xw->endElement();
    }
    $xw->endElement();

    // Data rows
    $rowNum = 2;
    foreach ($rows as $rowIdx => $rowVals) {
        $isOdd = ($rowIdx % 2 === 1);
        $xw->startElement('row');
        $xw->writeAttribute('r', (string)$rowNum);

        for ($c = 0; $c < $colCount; $c++) {
            $cellRef = crmOrderExcelColumnName($c + 1) . $rowNum;
            $rawVal  = (string)($rowVals[$c] ?? '');
            $numVal  = null;

            $isBoldNumCol = isset($boldNumericColMap[$c]);
            $isNumCol     = isset($numericColMap[$c]);
            $isIntCol     = isset($integerColMap[$c]);
            $isLinkCol    = isset($linkColMap[$c]);
            $isBoldAccent = isset($boldAccentColMap[$c]);

            if ($isBoldNumCol || $isNumCol || $isIntCol) {
                $numVal = crmOrderParseNumber($rawVal);
            }

            if ($numVal !== null) {
                if ($isBoldNumCol) {
                    $styleId = '7';
                } elseif ($isNumCol) {
                    $styleId = $isOdd ? '6' : '5';
                } else {
                    $styleId = $isOdd ? '4' : '3';
                }
                $xw->startElement('c');
                $xw->writeAttribute('r', $cellRef);
                $xw->writeAttribute('t', 'n');
                $xw->writeAttribute('s', $styleId);
                $xw->writeElement('v', crmOrderFormatDecimal($numVal));
                $xw->endElement();
                continue;
            }

            $safeVal = crmOrderSafeSpreadsheetCell($rawVal);
            $styleId = $isOdd ? '2' : '0';

            if ($statusCol !== null && $c === $statusCol) {
                $stLower = strtolower(trim($rawVal));
                if (in_array($stLower, ['confirm', 'confirmed', 'delivered'], true)) {
                    $styleId = '12';
                } elseif (in_array($stLower, ['pending', 'waiting'], true)) {
                    $styleId = '13';
                } elseif (in_array($stLower, ['cancel', 'cancelled', 'reject', 'rejected', 'rto'], true)) {
                    $styleId = '14';
                }
            } elseif ($isBoldNumCol) {
                $styleId = '7';
            } elseif ($isBoldAccent) {
                $styleId = $isOdd ? '11' : '10';
            } elseif ($isLinkCol && preg_match('#^https?://#i', trim($rawVal))) {
                $trimmedUrl = trim($rawVal);
                if (preg_match('#^https?://[^\s|]+$#i', $trimmedUrl)) {
                    $styleId = $isOdd ? '9' : '8';
                    if (!isset($urlRelMap[$trimmedUrl])) {
                        $urlRelMap[$trimmedUrl] = 'rId' . $nextRelIndex;
                        $nextRelIndex++;
                    }
                    $hyperlinks[] = [
                        'ref' => $cellRef,
                        'rid' => $urlRelMap[$trimmedUrl],
                    ];
                }
            }

            $xw->startElement('c');
            $xw->writeAttribute('r', $cellRef);
            $xw->writeAttribute('t', 'inlineStr');
            $xw->writeAttribute('s', $styleId);
            $xw->startElement('is');
            $xw->writeElement('t', crmOrderXmlSafeString($safeVal));
            $xw->endElement();
            $xw->endElement();
        }
        $xw->endElement();
        $rowNum++;
    }
    $xw->endElement(); // sheetData

    if ($colCount > 0) {
        $lastCol = crmOrderExcelColumnName($colCount);
        $lastRow = max(1, count($rows) + 1);
        $xw->startElement('autoFilter');
        $xw->writeAttribute('ref', "A1:{$lastCol}{$lastRow}");
        $xw->endElement();
    }

    // ECMA-376 strict child order: <hyperlinks> immediately follows <autoFilter>
    if (!empty($hyperlinks)) {
        $xw->startElement('hyperlinks');
        foreach ($hyperlinks as $hl) {
            $xw->startElement('hyperlink');
            $xw->writeAttribute('ref', $hl['ref']);
            $xw->writeAttribute('r:id', $hl['rid']);
            $xw->endElement();
        }
        $xw->endElement();
    }

    $xw->endElement(); // worksheet
    $xw->endDocument();
    $xw->flush();

    $zip->addFromString('[Content_Types].xml', $contentTypes);
    $zip->addFromString('_rels/.rels', $rootRels);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
    $zip->addFromString('xl/workbook.xml', $workbook);
    $zip->addFromString('xl/styles.xml', $styles);
    $zip->addFile($sheetXmlPath, 'xl/worksheets/sheet1.xml');

    if (!empty($urlRelMap)) {
        $sheetRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        foreach ($urlRelMap as $targetUrl => $rId) {
            $sheetRels .= '<Relationship Id="' . $rId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink" Target="' . htmlspecialchars(crmOrderXmlSafeString($targetUrl), ENT_XML1 | ENT_COMPAT, 'UTF-8') . '" TargetMode="External"/>';
        }
        $sheetRels .= '</Relationships>';
        $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', $sheetRels);
    }

    $zip->close();

    @unlink($sheetXmlPath);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', true);
    header('Content-Disposition: attachment; filename="' . $filename . '"', true);
    header('Cache-Control: no-store, no-cache, must-revalidate', true);
    header('Content-Length: ' . filesize($tmpPath), true);

    readfile($tmpPath);
    @unlink($tmpPath);
    exit;
}
