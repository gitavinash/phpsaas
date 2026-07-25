<?php

header('Content-Type: application/json');

$httpStatus = 405;
$validRequest = false;
$orderStatus = "";

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $trackingId = str_replace('OD', '', $_GET['orderId']);

    // ===========================================
    // ORDER DETAILS
    // ===========================================

    $orderSites = "IN (" . SITE_ID . ", " . APP_SITE_ID . ")";

    $checkOrderQuery = "SELECT order_id, order_status, order_generate, city_name, state_name FROM " . TABLE_PREFIX . "order LEFT JOIN " . TABLE_PREFIX . "address ON order_address_id=address_id LEFT JOIN " . TABLE_PREFIX . "state ON address_state_id=state_id LEFT JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id WHERE order_id='" . mysqli_real_escape_string($connection, $trackingId) . "' AND order_site_id " . $orderSites;

    if (mysqli_query($connection, $checkOrderQuery)) {

        $orderData = mysqli_fetch_assoc(mysqli_query($connection, $checkOrderQuery));

        if (!empty($orderData)) {

            // ===========================================
            // ORDER LOGS
            // ===========================================
            $logs = array();

            // Default First Log
            $logs[] = array(
                'status' => 'Order Received',
                'message' => 'Your order has been received successfully.',
                'date' => $orderData['order_generate'],
            );

            $checkLogQuery = "SELECT * FROM " . TABLE_PREFIX . "log WHERE log_ref_id=" . $orderData['order_id'] . " AND log_ref_type='product' AND log_site_id " . $orderSites . " ORDER BY log_time ASC";

            if (mysqli_query($connection, $checkLogQuery)) {

                $logResult = mysqli_query($connection, $checkLogQuery);

                while ($logData = mysqli_fetch_assoc($logResult)) {

                    $logs[] = array(
                        'status' => $logData['log_status'],
                        'message' => $logData['log_message'],
                        'date' => $logData['log_date'],
                    );
                }

                $status = 'success';
                $msg = 'Order tracking data found.';

                switch ($orderData['order_status']) {
                    case '0':
                        $orderStatus = 'Pending';
                        break;
                    case '1':
                        $orderStatus = 'Processing';
                        break;
                    case '2':
                        $orderStatus = 'Shipped';
                        break;
                    case '3':
                        $orderStatus = 'Delivered';
                        break;
                    case '4':
                        $orderStatus = 'Cancelled';
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
