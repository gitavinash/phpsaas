<?php

require_once MODULES . 'encryptor.php';
require_once MODULES . 'users.php';
require_once MODULES . 'order.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (filter_var($email, FILTER_VALIDATE_EMAIL) == true) {

        if (is_numeric($mobile)) {

            $userId = 0;

            if ($userToken != '') {

                $Encryptor = new Encryptor();
                $data['userId'] = $Encryptor->decrypt(ENCRYPTION_AUTH_KEY, ENCRYPTION_AUTH_IV, $userToken);
            } else {

                // Generate new user profile

                $usersModule = new Users();
                $data['userId'] = $usersModule->createNewUser($connection, $_REQUEST);
            }

            $ordermodule = new orderModule();
            $data['redirectURL'] = $ordermodule->generatePaymentLinkOrder($connection, $userId, ((isset($currency)) ? $xcurrency : 'INR'), $amount);
        } else {
            $msg = 'Enter valid indian mobile no.';
        }
    } else {
        http_response_code(422);
        $data['error']['email'] = true;
        $msg = 'Please enter valid email';
    }

    $data['mobile'] = $mobile;
} else {

    http_response_code(400);
    $msg = 'Invalid request method';
}

$apiResponse = array(
    'message' => ((isset($msg)) ? $msg : ''),
    'data' => ((isset($data)) ? $data : ''),
);

echo json_encode($apiResponse, TRUE);
exit();