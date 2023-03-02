<?php

require_once MODULES . 'encryptor.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    // Check email and mobile number is exist

    switch ($type) {

        case 'mobile':
            if (strlen($value) > 10) {
                $value = substr($value, -10);
            }
            $searchQuery = "SELECT profile_user_id as userId, profile_first_name as firstName, profile_last_name as lastName, profile_mobile as mobile, profile_email as email FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id WHERE user_mobile='{$value}'";
            $search = TRUE;
            break;

        case 'email':
            $searchQuery = "SELECT profile_user_id as userId, profile_first_name as firstName, profile_last_name as lastName, profile_mobile as mobile, profile_email as email FROM " . TABLE_PREFIX . "user INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = user_id WHERE user_email='{$value}'";
            $search = TRUE;
            break;

        default:
            http_response_code(405);
            $msg = 'Search method not defined';
            $search = FALSE;
            break;
    }

    if ($search == TRUE) {

        if ($checkUser = mysqli_query($connection, $searchQuery)) {

            if (mysqli_num_rows($checkUser) == 0) {

                if ($type == 'mobile') {
                    $data['mobile'] = $value;
                } else {
                    $data['email'] = $value;
                }
                $msg = 'No user found';
            } else {

                $msg = 'User found';
                $userFound = TRUE;
                $data = mysqli_fetch_assoc($checkUser);

                $Encryptor = new Encryptor();
                $data['userId'] = $Encryptor->encrypt(ENCRYPTION_AUTH_KEY, ENCRYPTION_AUTH_IV, $data['userId']);
            }
        }
    }
} else {

    http_response_code(405);
    $msg = 'Invalid request method';
}

$apiResponse = array(
    'message' => ((isset($msg)) ? $msg : ''),
    'isFound' => (int)((isset($userFound)) ? 1 : 0),
    'data' => ((isset($data)) ? $data : ''),
);

echo json_encode($apiResponse, TRUE);
exit();