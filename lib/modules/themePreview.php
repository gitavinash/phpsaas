<?php

/**
 * Isolated Website Theme Preview Engine
 *
 * Allows privileged users (or holders of a time-limited HMAC-signed preview link)
 * to preview any valid theme for the active tenant site's current application
 * without modifying `isw_site.site_theme_id` for live visitors.
 */

if (!defined('EMAIL_VERIFY_SECRET') || !defined('ENCRYPTION_AUTH_KEY')) {
    $iswSecureCfg = dirname(__DIR__) . '/configuration/secure.php';
    if (file_exists($iswSecureCfg)) {
        require_once $iswSecureCfg;
    }
}

if (!defined('ISW_THEME_PREVIEW_COOKIE')) {
    define('ISW_THEME_PREVIEW_COOKIE', 'isw_theme_preview');
}

if (!defined('ISW_THEME_PREVIEW_TTL')) {
    define('ISW_THEME_PREVIEW_TTL', 4 * 3600);
}

if (!function_exists('iswThemePreviewSecret')) {
    function iswThemePreviewSecret(): string
    {
        $k1 = defined('EMAIL_VERIFY_SECRET') ? (string) EMAIL_VERIFY_SECRET : '';
        $k2 = defined('ENCRYPTION_AUTH_KEY') ? (string) ENCRYPTION_AUTH_KEY : '';
        return hash('sha256', $k1 . '|' . $k2 . '|isw-theme-preview-v1');
    }
}

if (!function_exists('iswThemePreviewRootDir')) {
    function iswThemePreviewRootDir(): string
    {
        if (defined('ROOT') && ROOT !== '') {
            return rtrim((string) ROOT, '/');
        }
        return dirname(__DIR__, 2);
    }
}

if (!function_exists('iswThemePreviewIsHttps')) {
    function iswThemePreviewIsHttps(): bool
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

if (!function_exists('iswCheckThemeTemplateAvailability')) {
    function iswCheckThemeTemplateAvailability(string $siteAppPath, string $themePath): array
    {
        $cleanApp = trim($siteAppPath, "/ \t\r\n");
        $cleanTheme = trim($themePath, "/ \t\r\n");

        if (
            $cleanApp === ''
            || $cleanTheme === ''
            || strpos($cleanApp, '..') !== false
            || strpos($cleanTheme, '..') !== false
            || !preg_match('/^[A-Za-z0-9_-]+$/', $cleanApp)
            || !preg_match('/^[A-Za-z0-9_-]+$/', $cleanTheme)
        ) {
            return [
                'template_exists' => false,
                'has_desktop'     => false,
                'has_mobile'      => false,
            ];
        }

        $root = iswThemePreviewRootDir();
        $desktopIndex = $root . '/app/' . $cleanApp . '/visitor/' . $cleanTheme . '/desktop/index.php';
        $mobileIndex = $root . '/app/' . $cleanApp . '/visitor/' . $cleanTheme . '/mobile/index.php';

        $hasDesktop = is_file($desktopIndex);
        $hasMobile = is_file($mobileIndex);

        return [
            'template_exists' => ($hasDesktop || $hasMobile),
            'has_desktop'     => $hasDesktop,
            'has_mobile'      => $hasMobile,
        ];
    }
}

if (!function_exists('iswSignThemePreviewToken')) {
    function iswSignThemePreviewToken(int $siteId, int $themeId, int $expiresAt): string
    {
        $payload = $siteId . '|' . $themeId . '|' . $expiresAt;
        return hash_hmac('sha256', $payload, iswThemePreviewSecret());
    }
}

if (!function_exists('iswVerifyThemePreviewSignature')) {
    function iswVerifyThemePreviewSignature(int $siteId, int $themeId, int $expiresAt, string $signature): bool
    {
        if ($siteId <= 0 || $themeId <= 0 || $expiresAt < time() || $signature === '') {
            return false;
        }
        $expected = iswSignThemePreviewToken($siteId, $themeId, $expiresAt);
        return hash_equals($expected, strtolower(trim($signature)));
    }
}

if (!function_exists('iswBuildThemePreviewUrl')) {
    function iswBuildThemePreviewUrl(string $baseUrl, int $siteId, int $themeId, int $ttlSeconds = ISW_THEME_PREVIEW_TTL): string
    {
        $origin = rtrim(trim($baseUrl), '/');
        if ($siteId <= 0 || $themeId <= 0) {
            return $origin !== '' ? $origin . '/' : '/';
        }
        $exp = time() + max(60, $ttlSeconds);
        $sig = iswSignThemePreviewToken($siteId, $themeId, $exp);
        $query = http_build_query([
            '_preview_theme' => $themeId,
            '_preview_exp'   => $exp,
            '_preview_sig'   => $sig,
        ]);
        return ($origin !== '' ? $origin : '') . '/?' . $query;
    }
}

if (!function_exists('iswSetThemePreviewCookie')) {
    function iswSetThemePreviewCookie(int $siteId, int $themeId, int $ttlSeconds = ISW_THEME_PREVIEW_TTL): void
    {
        $exp = time() + max(60, $ttlSeconds);
        $payload = $siteId . ':' . $themeId . ':' . $exp;
        $encoded = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $sig = iswSignThemePreviewToken($siteId, $themeId, $exp);
        $value = $encoded . '.' . $sig;

        $_COOKIE[ISW_THEME_PREVIEW_COOKIE] = $value;
        if (!headers_sent()) {
            setcookie(ISW_THEME_PREVIEW_COOKIE, $value, [
                'expires'  => $exp,
                'path'     => '/',
                'domain'   => '',
                'secure'   => iswThemePreviewIsHttps(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }
}

if (!function_exists('iswClearThemePreviewCookie')) {
    function iswClearThemePreviewCookie(): void
    {
        unset($_COOKIE[ISW_THEME_PREVIEW_COOKIE]);
        if (!headers_sent()) {
            setcookie(ISW_THEME_PREVIEW_COOKIE, '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => iswThemePreviewIsHttps(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }
}

if (!function_exists('iswParseThemePreviewCookie')) {
    function iswParseThemePreviewCookie(string $rawCookie, int $expectedSiteId): ?array
    {
        $raw = trim($rawCookie);
        if ($raw === '' || strpos($raw, '.') === false || $expectedSiteId <= 0) {
            return null;
        }
        $parts = explode('.', $raw, 2);
        if (count($parts) !== 2) {
            return null;
        }
        $encoded = $parts[0];
        $sig = $parts[1];
        $padded = str_pad(strtr($encoded, '-_', '+/'), (int) (ceil(strlen($encoded) / 4) * 4), '=');
        $decoded = base64_decode($padded, true);
        if ($decoded === false || substr_count($decoded, ':') !== 2) {
            return null;
        }
        list($siteStr, $themeStr, $expStr) = explode(':', $decoded, 3);
        if (!ctype_digit($siteStr) || !ctype_digit($themeStr) || !ctype_digit($expStr)) {
            return null;
        }
        $cookieSiteId = (int) $siteStr;
        $themeId = (int) $themeStr;
        $exp = (int) $expStr;

        if ($cookieSiteId !== $expectedSiteId || $themeId <= 0 || $exp < time()) {
            return null;
        }
        if (!iswVerifyThemePreviewSignature($cookieSiteId, $themeId, $exp, $sig)) {
            return null;
        }

        return [
            'site_id'    => $cookieSiteId,
            'theme_id'   => $themeId,
            'expires_at' => $exp,
        ];
    }
}

if (!function_exists('iswIsVisitorPreviewEligibleRoute')) {
    function iswIsVisitorPreviewEligibleRoute(string $baseUrl = ''): bool
    {
        $path = $baseUrl !== '' ? $baseUrl : (defined('BASE_URL') ? (string) BASE_URL : (string) ($_SERVER['REQUEST_URI'] ?? '/'));
        $pathOnly = strtok($path, '?');
        if (!is_string($pathOnly) || $pathOnly === '') {
            $pathOnly = '/';
        }

        $trimmed = trim($pathOnly, '/');
        if ($trimmed === '') {
            return true;
        }

        $segments = explode('/', $trimmed);
        $first = strtolower((string) ($segments[0] ?? ''));

        $excludedRoots = [
            'dashboard',
            'api',
            'oauth',
            'sso',
            'webhook',
            'install',
            'cron',
            'viewmedia',
            'sw.js',
            'firebase-messaging-sw.js',
            'offline.html',
            'status',
            'sitemap',
            'feed',
            'config.min.css',
            'amp',
            'logout',
            'website-setup',
            'onboarding',
            'whatsapp',
        ];
        if (in_array($first, $excludedRoots, true)) {
            return false;
        }

        if (preg_match('/\.(?:css|js|json|xml|txt|ico|svg|png|jpe?g|webp|gif|woff2?|ttf|eot|pdf|map)$/i', $pathOnly)) {
            return false;
        }

        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return false;
        }

        return true;
    }
}

if (!function_exists('iswHasPrivilegedSiteSession')) {
    function iswHasPrivilegedSiteSession($connection, int $siteId): bool
    {
        if (!($connection instanceof mysqli) || $siteId <= 0) {
            return false;
        }
        if (empty($_COOKIE['user_data']) || empty($_COOKIE['device_id'])) {
            return false;
        }

        if (!function_exists('iswValidateCurrentSiteSession')) {
            $ssoFile = __DIR__ . '/sso.php';
            if (is_file($ssoFile)) {
                require_once $ssoFile;
            }
        }
        if (!function_exists('iswValidateCurrentSiteSession')) {
            return false;
        }

        $validation = iswValidateCurrentSiteSession($connection, $siteId);
        if (empty($validation['ok'])) {
            return false;
        }
        $role = strtolower(trim((string) ($validation['access']['site_access_type'] ?? '')));
        return in_array($role, ['administrator', 'admin', 'superadmin', 'operations', 'owner', 'author'], true);
    }
}

if (!function_exists('iswFetchValidPreviewTheme')) {
    function iswFetchValidPreviewTheme($connection, int $themeId, int $siteAppId, string $siteAppPath, int $liveThemeId = 0): ?array
    {
        if (!($connection instanceof mysqli) || $themeId <= 0 || $siteAppId <= 0) {
            return null;
        }
        $tblPrefix = defined('TABLE_PREFIX') ? (string) TABLE_PREFIX : 'isw_';
        $sql = "SELECT
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
                WHERE t.theme_id = {$themeId}
                  AND t.theme_status = 1
                  AND (t.theme_app_id = {$siteAppId} OR t.theme_id = {$liveThemeId})
                LIMIT 1";
        $res = @mysqli_query($connection, $sql);
        if (!$res || mysqli_num_rows($res) === 0) {
            return null;
        }
        $row = mysqli_fetch_assoc($res);
        $avail = iswCheckThemeTemplateAvailability($siteAppPath, (string) ($row['theme_path'] ?? ''));
        if (empty($avail['template_exists'])) {
            return null;
        }
        $row['template_exists'] = true;
        $row['has_desktop'] = $avail['has_desktop'];
        $row['has_mobile'] = $avail['has_mobile'];
        return $row;
    }
}

if (!function_exists('iswBuildCleanUrlWithoutPreviewParams')) {
    function iswBuildCleanUrlWithoutPreviewParams(): string
    {
        $reqUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $parsed = @parse_url($reqUri);
        $path = (is_array($parsed) && !empty($parsed['path'])) ? (string) $parsed['path'] : '/';
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }
        $queryArr = [];
        if (is_array($parsed) && isset($parsed['query']) && $parsed['query'] !== '') {
            parse_str($parsed['query'], $queryArr);
            unset($queryArr['_preview_theme'], $queryArr['_preview_exp'], $queryArr['_preview_sig']);
        }
        $qs = !empty($queryArr) ? ('?' . http_build_query($queryArr)) : '';
        return $path . $qs;
    }
}

if (!function_exists('iswResolveSiteThemePreview')) {
    function iswResolveSiteThemePreview($connection, int $siteId, int $siteAppId, string $siteAppPath, int $liveThemeId): array
    {
        $eligibleRoute = iswIsVisitorPreviewEligibleRoute();
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

        // Handle explicit preview exit on public storefront GET requests
        if ($eligibleRoute && $method === 'GET' && isset($_GET['_preview_theme']) && strtolower(trim((string) $_GET['_preview_theme'])) === 'exit') {
            iswClearThemePreviewCookie();
            $cleanUrl = iswBuildCleanUrlWithoutPreviewParams();
            if (!headers_sent()) {
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                header('Location: ' . $cleanUrl, true, 302);
                exit();
            }
            return ['active' => false, 'theme' => null, 'eligible_route' => true];
        }

        $requestedThemeId = 0;
        $fromSignedQuery = false;

        if ($eligibleRoute && isset($_GET['_preview_theme']) && ctype_digit((string) $_GET['_preview_theme'])) {
            $candId = (int) $_GET['_preview_theme'];
            $sig = isset($_GET['_preview_sig']) ? (string) $_GET['_preview_sig'] : '';
            $exp = isset($_GET['_preview_exp']) && ctype_digit((string) $_GET['_preview_exp']) ? (int) $_GET['_preview_exp'] : 0;

            if ($sig !== '' && iswVerifyThemePreviewSignature($siteId, $candId, $exp, $sig)) {
                $requestedThemeId = $candId;
                $fromSignedQuery = true;
            } elseif (iswHasPrivilegedSiteSession($connection, $siteId)) {
                $requestedThemeId = $candId;
                $fromSignedQuery = true;
            }
        }

        if ($requestedThemeId > 0) {
            $themeRow = iswFetchValidPreviewTheme($connection, $requestedThemeId, $siteAppId, $siteAppPath, $liveThemeId);
            if ($themeRow) {
                if ($fromSignedQuery) {
                    iswSetThemePreviewCookie($siteId, $requestedThemeId);
                }
                return ['active' => true, 'theme' => $themeRow, 'eligible_route' => $eligibleRoute];
            }
            iswClearThemePreviewCookie();
            return ['active' => false, 'theme' => null, 'eligible_route' => $eligibleRoute];
        }

        if (!empty($_COOKIE[ISW_THEME_PREVIEW_COOKIE])) {
            $parsedCookie = iswParseThemePreviewCookie((string) $_COOKIE[ISW_THEME_PREVIEW_COOKIE], $siteId);
            if ($parsedCookie) {
                $themeRow = iswFetchValidPreviewTheme($connection, (int) $parsedCookie['theme_id'], $siteAppId, $siteAppPath, $liveThemeId);
                if ($themeRow) {
                    return ['active' => true, 'theme' => $themeRow, 'eligible_route' => $eligibleRoute];
                }
            }
            iswClearThemePreviewCookie();
        }

        return ['active' => false, 'theme' => null, 'eligible_route' => $eligibleRoute];
    }
}

if (!function_exists('iswInjectThemePreviewBanner')) {
    function iswInjectThemePreviewBanner(string $html): string
    {
        if (!defined('ISW_THEME_PREVIEW_RENDERING') || ISW_THEME_PREVIEW_RENDERING !== true) {
            return $html;
        }
        if ($html === '' || strpos($html, 'id="iswThemePreviewBar"') !== false) {
            return $html;
        }

        // Only modify HTML responses; preserve JSON/CSS/XML/binary responses intact
        if (function_exists('headers_list')) {
            foreach (headers_list() as $hdr) {
                if (stripos($hdr, 'Content-Type:') === 0 && stripos($hdr, 'text/html') === false && stripos($hdr, 'application/xhtml') === false) {
                    return $html;
                }
            }
        }

        $bodyClosePos = strripos($html, '</body>');
        if ($bodyClosePos === false) {
            return $html;
        }

        $root = iswThemePreviewRootDir();
        $cssLib = defined('CSS_LIBRARY') ? (string) CSS_LIBRARY : '/lib/resource/ui/css/';
        $jsLib = defined('JS_LIBRARY') ? (string) JS_LIBRARY : '/lib/resource/ui/js/';
        $cssRel = 'app/default/desktop/theme-preview-bar.css';
        $jsRel = 'app/default/desktop/theme-preview-bar.js';
        $cssDisk = $root . $cssLib . $cssRel;
        $jsDisk = $root . $jsLib . $jsRel;
        $cssVer = is_file($cssDisk) ? (string) filemtime($cssDisk) : '1';
        $jsVer = is_file($jsDisk) ? (string) filemtime($jsDisk) : '1';

        $cssUrl = htmlspecialchars($cssLib . $cssRel . '?v=' . $cssVer, ENT_QUOTES, 'UTF-8');
        $jsUrl = htmlspecialchars($jsLib . $jsRel . '?v=' . $jsVer, ENT_QUOTES, 'UTF-8');

        $previewId = defined('ISW_THEME_PREVIEW_ID') ? (int) ISW_THEME_PREVIEW_ID : 0;
        $previewTitle = defined('ISW_THEME_PREVIEW_TITLE') ? (string) ISW_THEME_PREVIEW_TITLE : 'Preview Theme';
        $previewPath = defined('ISW_THEME_PREVIEW_PATH') ? (string) ISW_THEME_PREVIEW_PATH : '';
        $liveId = defined('ISW_THEME_LIVE_ID') ? (int) ISW_THEME_LIVE_ID : 0;
        $liveTitle = defined('ISW_THEME_LIVE_TITLE') ? (string) ISW_THEME_LIVE_TITLE : 'Live Theme';

        $canApply = defined('ISW_THEME_PREVIEW_CAN_APPLY')
            ? (bool) ISW_THEME_PREVIEW_CAN_APPLY
            : false;

        $isSameAsLive = ($previewId > 0 && $previewId === $liveId);
        $cleanPathWithoutPreview = iswBuildCleanUrlWithoutPreviewParams();
        $exitSep = (strpos($cleanPathWithoutPreview, '?') !== false) ? '&' : '?';
        $exitUrl = $cleanPathWithoutPreview . $exitSep . '_preview_theme=exit';

        $escTitle = htmlspecialchars($previewTitle, ENT_QUOTES, 'UTF-8');
        $escPath = htmlspecialchars($previewPath, ENT_QUOTES, 'UTF-8');
        $escLiveTitle = htmlspecialchars($liveTitle, ENT_QUOTES, 'UTF-8');
        $escExitUrl = htmlspecialchars($exitUrl, ENT_QUOTES, 'UTF-8');
        $escCleanUrl = htmlspecialchars($cleanPathWithoutPreview, ENT_QUOTES, 'UTF-8');

        $headCssTag = '<link rel="stylesheet" href="' . $cssUrl . '">' . "\n";
        $headClosePos = strripos($html, '</head>');
        if ($headClosePos !== false) {
            $html = substr_replace($html, $headCssTag, $headClosePos, 0);
            $bodyClosePos = strripos($html, '</body>');
            if ($bodyClosePos === false) {
                return $html;
            }
        } else {
            $html = $headCssTag . $html;
            $bodyClosePos = strripos($html, '</body>');
        }

        $applyButtonHtml = '';
        if ($canApply && !$isSameAsLive) {
            $applyButtonHtml = '<button type="button" class="isw-tpb-btn isw-tpb-btn-apply" id="iswTpbApplyBtn">Apply Theme</button>';
        }
        $themesLinkHtml = '';
        if ($canApply) {
            $themesLinkHtml = '<a class="isw-tpb-btn isw-tpb-btn-manage" id="iswTpbManageLink" href="/dashboard/appearance/?type=themes">Switch Theme</a>';
        }

        $liveNote = $isSameAsLive
            ? 'Previewing the current live theme'
            : 'Live visitors still see "' . $escLiveTitle . '"';

        $bannerHtml = "\n"
            . '<div id="iswThemePreviewFrame" aria-hidden="true">'
            . '<div class="isw-tpb-top-ribbon" aria-hidden="true">'
            . '<span class="isw-tpb-top-pill">PREVIEW MODE &middot; ' . $escTitle . '</span>'
            . '</div>'
            . '</div>' . "\n"
            . '<aside id="iswThemePreviewBar"'
            . ' class="isw-tpb-bar"'
            . ' role="region"'
            . ' aria-label="Website theme preview bar"'
            . ' data-theme-id="' . $previewId . '"'
            . ' data-theme-title="' . $escTitle . '"'
            . ' data-theme-path="' . $escPath . '"'
            . ' data-live-id="' . $liveId . '"'
            . ' data-same-as-live="' . ($isSameAsLive ? '1' : '0') . '"'
            . ' data-can-apply="' . ($canApply ? '1' : '0') . '"'
            . ' data-api-url="/api/v3/appearance/themes/"'
            . ' data-exit-url="' . $escExitUrl . '"'
            . ' data-clean-url="' . $escCleanUrl . '">'
            . '<div class="isw-tpb-inner">'
            . '<div class="isw-tpb-status">'
            . '<span class="isw-tpb-badge"><span class="isw-tpb-dot" aria-hidden="true"></span>PREVIEW MODE</span>'
            . '<div class="isw-tpb-text">'
            . '<strong class="isw-tpb-title">' . $escTitle . '</strong>'
            . '<span class="isw-tpb-note" id="iswTpbNote">' . $liveNote . '</span>'
            . '</div>'
            . '</div>'
            . '<div class="isw-tpb-actions">'
            . $applyButtonHtml
            . $themesLinkHtml
            . '<a class="isw-tpb-btn isw-tpb-btn-exit" id="iswTpbExitLink" href="' . $escExitUrl . '">Exit Preview</a>'
            . '<button type="button" class="isw-tpb-btn isw-tpb-btn-toggle" id="iswTpbToggleBtn" aria-expanded="true" aria-label="Minimize preview bar" title="Minimize preview bar">&#8722;</button>'
            . '</div>'
            . '</div>'
            . '</aside>' . "\n"
            . '<script defer src="' . $jsUrl . '"></script>' . "\n";

        return substr_replace($html, $bannerHtml, $bodyClosePos, 0);
    }
}
