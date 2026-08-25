<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : dirname(__DIR__, 3);
    if (file_exists($docRoot . '/config.php')) {
        require_once $docRoot . '/config.php';
    }
    if (file_exists($docRoot . '/lib/modules/connection.php')) {
        require_once $docRoot . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

$search = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
$state  = isset($_GET['state']) ? mysqli_real_escape_string($connection, trim($_GET['state'])) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

$rawId = $_GET['id'] ?? ($_GET['transporter_id'] ?? 0);
$transporterId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $transporterId = (int)$rawId;
} else if (!empty($rawId)) {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $transporterId = (int)$decoded;
    }
}

$whereConditions = [];
if ($siteId > 0) {
    $whereConditions[] = "t.transporter_site_id = {$siteId}";
}

if ($transporterId > 0) {
    $whereConditions[] = "t.transporter_id = {$transporterId}";
}

if (!empty($search)) {
    $whereConditions[] = "(
        t.transporter_title LIKE '%{$search}%' OR 
        t.transporter_city LIKE '%{$search}%' OR 
        t.transporter_mobile LIKE '%{$search}%' OR 
        t.transporter_email LIKE '%{$search}%' OR 
        t.transporter_service_state LIKE '%{$search}%'
    )";
}

if (!empty($state)) {
    $whereConditions[] = "(t.transporter_service_state LIKE '%{$state}%' OR t.transporter_city LIKE '%{$state}%')";
}

if ($status === 'inactive') {
    $whereConditions[] = "1 = 0";
}

$whereClause = !empty($whereConditions) ? implode(' AND ', $whereConditions) : '1=1';

// Fetch assigned packages count per transporter
$assignedCountMap = [];
$assignedWhere = $siteId > 0 ? "WHERE tpt_site_id = {$siteId} AND tpt_status = 1" : "WHERE tpt_status = 1";
$assignedRes = mysqli_query($connection, "
    SELECT tpt_transporter_id, COUNT(DISTINCT tpt_package_id) as pkg_count 
    FROM {$tblPrefix}tour_package_transporter 
    {$assignedWhere}
    GROUP BY tpt_transporter_id
");
if ($assignedRes) {
    while ($aRow = mysqli_fetch_assoc($assignedRes)) {
        $assignedCountMap[(int)$aRow['tpt_transporter_id']] = (int)$aRow['pkg_count'];
    }
}

// Fetch all transporters
$query = "
    SELECT 
        t.transporter_id,
        t.transporter_thumbnail,
        t.transporter_title,
        t.transporter_email,
        t.transporter_mobile,
        t.transporter_description,
        t.transporter_address,
        t.transporter_city,
        t.transporter_pincode,
        t.transporter_service_state,
        t.transporter_time,
        (
            SELECT s.state_name 
            FROM {$tblPrefix}city c 
            LEFT JOIN {$tblPrefix}state s ON c.city_state_id = s.state_id 
            WHERE c.city_name = t.transporter_city 
            LIMIT 1
        ) as resolved_state
    FROM {$tblPrefix}transporter AS t
    WHERE {$whereClause}
    ORDER BY t.transporter_id DESC
";

$res = mysqli_query($connection, $query);

$transportersList = [];
$uniqueStates = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $tId = (int)$row['transporter_id'];
        $rawStates = trim($row['transporter_service_state'] ?? '');
        
        if (!empty($rawStates)) {
            $splitStates = array_map('trim', explode(',', $rawStates));
            foreach ($splitStates as $s) {
                if (!empty($s) && !in_array($s, $uniqueStates, true)) {
                    $uniqueStates[] = $s;
                }
            }
        }

        $assignedPkgs = $assignedCountMap[$tId] ?? 0;

        $transportersList[] = [
            'id'             => $tId,
            'encoded_id'     => base64_encode((string)$tId),
            'title'          => $row['transporter_title'] ?? 'Transporter',
            'thumbnail'      => $row['transporter_thumbnail'] ?? '',
            'email'          => $row['transporter_email'] ?? '',
            'mobile'         => $row['transporter_mobile'] ?? '',
            'description'    => $row['transporter_description'] ?? '',
            'address'        => $row['transporter_address'] ?? '',
            'area'           => $row['transporter_address'] ?? '',
            'city'           => $row['transporter_city'] ?? '',
            'state'          => $row['resolved_state'] ?? '',
            'pincode'        => $row['transporter_pincode'] ?? '',
            'service_state'  => $rawStates,
            'service_states_array' => !empty($rawStates) ? array_values(array_filter(array_map('trim', explode(',', $rawStates)))) : [],
            'created'        => $row['transporter_time'] ?? '',
            'status'         => 1,
            'status_label'   => 'Active',
            'assigned_packages_count' => $assignedPkgs
        ];
    }
}

// Global Metrics
$totalWhere = $siteId > 0 ? "WHERE transporter_site_id = {$siteId}" : "";
$totalQuery = mysqli_query($connection, "SELECT COUNT(*) as total FROM {$tblPrefix}transporter {$totalWhere}");
$totalRow = $totalQuery ? mysqli_fetch_assoc($totalQuery) : ['total' => 0];

$totalTransporters = (int)($totalRow['total'] ?? 0);
$assignedTransporters = count($assignedCountMap);

sort($uniqueStates);

echo json_encode([
    'status'       => 'success',
    'metrics'      => [
        'total'      => $totalTransporters,
        'active'     => $totalTransporters,
        'assigned'   => $assignedTransporters,
        'states'     => count($uniqueStates),
        'filtered'   => count($transportersList)
    ],
    'states'       => $uniqueStates,
    'transporters' => $transportersList
], JSON_UNESCAPED_SLASHES);
