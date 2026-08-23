<?php

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId     = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';

$siteFilter = ($siteId > 0) ? "enquiry_site_id = {$siteId}" : "1=1";
$userFilter = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (enquiry_agent = '{$userId}' OR enquiry_agent = '0')"
    : "";

$daterangeFilter = "";
if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dates = explode(' - ', trim($_GET['daterange']));
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $t2 = date('Y-m-d 23:59:59', strtotime($dates[1]));
        $daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";
    }
}

$searchFilter = "";
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchKey = mysqli_real_escape_string($connection, trim($_GET['search']));
    $searchFilter = " AND (enquiry_first_name LIKE '%{$searchKey}%' 
                       OR enquiry_last_name LIKE '%{$searchKey}%'
                       OR enquiry_mobile LIKE '%{$searchKey}%' 
                       OR enquiry_email LIKE '%{$searchKey}%' 
                       OR enquiry_subject LIKE '%{$searchKey}%' 
                       OR enquiry_source LIKE '%{$searchKey}%'
                       OR enquiry_id = '{$searchKey}')";
    // When specific search is provided, search across all time
    $daterangeFilter = "";
}

function cleanLeadSource($src) {
    if (empty($src) || trim($src) === '') return 'Direct';
    $c = trim($src);
    $c = preg_replace('#^https?://#i', '', $c);
    $c = preg_replace('#^www\.#i', '', $c);
    $c = rtrim($c, '/');
    return ucfirst($c);
}

// Case 1: Fetching single column status (e.g. Load More pagination)
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $targetStatus = intval($_GET['status']);
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
    $limit  = isset($_GET['limit'])  ? min(max(1, intval($_GET['limit'])), 50) : 10;

    $sql = "SELECT enquiry_id, enquiry_first_name AS name, enquiry_mobile, enquiry_email,
                   enquiry_subject, enquiry_time, enquiry_status, enquiry_source
            FROM {$tblPrefix}enquiry
            WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}
              AND enquiry_status = {$targetStatus}
            ORDER BY enquiry_time DESC
            LIMIT {$offset}, {$limit}";

    $leads = [];
    if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
        while ($row = mysqli_fetch_assoc($res)) {
            $leads[] = [
                'id'         => (int)$row['enquiry_id'],
                'encoded_id' => base64_encode($row['enquiry_id']),
                'name'       => !empty($row['name'])           ? $row['name']                  : 'Lead #' . $row['enquiry_id'],
                'mobile'     => !empty($row['enquiry_mobile']) ? $row['enquiry_mobile']        : '',
                'email'      => !empty($row['enquiry_email'])  ? $row['enquiry_email']         : '',
                'subject'    => !empty($row['enquiry_subject'])? $row['enquiry_subject']       : 'General Enquiry',
                'source'     => cleanLeadSource($row['enquiry_source']),
                'time'       => date('d M, h:i A', strtotime($row['enquiry_time'])),
                'status'     => (int)$row['enquiry_status'],
            ];
        }
    }

    echo json_encode([
        'status' => 200,
        'data'   => $leads,
        'offset' => $offset,
        'limit'  => $limit,
        'total'  => count($leads),
    ]);
    exit();
}

// Case 2: Full Board Load (grouped by status '0', '2', '3', '1')
$grouped = ['0' => [], '2' => [], '3' => [], '1' => []];
$flatLeads = [];

$limitClause = !empty($searchFilter) ? "LIMIT 100" : "LIMIT 150";

$sql = "SELECT enquiry_id, enquiry_first_name AS name, enquiry_mobile, enquiry_email,
               enquiry_subject, enquiry_time, enquiry_status, enquiry_source
        FROM {$tblPrefix}enquiry
        WHERE {$siteFilter}{$userFilter}{$daterangeFilter}{$searchFilter}
          AND enquiry_status IN (0, 1, 2, 3)
        ORDER BY enquiry_time DESC
        {$limitClause}";

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $st   = (string)$row['enquiry_status'];
        $item = [
            'id'         => (int)$row['enquiry_id'],
            'encoded_id' => base64_encode($row['enquiry_id']),
            'name'       => !empty($row['name'])           ? $row['name']                  : 'Lead #' . $row['enquiry_id'],
            'mobile'     => !empty($row['enquiry_mobile']) ? $row['enquiry_mobile']        : '',
            'email'      => !empty($row['enquiry_email'])  ? $row['enquiry_email']         : '',
            'subject'    => !empty($row['enquiry_subject'])? $row['enquiry_subject']       : 'General Enquiry',
            'source'     => cleanLeadSource($row['enquiry_source']),
            'time'       => date('d M, h:i A', strtotime($row['enquiry_time'])),
            'status'     => (int)$row['enquiry_status'],
        ];
        $flatLeads[] = $item;
        if (isset($grouped[$st])) {
            $grouped[$st][] = $item;
        }
    }
}

echo json_encode([
    'status'  => 200,
    'data'    => $grouped,
    'grouped' => $grouped,
    'leads'   => $flatLeads,
    'total'   => count($flatLeads),
]);
exit();
