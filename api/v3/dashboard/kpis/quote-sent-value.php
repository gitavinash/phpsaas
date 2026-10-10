<?php

header("Content-type: application/json; charset=utf-8");

if (defined('LOGIN_CHECK')) {
    require_once LOGIN_CHECK;
}

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$userId     = defined('USER_ID')      ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'customer';

// Security: Enforce administrator authorization for this sensitive financial metric
if ($accessType !== 'administrator') {
    http_response_code(403);
    echo json_encode([
        'status' => 403,
        'msg'    => 'Unauthorized access'
    ]);
    exit();
}

$t1 = date('Y-m-d 00:00:00', strtotime('-29 days'));
$t2 = date('Y-m-d 23:59:59');

if (isset($_GET['daterange']) && !empty($_GET['daterange']) && $_GET['daterange'] !== 'null') {
    $dr = trim($_GET['daterange']);
    if (($ampPos = strpos($dr, '&')) !== false) {
        $dr = substr($dr, 0, $ampPos);
    }
    $dates = explode(' - ', $dr);
    if (count($dates) == 2) {
        $t1 = date('Y-m-d 00:00:00', strtotime(trim($dates[0])));
        $t2 = date('Y-m-d 23:59:59', strtotime(trim($dates[1])));
    }
}

$siteFilter          = ($siteId > 0) ? "e.enquiry_site_id = {$siteId}" : "1=1";
$siteQuotationFilter = ($siteId > 0) ? " AND q.quotation_site_id = {$siteId}" : "";
$userFilter          = ($accessType !== 'administrator' && $userId > 0)
    ? " AND (e.enquiry_agent = '{$userId}')"
    : "";

$totalQuoteValueRaw = 0;
$totalQuoteValueFormatted = '₹0';
$count = 0;

$sql = "SELECT 
            COUNT(DISTINCT e.enquiry_id) AS total_count,
            COALESCE(SUM(latest_q.quotation_price), 0) AS total_val
        FROM {$tblPrefix}enquiry e
        INNER JOIN (
            SELECT q.quotation_ref_id, CAST(q.quotation_price AS DECIMAL(15,2)) AS quotation_price, q.quotation_time
            FROM {$tblPrefix}quotation q
            INNER JOIN (
                SELECT quotation_ref_id, MAX(quotation_id) AS max_qid
                FROM {$tblPrefix}quotation q
                WHERE quotation_status = 0 AND quotation_ref_type = 'enquiry' {$siteQuotationFilter}
                GROUP BY quotation_ref_id
            ) mq ON q.quotation_id = mq.max_qid
        ) latest_q ON e.enquiry_id = latest_q.quotation_ref_id
        WHERE {$siteFilter}{$userFilter}
          AND e.enquiry_status NOT IN (4, 5, 15, 16, 17, 18)
          AND (
              latest_q.quotation_time BETWEEN '{$t1}' AND '{$t2}'
              OR (e.enquiry_time BETWEEN '{$t1}' AND '{$t2}' AND e.enquiry_status = 9)
          )";

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($num)
    {
        $num = round((float)$num);
        $isNegative = $num < 0;
        $numStr = (string)abs($num);

        if (strlen($numStr) <= 3) {
            return ($isNegative ? '-' : '') . $numStr;
        }

        $lastThree = substr($numStr, -3);
        $remaining = substr($numStr, 0, -3);
        $formattedRemaining = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining);

        return ($isNegative ? '-' : '') . $formattedRemaining . ',' . $lastThree;
    }
}

if (isset($connection) && $connection && ($res = mysqli_query($connection, $sql))) {
    if ($row = mysqli_fetch_assoc($res)) {
        $totalQuoteValueRaw = floatval($row['total_val'] ?? 0);
        $totalQuoteValueFormatted = '₹' . formatIndianCurrency($totalQuoteValueRaw);
        $count = intval($row['total_count'] ?? 0);
    }
}

echo json_encode([
    'status'              => 200,
    'kpi'                 => 'quote_sent_value',
    'totalQuoteSentValue' => $totalQuoteValueFormatted,
    'amount'              => $totalQuoteValueRaw,
    'count'               => $count,
]);
exit();
