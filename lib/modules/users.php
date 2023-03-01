<?php

class Users
{
    function createNewUser($connection, $userInfo)
    {

        extract($userInfo);

        if ($userData = mysqli_query($connection, "SELECT user_id as userId FROM " . TABLE_PREFIX . "user WHERE user_mobile='{$mobile}' AND user_email='{$email}'")) {

            if (mysqli_num_rows($userData) != 0) {

                extract(mysqli_fetch_assoc(($userData)));
            } else {

                $userId = 0;
            }
        }

        return $userId;
    }
}