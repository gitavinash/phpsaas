<?php

header('Content-Type: application/json');

$httpStatus = 405;
$validRequest = false;
$orderStatus = "";

function formatDeliverySlotAMPM($slotStr) {
    if (empty($slotStr)) return '';
    $slotStr = trim($slotStr);
    if (preg_match('/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})$/', $slotStr, $matches)) {
        $startTime = strtotime($matches[1]);
        $endTime = strtotime($matches[2]);
        if ($startTime !== false && $endTime !== false) {
            return date('h:i A', $startTime) . ' - ' . date('h:i A', $endTime);
        }
    }
    return $slotStr;
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $trackingId = str_replace('OD', '', $_GET['orderId'] ?? '');

    // ===========================================
    // ORDER DETAILS
    // ===========================================

    $orderSites = "IN (" . SITE_ID . ", " . APP_SITE_ID . ")";

    $checkOrderQuery = "SELECT order_id, order_status, order_generate, city_name, state_name FROM " . TABLE_PREFIX . "order LEFT JOIN " . TABLE_PREFIX . "address ON order_address_id=address_id LEFT JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id LEFT JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE order_id='" . mysqli_real_escape_string($connection, $trackingId) . "' AND order_site_id " . $orderSites;

    if ($orderRun = mysqli_query($connection, $checkOrderQuery)) {

        $orderData = mysqli_fetch_assoc($orderRun);

        if (!empty($orderData)) {
            $orderIdEsc = mysqli_real_escape_string($connection, $orderData['order_id']);

            // Fetch Order Meta values (Delivery Date, Delivery Slot, Return Comment/Reason)
            $deliveryDate = '';
            $deliverySlot = '';
            $returnComment = '';

            $metaQuery = mysqli_query($connection, "SELECT order_meta_key, order_meta_value FROM " . TABLE_PREFIX . "order_meta WHERE order_relation_id = '{$orderIdEsc}'");
            if ($metaQuery && mysqli_num_rows($metaQuery) > 0) {
                while ($metaRow = mysqli_fetch_assoc($metaQuery)) {
                    $key = strtolower(trim(str_replace(['_', ' '], '', $metaRow['order_meta_key'])));
                    $val = trim($metaRow['order_meta_value']);
                    if ($key === 'deliverydate') {
                        $deliveryDate = $val;
                    } else if (in_array($key, ['preferredtime', 'deliveryslot', 'timeslot', 'deliverypreferredtime'])) {
                        $deliverySlot = formatDeliverySlotAMPM($val);
                    } else if (in_array($key, ['returncomment', 'returnreason'])) {
                        if (empty($returnComment)) {
                            $returnComment = $val;
                        }
                    }
                }
            }

            // ===========================================
            // ORDER LOGS
            // ===========================================
            $logs = array();

            // Default First Log
            $logs[] = array(
                'status' => 'Order Received',
                'date' => $orderData['order_generate'],
            );

            $checkLogQuery = "SELECT * FROM " . TABLE_PREFIX . "log WHERE log_ref_id='{$orderIdEsc}' AND log_ref_type IN ('order', 'product') AND log_site_id " . $orderSites . " ORDER BY log_id ASC";

            if ($logResult = mysqli_query($connection, $checkLogQuery)) {

                while ($logData = mysqli_fetch_assoc($logResult)) {
                    $statusText = !empty($logData['log_description']) ? $logData['log_description'] : (!empty($logData['log_type']) ? ucwords(str_replace('_', ' ', $logData['log_type'])) : (!empty($logData['log_status']) ? $logData['log_status'] : 'Update'));
                    $dateText = !empty($logData['log_date']) ? $logData['log_date'] : (!empty($logData['log_time']) ? $logData['log_time'] : '');
                    $logType = strtolower((string)($logData['log_type'] ?? ''));

                    $logItem = array(
                        'status' => $statusText,
                        'date' => $dateText,
                    );

                    // Attach Reschedule/Return specific fields
                    if (strpos($logType, 'reschedule') !== false || stripos($statusText, 'reschedule') !== false) {
                        $logItem['deliveryDate'] = $deliveryDate;
                        $logItem['deliverySlot'] = $deliverySlot;
                    } else if (strpos($logType, 'return') !== false || stripos($statusText, 'return') !== false) {
                        $logItem['deliveryDate'] = $deliveryDate;
                        $logItem['deliverySlot'] = $deliverySlot;

                        // Parse return comment from log internal if meta wasn't found
                        if (empty($returnComment) && !empty($logData['log_internal'])) {
                            if (preg_match('/Comment:\s*(.*)$/i', $logData['log_internal'], $m)) {
                                $logItem['returnComment'] = trim($m[1]);
                            } else if (preg_match('/Reason:\s*(.*)$/i', $logData['log_internal'], $m)) {
                                $logItem['returnComment'] = trim($m[1]);
                            }
                        } else if (!empty($returnComment)) {
                            $logItem['returnComment'] = $returnComment;
                        }
                    }

                    $logs[] = $logItem;
                }

                $status = 'success';
                $msg = 'Order tracking data found.';

                switch ($orderData['order_status']) {
                    case '0':
                        $orderStatus = 'Pending';
                        break;
                    case '1':
                        $orderStatus = 'Accepted';
                        break;
                    case '2':
                        $orderStatus = 'Assigned to Vendor';
                        break;
                    case '3':
                        $orderStatus = 'Processing';
                        break;
                    case '4':
                        $orderStatus = 'Shipped';
                        break;
                    case '5':
                        $orderStatus = 'Out for Delivery';
                        break;
                    case '6':
                        $orderStatus = 'Delivered';
                        break;
                    case '7':
                        $orderStatus = 'Cancelled';
                        break;
                    case '8':
                        $orderStatus = 'Returned';
                        break;
                    case '9':
                        $orderStatus = 'Rescheduled';
                        break;
                    default:
                        $orderStatus = "Unknown";
                        break;
                }

                $result = array(
                    'order' => array(
                        'trackingId' => 'OD' . $trackingId,
                        'orderStatus' => $orderStatus,
                        'orderDate' => $orderData['order_generate'],
                        'deliveryCity' => $orderData['city_name'],
                        'deliveryState' => $orderData['state_name'],
                        'deliveryDate' => $deliveryDate,
                        'deliverySlot' => $deliverySlot,
                        'returnComment' => $returnComment,
                    ),
                    'logs' => $logs,
                );

                $httpStatus = 200;
            } else {

                $status = 'error';
                $msg = 'Database query failed: ' . mysqli_error($connection);
                $httpStatus = 500;
            }
        } else {

            $status = 'error';
            $msg = 'No order found.';
            $httpStatus = 404;
        }
    } else {

        $status = 'error';
        $msg = 'Database query failed: ' . mysqli_error($connection);
        $httpStatus = 500;
    }
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Invalid request method : ' . $_SERVER['REQUEST_METHOD']),
    'data' => ((isset($result)) ? $result : array()),
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);
