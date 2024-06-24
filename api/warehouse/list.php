<?php

$status = 500;
$companyFilter = "";

if (isset(($company_id))) {
    $companyFilter = "warehouse_company_id={$company_id} AND ";
    if ($checkList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "warehouse WHERE warehouse_status=1 AND {$companyFilter} warehouse_site_id=" . SITE_ID)) {
        if (mysqli_num_rows($checkList) > 0) {
            while ($warehouseInfo = mysqli_fetch_assoc($checkList)) {

                extract($warehouseInfo);

                $warehouseData[] = array(
                    'id' => $warehouse_id,
                    'name' => $warehouse_title
                );

                $status = 200;
                $msg = 'Warehouse data fetch successfully';
            }
        } else {
            $status = 404;
            $msg = "No warehouse data";
        }
    } else {
    }
} else {
    $msg = "Company id is required";
}

$result = array(
    'status' => $status,
    'msg' => ((isset($msg)) ? $msg : 'Invalid request'),
    'warehouseData' => ((isset($warehouseData)) ? $warehouseData : ''),
);

http_response_code($status);
echo json_encode($result, TRUE);
exit();