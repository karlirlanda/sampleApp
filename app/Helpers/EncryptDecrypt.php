<?php

namespace App\Helpers;

class EncryptDecrypt
{
    static function encryptDecrypt($string, $action = 'encrypt')
    {
        $encryptMethod = env('ID_ENCRYPT_METHOD');
        $secretKey = env('ID_SECRET_KEY');
        $secretIv = env('ID_SECRET_IV');
        $key = hash('sha256', $secretKey);
        $iv = substr(hash('sha256', $secretIv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encryptMethod, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encryptMethod, $key, 0, $iv);
        }

        return $output;
    }
}