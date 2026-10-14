<?php
/**
 * V3 Link Shortener API
 * Endpoint: /api/v3/shortener/shorten.php
 * Method: POST
 */

header('Content-Type: application/json; charset=utf-8');

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);

if (!isset($connection)) {
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
    if (!isset($connection) && file_exists($docRoot . '/dbConfig.php')) {
        require_once $docRoot . '/dbConfig.php';
        $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
}

if (!isset($connection) || !$connection) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database connection failed.']);
    exit();
}

if (!function_exists('longToShortUrl')) {
    if (defined('UTIL_FUNCTIONS') && file_exists(UTIL_FUNCTIONS)) {
        require_once UTIL_FUNCTIONS;
    } elseif (file_exists($docRoot . '/lib/functions/utils.php')) {
        require_once $docRoot . '/lib/functions/utils.php';
    }
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) {
    $data = [];
}
if (empty($data)) {
    if (!empty($_POST)) {
        $data = $_POST;
    } elseif (!empty($_REQUEST)) {
        $data = $_REQUEST;
    } elseif (!empty($_GET)) {
        $data = $_GET;
    }
}

$longUrl = trim((string)($data['url'] ?? ($data['long_url'] ?? ($_POST['url'] ?? ($_POST['long_url'] ?? ($_REQUEST['url'] ?? ($_REQUEST['long_url'] ?? ($_GET['url'] ?? ''))))))));
$alias   = trim((string)($data['alias'] ?? ($data['custom_slug'] ?? ($_POST['alias'] ?? ($_POST['custom_slug'] ?? ($_REQUEST['alias'] ?? ($_REQUEST['custom_slug'] ?? ($_GET['alias'] ?? ''))))))));

if (empty($longUrl)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'code'   => 400,
        'msg'    => 'Please provide a valid destination URL to shorten.'
    ]);
    exit();
}

// Prepend https:// if protocol is missing
if (!preg_match('#^https?://#i', $longUrl)) {
    $longUrl = 'https://' . $longUrl;
}

// Basic URL validation
if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'code'   => 400,
        'msg'    => 'Invalid destination URL format. Please ensure the URL is valid.'
    ]);
    exit();
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$userId    = defined('USER_ID') ? (int)USER_ID : 0;
$siteId    = defined('SITE_ID') ? (int)SITE_ID : 0;
$baseUrl   = function_exists('getLinkShortenerBaseUrl') ? getLinkShortenerBaseUrl($connection) : (defined('SITE_URL') ? SITE_URL : 'http://dc.local');

// If custom alias is requested
if (!empty($alias)) {
    // Sanitize alias
    $cleanAlias = preg_replace('/[^a-zA-Z0-9_-]/', '', $alias);
    if (strlen($cleanAlias) < 2) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'code'   => 400,
            'msg'    => 'Custom alias must be at least 2 alphanumeric characters.'
        ]);
        exit();
    }

    $escAlias = mysqli_real_escape_string($connection, $cleanAlias);
    $escLong  = mysqli_real_escape_string($connection, $longUrl);

    // Check if alias already taken
    $checkAliasSql = "SELECT redirection_id, redirection_to FROM {$tblPrefix}redirection 
                      WHERE (redirection_from = '{$escAlias}' OR redirection_from = '/{$escAlias}') AND redirection_status = 1 
                      LIMIT 1";
    $checkRes = mysqli_query($connection, $checkAliasSql);
    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        $row = mysqli_fetch_assoc($checkRes);
        if ($row['redirection_to'] === $longUrl) {
            // Already points to same destination
            $shortUrl = $baseUrl . '/' . $cleanAlias;
            echo json_encode([
                'status'     => 'success',
                'code'       => 200,
                'short_url'  => $shortUrl,
                'short_code' => $cleanAlias,
                'long_url'   => $longUrl,
                'msg'        => 'Custom short link ready.'
            ]);
            exit();
        } else {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'code'   => 409,
                'msg'    => 'This custom alias is already in use. Please pick another one.'
            ]);
            exit();
        }
    }

    // Insert custom redirection
    $insertSql = "INSERT INTO {$tblPrefix}redirection(redirection_from, redirection_to, redirection_http_status, redirection_owner_id, redirection_site_id, redirection_status, redirection_generated, redirection_update) 
                  VALUES('{$escAlias}', '{$escLong}', 301, {$userId}, {$siteId}, 1, NOW(), NOW())";
    if (mysqli_query($connection, $insertSql)) {
        $shortUrl = $baseUrl . '/' . $cleanAlias;
        echo json_encode([
            'status'     => 'success',
            'code'       => 200,
            'short_url'  => $shortUrl,
            'short_code' => $cleanAlias,
            'long_url'   => $longUrl,
            'msg'        => 'Custom short link created successfully.'
        ]);
        exit();
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'code'   => 500,
            'msg'    => 'Failed to save custom alias: ' . mysqli_error($connection)
        ]);
        exit();
    }
}

// Standard short URL generation
$shortUrl = longToShortUrl($connection, $longUrl);

if (!empty($shortUrl)) {
    $code = basename(parse_url($shortUrl, PHP_URL_PATH));
    http_response_code(200);
    echo json_encode([
        'status'     => 'success',
        'code'       => 200,
        'short_url'  => $shortUrl,
        'short_code' => $code,
        'long_url'   => $longUrl,
        'msg'        => 'URL shortened successfully.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'code'   => 500,
        'msg'    => 'Failed to create short link.'
    ]);
}
