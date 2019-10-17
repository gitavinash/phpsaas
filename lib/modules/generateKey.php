<?php

    /** Generate Key and Save into database table */

    if($keyType=='otp') {

        $saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('0123456789'), 0, 6));

    } else {

        $saltKey=hash('sha256', uniqid(mt_rand(), true).$email.substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?'), 0, 12).time());

    }

    if($insertKey=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."key(key_user_id, key_value, key_medium, key_type, key_expire_time) VALUES('{$userID}', '{$saltKey}', '{$keyMedium}', '{$keyType}', '".EXPIRE_TIME."')")) {

        require_once EMAIL_TEMPLATE.'installation-complete.php';

    }