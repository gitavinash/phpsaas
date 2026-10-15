<?php

/**
 * V3 Vehicles List & Metrics API
 * Path: /api/v3/vehicles/list.php
 * Handles fetching vehicle models, search, filters, and KPI summary
 */

header("Content-type: application/json; charset=utf-8");

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__, 3);
}
if (!isset($connection)) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    }
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : 'isw_';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : (int)($_SESSION['site_id'] ?? 0);
$userId    = defined('USER_ID')      ? (int) USER_ID : (int)($_SESSION['user_id'] ?? 0);

require_once __DIR__ . '/schema.php';
ensureTransporterVehiclesSchema($connection, $tblPrefix, $siteId);

$search   = isset($_GET['search']) ? mysqli_real_escape_string($connection, trim($_GET['search'])) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($connection, strtolower(trim($_GET['category']))) : '';
$rawTransporterId = $_GET['transporter_id'] ?? ($_GET['transporterId'] ?? 0);
$transporterId = is_numeric($rawTransporterId) ? (int)$rawTransporterId : 0;
if ($transporterId <= 0 && !empty($rawTransporterId)) {
    $decodedTransporterId = base64_decode((string)$rawTransporterId, true);
    if ($decodedTransporterId !== false && is_numeric($decodedTransporterId)) {
        $transporterId = (int)$decodedTransporterId;
    }
}

$rawId = $_GET['id'] ?? ($_GET['vehicle_id'] ?? 0);
$vehicleId = 0;
if (is_numeric($rawId) && (int)$rawId > 0) {
    $vehicleId = (int)$rawId;
} else if (!empty($rawId)) {
    $decoded = base64_decode((string)$rawId, true);
    if ($decoded !== false && is_numeric($decoded)) {
        $vehicleId = (int)$decoded;
    }
}

$whereConditions = [];

if ($transporterId > 0) {
    $transporterQuery = mysqli_query($connection, "SELECT transporter_description FROM {$tblPrefix}transporter WHERE transporter_id = {$transporterId} LIMIT 1");
    $transporterRow = $transporterQuery ? mysqli_fetch_assoc($transporterQuery) : null;
    $fleetTypes = [];
    if ($transporterRow && preg_match('/Fleet Types:\s*([^\]]+)/i', $transporterRow['transporter_description'] ?? '', $fleetMatch)) {
        $fleetTypes = array_values(array_filter(array_map('trim', explode(',', $fleetMatch[1]))));
    }

    if (empty($fleetTypes)) {
        $whereConditions[] = '1 = 0';
    } else {
        $fleetConditions = [];
        foreach ($fleetTypes as $fleetType) {
            $fleetType = mysqli_real_escape_string($connection, $fleetType);
            $fleetConditions[] = "(v.vehicle_type = LOWER('{$fleetType}') OR v.vehicle_title LIKE '%{$fleetType}%')";
        }
        $whereConditions[] = '(' . implode(' OR ', $fleetConditions) . ')';
    }
}

if ($vehicleId > 0) {
    $whereConditions[] = "v.vehicle_id = {$vehicleId}";
}

if (!empty($category) && $category !== 'all') {
    if ($category === 'mpv_suv') {
        $whereConditions[] = "LOWER(TRIM(v.vehicle_type)) IN ('mpv', 'suv')";
    } elseif ($category === 'tempo_bus') {
        $whereConditions[] = "LOWER(TRIM(v.vehicle_type)) IN ('bus', 'traveller', 'light_commercial_vehicle')";
    } elseif ($category === 'micro_hatch') {
        $whereConditions[] = "LOWER(TRIM(v.vehicle_type)) IN ('micro', 'hatchback')";
    } else {
        $whereConditions[] = "LOWER(TRIM(v.vehicle_type)) = '{$category}'";
    }
}

if (!empty($search)) {
    $whereConditions[] = "(v.vehicle_title LIKE '%{$search}%' OR v.vehicle_type LIKE '%{$search}%' OR v.vehicle_description LIKE '%{$search}%' OR v.vehicle_fuel_type LIKE '%{$search}%')";
}

$whereSql = '';
if (!empty($whereConditions)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereConditions);
}

// Fetch list of vehicles
$sql = "SELECT 
            v.vehicle_id,
            v.vehicle_thumbnail,
            v.vehicle_gallery,
            v.vehicle_title,
            v.vehicle_description,
            v.vehicle_seating,
            v.vehicle_per_km_rate_with_tax,
            v.vehicle_per_km_rate_without_tax,
            v.vehicle_luggage,
            v.vehicle_fuel_type,
            v.vehicle_is_deletable,
            v.vehicle_type,
            v.vehicle_time
        FROM {$tblPrefix}vehicles v
        {$whereSql}
        ORDER BY v.vehicle_id DESC";

$res = mysqli_query($connection, $sql);

$vehicles = [];
$formatVehicle = function ($row) {
    $vId = (int)$row['vehicle_id'];
    $type = strtolower($row['vehicle_type'] ?? 'other');
    $typeLabels = [
        'mpv' => 'MPV',
        'suv' => 'SUV',
        'bus' => 'Bus / Coach',
        'traveller' => 'Tempo Traveller',
        'light_commercial_vehicle' => 'Light Commercial / Van',
        'sedan' => 'Sedan',
        'hatchback' => 'Hatchback',
        'micro' => 'Small Car / Micro',
        'flight' => 'Flight',
        'rail' => 'Rail'
    ];

    $gallery = [];
    if (!empty($row['vehicle_gallery'])) {
        $decoded = json_decode($row['vehicle_gallery'], true);
        if (is_array($decoded)) {
            $gallery = array_values(array_filter(array_map('trim', $decoded)));
        }
    }

    return [
        'id' => $vId,
        'encoded_id' => base64_encode((string)$vId),
        'title' => ucwords(strtolower(trim($row['vehicle_title'] ?? ''))),
        'type' => $type,
        'type_label' => $typeLabels[$type] ?? ucwords(str_replace('_', ' ', $type)),
        'seating' => (int)($row['vehicle_seating'] ?? 0),
        'luggage' => (int)($row['vehicle_luggage'] ?? 0),
        'rate_per_km_with_tax' => (float)($row['vehicle_per_km_rate_with_tax'] ?? 0),
        'rate_per_km_without_tax' => (float)($row['vehicle_per_km_rate_without_tax'] ?? 0),
        'fuel_type' => $row['vehicle_fuel_type'] ?? '',
        'description' => $row['vehicle_description'] ?? '',
        'thumbnail' => !empty($row['vehicle_thumbnail']) ? $row['vehicle_thumbnail'] : '',
        'gallery' => $gallery,
        'gallery_count' => count($gallery),
        'is_deletable' => (int)($row['vehicle_is_deletable'] ?? 1) === 1,
        'created_at' => $row['vehicle_time'] ?? ''
    ];
};
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $vehicles[] = $formatVehicle($row);
    }
}

$masterVehicles = $vehicles;
if ($transporterId > 0) {
    $masterRes = mysqli_query($connection, "SELECT vehicle_id, vehicle_thumbnail, vehicle_gallery, vehicle_title, vehicle_description, vehicle_seating, vehicle_per_km_rate_with_tax, vehicle_per_km_rate_without_tax, vehicle_luggage, vehicle_fuel_type, vehicle_is_deletable, vehicle_type, vehicle_time FROM {$tblPrefix}vehicles ORDER BY vehicle_id DESC");
    $masterVehicles = [];
    if ($masterRes) {
        while ($masterRow = mysqli_fetch_assoc($masterRes)) {
            $masterVehicles[] = $formatVehicle($masterRow);
        }
    }
}

// Fetch category metrics
$metricsSql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN vehicle_type IN ('mpv', 'suv') THEN 1 ELSE 0 END) as mpv_suv,
                SUM(CASE WHEN vehicle_type IN ('bus', 'traveller', 'light_commercial_vehicle') THEN 1 ELSE 0 END) as tempo_bus,
                SUM(CASE WHEN vehicle_type = 'sedan' THEN 1 ELSE 0 END) as sedan,
                SUM(CASE WHEN vehicle_type IN ('micro', 'hatchback') THEN 1 ELSE 0 END) as micro_hatch
               FROM {$tblPrefix}vehicles";

$metricsRes = mysqli_query($connection, $metricsSql);
$metricsRow = $metricsRes ? mysqli_fetch_assoc($metricsRes) : [];

$metrics = [
    'total'       => (int)($metricsRow['total'] ?? count($vehicles)),
    'mpv_suv'     => (int)($metricsRow['mpv_suv'] ?? 0),
    'tempo_bus'   => (int)($metricsRow['tempo_bus'] ?? 0),
    'sedan'       => (int)($metricsRow['sedan'] ?? 0),
    'micro_hatch' => (int)($metricsRow['micro_hatch'] ?? 0)
];

echo json_encode([
    'status'     => 'success',
    'total'      => count($vehicles),
    'metrics'    => $metrics,
    'vehicles'   => $vehicles,
    'data'       => $vehicles,
    'master_vehicles' => $masterVehicles,
    'categories' => [
        'mpv'                      => 'MPV',
        'suv'                      => 'SUV',
        'bus'                      => 'Bus / Coach',
        'traveller'                => 'Tempo Traveller',
        'light_commercial_vehicle' => 'Light Commercial / Van',
        'sedan'                    => 'Sedan',
        'hatchback'                => 'Hatchback',
        'micro'                    => 'Small Car / Micro',
        'flight'                   => 'Flight',
        'rail'                     => 'Rail'
    ]
]);
