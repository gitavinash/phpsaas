<?php

require_once USER_FUNCTIONS;
require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';


require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once MODULES . 'imageUploader.php';

    $expensePhoto = '';
    if (isset($image['photo'])) {
        $expensePhoto = $image['photo'];
    }


    // Validation block (stop at first error)
    if (!isset($amount) || $amount <= 0) {
        $msg = 'Expense amount is required';
        $httpStatus = 400;
    } elseif (!isset($category) || $category === '') {
        $msg = 'Expense category is required';
        $httpStatus = 400;
    } elseif (!isset($description) || $description === '') {
        $msg = 'Expense description is required';
        $httpStatus = 400;
    } elseif (!isset($warehouseId) || $warehouseId === '' || $warehouseId === 'select') {
        $msg = 'Warehouse is required';
        $httpStatus = 400;
    } elseif (!isset($paymentMode) || $paymentMode === '') {
        $msg = 'Payment mode is required';
        $httpStatus = 400;
    }


    // Improved logic for $expenseDate
    $today = date('Y-m-d');
    if (isset($date) && $date === $today) {
        $expenseDate = date('Y-m-d H:i:s'); // current datetime
    } else {
        // Use user-provided date and time, fallback to current time if time not set
        $expenseDate = isset($date) ? $date : $today;
        $expenseDate .= ' ';
        $expenseDate .= (isset($time) && $time !== '') ? $time : date('H:i:s');
        // Ensure seconds are present
        if (strlen($expenseDate) === 16) $expenseDate .= ':00';
    }

    if ($httpStatus != 400) {
        $expenseQuery = "INSERT INTO " . TABLE_PREFIX . "expense (expense_site_id, expense_warehouse_id, expense_user_id, expense_amount, expense_category, expense_description, expense_payment_mode, expense_photo, expense_time, expense_status) VALUES (" . SITE_ID . ", $warehouseId, '" . USER_ID . "','$amount', '$category', '$description', '$paymentMode', '$expensePhoto', '{$expenseDate}', 0)";
        if (mysqli_query($connection, $expenseQuery)) {
            $status = 'success';
            $msg = 'Expense added successfully';
            $httpStatus = 200;
        } else {
            $msg = 'Failed to add expense';
            $httpStatus = 500;
        }
    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}

$result = array(
    'status' => ((isset($status)) ? $status : 'error'),
    'msg' => ((isset($msg)) ? $msg : 'Something went wrong')
);


http_response_code($httpStatus);
echo json_encode($result, TRUE);