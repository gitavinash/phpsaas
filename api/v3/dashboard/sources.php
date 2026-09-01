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
    ? " AND (enquiry_agent = '{$userId}')"
    : "";

$daterangeFilter = "";
$t1 = date('Y-m-d 00:00:00', strtotime('-29 days'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dates = explode(' - ', trim($_GET['daterange']));
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime($dates[0]));
        $t2 = date('Y-m-d 23:59:59', strtotime($dates[1]));
    }
}
$daterangeFilter = " AND enquiry_time BETWEEN '{$t1}' AND '{$t2}'";

function cleanSourceName($source)
{
    if (empty($source) || trim($source) === '') {
        return 'Direct';
    }
    $cleaned = trim($source);
    // Strip protocol http://, https://
    $cleaned = preg_replace('#^https?://#i', '', $cleaned);
    // Strip www. prefix
    $cleaned = preg_replace('#^www\.#i', '', $cleaned);
    // Strip trailing slash
    $cleaned = rtrim($cleaned, '/');
    return ucfirst($cleaned);
}

$sourceMap = [];
$sqlSource = "SELECT enquiry_source, COUNT(*) AS count
              FROM {$tblPrefix}enquiry
              WHERE {$siteFilter}{$userFilter}{$daterangeFilter}
              GROUP BY enquiry_source
              ORDER BY count DESC";

if (isset($connection) && $connection && ($resSource = mysqli_query($connection, $sqlSource))) {
    while ($row = mysqli_fetch_assoc($resSource)) {
        $name = cleanSourceName($row['enquiry_source']);
        $cnt = intval($row['count']);
        if (!isset($sourceMap[$name])) {
            $sourceMap[$name] = 0;
        }
        $sourceMap[$name] += $cnt;
    }
}

// Sort descending
arsort($sourceMap);

// Take top 6
$topSources = array_slice($sourceMap, 0, 6, true);
$sourceLabels = array_keys($topSources);
$sourceData   = array_values($topSources);

$totalSourceLeads = array_sum($sourceMap);
$sourceFlowBars   = [];
foreach ($topSources as $sName => $cnt) {
    $pct = $totalSourceLeads > 0 ? round(($cnt / $totalSourceLeads) * 100, 1) : 0;
    $sourceFlowBars[] = [
        'source'     => $sName,
        'count'      => $cnt,
        'percentage' => $pct
    ];
}

echo json_encode([
    'status'         => 200,
    'labels'         => $sourceLabels,
    'data'           => $sourceData,
    'sourceFlowBars' => $sourceFlowBars,
    'total'          => $totalSourceLeads,
]);
exit();
