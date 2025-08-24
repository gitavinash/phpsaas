<?php
// ---- Config ----
const DEFAULT_COUNTRY = 'IND';
const HERE_ENDPOINT   = 'https://autocomplete.search.hereapi.com/v1/autocomplete';
const HTTP_TIMEOUT    = 5;          // total seconds per attempt
const CONNECT_TIMEOUT = 2;          // TCP/SSL connect timeout
const RETRIES         = 1;          // retry count on transient failures (keep small for speed)
const LIMIT           = 10;         // max results
const CACHE_TTL       = 60;         // seconds (APCu micro-cache)

header('Content-Type: application/json; charset=utf-8');

$httpStatus   = 400;
$status       = 'warning';
$locationList = [];

$searchQuery = isset($_GET['searchQuery']) ? trim($_GET['searchQuery']) : null;
$countryId   = isset($_GET['countryId']) ? strtoupper(trim($_GET['countryId'])) : DEFAULT_COUNTRY;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $searchQuery !== null && $searchQuery !== '') {
    // Build HERE URL safely
    $params = [
        'q'      => $searchQuery,
        'lang'   => 'en-IN',
        'limit'  => LIMIT,
        'apiKey' => HERE_MAPS_KEY, // define your constant
    ];
    $apiUrl = HERE_ENDPOINT . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    // Simple micro-cache (APCu)
    $cacheKey = 'here_auto_' . md5($apiUrl);
    $useCache = function_exists('apcu_fetch');
    if ($useCache) {
        $cached = apcu_fetch($cacheKey);
        if ($cached !== false) {
            $responseData = $cached;
        }
    }

    if (!isset($responseData)) {
        $attempt = 0;
        $responseData = null;
        while ($attempt <= RETRIES) {
            $attempt++;

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING       => '', // allow gzip/deflate
                CURLOPT_MAXREDIRS      => 3,
                CURLOPT_TIMEOUT        => HTTP_TIMEOUT,
                CURLOPT_CONNECTTIMEOUT => CONNECT_TIMEOUT,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, // avoid IPv6 path issues
                CURLOPT_HTTPHEADER     => [
                    'Accept: application/json',
                    'User-Agent: your-app/1.0',
                ],
            ]);

            $raw  = curl_exec($ch);
            $err  = curl_errno($ch);
            $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if (!$err && $code >= 200 && $code < 300 && $raw) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $responseData = $decoded;
                    if ($useCache) apcu_store($cacheKey, $responseData, CACHE_TTL);
                    break;
                }
            }

            // brief pause before retry (kept tiny for responsiveness)
            if ($attempt <= RETRIES) usleep(150000); // 150ms
        }
    }

    if (isset($responseData['items']) && is_array($responseData['items'])) {
        foreach ($responseData['items'] as $item) {
            $addr = $item['address'] ?? [];
            $locationList[] = [
                'location' => $addr['label'] ?? '',
                'city'     => $addr['city'] ?? '',
                'state'    => $addr['state'] ?? '',
                'country'  => $addr['countryName'] ?? '',
                'pincode'  => $addr['postalCode'] ?? '',
                'address'  => implode(', ', array_filter([
                    $addr['city'] ?? '',
                    $addr['state'] ?? '',
                    $addr['countryName'] ?? '',
                    $addr['postalCode'] ?? '',
                ])),
            ];
        }
        $httpStatus = $locationList ? 200 : 204;
        $status     = $locationList ? 'success' : 'warning';
    } else {
        $httpStatus = 204; // no content from upstream
        $status     = 'warning';
    }
} else {
    $httpStatus = 400;
    $status     = 'warning';
}

http_response_code($httpStatus);
echo json_encode(['status' => $status, 'locationList' => $locationList], JSON_UNESCAPED_UNICODE);