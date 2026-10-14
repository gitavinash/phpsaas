<?php
function getInitials($string)
{
    // Remove any special characters except letters, numbers and spaces
    $cleanString = preg_replace('/[^A-Za-z0-9\s]/', '', $string);

    // Split into words
    $words = preg_split('/\s+/', trim($cleanString));

    // Take first letter of each word
    $initials = '';
    foreach ($words as $word) {
        if ($word !== '') {
            $initials .= strtoupper($word[0]);
        }
    }

    return $initials;
}

function assetVersion($relativePath, $basePath = null)
{
    if ($basePath === null) {
        $basePath = defined('USER_PATH') ? USER_PATH . 'assets/' : ROOT . '/';
    }

    $filePath = rtrim((string) $basePath, '/') . '/' . ltrim((string) $relativePath, '/');

    if (is_file($filePath)) {
        $contentHash = @md5_file($filePath);
        if (is_string($contentHash) && $contentHash !== '') {
            return substr($contentHash, 0, 12);
        }

        return (string) (@filemtime($filePath) ?: time());
    }

    return '1';
}

function isEmailValid(string $email): bool
{
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    [$local] = explode('@', strtolower($email), 2);

    // Reject plus addressing for login
    if (strpos($local, '+') !== false) {
        return false;
    }

    return true;
}

function getRootDomain($host)
{
    $host = preg_replace('#^https?://#', '', $host); // Remove http/https
    $host = explode(':', $host)[0];                  // Remove port if any
    $host = rtrim($host, '/');                       // Remove trailing slash

    $parts = explode('.', $host);
    $count = count($parts);

    if ($count >= 2) {
        return $parts[$count - 2] . '.' . $parts[$count - 1]; // last two parts
    } else {
        return $host;
    }
}

function getPastDuration($pastTimestamp)
{
    // Convert string datetime to timestamp if necessary
    if (!is_numeric($pastTimestamp)) {
        $pastTimestamp = strtotime($pastTimestamp);
        if ($pastTimestamp === false) {
            return "Invalid date";
        }
    }

    $diff = time() - $pastTimestamp;

    if ($diff < 0) {
        return "In the future";
    } elseif ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } else {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    }
}

function truncateText($text, $maxLength = 300)
{
    // Remove any extra whitespace
    $text = trim($text);

    // If text is already short enough, return as is
    if (strlen($text) <= $maxLength) {
        return $text;
    }

    // Truncate text to maxLength
    $truncated = substr($text, 0, $maxLength);

    // Make sure not to cut off in the middle of a word
    // Find the last space within the truncated string
    $lastSpace = strrpos($truncated, ' ');
    if ($lastSpace !== false) {
        $truncated = substr($truncated, 0, $lastSpace);
    }

    // Add ellipsis to indicate truncation
    return $truncated . '...';
}

function clearAllCookies()
{
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

    foreach ($_COOKIE as $name => $value) {
        // Clear cookie without specifying domain — applies to current host
        setcookie($name, '', [
            'expires'  => time() - 3600, // past date
            'path'     => '/',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $isSecure ? 'None' : 'Lax',
        ]);

        // Also try clearing with root domain wildcard
        setcookie($name, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '.' . $_SERVER['SERVER_NAME'], // root + subdomains
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $isSecure ? 'None' : 'Lax',
        ]);
    }
}

function safe_html($string)
{
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}
function get_object_from_constant(string $menu_name): array
{
    if (!defined($menu_name)) {
        return [];
    }
    $json = constant($menu_name);
    $decoded = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return [];
    }
    return $decoded;
}

function normalizeIndianMobile($input)
{
    // Remove everything except digits
    $num = preg_replace('/\D/', '', $input);

    // Remove leading country code patterns like +91, 91, 0091
    if (preg_match('/^(91|0091)/', $num)) {
        $num = preg_replace('/^(91|0091)/', '', $num);
    }

    // Remove leading zero if present (e.g., 09876543210)
    $num = ltrim($num, '0');

    // Now it should be exactly 10 digits starting with 6-9
    if (preg_match('/^[6-9]\d{9}$/', $num)) {
        return $num; // valid normalized Indian number
    }

    return false; // invalid number
}

function getLinkShortenerBaseUrl($connection)
{
    if (defined('SHORT_URL_DOMAIN') && !empty(SHORT_URL_DOMAIN)) {
        return rtrim(SHORT_URL_DOMAIN, '/');
    }

    static $cachedShortenerUrl = null;
    if ($cachedShortenerUrl !== null) {
        return $cachedShortenerUrl;
    }

    $tablePrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
    $protocol = (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? 'http://' : 'https://';

    $checkQuery = "SELECT site_domain, site_url FROM {$tablePrefix}site 
                   INNER JOIN {$tablePrefix}app ON site_app_id = app_id 
                   WHERE (app_path = 'link_shortener' OR app_id = 12) AND site_status = 1 
                   ORDER BY site_id DESC LIMIT 1";

    if ($connection instanceof mysqli) {
        if ($res = mysqli_query($connection, $checkQuery)) {
            if ($row = mysqli_fetch_assoc($res)) {
                $domain = !empty($row['site_domain']) ? $row['site_domain'] : $row['site_url'];
                if (!preg_match('#^https?://#i', $domain)) {
                    $domain = $protocol . $domain;
                }
                $cachedShortenerUrl = rtrim($domain, '/');
                return $cachedShortenerUrl;
            }
        }
    }

    if (defined('SITE_URL') && !empty(SITE_URL)) {
        return rtrim(SITE_URL, '/');
    }

    return $protocol . 'dc.local';
}

function longToShortUrl($connection, $longUrl)
{
    if (empty($longUrl) || !($connection instanceof mysqli)) {
        return '';
    }

    $escapedUrl = mysqli_real_escape_string($connection, trim($longUrl));
    $ownerId = defined('USER_ID') ? (int)USER_ID : 0;
    $siteId = defined('SITE_ID') ? (int)SITE_ID : 0;
    $tablePrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';

    // Check if an active short link already exists for this exact destination to reuse clean short links
    $checkSql = "SELECT redirection_id FROM {$tablePrefix}redirection 
                 WHERE redirection_to = '{$escapedUrl}' AND redirection_from = '#' AND redirection_status = 1 
                 ORDER BY redirection_id DESC LIMIT 1";
    $checkRes = mysqli_query($connection, $checkSql);
    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        $existing = mysqli_fetch_assoc($checkRes);
        $shortId = (int)$existing['redirection_id'];
    } else {
        $insertSql = "INSERT INTO {$tablePrefix}redirection(redirection_from, redirection_to, redirection_http_status, redirection_owner_id, redirection_site_id, redirection_status, redirection_generated, redirection_update) 
                      VALUES('#', '{$escapedUrl}', 301, {$ownerId}, {$siteId}, 1, NOW(), NOW())";
        if (mysqli_query($connection, $insertSql)) {
            $shortId = (int)mysqli_insert_id($connection);
        } else {
            return '';
        }
    }

    $baseUrl = getLinkShortenerBaseUrl($connection);
    return $baseUrl . '/' . base64_encode((string)$shortId);
}

function shortToLongUrl($connection, $shortUrl)
{
    if (empty($shortUrl) || !($connection instanceof mysqli)) {
        return [];
    }

    $shortUrl = trim((string)$shortUrl, '/');
    if ($shortUrl === '') {
        return [];
    }

    $tablePrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
    $escapedShort = mysqli_real_escape_string($connection, $shortUrl);
    $decodedId = base64_decode($shortUrl, true);
    $idCondition = ($decodedId !== false && is_numeric($decodedId)) ? "redirection_id = '" . (int)$decodedId . "'" : "0=1";

    $checkRedirectQuery = "SELECT redirection_id, redirection_to, redirection_http_status 
                           FROM {$tablePrefix}redirection 
                           WHERE ({$idCondition} OR redirection_from = '{$escapedShort}' OR redirection_from = '/{$escapedShort}') 
                             AND redirection_status = 1 
                           ORDER BY redirection_id DESC LIMIT 1";

    if ($checkUrl = mysqli_query($connection, $checkRedirectQuery)) {
        if (mysqli_num_rows($checkUrl) != 0) {
            return mysqli_fetch_assoc($checkUrl);
        }
    }
    return [];
}

function createLog($connection, $refId, $refType, $logType, $description, $internal = '', $siteId = null, $userId = null)
{
    if (!($connection instanceof mysqli)) {
        return false;
    }

    $siteId = (string) (($siteId !== null) ? $siteId : (defined('SITE_ID') ? SITE_ID : 0));
    $userId = (string) (($userId !== null) ? $userId : (defined('USER_ID') ? USER_ID : 0));
    $refId = (string) $refId;
    $refType = (string) $refType;
    $logType = (string) $logType;
    $description = (string) $description;
    $internal = (string) $internal;

    $insertLogSQL = "INSERT INTO " . TABLE_PREFIX . "log(log_site_id, log_user_id, log_ref_id, log_ref_type, log_type, log_description, log_internal) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insertLogSQL);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'sssssss', $siteId, $userId, $refId, $refType, $logType, $description, $internal);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}
