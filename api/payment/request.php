<?php

require_once MODULES . 'encryptor.php';
require_once MODULES . 'users.php';
require_once MODULES . 'order.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $data = array();

    if (filter_var($email, FILTER_VALIDATE_EMAIL) == true) {

        if (is_numeric($mobile)) {

            if(isset($userToken)) {
                if ($userToken != '') {
                $Encryptor = new Encryptor();
                $userId = $Encryptor->decrypt(ENCRYPTION_AUTH_KEY, ENCRYPTION_AUTH_IV, $userToken);
            }
            } else {

                // Generate new user profile

                $usersModule = new Users();
                $createNewUserResponse = $usersModule->createNewUser($connection, $_REQUEST);
                $userId = $createNewUserResponse['userId'];
            }

            $ordermodule = new orderModule();
            
            $data['redirectURL'] = $ordermodule->generatePaymentLinkOrder($connection, $userId, ((isset($currency)) ? $xcurrency : 'INR'), $amount, ((isset($redirect)) ? $redirect : ''));
        } else {
            $msg = 'Enter valid indian mobile no.';
        }
    } else {
        http_response_code(422);
        $data['error']['email'] = true;
        $msg = 'Please enter valid email';
    }
} else {

    http_response_code(400);
    $msg = 'Invalid request method';
}

$apiResponse = array(
    'message' => ((isset($msg)) ? $msg : ''),
    'data' => ((isset($data)) ? $data : ''),
);

// echo $data['redirectURL'];

header("Location: {$data['redirectURL']}", TRUE, 301);
exit();