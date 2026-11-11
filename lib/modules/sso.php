<?php

/**
 * Cross-Site Single Sign-On (SSO) & Parent-Group Session Helpers
 *
 * Authenticates users seamlessly across parent/sibling sites sharing the same
 * active `site_installation_parent_site_id` group while preserving strict per-site
 * `site_access` RBAC, single-use DB tokens, HMAC-signed hops, and open-redirect protection.
 */

if (!defined('ISW_SSO_LOADED')) {
    define('ISW_SSO_LOADED', true);
}

if (!defined('EMAIL_VERIFY_SECRET') || !defined('ENCRYPTION_AUTH_KEY')) {
    $secureConfigFile = dirname(__DIR__) . '/configuration/secure.php';
    if (file_exists($secureConfigFile)) {
        require_once $secureConfigFile;
    }
}

if (!function_exists('iswTablePrefix')) {
    function iswTablePrefix(): string
    {
        return defined('TABLE_PREFIX') ? (string) TABLE_PREFIX : 'isw_';
    }
}

if (!function_exists('iswIsHttpsRequest')) {
    function iswIsHttpsRequest(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        }
        if (!empty($_SERVER['HTTP_CF_VISITOR']) && stripos((string) $_SERVER['HTTP_CF_VISITOR'], '"https"') !== false) {
            return true;
        }
        if ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443) {
            return true;
        }
        return (defined('HTTP_PROTOCOL') && HTTP_PROTOCOL === 'https://');
    }
}

if (!function_exists('iswStripPort')) {
    function iswStripPort($host): string
    {
        $clean = preg_replace('#^https?://#i', '', trim((string) $host));
        $clean = rtrim($clean, '/');
        if (strpos($clean, '/') !== false) {
            $clean = explode('/', $clean, 2)[0];
        }
        $clean = preg_replace('/:\d+$/', '', $clean);
        return strtolower(trim($clean));
    }
}

if (!function_exists('iswExtractPort')) {
    function iswExtractPort($host): string
    {
        $clean = preg_replace('#^https?://#i', '', trim((string) $host));
        $clean = explode('/', $clean, 2)[0];
        if (preg_match('/:(\d+)$/', $clean, $m)) {
            return (string) $m[1];
        }
        return '';
    }
}

if (!function_exists('iswIsLocalHost')) {
    function iswIsLocalHost($host): bool
    {
        $noPort = iswStripPort($host);
        if ($noPort === '') {
            return false;
        }
        if ($noPort === 'localhost' || filter_var($noPort, FILTER_VALIDATE_IP)) {
            return true;
        }
        return (bool) preg_match('/\.(local|locals|test)$/i', $noPort);
    }
}

if (!function_exists('iswGetCookieDomain')) {
    function iswGetCookieDomain($host = null): string
    {
        $target = ($host !== null)
            ? (string) $host
            : (defined('MAIN_DOMAIN') ? (string) MAIN_DOMAIN : (string) ($_SERVER['HTTP_HOST'] ?? ''));

        $clean = iswStripPort($target);
        if ($clean === '' || $clean === 'localhost' || filter_var($clean, FILTER_VALIDATE_IP) || strpos($clean, '.') === false) {
            return '';
        }

        return '.' . ltrim($clean, '.');
    }
}

if (!function_exists('iswBuildCookieOptions')) {
    function iswBuildCookieOptions(int $expiry, bool $httpOnly = false, ?string $cookieDomain = null): array
    {
        $isHttps = iswIsHttpsRequest();
        $domain = ($cookieDomain !== null) ? $cookieDomain : iswGetCookieDomain();

        return [
            'expires'  => $expiry,
            'path'     => '/',
            'domain'   => $domain,
            'secure'   => $isHttps,
            'httponly' => $httpOnly,
            'samesite' => $isHttps ? 'None' : 'Lax',
        ];
    }
}

if (!function_exists('iswGetSsoSecret')) {
    function iswGetSsoSecret(): string
    {
        $k1 = defined('EMAIL_VERIFY_SECRET') ? (string) EMAIL_VERIFY_SECRET : '';
        $k2 = defined('ENCRYPTION_AUTH_KEY') ? (string) ENCRYPTION_AUTH_KEY : '';
        $raw = $k1 . '|' . $k2 . '|isw-sso-v1';
        return hash('sha256', $raw);
    }
}

if (!function_exists('iswSsoEnsureSchema')) {
    function iswSsoEnsureSchema($connection): bool
    {
        static $ensured = false;
        if ($ensured) {
            return true;
        }
        if (!($connection instanceof mysqli)) {
            return false;
        }

        $tbl = iswTablePrefix() . 'sso_tokens';
        $sql = "CREATE TABLE IF NOT EXISTS `{$tbl}` (
            `sso_id` bigint NOT NULL AUTO_INCREMENT,
            `sso_token` varchar(128) NOT NULL,
            `sso_user_id` bigint NOT NULL,
            `sso_source_site_id` bigint NOT NULL,
            `sso_target_site_id` bigint NOT NULL,
            `sso_parent_site_id` bigint NOT NULL DEFAULT 0,
            `sso_device_id` varchar(255) NOT NULL,
            `sso_expires_at` datetime NOT NULL,
            `sso_used_at` datetime DEFAULT NULL,
            `sso_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`sso_id`),
            UNIQUE KEY `uniq_sso_token` (`sso_token`),
            KEY `idx_sso_expires` (`sso_expires_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        if (@mysqli_query($connection, $sql)) {
            $ensured = true;
            try {
                if (random_int(1, 20) === 1) {
                    @mysqli_query($connection, "DELETE FROM `{$tbl}` WHERE `sso_expires_at` < DATE_SUB(NOW(), INTERVAL 1 DAY)");
                }
            } catch (\Throwable $e) {
                // Ignore cleanup failure
            }
            return true;
        }

        return false;
    }
}

if (!function_exists('iswNormalizeRedirectPath')) {
    function iswNormalizeRedirectPath($raw, string $default = '/dashboard/'): string
    {
        $val = trim((string) $raw);
        for ($i = 0; $i < 3; $i++) {
            if (stripos($val, '%2f') === false && stripos($val, '%3a') === false && stripos($val, '%25') === false) {
                break;
            }
            $decoded = urldecode($val);
            if ($decoded === $val) {
                break;
            }
            $val = trim($decoded);
        }

        // Strip control characters, reject backslashes, protocol-relative URLs, or non-HTTP schemes
        $val = preg_replace('/[\x00-\x1F\x7F]+/', '', $val);
        if ($val === '' || $val === '#' || strpos($val, '\\') !== false || strpos($val, '//') === 0) {
            return $default;
        }
        if (preg_match('/^[a-z0-9+.-]+:/i', $val) && !preg_match('#^https?://#i', $val)) {
            return $default;
        }

        // Convert same-origin absolute URL to relative path; reject external hosts
        if (preg_match('#^https?://#i', $val)) {
            $parsed = @parse_url($val);
            if (!is_array($parsed) || empty($parsed['host'])) {
                return $default;
            }
            $urlHost = iswStripPort($parsed['host']);
            $allowedHosts = [];
            if (!empty($_SERVER['HTTP_HOST'])) {
                $allowedHosts[] = iswStripPort($_SERVER['HTTP_HOST']);
            }
            if (defined('SITE_DOMAIN')) {
                $allowedHosts[] = iswStripPort(SITE_DOMAIN);
            }
            if (!in_array($urlHost, array_filter($allowedHosts), true)) {
                return $default;
            }
            $val = ($parsed['path'] ?? '/') . (isset($parsed['query']) && $parsed['query'] !== '' ? '?' . $parsed['query'] : '');
        }

        $trimmed = trim($val);
        if ($trimmed === '' || $trimmed === 'dashboard' || $trimmed === 'dashboard/' || $trimmed === '/login/dashboard' || $trimmed === '/login/dashboard/') {
            return $default;
        }

        $val = '/' . ltrim($trimmed, '/');
        if (strpos($val, '//') === 0) {
            return $default;
        }

        $pathOnly = strtok($val, '?') ?: '/';
        $pathLower = strtolower(rtrim($pathOnly, '/'));
        if ($pathLower === '/login' || $pathLower === '/logout' || strpos($pathLower, '/sso/') === 0 || $pathLower === '/sso') {
            return $default;
        }

        return $val;
    }
}

if (!function_exists('iswResolveSiteOrigin')) {
    function iswResolveSiteOrigin(array $siteRow, ?string $currentHost = null, ?string $protocol = null): string
    {
        $proto = $protocol ?: (iswIsHttpsRequest() ? 'https://' : 'http://');
        $curHost = $currentHost !== null
            ? trim($currentHost)
            : (string) ($_SERVER['HTTP_HOST'] ?? (defined('SITE_DOMAIN') ? SITE_DOMAIN : ''));
        $curPort = iswExtractPort($curHost);

        $rowSiteId = (int) ($siteRow['site_id'] ?? 0);
        $curSiteId = defined('SITE_ID') ? (int) SITE_ID : 0;
        if ($curSiteId > 0 && $rowSiteId === $curSiteId && $curHost !== '') {
            return $proto . $curHost;
        }

        $cleanHost = static function ($raw): string {
            $s = trim((string) $raw);
            $s = preg_replace('#^https?://#i', '', $s);
            $s = rtrim($s, '/');
            if (strpos($s, '/') !== false) {
                $s = explode('/', $s, 2)[0];
            }
            return strtolower(trim($s));
        };

        $candDomain = $cleanHost($siteRow['site_domain'] ?? '');
        $candUrl = $cleanHost($siteRow['site_url'] ?? '');

        $preferLocal = (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) || iswIsLocalHost($curHost);

        $chosen = '';
        if ($preferLocal) {
            if ($candDomain !== '') {
                $chosen = $candDomain;
            } elseif ($candUrl !== '') {
                $chosen = $candUrl;
            }
            if ($chosen !== '' && $curPort !== '' && iswIsLocalHost($chosen) && strpos($chosen, ':') === false) {
                $chosen .= ':' . $curPort;
            }
        } else {
            if ($candDomain !== '' && !iswIsLocalHost($candDomain)) {
                $chosen = $candDomain;
            } elseif ($candUrl !== '') {
                $chosen = $candUrl;
            } elseif ($candDomain !== '') {
                $chosen = $candDomain;
            }
        }

        if ($chosen === '') {
            return '';
        }

        return $proto . $chosen;
    }
}

if (!function_exists('iswGetParentGroupInfo')) {
    function iswGetParentGroupInfo($connection, int $siteId, bool $forceRefresh = false): array
    {
        static $cache = [];

        if (!$forceRefresh && isset($cache[$siteId])) {
            return $cache[$siteId];
        }

        $result = [
            'site_id'        => $siteId,
            'parent_site_id' => $siteId,
            'site_ids'       => $siteId > 0 ? [$siteId] : [],
            'sites'          => [],
            'siblings'       => [],
        ];

        if (!($connection instanceof mysqli) || $siteId <= 0) {
            $cache[$siteId] = $result;
            return $result;
        }

        $p = iswTablePrefix();
        $parentSiteId = $siteId;

        // 1. Resolve active installation parent for current site
        $instSql = "SELECT site_installation_site_id, site_installation_parent_site_id, site_installation_status
                    FROM {$p}site_installation
                    WHERE site_installation_site_id = {$siteId}
                    ORDER BY (site_installation_status = 1) DESC, site_installation_id DESC
                    LIMIT 1";
        if ($instRes = @mysqli_query($connection, $instSql)) {
            if ($instRow = mysqli_fetch_assoc($instRes)) {
                if ((int) $instRow['site_installation_status'] === 1 && (int) $instRow['site_installation_parent_site_id'] > 0) {
                    $parentSiteId = (int) $instRow['site_installation_parent_site_id'];
                }
            }
        }

        $result['parent_site_id'] = $parentSiteId;

        // 2. Fetch all active, unexpired sites in the parent group
        $sitesSql = "SELECT
                        s.site_id, s.site_owner_id, s.site_domain, s.site_url, s.site_name,
                        s.site_icon, s.site_status, s.site_app_id, s.site_dashboard_version,
                        a.app_path AS site_app,
                        si.site_installation_parent_site_id, si.site_installation_status, si.site_installation_expiry_date
                     FROM {$p}site s
                     INNER JOIN {$p}app a ON a.app_id = s.site_app_id
                     INNER JOIN {$p}site_installation si ON si.site_installation_site_id = s.site_id
                     WHERE s.site_status = 1
                       AND si.site_installation_status = 1
                       AND (si.site_installation_expiry_date IS NULL OR si.site_installation_expiry_date = '0000-00-00' OR si.site_installation_expiry_date > CURDATE())
                       AND (si.site_installation_parent_site_id = {$parentSiteId} OR s.site_id = {$parentSiteId})
                     ORDER BY (s.site_id = {$parentSiteId}) DESC, s.site_id ASC";

        $sitesById = [];
        if ($sitesRes = @mysqli_query($connection, $sitesSql)) {
            while ($row = mysqli_fetch_assoc($sitesRes)) {
                $sid = (int) $row['site_id'];
                if (isset($sitesById[$sid])) {
                    continue;
                }
                // Guard against stale parent mismatch when site has its own active parent row
                $rowParent = (int) $row['site_installation_parent_site_id'];
                if ($rowParent !== $parentSiteId && $sid !== $parentSiteId) {
                    continue;
                }
                $row['site_id'] = $sid;
                $row['origin'] = iswResolveSiteOrigin($row);
                $sitesById[$sid] = $row;
            }
        }

        // Ensure current site metadata exists even if installation row was inactive/custom
        if (!isset($sitesById[$siteId])) {
            $curSql = "SELECT
                            s.site_id, s.site_owner_id, s.site_domain, s.site_url, s.site_name,
                            s.site_icon, s.site_status, s.site_app_id, s.site_dashboard_version,
                            a.app_path AS site_app
                       FROM {$p}site s
                       INNER JOIN {$p}app a ON a.app_id = s.site_app_id
                       WHERE s.site_id = {$siteId}
                       LIMIT 1";
            if ($curRes = @mysqli_query($connection, $curSql)) {
                if ($curRow = mysqli_fetch_assoc($curRes)) {
                    $curRow['site_id'] = (int) $curRow['site_id'];
                    $curRow['origin'] = iswResolveSiteOrigin($curRow);
                    $sitesById[$siteId] = $curRow;
                }
            }
        }

        // Legacy compatibility: sites 49 & 234 always share group if one was queried without parent link
        $siteIds = array_keys($sitesById);
        if (!in_array($siteId, $siteIds, true)) {
            array_unshift($siteIds, $siteId);
        }

        $siblings = [];
        foreach ($sitesById as $sid => $sRow) {
            if ((int) $sid !== $siteId) {
                $siblings[$sid] = $sRow;
            }
        }

        $result['site_ids'] = array_values(array_unique(array_map('intval', $siteIds)));
        $result['sites']    = $sitesById;
        $result['siblings'] = $siblings;

        $cache[$siteId] = $result;
        return $result;
    }
}

if (!function_exists('iswResolveSiteAccessForUser')) {
    function iswResolveSiteAccessForUser($connection, int $userId, int $targetSiteId): ?array
    {
        if (!($connection instanceof mysqli) || $userId <= 0 || $targetSiteId <= 0) {
            return null;
        }

        $p = iswTablePrefix();

        // 1. Target site metadata (must always reflect targetSiteId, never a sibling)
        $siteSql = "SELECT
                        s.site_id, s.site_domain, s.site_url, s.site_name, s.site_icon,
                        s.site_status, s.site_app_id, s.site_dashboard_version,
                        a.app_path AS site_app
                    FROM {$p}site s
                    INNER JOIN {$p}app a ON a.app_id = s.site_app_id
                    WHERE s.site_id = {$targetSiteId}
                    LIMIT 1";
        $siteRes = @mysqli_query($connection, $siteSql);
        if (!$siteRes || mysqli_num_rows($siteRes) === 0) {
            return null;
        }
        $siteRow = mysqli_fetch_assoc($siteRes);

        // 2. User + profile + company/vendor
        $userSql = "SELECT
                        u.user_id, u.user_username, u.user_email, u.user_mobile, u.user_salt_key, u.user_status,
                        p.profile_display_name AS logginUserName, p.profile_first_name, p.profile_last_name, p.profile_image,
                        c.company_id, v.vendor_id
                    FROM {$p}user u
                    LEFT JOIN {$p}profile p ON p.profile_user_id = u.user_id
                    LEFT JOIN {$p}company c ON c.company_user_id = u.user_id
                    LEFT JOIN {$p}vendor v ON v.vendor_user_id = u.user_id
                    WHERE u.user_id = {$userId}
                    LIMIT 1";
        $userRes = @mysqli_query($connection, $userSql);
        if (!$userRes || mysqli_num_rows($userRes) === 0) {
            return null;
        }
        $userRow = mysqli_fetch_assoc($userRes);

        // 3. Explicit site_access on targetSiteId first
        $directSql = "SELECT site_access_id, site_access_site_id, site_access_type, site_access_status, site_access_meta
                      FROM {$p}site_access
                      WHERE site_access_user_id = {$userId}
                        AND site_access_site_id = {$targetSiteId}
                      ORDER BY site_access_id DESC
                      LIMIT 1";
        $directRes = @mysqli_query($connection, $directSql);
        if ($directRes && mysqli_num_rows($directRes) > 0) {
            $accessRow = mysqli_fetch_assoc($directRes);
            if ((int) $accessRow['site_access_status'] !== 1) {
                return [
                    'has_access' => false,
                    'reason'     => 'disabled',
                    'user'       => $userRow,
                    'site'       => $siteRow,
                    'access'     => $accessRow,
                ];
            }
            return [
                'has_access' => true,
                'reason'     => 'ok',
                'user'       => $userRow,
                'site'       => $siteRow,
                'access'     => $accessRow,
            ];
        }

        // 4. Legacy shared-subscriber exception ONLY for site 49 <-> 234 when no direct row exists
        if (in_array($targetSiteId, [49, 234], true)) {
            $legacySql = "SELECT site_access_id, site_access_site_id, site_access_type, site_access_status, site_access_meta
                          FROM {$p}site_access
                          WHERE site_access_user_id = {$userId}
                            AND site_access_site_id IN (49, 234)
                            AND site_access_status = 1
                          ORDER BY (site_access_site_id = {$targetSiteId}) DESC, site_access_id DESC
                          LIMIT 1";
            $legacyRes = @mysqli_query($connection, $legacySql);
            if ($legacyRes && mysqli_num_rows($legacyRes) > 0) {
                $legacyRow = mysqli_fetch_assoc($legacyRes);
                return [
                    'has_access' => true,
                    'reason'     => 'ok',
                    'user'       => $userRow,
                    'site'       => $siteRow,
                    'access'     => $legacyRow,
                ];
            }
        }

        return [
            'has_access' => false,
            'reason'     => 'no_access',
            'user'       => $userRow,
            'site'       => $siteRow,
            'access'     => null,
        ];
    }
}

if (!function_exists('iswFindUserAndSiteAccessByIdentifier')) {
    /**
     * Resolve candidate user(s) by email/mobile/username preferring active `site_access`
     * on $targetSiteId (plus legacy 49<->234 fallback inside iswResolveSiteAccessForUser).
     *
     * @param string $mode 'mobile' | 'email' | 'email_or_mobile' | 'any'
     * @return array{found:bool, resolved:?array, reason:string}
     */
    function iswFindUserAndSiteAccessByIdentifier($connection, string $identifier, int $targetSiteId, string $mode = 'any'): array
    {
        $raw = trim($identifier);
        if (!($connection instanceof mysqli) || $raw === '' || $targetSiteId <= 0) {
            return ['found' => false, 'resolved' => null, 'reason' => 'invalid'];
        }

        $p = iswTablePrefix();
        $escRaw = mysqli_real_escape_string($connection, $raw);

        $digits = preg_replace('/\D/', '', $raw);
        $cleanMobile = $digits;
        if (strlen($cleanMobile) === 12 && substr($cleanMobile, 0, 2) === '91') {
            $cleanMobile = substr($cleanMobile, 2);
        } elseif (strlen($cleanMobile) === 13 && substr($cleanMobile, 0, 3) === '091') {
            $cleanMobile = substr($cleanMobile, 3);
        }
        $escCleanMobile = mysqli_real_escape_string($connection, $cleanMobile);

        switch ($mode) {
            case 'mobile':
                $where = ($cleanMobile !== '' && $cleanMobile !== $raw)
                    ? "(u.user_mobile = '{$escRaw}' OR u.user_mobile = '{$escCleanMobile}')"
                    : "u.user_mobile = '{$escRaw}'";
                break;

            case 'email':
                $where = "u.user_email = '{$escRaw}'";
                break;

            case 'email_or_mobile':
                $mobileClause = ($cleanMobile !== '' && $cleanMobile !== $raw)
                    ? "u.user_mobile = '{$escRaw}' OR u.user_mobile = '{$escCleanMobile}'"
                    : "u.user_mobile = '{$escRaw}'";
                $where = "(u.user_email = '{$escRaw}' OR {$mobileClause})";
                break;

            default:
                $mobileClause = ($cleanMobile !== '' && $cleanMobile !== $raw)
                    ? "u.user_mobile = '{$escRaw}' OR u.user_mobile = '{$escCleanMobile}'"
                    : "u.user_mobile = '{$escRaw}'";
                $where = "(u.user_email = '{$escRaw}' OR {$mobileClause} OR u.user_username = '{$escRaw}')";
                break;
        }

        $saSiteFilter = in_array($targetSiteId, [49, 234], true)
            ? "sa.site_access_site_id IN (49, 234)"
            : "sa.site_access_site_id = {$targetSiteId}";

        $sql = "SELECT u.user_id
                FROM {$p}user u
                LEFT JOIN {$p}site_access sa
                       ON sa.site_access_user_id = u.user_id
                      AND {$saSiteFilter}
                WHERE {$where}
                ORDER BY (sa.site_access_status = 1) DESC,
                         (sa.site_access_site_id = {$targetSiteId}) DESC,
                         (sa.site_access_id IS NOT NULL) DESC,
                         (u.user_status = 1) DESC,
                         u.user_id ASC
                LIMIT 10";

        $res = @mysqli_query($connection, $sql);
        if (!$res || mysqli_num_rows($res) === 0) {
            return ['found' => false, 'resolved' => null, 'reason' => 'not_found'];
        }

        $seen = [];
        $hasAccessCandidate = null;
        $disabledCandidate = null;
        $firstCandidate = null;

        while ($row = mysqli_fetch_assoc($res)) {
            $uid = (int) ($row['user_id'] ?? 0);
            if ($uid <= 0 || isset($seen[$uid])) {
                continue;
            }
            $seen[$uid] = true;

            $cand = iswResolveSiteAccessForUser($connection, $uid, $targetSiteId);
            if (!$cand) {
                continue;
            }
            if ($firstCandidate === null) {
                $firstCandidate = $cand;
            }

            if (!empty($cand['has_access'])) {
                $uStatus = (int) ($cand['user']['user_status'] ?? 0);
                $appId   = (int) ($cand['site']['site_app_id'] ?? 0);
                if ($uStatus === 1 || $appId === 3) {
                    return ['found' => true, 'resolved' => $cand, 'reason' => 'ok'];
                }
                if ($hasAccessCandidate === null) {
                    $hasAccessCandidate = $cand;
                }
            } elseif (($cand['reason'] ?? '') === 'disabled' && $disabledCandidate === null) {
                $disabledCandidate = $cand;
            }
        }

        if ($hasAccessCandidate !== null) {
            return ['found' => true, 'resolved' => $hasAccessCandidate, 'reason' => 'inactive_user'];
        }
        if ($disabledCandidate !== null) {
            return ['found' => true, 'resolved' => $disabledCandidate, 'reason' => 'disabled'];
        }
        if ($firstCandidate !== null) {
            return ['found' => true, 'resolved' => $firstCandidate, 'reason' => 'no_access'];
        }

        return ['found' => false, 'resolved' => null, 'reason' => 'not_found'];
    }
}

if (!function_exists('iswEnsureSingleSiteSession')) {
    function iswEnsureSingleSiteSession($connection, int $userId, string $deviceId, int $siteId, int $analyticsId = 0, bool $isSource = false): bool
    {
        if (!($connection instanceof mysqli) || $userId <= 0 || $deviceId === '' || $siteId <= 0) {
            return false;
        }

        $p = iswTablePrefix();
        $escDevice = mysqli_real_escape_string($connection, $deviceId);

        $checkSql = "SELECT us_id, us_status FROM {$p}user_sessions
                     WHERE us_user_id = {$userId}
                       AND us_active_device = '{$escDevice}'
                       AND us_site_id = {$siteId}
                     ORDER BY us_id DESC
                     LIMIT 1";
        $res = @mysqli_query($connection, $checkSql);
        if ($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $usId = (int) $row['us_id'];
            if ($isSource) {
                return (bool) @mysqli_query($connection, "UPDATE {$p}user_sessions SET us_status = 1, us_active_time = NOW() WHERE us_id = {$usId}");
            }
            if ((int) $row['us_status'] !== 1) {
                return (bool) @mysqli_query($connection, "UPDATE {$p}user_sessions SET us_status = 1 WHERE us_id = {$usId}");
            }
            return true;
        }

        $activeTimeExpr = $isSource ? 'NOW()' : 'DATE_SUB(NOW(), INTERVAL 1 SECOND)';
        $insSql = "INSERT INTO {$p}user_sessions
                   (us_site_id, us_user_id, us_analytics_id, us_active_device, us_status, us_active_time)
                   VALUES ({$siteId}, {$userId}, {$analyticsId}, '{$escDevice}', 1, {$activeTimeExpr})";
        return (bool) @mysqli_query($connection, $insSql);
    }
}

if (!function_exists('iswSyncGroupUserSessions')) {
    function iswSyncGroupUserSessions($connection, int $userId, string $deviceId, int $sourceSiteId, int $analyticsId = 0): bool
    {
        if (!($connection instanceof mysqli) || $userId <= 0 || trim($deviceId) === '' || $sourceSiteId <= 0) {
            return false;
        }

        $groupInfo = iswGetParentGroupInfo($connection, $sourceSiteId);
        $siteIds = !empty($groupInfo['site_ids']) ? $groupInfo['site_ids'] : [$sourceSiteId];
        if (!in_array($sourceSiteId, $siteIds, true)) {
            array_unshift($siteIds, $sourceSiteId);
        }

        $syncedAny = false;
        foreach ($siteIds as $sid) {
            $sid = (int) $sid;
            if ($sid <= 0) {
                continue;
            }
            if ($sid === $sourceSiteId) {
                if (iswEnsureSingleSiteSession($connection, $userId, $deviceId, $sid, $analyticsId, true)) {
                    $syncedAny = true;
                }
                continue;
            }
            $access = iswResolveSiteAccessForUser($connection, $userId, $sid);
            if ($access && !empty($access['has_access'])) {
                if (iswEnsureSingleSiteSession($connection, $userId, $deviceId, $sid, $analyticsId, false)) {
                    $syncedAny = true;
                }
            }
        }

        return $syncedAny;
    }
}

if (!function_exists('iswRevokeGroupUserSessions')) {
    function iswRevokeGroupUserSessions($connection, int $userId, string $deviceId, int $siteId): bool
    {
        if (!($connection instanceof mysqli) || $userId <= 0 || trim($deviceId) === '' || $siteId <= 0) {
            return false;
        }

        $p = iswTablePrefix();
        $groupInfo = iswGetParentGroupInfo($connection, $siteId);
        $siteIds = !empty($groupInfo['site_ids']) ? $groupInfo['site_ids'] : [$siteId];
        if (in_array($siteId, [49, 234], true)) {
            $siteIds = array_unique(array_merge($siteIds, [49, 234]));
        }
        $idsSql = implode(',', array_map('intval', $siteIds));
        $escDevice = mysqli_real_escape_string($connection, trim($deviceId));

        if (iswSsoEnsureSchema($connection)) {
            @mysqli_query(
                $connection,
                "DELETE FROM {$p}sso_tokens
                 WHERE sso_user_id = {$userId}
                   AND sso_device_id = '{$escDevice}'"
            );
        }

        return (bool) @mysqli_query(
            $connection,
            "DELETE FROM {$p}user_sessions
             WHERE us_user_id = {$userId}
               AND us_active_device = '{$escDevice}'
               AND us_site_id IN ({$idsSql})"
        );
    }
}

if (!function_exists('iswValidateCurrentSiteSession')) {
    function iswValidateCurrentSiteSession($connection, int $siteId): array
    {
        if (!($connection instanceof mysqli) || $siteId <= 0) {
            return ['ok' => false, 'reason' => 'invalid_context'];
        }

        $rawUserData = isset($_COOKIE['user_data']) ? trim((string) $_COOKIE['user_data']) : '';
        if ($rawUserData === '') {
            return ['ok' => false, 'reason' => 'missing_cookies'];
        }

        $decodedSalt = base64_decode($rawUserData, true);
        if ($decodedSalt === false || $decodedSalt === '') {
            $decodedSalt = base64_decode($rawUserData);
        }
        $decodedSalt = trim((string) $decodedSalt);
        if ($decodedSalt === '') {
            return ['ok' => false, 'reason' => 'missing_cookies'];
        }

        $p = iswTablePrefix();
        $escSalt = mysqli_real_escape_string($connection, $decodedSalt);

        $userRes = @mysqli_query(
            $connection,
            "SELECT user_id, user_email, user_status FROM {$p}user WHERE user_salt_key = '{$escSalt}' LIMIT 1"
        );
        if (!$userRes || mysqli_num_rows($userRes) === 0) {
            return ['ok' => false, 'reason' => 'invalid_user'];
        }
        $uRow = mysqli_fetch_assoc($userRes);
        $userId = (int) $uRow['user_id'];

        $deviceId = isset($_COOKIE['device_id']) ? trim((string) $_COOKIE['device_id']) : '';
        if ($deviceId === '') {
            return ['ok' => false, 'reason' => 'missing_device', 'user_id' => $userId];
        }

        $groupInfo = iswGetParentGroupInfo($connection, $siteId);
        $siteIds = !empty($groupInfo['site_ids']) ? $groupInfo['site_ids'] : [$siteId];
        if (in_array($siteId, [49, 234], true)) {
            $siteIds = array_unique(array_merge($siteIds, [49, 234]));
        }
        $idsSql = implode(',', array_map('intval', $siteIds));
        $escDevice = mysqli_real_escape_string($connection, $deviceId);

        $sessSql = "SELECT us_id, us_site_id, us_status
                    FROM {$p}user_sessions
                    WHERE us_user_id = {$userId}
                      AND us_active_device = '{$escDevice}'
                      AND us_status = 1
                      AND us_site_id IN ({$idsSql})
                    ORDER BY (us_site_id = {$siteId}) DESC, us_id DESC
                    LIMIT 1";
        $sessRes = @mysqli_query($connection, $sessSql);
        if (!$sessRes || mysqli_num_rows($sessRes) === 0) {
            return ['ok' => false, 'reason' => 'invalid_session', 'user_id' => $userId];
        }
        $sessRow = mysqli_fetch_assoc($sessRes);

        $resolved = iswResolveSiteAccessForUser($connection, $userId, $siteId);
        if (!$resolved) {
            return ['ok' => false, 'reason' => 'invalid_user', 'user_id' => $userId];
        }

        $userStatus = (int) ($resolved['user']['user_status'] ?? 0);
        $siteAppId = (int) ($resolved['site']['site_app_id'] ?? 0);
        if ($userStatus !== 1 && $siteAppId !== 3) {
            return [
                'ok'     => false,
                'reason' => 'unverified_user',
                'user'   => $resolved['user'],
                'site'   => $resolved['site'],
                'access' => $resolved['access'],
            ];
        }

        if (empty($resolved['has_access'])) {
            return [
                'ok'     => false,
                'reason' => $resolved['reason'],
                'user'   => $resolved['user'],
                'site'   => $resolved['site'],
                'access' => $resolved['access'],
            ];
        }

        // Backfill active session on current site if authenticated via sibling session
        if ((int) $sessRow['us_site_id'] !== $siteId) {
            iswEnsureSingleSiteSession($connection, $userId, $deviceId, $siteId, 0, true);
        }

        return [
            'ok'        => true,
            'reason'    => 'ok',
            'user_id'   => $userId,
            'device_id' => $deviceId,
            'user'      => $resolved['user'],
            'site'      => $resolved['site'],
            'access'    => $resolved['access'],
            'group'     => $groupInfo,
        ];
    }
}

if (!function_exists('iswSetSiteAuthCookies')) {
    function iswSetSiteAuthCookies(array $resolvedPayload, string $deviceId, int $expireDays = 30): void
    {
        $user   = $resolvedPayload['user'] ?? [];
        $site   = $resolvedPayload['site'] ?? [];
        $access = $resolvedPayload['access'] ?? [];

        $userId      = (int) ($user['user_id'] ?? 0);
        $userSalt    = (string) ($user['user_salt_key'] ?? '');
        $userEmail   = trim((string) ($user['user_email'] ?? ''));
        $userMobile  = trim((string) ($user['user_mobile'] ?? ''));
        $userAvatar  = trim((string) ($user['profile_image'] ?? ''));
        $displayName = trim((string) ($user['logginUserName'] ?? ($user['profile_display_name'] ?? '')));
        if ($displayName === '') {
            $displayName = trim(((string) ($user['profile_first_name'] ?? '')) . ' ' . ((string) ($user['profile_last_name'] ?? '')));
        }
        if ($displayName === '') {
            $displayName = trim((string) ($user['user_username'] ?? 'Team Member'));
        }

        $userRole = strtolower(trim((string) ($access['site_access_type'] ?? 'visitor')));
        $userMeta = (string) ($access['site_access_meta'] ?? '');
        $siteId   = (int) ($site['site_id'] ?? (defined('SITE_ID') ? SITE_ID : 0));
        $siteApp  = trim((string) ($site['site_app'] ?? (defined('SITE_APP') ? SITE_APP : 'system')));

        $expiry = time() + ($expireDays * 24 * 60 * 60);
        $past   = time() - 3600;
        $secOpts = iswBuildCookieOptions($expiry, true);
        $uiOpts  = iswBuildCookieOptions($expiry, false);

        $encodedValues = [
            'user_data'    => base64_encode($userSalt),
            'device_id'    => $deviceId,
            'user_name'    => base64_encode($displayName),
            'user_email'   => base64_encode($userEmail),
            'user_mobile'  => base64_encode($userMobile),
            'user_image'   => base64_encode($userAvatar),
            'user_type'    => base64_encode($userRole),
            'user_meta'    => base64_encode($userMeta),
            'site_app'     => base64_encode($siteApp),
            'user_site_id' => base64_encode((string) $siteId),
            'isLogin'      => '1',
        ];

        if (!headers_sent()) {
            // Expire any stale host-only auth cookies when using domain-scoped cookies so they cannot shadow updated values
            if (!empty($uiOpts['domain'])) {
                foreach ($encodedValues as $cName => $unusedVal) {
                    $cHttpOnly = in_array($cName, ['device_id', 'user_data'], true);
                    @setcookie($cName, '', iswBuildCookieOptions($past, $cHttpOnly, ''));
                }
            }

            setcookie('device_id', $encodedValues['device_id'], $secOpts);
            setcookie('user_data', $encodedValues['user_data'], $secOpts);
            setcookie('user_name', $encodedValues['user_name'], $uiOpts);
            setcookie('user_email', $encodedValues['user_email'], $uiOpts);
            setcookie('user_mobile', $encodedValues['user_mobile'], $uiOpts);
            setcookie('user_image', $encodedValues['user_image'], $uiOpts);
            setcookie('user_type', $encodedValues['user_type'], $uiOpts);
            setcookie('user_meta', $encodedValues['user_meta'], $uiOpts);
            setcookie('site_app', $encodedValues['site_app'], $uiOpts);
            setcookie('user_site_id', $encodedValues['user_site_id'], $uiOpts);
            setcookie('isLogin', $encodedValues['isLogin'], $uiOpts);
        }

        foreach ($encodedValues as $k => $v) {
            $_COOKIE[$k] = $v;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['user_id']      = $userId;
            $_SESSION['site_id']      = $siteId;
            $_SESSION['user_site_id'] = $siteId;
            $_SESSION['user_role']    = $userRole;
            $_SESSION['user_type']    = $userRole;
            $_SESSION['display_name'] = $displayName;
        }
    }
}

if (!function_exists('iswClearAllAuthCookies')) {
    function iswClearAllAuthCookies(bool $includeExtraCookies = false): void
    {
        $authKeys = [
            'user_data',
            'device_id',
            'isLogin',
            'user_name',
            'user_email',
            'user_mobile',
            'user_image',
            'user_type',
            'user_meta',
            'site_app',
            'user_site_id',
        ];

        if ($includeExtraCookies && is_array($_COOKIE)) {
            foreach (array_keys($_COOKIE) as $cName) {
                if ($cName !== 'PHPSESSID' && !in_array($cName, $authKeys, true)) {
                    $authKeys[] = (string) $cName;
                }
            }
        }

        $domains = [''];
        $cookieDomain = iswGetCookieDomain();
        if ($cookieDomain !== '') {
            $domains[] = $cookieDomain;
        }
        $rawCandidates = [
            defined('MAIN_DOMAIN') ? iswStripPort(MAIN_DOMAIN) : '',
            defined('SITE_DOMAIN') ? iswStripPort(SITE_DOMAIN) : '',
            !empty($_SERVER['HTTP_HOST']) ? iswStripPort($_SERVER['HTTP_HOST']) : '',
            !empty($_SERVER['SERVER_NAME']) ? iswStripPort($_SERVER['SERVER_NAME']) : '',
        ];
        foreach ($rawCandidates as $cand) {
            if ($cand === '' || $cand === 'localhost' || filter_var($cand, FILTER_VALIDATE_IP)) {
                continue;
            }
            $domains[] = $cand;
            $domains[] = '.' . ltrim($cand, '.');
        }
        $domains = array_values(array_unique($domains));

        $past = time() - 3600;
        if (!headers_sent()) {
            foreach ($authKeys as $name) {
                $httpOnly = in_array($name, ['device_id', 'user_data'], true);
                foreach ($domains as $dom) {
                    @setcookie($name, '', iswBuildCookieOptions($past, $httpOnly, $dom));
                }
            }
        }

        foreach ($authKeys as $name) {
            unset($_COOKIE[$name]);
        }
    }
}

if (!function_exists('iswGetSiblingSitesWithActiveSessions')) {
    function iswGetSiblingSitesWithActiveSessions($connection, int $siteId): array
    {
        if (!($connection instanceof mysqli) || $siteId <= 0) {
            return [];
        }

        $groupInfo = iswGetParentGroupInfo($connection, $siteId);
        if (empty($groupInfo['siblings'])) {
            return [];
        }

        $currentSiteRow = $groupInfo['sites'][$siteId] ?? ['site_id' => $siteId];
        $currentOrigin = iswResolveSiteOrigin($currentSiteRow);

        $candidatesById = [];
        foreach ($groupInfo['siblings'] as $sid => $sRow) {
            $origin = (string) ($sRow['origin'] ?? '');
            if ($origin === '' || $origin === $currentOrigin) {
                continue;
            }
            $candidatesById[(int) $sid] = $sRow;
        }

        if (empty($candidatesById)) {
            return [];
        }

        $p = iswTablePrefix();
        $idsSql = implode(',', array_map('intval', array_keys($candidatesById)));
        $sql = "SELECT us_site_id, MAX(us_active_time) AS last_active, MAX(us_id) AS max_id
                FROM {$p}user_sessions
                WHERE us_status = 1 AND us_site_id IN ({$idsSql})
                GROUP BY us_site_id
                ORDER BY last_active DESC, max_id DESC";

        $ordered = [];
        if ($res = @mysqli_query($connection, $sql)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $sid = (int) $row['us_site_id'];
                if (isset($candidatesById[$sid])) {
                    $ordered[] = $candidatesById[$sid];
                }
            }
        }

        return $ordered;
    }
}

if (!function_exists('iswSignSsoParams')) {
    function iswSignSsoParams(array $params): string
    {
        unset($params['sig']);
        ksort($params);
        $canonical = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return hash_hmac('sha256', $canonical, iswGetSsoSecret());
    }
}

if (!function_exists('iswVerifySsoSignature')) {
    function iswVerifySsoSignature(array $params, int $maxAgeSeconds = 120): bool
    {
        $sig = isset($params['sig']) ? (string) $params['sig'] : '';
        $ts  = isset($params['ts']) ? (int) $params['ts'] : 0;
        if ($sig === '' || $ts <= 0) {
            return false;
        }
        if (abs(time() - $ts) > $maxAgeSeconds) {
            return false;
        }
        $expected = iswSignSsoParams($params);
        return hash_equals($expected, $sig);
    }
}

if (!function_exists('iswBuildSsoIssueUrl')) {
    function iswBuildSsoIssueUrl(string $issuerOrigin, int $targetSiteId, string $returnPath, array $remainingSiteIds = []): string
    {
        $params = [
            'target'    => (string) $targetSiteId,
            'return'    => $returnPath,
            'remaining' => implode(',', array_map('intval', $remainingSiteIds)),
            'ts'        => (string) time(),
        ];
        $params['sig'] = iswSignSsoParams($params);
        return rtrim($issuerOrigin, '/') . '/sso/issue/?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}

if (!function_exists('iswCreateSsoToken')) {
    function iswCreateSsoToken($connection, int $userId, int $sourceSiteId, int $targetSiteId, int $parentSiteId, string $deviceId, int $ttlSeconds = 60): string
    {
        if (!($connection instanceof mysqli) || $userId <= 0 || $sourceSiteId <= 0 || $targetSiteId <= 0 || trim($deviceId) === '') {
            return '';
        }

        if (!iswSsoEnsureSchema($connection)) {
            return '';
        }

        try {
            $token = bin2hex(random_bytes(32));
        } catch (\Throwable $e) {
            $token = hash('sha256', uniqid((string) mt_rand(), true) . $userId . microtime(true));
        }

        $p = iswTablePrefix();
        $escToken  = mysqli_real_escape_string($connection, $token);
        $escDevice = mysqli_real_escape_string($connection, trim($deviceId));
        $ttlSeconds = max(15, min(300, $ttlSeconds));

        $sql = "INSERT INTO `{$p}sso_tokens`
                (`sso_token`, `sso_user_id`, `sso_source_site_id`, `sso_target_site_id`, `sso_parent_site_id`, `sso_device_id`, `sso_expires_at`)
                VALUES
                ('{$escToken}', {$userId}, {$sourceSiteId}, {$targetSiteId}, {$parentSiteId}, '{$escDevice}', DATE_ADD(NOW(), INTERVAL {$ttlSeconds} SECOND))";

        if (@mysqli_query($connection, $sql)) {
            return $token;
        }

        return '';
    }
}

if (!function_exists('iswConsumeSsoToken')) {
    function iswConsumeSsoToken($connection, string $rawToken, int $expectedTargetSiteId): ?array
    {
        if (!($connection instanceof mysqli) || $expectedTargetSiteId <= 0) {
            return null;
        }

        $token = trim($rawToken);
        if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
            return null;
        }

        if (!iswSsoEnsureSchema($connection)) {
            return null;
        }

        $p = iswTablePrefix();
        $escToken = mysqli_real_escape_string($connection, $token);

        $updSql = "UPDATE `{$p}sso_tokens`
                   SET `sso_used_at` = NOW()
                   WHERE `sso_token` = '{$escToken}'
                     AND `sso_target_site_id` = {$expectedTargetSiteId}
                     AND `sso_used_at` IS NULL
                     AND `sso_expires_at` >= NOW()
                   LIMIT 1";
        if (!@mysqli_query($connection, $updSql) || mysqli_affected_rows($connection) !== 1) {
            return null;
        }

        $selSql = "SELECT * FROM `{$p}sso_tokens` WHERE `sso_token` = '{$escToken}' LIMIT 1";
        $res = @mysqli_query($connection, $selSql);
        if (!$res || mysqli_num_rows($res) === 0) {
            return null;
        }

        return mysqli_fetch_assoc($res);
    }
}
