<?php
namespace Signer;

use phpseclib3\Crypt\RSA;

class RSASigner
{
    public static function sign($message, $privateKey)
    {

        $rsa = RSA::load($privateKey);
      
        return $rsa->sign($message);
    }

    public static function verify($message, $signature, $publicKey)
    {
        $rsa = RSA::load($publicKey);
        return $rsa->verify($message, $signature);
    }
}

