<?php
/**
 * V3 Package Prices API
 * Path: /api/v3/packages/prices.php
 * Handles fetching, updating, and deleting pricing slabs for tour packages.
 */

header('Content-Type: application/json; charset=utf-8');

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET: Fetch Package details & Price Slabs
if ($method === 'GET') {
    $rawPkgId = $_GET['package_id'] ?? ($_GET['id'] ?? 0);
    $packageId = 0;
    if (is_numeric($rawPkgId) && (int)$rawPkgId > 0) {
        $packageId = (int)$rawPkgId;
    } else if (!empty($rawPkgId)) {
        $decoded = base64_decode((string)$rawPkgId, true);
        if ($decoded !== false && is_numeric($decoded)) {
            $packageId = (int)$decoded;
        }
    }

    if ($packageId <= 0) {
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Valid Package ID is required.'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit();
    }

    // 1. Fetch Package Info
    $pkgTitle = 'Tour Package';
    $pkgCity = '';
    $pkgState = '';
    $pkgCountry = '';
    $pkgDuration = 1;
    $pkgTheme = 'Standard';
    $pkgType = 'domestic';

    $pkgSql = "
        SELECT tour_package_id, tour_package_title, tour_package_city, tour_package_state, 
               tour_package_country, tour_package_duration, tour_package_theme, tour_package_type
        FROM {$tblPrefix}tour_package
        WHERE tour_package_id = {$packageId} AND tour_package_site_id = {$siteId}
        LIMIT 1
    ";

    if ($pkgRes = mysqli_query($connection, $pkgSql)) {
        if ($pRow = mysqli_fetch_assoc($pkgRes)) {
            $pkgTitle = $pRow['tour_package_title'] ?? $pkgTitle;
            $pkgCity = trim($pRow['tour_package_city'] ?? '');
            $pkgState = trim($pRow['tour_package_state'] ?? '');
            $pkgCountry = trim($pRow['tour_package_country'] ?? '');
            $pkgDuration = max(1, (int)($pRow['tour_package_duration'] ?? 1));
            $pkgTheme = !empty($pRow['tour_package_theme']) ? ucwords(str_replace(['_', '-'], ' ', $pRow['tour_package_theme'])) : 'Standard';
            $pkgType = !empty($pRow['tour_package_type']) ? strtolower($pRow['tour_package_type']) : 'domestic';
        }
    }

    $nights = max(0, $pkgDuration - 1);
    $days = $pkgDuration;
    $locationLabel = ($pkgType === 'international' && !empty($pkgCountry)) ? $pkgCountry : (!empty($pkgCity) ? $pkgCity : 'Destination');

    // 2. Fetch Price Slabs
    $priceList = [];
    $totalSlabs = 0;
    $activeSlabs = 0;
    $minAdultSelling = null;
    $minChildSelling = null;

    $priceSql = "
        SELECT *
        FROM {$tblPrefix}tour_package_price
        WHERE tour_package_price_status != 3
          AND tour_package_price_parent_id = {$packageId}
          AND tour_package_price_site_id = {$siteId}
        ORDER BY tour_package_price_date_start ASC, tour_package_price_created_at DESC
    ";

    if ($priceRes = mysqli_query($connection, $priceSql)) {
        while ($row = mysqli_fetch_assoc($priceRes)) {
            $pId = (int)$row['tour_package_price_id'];
            $st = (int)$row['tour_package_price_status'];
            $totalSlabs++;
            if ($st === 1) {
                $activeSlabs++;
            }

            $adultSelling = (float)$row['tour_package_price_selling_adult'];
            $childSelling = (float)$row['tour_package_price_selling_child'];

            if ($adultSelling > 0 && ($minAdultSelling === null || $adultSelling < $minAdultSelling)) {
                $minAdultSelling = $adultSelling;
            }
            if ($childSelling > 0 && ($minChildSelling === null || $childSelling < $minChildSelling)) {
                $minChildSelling = $childSelling;
            }

            $startDate = !empty($row['tour_package_price_date_start']) ? $row['tour_package_price_date_start'] : '';
            $endDate   = !empty($row['tour_package_price_date_end'])   ? $row['tour_package_price_date_end']   : '';

            $formattedStart = !empty($startDate) ? date('d F', strtotime($startDate)) : 'Open';
            $formattedEnd   = !empty($endDate)   ? date('d F', strtotime($endDate))   : 'Open';

            $statusClass = 'status-active';
            $statusLabel = 'Active';
            if ($st === 2) {
                $statusClass = 'status-draft';
                $statusLabel = 'Draft';
            } elseif ($st === 3) {
                $statusClass = 'status-delete';
                $statusLabel = 'Deleted';
            } elseif ($st !== 1) {
                $statusClass = 'status-pending';
                $statusLabel = 'Pending';
            }

            $rawType = strtolower(trim($row['tour_package_price_type'] ?? 'single'));
            $typeLabel = ucwords($rawType);
            $typeClass = 'badge-type-single';
            if (strpos($rawType, 'double') !== false) {
                $typeClass = 'badge-type-double';
            } elseif (strpos($rawType, 'triple') !== false) {
                $typeClass = 'badge-type-triple';
            } elseif (strpos($rawType, 'quad') !== false) {
                $typeClass = 'badge-type-quad';
            }

            $priceList[] = [
                'id'                   => $pId,
                'encoded_id'           => base64_encode((string)$pId),
                'date_start'           => !empty($startDate) ? date('d F', strtotime($startDate)) : '',
                'date_end'             => !empty($endDate) ? date('d F', strtotime($endDate)) : '',
                'formatted_date_start' => $formattedStart,
                'formatted_date_end'   => $formattedEnd,
                'validity_text'        => "{$formattedStart} → {$formattedEnd}",
                'net_adult'            => (float)$row['tour_package_price_net_adult'],
                'mrp_adult'            => (float)$row['tour_package_price_mrp_adult'],
                'selling_adult'        => $adultSelling,
                'agent_adult'          => (float)$row['tour_package_price_agent_adult'],
                'net_child'            => (float)$row['tour_package_price_net_child'],
                'mrp_child'            => (float)$row['tour_package_price_mrp_child'],
                'selling_child'        => $childSelling,
                'agent_child'          => (float)$row['tour_package_price_agent_child'],
                'type'                 => $rawType,
                'type_label'           => $typeLabel,
                'type_class'           => $typeClass,
                'status'               => $st,
                'status_label'         => $statusLabel,
                'status_class'         => $statusClass,
                'created_at'           => $row['tour_package_price_created_at'] ?? ''
            ];
        }
    }

    echo json_encode([
        'status'  => 'success',
        'package' => [
            'id'             => $packageId,
            'encoded_id'     => base64_encode((string)$packageId),
            'title'          => $pkgTitle,
            'category'       => $pkgType,
            'city'           => $pkgCity,
            'state'          => $pkgState,
            'country'        => $pkgCountry,
            'location_label' => $locationLabel,
            'theme'          => $pkgTheme,
            'duration'       => $pkgDuration,
            'nights'         => $nights,
            'days'           => $days
        ],
        'metrics' => [
            'total_slabs'          => $totalSlabs,
            'active_slabs'         => $activeSlabs,
            'lowest_adult_selling' => $minAdultSelling,
            'lowest_child_selling' => $minChildSelling
        ],
        'prices' => $priceList
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

// Handle POST: Create, Update or Delete Price Slab
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $payload = json_decode($rawInput, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $action = strtolower(trim($payload['action'] ?? 'update'));

    // Handle Delete
    if ($action === 'delete') {
        $rawPriceId = $payload['id'] ?? ($payload['price_id'] ?? 0);
        $priceId = 0;
        if (is_numeric($rawPriceId) && (int)$rawPriceId > 0) {
            $priceId = (int)$rawPriceId;
        } else if (!empty($rawPriceId)) {
            $decoded = base64_decode((string)$rawPriceId, true);
            if ($decoded !== false && is_numeric($decoded)) {
                $priceId = (int)$decoded;
            }
        }

        if ($priceId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid price slab ID.']);
            exit();
        }

        $delSql = "UPDATE {$tblPrefix}tour_package_price SET tour_package_price_status = 3 WHERE tour_package_price_id = {$priceId} AND tour_package_price_site_id = {$siteId}";
        if (mysqli_query($connection, $delSql)) {
            echo json_encode(['status' => 'success', 'msg' => 'Price slab deleted successfully.']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'msg' => 'Database error while deleting price slab.']);
        }
        exit();
    }

    // Handle Create or Update Price Slab
    $rawPriceId = $payload['id'] ?? ($payload['tour_package_price_id'] ?? 0);
    $priceId = 0;
    if (is_numeric($rawPriceId) && (int)$rawPriceId > 0) {
        $priceId = (int)$rawPriceId;
    } else if (!empty($rawPriceId)) {
        $decoded = base64_decode((string)$rawPriceId, true);
        if ($decoded !== false && is_numeric($decoded)) {
            $priceId = (int)$decoded;
        }
    }

    $rawPkgId = $payload['package_id'] ?? ($payload['packageId'] ?? 0);
    $packageId = 0;
    if (is_numeric($rawPkgId) && (int)$rawPkgId > 0) {
        $packageId = (int)$rawPkgId;
    } else if (!empty($rawPkgId)) {
        $decoded = base64_decode((string)$rawPkgId, true);
        if ($decoded !== false && is_numeric($decoded)) {
            $packageId = (int)$decoded;
        }
    }

    $priceType = strtolower(trim($payload['priceType'] ?? ($payload['type'] ?? 'single')));
    if (!in_array($priceType, ['single', 'double', 'triple', 'quad'])) {
        $priceType = 'single';
    }

    $rawStartDate = trim($payload['startDate'] ?? ($payload['start_date'] ?? ($payload['sessionStartSingleSharing'] ?? '')));
    $rawEndDate   = trim($payload['endDate'] ?? ($payload['end_date'] ?? ($payload['sessionEndSingleSharing'] ?? '')));

    $startDate = !empty($rawStartDate) ? date('Y-m-d', strtotime($rawStartDate)) : date('Y-m-d');
    $endDate   = !empty($rawEndDate)   ? date('Y-m-d', strtotime($rawEndDate))   : date('Y-m-d', strtotime('+30 days'));

    $adultNet     = (float)($payload['adultNet'] ?? ($payload['netCostAdultSingleSharing'] ?? 0));
    $adultMRP     = (float)($payload['adultMRP'] ?? ($payload['mrpAdultSingleSharing'] ?? 0));
    $adultSelling = (float)($payload['adultSelling'] ?? ($payload['sellingPriceAdultSingleSharing'] ?? 0));
    $adultAgent   = (float)($payload['adultAgent'] ?? ($payload['agentPriceAdultSingleSharing'] ?? 0));

    $childNet     = (float)($payload['childNet'] ?? ($payload['netCostChildSingleSharing'] ?? 0));
    $childMRP     = (float)($payload['childMRP'] ?? ($payload['mrpChildSingleSharing'] ?? 0));
    $childSelling = (float)($payload['childSelling'] ?? ($payload['sellingPriceChildSingleSharing'] ?? 0));
    $childAgent   = (float)($payload['childAgent'] ?? ($payload['agentPriceChildSingleSharing'] ?? 0));

    // If action is create or priceId is 0, insert a new record
    if ($action === 'create' || $action === 'add' || $priceId <= 0) {
        if ($packageId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Valid Package ID is required to add pricing slab.']);
            exit();
        }

        $insertSql = "
            INSERT INTO {$tblPrefix}tour_package_price (
                tour_package_price_parent_id,
                tour_package_price_type,
                tour_package_price_date_start,
                tour_package_price_date_end,
                tour_package_price_net_adult,
                tour_package_price_mrp_adult,
                tour_package_price_selling_adult,
                tour_package_price_agent_adult,
                tour_package_price_net_child,
                tour_package_price_mrp_child,
                tour_package_price_selling_child,
                tour_package_price_agent_child,
                tour_package_price_status,
                tour_package_price_site_id,
                tour_package_price_created_at
            ) VALUES (
                {$packageId},
                '{$priceType}',
                '{$startDate}',
                '{$endDate}',
                {$adultNet},
                {$adultMRP},
                {$adultSelling},
                {$adultAgent},
                {$childNet},
                {$childMRP},
                {$childSelling},
                {$childAgent},
                1,
                {$siteId},
                NOW()
            )
        ";

        if (mysqli_query($connection, $insertSql)) {
            // Also ensure package status is active
            mysqli_query($connection, "UPDATE {$tblPrefix}tour_package SET tour_package_status = 1 WHERE tour_package_id = {$packageId} AND tour_package_site_id = {$siteId}");
            echo json_encode([
                'status' => 'success',
                'msg'    => 'New pricing slab added successfully.'
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'msg'    => 'Failed to create pricing slab in database.'
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    // Otherwise update existing record
    $updateSql = "
        UPDATE {$tblPrefix}tour_package_price
        SET
            tour_package_price_type             = '{$priceType}',
            tour_package_price_date_start       = '{$startDate}',
            tour_package_price_date_end         = '{$endDate}',
            tour_package_price_net_adult        = {$adultNet},
            tour_package_price_mrp_adult        = {$adultMRP},
            tour_package_price_selling_adult    = {$adultSelling},
            tour_package_price_agent_adult      = {$adultAgent},
            tour_package_price_net_child        = {$childNet},
            tour_package_price_mrp_child        = {$childMRP},
            tour_package_price_selling_child    = {$childSelling},
            tour_package_price_agent_child      = {$childAgent}
        WHERE tour_package_price_id = {$priceId}
          AND tour_package_price_site_id = {$siteId}
    ";

    if (mysqli_query($connection, $updateSql)) {
        echo json_encode([
            'status' => 'success',
            'msg'    => 'Price slab updated successfully.'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'msg'    => 'Failed to update price slab in database.'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    exit();
}

http_response_code(405);
echo json_encode(['status' => 'error', 'msg' => 'Method not allowed.']);
