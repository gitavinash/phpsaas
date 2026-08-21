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

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_REQUEST['leadId'])) {
    $leadId    = isset($_REQUEST['leadId']) ? trim($_REQUEST['leadId']) : '';
    $newStatus = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;

    if (!empty($leadId)) {
        $decoded = base64_decode($leadId, true);
        $realId  = ($decoded !== false && is_numeric($decoded)) ? intval($decoded) : intval($leadId);

        if ($realId > 0 && isset($connection) && $connection) {
            // Enforce site_id when updating so agents can't modify cross-site leads
            $siteClause = ($siteId > 0) ? " AND enquiry_site_id = {$siteId}" : "";
            $sql = "UPDATE {$tblPrefix}enquiry
                    SET enquiry_status = {$newStatus}
                    WHERE enquiry_id = {$realId}{$siteClause}";

            if (mysqli_query($connection, $sql) && mysqli_affected_rows($connection) > 0) {
                echo json_encode(['status' => 200, 'msg' => 'Lead status updated successfully']);
                exit();
            }

            echo json_encode(['status' => 404, 'msg' => 'Lead not found or permission denied']);
            exit();
        }
    }
}

echo json_encode(['status' => 400, 'msg' => 'Invalid lead request parameters']);
exit();
