<?php
namespace Encryption;

use phpseclib3\Crypt\RSA;

class RSAEncryptor
{
    public static function encrypt($message, $publicKey)
    {
        $rsa = RSA::load($publicKey);
        return $rsa->encrypt($message);
    }

    public static function decrypt($ciphertext, $privateKey)
    {
        $rsa = RSA::load($privateKey);
        return $rsa->decrypt($ciphertext);
    }
}

