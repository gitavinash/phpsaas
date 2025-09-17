<?php

require_once LOGIN_CHECK;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
} else {
    $httpStatus = 400;
    $msg = 'Bad request';
}


http_response_code($httpStatus);
echo json_encode($result, TRUE);