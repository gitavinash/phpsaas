<?php

require_once LOGIN_CHECK;

header("Content-type: application/json; charset=utf-8");

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId    = defined('SITE_ID')      ? (int) SITE_ID : 0;

$rawLeadId = isset($_POST['leadId']) ? $_POST['leadId'] : (isset($_GET['leadId']) ? $_GET['leadId'] : (isset($_REQUEST['leadId']) ? $_REQUEST['leadId'] : (isset($leadId) ? $leadId : '')));
$rawStatus = isset($_POST['status']) ? $_POST['status'] : (isset($_GET['status']) ? $_GET['status'] : (isset($_REQUEST['status']) ? $_REQUEST['status'] : (isset($status) ? $status : null)));

if ($rawStatus === null) {
    $rawStatus = isset($_POST['leadStatus']) ? $_POST['leadStatus'] : (isset($_GET['leadStatus']) ? $_GET['leadStatus'] : (isset($leadStatus) ? $leadStatus : null));
}

if (!empty($rawLeadId) && $rawStatus !== null && is_numeric($rawStatus)) {
    $newStatus = intval($rawStatus);
    $decoded   = base64_decode(trim($rawLeadId), true);
    $realId    = ($decoded !== false && is_numeric($decoded)) ? intval($decoded) : intval($rawLeadId);

    if ($realId > 0 && isset($connection) && $connection) {
        $siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
        $sql = "UPDATE {$tblPrefix}enquiry
                SET enquiry_status = {$newStatus}
                WHERE enquiry_id = {$realId}{$siteClause}";

        if (mysqli_query($connection, $sql)) {
            echo json_encode([
                'status'  => 200,
                'msg'     => 'Lead status updated successfully',
                'lead_id' => $realId,
                'new_status' => $newStatus,
            ]);
            exit();
        }

        echo json_encode(['status' => 500, 'msg' => 'Database error during status update: ' . mysqli_error($connection)]);
        exit();
    }
}

echo json_encode([
    'status' => 400,
    'msg' => 'Invalid lead request parameters',
    'debug' => [
        'rawLeadId' => $rawLeadId,
        'rawStatus' => $rawStatus,
    ],
]);
exit();
