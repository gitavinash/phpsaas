<?php
class Encryptor
{

    private const OPENSSL_CIPHER_NAME = ENCRYPTION_METHOD;
    private const CIPHER_KEY_LEN = ENCRYPTION_LENGHT;
    private static function fixKey($key)
    {

        if (strlen($key) < Encryptor::CIPHER_KEY_LEN) {

            return str_pad("$key", Encryptor::CIPHER_KEY_LEN, "0");
        }

        if (strlen($key) > Encryptor::CIPHER_KEY_LEN) {

            return substr($key, 0, Encryptor::CIPHER_KEY_LEN);
        }
        return $key;
    }

    static function encrypt($key, $iv, $data)
    {
        $encodedEncryptedData = base64_encode(openssl_encrypt($data, Encryptor::OPENSSL_CIPHER_NAME, Encryptor::fixKey($key), OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData . ":" . $encodedIV;
        return $encryptedPayload;
    }

    static function decrypt($key, $iv, $data)
    {

        $parts = explode(':', $data);
        $encrypted = $parts[0];
        $iv = $parts[1];
        $decryptedData = openssl_decrypt(base64_decode($encrypted), Encryptor::OPENSSL_CIPHER_NAME, Encryptor::fixKey($key), OPENSSL_RAW_DATA, base64_decode($iv));
        return $decryptedData;
    }
}
