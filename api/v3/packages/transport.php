<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId    = defined('USER_ID')      ? (int) USER_ID : 0;

// Ensure tour_package_transporter relation table exists
mysqli_query($connection, "
    CREATE TABLE IF NOT EXISTS `{$tblPrefix}tour_package_transporter` (
        `tpt_id` INT AUTO_INCREMENT PRIMARY KEY,
        `tpt_site_id` INT NOT NULL DEFAULT 0,
        `tpt_package_id` INT NOT NULL DEFAULT 0,
        `tpt_transporter_id` INT NOT NULL DEFAULT 0,
        `tpt_vehicle_type` VARCHAR(100) DEFAULT '',
        `tpt_rate` VARCHAR(100) DEFAULT '',
        `tpt_notes` TEXT,
        `tpt_status` TINYINT(1) DEFAULT 1,
        `tpt_created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `tpt_updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_pkg` (`tpt_package_id`, `tpt_site_id`),
        KEY `idx_transporter` (`tpt_transporter_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// -------------------------------------------------------------
// POST: Assign / Unassign Transporter OR Update Package Vehicles
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);
    if (!is_array($data) || empty($data)) {
        $data = $_POST;
    }

    $action = $data['action'] ?? 'assign_transporter';
    $rawId  = $data['package_id'] ?? ($data['packageId'] ?? ($data['id'] ?? 0));

    if (is_numeric($rawId) && (int)$rawId > 0) {
        $packageId = (int)$rawId;
    } else {
        $decoded   = base64_decode((string)$rawId, true);
        $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
    }

    if ($packageId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Invalid or missing tour package ID.'
        ], JSON_UNESCAPED_SLASHES);
        exit();
    }

    if ($action === 'update_vehicles') {
        $outstationVehicle = mysqli_real_escape_string($connection, trim($data['outstation_vehicle'] ?? ''));
        $localVehicle      = mysqli_real_escape_string($connection, trim($data['localsightseen_vehicle'] ?? ''));

        $updateSql = "
            UPDATE {$tblPrefix}tour_package 
            SET tour_package_outstation_vehicle = '{$outstationVehicle}',
                tour_package_localsightseen_vehicle = '{$localVehicle}'
            WHERE tour_package_id = '{$packageId}' AND tour_package_site_id = {$siteId}
        ";

        if (mysqli_query($connection, $updateSql)) {
            echo json_encode([
                'status'  => 'success',
                'msg'     => 'Package vehicles updated successfully.',
                'outstation_vehicle'     => $outstationVehicle,
                'localsightseen_vehicle' => $localVehicle
            ], JSON_UNESCAPED_SLASHES);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Failed to update package vehicles.'
            ], JSON_UNESCAPED_SLASHES);
        }
        exit();
    }

    // Default: Assign / Unassign Transporter
    $transporterId = (int)($data['transporter_id'] ?? 0);
    $vehicleType   = mysqli_real_escape_string($connection, trim($data['vehicle_type'] ?? ''));
    $notes         = mysqli_real_escape_string($connection, trim($data['notes'] ?? ''));
    $assignState   = isset($data['is_assigned']) ? (bool)$data['is_assigned'] : ($action !== 'unassign');

    if ($transporterId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Invalid transporter ID.'
        ], JSON_UNESCAPED_SLASHES);
        exit();
    }

    if ($assignState) {
        // Upsert assignment
        $checkSql = "
            SELECT tpt_id FROM {$tblPrefix}tour_package_transporter 
            WHERE tpt_package_id = {$packageId} AND tpt_transporter_id = {$transporterId} AND tpt_site_id = {$siteId}
            LIMIT 1
        ";
        $checkRes = mysqli_query($connection, $checkSql);

        if ($checkRes && mysqli_num_rows($checkRes) > 0) {
            $row = mysqli_fetch_assoc($checkRes);
            $tptId = (int)$row['tpt_id'];
            $upsertSql = "
                UPDATE {$tblPrefix}tour_package_transporter 
                SET tpt_vehicle_type = '{$vehicleType}',
                    tpt_notes = '{$notes}',
                    tpt_status = 1
                WHERE tpt_id = {$tptId}
            ";
        } else {
            $upsertSql = "
                INSERT INTO {$tblPrefix}tour_package_transporter 
                (tpt_site_id, tpt_package_id, tpt_transporter_id, tpt_vehicle_type, tpt_notes, tpt_status)
                VALUES ({$siteId}, {$packageId}, {$transporterId}, '{$vehicleType}', '{$notes}', 1)
            ";
        }

        if (mysqli_query($connection, $upsertSql)) {
            echo json_encode([
                'status'         => 'success',
                'msg'            => 'Transporter assigned to package successfully.',
                'is_assigned'    => true,
                'transporter_id' => $transporterId,
                'vehicle_type'   => $vehicleType
            ], JSON_UNESCAPED_SLASHES);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Database error assigning transporter.'
            ], JSON_UNESCAPED_SLASHES);
        }
    } else {
        // Unassign transporter
        $delSql = "
            DELETE FROM {$tblPrefix}tour_package_transporter 
            WHERE tpt_package_id = {$packageId} AND tpt_transporter_id = {$transporterId} AND tpt_site_id = {$siteId}
        ";

        if (mysqli_query($connection, $delSql)) {
            echo json_encode([
                'status'         => 'success',
                'msg'            => 'Transporter unassigned from package.',
                'is_assigned'    => false,
                'transporter_id' => $transporterId
            ], JSON_UNESCAPED_SLASHES);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Failed to unassign transporter.'
            ], JSON_UNESCAPED_SLASHES);
        }
    }
    exit();
}

// -------------------------------------------------------------
// GET: Fetch Package Info, Available Transporters & Vehicles
// -------------------------------------------------------------
$rawId = $_GET['package_id'] ?? ($_GET['packageId'] ?? ($_GET['id'] ?? 0));
if (is_numeric($rawId) && (int)$rawId > 0) {
    $packageId = (int)$rawId;
} else {
    $decoded   = base64_decode((string)$rawId, true);
    $packageId = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : 0;
}

if ($packageId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Invalid or missing tour package ID.'
    ], JSON_UNESCAPED_SLASHES);
    exit();
}

// 1. Fetch Package Info
$pkgSql = "
    SELECT 
        p.tour_package_id,
        p.tour_package_title, 
        p.tour_package_theme,
        p.tour_package_city, 
        p.tour_package_state, 
        p.tour_package_country, 
        p.tour_package_duration, 
        p.tour_package_itinerary,
        p.tour_package_type,
        p.tour_package_outstation_vehicle,
        p.tour_package_localsightseen_vehicle,
        l.link_url
    FROM {$tblPrefix}tour_package p
    LEFT JOIN {$tblPrefix}link l 
      ON l.link_relation_id = p.tour_package_id 
     AND l.link_type = 'tour_package'
     AND l.link_site_id = {$siteId}
    WHERE p.tour_package_id = '{$packageId}' AND p.tour_package_site_id = {$siteId}
    LIMIT 1
";

$pkgRes = mysqli_query($connection, $pkgSql);
if (!$pkgRes || mysqli_num_rows($pkgRes) === 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Tour package not found.'
    ], JSON_UNESCAPED_SLASHES);
    exit();
}

$pkgRow = mysqli_fetch_assoc($pkgRes);
$pkgTitle = $pkgRow['tour_package_title'] ?? 'Tour Package';
$pkgTheme = trim($pkgRow['tour_package_theme'] ?? 'Standard');
$pkgCity  = trim($pkgRow['tour_package_city'] ?? '');
$pkgState = trim($pkgRow['tour_package_state'] ?? '');
$pkgCountry = trim($pkgRow['tour_package_country'] ?? '');
$pkgDuration = max(1, (int)($pkgRow['tour_package_duration'] ?? 1));
$pkgDays = $pkgDuration + 1;
$pkgCategory = strtolower(trim($pkgRow['tour_package_type'] ?? 'domestic'));
$outstationVehicle = trim($pkgRow['tour_package_outstation_vehicle'] ?? '');
$localSightseeingVehicle = trim($pkgRow['tour_package_localsightseen_vehicle'] ?? '');
$pkgItinerary = trim($pkgRow['tour_package_itinerary'] ?? '');

// 2. Fetch Available Vehicles
$vehiclesList = [];
$vRes = mysqli_query($connection, "SELECT vehicle_title, vehicle_type FROM {$tblPrefix}vehicles ORDER BY vehicle_title ASC");
if ($vRes && mysqli_num_rows($vRes) > 0) {
    while ($vRow = mysqli_fetch_assoc($vRes)) {
        $vehiclesList[] = [
            'title' => $vRow['vehicle_title'],
            'type'  => $vRow['vehicle_type']
        ];
    }
} else {
    // Default fallback vehicle options
    $vehiclesList = [
        ['title' => 'Sedan (Dzire / Etios)', 'type' => 'sedan'],
        ['title' => 'SUV (Innova / Ertiga)', 'type' => 'suv'],
        ['title' => 'Innova Crysta (Premium SUV)', 'type' => 'innova_crysta'],
        ['title' => 'Tempo Traveller (12 Seater)', 'type' => 'tempo_traveller_12'],
        ['title' => 'Tempo Traveller (17 Seater)', 'type' => 'tempo_traveller_17'],
        ['title' => 'Mini Coach (22 Seater)', 'type' => 'mini_coach'],
        ['title' => 'Hatchback (WagonR / Swift)', 'type' => 'hatchback'],
        ['title' => 'Luxury Bus / Coach', 'type' => 'luxury_coach']
    ];
}

// 3. Fetch Assigned Transporters for this Package
$assignedTransporterMap = [];
$assignedQuery = "
    SELECT tpt_id, tpt_transporter_id, tpt_vehicle_type, tpt_rate, tpt_notes 
    FROM {$tblPrefix}tour_package_transporter 
    WHERE tpt_package_id = '{$packageId}' AND tpt_site_id = {$siteId} AND tpt_status = 1
";
if ($assignedRes = mysqli_query($connection, $assignedQuery)) {
    while ($aRow = mysqli_fetch_assoc($assignedRes)) {
        $tId = (int)$aRow['tpt_transporter_id'];
        $assignedTransporterMap[$tId] = $aRow;
    }
}

// 4. Fetch All Available Transporters in the System
$transportersList = [];
$transporterSql = "
    SELECT 
        transporter_id,
        transporter_title,
        transporter_email,
        transporter_mobile,
        transporter_description,
        transporter_address,
        transporter_city,
        transporter_pincode,
        transporter_service_state
    FROM {$tblPrefix}transporter 
    WHERE transporter_site_id = {$siteId}
    ORDER BY transporter_title ASC
";

$searchTarget = strtolower($pkgTitle . ' ' . $pkgCity . ' ' . $pkgState . ' ' . $pkgItinerary);

if ($tRes = mysqli_query($connection, $transporterSql)) {
    while ($tRow = mysqli_fetch_assoc($tRes)) {
        $tId = (int)$tRow['transporter_id'];
        $isAssigned = isset($assignedTransporterMap[$tId]);
        $assignmentData = $isAssigned ? $assignedTransporterMap[$tId] : null;

        $serviceState = strtolower(trim($tRow['transporter_service_state'] ?? ''));
        $transCity    = strtolower(trim($tRow['transporter_city'] ?? ''));
        $pkgCityLower = strtolower($pkgCity);
        $pkgStateLower = strtolower($pkgState);

        $isLocal = false;
        if (!empty($pkgStateLower) && (strpos($serviceState, $pkgStateLower) !== false || strpos($pkgStateLower, $serviceState) !== false)) {
            $isLocal = true;
        } elseif (!empty($pkgCityLower) && (strpos($serviceState, $pkgCityLower) !== false || strpos($transCity, $pkgCityLower) !== false)) {
            $isLocal = true;
        } elseif (!empty($transCity) && strpos($searchTarget, $transCity) !== false) {
            $isLocal = true;
        }

        $transportersList[] = [
            'id'             => $tId,
            'title'          => $tRow['transporter_title'] ?? 'Transporter',
            'email'          => $tRow['transporter_email'] ?? '',
            'mobile'         => $tRow['transporter_mobile'] ?? '',
            'description'    => $tRow['transporter_description'] ?? '',
            'address'        => trim(($tRow['transporter_address'] ?? '') . ' ' . ($tRow['transporter_city'] ?? '') . ' ' . ($tRow['transporter_pincode'] ?? '')),
            'city'           => $tRow['transporter_city'] ?? '',
            'pincode'        => $tRow['transporter_pincode'] ?? '',
            'service_state'  => $tRow['transporter_service_state'] ?? 'All Regions',
            'is_assigned'    => $isAssigned,
            'is_local'       => $isLocal,
            'assigned_vehicle_type' => $assignmentData['tpt_vehicle_type'] ?? '',
            'assigned_notes'        => $assignmentData['tpt_notes'] ?? '',
            'assigned_rate'         => $assignmentData['tpt_rate'] ?? ''
        ];
    }
}

// Sort: Assigned first, then local/nearby, then alphabetical
usort($transportersList, function ($a, $b) {
    if ($a['is_assigned'] !== $b['is_assigned']) {
        return $a['is_assigned'] ? -1 : 1;
    }
    if ($a['is_local'] !== $b['is_local']) {
        return $a['is_local'] ? -1 : 1;
    }
    return strcmp($a['title'], $b['title']);
});

$assignedCount = count($assignedTransporterMap);
$localCount    = count(array_filter($transportersList, function ($t) {
    return $t['is_local'];
}));
$totalCount    = count($transportersList);
$unassignedCount = max(0, $totalCount - $assignedCount);

echo json_encode([
    'status'  => 'success',
    'package' => [
        'id'                      => $packageId,
        'encoded_id'              => base64_encode((string)$packageId),
        'title'                   => $pkgTitle,
        'theme'                   => $pkgTheme,
        'city'                    => $pkgCity,
        'state'                   => $pkgState,
        'country'                 => $pkgCountry,
        'duration'                => $pkgDuration,
        'days'                    => $pkgDays,
        'location_label'          => $pkgCity ?: ($pkgState ?: ($pkgCountry ?: 'Domestic')),
        'category'                => $pkgCategory,
        'outstation_vehicle'      => $outstationVehicle,
        'localsightseen_vehicle'  => $localSightseeingVehicle,
        'link_url'                => $pkgRow['link_url'] ?? '',
        'linkUrl'                 => $pkgRow['link_url'] ?? ''
    ],
    'vehicles'     => $vehiclesList,
    'transporters' => $transportersList,
    'metrics'      => [
        'total_transporters'    => $totalCount,
        'assigned_transporters' => $assignedCount,
        'unassigned_transporters' => $unassignedCount,
        'nearby_transporters'   => $localCount,
        'has_outstation_vehicle' => !empty($outstationVehicle),
        'has_local_vehicle'     => !empty($localSightseeingVehicle)
    ]
], JSON_UNESCAPED_SLASHES);
