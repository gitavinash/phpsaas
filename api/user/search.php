<?php

require_once MODULES . 'encryptor.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    switch ($type) {
        case 'mobile':
            if (strlen($value) > 10) {
                $value = substr($value, -10);
            }
            // Updated SQL: join profile_meta on profile_id, select meta keys/values separately
            $searchQuery = "SELECT 
                                u.user_id AS userId,
                                p.profile_first_name AS firstName,
                                p.profile_last_name AS lastName,
                                p.profile_mobile AS mobile,
                                p.profile_email AS email,
                                pm.profile_meta_key,
                                pm.profile_meta_value
                            FROM 
                                " . TABLE_PREFIX . "user u
                            INNER JOIN 
                                " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
                            LEFT JOIN 
                                " . TABLE_PREFIX . "address a ON a.address_user_id = u.user_id AND a.address_site_id = " . SITE_ID . "
                            LEFT JOIN 
                                " . TABLE_PREFIX . "profile_meta pm ON pm.profile_meta_profile_id = p.profile_user_id
                            WHERE 
                                u.user_mobile = '{$value}'";
            $search = TRUE;
            break;

        case 'email':
            // Similarly, update for email search if you want profileMeta for email case too
            $searchQuery = "SELECT 
                                u.user_id AS userId,
                                p.profile_first_name AS firstName,
                                p.profile_last_name AS lastName,
                                p.profile_mobile AS mobile,
                                p.profile_email AS email,
                                pm.profile_meta_key,
                                pm.profile_meta_value
                            FROM 
                                " . TABLE_PREFIX . "user u
                            INNER JOIN 
                                " . TABLE_PREFIX . "profile p ON p.profile_user_id = u.user_id
                            LEFT JOIN 
                                " . TABLE_PREFIX . "address a ON a.address_user_id = u.user_id AND a.address_site_id = " . SITE_ID . "
                            LEFT JOIN 
                                " . TABLE_PREFIX . "profile_meta pm ON pm.profile_meta_profile_id = p.profile_user_id
                            WHERE 
                                u.user_email = '{$value}'";
            $search = TRUE;
            break;

        default:
            http_response_code(405);
            $msg = 'Search method not defined';
            $search = FALSE;
            break;
    }

    if ($search === TRUE) {

        if ($result = mysqli_query($connection, $searchQuery)) {

            if (mysqli_num_rows($result) === 0) {

                if ($type === 'mobile') {
                    $data['mobile'] = $value;
                } else {
                    $data['email'] = $value;
                }
                $msg = 'No user found';
            } else {
                $msg = 'User found';
                $userFound = TRUE;

                // Initialize user data and profileMeta array
                $userData = null;
                $profileMeta = [];

                // Fetch rows and build profileMeta associative array
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($userData === null) {
                        // Capture user basic info only once
                        $userData = [
                            'userId'    => $row['userId'],
                            'firstName' => $row['firstName'],
                            'lastName'  => $row['lastName'],
                            'mobile'    => $row['mobile'],
                            'email'     => $row['email'],
                        ];
                    }

                    // Add profile meta if available
                    if (!empty($row['profile_meta_key'])) {
                        $profileMeta[$row['profile_meta_key']] = $row['profile_meta_value'];
                    }
                }

                // Add profileMeta as an object (associative array)
                $userData['profileMeta'] = $profileMeta;

                // Encrypt userId
                $Encryptor = new Encryptor();
                $userData['userId'] = $Encryptor->encrypt(ENCRYPTION_AUTH_KEY, ENCRYPTION_AUTH_IV, $userData['userId']);

                // Assign to final data array
                $data = $userData;
            }
        } else {
            $msg = 'Database query failed: ' . mysqli_error($connection);
            http_response_code(500);
        }
    }
} else {
    http_response_code(405);
    $msg = 'Invalid request method';
}

$apiResponse = [
    'message' => $msg ?? '',
    'isFound' => isset($userFound) ? 1 : 0,
    'userData'    => $data ?? '',
];

echo json_encode($apiResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit();