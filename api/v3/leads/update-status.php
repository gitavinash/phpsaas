<?php

header("Content-type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_REQUEST['leadId'])) {
    $leadId = isset($_REQUEST['leadId']) ? $_REQUEST['leadId'] : '';
    $status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 0;

    if (!empty($leadId)) {
        $decodedId = base64_decode($leadId, true);
        if ($decodedId !== false && is_numeric($decodedId)) {
            $realId = intval($decodedId);
        } else {
            $realId = intval($leadId);
        }

        if ($realId > 0) {
            $updateSql = "UPDATE " . TABLE_PREFIX . "enquiry SET enquiry_status = {$status} WHERE enquiry_id = {$realId}";
            if (mysqli_query($connection, $updateSql)) {
                echo json_encode(array('status' => 200, 'msg' => 'Lead status updated successfully'));
                exit();
            }
        }
    }
}

echo json_encode(array('status' => 400, 'msg' => 'Invalid lead request parameters'));
exit();
