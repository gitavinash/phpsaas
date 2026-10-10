<?php
/**
 * V3 Transporter Terms & Rules API
 * Path: /api/v3/transporters/terms.php
 * Handles fetching and persisting standard and customized transporter billing terms.
 */

header('Content-Type: application/json; charset=utf-8');

$docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
if (!isset($connection)) {
    if (file_exists($docRoot . '/lib/configuration/index.php')) {
        require_once $docRoot . '/lib/configuration/index.php';
    }
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

if (!defined('LOGIN_CHECK')) {
    define('LOGIN_CHECK', $docRoot . '/lib/modules/isLogin.php');
}
if (file_exists(LOGIN_CHECK)) {
    require_once LOGIN_CHECK;
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

if ($siteId <= 0) {
    $siteId = 1;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$defaultTerms = [
    'parking_charges'    => 'no',
    'garage_to_garage'   => 'yes',
    'toll_tax'           => 'yes',
    'state_tax'          => 'yes',
    'driver_allowance'   => 'yes',
    'default_km_running' => 250,
    'custom_rules'       => [
        [
            'id'          => 'rule_1',
            'title'       => 'Daily Minimum KM Threshold',
            'description' => 'Minimum 250 KM charged per calendar day.',
            'enabled'     => true
        ],
        [
            'id'          => 'rule_2',
            'title'       => 'Payment Conditions',
            'description' => 'Booking deposit required to confirm; balance payable upon departure/completion.',
            'enabled'     => true
        ],
        [
            'id'          => 'rule_3',
            'title'       => 'Night Driving & Overtime',
            'description' => 'Night driving between 11:00 PM and 6:00 AM incurs additional driver night fee.',
            'enabled'     => true
        ],
        [
            'id'          => 'rule_4',
            'title'       => 'Hill Station AC Policy',
            'description' => 'Air conditioning remains off while driving on steep mountain/ghat climbs.',
            'enabled'     => true
        ]
    ]
];

// -----------------------------------------------------------------------------
// GET: Fetch Active Transporter Terms
// -----------------------------------------------------------------------------
if ($method === 'GET') {
    $sql = "SELECT site_configuration_value FROM {$tblPrefix}site_configuration 
            WHERE site_configuration_site_id = {$siteId} 
              AND site_configuration_key = 'transporter_billing_terms' 
              AND site_configuration_status = 1 
            LIMIT 1";
    $res = mysqli_query($connection, $sql);

    $terms = $defaultTerms;
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $saved = json_decode($row['site_configuration_value'] ?? '', true);
        if (is_array($saved) && !empty($saved)) {
            $terms = array_merge($defaultTerms, $saved);
        }
    }

    echo json_encode([
        'status' => 'success',
        'data'   => $terms
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// POST / PUT: Update Transporter Terms
// -----------------------------------------------------------------------------
if ($method === 'POST' || $method === 'PUT') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload) || empty($payload)) {
        $payload = !empty($_POST) ? $_POST : [];
    }

    $termsToSave = [
        'parking_charges'    => strtolower((string)($payload['parking_charges'] ?? 'no')) === 'yes' ? 'yes' : 'no',
        'garage_to_garage'   => strtolower((string)($payload['garage_to_garage'] ?? 'yes')) === 'no' ? 'no' : 'yes',
        'toll_tax'           => strtolower((string)($payload['toll_tax'] ?? 'yes')) === 'no' ? 'no' : 'yes',
        'state_tax'          => strtolower((string)($payload['state_tax'] ?? 'yes')) === 'no' ? 'no' : 'yes',
        'driver_allowance'   => strtolower((string)($payload['driver_allowance'] ?? 'yes')) === 'no' ? 'no' : 'yes',
        'default_km_running' => (float)($payload['default_km_running'] ?? 250),
        'custom_rules'       => is_array($payload['custom_rules'] ?? null) ? $payload['custom_rules'] : $defaultTerms['custom_rules']
    ];

    $jsonVal = mysqli_real_escape_string($connection, json_encode($termsToSave, JSON_UNESCAPED_UNICODE));

    // Check if configuration exists
    $checkQ = mysqli_query($connection, "SELECT site_configuration_id FROM {$tblPrefix}site_configuration 
                                         WHERE site_configuration_site_id = {$siteId} 
                                           AND site_configuration_key = 'transporter_billing_terms' 
                                         LIMIT 1");

    if ($checkQ && mysqli_num_rows($checkQ) > 0) {
        $updQ = "UPDATE {$tblPrefix}site_configuration 
                 SET site_configuration_value = '{$jsonVal}', site_configuration_status = 1 
                 WHERE site_configuration_site_id = {$siteId} 
                   AND site_configuration_key = 'transporter_billing_terms'";
        mysqli_query($connection, $updQ);
    } else {
        $insQ = "INSERT INTO {$tblPrefix}site_configuration (
                    site_configuration_site_id, site_configuration_key, site_configuration_value, site_configuration_status
                 ) VALUES (
                    {$siteId}, 'transporter_billing_terms', '{$jsonVal}', 1
                 )";
        mysqli_query($connection, $insQ);
    }

    echo json_encode([
        'status'  => 'success',
        'message' => 'Transporter billing terms and rules saved successfully.',
        'data'    => $termsToSave
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
exit;
