<?php
require_once USER_FUNCTIONS;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';
$inserted = 0;
$failed = 0;

// Accept only JSON for bulk insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $isJson = stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
    $contacts = $isJson ? json_decode($input, true) : null;
    if (!is_array($contacts)) {
        $msg = 'Invalid input. Expecting JSON array of contacts.';
        $httpStatus = 400;
    } else {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/modules/connection.php';
        $values = [];
        $validIndexes = [];
        foreach ($contacts as $i => $contact) {
            $name = trim($contact['name'] ?? '');
            $phone = trim($contact['phone'] ?? '');
            $email = trim($contact['email'] ?? '');
            if ($name === '' && $phone === '') {
                $failed++;
                continue;
            }
            $name = mysqli_real_escape_string($connection, $name);
            $phone = mysqli_real_escape_string($connection, $phone);
            $email = mysqli_real_escape_string($connection, $email);
            $values[] = "(" . SITE_ID . ", '$name', '$phone', '$email', NOW())";
            $validIndexes[] = $i;
        }
        if (!empty($values)) {
            $sql = "INSERT INTO " . TABLE_PREFIX . "contacts (contact_site_id, contact_name, contact_phone, contact_email, created_at) VALUES " . implode(',', $values) . " ON DUPLICATE KEY UPDATE contact_name=VALUES(contact_name), contact_email=VALUES(contact_email), created_at=VALUES(created_at)";
            if (mysqli_query($connection, $sql)) {
                $inserted = mysqli_affected_rows($connection);
                $status = 'success';
                $msg = "$inserted contacts saved or updated.";
                $httpStatus = 200;
            } else {
                $msg = 'Bulk insert failed: ' . mysqli_error($connection);
                $httpStatus = 500;
                $failed += count($values);
            }
        }
        if ($failed > 0 && $inserted === 0) {
            $status = 'error';
            $httpStatus = 400;
        } elseif ($failed > 0) {
            $status = 'partial';
            $httpStatus = 207;
        }
    }
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}

$result = array(
    'status' => $status,
    'msg' => $msg,
    'inserted' => $inserted,
    'failed' => $failed,
);

http_response_code($httpStatus);
echo json_encode($result, TRUE);