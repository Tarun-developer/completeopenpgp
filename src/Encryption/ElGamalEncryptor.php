<?php

namespace Encryption;

use phpseclib3\Crypt\ElGamal;

class ElGamalEncryptor
{
    public static function encrypt($message, $publicKey)
    {
        $elgamal = new ElGamal();
        $elgamal->loadPublicKey($publicKey);
        return $elgamal->encrypt($message);
    }

    public static function decrypt($ciphertext, $privateKey)
    {
        $elgamal = new ElGamal();
        $elgamal->loadPrivateKey($privateKey);
        return $elgamal->decrypt($ciphertext);
    }
}

