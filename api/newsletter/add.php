<?php

require_once USER_FUNCTIONS;
require_once COMMUNICATION_FUNCTIONS;

$httpStatus = 500;
$status = 'warning';
$msg = 'Something went wrong';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $newsletterEmail = mysqli_real_escape_string($connection, trim($_POST['email']));

    if (filter_var($newsletterEmail, FILTER_VALIDATE_EMAIL)) {

        // Check if email already exists
        $checkQuery = "SELECT newsletter_id, newsletter_status FROM " . TABLE_PREFIX . "newsletter WHERE newsletter_email='$newsletterEmail' AND newsletter_site_id='" . SITE_ID . "'";
        $checkResult = mysqli_query($connection, $checkQuery);

        if (mysqli_num_rows($checkResult) == 0) {
            // Insert new subscriber
            $insertQuery = "INSERT INTO " . TABLE_PREFIX . "newsletter (newsletter_email, newsletter_status, newsletter_site_id) VALUES ('$newsletterEmail', 1, '" . SITE_ID . "')";
            if (mysqli_query($connection, $insertQuery)) {
                $httpStatus = 201;
                $status = 'success';
                $msg = 'You have been successfully subscribed to the newsletter. Please check your email for verification link.';
            } else {
                $httpStatus = 500;
                $msg = 'Database insertion failed';
            }
        } else {
            extract(mysqli_fetch_assoc($checkResult));
            if ($newsletter_status == 0) {
                sendNewsLetterVerficationLinkviaEmail($newsletterEmail);
                $httpStatus = 200;;
                $status = 'success';
                $msg = 'Verification link has been sent to your email address.';
            } else {
                $httpStatus = 200;
                $status = 'info';
                $msg = 'Email is already subscribed to the newsletter';
            }
        }
    } else {
        $httpStatus = 400;
        $msg = 'Invalid email format';
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