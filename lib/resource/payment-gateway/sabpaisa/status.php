<?php

require_once MODULES . 'encryptor.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $AesCipher = new Encryptor();
    $response = $AesCipher->decrypt(PG_AUTH_KEY, PG_AUTH_IV, $_POST['encResponse']);

    $response = explode('&', $response);

    print_r($response);
} else {
    echo "Invalid request method";
}
