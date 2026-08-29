<?php
/**
 * V3 Company List & Directory API
 * Endpoint: /api/v3/company/list.php or /api/v3/company/
 * Method: GET
 */

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
$siteId = defined('SITE_ID') ? (int)SITE_ID : (int)($_SESSION['site_id'] ?? 0);
if ($siteId <= 0 && isset($_COOKIE['user_site_id'])) {
    $siteId = (int)base64_decode($_COOKIE['user_site_id']);
}
if ($siteId <= 0 && isset($connection)) {
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';
    if (!empty($currentHost)) {
        $hostSafe = mysqli_real_escape_string($connection, $currentHost);
        $sQuery = mysqli_query($connection, "SELECT site_id FROM {$tblPrefix}site WHERE site_url = '{$hostSafe}' OR site_domain = '{$hostSafe}' LIMIT 1");
        if ($sQuery && $sRow = mysqli_fetch_assoc($sQuery)) {
            $siteId = (int)$sRow['site_id'];
        }
    }
}

// 1. Gather Query Filters
$search = trim($_GET['search'] ?? ($_GET['q'] ?? ''));
$status = trim($_GET['status'] ?? '');
$type   = trim($_GET['type'] ?? '');

$where = ["c.company_site_id = {$siteId}"];

if ($status !== '' && $status !== 'all') {
    $statusInt = (int)$status;
    $where[] = "c.company_status = {$statusInt}";
} else {
    // Exclude deleted (status 0) unless explicitly requested
    $where[] = "c.company_status != 0";
}

if ($type !== '' && $type !== 'all') {
    $escType = mysqli_real_escape_string($connection, $type);
    $where[] = "c.company_type = '{$escType}'";
}

if (!empty($search)) {
    $escSearch = mysqli_real_escape_string($connection, $search);
    $where[] = "(c.company_title LIKE '%{$escSearch}%' 
                OR c.company_email LIKE '%{$escSearch}%' 
                OR c.company_mobile LIKE '%{$escSearch}%' 
                OR c.company_gst LIKE '%{$escSearch}%' 
                OR c.company_pan LIKE '%{$escSearch}%' 
                OR ct.city_name LIKE '%{$escSearch}%' 
                OR a.address_area LIKE '%{$escSearch}%')";
}

$whereClause = implode(' AND ', $where);

// 2. Fetch Companies
$query = "
    SELECT 
        c.*,
        a.address_house_no,
        a.address_area,
        a.address_landmark,
        a.address_pin_code,
        ct.city_name,
        s.state_name
    FROM {$tblPrefix}company c
    LEFT JOIN {$tblPrefix}address a ON c.company_address_id = a.address_id
    LEFT JOIN {$tblPrefix}city ct ON a.address_city_id = ct.city_id
    LEFT JOIN {$tblPrefix}state s ON ct.city_state_id = s.state_id
    WHERE {$whereClause}
    ORDER BY c.company_update DESC, c.company_id DESC
";

$result = mysqli_query($connection, $query);

$companies = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $compId = (int)$row['company_id'];
        $encodedId = base64_encode((string)$compId);
        
        $cType = strtolower(trim((string)$row['company_type']));
        $typeLabel = ucwords(str_replace('_', ' ', $cType ?: 'Registered Entity'));
        
        $cStatus = (int)$row['company_status'];
        $statusLabel = ($cStatus === 1) ? 'Active' : (($cStatus === 2) ? 'Pending' : 'Inactive');
        
        // Build address string
        $addressParts = array_filter([
            trim((string)$row['address_house_no']),
            trim((string)$row['address_area']),
            trim((string)$row['address_landmark']),
            trim((string)$row['city_name']),
            trim((string)$row['state_name']),
            trim((string)$row['address_pin_code'])
        ]);
        $formattedAddress = implode(', ', $addressParts);

        $companies[] = [
            'id'                => $compId,
            'encoded_id'        => $encodedId,
            'code'              => stripslashes($row['company_code'] ?? ''),
            'name'              => stripslashes($row['company_title'] ?? ''),
            'description'       => stripslashes($row['company_description'] ?? ''),
            'logo'              => !empty($row['company_logo']) ? (strpos($row['company_logo'], 'http') === 0 || strpos($row['company_logo'], '/') === 0 ? $row['company_logo'] : '/' . $row['company_logo']) : '',
            'gst'               => stripslashes($row['company_gst'] ?? ''),
            'pan'               => stripslashes($row['company_pan'] ?? ''),
            'cin'               => stripslashes($row['company_cin'] ?? ''),
            'email'             => stripslashes($row['company_email'] ?? ''),
            'mobile'            => stripslashes($row['company_mobile'] ?? ''),
            'type'              => $cType,
            'type_label'        => $typeLabel,
            'status'            => $cStatus,
            'status_label'      => $statusLabel,
            'city'              => stripslashes($row['city_name'] ?? ''),
            'state'             => stripslashes($row['state_name'] ?? ''),
            'pincode'           => stripslashes($row['address_pin_code'] ?? ''),
            'area'              => stripslashes($row['address_area'] ?? ''),
            'formatted_address' => stripslashes($formattedAddress),
            'updated_at'        => $row['company_update'] ? date('d M Y, h:i A', strtotime($row['company_update'])) : '',
            'edit_url'          => '/dashboard/company/update/?id=' . $encodedId
        ];
    }
}

// 3. Overall Stats Calculation for KPIs
$statsQuery = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN company_status = 1 THEN 1 ELSE 0 END) as active_count,
        SUM(CASE WHEN company_type IN ('head_office', 'headquarters', 'private', 'partnerships', 'proprietorship', 'company') THEN 1 ELSE 0 END) as head_count,
        SUM(CASE WHEN company_type IN ('branch', 'clinic', 'store', 'agency') THEN 1 ELSE 0 END) as branch_count
    FROM {$tblPrefix}company 
    WHERE company_site_id = {$siteId} AND company_status != 0
";
$statsRes = mysqli_query($connection, $statsQuery);
$stats = [
    'total'    => count($companies),
    'active'   => 0,
    'head'     => 0,
    'branches' => 0
];
if ($statsRes && ($sRow = mysqli_fetch_assoc($statsRes))) {
    $stats['total']    = (int)($sRow['total'] ?? 0);
    $stats['active']   = (int)($sRow['active_count'] ?? 0);
    $stats['head']     = (int)($sRow['head_count'] ?? 0);
    $stats['branches'] = (int)($sRow['branch_count'] ?? 0);
}

echo json_encode([
    'status'    => 'success',
    'stats'     => $stats,
    'total'     => count($companies),
    'companies' => $companies
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
